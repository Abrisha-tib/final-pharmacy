<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Medicine;
use App\Models\Category;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    /**
     * Get comprehensive business intelligence analytics
     * Optimized for shared hosting with database-level calculations
     */
    public function getBusinessIntelligence(): JsonResponse
    {
        try {
            // Use comprehensive caching strategy for shared hosting
            $cacheKey = 'business_intelligence_analytics';
            $analytics = Cache::remember($cacheKey, 300, function () {
                return $this->calculateBusinessIntelligence();
            });

            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);
        } catch (\Exception $e) {
            \Log::error('Business Intelligence Analytics Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load analytics data'
            ], 500);
        }
    }

    /**
     * Calculate comprehensive business intelligence metrics
     * All calculations done at database level for shared hosting optimization
     */
    private function calculateBusinessIntelligence(): array
    {
        // Core inventory metrics using single optimized query
        $inventoryMetrics = $this->getInventoryMetrics();
        
        // Revenue and profit analysis
        $financialMetrics = $this->getFinancialMetrics();
        
        // Category performance analysis
        $categoryPerformance = $this->getCategoryPerformance();
        
        // Predictive analytics
        $predictiveInsights = $this->getPredictiveInsights();
        
        // Business recommendations
        $recommendations = $this->getBusinessRecommendations($inventoryMetrics, $financialMetrics, $categoryPerformance);
        
        // Performance indicators
        $performanceIndicators = $this->getPerformanceIndicators();

        return [
            'overview' => $inventoryMetrics,
            'financial' => $financialMetrics,
            'category_performance' => $categoryPerformance,
            'predictive' => $predictiveInsights,
            'recommendations' => $recommendations,
            'performance' => $performanceIndicators,
            'last_updated' => now()->toISOString()
        ];
    }

    /**
     * Get core inventory metrics using optimized database queries
     */
    private function getInventoryMetrics(): array
    {
        $metrics = DB::table('medicines')
            ->selectRaw('
                COUNT(*) as total_medicines,
                SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_medicines,
                SUM(CASE WHEN stock_quantity > 10 THEN 1 ELSE 0 END) as in_stock,
                SUM(CASE WHEN stock_quantity > 0 AND stock_quantity <= 10 THEN 1 ELSE 0 END) as low_stock,
                SUM(CASE WHEN stock_quantity <= 0 THEN 1 ELSE 0 END) as out_of_stock,
                SUM(selling_price * stock_quantity) as total_inventory_value,
                AVG(selling_price) as avg_selling_price,
                AVG(cost_price) as avg_cost_price,
                SUM(CASE WHEN expiry_date <= DATE_ADD(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_soon
            ')
            ->first();

        return [
            'total_medicines' => (int) $metrics->total_medicines,
            'active_medicines' => (int) $metrics->active_medicines,
            'in_stock' => (int) $metrics->in_stock,
            'low_stock' => (int) $metrics->low_stock,
            'out_of_stock' => (int) $metrics->out_of_stock,
            'total_inventory_value' => (float) $metrics->total_inventory_value,
            'avg_selling_price' => (float) $metrics->avg_selling_price,
            'avg_cost_price' => (float) $metrics->avg_cost_price,
            'expiring_soon' => (int) $metrics->expiring_soon,
            'stock_health_percentage' => $metrics->total_medicines > 0 ? 
                round(($metrics->in_stock / $metrics->total_medicines) * 100, 1) : 0
        ];
    }

    /**
     * Get financial metrics and profit analysis
     */
    private function getFinancialMetrics(): array
    {
        $financial = DB::table('medicines')
            ->selectRaw('
                SUM(selling_price * stock_quantity) as total_revenue_potential,
                SUM(cost_price * stock_quantity) as total_cost_value,
                AVG(selling_price) as avg_selling_price,
                AVG(cost_price) as avg_cost_price,
                SUM(CASE WHEN selling_price > 0 AND cost_price > 0 
                    THEN ((selling_price - cost_price) / selling_price) * 100 
                    ELSE 0 END) / COUNT(*) as avg_profit_margin
            ')
            ->where('is_active', 1)
            ->first();

        $totalRevenuePotential = (float) $financial->total_revenue_potential;
        $totalCostValue = (float) $financial->total_cost_value;
        $avgProfitMargin = (float) $financial->avg_profit_margin;

        return [
            'total_revenue_potential' => $totalRevenuePotential,
            'total_cost_value' => $totalCostValue,
            'gross_profit_potential' => $totalRevenuePotential - $totalCostValue,
            'avg_selling_price' => (float) $financial->avg_selling_price,
            'avg_cost_price' => (float) $financial->avg_cost_price,
            'avg_profit_margin' => round($avgProfitMargin, 1),
            'profit_health_score' => $this->calculateProfitHealthScore($avgProfitMargin),
            'revenue_growth_potential' => $this->calculateRevenueGrowthPotential($totalRevenuePotential)
        ];
    }

    /**
     * Get category performance analysis
     */
    private function getCategoryPerformance(): array
    {
        $categoryStats = DB::table('medicines')
            ->join('categories', 'medicines.category_id', '=', 'categories.id')
            ->selectRaw('
                categories.name as category_name,
                categories.color as category_color,
                COUNT(medicines.id) as medicine_count,
                SUM(medicines.stock_quantity) as total_stock,
                SUM(medicines.selling_price * medicines.stock_quantity) as category_value,
                AVG(medicines.selling_price) as avg_price,
                AVG(CASE WHEN medicines.selling_price > 0 AND medicines.cost_price > 0 
                    THEN ((medicines.selling_price - medicines.cost_price) / medicines.selling_price) * 100 
                    ELSE 0 END) as profit_margin
            ')
            ->where('medicines.is_active', 1)
            ->groupBy('categories.id', 'categories.name', 'categories.color')
            ->orderBy('category_value', 'desc')
            ->get();

        $totalValue = $categoryStats->sum('category_value');

        return [
            'categories' => $categoryStats->map(function ($category) use ($totalValue) {
                return [
                    'name' => $category->category_name,
                    'color' => $category->category_color,
                    'medicine_count' => (int) $category->medicine_count,
                    'total_stock' => (int) $category->total_stock,
                    'category_value' => (float) $category->category_value,
                    'avg_price' => (float) $category->avg_price,
                    'profit_margin' => round((float) $category->profit_margin, 1),
                    'value_percentage' => $totalValue > 0 ? 
                        round(($category->category_value / $totalValue) * 100, 1) : 0
                ];
            }),
            'top_performing_category' => $categoryStats->first(),
            'total_categories' => $categoryStats->count()
        ];
    }

    /**
     * Get predictive insights and forecasting
     */
    private function getPredictiveInsights(): array
    {
        // Expiry predictions
        $expiryPredictions = DB::table('medicines')
            ->selectRaw('
                SUM(CASE WHEN expiry_date <= DATE_ADD(NOW(), INTERVAL 7 DAY) THEN 1 ELSE 0 END) as expiring_7_days,
                SUM(CASE WHEN expiry_date <= DATE_ADD(NOW(), INTERVAL 30 DAY) THEN 1 ELSE 0 END) as expiring_30_days,
                SUM(CASE WHEN expiry_date < NOW() THEN 1 ELSE 0 END) as expired,
                SUM(CASE WHEN expiry_date <= DATE_ADD(NOW(), INTERVAL 7 DAY) 
                    THEN selling_price * stock_quantity ELSE 0 END) as expiring_value_7_days
            ')
            ->where('is_active', 1)
            ->first();

        // Reorder recommendations
        $reorderRecommendations = DB::table('medicines')
            ->where('is_active', 1)
            ->where('stock_quantity', '<=', 10)
            ->where('stock_quantity', '>', 0)
            ->select('name', 'stock_quantity', 'reorder_level', 'selling_price')
            ->orderBy('stock_quantity', 'asc')
            ->limit(10)
            ->get();

        // Demand forecasting (simplified based on stock turnover)
        $demandForecast = $this->calculateDemandForecast();

        return [
            'expiry_predictions' => [
                'expiring_7_days' => (int) $expiryPredictions->expiring_7_days,
                'expiring_30_days' => (int) $expiryPredictions->expiring_30_days,
                'expired' => (int) $expiryPredictions->expired,
                'expiring_value_7_days' => (float) $expiryPredictions->expiring_value_7_days,
                'risk_level' => $this->calculateExpiryRiskLevel($expiryPredictions->expiring_7_days)
            ],
            'reorder_recommendations' => $reorderRecommendations->map(function ($item) {
                return [
                    'name' => $item->name,
                    'current_stock' => (int) $item->stock_quantity,
                    'reorder_level' => (int) $item->reorder_level,
                    'selling_price' => (float) $item->selling_price,
                    'urgency' => $item->stock_quantity <= 5 ? 'high' : 'medium'
                ];
            }),
            'demand_forecast' => $demandForecast
        ];
    }

    /**
     * Get business recommendations based on analytics
     */
    private function getBusinessRecommendations(array $inventory, array $financial, array $categoryPerformance): array
    {
        $recommendations = [];

        // Stock optimization recommendations
        if ($inventory['low_stock'] > 0) {
            $recommendations[] = [
                'type' => 'stock_optimization',
                'priority' => 'high',
                'title' => 'Stock Replenishment Needed',
                'description' => "{$inventory['low_stock']} items are running low on stock. Consider reordering to prevent stockouts.",
                'action' => 'Review low stock items and place reorders',
                'impact' => 'Prevent revenue loss from stockouts'
            ];
        }

        // Profit optimization recommendations
        if ($financial['avg_profit_margin'] < 20) {
            $recommendations[] = [
                'type' => 'profit_optimization',
                'priority' => 'medium',
                'title' => 'Profit Margin Improvement',
                'description' => "Average profit margin is {$financial['avg_profit_margin']}%. Consider reviewing pricing strategy.",
                'action' => 'Analyze pricing and cost structure',
                'impact' => 'Increase profitability by 5-10%'
            ];
        }

        // Category performance recommendations
        $topCategory = $categoryPerformance['top_performing_category'];
        if ($topCategory) {
            $recommendations[] = [
                'type' => 'category_optimization',
                'priority' => 'low',
                'title' => 'Category Performance Insight',
                'description' => "{$topCategory->category_name} is your top-performing category with {$topCategory->profit_margin}% profit margin.",
                'action' => 'Expand successful category or apply strategies to other categories',
                'impact' => 'Replicate success across inventory'
            ];
        }

        // Expiry risk recommendations
        if ($inventory['expiring_soon'] > 0) {
            $recommendations[] = [
                'type' => 'expiry_management',
                'priority' => 'high',
                'title' => 'Expiry Risk Management',
                'description' => "{$inventory['expiring_soon']} items are expiring within 30 days. Consider promotional pricing.",
                'action' => 'Implement expiry management strategy',
                'impact' => 'Minimize waste and maximize revenue'
            ];
        }

        return $recommendations;
    }

    /**
     * Get performance indicators and system health
     */
    private function getPerformanceIndicators(): array
    {
        return [
            'inventory_turnover_rate' => $this->calculateInventoryTurnoverRate(),
            'stock_health_score' => $this->calculateStockHealthScore(),
            'profitability_score' => $this->calculateProfitabilityScore(),
            'efficiency_score' => $this->calculateEfficiencyScore(),
            'growth_potential' => $this->calculateGrowthPotential()
        ];
    }

    /**
     * Calculate profit health score
     */
    private function calculateProfitHealthScore(float $profitMargin): string
    {
        if ($profitMargin >= 30) return 'excellent';
        if ($profitMargin >= 20) return 'good';
        if ($profitMargin >= 10) return 'fair';
        return 'needs_improvement';
    }

    /**
     * Calculate revenue growth potential
     */
    private function calculateRevenueGrowthPotential(float $currentRevenue): string
    {
        if ($currentRevenue >= 100000) return 'high';
        if ($currentRevenue >= 50000) return 'medium';
        return 'low';
    }

    /**
     * Calculate expiry risk level
     */
    private function calculateExpiryRiskLevel(int $expiring7Days): string
    {
        if ($expiring7Days >= 10) return 'critical';
        if ($expiring7Days >= 5) return 'high';
        if ($expiring7Days >= 1) return 'medium';
        return 'low';
    }

    /**
     * Calculate demand forecast (simplified)
     */
    private function calculateDemandForecast(): array
    {
        // Simplified demand forecasting based on stock levels and categories
        $fastMoving = DB::table('medicines')
            ->where('is_active', 1)
            ->where('stock_quantity', '<=', 20)
            ->where('stock_quantity', '>', 0)
            ->count();

        $slowMoving = DB::table('medicines')
            ->where('is_active', 1)
            ->where('stock_quantity', '>', 50)
            ->count();

        return [
            'fast_moving_items' => $fastMoving,
            'slow_moving_items' => $slowMoving,
            'demand_trend' => $fastMoving > $slowMoving ? 'increasing' : 'stable',
            'forecast_confidence' => 'medium'
        ];
    }

    /**
     * Calculate inventory turnover rate
     */
    private function calculateInventoryTurnoverRate(): float
    {
        // Simplified calculation - in real scenario, would use historical sales data
        $totalValue = DB::table('medicines')
            ->where('is_active', 1)
            ->sum(DB::raw('selling_price * stock_quantity'));

        return $totalValue > 0 ? round($totalValue / 12, 2) : 0; // Monthly turnover estimate
    }

    /**
     * Calculate stock health score
     */
    private function calculateStockHealthScore(): int
    {
        $total = DB::table('medicines')->where('is_active', 1)->count();
        $inStock = DB::table('medicines')
            ->where('is_active', 1)
            ->where('stock_quantity', '>', 10)
            ->count();

        return $total > 0 ? round(($inStock / $total) * 100) : 0;
    }

    /**
     * Calculate profitability score
     */
    private function calculateProfitabilityScore(): int
    {
        $avgMargin = DB::table('medicines')
            ->where('is_active', 1)
            ->where('selling_price', '>', 0)
            ->where('cost_price', '>', 0)
            ->avg(DB::raw('((selling_price - cost_price) / selling_price) * 100'));

        return round($avgMargin ?: 0);
    }

    /**
     * Calculate efficiency score
     */
    private function calculateEfficiencyScore(): int
    {
        $stockHealth = $this->calculateStockHealthScore();
        $profitability = $this->calculateProfitabilityScore();
        
        return round(($stockHealth + $profitability) / 2);
    }

    /**
     * Calculate growth potential
     */
    private function calculateGrowthPotential(): string
    {
        $totalValue = DB::table('medicines')
            ->where('is_active', 1)
            ->sum(DB::raw('selling_price * stock_quantity'));

        if ($totalValue >= 100000) return 'high';
        if ($totalValue >= 50000) return 'medium';
        return 'low';
    }

    /**
     * Clear analytics cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            Cache::forget('business_intelligence_analytics');
            Cache::forget('inventory_stats_global');
            
            return response()->json([
                'success' => true,
                'message' => 'Analytics cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }
}
