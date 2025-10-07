<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Register Spatie Permission middleware
        $middleware->alias([
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'track.activity' => \App\Http\Middleware\TrackUserActivity::class,
            'redirect.unauthorized' => \App\Http\Middleware\RedirectUnauthorizedAccess::class,
            'apply.preferences' => \App\Http\Middleware\ApplyUserPreferences::class,
        ]);
        
        // Add activity tracking, unauthorized access redirect, and user preferences to web middleware group
        $middleware->web(append: [
            \App\Http\Middleware\TrackUserActivity::class,
            \App\Http\Middleware\RedirectUnauthorizedAccess::class,
            \App\Http\Middleware\ApplyUserPreferences::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
