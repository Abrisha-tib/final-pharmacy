<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\DispensaryController;
use App\Http\Controllers\ImportExportController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AlertController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\RoleManagementController;

// Public routes
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

// Authentication routes
Auth::routes();

// Dashboard routes (protected with authentication and permissions)
Route::middleware(['auth', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-sales'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/stats', [DashboardController::class, 'getStats'])->name('dashboard.stats');
    Route::get('/dashboard/real-time', [DashboardController::class, 'getRealTimeUpdates'])->name('dashboard.real-time');
    Route::get('/dashboard/low-stock-alerts', [DashboardController::class, 'getLowStockAlerts'])->name('dashboard.low-stock-alerts');
    Route::get('/dashboard/chart-data/{period}', [DashboardController::class, 'getChartData'])->name('dashboard.chart-data');
    Route::post('/dashboard/clear-cache', [DashboardController::class, 'clearCache'])->name('dashboard.clear-cache');
});

// Sales routes (protected with authentication only)
Route::middleware(['auth'])->group(function () {
    Route::get('/sales', [SalesController::class, 'index'])->name('sales');
    Route::get('/sales/print', [SalesController::class, 'printReport'])->name('sales.print');
    
    // Sales API routes
    Route::prefix('sales/api')->group(function () {
        Route::get('/', [SalesController::class, 'getSales'])->name('sales.api.index');
        Route::get('/stats', [SalesController::class, 'getStats'])->name('sales.api.stats');
        Route::post('/', [SalesController::class, 'store'])->name('sales.api.store');
        Route::get('/{id}', [SalesController::class, 'show'])->name('sales.api.show');
        Route::put('/{id}/status', [SalesController::class, 'updateStatus'])->name('sales.api.update-status');
        Route::delete('/{id}', [SalesController::class, 'destroy'])->name('sales.api.destroy');
        Route::get('/medicines/search', [SalesController::class, 'searchMedicines'])->name('sales.api.search-medicines');
        Route::get('/performance/metrics', [SalesController::class, 'getPerformanceMetrics'])->name('sales.api.performance');
    });
});

// Cashier routes (protected with authentication only)
Route::middleware(['auth'])->group(function () {
    Route::get('/cashier', [CashierController::class, 'index'])->name('cashier');
    
    // Cashier API routes
    Route::prefix('cashier/api')->group(function () {
        Route::get('/metrics', [CashierController::class, 'getMetrics'])->name('cashier.api.metrics');
        Route::get('/sales', [CashierController::class, 'getSales'])->name('cashier.api.sales');
        Route::get('/sales/{id}', [CashierController::class, 'getSaleDetails'])->name('cashier.api.sale-details');
        Route::get('/refresh', [CashierController::class, 'refresh'])->name('cashier.api.refresh');
        Route::post('/sales/{id}/process', [CashierController::class, 'processSale'])->name('cashier.api.process-sale');
        Route::get('/sales/{id}/receipt', [CashierController::class, 'generateReceipt'])->name('cashier.api.generate-receipt');
    });
});

