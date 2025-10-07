<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectUnauthorizedAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // If user is not authenticated, let the auth middleware handle it
        if (!Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();
        $route = $request->route()->getName();

        // Define route-to-permission mapping
        $routePermissions = [
            'inventory' => 'view-inventory',
            'dispensary' => 'view-dispensary',
            'sales' => 'view-sales',
            'cashier' => 'view-cashier',
            'purchases.index' => 'view-purchases',
            'suppliers' => 'view-suppliers',
            'customers.index' => 'view-customers',
            'reports' => 'view-reports',
            'alerts' => 'view-alerts',
            'settings.index' => 'view-settings',
            'users.index' => 'view-users',
        ];

        // Check if current route requires permission
        if (isset($routePermissions[$route])) {
            $requiredPermission = $routePermissions[$route];
            
            if (!$user->can($requiredPermission)) {
                // Find the first accessible route for this user
                $accessibleRoute = $this->getFirstAccessibleRoute($user);
                
                return redirect()->route($accessibleRoute)
                    ->with('error', 'You do not have permission to access this page.');
            }
        }

        return $next($request);
    }

    /**
     * Get the first accessible route for the user based on their permissions
     */
    private function getFirstAccessibleRoute($user)
    {
        // Define routes in order of preference
        $routePriority = [
            'dashboard' => null, // Always accessible
            'sales' => 'view-sales',
            'cashier' => 'view-cashier',
            'reports' => 'view-reports',
            'inventory' => 'view-inventory',
            'dispensary' => 'view-dispensary',
            'customers.index' => 'view-customers',
            'purchases.index' => 'view-purchases',
            'suppliers' => 'view-suppliers',
            'alerts' => 'view-alerts',
            'settings.index' => 'view-settings',
            'users.index' => 'view-users',
        ];

        foreach ($routePriority as $route => $permission) {
            if ($permission === null || $user->can($permission)) {
                return $route;
            }
        }

        // Fallback to dashboard
        return 'dashboard';
    }
}
