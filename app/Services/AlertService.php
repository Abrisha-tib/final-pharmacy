<?php

namespace App\Services;

use App\Models\Alert;
use App\Models\Medicine;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Alert Service
 * 
 * Business logic for alert generation and management.
 * Handles automatic alert creation for inventory, expiry, and system monitoring.
 * Optimized for cPanel/shared hosting with efficient database operations.
 * 
 * @author Analog Software Solutions
 * @version 1.0
 */
class AlertService
{
    /**
     * Generate all system alerts.
     * 
     * @return array
     */
    public function generateAllAlerts()
    {
        $alerts = [];
        
        try {
            // Generate inventory alerts
            $alerts = array_merge($alerts, $this->generateInventoryAlerts());
            
            // Generate expiry alerts
            $alerts = array_merge($alerts, $this->generateExpiryAlerts());
            
            // Generate system alerts
            $alerts = array_merge($alerts, $this->generateSystemAlerts());
            
            // Generate sales alerts
            $alerts = array_merge($alerts, $this->generateSalesAlerts());
            
            // Generate customer alerts
            $alerts = array_merge($alerts, $this->generateCustomerAlerts());
            
            // Generate supplier alerts
            $alerts = array_merge($alerts, $this->generateSupplierAlerts());
            
            return $alerts;
            
        } catch (\Exception $e) {
            \Log::error('Alert generation error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Generate inventory alerts.
     * 
     * @return array
     */
    public function generateInventoryAlerts()
    {
        $alerts = [];
        
        try {
            // Low stock alerts
            $lowStockMedicines = Medicine::where('stock_quantity', '<=', 10)
                ->where('stock_quantity', '>', 0)
                ->where('is_active', true)
                ->get();
            
            foreach ($lowStockMedicines as $medicine) {
                $existingAlert = Alert::where('category', 'inventory')
                    ->where('type', 'warning')
                    ->where('metadata->medicine_id', $medicine->id)
                    ->where('status', 'active')
                    ->first();
                
                if (!$existingAlert) {
                    $alert = Alert::create([
                        'title' => 'Low Stock Alert',
                        'message' => "Medicine '{$medicine->name}' is running low. Current stock: {$medicine->stock_quantity} units.",
                        'category' => 'inventory',
                        'type' => 'warning',
                        'priority' => $medicine->stock_quantity <= 5 ? 'high' : 'medium',
                        'status' => 'active',
                        'metadata' => [
                            'medicine_id' => $medicine->id,
                            'medicine_name' => $medicine->name,
                            'current_stock' => $medicine->stock_quantity,
                            'reorder_level' => $medicine->reorder_level ?? 10
                        ],
                        'is_auto_generated' => true,
                        'source' => 'low_stock_checker'
                    ]);
                    
                    $alerts[] = $alert;
                }
            }
            
            // Out of stock alerts
            $outOfStockMedicines = Medicine::where('stock_quantity', '<=', 0)
                ->where('is_active', true)
                ->get();
            
            foreach ($outOfStockMedicines as $medicine) {
                $existingAlert = Alert::where('category', 'inventory')
                    ->where('type', 'error')
                    ->where('metadata->medicine_id', $medicine->id)
                    ->where('status', 'active')
                    ->first();
                
                if (!$existingAlert) {
                    $alert = Alert::create([
                        'title' => 'Out of Stock Alert',
                        'message' => "Medicine '{$medicine->name}' is out of stock. Immediate restocking required.",
                        'category' => 'inventory',
                        'type' => 'error',
                        'priority' => 'critical',
                        'status' => 'active',
                        'metadata' => [
                            'medicine_id' => $medicine->id,
                            'medicine_name' => $medicine->name,
                            'current_stock' => $medicine->stock_quantity
                        ],
                        'is_auto_generated' => true,
                        'source' => 'out_of_stock_checker'
                    ]);
                    
                    $alerts[] = $alert;
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Inventory alert generation error: ' . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * Generate expiry alerts.
     * 
     * @return array
     */
    public function generateExpiryAlerts()
    {
        $alerts = [];
        
        try {
            // Expiring soon (30 days)
            $expiringSoon = Medicine::where('expiry_date', '<=', now()->addDays(30))
                ->where('expiry_date', '>', now())
                ->where('is_active', true)
                ->get();
            
            foreach ($expiringSoon as $medicine) {
                $daysUntilExpiry = now()->diffInDays($medicine->expiry_date);
                
                $existingAlert = Alert::where('category', 'expiry')
                    ->where('metadata->medicine_id', $medicine->id)
                    ->where('status', 'active')
                    ->first();
                
                if (!$existingAlert) {
                    $priority = $daysUntilExpiry <= 7 ? 'critical' : ($daysUntilExpiry <= 15 ? 'high' : 'medium');
                    $type = $daysUntilExpiry <= 7 ? 'critical' : 'warning';
                    
                    $alert = Alert::create([
                        'title' => 'Medicine Expiring Soon',
                        'message' => "Medicine '{$medicine->name}' expires in {$daysUntilExpiry} days ({$medicine->expiry_date->format('M d, Y')}).",
                        'category' => 'expiry',
                        'type' => $type,
                        'priority' => $priority,
                        'status' => 'active',
                        'metadata' => [
                            'medicine_id' => $medicine->id,
                            'medicine_name' => $medicine->name,
                            'expiry_date' => $medicine->expiry_date->format('Y-m-d'),
                            'days_until_expiry' => $daysUntilExpiry,
                            'batch_number' => $medicine->batch_number
                        ],
                        'is_auto_generated' => true,
                        'source' => 'expiry_monitor',
                        'expires_at' => $medicine->expiry_date
                    ]);
                    
                    $alerts[] = $alert;
                }
            }
            
            // Expired medicines
            $expiredMedicines = Medicine::where('expiry_date', '<', now())
                ->where('is_active', true)
                ->get();
            
            foreach ($expiredMedicines as $medicine) {
                $existingAlert = Alert::where('category', 'expiry')
                    ->where('type', 'critical')
                    ->where('metadata->medicine_id', $medicine->id)
                    ->where('status', 'active')
                    ->first();
                
                if (!$existingAlert) {
                    $alert = Alert::create([
                        'title' => 'Medicine Expired',
                        'message' => "Medicine '{$medicine->name}' has expired on {$medicine->expiry_date->format('M d, Y')}. Remove from inventory immediately.",
                        'category' => 'expiry',
                        'type' => 'critical',
                        'priority' => 'critical',
                        'status' => 'active',
                        'metadata' => [
                            'medicine_id' => $medicine->id,
                            'medicine_name' => $medicine->name,
                            'expiry_date' => $medicine->expiry_date->format('Y-m-d'),
                            'batch_number' => $medicine->batch_number
                        ],
                        'is_auto_generated' => true,
                        'source' => 'expiry_monitor'
                    ]);
                    
                    $alerts[] = $alert;
                }
            }
            
        } catch (\Exception $e) {
            \Log::error('Expiry alert generation error: ' . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * Generate system alerts.
     * 
     * @return array
     */
    public function generateSystemAlerts()
    {
        $alerts = [];
        
        try {
            // Database connection health
            $dbHealth = $this->checkDatabaseHealth();
            if (!$dbHealth) {
                $alert = Alert::create([
                    'title' => 'Database Connection Issue',
                    'message' => 'Database connection is experiencing issues. Please check system status.',
                    'category' => 'system',
                    'type' => 'error',
                    'priority' => 'critical',
                    'status' => 'active',
                    'is_auto_generated' => true,
                    'source' => 'system_monitor'
                ]);
                
                $alerts[] = $alert;
            }
            
            // Cache performance
            $cacheHealth = $this->checkCacheHealth();
            if (!$cacheHealth) {
                $alert = Alert::create([
                    'title' => 'Cache Performance Issue',
                    'message' => 'System cache is experiencing performance issues. Consider clearing cache.',
                    'category' => 'system',
                    'type' => 'warning',
                    'priority' => 'medium',
                    'status' => 'active',
                    'is_auto_generated' => true,
                    'source' => 'system_monitor'
                ]);
                
                $alerts[] = $alert;
            }
            
            // Disk space (if applicable)
            $diskSpace = $this->checkDiskSpace();
            if ($diskSpace < 10) { // Less than 10% free space
                $alert = Alert::create([
                    'title' => 'Low Disk Space',
                    'message' => "Server disk space is running low. Only {$diskSpace}% free space remaining.",
                    'category' => 'system',
                    'type' => 'warning',
                    'priority' => 'high',
                    'status' => 'active',
                    'metadata' => ['free_space_percentage' => $diskSpace],
                    'is_auto_generated' => true,
                    'source' => 'system_monitor'
                ]);
                
                $alerts[] = $alert;
            }
            
        } catch (\Exception $e) {
            \Log::error('System alert generation error: ' . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * Generate sales alerts.
     * 
     * @return array
     */
    public function generateSalesAlerts()
    {
        $alerts = [];
        
        try {
            // High-value sales
            $highValueSales = Sale::where('total_amount', '>', 1000)
                ->where('created_at', '>=', now()->subHours(24))
                ->count();
            
            if ($highValueSales > 0) {
                $alert = Alert::create([
                    'title' => 'High-Value Sales Detected',
                    'message' => "{$highValueSales} high-value sales (over $1,000) detected in the last 24 hours.",
                    'category' => 'sales',
                    'type' => 'info',
                    'priority' => 'medium',
                    'status' => 'active',
                    'metadata' => ['high_value_sales_count' => $highValueSales],
                    'is_auto_generated' => true,
                    'source' => 'sales_monitor'
                ]);
                
                $alerts[] = $alert;
            }
            
            // Unusual sales patterns
            $todaySales = Sale::whereDate('created_at', today())->count();
            $yesterdaySales = Sale::whereDate('created_at', yesterday())->count();
            
            if ($todaySales > ($yesterdaySales * 2) && $todaySales > 10) {
                $alert = Alert::create([
                    'title' => 'Unusual Sales Activity',
                    'message' => "Sales activity is significantly higher than usual. Today: {$todaySales}, Yesterday: {$yesterdaySales}.",
                    'category' => 'sales',
                    'type' => 'info',
                    'priority' => 'medium',
                    'status' => 'active',
                    'metadata' => [
                        'today_sales' => $todaySales,
                        'yesterday_sales' => $yesterdaySales
                    ],
                    'is_auto_generated' => true,
                    'source' => 'sales_monitor'
                ]);
                
                $alerts[] = $alert;
            }
            
        } catch (\Exception $e) {
            \Log::error('Sales alert generation error: ' . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * Generate customer alerts.
     * 
     * @return array
     */
    public function generateCustomerAlerts()
    {
        $alerts = [];
        
        try {
            // New customers
            $newCustomers = Customer::where('created_at', '>=', now()->subDays(7))->count();
            
            if ($newCustomers > 0) {
                $alert = Alert::create([
                    'title' => 'New Customer Registrations',
                    'message' => "{$newCustomers} new customers registered in the last 7 days.",
                    'category' => 'customer',
                    'type' => 'success',
                    'priority' => 'low',
                    'status' => 'active',
                    'metadata' => ['new_customers_count' => $newCustomers],
                    'is_auto_generated' => true,
                    'source' => 'customer_monitor'
                ]);
                
                $alerts[] = $alert;
            }
            
        } catch (\Exception $e) {
            \Log::error('Customer alert generation error: ' . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * Generate supplier alerts.
     * 
     * @return array
     */
    public function generateSupplierAlerts()
    {
        $alerts = [];
        
        try {
            // Inactive suppliers
            $inactiveSuppliers = Supplier::where('status', 'Inactive')->count();
            
            if ($inactiveSuppliers > 0) {
                $alert = Alert::create([
                    'title' => 'Inactive Suppliers',
                    'message' => "{$inactiveSuppliers} suppliers are marked as inactive. Consider reviewing supplier relationships.",
                    'category' => 'supplier',
                    'type' => 'warning',
                    'priority' => 'medium',
                    'status' => 'active',
                    'metadata' => ['inactive_suppliers_count' => $inactiveSuppliers],
                    'is_auto_generated' => true,
                    'source' => 'supplier_monitor'
                ]);
                
                $alerts[] = $alert;
            }
            
        } catch (\Exception $e) {
            \Log::error('Supplier alert generation error: ' . $e->getMessage());
        }
        
        return $alerts;
    }

    /**
     * Check database health.
     * 
     * @return bool
     */
    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check cache health.
     * 
     * @return bool
     */
    private function checkCacheHealth()
    {
        try {
            $testKey = 'health_check_' . time();
            Cache::put($testKey, 'test', 60);
            $result = Cache::get($testKey);
            Cache::forget($testKey);
            return $result === 'test';
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check disk space.
     * 
     * @return float
     */
    private function checkDiskSpace()
    {
        try {
            $bytes = disk_free_space(storage_path());
            $total = disk_total_space(storage_path());
            return ($bytes / $total) * 100;
        } catch (\Exception $e) {
            return 100; // Assume healthy if we can't check
        }
    }

    /**
     * Clean up old alerts.
     * 
     * @return int Number of alerts cleaned up
     */
    public function cleanupOldAlerts()
    {
        try {
            $cutoffDate = now()->subDays(30);
            
            $deletedCount = Alert::where('status', 'resolved')
                ->where('updated_at', '<', $cutoffDate)
                ->delete();
            
            return $deletedCount;
            
        } catch (\Exception $e) {
            \Log::error('Alert cleanup error: ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * Get alert summary for dashboard.
     * 
     * @return array
     */
    public function getAlertSummary()
    {
        $cacheKey = 'alert_summary';
        
        return Cache::remember($cacheKey, 300, function() {
            return [
                'total' => Alert::count(),
                'active' => Alert::active()->count(),
                'critical' => Alert::critical()->active()->count(),
                'acknowledged' => Alert::acknowledged()->count(),
                'resolved' => Alert::resolved()->count(),
                'by_category' => Alert::selectRaw('category, count(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category'),
                'recent_critical' => Alert::critical()
                    ->active()
                    ->orderBy('created_at', 'desc')
                    ->limit(5)
                    ->get()
            ];
        });
    }
}
