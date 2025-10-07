<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use App\Models\Medicine;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class PurchaseController extends Controller
{
    /**
     * Display the purchase management page
     */
    public function index(Request $request)
    {
        // Get search and filter parameters
        $search = $request->get('search');
        $supplierFilter = $request->get('supplier');
        $statusFilter = $request->get('status');
        $dateFilter = $request->get('date');
        $viewType = $request->get('view', 'cards'); // cards or table

        // Build query
        $query = Purchase::with(['supplier', 'items.medicine', 'createdBy'])
            ->select(['id', 'purchase_number', 'supplier_id', 'order_date', 'expected_delivery', 'total_amount', 'status', 'created_at']);

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('purchase_number', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($supplierQuery) use ($search) {
                      $supplierQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Apply supplier filter
        if ($supplierFilter && $supplierFilter !== 'All Suppliers') {
            $query->where('supplier_id', $supplierFilter);
        }

        // Apply status filter
        if ($statusFilter && $statusFilter !== 'All Status') {
            $query->where('status', $statusFilter);
        }

        // Apply date filter
        if ($dateFilter) {
            $query->whereDate('order_date', $dateFilter);
        }

        // Get paginated results
        $purchases = $query->orderBy('order_date', 'desc')->paginate(12);

        // Get statistics
        $stats = $this->getPurchaseStats();

        // Get suppliers for filter dropdown
        $suppliers = Supplier::select('id', 'name')->orderBy('name')->get();

        return view('purchases', compact('purchases', 'stats', 'suppliers', 'request', 'viewType'));
    }

    /**
     * Show the form for creating a new purchase
     */
    public function create()
    {
        $suppliers = Supplier::where('status', 'Active')->orderBy('name')->get();
        $medicines = Medicine::where('is_active', true)->orderBy('name')->get();
        
        return view('purchases.create', compact('suppliers', 'medicines'));
    }

    /**
     * Store a newly created purchase
     */
    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery' => 'nullable|date|after:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string|max:50',
            'items.*.expiry_date' => 'nullable|date|after:today',
            'items.*.notes' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            // Generate purchase number
            $purchaseNumber = 'PO-' . date('Y') . '-' . str_pad(Purchase::count() + 1, 4, '0', STR_PAD_LEFT);

            // Create purchase
            $purchase = Purchase::create([
                'purchase_number' => $purchaseNumber,
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'expected_delivery' => $request->expected_delivery,
                'notes' => $request->notes,
                'created_by' => auth()->id(),
                'total_amount' => 0 // Will be calculated below
            ]);

            $totalAmount = 0;

            // Create purchase items
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $totalAmount += $itemTotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $itemTotal,
                    'batch_number' => $item['batch_number'],
                    'expiry_date' => $item['expiry_date'],
                    'notes' => $item['notes']
                ]);
            }

            // Update purchase total
            $purchase->update(['total_amount' => $totalAmount]);

            DB::commit();

            // Clear cache after successful creation
            Cache::forget('purchase_stats');

            return response()->json([
                'success' => true,
                'message' => 'Purchase order created successfully',
                'purchase' => $purchase->load(['supplier', 'items.medicine'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified purchase
     */
    public function show($id)
    {
        $purchase = Purchase::with(['supplier', 'items.medicine', 'createdBy', 'approvedBy'])
            ->findOrFail($id);

        return response()->json($purchase);
    }

    /**
     * Update the specified purchase
     */
    public function update(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        // Only allow updates for pending purchases
        if ($purchase->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending purchases can be updated'
            ], 400);
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_delivery' => 'nullable|date|after:order_date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.medicine_id' => 'required|exists:medicines,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.batch_number' => 'nullable|string|max:50',
            'items.*.expiry_date' => 'nullable|date|after:today',
            'items.*.notes' => 'nullable|string|max:255'
        ]);

        DB::beginTransaction();
        try {
            // Update purchase
            $purchase->update([
                'supplier_id' => $request->supplier_id,
                'order_date' => $request->order_date,
                'expected_delivery' => $request->expected_delivery,
                'notes' => $request->notes
            ]);

            // Delete existing items
            $purchase->items()->delete();

            $totalAmount = 0;

            // Create new purchase items
            foreach ($request->items as $item) {
                $itemTotal = $item['quantity'] * $item['unit_price'];
                $totalAmount += $itemTotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $itemTotal,
                    'batch_number' => $item['batch_number'],
                    'expiry_date' => $item['expiry_date'],
                    'notes' => $item['notes']
                ]);
            }

            // Update purchase total
            $purchase->update(['total_amount' => $totalAmount]);

            DB::commit();

            // Clear cache after successful update
            Cache::forget('purchase_stats');

            return response()->json([
                'success' => true,
                'message' => 'Purchase order updated successfully',
                'purchase' => $purchase->load(['supplier', 'items.medicine'])
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update purchase order: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update purchase status
     */
    public function updateStatus(Request $request, $id)
    {
        $purchase = Purchase::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,received,cancelled'
        ]);

        $purchase->update([
            'status' => $request->status,
            'delivery_date' => $request->status === 'received' ? now()->toDateString() : null,
            'approved_by' => $request->status === 'received' ? auth()->id() : null,
            'approved_at' => $request->status === 'received' ? now() : null
        ]);

        // Clear cache after status update
        Cache::forget('purchase_stats');

        return response()->json([
            'success' => true,
            'message' => 'Purchase status updated successfully',
            'purchase' => $purchase
        ]);
    }

    /**
     * Remove the specified purchase
     */
    public function destroy($id)
    {
        $purchase = Purchase::findOrFail($id);

        // Only allow deletion of pending purchases
        if ($purchase->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending purchases can be deleted'
            ], 400);
        }

        $purchase->delete();

        // Clear cache after deletion
        Cache::forget('purchase_stats');

        return response()->json([
            'success' => true,
            'message' => 'Purchase order deleted successfully'
        ]);
    }

    /**
     * Get purchase statistics - optimized for cPanel/shared hosting with caching
     */
    private function getPurchaseStats()
    {
        return Cache::remember('purchase_stats', 300, function() {
            // Use database aggregation for better performance
            $stats = DB::table('purchases')
                ->selectRaw('
                    COALESCE(SUM(total_amount), 0) as total_value,
                    COUNT(*) as total_purchases,
                    SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending_orders,
                    SUM(CASE WHEN status = "pending" AND expected_delivery < NOW() THEN 1 ELSE 0 END) as pending_delivery
                ')
                ->first();

            return [
                'totalValue' => $stats->total_value,
                'pendingOrders' => $stats->pending_orders,
                'pendingDelivery' => $stats->pending_delivery,
                'totalPurchases' => $stats->total_purchases
            ];
        });
    }

    /**
     * Get analytics data
     */
    public function getAnalytics()
    {
        $analytics = [
            'monthlyPurchases' => Purchase::selectRaw('DATE_FORMAT(order_date, "%Y-%m") as month, COUNT(*) as count, SUM(total_amount) as total')
                ->where('order_date', '>=', now()->subMonths(12))
                ->groupBy('month')
                ->orderBy('month')
                ->get(),
            
            'supplierStats' => Purchase::join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
                ->selectRaw('suppliers.name, COUNT(purchases.id) as purchase_count, SUM(purchases.total_amount) as total_amount')
                ->groupBy('suppliers.id', 'suppliers.name')
                ->orderBy('total_amount', 'desc')
                ->limit(10)
                ->get(),
            
            'statusDistribution' => Purchase::selectRaw('status, COUNT(*) as count')
                ->groupBy('status')
                ->get()
        ];

        return response()->json($analytics);
    }

    /**
     * Export purchases data
     */
    public function export(Request $request)
    {
        // Implementation for export functionality
        return response()->json(['message' => 'Export functionality to be implemented']);
    }

    /**
     * Print purchase report
     */
    public function print($id)
    {
        $purchase = Purchase::with(['supplier', 'items.medicine', 'createdBy'])
            ->findOrFail($id);

        return view('purchases.print', compact('purchase'));
    }

    /**
     * API: Get purchases data for frontend
     */
    public function getPurchases(Request $request)
    {
        try {
            // Get search and filter parameters
            $search = $request->get('search');
            $supplierFilter = $request->get('supplier');
            $statusFilter = $request->get('status');
            $dateFilter = $request->get('date');

            // Build query
            $query = Purchase::with(['supplier', 'items.medicine', 'createdBy'])
                ->select(['id', 'purchase_number', 'supplier_id', 'order_date', 'expected_delivery', 'total_amount', 'status', 'created_at']);

            // Apply search filter
            if ($search) {
                $query->where(function($q) use ($search) {
                    $q->where('purchase_number', 'like', "%{$search}%")
                      ->orWhereHas('supplier', function($supplierQuery) use ($search) {
                          $supplierQuery->where('name', 'like', "%{$search}%");
                      });
                });
            }

            // Apply supplier filter
            if ($supplierFilter && $supplierFilter !== 'All Suppliers') {
                $query->where('supplier_id', $supplierFilter);
            }

            // Apply status filter
            if ($statusFilter && $statusFilter !== 'All Status') {
                $query->where('status', $statusFilter);
            }

            // Apply date filter
            if ($dateFilter) {
                $query->whereDate('order_date', $dateFilter);
            }

            // Get paginated results
            $purchases = $query->orderBy('order_date', 'desc')->paginate(12);

            return response()->json([
                'success' => true,
                'data' => $purchases,
                'stats' => $this->getPurchaseStats()
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch purchases: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Get purchase statistics
     */
    public function getStats()
    {
        try {
            $stats = $this->getPurchaseStats();
            
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch statistics: ' . $e->getMessage()
            ], 500);
        }
    }
}