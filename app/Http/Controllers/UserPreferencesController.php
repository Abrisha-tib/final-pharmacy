<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;
use App\Services\UserPreferencesService;

/**
 * User Preferences Controller
 * 
 * Handles user preferences, settings, and profile customization.
 * 
 * @author Analog Software Solutions
 * @version 1.0
 */
class UserPreferencesController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display user preferences page.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Get user preferences with defaults
        $preferences = UserPreferencesService::getPreferences($user);
        
        return view('user-preferences.index', compact('user', 'preferences'));
    }

    /**
     * Update user preferences.
     */
    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'theme' => 'required|in:light,dark,auto',
            'language' => 'required|in:en,am,ar',
            'timezone' => 'required|string',
            'date_format' => 'required|in:Y-m-d,m/d/Y,d/m/Y',
            'time_format' => 'required|in:12,24',
            'currency' => 'required|in:USD,ETB,EUR',
            'notifications' => 'array',
            'notifications.email' => 'boolean',
            'notifications.sms' => 'boolean',
            'notifications.push' => 'boolean',
            'dashboard_widgets' => 'array',
            'dashboard_widgets.sales_chart' => 'boolean',
            'dashboard_widgets.inventory_alerts' => 'boolean',
            'dashboard_widgets.recent_sales' => 'boolean',
            'dashboard_widgets.quick_actions' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = Auth::user();
            
            // Update user preferences
            $preferences = [
                'theme' => $request->theme,
                'language' => $request->language,
                'timezone' => $request->timezone,
                'date_format' => $request->date_format,
                'time_format' => $request->time_format,
                'currency' => $request->currency,
                'notifications' => $request->notifications ?? [],
                'dashboard_widgets' => $request->dashboard_widgets ?? [],
                'updated_at' => now()
            ];

            // Store preferences in user_preferences table
            $user->preferences()->updateOrCreate(
                ['user_id' => $user->id],
                $preferences
            );

            // Clear user-specific cache
            UserPreferencesService::clearCache($user);
            Cache::forget('dashboard_data_' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Preferences updated successfully',
                'preferences' => $preferences
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update preferences: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Reset preferences to defaults.
     */
    public function reset()
    {
        try {
            $user = Auth::user();
            $user->update(['notes' => null]);
            
            UserPreferencesService::clearCache($user);
            Cache::forget('dashboard_data_' . $user->id);

            return response()->json([
                'success' => true,
                'message' => 'Preferences reset to defaults'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset preferences: ' . $e->getMessage()
            ], 500);
        }
    }
}
