<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TrackUserActivity
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
        // Only track activity for authenticated users
        if (Auth::check()) {
            $user = Auth::user();
            $cacheKey = 'user_activity_' . $user->id;
            
            // Only update activity if it's been more than 5 minutes since last update
            // This prevents excessive database writes
            if (!Cache::has($cacheKey)) {
                $user->updateLastActivity();
                
                // Cache for 5 minutes to prevent excessive updates
                Cache::put($cacheKey, true, 300);
            }
        }

        return $next($request);
    }
}