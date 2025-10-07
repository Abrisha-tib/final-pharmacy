<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Transfer;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class TransferController extends Controller
{
    /**
     * Get medicines available for transfer from inventory
     */
    public function getInventoryMedicines(Request $request)
    {
        $perPage = 20;
        
        // Build query for medicines in inventory (not in dispensary)
        $query = Medicine::select([
            'id', 'name', 'generic_name', 'stock_quantity', 
            'selling_price', 'cost_price', 'category_id', 
            'is_active', 'batch_number', 'expiry_date',
            'manufacturer', 'strength', 'form', 'unit', 'barcode'
        ])->with('category:id,name,color')
          ->where('is_active', true)
          ->where('stock_quantity', '>', 0);

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%");
            });
        }
        
        // Category filter
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->category}%");
            });
        }
        
        $medicines = $query->orderBy('name')->paginate($perPage);
        
        return response()->json([
            'medicines' => $medicines->items(),
            'pagination' => [
                'current_page' => $medicines->currentPage(),
                'last_page' => $medicines->lastPage(),
                'per_page' => $medicines->perPage(),
                'total' => $medicines->total()
            ]
        ]);
    }

    /**
     * Get medicine details for transfer
     */
    public function getMedicineDetails($id)
    {
        $medicine = Medicine::with('category')
            ->where('id', $id)
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->first();
            
        if (!$medicine) {
            return response()->json(['error' => 'Medicine not found or out of stock'], 404);
        }
        
        return response()->json([
            'id' => $medicine->id,
            'name' => $medicine->name,
            'generic_name' => $medicine->generic_name,
            'stock_quantity' => $medicine->stock_quantity,
            'selling_price' => $medicine->selling_price,
            'strength' => $medicine->strength,
            'form' => $medicine->form,
            'unit' => $medicine->unit,
            'batch_number' => $medicine->batch_number,
            'expiry_date' => $medicine->expiry_date,
            'manufacturer' => $medicine->manufacturer,
            'category' => $medicine->category->name ?? 'N/A'
        ]);
    }

    /**
     * Process inventory to dispensary transfer
     */
    public function transferToDispensary(Request $request)
    {
        $request->validate([
            'medicine_id' => 'required|exists:medicines,id',
            'quantity' => 'nullable|integer|min:1',
            'notes' => 'nullable|string|max:1000'
        ]);

        try {
            DB::beginTransaction();

            $medicine = Medicine::findOrFail($request->medicine_id);
            
            if ($medicine->stock_quantity <= 0) {
                return response()->json(['error' => 'Medicine is out of stock'], 400);
            }

            $quantityToTransfer = $request->quantity ?? $medicine->stock_quantity;
            
            if ($quantityToTransfer > $medicine->stock_quantity) {
                return response()->json(['error' => 'Transfer quantity exceeds available stock'], 400);
            }

            // Create transfer record
            $transfer = Transfer::create([
                'medicine_id' => $medicine->id,
                'quantity_transferred' => $quantityToTransfer,
                'quantity_remaining' => $medicine->stock_quantity - $quantityToTransfer,
                'batch_number' => $medicine->batch_number,
                'expiry_date' => $medicine->expiry_date,
                'notes' => $request->notes,
                'status' => 'completed',
                'transfer_type' => 'inventory_to_dispensary',
                'transferred_by' => Auth::id(),
                'transferred_at' => now()
            ]);

            // Update medicine stock
            $medicine->stock_quantity -= $quantityToTransfer;
            $medicine->save();

            // Clear relevant caches
            Cache::forget('dispensary_stats_global');
            Cache::forget('inventory_stats_global');
            Cache::forget('dispensary_medicines_search_*');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transfer completed successfully',
                'transfer' => $transfer,
                'remaining_stock' => $medicine->stock_quantity
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Transfer failed: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get transfer history with analytics
     */
    public function getTransferHistory(Request $request)
    {
        $perPage = 20;
        
        $query = Transfer::with(['medicine', 'transferredBy'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('medicine', function($medicineQuery) use ($search) {
                    $medicineQuery->where('name', 'like', "%{$search}%")
                                  ->orWhere('generic_name', 'like', "%{$search}%");
                })
                ->orWhere('batch_number', 'like', "%{$search}%")
                ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Date filter
        if ($request->has('date') && $request->date) {
            $query->whereDate('created_at', $request->date);
        }

        // Type filter
        if ($request->has('type') && $request->type) {
            $query->where('transfer_type', $request->type);
        }

        $transfers = $query->paginate($perPage);
        
        // Calculate analytics
        $analytics = $this->calculateTransferAnalytics();
        
        return response()->json([
            'transfers' => $transfers->items(),
            'pagination' => [
                'current_page' => $transfers->currentPage(),
                'last_page' => $transfers->lastPage(),
                'per_page' => $transfers->perPage(),
                'total' => $transfers->total()
            ],
            'analytics' => $analytics
        ]);
    }

    /**
     * Calculate transfer analytics
     */
    private function calculateTransferAnalytics()
    {
        $totalTransfers = Transfer::count();
        $completedTransfers = Transfer::where('status', 'completed')->count();
        $pendingTransfers = Transfer::where('status', 'pending')->count();
        
        // Calculate total value of completed transfers
        $totalValue = Transfer::where('status', 'completed')
            ->with('medicine')
            ->get()
            ->sum(function($transfer) {
                return $transfer->quantity_transferred * ($transfer->medicine->selling_price ?? 0);
            });
        
        return [
            'total_transfers' => $totalTransfers,
            'completed_transfers' => $completedTransfers,
            'pending_transfers' => $pendingTransfers,
            'total_value' => $totalValue
        ];
    }
}
