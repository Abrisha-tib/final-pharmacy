@extends('layouts.app')

@section('title', 'Reports & Analytics - Analog Pharmacy Management System')
@section('page-title', 'Reports & Analytics')
@section('page-description', 'Comprehensive business intelligence and reporting dashboard')

@section('content')
<style>
    /* Print Styles */
    @media print {
        .no-print {
            display: none !important;
        }
        
        .print-only {
            display: block !important;
        }
        
        body {
            font-size: 12px;
            line-height: 1.4;
        }
        
        .bg-gray-50, .bg-gray-100, .bg-blue-50 {
            background: white !important;
        }
        
        .shadow-lg, .shadow-xl, .shadow-2xl {
            box-shadow: none !important;
        }
        
        .border {
            border: 1px solid #333 !important;
        }
    }
    
    .print-only {
        display: none;
    }
    
    /* Reports specific styles */
    .report-card {
        transition: all 0.3s ease;
        border-left: 4px solid #10b981;
    }
    
    .report-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .metric-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1rem;
    }
    
    .metric-card.green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }
    
    .metric-card.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }
    
    .metric-card.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    
    .metric-card.orange {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }
    
    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
    }
    
    /* Sophisticated loading container styling */
    .sophisticated-loading-container {
        padding: 2rem;
        border-radius: 1rem;
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .dark .sophisticated-loading-container {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border: 1px solid #475569;
    }
    
    .tab-content {
        display: none;
    }
    
    .tab-content.active {
        display: block;
    }
    
    .tab-button {
        transition: all 0.3s ease;
    }
    
    .tab-button.active {
        background-color: #10b981;
        color: white;
    }
    
    .tab-button:hover {
        background-color: #059669;
        color: white;
    }
    
    /* Button styles */
    .btn-primary {
        background-color: #10b981;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        background-color: #059669;
    }
    
    .btn-secondary {
        background-color: #6b7280;
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        border: none;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-secondary:hover {
        background-color: #4b5563;
    }
    
    .form-input {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        font-size: 0.875rem;
        line-height: 1.25rem;
    }
</style>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Reports & Analytics</h1>
                    <p class="mt-2 text-gray-600 dark:text-gray-400">Comprehensive business intelligence dashboard</p>
                </div>
                <div class="flex space-x-3">
                    <!-- Date Range Filter -->
                    <div class="flex items-center space-x-2">
                        <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Date Range:</label>
                        <input type="date" id="startDate" class="form-input rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" value="{{ $dateRange['start_formatted'] }}">
                        <span class="text-gray-500">to</span>
                        <input type="date" id="endDate" class="form-input rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white" value="{{ $dateRange['end_formatted'] }}">
                        <button onclick="updateDateRange()" class="btn-primary">
                            <i class="fas fa-filter mr-2"></i>Apply
                        </button>
                    </div>
                    
                    <!-- Export Button -->
                    <button onclick="exportReport()" class="btn-secondary">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    
                    <!-- Refresh Button -->
                    <button onclick="refreshReports()" class="btn-primary">
                        <i class="fas fa-sync-alt mr-2"></i>Refresh
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Report Tabs -->
        <div class="mb-8">
            <div class="border-b border-gray-200 dark:border-gray-700">
                <nav class="-mb-px flex space-x-8">
                    <button class="tab-button active py-2 px-1 border-b-2 border-green-500 font-medium text-sm" data-tab="overview">
                        <i class="fas fa-chart-line mr-2"></i>Overview
                    </button>
                    <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm" data-tab="sales">
                        <i class="fas fa-shopping-cart mr-2"></i>Sales
                    </button>
                    <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm" data-tab="inventory">
                        <i class="fas fa-boxes mr-2"></i>Inventory
                    </button>
                    <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm" data-tab="financial">
                        <i class="fas fa-chart-pie mr-2"></i>Financial
                    </button>
                    <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm" data-tab="customers">
                        <i class="fas fa-users mr-2"></i>Customers
                    </button>
                    <button class="tab-button py-2 px-1 border-b-2 border-transparent font-medium text-sm" data-tab="purchases">
                        <i class="fas fa-shopping-bag mr-2"></i>Purchases
                    </button>
                </nav>
            </div>
        </div>

        <!-- Overview Tab Content -->
        <div id="overview-tab" class="tab-content active">
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="metric-card green p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Revenue</p>
                            <p class="text-3xl font-bold text-white" id="totalRevenue">
                                Br {{ number_format($reports['sales_summary']['total_revenue'] ?? 0, 2) }}
                            </p>
                        </div>
                        <div class="text-green-200">
                            <i class="fas fa-dollar-sign text-3xl"></i>
                        </div>
                    </div>
                </div>

                <div class="metric-card blue p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Sales</p>
                            <p class="text-3xl font-bold text-white" id="totalSales">
                                {{ $reports['sales_summary']['total_sales'] ?? 0 }}
                            </p>
                        </div>
                        <div class="text-blue-200">
                            <i class="fas fa-shopping-cart text-3xl"></i>
                        </div>
                    </div>
                </div>

                <div class="metric-card purple p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Avg Sale Value</p>
                            <p class="text-3xl font-bold text-white" id="avgSaleValue">
                                Br {{ number_format($reports['sales_summary']['avg_sale_amount'] ?? 0, 2) }}
                            </p>
                        </div>
                        <div class="text-purple-200">
                            <i class="fas fa-chart-bar text-3xl"></i>
                        </div>
                    </div>
                </div>

                <div class="metric-card orange p-6 rounded-xl">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Completed Sales</p>
                            <p class="text-3xl font-bold text-white" id="completedSales">
                                {{ $reports['sales_summary']['completed_sales'] ?? 0 }}
                            </p>
                        </div>
                        <div class="text-orange-200">
                            <i class="fas fa-check-circle text-3xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Daily Sales Trend -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-chart-line mr-2 text-green-500"></i>Daily Sales Trend
                    </h3>
                    <div class="chart-container">
                        <canvas id="dailySalesChart"></canvas>
                    </div>
                </div>

                <!-- Payment Method Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        <i class="fas fa-credit-card mr-2 text-blue-500"></i>Payment Methods
                    </h3>
                    <div class="chart-container">
                        <canvas id="paymentMethodChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Selling Medicines -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <i class="fas fa-star mr-2 text-yellow-500"></i>Top Selling Medicines
                </h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Medicine</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Quantity Sold</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Revenue</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sales Count</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($reports['top_medicines'] ?? [] as $medicine)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $medicine->name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $medicine->generic_name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $medicine->total_quantity }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Br {{ number_format($medicine->total_revenue, 2) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $medicine->sale_count }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">No sales data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sales Tab Content -->
        <div id="sales-tab" class="tab-content">
            <div class="text-center py-8">
                <div class="sophisticated-loading-container">
                    <div class="relative w-16 h-16 mx-auto mb-6">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-chart-bar text-blue-500 text-3xl animate-pulse"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Loading Sales Reports...</h3>
                    <div class="w-64 mx-auto mb-4">
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-full rounded-full animate-pulse" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center space-x-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inventory Tab Content -->
        <div id="inventory-tab" class="tab-content">
            <div class="text-center py-8">
                <div class="sophisticated-loading-container">
                    <div class="relative w-16 h-16 mx-auto mb-6">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-boxes text-green-500 text-3xl animate-pulse"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Loading Inventory Reports...</h3>
                    <div class="w-64 mx-auto mb-4">
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-green-400 to-green-600 h-full rounded-full animate-pulse" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center space-x-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Tab Content -->
        <div id="financial-tab" class="tab-content">
            <div class="text-center py-8">
                <div class="sophisticated-loading-container">
                    <div class="relative w-16 h-16 mx-auto mb-6">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-chart-pie text-purple-500 text-3xl animate-pulse"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Loading Financial Reports...</h3>
                    <div class="w-64 mx-auto mb-4">
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-purple-400 to-purple-600 h-full rounded-full animate-pulse" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center space-x-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-purple-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Customers Tab Content -->
        <div id="customers-tab" class="tab-content">
            <div class="text-center py-8">
                <div class="sophisticated-loading-container">
                    <div class="relative w-16 h-16 mx-auto mb-6">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-users text-orange-500 text-3xl animate-pulse"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Loading Customer Reports...</h3>
                    <div class="w-64 mx-auto mb-4">
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-orange-400 to-orange-600 h-full rounded-full animate-pulse" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center space-x-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Purchases Tab Content -->
        <div id="purchases-tab" class="tab-content">
            <div class="text-center py-8">
                <div class="sophisticated-loading-container">
                    <div class="relative w-16 h-16 mx-auto mb-6">
                        <div class="absolute inset-0 flex items-center justify-center">
                            <i class="fas fa-shopping-bag text-indigo-500 text-3xl animate-pulse"></i>
                        </div>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Loading Purchase Reports...</h3>
                    <div class="w-64 mx-auto mb-4">
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                            <div class="bg-gradient-to-r from-indigo-400 to-indigo-600 h-full rounded-full animate-pulse" style="width: 100%"></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-center space-x-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Loading...</div>
                        <div class="flex items-center space-x-1">
                            <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse"></div>
                            <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                            <div class="w-2 h-2 bg-indigo-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Global variables
let dailySalesChart = null;
let paymentMethodChart = null;

/**
 * Show sophisticated loading spinner with progress animation (exact copy from suppliers page)
 */
function showGlobalLoading(message = 'Loading...') {
    // Remove existing loading if any
    hideGlobalLoading();
    
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'globalLoading';
    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
    loadingDiv.innerHTML = `
        <div class="text-center">
            <!-- Reports icon container -->
            <div class="relative w-16 h-16 mx-auto mb-6">
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-chart-line text-blue-500 text-3xl animate-pulse"></i>
                </div>
            </div>
            
            <!-- Progress text -->
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${message}</h3>
            </div>
            
            <!-- Horizontal progress bar -->
            <div class="w-64 mx-auto mb-4">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-400 to-blue-600 h-full rounded-full transition-all duration-300 ease-out" 
                         id="progressBar" style="width: 0%"></div>
                </div>
            </div>
            
            <!-- Progress percentage and dots -->
            <div class="flex items-center justify-center space-x-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span id="loadingProgress">0</span>%
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse"></div>
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-blue-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingDiv);
    
    // Start progress animation
    startProgressAnimation();
}

/**
 * Start progress animation for horizontal loading bar (exact copy from suppliers page)
 */
function startProgressAnimation() {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('loadingProgress');
    
    if (!progressBar || !progressText) return;
    
    let progress = 0;
    const duration = 2000; // 2 seconds total
    const interval = 50; // Update every 50ms
    const increment = 100 / (duration / interval);
    
    const animation = setInterval(() => {
        progress += increment;
        
        if (progress >= 100) {
            progress = 100;
            clearInterval(animation);
        }
        
        // Update horizontal progress bar
        progressBar.style.width = progress + '%';
        
        // Update progress text
        progressText.textContent = Math.round(progress);
        
    }, interval);
    
    // Store animation ID for cleanup
    window.loadingAnimation = animation;
}

/**
 * Hide global loading spinner (exact copy from suppliers page)
 */
function hideGlobalLoading() {
    // Remove all loading overlays
    const loadingDivs = document.querySelectorAll('#globalLoading');
    loadingDivs.forEach(div => div.remove());
    
    // Also remove any loading overlays with different IDs
    const allLoadingDivs = document.querySelectorAll('[id*="loading"], .loading-overlay');
    allLoadingDivs.forEach(div => div.remove());
    
    // Clear any running animation
    if (window.loadingAnimation) {
        clearInterval(window.loadingAnimation);
        window.loadingAnimation = null;
    }
    
    // Clear any other animation intervals
    for (let i = 1; i < 99999; i++) {
        window.clearInterval(i);
    }
}

/**
 * Show loading with success/error style (exact copy from suppliers page)
 */
function showLoadingWithStyle(type, message) {
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'globalLoading';
    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
    
    const colorClass = type === 'success' ? 'text-green-500' : 
                      type === 'error' ? 'text-red-500' : 
                      'text-blue-500';
    
    const iconClass = type === 'success' ? 'fa-check-circle' : 
                     type === 'error' ? 'fa-exclamation-triangle' : 
                     'fa-chart-line';
    
    loadingDiv.innerHTML = `
        <div class="text-center">
            <!-- Reports icon container -->
            <div class="relative w-16 h-16 mx-auto mb-6">
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas ${iconClass} ${colorClass} text-3xl ${type === 'success' ? 'animate-pulse' : type === 'error' ? 'animate-bounce' : 'animate-bounce'}"></i>
                </div>
            </div>
            
            <!-- Message -->
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${message}</h3>
            </div>
            
            <!-- Horizontal progress bar -->
            <div class="w-64 mx-auto mb-4">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300 ease-out" 
                         id="progressBar" style="width: 100%; background: ${type === 'success' ? 'linear-gradient(to right, #10b981, #059669)' : type === 'error' ? 'linear-gradient(to right, #ef4444, #dc2626)' : 'linear-gradient(to right, #3b82f6, #1d4ed8)'}"></div>
                </div>
            </div>
            
            <!-- Progress percentage and dots -->
            <div class="flex items-center justify-center space-x-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span id="loadingProgress">100</span>%
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'} rounded-full animate-pulse"></div>
                    <div class="w-2 h-2 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'} rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'} rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingDiv);
    
    // Start progress animation for success/error states
    if (type === 'success' || type === 'error') {
        startProgressAnimation();
    }
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    initializeTabs();
    initializeCharts();
});

// Tab functionality
function initializeTabs() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Remove active class from all buttons and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab + '-tab').classList.add('active');
            
            // Load tab content if needed
            loadTabContent(targetTab);
        });
    });
}

