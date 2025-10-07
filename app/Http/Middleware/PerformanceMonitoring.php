<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PerformanceMonitoring
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $response = $next($request);
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        $executionTime = $endTime - $startTime;
        $memoryUsed = $endMemory - $startMemory;
        
        // Log performance metrics for slow requests
        if ($executionTime > 1.0) { // Log requests taking more than 1 second
            Log::warning('Slow request detected', [
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'execution_time' => round($executionTime, 3),
                'memory_used' => $this->formatBytes($memoryUsed),
                'user_agent' => $request->userAgent(),
                'ip' => $request->ip()
            ]);
        }
        
        // Cache performance metrics
        $this->cachePerformanceMetrics($request, $executionTime, $memoryUsed);
        
        // Add performance headers
        $response->headers->set('X-Execution-Time', round($executionTime, 3));
        $response->headers->set('X-Memory-Used', $this->formatBytes($memoryUsed));
        
        return $response;
    }
    
    /**
     * Cache performance metrics for analysis
     */
    private function cachePerformanceMetrics(Request $request, float $executionTime, int $memoryUsed)
    {
        $route = $request->route()?->getName() ?? $request->path();
        $key = "performance_metrics_{$route}";
        
        $metrics = Cache::get($key, [
            'count' => 0,
            'total_time' => 0,
            'total_memory' => 0,
            'avg_time' => 0,
            'avg_memory' => 0,
            'max_time' => 0,
            'max_memory' => 0
        ]);
        
        $metrics['count']++;
        $metrics['total_time'] += $executionTime;
        $metrics['total_memory'] += $memoryUsed;
        $metrics['avg_time'] = $metrics['total_time'] / $metrics['count'];
        $metrics['avg_memory'] = $metrics['total_memory'] / $metrics['count'];
        $metrics['max_time'] = max($metrics['max_time'], $executionTime);
        $metrics['max_memory'] = max($metrics['max_memory'], $memoryUsed);
        
        Cache::put($key, $metrics, 3600); // Cache for 1 hour
    }
    
    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }
}
