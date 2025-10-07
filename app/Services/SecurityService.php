<?php

namespace App\Services;

use App\Models\SystemSetting;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class SecurityService
{
    /**
     * Get security settings
     */
    public function getSecuritySettings()
    {
        return [
            'password_policy' => [
                'min_length' => SystemSetting::get('password_min_length', 8),
                'require_uppercase' => SystemSetting::get('password_require_uppercase', true),
                'require_lowercase' => SystemSetting::get('password_require_lowercase', true),
                'require_numbers' => SystemSetting::get('password_require_numbers', true),
                'require_symbols' => SystemSetting::get('password_require_symbols', false),
                'max_age_days' => SystemSetting::get('password_max_age_days', 90),
            ],
            'account_locking' => [
                'enabled' => SystemSetting::get('account_locking_enabled', true),
                'max_attempts' => SystemSetting::get('account_max_attempts', 5),
                'lockout_duration' => SystemSetting::get('account_lockout_duration', 15), // minutes
            ],
            'session_management' => [
                'enabled' => SystemSetting::get('session_management_enabled', true),
                'timeout' => SystemSetting::get('session_timeout', 120), // minutes
                'max_concurrent' => SystemSetting::get('session_max_concurrent', 3),
            ],
        ];
    }

    /**
     * Update security settings
     */
    public function updateSecuritySettings($data)
    {
        try {
            // Password Policy
            if (isset($data['password_policy'])) {
                $policy = $data['password_policy'];
                SystemSetting::set('password_min_length', $policy['min_length'] ?? 8, 'integer', 'Minimum password length', 'security');
                SystemSetting::set('password_require_uppercase', $policy['require_uppercase'] ?? true, 'boolean', 'Require uppercase letters', 'security');
                SystemSetting::set('password_require_lowercase', $policy['require_lowercase'] ?? true, 'boolean', 'Require lowercase letters', 'security');
                SystemSetting::set('password_require_numbers', $policy['require_numbers'] ?? true, 'boolean', 'Require numbers', 'security');
                SystemSetting::set('password_require_symbols', $policy['require_symbols'] ?? false, 'boolean', 'Require symbols', 'security');
                SystemSetting::set('password_max_age_days', $policy['max_age_days'] ?? 90, 'integer', 'Password maximum age in days', 'security');
            }

            // Account Locking
            if (isset($data['account_locking'])) {
                $locking = $data['account_locking'];
                SystemSetting::set('account_locking_enabled', $locking['enabled'] ?? true, 'boolean', 'Enable account locking', 'security');
                SystemSetting::set('account_max_attempts', $locking['max_attempts'] ?? 5, 'integer', 'Maximum login attempts', 'security');
                SystemSetting::set('account_lockout_duration', $locking['lockout_duration'] ?? 15, 'integer', 'Lockout duration in minutes', 'security');
            }

            // Session Management
            if (isset($data['session_management'])) {
                $session = $data['session_management'];
                SystemSetting::set('session_management_enabled', $session['enabled'] ?? true, 'boolean', 'Enable session management', 'security');
                SystemSetting::set('session_timeout', $session['timeout'] ?? 120, 'integer', 'Session timeout in minutes', 'security');
                SystemSetting::set('session_max_concurrent', $session['max_concurrent'] ?? 3, 'integer', 'Maximum concurrent sessions', 'security');
            }

            SystemSetting::clearCache();

            return [
                'success' => true,
                'message' => 'Security settings updated successfully'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to update security settings: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Validate password against policy
     */
    public function validatePassword($password)
    {
        $policy = $this->getSecuritySettings()['password_policy'];
        $errors = [];

        if (strlen($password) < $policy['min_length']) {
            $errors[] = "Password must be at least {$policy['min_length']} characters long";
        }

        if ($policy['require_uppercase'] && !preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }

        if ($policy['require_lowercase'] && !preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }

        if ($policy['require_numbers'] && !preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }

        if ($policy['require_symbols'] && !preg_match('/[^A-Za-z0-9]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }
}
