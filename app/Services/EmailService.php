<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class EmailService
{
    /**
     * Get email settings
     */
    public function getEmailSettings()
    {
        return [
            'smtp' => [
                'host' => SystemSetting::get('smtp_host', ''),
                'port' => SystemSetting::get('smtp_port', 587),
                'username' => SystemSetting::get('smtp_username', ''),
                'password' => SystemSetting::get('smtp_password', ''),
                'encryption' => SystemSetting::get('smtp_encryption', 'tls'),
                'from_address' => SystemSetting::get('smtp_from_address', ''),
                'from_name' => SystemSetting::get('smtp_from_name', ''),
            ],
            'notifications' => [
                'enabled' => SystemSetting::get('email_notifications_enabled', false),
                'low_stock' => SystemSetting::get('email_low_stock', true),
                'system_alerts' => SystemSetting::get('email_system_alerts', true),
                'user_activities' => SystemSetting::get('email_user_activities', false),
            ],
            'queue' => [
                'enabled' => SystemSetting::get('email_queue_enabled', false),
                'connection' => SystemSetting::get('email_queue_connection', 'database'),
            ]
        ];
    }

    /**
     * Update email settings
     */
    public function updateEmailSettings($data)
    {
        try {
            // SMTP Settings
            if (isset($data['smtp'])) {
                $smtp = $data['smtp'];
                SystemSetting::set('smtp_host', $smtp['host'] ?? '', 'string', 'SMTP Host', 'email');
                SystemSetting::set('smtp_port', $smtp['port'] ?? 587, 'integer', 'SMTP Port', 'email');
                SystemSetting::set('smtp_username', $smtp['username'] ?? '', 'string', 'SMTP Username', 'email');
                SystemSetting::set('smtp_password', $smtp['password'] ?? '', 'string', 'SMTP Password', 'email');
                SystemSetting::set('smtp_encryption', $smtp['encryption'] ?? 'tls', 'string', 'SMTP Encryption', 'email');
                SystemSetting::set('smtp_from_address', $smtp['from_address'] ?? '', 'string', 'From Email Address', 'email');
                SystemSetting::set('smtp_from_name', $smtp['from_name'] ?? '', 'string', 'From Name', 'email');
            }

            // Notification Settings
            if (isset($data['notifications'])) {
                $notifications = $data['notifications'];
                SystemSetting::set('email_notifications_enabled', $notifications['enabled'] ?? false, 'boolean', 'Enable email notifications', 'email');
                SystemSetting::set('email_low_stock', $notifications['low_stock'] ?? true, 'boolean', 'Low stock notifications', 'email');
                SystemSetting::set('email_system_alerts', $notifications['system_alerts'] ?? true, 'boolean', 'System alert notifications', 'email');
                SystemSetting::set('email_user_activities', $notifications['user_activities'] ?? false, 'boolean', 'User activity notifications', 'email');
            }

            // Queue Settings
            if (isset($data['queue'])) {
                $queue = $data['queue'];
                SystemSetting::set('email_queue_enabled', $queue['enabled'] ?? false, 'boolean', 'Enable email queue', 'email');
                SystemSetting::set('email_queue_connection', $queue['connection'] ?? 'database', 'string', 'Email queue connection', 'email');
            }

            SystemSetting::clearCache();

            return [
                'success' => true,
                'message' => 'Email settings updated successfully'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update email settings: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test email configuration
     */
    public function testEmailConfiguration()
    {
        try {
            $settings = $this->getEmailSettings();
            
            // Update mail configuration
            Config::set('mail.mailers.smtp.host', $settings['smtp']['host']);
            Config::set('mail.mailers.smtp.port', $settings['smtp']['port']);
            Config::set('mail.mailers.smtp.username', $settings['smtp']['username']);
            Config::set('mail.mailers.smtp.password', $settings['smtp']['password']);
            Config::set('mail.mailers.smtp.encryption', $settings['smtp']['encryption']);
            Config::set('mail.from.address', $settings['smtp']['from_address']);
            Config::set('mail.from.name', $settings['smtp']['from_name']);

            // Send test email
            Mail::raw('This is a test email from the pharmacy management system.', function ($message) use ($settings) {
                $message->to(auth()->user()->email)
                        ->subject('Test Email - Pharmacy Management System');
            });

            return [
                'success' => true,
                'message' => 'Test email sent successfully'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get email queue statistics
     */
    public function getEmailQueueStats()
    {
        try {
            $queueConnection = SystemSetting::get('email_queue_connection', 'database');
            
            if ($queueConnection === 'database') {
                $pendingJobs = DB::table('jobs')->count();
                $failedJobs = DB::table('failed_jobs')->count();
            } else {
                $pendingJobs = 0;
                $failedJobs = 0;
            }

            return [
                'pending' => $pendingJobs,
                'failed' => $failedJobs,
                'connection' => $queueConnection,
            ];

        } catch (\Exception $e) {
            return [
                'pending' => 0,
                'failed' => 0,
                'connection' => 'unknown',
            ];
        }
    }
}
