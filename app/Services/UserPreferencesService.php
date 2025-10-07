<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Carbon\Carbon;

/**
 * User Preferences Service
 * 
 * Handles global user preferences integration throughout the application.
 * 
 * @author Analog Software Solutions
 * @version 1.0
 */
class UserPreferencesService
{
    /**
     * Get user preferences with caching
     */
    public static function getPreferences($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        
        if (!$user) {
            return self::getDefaults();
        }
        
        $cacheKey = 'user_preferences_' . $user->id;
        
        return Cache::remember($cacheKey, 300, function () use ($user) {
            $defaults = self::getDefaults();
            
            if ($user->notes) {
                $saved = json_decode($user->notes, true);
                if (is_array($saved)) {
                    return array_merge($defaults, $saved);
                }
            }
            
            return $defaults;
        });
    }
    
    /**
     * Get default preferences
     */
    public static function getDefaults()
    {
        return [
            'theme' => 'auto',
            'language' => 'en',
            'timezone' => 'Africa/Addis_Ababa',
            'date_format' => 'Y-m-d',
            'time_format' => '24',
            'currency' => 'ETB',
            'currency_symbol' => 'Br',
            'notifications' => [
                'email' => true,
                'sms' => false,
                'push' => true
            ],
            'dashboard_widgets' => [
                'sales_chart' => true,
                'inventory_alerts' => true,
                'recent_sales' => true,
                'quick_actions' => true
            ]
        ];
    }
    
    /**
     * Get user's theme preference
     */
    public static function getTheme($user = null)
    {
        $preferences = self::getPreferences($user);
        return $preferences['theme'];
    }
    
    /**
     * Get user's currency preference
     */
    public static function getCurrency($user = null)
    {
        $preferences = self::getPreferences($user);
        return $preferences['currency'];
    }
    
    /**
     * Get user's currency symbol
     */
    public static function getCurrencySymbol($user = null)
    {
        $preferences = self::getPreferences($user);
        return $preferences['currency_symbol'];
    }
    
    /**
     * Get user's language preference
     */
    public static function getLanguage($user = null)
    {
        $preferences = self::getPreferences($user);
        return $preferences['language'];
    }
    
    /**
     * Get user's timezone
     */
    public static function getTimezone($user = null)
    {
        $preferences = self::getPreferences($user);
        return $preferences['timezone'];
    }
    
    /**
     * Get user's date format
     */
    public static function getDateFormat($user = null)
    {
        $preferences = self::getPreferences($user);
        return $preferences['date_format'];
    }
    
    /**
     * Get user's time format
     */
    public static function getTimeFormat($user = null)
    {
        $preferences = self::getPreferences($user);
        return $preferences['time_format'];
    }
    
    /**
     * Format date according to user preferences
     */
    public static function formatDate($date, $user = null)
    {
        $preferences = self::getPreferences($user);
        $timezone = $preferences['timezone'];
        $format = $preferences['date_format'];
        
        if ($date instanceof Carbon) {
            return $date->setTimezone($timezone)->format($format);
        }
        
        return Carbon::parse($date)->setTimezone($timezone)->format($format);
    }
    
    /**
     * Format time according to user preferences
     */
    public static function formatTime($time, $user = null)
    {
        $preferences = self::getPreferences($user);
        $timezone = $preferences['timezone'];
        $format = $preferences['time_format'] === '12' ? 'g:i A' : 'H:i';
        
        if ($time instanceof Carbon) {
            return $time->setTimezone($timezone)->format($format);
        }
        
        return Carbon::parse($time)->setTimezone($timezone)->format($format);
    }
    
    /**
     * Format currency according to user preferences
     */
    public static function formatCurrency($amount, $user = null)
    {
        $preferences = self::getPreferences($user);
        $symbol = $preferences['currency_symbol'];
        
        return $symbol . ' ' . number_format($amount, 2);
    }
    
    /**
     * Get dashboard widget preferences
     */
    public static function getDashboardWidgets($user = null)
    {
        $preferences = self::getPreferences($user);
        return $preferences['dashboard_widgets'];
    }
    
    /**
     * Check if a dashboard widget should be shown
     */
    public static function shouldShowWidget($widgetName, $user = null)
    {
        $widgets = self::getDashboardWidgets($user);
        return $widgets[$widgetName] ?? true;
    }
    
    /**
     * Clear user preferences cache
     */
    public static function clearCache($user = null)
    {
        if (!$user) {
            $user = Auth::user();
        }
        
        if ($user) {
            Cache::forget('user_preferences_' . $user->id);
        }
    }
    
    /**
     * Apply theme to HTML element
     */
    public static function applyTheme($user = null)
    {
        $theme = self::getTheme($user);
        
        if ($theme === 'auto') {
            return 'auto';
        }
        
        return $theme;
    }
    
    /**
     * Get notification preferences
     */
    public static function getNotificationPreferences($user = null)
    {
        $preferences = self::getPreferences($user);
        return $preferences['notifications'];
    }
    
    /**
     * Check if email notifications are enabled
     */
    public static function isEmailNotificationsEnabled($user = null)
    {
        $notifications = self::getNotificationPreferences($user);
        return $notifications['email'] ?? true;
    }
    
    /**
     * Check if SMS notifications are enabled
     */
    public static function isSmsNotificationsEnabled($user = null)
    {
        $notifications = self::getNotificationPreferences($user);
        return $notifications['sms'] ?? false;
    }
    
    /**
     * Check if push notifications are enabled
     */
    public static function isPushNotificationsEnabled($user = null)
    {
        $notifications = self::getNotificationPreferences($user);
        return $notifications['push'] ?? true;
    }
}