// Suppliers routes (protected with authentication only)
Route::middleware(['auth'])->group(function () {
    Route::get('/suppliers', function (Request $request) {
        // Get suppliers from database with pagination
        $query = \App\Models\Supplier::query();

        // Apply search filter
        if ($request->has('search') && $request->search) {
            $searchTerm = $request->search;
            $query->where(function($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('contact_person', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // Apply status filter
        if ($request->has('status') && $request->status && $request->status !== 'All Statuses') {
            $query->where('status', $request->status);
        }

        // Apply category filter (if categories are stored as JSON)
        if ($request->has('category') && $request->category && $request->category !== 'All Categories') {
            $query->whereJsonContains('categories', $request->category);
        }

        // Pagination
        $perPage = 12; // Show 12 suppliers per page
        $suppliers = $query->paginate($perPage);

        return view('suppliers', compact('suppliers', 'request'));
    })->name('suppliers');
    
    // Supplier operations routes
    Route::get('/suppliers/{id}', function ($id) {
        $supplier = \App\Models\Supplier::findOrFail($id);
        return response()->json($supplier);
    })->name('suppliers.view');
    
    Route::post('/suppliers/store', function (Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'status' => 'required|in:Active,Pending,Inactive',
            'rating' => 'nullable|numeric|min:0|max:5',
            'total_orders' => 'nullable|integer|min:0',
            'on_time_delivery' => 'nullable|string|max:10',
            'total_spent' => 'nullable|numeric|min:0',
            'categories' => 'nullable|array'
        ]);
        
        $supplier = \App\Models\Supplier::create($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Supplier created successfully',
            'supplier' => $supplier
        ]);
    })->name('suppliers.store');
    
    Route::put('/suppliers/{id}', function (Request $request, $id) {
        $supplier = \App\Models\Supplier::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_person' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'status' => 'required|in:Active,Pending,Inactive',
            'rating' => 'nullable|numeric|min:0|max:5',
            'total_orders' => 'nullable|integer|min:0',
            'on_time_delivery' => 'nullable|string|max:10',
            'total_spent' => 'nullable|numeric|min:0',
            'categories' => 'nullable|array'
        ]);
        
        $supplier->update($request->all());
        
        return response()->json([
            'success' => true,
            'message' => 'Supplier updated successfully',
            'supplier' => $supplier
        ]);
    })->name('suppliers.update');
    
    Route::delete('/suppliers/{id}', function ($id) {
        $supplier = \App\Models\Supplier::findOrFail($id);
        $supplier->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Supplier deleted successfully'
        ]);
    })->name('suppliers.delete');
    
    // POST route for handling form submissions (redirects to GET)
    Route::post('/suppliers', function (Request $request) {
        // Build query parameters from POST data
        $params = [];
        
        if ($request->has('search') && $request->search) {
            $params['search'] = $request->search;
        }
        
        if ($request->has('category') && $request->category) {
            $params['category'] = $request->category;
        }
        
        if ($request->has('status') && $request->status) {
            $params['status'] = $request->status;
        }
        
        if ($request->has('page') && $request->page) {
            $params['page'] = $request->page;
        }
        
        // Redirect to GET route with parameters
        return redirect()->route('suppliers', $params);
    })->name('suppliers.filter');
});