// Load tab content dynamically
function loadTabContent(tabName) {
    const tabContent = document.getElementById(tabName + '-tab');
    
    // Overview tab already has data loaded, no need for AJAX
    if (tabName === 'overview') {
        tabContent.dataset.loaded = 'true';
        return;
    }
    
    if (tabContent && !tabContent.dataset.loaded) {
        // Show sophisticated loading spinner
        const loadingMessages = {
            'sales': 'Loading Sales Reports...',
            'inventory': 'Loading Inventory Reports...',
            'financial': 'Loading Financial Reports...',
            'customers': 'Loading Customer Reports...',
            'purchases': 'Loading Purchase Reports...'
        };
        
        showGlobalLoading(loadingMessages[tabName] || 'Loading Reports...');
        
        fetch(`/reports/api/${tabName}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide loading and show content immediately
                    hideGlobalLoading();
                    renderTabContent(tabName, data.data);
                    tabContent.dataset.loaded = 'true';
                } else {
                    showLoadingWithStyle('error', 'Failed to load ' + tabName + ' data');
                    setTimeout(() => {
                        hideGlobalLoading();
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Error loading tab content:', error);
                showLoadingWithStyle('error', 'Failed to load ' + tabName + ' data');
                setTimeout(() => {
                    hideGlobalLoading();
                }, 2000);
            });
    }
}

// Render tab content
function renderTabContent(tabName, data) {
    const tabContent = document.getElementById(tabName + '-tab');
    
    switch(tabName) {
        case 'sales':
            renderSalesContent(tabContent, data);
            break;
        case 'inventory':
            renderInventoryContent(tabContent, data);
            break;
        case 'financial':
            renderFinancialContent(tabContent, data);
            break;
        case 'customers':
            renderCustomerContent(tabContent, data);
            break;
        case 'purchases':
            renderPurchaseContent(tabContent, data);
            break;
    }
}

// Initialize charts
function initializeCharts() {
    // Daily Sales Chart
    const dailySalesCtx = document.getElementById('dailySalesChart');
    if (dailySalesCtx) {
        const dailyData = @json($reports['daily_trend'] ?? []);
        
        dailySalesChart = new Chart(dailySalesCtx, {
            type: 'line',
            data: {
                labels: dailyData.map(item => item.date),
                datasets: [{
                    label: 'Revenue',
                    data: dailyData.map(item => item.revenue),
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Br ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Payment Method Chart
    const paymentMethodCtx = document.getElementById('paymentMethodChart');
    if (paymentMethodCtx) {
        const paymentData = {
            'Cash': {{ $reports['sales_summary']['cash_sales'] ?? 0 }},
            'Card': {{ $reports['sales_summary']['card_sales'] ?? 0 }}
        };
        
        paymentMethodChart = new Chart(paymentMethodCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(paymentData),
                datasets: [{
                    data: Object.values(paymentData),
                    backgroundColor: [
                        '#10b981',
                        '#3b82f6',
                        '#8b5cf6',
                        '#f59e0b'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
}

// Update date range
function updateDateRange() {
    const startDate = document.getElementById('startDate').value;
    const endDate = document.getElementById('endDate').value;
    
    if (!startDate || !endDate) {
        showError('Please select both start and end dates');
        return;
    }
    
    // Reload page with new date range
    const url = new URL(window.location);
    url.searchParams.set('start_date', startDate);
    url.searchParams.set('end_date', endDate);
    window.location.href = url.toString();
}

// Export report
function exportReport() {
    const format = prompt('Select export format:\n1. PDF\n2. Excel\n3. CSV', '1');
    
    if (format) {
        const formats = { '1': 'pdf', '2': 'excel', '3': 'csv' };
        const selectedFormat = formats[format];
        
        if (selectedFormat) {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            
            // Show sophisticated loading spinner
            showGlobalLoading(`Exporting ${selectedFormat.toUpperCase()} Report...`);
            
            fetch('/reports/api/export', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    type: 'overview',
                    format: selectedFormat,
                    start_date: startDate,
                    end_date: endDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showLoadingWithStyle('success', `${selectedFormat.toUpperCase()} export completed successfully!`);
                    setTimeout(() => {
                        hideGlobalLoading();
                    }, 2000);
                } else {
                    showLoadingWithStyle('error', 'Export failed: ' + data.message);
                    setTimeout(() => {
                        hideGlobalLoading();
                    }, 2000);
                }
            })
            .catch(error => {
                console.error('Export error:', error);
                showLoadingWithStyle('error', 'Export failed');
                setTimeout(() => {
                    hideGlobalLoading();
                }, 2000);
            });
        }
    }
}

// Refresh reports
function refreshReports() {
    // Show sophisticated loading spinner
    showGlobalLoading('Refreshing Reports...');
    
    // Clear cache and reload
    fetch('/reports/api/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showLoadingWithStyle('success', 'Reports refreshed successfully!');
            setTimeout(() => {
                hideGlobalLoading();
                location.reload();
            }, 1500);
        } else {
            showLoadingWithStyle('error', 'Failed to refresh reports');
            setTimeout(() => {
                hideGlobalLoading();
            }, 2000);
        }
    })
    .catch(error => {
        console.error('Refresh error:', error);
        showLoadingWithStyle('error', 'Failed to refresh reports');
        setTimeout(() => {
            hideGlobalLoading();
        }, 2000);
    });
}

// Render sales content
function renderSalesContent(container, data) {
    container.innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Revenue by Payment Method</h4>
                <div class="space-y-4">
                    ${data.revenue_by_payment ? data.revenue_by_payment.map(item => `
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <span class="font-medium">${item.payment_method}</span>
                            <span class="text-green-600 font-bold">Br ${parseFloat(item.total_revenue).toLocaleString()}</span>
                        </div>
                    `).join('') : '<p class="text-gray-500">No data available</p>'}
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Customer Analysis</h4>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span>Unique Customers:</span>
                        <span class="font-bold">${data.customer_analysis.unique_customers}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Transactions:</span>
                        <span class="font-bold">${data.customer_analysis.total_transactions}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Avg Transaction Value:</span>
                        <span class="font-bold">Br ${parseFloat(data.customer_analysis.avg_transaction_value).toLocaleString()}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Render inventory content
function renderInventoryContent(container, data) {
    container.innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Stock Analysis</h4>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span>Total Medicines:</span>
                        <span class="font-bold">${data.stock_analysis.total_medicines}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>In Stock:</span>
                        <span class="font-bold text-green-600">${data.stock_analysis.in_stock}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Low Stock:</span>
                        <span class="font-bold text-yellow-600">${data.stock_analysis.low_stock}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Out of Stock:</span>
                        <span class="font-bold text-red-600">${data.stock_analysis.out_of_stock}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Category Performance</h4>
                <div class="space-y-3">
                    ${data.category_performance ? data.category_performance.map(category => `
                        <div class="flex justify-between items-center p-2 bg-gray-50 dark:bg-gray-700 rounded">
                            <span>${category.category_name}</span>
                            <span class="font-bold">Br ${parseFloat(category.category_value).toLocaleString()}</span>
                        </div>
                    `).join('') : '<p class="text-gray-500">No data available</p>'}
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Expiring Soon</h4>
                <div class="space-y-2">
                    ${data.expiring_medicines ? data.expiring_medicines.map(medicine => `
                        <div class="flex justify-between items-center p-2 bg-red-50 dark:bg-red-900/20 rounded">
                            <span class="text-sm">${medicine.name}</span>
                            <span class="text-sm font-bold text-red-600">${medicine.expiry_date}</span>
                        </div>
                    `).join('') : '<p class="text-gray-500">No expiring medicines</p>'}
                </div>
            </div>
        </div>
    `;
}

// Render financial content
function renderFinancialContent(container, data) {
    container.innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Revenue Analysis</h4>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span>Total Revenue:</span>
                        <span class="font-bold text-green-600">Br ${parseFloat(data.revenue_analysis.total_revenue).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Tax:</span>
                        <span class="font-bold">Br ${parseFloat(data.revenue_analysis.total_tax).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Discounts:</span>
                        <span class="font-bold text-red-600">Br ${parseFloat(data.revenue_analysis.total_discounts).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Avg Transaction:</span>
                        <span class="font-bold">Br ${parseFloat(data.revenue_analysis.avg_transaction_value).toLocaleString()}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Profit Analysis</h4>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span>Potential Revenue:</span>
                        <span class="font-bold text-green-600">Br ${parseFloat(data.profit_analysis.potential_revenue).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Cost:</span>
                        <span class="font-bold text-red-600">Br ${parseFloat(data.profit_analysis.total_cost).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Gross Profit:</span>
                        <span class="font-bold text-blue-600">Br ${parseFloat(data.profit_analysis.gross_profit_potential).toLocaleString()}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Avg Selling Price:</span>
                        <span class="font-bold">Br ${parseFloat(data.profit_analysis.avg_selling_price).toLocaleString()}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
}

// Render customer content
function renderCustomerContent(container, data) {
    container.innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Customer Statistics</h4>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span>Total Customers:</span>
                        <span class="font-bold">${data.customer_stats.total_customers}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Active Customers:</span>
                        <span class="font-bold text-green-600">${data.customer_stats.active_customers}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>VIP Customers:</span>
                        <span class="font-bold text-purple-600">${data.customer_stats.vip_customers}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Spending:</span>
                        <span class="font-bold text-blue-600">Br ${parseFloat(data.customer_stats.total_customer_spending).toLocaleString()}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Top Customers</h4>
                <div class="space-y-3">
                    ${data.top_customers ? data.top_customers.map(customer => `
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <div class="font-medium">${customer.name}</div>
                                <div class="text-sm text-gray-500">${customer.email}</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold">Br ${parseFloat(customer.total_spent).toLocaleString()}</div>
                                <div class="text-sm text-gray-500">${customer.loyalty_points} points</div>
                            </div>
                        </div>
                    `).join('') : '<p class="text-gray-500">No customer data available</p>'}
                </div>
            </div>
        </div>
    `;
}

// Render purchase content
function renderPurchaseContent(container, data) {
    container.innerHTML = `
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Purchase Summary</h4>
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <span>Total Purchases:</span>
                        <span class="font-bold">${data.purchase_summary.total_purchases}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Received:</span>
                        <span class="font-bold text-green-600">${data.purchase_summary.received_purchases}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Pending:</span>
                        <span class="font-bold text-yellow-600">${data.purchase_summary.pending_purchases}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Total Value:</span>
                        <span class="font-bold text-blue-600">Br ${parseFloat(data.purchase_summary.total_purchase_value).toLocaleString()}</span>
                    </div>
                </div>
            </div>
            
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6">
                <h4 class="text-lg font-semibold mb-4">Supplier Performance</h4>
                <div class="space-y-3">
                    ${data.supplier_performance ? data.supplier_performance.map(supplier => `
                        <div class="flex justify-between items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <div>
                                <div class="font-medium">${supplier.supplier_name}</div>
                                <div class="text-sm text-gray-500">${supplier.purchase_count} purchases</div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold">Br ${parseFloat(supplier.total_spent).toLocaleString()}</div>
                                <div class="text-sm text-gray-500">Avg: Br ${parseFloat(supplier.avg_purchase_value).toLocaleString()}</div>
                            </div>
                        </div>
                    `).join('') : '<p class="text-gray-500">No supplier data available</p>'}
                </div>
            </div>
        </div>
    `;
}

// Utility functions
function showSuccess(message) {
    // Implementation for success notifications
    alert('Success: ' + message);
}

function showError(message) {
    // Implementation for error notifications
    alert('Error: ' + message);
}
</script>
@endsection
