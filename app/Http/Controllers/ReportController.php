<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Medicine;
use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Category;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display the main reports dashboard
     * Optimized for shared hosting with comprehensive caching
     */
    public function index(Request $request)
    {
        try {
            // Get date range from request or default to last 30 days
            $dateRange = $this->getDateRange($request);
            
            // Cache key based on date range and user
            $cacheKey = 'reports_dashboard_' . md5(serialize($dateRange) . auth()->id());
            
            $reports = Cache::remember($cacheKey, 300, function() use ($dateRange) {
                return $this->generateReportsData($dateRange);
            });

            return view('reports', compact('reports', 'dateRange'));
        } catch (\Exception $e) {
            Log::error('Reports Dashboard Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load reports. Please try again.');
        }
    }

    /**
     * Get sales reports with comprehensive analytics
     * Database-level calculations for shared hosting optimization
     */
    public function salesReports(Request $request): JsonResponse
    {
        try {
            $dateRange = $this->getDateRange($request);
            $cacheKey = 'sales_reports_' . md5(serialize($dateRange));
            
            $salesData = Cache::remember($cacheKey, 300, function() use ($dateRange) {
                return $this->generateSalesReports($dateRange);
            });

            return response()->json([
                'success' => true,
                'data' => $salesData
            ]);
        } catch (\Exception $e) {
            Log::error('Sales Reports Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load sales reports'
            ], 500);
        }
    }

    /**
     * Get inventory reports with stock analysis
     */
    public function inventoryReports(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'inventory_reports_' . auth()->id();
            
            $inventoryData = Cache::remember($cacheKey, 600, function() {
                return $this->generateInventoryReports();
            });

            return response()->json([
                'success' => true,
                'data' => $inventoryData
            ]);
        } catch (\Exception $e) {
            Log::error('Inventory Reports Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load inventory reports'
            ], 500);
        }
    }

    /**
     * Get financial reports with profit analysis
     */
    public function financialReports(Request $request): JsonResponse
    {
        try {
            $dateRange = $this->getDateRange($request);
            $cacheKey = 'financial_reports_' . md5(serialize($dateRange));
            
            $financialData = Cache::remember($cacheKey, 300, function() use ($dateRange) {
                return $this->generateFinancialReports($dateRange);
            });

            return response()->json([
                'success' => true,
                'data' => $financialData
            ]);
        } catch (\Exception $e) {
            Log::error('Financial Reports Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load financial reports'
            ], 500);
        }
    }

    /**
     * Get customer reports with demographics and loyalty analysis
     */
    public function customerReports(Request $request): JsonResponse
    {
        try {
            $cacheKey = 'customer_reports_' . auth()->id();
            
            $customerData = Cache::remember($cacheKey, 600, function() {
                return $this->generateCustomerReports();
            });

            return response()->json([
                'success' => true,
                'data' => $customerData
            ]);
        } catch (\Exception $e) {
            Log::error('Customer Reports Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load customer reports'
            ], 500);
        }
    }

    /**
     * Get purchase reports with supplier analysis
     */
    public function purchaseReports(Request $request): JsonResponse
    {
        try {
            $dateRange = $this->getDateRange($request);
            $cacheKey = 'purchase_reports_' . md5(serialize($dateRange));
            
            $purchaseData = Cache::remember($cacheKey, 300, function() use ($dateRange) {
                return $this->generatePurchaseReports($dateRange);
            });

            return response()->json([
                'success' => true,
                'data' => $purchaseData
            ]);
        } catch (\Exception $e) {
            Log::error('Purchase Reports Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load purchase reports'
            ], 500);
        }
    }

    /**
     * Export reports to various formats
     */
    public function export(Request $request, $format = 'pdf')
    {
        try {
            $reportType = $request->get('type', 'sales');
            $dateRange = $this->getDateRange($request);
            
            // Implementation for export functionality
            // This would integrate with PDF/Excel libraries
            
            return response()->json([
                'success' => true,
                'message' => 'Export functionality will be implemented'
            ]);
        } catch (\Exception $e) {
            Log::error('Export Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to export report'
            ], 500);
        }
    }

    /**
     * Clear reports cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Reports cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }

    /**
     * Generate comprehensive reports data
     * All calculations done at database level for shared hosting optimization
     */
    private function generateReportsData(array $dateRange): array
    {
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Sales summary using single optimized query
        $salesSummary = DB::table('sales')
            ->selectRaw('
                COUNT(*) as total_sales,
                COUNT(CASE WHEN status = "completed" THEN 1 END) as completed_sales,
                COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_sales,
                COALESCE(SUM(CASE WHEN status = "completed" THEN total_amount END), 0) as total_revenue,
                COALESCE(AVG(CASE WHEN status = "completed" THEN total_amount END), 0) as avg_sale_amount,
                COUNT(CASE WHEN payment_method = "cash" THEN 1 END) as cash_sales,
                COUNT(CASE WHEN payment_method = "card" THEN 1 END) as card_sales
            ')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->first();

        // Top selling medicines
        $topMedicines = DB::table('sale_items')
            ->join('medicines', 'sale_items.medicine_id', '=', 'medicines.id')
            ->join('sales', 'sale_items.sale_id', '=', 'sales.id')
            ->selectRaw('
                medicines.name,
                medicines.generic_name,
                SUM(sale_items.quantity) as total_quantity,
                SUM(sale_items.total_price) as total_revenue,
                COUNT(DISTINCT sales.id) as sale_count
            ')
            ->whereBetween('sales.sale_date', [$startDate, $endDate])
            ->where('sales.status', 'completed')
            ->groupBy('medicines.id', 'medicines.name', 'medicines.generic_name')
            ->orderBy('total_quantity', 'desc')
            ->limit(10)
            ->get();

        // Daily sales trend
        $dailyTrend = DB::table('sales')
            ->selectRaw('
                DATE(sale_date) as date,
                COUNT(*) as sales_count,
                COALESCE(SUM(CASE WHEN status = "completed" THEN total_amount END), 0) as revenue
            ')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'sales_summary' => [
                'total_sales' => (int) $salesSummary->total_sales,
                'completed_sales' => (int) $salesSummary->completed_sales,
                'pending_sales' => (int) $salesSummary->pending_sales,
                'total_revenue' => (float) $salesSummary->total_revenue,
                'avg_sale_amount' => (float) $salesSummary->avg_sale_amount,
                'cash_sales' => (int) $salesSummary->cash_sales,
                'card_sales' => (int) $salesSummary->card_sales
            ],
            'top_medicines' => $topMedicines,
            'daily_trend' => $dailyTrend,
            'date_range' => $dateRange
        ];
    }

    /**
     * Generate detailed sales reports
     */
    private function generateSalesReports(array $dateRange): array
    {
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Revenue by payment method
        $revenueByPayment = DB::table('sales')
            ->selectRaw('
                payment_method,
                COUNT(*) as transaction_count,
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COALESCE(AVG(total_amount), 0) as avg_transaction
            ')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupBy('payment_method')
            ->get();

        // Sales by hour (for daily patterns)
        $salesByHour = DB::table('sales')
            ->selectRaw('
                HOUR(sale_date) as hour,
                COUNT(*) as sales_count,
                COALESCE(SUM(total_amount), 0) as revenue
            ')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        // Customer analysis
        $customerAnalysis = DB::table('sales')
            ->selectRaw('
                COUNT(DISTINCT customer_email) as unique_customers,
                COUNT(*) as total_transactions,
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COALESCE(AVG(total_amount), 0) as avg_transaction_value
            ')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->whereNotNull('customer_email')
            ->first();

        return [
            'revenue_by_payment' => $revenueByPayment,
            'sales_by_hour' => $salesByHour,
            'customer_analysis' => [
                'unique_customers' => (int) $customerAnalysis->unique_customers,
                'total_transactions' => (int) $customerAnalysis->total_transactions,
                'total_revenue' => (float) $customerAnalysis->total_revenue,
                'avg_transaction_value' => (float) $customerAnalysis->avg_transaction_value
            ]
        ];
    }

    /**
     * Generate inventory reports
     */
    private function generateInventoryReports(): array
    {
        // Stock levels analysis
        $stockAnalysis = DB::table('medicines')
            ->selectRaw('
                COUNT(*) as total_medicines,
                SUM(CASE WHEN stock_quantity > 10 THEN 1 ELSE 0 END) as in_stock,
                SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= 10 THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(selling_price * stock_quantity) as total_inventory_value,
                SUM(CASE WHEN expiry_date <= DATE_ADD(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_soon
            ')
            ->where('is_active', 1)
            ->first();

        // Category performance
        $categoryPerformance = DB::table('medicines')
            ->join('categories', 'medicines.category_id', '=', 'categories.id')
            ->selectRaw('
                categories.name as category_name,
                categories.color as category_color,
                COUNT(medicines.id) as medicine_count,
                SUM(medicines.stock_quantity) as total_stock,
                SUM(medicines.selling_price * medicines.stock_quantity) as category_value
            ')
            ->where('medicines.is_active', 1)
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderBy('category_value', 'desc')
            ->get();

        // Expiring medicines
        $expiringMedicines = DB::table('medicines')
            ->join('categories', 'medicines.category_id', '=', 'categories.id')
            ->select('medicines.name', 'medicines.stock_quantity', 'medicines.expiry_date', 'categories.name as category_name')
            ->where('medicines.is_active', 1)
            ->where('medicines.expiry_date', '<=', now()->addDays(30))
            ->orderBy('medicines.expiry_date')
            ->limit(20)
            ->get();

        return [
            'stock_analysis' => [
                'total_medicines' => (int) $stockAnalysis->total_medicines,
                'in_stock' => (int) $stockAnalysis->in_stock,
                'low_stock' => (int) $stockAnalysis->low_stock,
                'out_of_stock' => (int) $stockAnalysis->out_of_stock,
                'total_inventory_value' => (float) $stockAnalysis->total_inventory_value,
                'expiring_soon' => (int) $stockAnalysis->expiring_soon
            ],
            'category_performance' => $categoryPerformance,
            'expiring_medicines' => $expiringMedicines
        ];
    }

    /**
     * Generate financial reports
     */
    private function generateFinancialReports(array $dateRange): array
    {
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Revenue analysis
        $revenueAnalysis = DB::table('sales')
            ->selectRaw('
                COALESCE(SUM(total_amount), 0) as total_revenue,
                COALESCE(SUM(subtotal), 0) as total_subtotal,
                COALESCE(SUM(tax_amount), 0) as total_tax,
                COALESCE(SUM(discount_amount), 0) as total_discounts,
                COUNT(*) as total_transactions,
                COALESCE(AVG(total_amount), 0) as avg_transaction_value
            ')
            ->whereBetween('sale_date', [$startDate, $endDate])
            ->where('status', 'completed')
            ->first();

        // Profit analysis (simplified - would need cost data from purchases)
        $profitAnalysis = DB::table('medicines')
            ->selectRaw('
                SUM(selling_price * stock_quantity) as potential_revenue,
                SUM(cost_price * stock_quantity) as total_cost,
                AVG(selling_price) as avg_selling_price,
                AVG(cost_price) as avg_cost_price
            ')
            ->where('is_active', 1)
            ->first();

        return [
            'revenue_analysis' => [
                'total_revenue' => (float) $revenueAnalysis->total_revenue,
                'total_subtotal' => (float) $revenueAnalysis->total_subtotal,
                'total_tax' => (float) $revenueAnalysis->total_tax,
                'total_discounts' => (float) $revenueAnalysis->total_discounts,
                'total_transactions' => (int) $revenueAnalysis->total_transactions,
                'avg_transaction_value' => (float) $revenueAnalysis->avg_transaction_value
            ],
            'profit_analysis' => [
                'potential_revenue' => (float) $profitAnalysis->potential_revenue,
                'total_cost' => (float) $profitAnalysis->total_cost,
                'gross_profit_potential' => (float) $profitAnalysis->potential_revenue - (float) $profitAnalysis->total_cost,
                'avg_selling_price' => (float) $profitAnalysis->avg_selling_price,
                'avg_cost_price' => (float) $profitAnalysis->avg_cost_price
            ]
        ];
    }

    /**
     * Generate customer reports
     */
    private function generateCustomerReports(): array
    {
        // Customer demographics
        $customerStats = DB::table('customers')
            ->selectRaw('
                COUNT(*) as total_customers,
                COUNT(CASE WHEN status = "active" THEN 1 END) as active_customers,
                COUNT(CASE WHEN segment = "vip" THEN 1 END) as vip_customers,
                COALESCE(SUM(total_spent), 0) as total_customer_spending,
                COALESCE(AVG(total_spent), 0) as avg_customer_value,
                COALESCE(AVG(loyalty_points), 0) as avg_loyalty_points
            ')
            ->first();

        // Top customers by spending
        $topCustomers = DB::table('customers')
            ->select('name', 'email', 'total_spent', 'loyalty_points', 'segment')
            ->orderBy('total_spent', 'desc')
            ->limit(10)
            ->get();

        // Customer segments
        $segmentAnalysis = DB::table('customers')
            ->selectRaw('
                segment,
                COUNT(*) as customer_count,
                COALESCE(SUM(total_spent), 0) as total_spending,
                COALESCE(AVG(total_spent), 0) as avg_spending
            ')
            ->groupBy('segment')
            ->get();

        return [
            'customer_stats' => [
                'total_customers' => (int) $customerStats->total_customers,
                'active_customers' => (int) $customerStats->active_customers,
                'vip_customers' => (int) $customerStats->vip_customers,
                'total_customer_spending' => (float) $customerStats->total_customer_spending,
                'avg_customer_value' => (float) $customerStats->avg_customer_value,
                'avg_loyalty_points' => (float) $customerStats->avg_loyalty_points
            ],
            'top_customers' => $topCustomers,
            'segment_analysis' => $segmentAnalysis
        ];
    }

    /**
     * Generate purchase reports
     */
    private function generatePurchaseReports(array $dateRange): array
    {
        $startDate = $dateRange['start'];
        $endDate = $dateRange['end'];

        // Purchase summary
        $purchaseSummary = DB::table('purchases')
            ->selectRaw('
                COUNT(*) as total_purchases,
                COUNT(CASE WHEN status = "received" THEN 1 END) as received_purchases,
                COUNT(CASE WHEN status = "pending" THEN 1 END) as pending_purchases,
                COALESCE(SUM(total_amount), 0) as total_purchase_value,
                COALESCE(AVG(total_amount), 0) as avg_purchase_value
            ')
            ->whereBetween('order_date', [$startDate, $endDate])
            ->first();

        // Supplier performance
        $supplierPerformance = DB::table('purchases')
            ->join('suppliers', 'purchases.supplier_id', '=', 'suppliers.id')
            ->selectRaw('
                suppliers.name as supplier_name,
                COUNT(purchases.id) as purchase_count,
                COALESCE(SUM(purchases.total_amount), 0) as total_spent,
                COALESCE(AVG(purchases.total_amount), 0) as avg_purchase_value
            ')
            ->whereBetween('purchases.order_date', [$startDate, $endDate])
            ->groupBy('suppliers.id', 'suppliers.name')
            ->orderBy('total_spent', 'desc')
            ->get();

        return [
            'purchase_summary' => [
                'total_purchases' => (int) $purchaseSummary->total_purchases,
                'received_purchases' => (int) $purchaseSummary->received_purchases,
                'pending_purchases' => (int) $purchaseSummary->pending_purchases,
                'total_purchase_value' => (float) $purchaseSummary->total_purchase_value,
                'avg_purchase_value' => (float) $purchaseSummary->avg_purchase_value
            ],
            'supplier_performance' => $supplierPerformance
        ];
    }

    /**
     * Get date range from request or default to last 30 days
     */
    private function getDateRange(Request $request): array
    {
        $startDate = $request->get('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->format('Y-m-d'));

        return [
            'start' => Carbon::parse($startDate)->startOfDay(),
            'end' => Carbon::parse($endDate)->endOfDay(),
            'start_formatted' => $startDate,
            'end_formatted' => $endDate
        ];
    }
}
