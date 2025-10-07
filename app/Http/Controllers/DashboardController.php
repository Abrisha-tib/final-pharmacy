<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Services\UserPreferencesService;

/**
 * Dashboard Controller
 * 
 * Handles the main dashboard functionality for the pharmacy management system.
 * Optimized for performance with caching and minimal database queries.
 * 
 * @author Analog Software Solutions
 * @version 1.0
 */
class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * Apply authentication and permission middleware.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(\Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-sales');
    }

    /**
     * Display the dashboard with analytics and charts.
     * 
     * This method renders the main dashboard view with:
     * - Sales analytics and metrics
     * - Inventory status
     * - Recent transactions
     * - Performance indicators
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            // Get dashboard data with caching for performance
            $dashboardData = $this->getDashboardData();
            
            // Get user preferences
            $userPreferences = UserPreferencesService::getPreferences(Auth::user());
            $dashboardData['userPreferences'] = $userPreferences;
            
            // Return the dashboard view with data
            return view('dashboard', $dashboardData);
            
        } catch (\Exception $e) {
            // Log error for debugging (in production, use proper logging)
            \Log::error('Dashboard error: ' . $e->getMessage());
            
            // Return dashboard with empty data to prevent crashes
            return view('dashboard', $this->getEmptyDashboardData());
        }
    }

    /**
     * Get dashboard data with caching for performance optimization.
     * 
     * Uses Laravel's cache system to store expensive calculations
     * and reduce database load for cPanel shared hosting.
     * 
     * @return array
     */
    private function getDashboardData()
    {
        // Temporarily disable caching to force fresh data
        // Cache key for dashboard data (expires in 5 minutes)
        $cacheKey = 'dashboard_data_' . Auth::id();
        
        // Clear any existing cache first
        Cache::forget($cacheKey);
        
        // Get fresh data without caching for now
        $activeCustomersData = $this->getActiveCustomersCount();
        $totalSalesData = $this->getTotalSales();
        return [
            'totalSales' => $totalSalesData['amount'],
            'totalSalesGrowth' => $totalSalesData['growth'],
            'inventoryCount' => $this->getInventoryCount(),
            'lowStockCount' => $this->getLowStockCount(),
            'activeCustomers' => $activeCustomersData['count'],
            'activeCustomersGrowth' => $activeCustomersData['growth'],
            'recentSales' => $this->getRecentSales(),
            'topProducts' => $this->getTopProducts(),
            'monthlySalesData' => $this->getMonthlySalesData(),
        ];
    }

    /**
     * Get total sales amount for the current month with growth calculation.
     * 
     * @return array
     */
    private function getTotalSales()
    {
        try {
            $currentMonth = now()->startOfMonth();
            $nextMonth = now()->addMonth()->startOfMonth();
            $previousMonth = now()->subMonth()->startOfMonth();
            $currentMonthStart = now()->startOfMonth();
            
            // Current month sales
            $currentSales = DB::table('sales')
                ->where('status', 'completed')
                ->whereBetween('sale_date', [$currentMonth, $nextMonth])
                ->sum('total_amount');
            
            // Previous month sales
            $previousSales = DB::table('sales')
                ->where('status', 'completed')
                ->whereBetween('sale_date', [$previousMonth, $currentMonthStart])
                ->sum('total_amount');
            
            // Calculate growth percentage
            $growth = 0;
            if ($previousSales > 0) {
                $growth = (($currentSales - $previousSales) / $previousSales) * 100;
            } elseif ($currentSales > 0) {
                $growth = 100; // 100% growth if no previous sales but current ones exist
            }
            
            return [
                'amount' => (float) $currentSales,
                'growth' => round($growth, 1)
            ];
        } catch (\Exception $e) {
            \Log::error('Error calculating total sales: ' . $e->getMessage());
            return ['amount' => 0.00, 'growth' => 0];
        }
    }

    /**
     * Get total inventory items count.
     * 
     * @return int
     */
    private function getInventoryCount()
    {
        try {
            $inventoryCount = DB::table('medicines')
                ->where('is_active', true)
                ->where('stock_quantity', '>', 0)
                ->count();
                
            return (int) $inventoryCount;
        } catch (\Exception $e) {
            \Log::error('Error calculating inventory count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get count of items with low stock.
     * 
     * @return int
     */
    private function getLowStockCount()
    {
        try {
            $lowStockCount = DB::table('medicines')
                ->where('is_active', true)
                ->whereRaw('stock_quantity <= reorder_level')
                ->where('stock_quantity', '>', 0)
                ->count();
                
            return (int) $lowStockCount;
        } catch (\Exception $e) {
            \Log::error('Error calculating low stock count: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get count of active customers with growth calculation.
     * 
     * @return array
     */
    private function getActiveCustomersCount()
    {
        try {
            // Get current active customers (last 90 days)
            $currentActiveCustomers = DB::table('customers')
                ->where('is_active', true)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('sales')
                        ->whereRaw('sales.customer_name = customers.name')
                        ->where('sales.sale_date', '>=', now()->subDays(90))
                        ->where('sales.status', 'completed');
                })
                ->count();
            
            // Get previous period active customers (90-180 days ago)
            $previousActiveCustomers = DB::table('customers')
                ->where('is_active', true)
                ->whereExists(function ($query) {
                    $query->select(DB::raw(1))
                        ->from('sales')
                        ->whereRaw('sales.customer_name = customers.name')
                        ->whereBetween('sales.sale_date', [
                            now()->subDays(180),
                            now()->subDays(90)
                        ])
                        ->where('sales.status', 'completed');
                })
                ->count();
            
            // Calculate growth percentage
            $growth = 0;
            if ($previousActiveCustomers > 0) {
                $growth = (($currentActiveCustomers - $previousActiveCustomers) / $previousActiveCustomers) * 100;
            } elseif ($currentActiveCustomers > 0) {
                $growth = 100; // 100% growth if no previous customers but current ones exist
            }
            
            return [
                'count' => (int) $currentActiveCustomers,
                'growth' => round($growth, 1)
            ];
        } catch (\Exception $e) {
            \Log::error('Error calculating active customers count: ' . $e->getMessage());
            return ['count' => 0, 'growth' => 0];
        }
    }

    /**
     * Get recent sales transactions.
     * 
     * @return array
     */
    private function getRecentSales()
    {
        try {
            $recentSales = DB::table('sales')
                ->select([
                    'sale_number as id',
                    'customer_name as customer',
                    'total_amount as amount',
                    'sale_date',
                    DB::raw('(SELECT COUNT(*) FROM sale_items WHERE sale_items.sale_id = sales.id) as items')
                ])
                ->where('status', 'completed')
                ->orderBy('sale_date', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($sale) {
                    return [
                        'id' => $sale->id,
                        'customer' => $sale->customer ?: 'Walk-in Customer',
                        'items' => (int) $sale->items,
                        'amount' => (float) $sale->amount,
                        'time' => $this->getTimeAgo($sale->sale_date)
                    ];
                })
                ->toArray();
                
            return $recentSales;
        } catch (\Exception $e) {
            \Log::error('Error fetching recent sales: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get human-readable time ago string.
     * 
     * @param string $datetime
     * @return string
     */
    private function getTimeAgo($datetime)
    {
        $time = \Carbon\Carbon::parse($datetime);
        $now = \Carbon\Carbon::now();
        
        // Get the difference in various units (convert to integers)
        $diffInMinutes = (int) $time->diffInMinutes($now);
        $diffInHours = (int) $time->diffInHours($now);
        $diffInDays = (int) $time->diffInDays($now);
        $diffInWeeks = (int) $time->diffInWeeks($now);
        $diffInMonths = (int) $time->diffInMonths($now);
        $diffInYears = (int) $time->diffInYears($now);
        
        // Return appropriate format based on time difference
        if ($diffInMinutes < 1) {
            return 'Just now';
        } elseif ($diffInMinutes < 60) {
            return $diffInMinutes . ' min ago';
        } elseif ($diffInHours < 24) {
            return $diffInHours . ' hour' . ($diffInHours > 1 ? 's' : '') . ' ago';
        } elseif ($diffInDays < 7) {
            return $diffInDays . ' day' . ($diffInDays > 1 ? 's' : '') . ' ago';
        } elseif ($diffInWeeks < 4) {
            return $diffInWeeks . ' week' . ($diffInWeeks > 1 ? 's' : '') . ' ago';
        } elseif ($diffInMonths < 12) {
            return $diffInMonths . ' month' . ($diffInMonths > 1 ? 's' : '') . ' ago';
        } else {
            return $diffInYears . ' year' . ($diffInYears > 1 ? 's' : '') . ' ago';
        }
    }

    /**
     * Get top selling products.
     * 
     * @return array
     */
    private function getTopProducts()
    {
        try {
            $topProducts = DB::table('sale_items')
                ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
                ->join('categories', 'medicines.category_id', '=', 'categories.id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->select([
                    'medicines.name',
                    'categories.name as category',
                    DB::raw('SUM(sale_items.quantity) as units'),
                    DB::raw('COUNT(DISTINCT sales.id) as sales_count')
                ])
                ->where('sales.status', 'completed')
                ->where('sales.sale_date', '>=', now()->subDays(30))
                ->groupBy('medicines.id', 'medicines.name', 'categories.name')
                ->orderBy('units', 'desc')
                ->limit(3)
                ->get()
                ->map(function ($product) {
                    return [
                        'name' => $product->name,
                        'category' => $product->category,
                        'units' => (int) $product->units,
                        'growth' => $this->calculateGrowth($product->name, $product->units)
                    ];
                })
                ->toArray();
                
            return $topProducts;
        } catch (\Exception $e) {
            \Log::error('Error fetching top products: ' . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Calculate growth percentage for a product.
     * 
     * @param string $productName
     * @param int $currentUnits
     * @return string
     */
    private function calculateGrowth($productName, $currentUnits)
    {
        try {
            // Get previous month's sales for comparison
            $previousMonthUnits = DB::table('sale_items')
                ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
                ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
                ->where('medicines.name', $productName)
                ->where('sales.status', 'completed')
                ->whereBetween('sales.sale_date', [
                    now()->subDays(60)->startOfDay(),
                    now()->subDays(30)->endOfDay()
                ])
                ->sum('sale_items.quantity');
                
            if ($previousMonthUnits == 0) {
                return '+100%';
            }
            
            $growth = (($currentUnits - $previousMonthUnits) / $previousMonthUnits) * 100;
            $sign = $growth >= 0 ? '+' : '';
            
            return $sign . round($growth, 0) . '%';
        } catch (\Exception $e) {
            return '+0%';
        }
    }

    /**
     * Get monthly sales data for Chart.js.
     * 
     * @return array
     */
    private function getMonthlySalesData()
    {
        try {
            // Get last 3 months of sales data
            $months = [];
            $salesData = [];
            
            for ($i = 2; $i >= 0; $i--) {
                $month = now()->subMonths($i);
                $months[] = $month->format('F');
                
                $monthlySales = DB::table('sales')
                    ->where('status', 'completed')
                    ->whereYear('sale_date', $month->year)
                    ->whereMonth('sale_date', $month->month)
                    ->sum('total_amount');
                    
                $salesData[] = (float) $monthlySales;
            }
            
            return [
                'labels' => $months,
                'data' => $salesData
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching monthly sales data: ' . $e->getMessage());
            return [
                'labels' => ['January', 'February', 'March'],
                'data' => [0, 0, 0]
            ];
        }
    }

    /**
     * Get empty dashboard data for error handling.
     * 
     * @return array
     */
    private function getEmptyDashboardData()
    {
        return [
            'totalSales' => 0,
            'inventoryCount' => 0,
            'lowStockCount' => 0,
            'activeCustomers' => 0,
            'recentSales' => [],
            'topProducts' => [],
            'monthlySalesData' => [
                'labels' => ['January', 'February', 'March'],
                'data' => [0, 0, 0]
            ]
        ];
    }

    /**
     * Clear dashboard cache (useful for admin functions).
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            $cacheKey = 'dashboard_data_' . Auth::id();
            Cache::forget($cacheKey);
            
            return response()->json([
                'success' => true,
                'message' => 'Dashboard cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get dashboard statistics for API endpoints.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStats()
    {
        try {
            $data = $this->getDashboardData();
            
            return response()->json([
                'success' => true,
                'data' => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch dashboard statistics'
            ], 500);
        }
    }
    
    /**
     * Get real-time dashboard updates.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getRealTimeUpdates()
    {
        try {
            // Clear cache to get fresh data
            $cacheKey = 'dashboard_data_' . Auth::id();
            Cache::forget($cacheKey);
            
            $data = $this->getDashboardData();
            
            return response()->json([
                'success' => true,
                'data' => $data,
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            \Log::error('Real-time dashboard update error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch real-time updates'
            ], 500);
        }
    }
    
    /**
     * Get low stock alerts with detailed information.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLowStockAlerts()
    {
        try {
            $lowStockItems = DB::table('medicines')
                ->join('categories', 'medicines.category_id', '=', 'categories.id')
                ->select([
                    'medicines.id',
                    'medicines.name',
                    'medicines.stock_quantity',
                    'medicines.reorder_level',
                    'categories.name as category'
                ])
                ->where('medicines.is_active', true)
                ->whereRaw('medicines.stock_quantity <= medicines.reorder_level')
                ->where('medicines.stock_quantity', '>', 0)
                ->orderBy('medicines.stock_quantity', 'asc')
                ->limit(10)
                ->get();
                
            return response()->json([
                'success' => true,
                'data' => $lowStockItems,
                'count' => $lowStockItems->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Low stock alerts error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch low stock alerts'
            ], 500);
        }
    }
    
    /**
     * Get chart data for different time periods.
     * 
     * @param string $period
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChartData($period)
    {
        try {
            $chartData = $this->getSalesDataForPeriod($period);
            
            return response()->json([
                'success' => true,
                'data' => $chartData
            ]);
        } catch (\Exception $e) {
            \Log::error('Chart data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch chart data'
            ], 500);
        }
    }
    
    /**
     * Get sales data for specific time period.
     * 
     * @param string $period
     * @return array
     */
    private function getSalesDataForPeriod($period)
    {
        try {
            $months = [];
            $salesData = [];
            
            switch ($period) {
                case '3m':
                    // Last 3 months
                    for ($i = 2; $i >= 0; $i--) {
                        $month = now()->subMonths($i);
                        $months[] = $month->format('M Y');
                        
                        $monthlySales = DB::table('sales')
                            ->where('status', 'completed')
                            ->whereYear('sale_date', $month->year)
                            ->whereMonth('sale_date', $month->month)
                            ->sum('total_amount');
                            
                        $salesData[] = (float) $monthlySales;
                    }
                    break;
                    
                case '6m':
                    // Last 6 months
                    for ($i = 5; $i >= 0; $i--) {
                        $month = now()->subMonths($i);
                        $months[] = $month->format('M Y');
                        
                        $monthlySales = DB::table('sales')
                            ->where('status', 'completed')
                            ->whereYear('sale_date', $month->year)
                            ->whereMonth('sale_date', $month->month)
                            ->sum('total_amount');
                            
                        $salesData[] = (float) $monthlySales;
                    }
                    break;
                    
                case '1y':
                    // Last 12 months
                    for ($i = 11; $i >= 0; $i--) {
                        $month = now()->subMonths($i);
                        $months[] = $month->format('M Y');
                        
                        $monthlySales = DB::table('sales')
                            ->where('status', 'completed')
                            ->whereYear('sale_date', $month->year)
                            ->whereMonth('sale_date', $month->month)
                            ->sum('total_amount');
                            
                        $salesData[] = (float) $monthlySales;
                    }
                    break;
                    
                default:
                    // Default to 3 months
                    for ($i = 2; $i >= 0; $i--) {
                        $month = now()->subMonths($i);
                        $months[] = $month->format('M Y');
                        
                        $monthlySales = DB::table('sales')
                            ->where('status', 'completed')
                            ->whereYear('sale_date', $month->year)
                            ->whereMonth('sale_date', $month->month)
                            ->sum('total_amount');
                            
                        $salesData[] = (float) $monthlySales;
                    }
            }
            
            return [
                'labels' => $months,
                'data' => $salesData
            ];
        } catch (\Exception $e) {
            \Log::error('Error fetching sales data for period: ' . $e->getMessage());
            return [
                'labels' => [],
                'data' => []
            ];
        }
    }
}
