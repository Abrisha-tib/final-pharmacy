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
use Carbon\Carbon;

class CashierController extends Controller
{
    /**
     * Display the cashier dashboard
     * Optimized for shared hosting with aggressive caching
     */
    public function index(Request $request)
    {
        try {
            $start = microtime(true);
            $startMemory = memory_get_usage();
            
            // Get cashier metrics with caching
            $metrics = $this->getCashierMetricsOptimized();
            
            // Get sales data with optimized query
            $salesData = $this->getCashierSalesDataOptimized($request);
            
            $end = microtime(true);
            $endMemory = memory_get_usage();
            
            // Log performance metrics
            Log::info('Cashier Dashboard Performance', [
                'execution_time' => $end - $start,
                'memory_usage' => $endMemory - $startMemory,
                'peak_memory' => memory_get_peak_usage()
            ]);
            
            return view('cashier', compact('metrics', 'salesData'));
            
        } catch (\Exception $e) {
            Log::error('Cashier dashboard error: ' . $e->getMessage());
            
            return view('cashier', [
                'metrics' => $this->getDefaultMetrics(),
                'salesData' => collect()
            ]);
        }
    }

    /**
     * Get cashier metrics optimized for shared hosting
     */
    public function getMetrics(Request $request)
    {
        try {
            $cacheKey = 'cashier_metrics_' . md5(serialize($request->all()));
            
            $metrics = Cache::remember($cacheKey, 60, function() use ($request) {
                return $this->getCashierMetricsOptimized();
            });

            return response()->json([
                'success' => true,
                'data' => $metrics,
                'message' => 'Cashier metrics retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get cashier metrics: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve cashier metrics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get sales data for cashier interface
     */
    public function getSales(Request $request)
    {
        try {
            $sales = $this->getCashierSalesDataOptimized($request);

            return response()->json([
                'success' => true,
                'data' => $sales->items(), // Return only the items for API
                'pagination' => [
                    'current_page' => $sales->currentPage(),
                    'last_page' => $sales->lastPage(),
                    'per_page' => $sales->perPage(),
                    'total' => $sales->total(),
                    'from' => $sales->firstItem(),
                    'to' => $sales->lastItem()
                ],
                'message' => 'Sales data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get cashier sales data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales data: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process a pending sale (change status from pending to completed)
     */
    public function processSale(Request $request, $id)
    {
        try {
            DB::beginTransaction();

            $sale = Sale::findOrFail($id);
            
            if ($sale->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Sale is not in pending status'
                ], 400);
            }

            // Update sale status
            $sale->update([
                'status' => 'completed',
                'sale_date' => now()
            ]);

            // Clear relevant caches
            $this->clearCashierCache();

            DB::commit();

            return response()->json([
                'success' => true,
                'data' => $sale->load(['items.medicine', 'soldBy']),
                'message' => 'Sale processed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process sale: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to process sale: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate receipt for a sale
     */
    public function generateReceipt(Request $request, $id)
    {
        try {
            $sale = Sale::with(['items.medicine', 'soldBy'])->findOrFail($id);
            
            // Return sale data for receipt generation
            return response()->json([
                'success' => true,
                'data' => $sale,
                'message' => 'Receipt data retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to generate receipt: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate receipt: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get optimized cashier metrics
     * Uses database-level calculations for performance
     * Enhanced for SSE streaming with shorter cache times
     */
    private function getCashierMetricsOptimized()
    {
        $cacheKey = 'cashier_metrics_all_sales';
        
        return Cache::remember($cacheKey, 60, function() { // Increased cache time for better performance
            $start = microtime(true);
            
            // Use raw SQL for better performance on shared hosting
            // Calculate ALL sales across all pages (like Sales page)
            $metrics = DB::select("
                SELECT 
                    COUNT(CASE WHEN status = 'pending' THEN 1 END) as incoming_sales,
                    COUNT(CASE WHEN status = 'completed' THEN 1 END) as processed_sales,
                    COALESCE(SUM(CASE WHEN status = 'completed' THEN total_amount END), 0) as total_revenue,
                    AVG(CASE WHEN status = 'completed' THEN 
                        TIMESTAMPDIFF(SECOND, created_at, sale_date) 
                    END) as avg_processing_time
                FROM sales 
                WHERE status IN ('pending', 'completed')
            ")[0];

            $executionTime = microtime(true) - $start;
            
            // Log performance for monitoring
            if ($executionTime > 0.5) { // Log slow queries
                Log::warning('Slow cashier metrics query', [
                    'execution_time' => $executionTime,
                    'memory_usage' => memory_get_usage(true)
                ]);
            }

            return [
                'incoming_sales' => (int) $metrics->incoming_sales,
                'processed_sales' => (int) $metrics->processed_sales,
                'total_revenue' => number_format((float) $metrics->total_revenue, 2),
                'avg_processing_time' => $metrics->avg_processing_time ? round($metrics->avg_processing_time) . 's' : '0s'
            ];
        });
    }

    /**
     * Get optimized sales data for cashier interface
     */
    private function getCashierSalesDataOptimized(Request $request)
    {
        // Don't cache paginated results as they change with page parameter
        $query = Sale::select([
            'id', 'sale_number', 'customer_name', 'customer_phone', 
            'total_amount', 'payment_method', 'status', 'sale_date', 
            'sold_by', 'created_at'
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

        // Add pagination support
        $perPage = 12; // Items per page
        $page = $request->get('page', 1);
        
        return $query->paginate($perPage, ['*'], 'page', $page);
    }

    /**
     * Get default metrics for error handling
     */
    private function getDefaultMetrics()
    {
        return [
            'incoming_sales' => 0,
            'processed_sales' => 0,
            'total_revenue' => '0.00',
            'avg_processing_time' => '0s'
        ];
    }

    /**
     * Simple refresh endpoint (like Sales page)
     * No SSE complexity - just return current data
     */
    public function refresh(Request $request)
    {
        try {
            $metrics = $this->getCashierMetricsOptimized();
            $sales = $this->getSalesData($request);
            
            return response()->json([
                'success' => true,
                'metrics' => $metrics,
                'sales' => $sales,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Cashier refresh error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to refresh data'
            ], 500);
        }
    }

    /**
     * Get sale details for viewing
     */
    public function getSaleDetails($id)
    {
        try {
            $sale = Sale::with([
                'items.medicine',
                'soldBy'
            ])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $sale,
                'message' => 'Sale details retrieved successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to get sale details: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Sale not found'
            ], 404);
        }
    }

    /**
     * Clear cashier-related caches
     */
    private function clearCashierCache()
    {
        $patterns = [
            'cashier_metrics*',
            'cashier_sales*'
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }
}
