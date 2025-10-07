<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\UserActivity;
use App\Models\SystemSetting;
use App\Models\Backup;
use App\Services\BackupService;
use App\Services\SecurityService;
use App\Services\EmailService;
use App\Services\PrinterService;

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
            $backupService = new BackupService();
            $emailService = new EmailService();
            $securityService = new SecurityService();
            $printerService = new PrinterService();
            
            return [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'locked_users' => User::whereNotNull('locked_until')->count(),
                'recent_activities' => UserActivity::count(),
                'system_health' => $this->getSystemHealth(),
                'backup_stats' => $backupService->getBackupStats(),
                'email_stats' => $emailService->getEmailQueueStats(),
                'security_settings' => $securityService->getSecuritySettings(),
                'printer_stats' => $printerService->getPrinterStats(),
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

    /**
     * Get security settings
     */
    public function getSecuritySettings()
    {
        $this->authorize('manage-settings');
        
        $securityService = new SecurityService();
        $settings = $securityService->getSecuritySettings();
        
        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Update security settings
     */
    public function updateSecuritySettings(Request $request)
    {
        $this->authorize('manage-settings');
        
        $validator = $request->validate([
            'password_policy.min_length' => 'required|integer|min:6|max:50',
            'password_policy.require_uppercase' => 'boolean',
            'password_policy.require_lowercase' => 'boolean',
            'password_policy.require_numbers' => 'boolean',
            'password_policy.require_symbols' => 'boolean',
            'password_policy.max_age_days' => 'required|integer|min:30|max:365',
            'account_locking.enabled' => 'boolean',
            'account_locking.max_attempts' => 'required|integer|min:3|max:10',
            'account_locking.lockout_duration' => 'required|integer|min:5|max:60',
            'session_management.enabled' => 'boolean',
            'session_management.timeout' => 'required|integer|min:15|max:480',
            'session_management.max_concurrent' => 'required|integer|min:1|max:10',
        ]);

        $securityService = new SecurityService();
        $result = $securityService->updateSecuritySettings($request->all());
        
        return response()->json($result);
    }

    /**
     * Get email settings
     */
    public function getEmailSettings()
    {
        $this->authorize('manage-settings');
        
        $emailService = new EmailService();
        $settings = $emailService->getEmailSettings();
        $queueStats = $emailService->getEmailQueueStats();
        
        return response()->json([
            'success' => true,
            'settings' => $settings,
            'queue_stats' => $queueStats
        ]);
    }

    /**
     * Update email settings
     */
    public function updateEmailSettings(Request $request)
    {
        $this->authorize('manage-settings');
        
        $validator = $request->validate([
            'smtp.host' => 'required|string|max:255',
            'smtp.port' => 'required|integer|min:1|max:65535',
            'smtp.username' => 'required|string|max:255',
            'smtp.password' => 'required|string|max:255',
            'smtp.encryption' => 'required|in:tls,ssl,none',
            'smtp.from_address' => 'required|email|max:255',
            'smtp.from_name' => 'required|string|max:255',
            'notifications.enabled' => 'boolean',
            'notifications.low_stock' => 'boolean',
            'notifications.system_alerts' => 'boolean',
            'notifications.user_activities' => 'boolean',
            'queue.enabled' => 'boolean',
            'queue.connection' => 'required|in:database,sync',
        ]);

        $emailService = new EmailService();
        $result = $emailService->updateEmailSettings($request->all());
        
        return response()->json($result);
    }

    /**
     * Test email configuration
     */
    public function testEmailConfiguration()
    {
        $this->authorize('manage-settings');
        
        $emailService = new EmailService();
        $result = $emailService->testEmailConfiguration();
        
        return response()->json($result);
    }

    /**
     * Get backup statistics
     */
    public function getBackupStats()
    {
        $this->authorize('manage-settings');
        
        $backupService = new BackupService();
        $stats = $backupService->getBackupStats();
        
        return response()->json([
            'success' => true,
            'stats' => $stats
        ]);
    }

    /**
     * Create database backup
     */
    public function createBackup(Request $request)
    {
        $this->authorize('manage-settings');
        
        $request->validate([
            'name' => 'nullable|string|max:255'
        ]);

        $backupService = new BackupService();
        $result = $backupService->createDatabaseBackup($request->name);
        
        return response()->json($result);
    }

    /**
     * Download backup
     */
    public function downloadBackup($id)
    {
        $this->authorize('manage-settings');
        
        $backupService = new BackupService();
        return $backupService->downloadBackup($id);
    }

    /**
     * Delete backup
     */
    public function deleteBackup($id)
    {
        $this->authorize('manage-settings');
        
        $backupService = new BackupService();
        $result = $backupService->deleteBackup($id);
        
        return response()->json($result);
    }

    /**
     * Get all backups
     */
    public function getBackups()
    {
        $this->authorize('manage-settings');
        
        $backups = Backup::with('creator')->orderBy('created_at', 'desc')->paginate(10);
        
        return response()->json([
            'success' => true,
            'backups' => $backups
        ]);
    }

    /**
     * Get printer settings
     */
    public function getPrinterSettings()
    {
        $this->authorize('manage-settings');
        
        $printerService = new PrinterService();
        $settings = $printerService->getPrinterSettings();
        
        return response()->json([
            'success' => true,
            'settings' => $settings
        ]);
    }

    /**
     * Update printer settings
     */
    public function updatePrinterSettings(Request $request)
    {
        $this->authorize('manage-settings');
        
        $validator = $request->validate([
            'default_printer' => 'nullable|string|max:255',
            'print_quality' => 'required|in:draft,normal,high',
            'paper_size' => 'required|in:A4,A3,Letter,Legal,A5',
            'orientation' => 'required|in:portrait,landscape',
            'color_mode' => 'required|in:color,grayscale,black',
            'duplex' => 'boolean',
            'auto_cut' => 'boolean',
            'print_margin' => 'required|in:minimal,normal,wide',
            'header_footer' => 'boolean',
            'watermark' => 'boolean',
        ]);

        $printerService = new PrinterService();
        $result = $printerService->updatePrinterSettings($request->all());
        
        return response()->json($result);
    }

    /**
     * Get available printers
     */
    public function getAvailablePrinters()
    {
        $this->authorize('manage-settings');
        
        $printerService = new PrinterService();
        $printers = $printerService->getAvailablePrinters();
        
        return response()->json([
            'success' => true,
            'printers' => $printers
        ]);
    }

    /**
     * Test printer
     */
    public function testPrinter(Request $request)
    {
        $this->authorize('manage-settings');
        
        $request->validate([
            'printer' => 'required|string|max:255'
        ]);

        $printerService = new PrinterService();
        $result = $printerService->testPrinter($request->printer);
        
        return response()->json($result);
    }
}
