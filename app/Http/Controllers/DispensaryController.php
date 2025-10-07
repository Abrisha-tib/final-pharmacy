<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\Category;
use App\Models\Transfer;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DispensaryController extends Controller
{
    /**
     * Display the dispensary page with medicine inventory for dispensing
     */
    public function index(Request $request)
    {
        $perPage = 12;
        
        // Build optimized query for dispensary - only medicines that have been transferred to dispensary
        $query = Medicine::select([
            'id', 'name', 'generic_name', 'stock_quantity', 
            'selling_price', 'cost_price', 'category_id', 
            'is_active', 'batch_number', 'expiry_date',
            'manufacturer', 'strength', 'form', 'unit', 'barcode'
        ])->with(['category:id,name,color', 'transfers' => function($q) {
            $q->where('transfer_type', 'inventory_to_dispensary')
              ->where('status', 'completed');
        }])
          ->where('is_active', true)
          ->where('stock_quantity', '>', 0)
          ->whereHas('transfers', function($q) {
              $q->where('transfer_type', 'inventory_to_dispensary')
                ->where('status', 'completed');
          });

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
        
        // Stock status filter
        if ($request->has('stock') && $request->stock) {
            switch ($request->stock) {
                case 'in-stock':
                    $query->where('stock_quantity', '>', 10);
                    break;
                case 'low-stock':
                    $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
                    break;
            }
        }
        
        // Apply pagination (temporarily without caching for testing)
        $medicines = $query->orderBy('name')->paginate($perPage);
        
        // Cache categories
        $categories = Cache::remember('categories_active', 3600, function() {
            return Category::active()->ordered()->get();
        });
        
        // Cache dispensary statistics - only for transferred medicines
        $statsCacheKey = 'dispensary_stats_global';
        $stats = Cache::remember($statsCacheKey, 300, function() {
            $totalMedicines = Medicine::where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->whereHas('transfers', function($q) {
                    $q->where('transfer_type', 'inventory_to_dispensary')
                      ->where('status', 'completed');
                })->count();
                
            $lowStockMedicines = Medicine::where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->where('stock_quantity', '<=', 10)
                ->whereHas('transfers', function($q) {
                    $q->where('transfer_type', 'inventory_to_dispensary')
                      ->where('status', 'completed');
                })->count();
            
            $medicines = Medicine::where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->whereHas('transfers', function($q) {
                    $q->where('transfer_type', 'inventory_to_dispensary')
                      ->where('status', 'completed');
                })
                ->select('selling_price', 'stock_quantity')
                ->get();
            
            $totalValue = $medicines->sum(function($medicine) {
                return (float)$medicine->selling_price * (int)$medicine->stock_quantity;
            });
            
            return [
                'totalMedicines' => $totalMedicines,
                'lowStockMedicines' => $lowStockMedicines,
                'totalValue' => $totalValue,
                'inStock' => $totalMedicines - $lowStockMedicines
            ];
        });
        
        extract($stats);
        
        return view('dispensary', compact('medicines', 'categories', 'totalMedicines', 'lowStockMedicines', 'totalValue', 'inStock', 'request'));
    }
    
    /**
     * Handle dispensary form submissions
     */
    public function filter(Request $request)
    {
        // Build query parameters from POST data
        $params = [];
        
        if ($request->has('search') && $request->search) {
            $params['search'] = $request->search;
        }
        
        if ($request->has('category') && $request->category) {
            $params['category'] = $request->category;
        }
        
        if ($request->has('stock') && $request->stock) {
            $params['stock'] = $request->stock;
        }
        
        if ($request->has('page') && $request->page) {
            $params['page'] = $request->page;
        }
        
        // Redirect to GET route with parameters
        return redirect()->route('dispensary', $params);
    }
    
    /**
     * Get medicine details for dispensing
     */
    public function getMedicineDetails($id)
    {
        try {
            $medicine = Medicine::with(['category:id,name,color', 'transfers' => function($q) {
                $q->where('transfer_type', 'inventory_to_dispensary')
                  ->where('status', 'completed');
            }])
            ->where('is_active', true)
            ->where('stock_quantity', '>', 0)
            ->whereHas('transfers', function($q) {
                $q->where('transfer_type', 'inventory_to_dispensary')
                  ->where('status', 'completed');
            })
            ->find($id);

            if (!$medicine) {
                return response()->json(['error' => 'Medicine not found'], 404);
            }

            // Format the expiry date for better display
            $medicine->expiry_date_formatted = $medicine->expiry_date ? $medicine->expiry_date->format('F j, Y') : null;
            
            return response()->json($medicine);
        } catch (\Exception $e) {
            Log::error('Error fetching medicine details: ' . $e->getMessage());
            return response()->json(['error' => 'Error fetching medicine details'], 500);
        }
    }
    
    /**
     * Clear dispensary cache
     */
    public function clearCache()
    {
        Cache::forget('dispensary_stats_global');
        Cache::flush();
        
        return response()->json(['message' => 'Dispensary cache cleared successfully']);
    }
    
    /**
     * Get comprehensive analytics data for dispensary
     */
    public function getAnalytics()
    {
        try {
            $cacheKey = 'dispensary_analytics_' . date('Y-m-d-H');
            $analytics = Cache::remember($cacheKey, 3600, function() {
                // Basic metrics - only transferred medicines
                $totalMedicines = Medicine::where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->whereHas('transfers', function($q) {
                        $q->where('transfer_type', 'inventory_to_dispensary')
                          ->where('status', 'completed');
                    })->count();
                    
                $availableMedicines = Medicine::where('is_active', true)
                    ->where('stock_quantity', '>', 10)
                    ->whereHas('transfers', function($q) {
                        $q->where('transfer_type', 'inventory_to_dispensary')
                          ->where('status', 'completed');
                    })->count();
                    
                $lowStockItems = Medicine::where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->where('stock_quantity', '<=', 10)
                    ->whereHas('transfers', function($q) {
                        $q->where('transfer_type', 'inventory_to_dispensary')
                          ->where('status', 'completed');
                    })->count();
                $totalTransfers = Transfer::count();
                
                // Calculate changes from last month
                $lastMonth = Carbon::now()->subMonth();
                $lastMonthMedicines = Medicine::where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->where('created_at', '<=', $lastMonth)
                    ->count();
                $medicinesChange = $lastMonthMedicines > 0 ? 
                    round((($totalMedicines - $lastMonthMedicines) / $lastMonthMedicines) * 100, 1) : 0;
                
                $lastMonthAvailable = Medicine::where('is_active', true)
                    ->where('stock_quantity', '>', 10)
                    ->where('created_at', '<=', $lastMonth)
                    ->count();
                $availableChange = $lastMonthAvailable > 0 ? 
                    round((($availableMedicines - $lastMonthAvailable) / $lastMonthAvailable) * 100, 1) : 0;
                
                $lastMonthTransfers = Transfer::where('created_at', '<=', $lastMonth)->count();
                $transfersChange = $lastMonthTransfers > 0 ? 
                    round((($totalTransfers - $lastMonthTransfers) / $lastMonthTransfers) * 100, 1) : 0;
                
                $lastMonthLowStock = Medicine::where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->where('stock_quantity', '<=', 10)
                    ->where('created_at', '<=', $lastMonth)
                    ->count();
                $lowStockChange = $lastMonthLowStock > 0 ? 
                    round((($lowStockItems - $lastMonthLowStock) / $lastMonthLowStock) * 100, 1) : 0;
                
                // Category distribution - only transferred medicines
                $categoryDistribution = Medicine::where('is_active', true)
                    ->where('stock_quantity', '>', 0)
                    ->whereHas('transfers', function($q) {
                        $q->where('transfer_type', 'inventory_to_dispensary')
                          ->where('status', 'completed');
                    })
                    ->with('category')
                    ->get()
                    ->groupBy('category.name')
                    ->map(function($medicines, $categoryName) {
                        return [
                            'name' => $categoryName ?: 'Uncategorized',
                            'count' => $medicines->count()
                        ];
                    })
                    ->values()
                    ->toArray();
                
                // Stock status distribution
                $stockStatus = [
                    ['status' => 'Available', 'count' => $availableMedicines],
                    ['status' => 'Low Stock', 'count' => $lowStockItems],
                    ['status' => 'Out of Stock', 'count' => Medicine::where('is_active', true)->where('stock_quantity', 0)->count()],
                    ['status' => 'Expired', 'count' => Medicine::where('is_active', true)->where('expiry_date', '<', now())->count()]
                ];
                
                // Transfer trends (last 30 days)
                $transferTrends = [];
                for ($i = 29; $i >= 0; $i--) {
                    $date = Carbon::now()->subDays($i);
                    $count = Transfer::whereDate('created_at', $date)->count();
                    $transferTrends[] = [
                        'date' => $date->format('M d'),
                        'count' => $count
                    ];
                }
                
                // Transfer status distribution
                $transferStatus = Transfer::select('status', DB::raw('count(*) as count'))
                    ->groupBy('status')
                    ->get()
                    ->map(function($item) {
                        return [
                            'status' => ucfirst($item->status),
                            'count' => $item->count
                        ];
                    })
                    ->toArray();
                
                // Top categories
                $topCategories = collect($categoryDistribution)
                    ->sortByDesc('count')
                    ->take(5)
                    ->map(function($category) use ($totalMedicines) {
                        return [
                            'name' => $category['name'],
                            'count' => $category['count'],
                            'percentage' => round(($category['count'] / $totalMedicines) * 100, 1)
                        ];
                    })
                    ->values()
                    ->toArray();
                
                // Recent transfers
                $recentTransfers = Transfer::with('medicine')
                    ->orderBy('created_at', 'desc')
                    ->take(10)
                    ->get()
                    ->map(function($transfer) {
                        return [
                            'medicine_name' => $transfer->medicine->name ?? 'Unknown',
                            'quantity' => $transfer->quantity_transferred,
                            'status' => ucfirst($transfer->status),
                            'date' => $transfer->created_at->format('M d, Y')
                        ];
                    })
                    ->toArray();
                
                return [
                    'total_medicines' => $totalMedicines,
                    'available_medicines' => $availableMedicines,
                    'total_transfers' => $totalTransfers,
                    'low_stock_items' => $lowStockItems,
                    'medicines_change' => $medicinesChange,
                    'available_change' => $availableChange,
                    'transfers_change' => $transfersChange,
                    'low_stock_change' => $lowStockChange,
                    'category_distribution' => $categoryDistribution,
                    'stock_status' => $stockStatus,
                    'transfer_trends' => $transferTrends,
                    'transfer_status' => $transferStatus,
                    'top_categories' => $topCategories,
                    'recent_transfers' => $recentTransfers
                ];
            });
            
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error loading dispensary analytics: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading analytics data'
            ], 500);
        }
    }
    
    /**
     * Export analytics report
     */
    public function exportAnalytics()
    {
        // This would generate and download an analytics report
        // For now, return a simple response
        return response()->json(['message' => 'Analytics export feature coming soon']);
    }
    
    /**
     * Get categories for filters
     */
    public function getCategories()
    {
        try {
            $categories = Cache::remember('categories_for_analytics', 3600, function() {
                return Category::active()->ordered()->get(['id', 'name']);
            });
            
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading categories'
            ], 500);
        }
    }
    
    /**
     * Get export statistics
     */
    public function getExportStats(Request $request)
    {
        try {
            $filters = $request->get('filters', []);
            
            $query = Medicine::where('is_active', true);
            
            // Apply filters
            if (isset($filters['category_id']) && $filters['category_id']) {
                $query->where('category_id', $filters['category_id']);
            }
            
            if (isset($filters['dispensary_status']) && $filters['dispensary_status']) {
                switch ($filters['dispensary_status']) {
                    case 'available':
                        $query->where('stock_quantity', '>', 10);
                        break;
                    case 'low_stock':
                        $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
                        break;
                    case 'out_of_stock':
                        $query->where('stock_quantity', 0);
                        break;
                    case 'expired':
                        $query->where('expiry_date', '<', now());
                        break;
                }
            }
            
            $totalMedicines = $query->count();
            $availableMedicines = $query->where('stock_quantity', '>', 10)->count();
            $lowStockItems = $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10)->count();
            $totalTransfers = Transfer::count();
            
            return response()->json([
                'success' => true,
                'data' => [
                    'total_medicines' => $totalMedicines,
                    'available_medicines' => $availableMedicines,
                    'total_transfers' => $totalTransfers,
                    'low_stock_items' => $lowStockItems
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading export statistics'
            ], 500);
        }
    }
    
    /**
     * Download template
     */
    public function downloadTemplate()
    {
        // This would generate and download a template file
        // For now, return a simple response
        return response()->json(['message' => 'Template download feature coming soon']);
    }
    
    /**
     * Import data
     */
    public function importData(Request $request)
    {
        // This would handle file import
        // For now, return a simple response
        return response()->json(['message' => 'Import feature coming soon']);
    }
    
    /**
     * Export data
     */
    public function exportData(Request $request)
    {
        // This would handle data export
        // For now, return a simple response
        return response()->json(['message' => 'Export feature coming soon']);
    }
    
    /**
     * Print report
     */
    public function printReport(Request $request)
    {
        // This would generate a print report
        // For now, return a simple response
        return response()->json(['message' => 'Print feature coming soon']);
    }

}
