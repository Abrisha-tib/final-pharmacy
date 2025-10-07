<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SystemSetting;

class SystemSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Security Settings
        SystemSetting::set('password_min_length', 8, 'integer', 'Minimum password length', 'security');
        SystemSetting::set('password_require_uppercase', true, 'boolean', 'Require uppercase letters', 'security');
        SystemSetting::set('password_require_lowercase', true, 'boolean', 'Require lowercase letters', 'security');
        SystemSetting::set('password_require_numbers', true, 'boolean', 'Require numbers', 'security');
        SystemSetting::set('password_require_symbols', false, 'boolean', 'Require symbols', 'security');
        SystemSetting::set('password_max_age_days', 90, 'integer', 'Password maximum age in days', 'security');
        
        SystemSetting::set('account_locking_enabled', true, 'boolean', 'Enable account locking', 'security');
        SystemSetting::set('account_max_attempts', 5, 'integer', 'Maximum login attempts', 'security');
        SystemSetting::set('account_lockout_duration', 15, 'integer', 'Lockout duration in minutes', 'security');
        
        SystemSetting::set('session_management_enabled', true, 'boolean', 'Enable session management', 'security');
        SystemSetting::set('session_timeout', 120, 'integer', 'Session timeout in minutes', 'security');
        SystemSetting::set('session_max_concurrent', 3, 'integer', 'Maximum concurrent sessions', 'security');

        // Email Settings
        SystemSetting::set('smtp_host', '', 'string', 'SMTP Host', 'email');
        SystemSetting::set('smtp_port', 587, 'integer', 'SMTP Port', 'email');
        SystemSetting::set('smtp_username', '', 'string', 'SMTP Username', 'email');
        SystemSetting::set('smtp_password', '', 'string', 'SMTP Password', 'email');
        SystemSetting::set('smtp_encryption', 'tls', 'string', 'SMTP Encryption', 'email');
        SystemSetting::set('smtp_from_address', '', 'string', 'From Email Address', 'email');
        SystemSetting::set('smtp_from_name', 'Pharmacy Management System', 'string', 'From Name', 'email');
        
        SystemSetting::set('email_notifications_enabled', false, 'boolean', 'Enable email notifications', 'email');
        SystemSetting::set('email_low_stock', true, 'boolean', 'Low stock notifications', 'email');
        SystemSetting::set('email_system_alerts', true, 'boolean', 'System alert notifications', 'email');
        SystemSetting::set('email_user_activities', false, 'boolean', 'User activity notifications', 'email');
        
        SystemSetting::set('email_queue_enabled', false, 'boolean', 'Enable email queue', 'email');
        SystemSetting::set('email_queue_connection', 'database', 'string', 'Email queue connection', 'email');

        // Printer Settings
        SystemSetting::set('default_printer', '', 'string', 'Default printer name', 'printer');
        SystemSetting::set('print_quality', 'normal', 'string', 'Print quality setting', 'printer');
        SystemSetting::set('paper_size', 'A4', 'string', 'Paper size', 'printer');
        SystemSetting::set('print_orientation', 'portrait', 'string', 'Print orientation', 'printer');
        SystemSetting::set('color_mode', 'color', 'string', 'Color mode', 'printer');
        SystemSetting::set('duplex_printing', false, 'boolean', 'Duplex printing', 'printer');
        SystemSetting::set('auto_cut', false, 'boolean', 'Auto cut after printing', 'printer');
        SystemSetting::set('print_margin', 'normal', 'string', 'Print margins', 'printer');
        SystemSetting::set('header_footer', true, 'boolean', 'Print headers and footers', 'printer');
        SystemSetting::set('watermark', false, 'boolean', 'Print watermark', 'printer');
    }
}