// Inventory routes (protected with authentication only)
Route::middleware(['auth'])->group(function () {
            // GET route for displaying inventory (optimized for shared hosting)
            Route::get('/inventory', function (Request $request) {
                $perPage = 20; // Increased for better performance
                
                // Build optimized query - select only required columns
                $query = \App\Models\Medicine::select([
                    'id', 'name', 'generic_name', 'stock_quantity', 
                    'selling_price', 'cost_price', 'category_id', 
                    'is_active', 'batch_number', 'expiry_date',
                    'manufacturer', 'strength', 'form', 'unit', 'barcode'
                ])->with('category:id,name,color');
        
        // Search functionality - search through ALL medicines
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('generic_name', 'like', "%{$search}%")
                  ->orWhere('batch_number', 'like', "%{$search}%");
            });
        }
        
        // Category filter - filter through ALL medicines
        if ($request->has('category') && $request->category) {
            $query->whereHas('category', function($q) use ($request) {
                $q->where('name', 'like', "%{$request->category}%");
            });
        }
        
        // Batch number filter - filter through ALL medicines
        if ($request->has('batch') && $request->batch) {
            $query->where('batch_number', 'like', "%{$request->batch}%");
        }
        
        // Stock status filter - filter through ALL medicines
        if ($request->has('stock') && $request->stock) {
            switch ($request->stock) {
                case 'in-stock':
                    $query->where('stock_quantity', '>', 10);
                    break;
                case 'low-stock':
                    $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 10);
                    break;
                case 'out-of-stock':
                    $query->where('stock_quantity', '<=', 0);
                    break;
            }
        }
        
                // Apply pagination to the filtered results with caching
                $cacheKey = 'medicines_' . md5(serialize($request->all()));
                $medicines = \Cache::remember($cacheKey, 300, function() use ($query, $perPage) {
                    return $query->orderBy('name')->paginate($perPage);
                });
                
                // Cache categories and forms for 1 hour
                $categories = \Cache::remember('categories_active', 3600, function() {
                    return \App\Models\Category::active()->ordered()->get();
                });
                
                $pharmaceuticalForms = \Cache::remember('pharmaceutical_forms', 3600, function() {
                    return \App\Models\PharmaceuticalForm::active()->orderBy('sort_order')->get();
                });
                
                $pharmaceuticalUnits = \Cache::remember('pharmaceutical_units', 3600, function() {
                    return \App\Models\PharmaceuticalUnit::active()->orderBy('sort_order')->get();
                });
                
                // Cache statistics for 5 minutes - OPTIMIZED for shared hosting
                // Use a simple cache key that doesn't depend on query parameters for better reliability
                $statsCacheKey = 'inventory_stats_global';
                
                // Check if cache exists and what it contains
                $cachedStats = \Cache::get($statsCacheKey);
                \Log::info('Cache Check', [
                    'cache_key' => $statsCacheKey,
                    'cached_exists' => $cachedStats !== null,
                    'cached_value' => $cachedStats
                ]);
                
                $stats = \Cache::remember($statsCacheKey, 300, function() {
                    \Log::info('Cache MISS - Recalculating statistics');
                    
                    // Use separate optimized queries for better compatibility
                    $totalMedicines = \App\Models\Medicine::count();
                    $activeMedicines = \App\Models\Medicine::where('is_active', true)->count();
                    
                    // Calculate total value using collection for accuracy
                    $medicines = \App\Models\Medicine::select('selling_price', 'stock_quantity')->get();
                    $totalValue = $medicines->sum(function($medicine) {
                        return (float)$medicine->selling_price * (int)$medicine->stock_quantity;
                    });
                    
                    // Detailed debug logging
                    \Log::info('Statistics Calculation - DETAILED', [
                        'total_medicines' => $totalMedicines,
                        'active_medicines' => $activeMedicines,
                        'total_value' => $totalValue,
                        'medicines_sample' => $medicines->take(3)->map(function($m) {
                            return [
                                'selling_price' => $m->selling_price,
                                'stock_quantity' => $m->stock_quantity,
                                'calculated' => (float)$m->selling_price * (int)$m->stock_quantity
                            ];
                        }),
                        'all_medicines_sum' => $medicines->sum('selling_price'),
                        'all_medicines_count' => $medicines->count()
                    ]);
                    
                    return [
                        'totalMedicines' => $totalMedicines,
                        'activeMedicines' => $activeMedicines,
                        'totalValue' => $totalValue,
                        'inStock' => $activeMedicines,
                        'outOfStock' => $totalMedicines - $activeMedicines
                    ];
                });
                
                // Log final result
                \Log::info('Final Statistics Result', [
                    'stats' => $stats,
                    'totalValue_type' => gettype($stats['totalValue']),
                    'totalValue_value' => $stats['totalValue']
                ]);
                
                extract($stats);
        
        return view('inventory', compact('medicines', 'categories', 'pharmaceuticalForms', 'pharmaceuticalUnits', 'totalMedicines', 'activeMedicines', 'totalValue', 'inStock', 'outOfStock', 'request'));
    })->name('inventory');
    
    // POST route for handling form submissions (redirects to GET)
    Route::post('/inventory', function (Request $request) {
        // Build query parameters from POST data
        $params = [];
        
        if ($request->has('search') && $request->search) {
            $params['search'] = $request->search;
        }
        
        if ($request->has('category') && $request->category) {
            $params['category'] = $request->category;
        }
        
        if ($request->has('batch') && $request->batch) {
            $params['batch'] = $request->batch;
        }
        
        if ($request->has('stock') && $request->stock) {
            $params['stock'] = $request->stock;
        }
        
        if ($request->has('page') && $request->page) {
            $params['page'] = $request->page;
        }
        
        // Redirect to GET route with parameters
        return redirect()->route('inventory', $params);
    })->name('inventory.filter');
    
    // Dispensary routes (protected with authentication only)
    Route::get('/dispensary', [DispensaryController::class, 'index'])->name('dispensary');
    Route::post('/dispensary', [DispensaryController::class, 'filter'])->name('dispensary.filter');
    Route::get('/dispensary/medicine/{id}', [DispensaryController::class, 'getMedicineDetails'])->name('dispensary.medicine');
    Route::post('/dispensary/clear-cache', [DispensaryController::class, 'clearCache'])->name('dispensary.clear-cache');
    
    // Transfer routes
