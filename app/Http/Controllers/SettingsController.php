<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserActivity;

class SettingsController extends Controller
{
    /**
     * Display the settings dashboard.
     */
    public function index()
    {
        $this->authorize('manage-settings');

        // Get system statistics
        $stats = Cache::remember('settings_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'locked_users' => User::whereNotNull('locked_until')->count(),
                'recent_activities' => UserActivity::count(),
                'system_health' => $this->getSystemHealth(),
            ];
        });

        return view('settings.index', compact('stats'));
    }

    /**
     * Get system health status.
     */
    private function getSystemHealth()
    {
        try {
            // Check database connection
            DB::connection()->getPdo();
            
            // Check cache
            Cache::put('health_check', 'ok', 60);
            $cacheStatus = Cache::get('health_check') === 'ok';
            
            // Check storage
            $storageStatus = is_writable(storage_path());
            
            return [
                'database' => 'connected',
                'cache' => $cacheStatus ? 'working' : 'error',
                'storage' => $storageStatus ? 'writable' : 'readonly',
                'overall' => ($cacheStatus && $storageStatus) ? 'healthy' : 'warning'
            ];
        } catch (\Exception $e) {
            return [
                'database' => 'error',
                'cache' => 'error',
                'storage' => 'error',
                'overall' => 'critical'
            ];
        }
    }

    /**
     * Clear system cache.
     */
    public function clearCache()
    {
        $this->authorize('manage-settings');

        try {
            Cache::flush();
            
            // Clear specific caches
            Cache::forget('users_*');
            Cache::forget('user_stats');
            Cache::forget('settings_stats');
            
            return redirect()->back()
                ->with('success', 'System cache cleared successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }

    /**
     * Get system information.
     */
    public function getSystemInfo()
    {
        $this->authorize('manage-settings');

        $info = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'memory_limit' => ini_get('memory_limit'),
            'max_execution_time' => ini_get('max_execution_time'),
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'database_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'session_driver' => config('session.driver'),
        ];

        return response()->json($info);
    }
}
