<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Medicine;
use App\Models\User;

class SalesController extends Controller
{
    /**
     * Display the sales page with server-side rendering
     * Optimized for shared hosting with caching
     */
    public function index(Request $request)
    {
        try {
            $start = microtime(true);
            $startMemory = memory_get_usage();
            
            // Get sales data with optimized query and caching
            $salesData = $this->getSalesDataOptimized($request);
            
            // Get sales statistics with caching
            $statsData = $this->getStatsDataOptimized($request);
            
            // Get dispensary medicines for sale creation
            $dispensaryMedicines = $this->getDispensaryMedicinesOptimized();
            
            $end = microtime(true);
            $endMemory = memory_get_usage();
            
            // Log performance metrics
            Log::info('Sales Page Performance', [
                'execution_time' => $end - $start,
                'memory_usage' => $endMemory - $startMemory,
                'peak_memory' => memory_get_peak_usage()
            ]);
            
            return view('sales', compact('salesData', 'statsData', 'dispensaryMedicines'));
            
        } catch (\Exception $e) {
            Log::error('Sales page error: ' . $e->getMessage());
            
            // Return empty data to prevent crashes
            return view('sales', [
                'salesData' => collect([]),
                'statsData' => [
                    'total_sales' => 0,
                    'completed_sales' => 0,
                    'pending_sales' => 0,
                    'cancelled_sales' => 0,
                    'total_revenue' => 0,
                    'average_sale' => 0,
                    'today_sales' => 0,
                    'today_revenue' => 0
                ],
                'dispensaryMedicines' => collect([])
            ]);
        }
    }