Route::prefix('transfers')->name('transfers.')->group(function () {
    Route::get('/inventory-medicines', [App\Http\Controllers\TransferController::class, 'getInventoryMedicines'])->name('inventory-medicines');
    Route::get('/medicine/{id}', [App\Http\Controllers\TransferController::class, 'getMedicineDetails'])->name('medicine-details');
    Route::post('/to-dispensary', [App\Http\Controllers\TransferController::class, 'transferToDispensary'])->name('to-dispensary');
    Route::get('/history', [App\Http\Controllers\TransferController::class, 'getTransferHistory'])->name('history');
});

    // Dispensary Analytics Routes
    Route::prefix('dispensary')->name('dispensary.')->group(function () {
        Route::get('/analytics', [App\Http\Controllers\DispensaryController::class, 'getAnalytics'])->name('analytics');
        Route::get('/analytics/export', [App\Http\Controllers\DispensaryController::class, 'exportAnalytics'])->name('analytics.export');
        Route::get('/categories', [App\Http\Controllers\DispensaryController::class, 'getCategories'])->name('categories');
        Route::get('/export-stats', [App\Http\Controllers\DispensaryController::class, 'getExportStats'])->name('export-stats');
        Route::get('/template', [App\Http\Controllers\DispensaryController::class, 'downloadTemplate'])->name('template');
        Route::post('/import', [App\Http\Controllers\DispensaryController::class, 'importData'])->name('import');
        Route::get('/export', [App\Http\Controllers\DispensaryController::class, 'exportData'])->name('export');
        Route::get('/print', [App\Http\Controllers\DispensaryController::class, 'printReport'])->name('print');
        Route::get('/medicine/{id}', [App\Http\Controllers\DispensaryController::class, 'getMedicineDetails'])->name('medicine-details');
    });
    
    // Category management routes
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/statistics/overview', [CategoryController::class, 'statistics'])->name('statistics');
    });
    
    // Medicine management routes
    Route::prefix('medicines')->name('medicines.')->group(function () {
        Route::get('/', [MedicineController::class, 'index'])->name('index');
        Route::post('/', [MedicineController::class, 'store'])->name('store');
        Route::get('/{medicine}', [MedicineController::class, 'show'])->name('show');
        Route::put('/{medicine}', [MedicineController::class, 'update'])->name('update');
        Route::delete('/{medicine}', [MedicineController::class, 'destroy'])->name('destroy');
        Route::patch('/{medicine}/toggle-status', [MedicineController::class, 'toggleStatus'])->name('toggle-status');
        Route::get('/statistics/overview', [MedicineController::class, 'statistics'])->name('statistics');
        Route::get('/stream', [MedicineController::class, 'stream'])->name('stream');
        
        // Optimized routes for large scale
        Route::get('/statistics', [App\Http\Controllers\OptimizedMedicineController::class, 'statistics'])->name('statistics.optimized');
        Route::get('/autocomplete', [App\Http\Controllers\OptimizedMedicineController::class, 'autocomplete'])->name('autocomplete');
        Route::get('/virtual', [App\Http\Controllers\OptimizedMedicineController::class, 'virtual'])->name('virtual');
        Route::post('/clear-cache', [App\Http\Controllers\OptimizedMedicineController::class, 'clearCache'])->name('clear-cache');
    });
    
    // Import/Export routes
    Route::prefix('import-export')->name('import-export.')->group(function () {
        Route::post('/import', [ImportExportController::class, 'import'])->name('import');
        Route::get('/export', [ImportExportController::class, 'export'])->name('export');
        Route::get('/template', [ImportExportController::class, 'downloadTemplate'])->name('template');
        Route::get('/stats', [ImportExportController::class, 'getExportStats'])->name('stats');
        Route::get('/categories', [ImportExportController::class, 'getCategories'])->name('categories');
        Route::get('/print-preview', [ImportExportController::class, 'getPrintPreview'])->name('print-preview');
        Route::get('/print', [ImportExportController::class, 'printReport'])->name('print');
    });
    
    // Analytics routes
    Route::prefix('analytics')->name('analytics.')->group(function () {
        Route::get('/business-intelligence', [App\Http\Controllers\AnalyticsController::class, 'getBusinessIntelligence'])->name('business-intelligence');
        Route::post('/clear-cache', [App\Http\Controllers\AnalyticsController::class, 'clearCache'])->name('clear-cache');
    });
    
    // Customer management routes
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/', [CustomerController::class, 'index'])->name('index');
        Route::get('/create', [CustomerController::class, 'create'])->name('create');
        Route::post('/', [CustomerController::class, 'store'])->name('store');
        Route::get('/{customer}', [CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [CustomerController::class, 'destroy'])->name('destroy');
        Route::get('/stats/overview', [CustomerController::class, 'getStats'])->name('stats');
        Route::post('/batch-add', [CustomerController::class, 'batchAdd'])->name('batch-add');
        
        // Customer filter route (POST route for handling form submissions)
        Route::post('/filter', function (Request $request) {
            // Build query parameters from POST data
            $params = [];
            
            if ($request->has('search') && $request->search) {
                $params['search'] = $request->search;
            }
            
            if ($request->has('segment') && $request->segment) {
                $params['segment'] = $request->segment;
            }
            
            if ($request->has('status') && $request->status) {
                $params['status'] = $request->status;
            }
            
            if ($request->has('sort_by') && $request->sort_by) {
                $params['sort_by'] = $request->sort_by;
            }
            
            if ($request->has('sort_direction') && $request->sort_direction) {
                $params['sort_direction'] = $request->sort_direction;
            }
            
            if ($request->has('page') && $request->page) {
                $params['page'] = $request->page;
            }
            
            // Redirect to GET route with parameters
            return redirect()->route('customers.index', $params);
        })->name('filter');
        
        // Customer Import/Export routes
        Route::post('/import', [CustomerController::class, 'import'])->name('import');
        Route::get('/export', [CustomerController::class, 'export'])->name('export');
        Route::get('/template', [CustomerController::class, 'downloadTemplate'])->name('template');
        Route::get('/export-stats', [CustomerController::class, 'getExportStats'])->name('export-stats');
        Route::get('/print-preview', [CustomerController::class, 'getPrintPreview'])->name('print-preview');
        Route::get('/print', [CustomerController::class, 'printReport'])->name('print');
    });
    
    // Purchase management routes
    Route::prefix('purchases')->name('purchases.')->group(function () {
        Route::get('/', [PurchaseController::class, 'index'])->name('index');
        Route::get('/create', [PurchaseController::class, 'create'])->name('create');
        Route::post('/', [PurchaseController::class, 'store'])->name('store');
        Route::get('/{purchase}', [PurchaseController::class, 'show'])->name('show');
        Route::put('/{purchase}', [PurchaseController::class, 'update'])->name('update');
        Route::patch('/{purchase}/status', [PurchaseController::class, 'updateStatus'])->name('update-status');
        Route::delete('/{purchase}', [PurchaseController::class, 'destroy'])->name('destroy');
        Route::get('/analytics/overview', [PurchaseController::class, 'getAnalytics'])->name('analytics');
        Route::get('/export', [PurchaseController::class, 'export'])->name('export');
        Route::get('/{purchase}/print', [PurchaseController::class, 'print'])->name('print');
        
        // API routes for frontend integration
        Route::prefix('api')->group(function () {
            Route::get('/', [PurchaseController::class, 'getPurchases'])->name('api.index');
            Route::get('/stats', [PurchaseController::class, 'getStats'])->name('api.stats');
            Route::post('/', [PurchaseController::class, 'store'])->name('api.store');
            Route::get('/{purchase}', [PurchaseController::class, 'show'])->name('api.show');
            Route::put('/{purchase}', [PurchaseController::class, 'update'])->name('api.update');
            Route::patch('/{purchase}/status', [PurchaseController::class, 'updateStatus'])->name('api.update-status');
            Route::delete('/{purchase}', [PurchaseController::class, 'destroy'])->name('api.destroy');
            Route::get('/analytics/overview', [PurchaseController::class, 'getAnalytics'])->name('api.analytics');
        });
    });
});

