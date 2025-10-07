<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use App\Services\UserPreferencesService;
use Carbon\Carbon;

/**
 * Apply User Preferences Middleware
 * 
 * Automatically applies user preferences to all views and controllers.
 * 
 * @author Analog Software Solutions
 * @version 1.0
 */
class ApplyUserPreferences
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $preferences = UserPreferencesService::getPreferences($user);
            
            // Share preferences with all views
            View::share('userPreferences', $preferences);
            View::share('userTheme', UserPreferencesService::getTheme($user));
            View::share('userCurrency', UserPreferencesService::getCurrency($user));
            View::share('userCurrencySymbol', UserPreferencesService::getCurrencySymbol($user));
            View::share('userLanguage', UserPreferencesService::getLanguage($user));
            View::share('userTimezone', UserPreferencesService::getTimezone($user));
            View::share('userDateFormat', UserPreferencesService::getDateFormat($user));
            View::share('userTimeFormat', UserPreferencesService::getTimeFormat($user));
            
            // Set application locale
            app()->setLocale($preferences['language']);
            
            // Set timezone for Carbon
            Carbon::setTestNow(Carbon::now($preferences['timezone']));
            
            // Add helper functions to views
            View::share('formatUserDate', function($date) use ($user) {
                return UserPreferencesService::formatDate($date, $user);
            });
            
            View::share('formatUserTime', function($time) use ($user) {
                return UserPreferencesService::formatTime($time, $user);
            });
            
            View::share('formatUserCurrency', function($amount) use ($user) {
                return UserPreferencesService::formatCurrency($amount, $user);
            });
            
            View::share('shouldShowWidget', function($widgetName) use ($user) {
                return UserPreferencesService::shouldShowWidget($widgetName, $user);
            });
        } else {
            // Default preferences for non-authenticated users
            $defaults = UserPreferencesService::getDefaults();
            View::share('userPreferences', $defaults);
            View::share('userTheme', 'auto');
            View::share('userCurrency', 'ETB');
            View::share('userCurrencySymbol', 'Br');
            View::share('userLanguage', 'en');
            View::share('userTimezone', 'Africa/Addis_Ababa');
            View::share('userDateFormat', 'Y-m-d');
            View::share('userTimeFormat', '24');
        }
        
        return $next($request);
    }
}
