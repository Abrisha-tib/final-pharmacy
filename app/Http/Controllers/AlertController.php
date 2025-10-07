<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\Alert;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Supplier;
use App\Services\AlertService;
use Carbon\Carbon;

/**
 * Alert Controller
 * 
 * Comprehensive alert management system for pharmacy operations.
 * Handles alert creation, management, and real-time monitoring.
 * Optimized for cPanel/shared hosting with caching and performance.
 * 
 * @author Analog Software Solutions
 * @version 1.0
 */
class AlertController extends Controller
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
     * Display the alerts dashboard.
     * 
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        try {
            // Get alerts with filtering and pagination
            $alerts = $this->getAlerts($request);
            
            // Get alert statistics
            $statistics = $this->getAlertStatistics();
            
            // Get filter options
            $filterOptions = $this->getFilterOptions();
            
            return view('alerts', compact('alerts', 'statistics', 'filterOptions'));
            
        } catch (\Exception $e) {
            \Log::error('Alerts page error: ' . $e->getMessage());
            
            return view('alerts', [
                'alerts' => collect(),
                'statistics' => $this->getEmptyStatistics(),
                'filterOptions' => $this->getFilterOptions()
            ]);
        }
    }

    /**
     * Get alerts with filtering and pagination.
     * 
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    private function getAlerts(Request $request)
    {
        $query = Alert::with(['user', 'acknowledgedBy'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        // Cache the results for 2 minutes for performance
        $cacheKey = 'alerts_' . md5(serialize($request->all()));
        
        return Cache::remember($cacheKey, 120, function() use ($query) {
            return $query->paginate(20);
        });
    }

    /**
     * Get alert statistics with caching.
     * 
     * @return array
     */
    private function getAlertStatistics()
    {
        $cacheKey = 'alert_statistics';
        
        return Cache::remember($cacheKey, 300, function() {
            return [
                'total' => Alert::count(),
                'active' => Alert::active()->count(),
                'critical' => Alert::critical()->active()->count(),
                'acknowledged' => Alert::acknowledged()->count(),
                'resolved' => Alert::resolved()->count(),
                'by_category' => Alert::selectRaw('category, count(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category'),
                'by_priority' => Alert::selectRaw('priority, count(*) as count')
                    ->groupBy('priority')
                    ->pluck('count', 'priority'),
                'recent_alerts' => Alert::with(['user'])
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ];
        });
    }

    /**
     * Get filter options for the alerts page.
     * 
     * @return array
     */
    private function getFilterOptions()
    {
        return [
            'statuses' => ['active', 'acknowledged', 'resolved', 'dismissed'],
            'categories' => ['inventory', 'expiry', 'system', 'sales', 'customer', 'purchase', 'supplier'],
            'priorities' => ['low', 'medium', 'high', 'critical'],
            'types' => ['info', 'warning', 'error', 'success', 'critical']
        ];
    }

    /**
     * Get empty statistics for error handling.
     * 
     * @return array
     */
    private function getEmptyStatistics()
    {
        return [
            'total' => 0,
            'active' => 0,
            'critical' => 0,
            'acknowledged' => 0,
            'resolved' => 0,
            'by_category' => collect(),
            'by_priority' => collect(),
            'recent_alerts' => collect()
        ];
    }

    /**
     * Show a specific alert.
     * 
     * @param Alert $alert
     * @return \Illuminate\View\View
     */
    public function show(Alert $alert)
    {
        $alert->load(['user', 'acknowledgedBy']);
        
        return view('alerts.show', compact('alert'));
    }

    /**
     * Create a new alert.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'category' => 'required|in:inventory,expiry,system,sales,customer,purchase,supplier',
            'priority' => 'required|in:low,medium,high,critical',
            'type' => 'required|in:info,warning,error,success,critical',
            'expires_at' => 'nullable|date|after:now'
        ]);

        try {
            $alert = Alert::create([
                'title' => $request->title,
                'message' => $request->message,
                'category' => $request->category,
                'priority' => $request->priority,
                'type' => $request->type,
                'status' => 'active',
                'user_id' => Auth::id(),
                'is_auto_generated' => false,
                'expires_at' => $request->expires_at
            ]);

            // Clear cache
            $this->clearAlertsCache();

            return response()->json([
                'success' => true,
                'message' => 'Alert created successfully',
                'alert' => $alert->load(['user'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Alert creation error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create alert'
            ], 500);
        }
    }

    /**
     * Update an alert.
     * 
     * @param Request $request
     * @param Alert $alert
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Alert $alert)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'message' => 'sometimes|string',
            'category' => 'sometimes|in:inventory,expiry,system,sales,customer,purchase,supplier',
            'priority' => 'sometimes|in:low,medium,high,critical',
            'type' => 'sometimes|in:info,warning,error,success,critical',
            'status' => 'sometimes|in:active,acknowledged,resolved,dismissed'
        ]);

        try {
            $alert->update($request->only([
                'title', 'message', 'category', 'priority', 'type', 'status'
            ]));

            // Clear cache
            $this->clearAlertsCache();

            return response()->json([
                'success' => true,
                'message' => 'Alert updated successfully',
                'alert' => $alert->fresh(['user', 'acknowledgedBy'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Alert update error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update alert'
            ], 500);
        }
    }

    /**
     * Acknowledge an alert.
     * 
     * @param Alert $alert
     * @return \Illuminate\Http\JsonResponse
     */
    public function acknowledge(Alert $alert)
    {
        try {
            $alert->acknowledge();

            // Clear cache
            $this->clearAlertsCache();

            return response()->json([
                'success' => true,
                'message' => 'Alert acknowledged successfully',
                'alert' => $alert->fresh(['user', 'acknowledgedBy'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Alert acknowledgment error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to acknowledge alert'
            ], 500);
        }
    }

    /**
     * Resolve an alert.
     * 
     * @param Alert $alert
     * @return \Illuminate\Http\JsonResponse
     */
    public function resolve(Alert $alert)
    {
        try {
            $alert->resolve();

            // Clear cache
            $this->clearAlertsCache();

            return response()->json([
                'success' => true,
                'message' => 'Alert resolved successfully',
                'alert' => $alert->fresh(['user', 'acknowledgedBy'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Alert resolution error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve alert'
            ], 500);
        }
    }

    /**
     * Dismiss an alert.
     * 
     * @param Alert $alert
     * @return \Illuminate\Http\JsonResponse
     */
    public function dismiss(Alert $alert)
    {
        try {
            $alert->dismiss();

            // Clear cache
            $this->clearAlertsCache();

            return response()->json([
                'success' => true,
                'message' => 'Alert dismissed successfully',
                'alert' => $alert->fresh(['user', 'acknowledgedBy'])
            ]);

        } catch (\Exception $e) {
            \Log::error('Alert dismissal error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to dismiss alert'
            ], 500);
        }
    }

    /**
     * Delete an alert.
     * 
     * @param Alert $alert
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Alert $alert)
    {
        try {
            $alert->delete();

            // Clear cache
            $this->clearAlertsCache();

            return response()->json([
                'success' => true,
                'message' => 'Alert deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Alert deletion error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete alert'
            ], 500);
        }
    }

    /**
     * Get alerts for API endpoints.
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAlertsApi(Request $request)
    {
        try {
            $alerts = $this->getAlerts($request);
            $statistics = $this->getAlertStatistics();

            return response()->json([
                'success' => true,
                'alerts' => $alerts,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            \Log::error('Get alerts API error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch alerts'
            ], 500);
        }
    }

    /**
     * Get alert statistics for API.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getStatistics()
    {
        try {
            $statistics = $this->getAlertStatistics();

            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            \Log::error('Get alert statistics error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch alert statistics'
            ], 500);
        }
    }

    /**
     * Clear alerts cache.
     * 
     * @return void
     */
    private function clearAlertsCache()
    {
        Cache::forget('alert_statistics');
        // Clear pagination cache by pattern
        Cache::flush();
    }

    /**
     * Generate alerts using AlertService.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function generateAlerts()
    {
        try {
            $alertService = new AlertService();
            $generatedAlerts = $alertService->generateAllAlerts();

            // Clear cache after generating alerts
            $this->clearAlertsCache();

            return response()->json([
                'success' => true,
                'message' => 'Alerts generated successfully',
                'count' => count($generatedAlerts)
            ]);

        } catch (\Exception $e) {
            \Log::error('Generate alerts error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate alerts'
            ], 500);
        }
    }

    /**
     * Clear all alerts cache.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function clearCache()
    {
        try {
            $this->clearAlertsCache();

            return response()->json([
                'success' => true,
                'message' => 'Alerts cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Clear alerts cache error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear cache'
            ], 500);
        }
    }
}