// Reports routes (protected with authentication and permissions)
Route::middleware(['auth', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-sales'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])->name('reports');
    
    // Reports API routes for AJAX requests
    Route::prefix('reports/api')->group(function () {
        Route::get('/sales', [ReportController::class, 'salesReports'])->name('reports.api.sales');
        Route::get('/inventory', [ReportController::class, 'inventoryReports'])->name('reports.api.inventory');
        Route::get('/financial', [ReportController::class, 'financialReports'])->name('reports.api.financial');
        Route::get('/customers', [ReportController::class, 'customerReports'])->name('reports.api.customers');
        Route::get('/purchases', [ReportController::class, 'purchaseReports'])->name('reports.api.purchases');
        Route::post('/export', [ReportController::class, 'export'])->name('reports.api.export');
        Route::post('/clear-cache', [ReportController::class, 'clearCache'])->name('reports.api.clear-cache');
    });
});

// Alerts routes (protected with authentication and permissions)
Route::middleware(['auth', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-sales'])->group(function () {
    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts');
    Route::get('/alerts/{alert}', [AlertController::class, 'show'])->name('alerts.show');
    Route::post('/alerts', [AlertController::class, 'store'])->name('alerts.store');
    Route::put('/alerts/{alert}', [AlertController::class, 'update'])->name('alerts.update');
    Route::delete('/alerts/{alert}', [AlertController::class, 'destroy'])->name('alerts.destroy');
    Route::post('/alerts/{alert}/acknowledge', [AlertController::class, 'acknowledge'])->name('alerts.acknowledge');
    Route::post('/alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');
    Route::post('/alerts/{alert}/dismiss', [AlertController::class, 'dismiss'])->name('alerts.dismiss');
    Route::post('/alerts/generate', [AlertController::class, 'generateAlerts'])->name('alerts.generate');
    Route::post('/alerts/clear-cache', [AlertController::class, 'clearCache'])->name('alerts.clear-cache');
    
    // Alerts API routes for AJAX requests
    Route::prefix('alerts/api')->group(function () {
        Route::get('/', [AlertController::class, 'getAlertsApi'])->name('alerts.api.index');
        Route::get('/statistics', [AlertController::class, 'getStatistics'])->name('alerts.api.statistics');
    });
});