    /**
     * Get sales data with filters and pagination
     */
    public function getSales(Request $request)
    {
        try {
            $query = Sale::with(['items.medicine', 'soldBy'])
                        ->orderBy('sale_date', 'desc');

            // Apply filters
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('payment_method') && $request->payment_method) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('sale_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('sale_date', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('sale_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            }

            // Pagination
            $perPage = $request->get('per_page', 15);
            $sales = $query->paginate($perPage);

            return response()->json([
                'success' => true,
                'data' => $sales,
                'message' => 'Sales data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get sales data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales statistics
     */
    public function getStats(Request $request)
    {
        try {
            $cacheKey = 'sales_stats_' . md5(serialize($request->all()));
            
            $stats = Cache::remember($cacheKey, 300, function() use ($request) {
                $query = Sale::query();

                // Apply date filters
                if ($request->has('date_from') && $request->date_from) {
                    $query->whereDate('sale_date', '>=', $request->date_from);
                }

                if ($request->has('date_to') && $request->date_to) {
                    $query->whereDate('sale_date', '<=', $request->date_to);
                }

                return [
                    'total_sales' => $query->count(),
                    'completed_sales' => $query->clone()->where('status', 'completed')->count(),
                    'pending_sales' => $query->clone()->where('status', 'pending')->count(),
                    'cancelled_sales' => $query->clone()->where('status', 'cancelled')->count(),
                    'total_revenue' => $query->clone()->where('status', 'completed')->sum('total_amount'),
                    'average_sale' => $query->clone()->where('status', 'completed')->avg('total_amount'),
                    'today_sales' => Sale::whereDate('sale_date', today())->where('status', 'completed')->count(),
                    'today_revenue' => Sale::whereDate('sale_date', today())->where('status', 'completed')->sum('total_amount')
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $stats,
                'message' => 'Sales statistics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get sales statistics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create a new sale
     */
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_name' => 'nullable|string|max:255',
                'customer_phone' => 'nullable|string|max:20',
                'customer_email' => 'nullable|email|max:255',
                'items' => 'required|array|min:1',
                'items.*.medicine_id' => 'required|exists:medicines,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.unit_price' => 'required|numeric|min:0',
                'payment_method' => 'required|in:cash,card,mobile_payment,bank_transfer,tele_birr',
                'prescription_required' => 'boolean',
                'notes' => 'nullable|string',
                'discount_amount' => 'nullable|numeric|min:0',
                'tax_rate' => 'nullable|numeric|min:0|max:100'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            DB::beginTransaction();

            // Check stock availability and validate dispensary availability
            foreach ($request->items as $item) {
                $medicine = Medicine::with(['transfers' => function($q) {
                    $q->where('transfer_type', 'inventory_to_dispensary')
                      ->where('status', 'completed');
                }])
                ->where('is_active', true)
                ->whereHas('transfers', function($q) {
                    $q->where('transfer_type', 'inventory_to_dispensary')
                      ->where('status', 'completed');
                })
                ->findOrFail($item['medicine_id']);
                
                // Check if medicine is available in dispensary
                if (!$medicine->transfers->count()) {
                    throw new \Exception("Medicine {$medicine->name} is not available in dispensary. Please transfer from inventory first.");
                }
                
                // Calculate total dispensary stock from transfers
                $dispensaryStock = $medicine->transfers->sum('quantity_remaining');
                
                if ($dispensaryStock < $item['quantity']) {
                    throw new \Exception("Insufficient dispensary stock for {$medicine->name}. Available in dispensary: {$dispensaryStock}, Required: {$item['quantity']}");
                }

                // Update dispensary stock (decrease quantity_remaining in transfers)
                $remainingQuantity = $item['quantity'];
                
                foreach ($medicine->transfers->sortBy('transferred_at') as $transfer) {
                    if ($remainingQuantity <= 0) break;
                    
                    $availableInTransfer = $transfer->quantity_remaining;
                    $toDeduct = min($remainingQuantity, $availableInTransfer);
                    
                    $transfer->decrement('quantity_remaining', $toDeduct);
                    $remainingQuantity -= $toDeduct;
                }
            }

            // Calculate totals
            $subtotal = 0;
            $taxRate = $request->tax_rate ?? 10; // Default 10% tax
            $discountAmount = $request->discount_amount ?? 0;

            foreach ($request->items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $taxAmount = ($subtotal * $taxRate) / 100;
            $totalAmount = $subtotal + $taxAmount - $discountAmount;

            // Create sale
            $sale = Sale::create([
                'sale_number' => Sale::generateSaleNumber(),
                'customer_name' => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'discount_amount' => $discountAmount,
                'total_amount' => $totalAmount,
                'payment_method' => $request->payment_method,
                'status' => 'pending',
                'prescription_required' => $request->prescription_required ?? false,
                'notes' => $request->notes,
                'sold_by' => auth()->id(),
                'sale_date' => now()
            ]);

            // Create sale items
            foreach ($request->items as $item) {
                $medicine = Medicine::find($item['medicine_id']);
                
                SaleItem::create([
                    'sale_id' => $sale->id,
                    'medicine_id' => $item['medicine_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                    'batch_number' => $medicine->batch_number,
                    'expiry_date' => $medicine->expiry_date
                ]);
            }

            DB::commit();

            // Clear cache
            $this->clearSalesCache();

            return response()->json([
                'success' => true,
                'data' => $sale->load(['items.medicine', 'soldBy']),
                'message' => 'Sale created successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create sale: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get a specific sale
     */
    public function show($id)
    {
        try {
            $sale = Sale::with(['items.medicine', 'soldBy'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $sale,
                'message' => 'Sale retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get sale: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Sale not found'
            ], 404);
        }
    }

    /**
     * Update sale status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:pending,completed,cancelled,refunded'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            $sale = Sale::findOrFail($id);
            $oldStatus = $sale->status;
            $sale->update(['status' => $request->status]);

            // If cancelling or refunding, restore stock
            if (in_array($request->status, ['cancelled', 'refunded']) && in_array($oldStatus, ['completed', 'pending'])) {
                foreach ($sale->items as $item) {
                    $medicine = Medicine::find($item->medicine_id);
                    $medicine->increment('stock_quantity', $item->quantity);
                }
            }

            // Clear cache
            $this->clearSalesCache();

            return response()->json([
                'success' => true,
                'data' => $sale->load(['items.medicine', 'soldBy']),
                'message' => 'Sale status updated successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to update sale status: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update sale status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a sale
     */
    public function destroy($id)
    {
        try {
            $sale = Sale::findOrFail($id);

            // Only allow deletion of pending or cancelled sales
            if (!in_array($sale->status, ['pending', 'cancelled'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete completed or refunded sales'
                ], 422);
            }

            DB::beginTransaction();

            // Restore stock if sale was pending
            if ($sale->status === 'pending') {
                foreach ($sale->items as $item) {
                    $medicine = Medicine::find($item->medicine_id);
                    $medicine->increment('stock_quantity', $item->quantity);
                }
            }

            $sale->delete();

            DB::commit();

            // Clear cache
            $this->clearSalesCache();

            return response()->json([
                'success' => true,
                'message' => 'Sale deleted successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete sale: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search medicines for sale (only dispensary medicines)
     */
    public function searchMedicines(Request $request)
    {
        try {
            $query = $request->get('q', '');
            
            // Create cache key for dispensary medicines search
            $cacheKey = 'dispensary_medicines_search_' . md5($query);
            
            $medicines = Cache::remember($cacheKey, 300, function() use ($query) {
                // Optimized query for dispensary medicines - only medicines transferred to dispensary
                return Medicine::select([
                    'id', 'name', 'generic_name', 'stock_quantity', 
                    'selling_price', 'category_id', 'manufacturer', 
                    'strength', 'form', 'unit', 'barcode'
                ])
                ->with(['category:id,name,color'])
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->whereHas('transfers', function($q) {
                    $q->where('transfer_type', 'inventory_to_dispensary')
                      ->where('status', 'completed');
                })
                ->where(function($q) use ($query) {
                    if ($query) {
                        $q->where('name', 'like', "%{$query}%")
                          ->orWhere('generic_name', 'like', "%{$query}%")
                          ->orWhere('barcode', 'like', "%{$query}%");
                    }
                })
                ->orderBy('name')
                ->limit(20)
                ->get();
            });

            return response()->json([
                'success' => true,
                'data' => $medicines,
                'message' => 'Dispensary medicines retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to search dispensary medicines: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to search dispensary medicines: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Print sales report
     */
    public function printReport(Request $request)
    {
        try {
            $filters = $request->input('filters', []);
            
            $query = Sale::with(['items.medicine', 'soldBy']);

            // Apply filters
            if (isset($filters['status']) && $filters['status']) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['payment_method']) && $filters['payment_method']) {
                $query->where('payment_method', $filters['payment_method']);
            }

            if (isset($filters['date_from']) && $filters['date_from']) {
                $query->whereDate('sale_date', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to']) && $filters['date_to']) {
                $query->whereDate('sale_date', '<=', $filters['date_to']);
            }

            $sales = $query->orderBy('sale_date', 'desc')->get();
            
            // Calculate statistics
            $totalSales = $sales->count();
            $completedSales = $sales->where('status', 'completed')->count();
            $pendingSales = $sales->where('status', 'pending')->count();
            $totalRevenue = $sales->where('status', 'completed')->sum('total_amount');
            
            return view('print.sales-report', compact('sales', 'totalSales', 'completedSales', 'pendingSales', 'totalRevenue', 'filters'));

        } catch (\Exception $e) {
            Log::error('Sales print report failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate sales print report: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get optimized sales data for server-side rendering
     * Optimized for shared hosting with aggressive caching
     */
    private function getSalesDataOptimized(Request $request)
    {
        $cacheKey = 'sales_data_optimized_' . md5(serialize($request->all()));
        
        return Cache::remember($cacheKey, 300, function() use ($request) {
            
            // Build optimized query - select only required columns
            $query = Sale::select([
                'id', 'sale_number', 'customer_name', 'customer_phone', 
                'customer_email', 'subtotal', 'tax_amount', 'discount_amount', 
                'total_amount', 'payment_method', 'status', 'sale_date', 
                'sold_by', 'prescription_required', 'notes'
            ])
            ->with([
                'items:id,sale_id,medicine_id,quantity,unit_price,total_price',
                'items.medicine:id,name,generic_name,strength,form',
                'soldBy:id,name'
            ])
            ->orderBy('sale_date', 'desc');

            // Apply filters
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('payment_method') && $request->payment_method) {
                $query->where('payment_method', $request->payment_method);
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('sale_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('sale_date', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('sale_number', 'like', "%{$search}%")
                      ->orWhere('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                });
            }

            // Pagination - limit to 15 items for performance
            return $query->paginate(15);
        });
    }

    /**
     * Get optimized sales statistics for server-side rendering
     * Optimized for shared hosting with aggressive caching
     */
    private function getStatsDataOptimized(Request $request)
    {
        $cacheKey = 'sales_stats_optimized_' . md5(serialize($request->all()));
        
        return Cache::remember($cacheKey, 300, function() use ($request) {
            $query = Sale::query();

            // Apply date filters
            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('sale_date', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('sale_date', '<=', $request->date_to);
            }

            return [
                'total_sales' => $query->count(),
                'completed_sales' => $query->clone()->where('status', 'completed')->count(),
                'pending_sales' => $query->clone()->where('status', 'pending')->count(),
                'cancelled_sales' => $query->clone()->where('status', 'cancelled')->count(),
                'total_revenue' => $query->clone()->where('status', 'completed')->sum('total_amount'),
                'average_sale' => $query->clone()->where('status', 'completed')->avg('total_amount'),
                'today_sales' => Sale::whereDate('sale_date', today())->where('status', 'completed')->count(),
                'today_revenue' => Sale::whereDate('sale_date', today())->where('status', 'completed')->sum('total_amount')
            ];
        });
    }

    /**
     * Get optimized dispensary medicines for sale creation
     * Optimized for shared hosting with aggressive caching
     */
    private function getDispensaryMedicinesOptimized()
    {
        $cacheKey = 'dispensary_medicines_optimized';
        
        return Cache::remember($cacheKey, 300, function() {
            return Medicine::select([
                'id', 'name', 'generic_name', 'stock_quantity', 
                'selling_price', 'category_id', 'manufacturer', 
                'strength', 'form', 'unit', 'barcode'
            ])
            ->with(['category:id,name,color', 'transfers' => function($q) {
                $q->where('transfer_type', 'inventory_to_dispensary')
                  ->where('status', 'completed')
                  ->select('medicine_id', 'quantity_remaining');
            }])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->whereHas('transfers', function($q) {
                $q->where('transfer_type', 'inventory_to_dispensary')
                  ->where('status', 'completed');
            })
            ->orderBy('name')
            ->limit(50) // Limit for performance
            ->get()
            ->map(function($medicine) {
                // Calculate dispensary stock from transfers
                $dispensaryStock = $medicine->transfers->sum('quantity_remaining');
                $medicine->dispensary_stock = $dispensaryStock;
                return $medicine;
            });
        });
    }

    /**
     * Clear sales-related cache efficiently
     * Optimized for shared hosting
     */
    private function clearSalesCache()
    {
        try {
            // Clear specific cache keys instead of wildcard patterns
            $cacheKeys = [
                'sales_data_optimized_*',
                'sales_stats_optimized_*',
                'dispensary_medicines_optimized'
            ];
            
            foreach ($cacheKeys as $pattern) {
                if (str_contains($pattern, '*')) {
                    // For shared hosting, clear cache by pattern
                    Cache::flush(); // Simple approach for shared hosting
                    break;
                } else {
                    Cache::forget($pattern);
                }
            }
            
            Log::info('Sales cache cleared successfully');
        } catch (\Exception $e) {
            Log::error('Failed to clear sales cache: ' . $e->getMessage());
        }
    }

    /**
     * Get performance metrics for monitoring
     */
    public function getPerformanceMetrics()
    {
        try {
            $metrics = [
                'cache_hit_rate' => Cache::get('cache_hit_rate', 0),
                'avg_response_time' => Cache::get('avg_response_time', 0),
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
                'cache_size' => $this->getCacheSize(),
                'last_updated' => now()->toISOString()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to get performance metrics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get performance metrics'
            ], 500);
        }
    }

    /**
     * Get cache size for monitoring
     */
    private function getCacheSize()
    {
        try {
            $cachePath = storage_path('framework/cache');
            if (is_dir($cachePath)) {
                $size = 0;
                $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($cachePath));
                foreach ($files as $file) {
                    if ($file->isFile()) {
                        $size += $file->getSize();
                    }
                }
                return round($size / 1024, 2); // Return size in KB
            }
            return 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