// Legacy home route (redirects to dashboard)
Route::get('/home', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

// Logout route (must be POST for security)
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// User Management Routes (protected with authentication and view-users permission)
Route::middleware(['auth', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-users'])->group(function () {
    Route::resource('users', UserManagementController::class);
    Route::post('/users/bulk-action', [UserManagementController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('/users/export', [UserManagementController::class, 'export'])->name('users.export');
    Route::post('/users/import', [UserManagementController::class, 'import'])->name('users.import');
    Route::post('/users/{user}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset-password');
    Route::post('/users/{user}/toggle-lock', [UserManagementController::class, 'toggleLock'])->name('users.toggle-lock');
    Route::get('/users/stats', [UserManagementController::class, 'getStats'])->name('users.stats');
    Route::get('/users/api', [UserManagementController::class, 'api'])->name('users.api');
    
    // Role Management Routes
    Route::resource('roles', RoleManagementController::class);
    Route::get('/roles/{role}/stats', [RoleManagementController::class, 'getStats'])->name('roles.stats');
});

// Settings Routes (protected with authentication and view-settings permission)
Route::middleware(['auth', \Spatie\Permission\Middleware\PermissionMiddleware::class . ':view-settings'])->group(function () {
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/clear-cache', [SettingsController::class, 'clearCache'])->name('settings.clear-cache');
    Route::get('/settings/system-info', [SettingsController::class, 'getSystemInfo'])->name('settings.system-info');
});

// User Preferences Routes (protected with authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/user-preferences', [App\Http\Controllers\UserPreferencesController::class, 'index'])->name('user-preferences.index');
    Route::post('/user-preferences', [App\Http\Controllers\UserPreferencesController::class, 'update'])->name('user-preferences.update');
    Route::post('/user-preferences/reset', [App\Http\Controllers\UserPreferencesController::class, 'reset'])->name('user-preferences.reset');
});

// Help & Support Routes (protected with authentication)
Route::middleware(['auth'])->group(function () {
    Route::get('/help-support', [App\Http\Controllers\HelpSupportController::class, 'index'])->name('help-support.index');
    Route::get('/help-support/faq', [App\Http\Controllers\HelpSupportController::class, 'faq'])->name('help-support.faq');
    Route::get('/help-support/documentation', [App\Http\Controllers\HelpSupportController::class, 'documentation'])->name('help-support.documentation');
    Route::get('/help-support/contact', [App\Http\Controllers\HelpSupportController::class, 'contact'])->name('help-support.contact');
    Route::post('/help-support/submit', [App\Http\Controllers\HelpSupportController::class, 'submitSupport'])->name('help-support.submit');
});
