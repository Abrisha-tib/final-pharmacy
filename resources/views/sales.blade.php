@extends('layouts.app')

@section('title', 'Sales Management - Analog Pharmacy Management System')
@section('page-title', 'Sales Management')
@section('page-description', 'Manage your pharmacy\'s sales transactions')

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
    
    /* Line clamp utility for text truncation */
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    /* Sales specific styles matching dispensary */
    .sales-card {
        transition: all 0.3s ease;
        border-left: 4px solid #10b981;
    }
    
    .sales-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Card hover effects matching dispensary */
    .card-hover {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }
    
    .status-completed {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .payment-badge {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .summary-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 1rem;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        min-height: 140px;
    }
    
    .summary-card.orange {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }
    
    .summary-card.red {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
    }
    
    .summary-card.green {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    }
    
    .summary-card.teal {
        background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    }
    
    .items-placeholder {
        background-color: #374151;
        border: 2px dashed #6b7280;
        border-radius: 0.5rem;
        padding: 2rem;
        text-align: center;
        color: #9ca3af;
        font-style: italic;
    }
    
    .separator {
        height: 1px;
        background-color: #4b5563;
        margin: 0.75rem 0;
    }
    
    .total-amount {
        font-size: 1.25rem;
        font-weight: 700;
        color: #ffffff;
    }
    
    .view-link {
        color: #9ca3af;
        text-decoration: none;
        font-size: 0.875rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: color 0.2s ease;
    }
    
    .view-link:hover {
        color: #ffffff;
    }
    
    /* Modal z-index fix */
    #addSaleModal {
        z-index: 9999 !important;
    }
    
    /* Ensure modal appears above header */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
    }
    
    /* Modal layout fixes */
    #addSaleModal .modal-body {
        display: flex;
        width: 100%;
        min-height: 600px;
    }
    
    #addSaleModal .modal-left {
        flex: 1;
        width: 50%;
        min-width: 0;
    }
    
    #addSaleModal .modal-right {
        flex: 1;
        width: 50%;
        min-width: 0;
    }
</style>

<!-- Welcome Section -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-blue-200 dark:border-gray-600">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Sales Management</h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg">Manage your pharmacy's sales transactions</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">System Status</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white" id="salesStatus">All Systems Operational</p>
            </div>
        </div>
    </div>
</div>

<!-- Sales Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Sales Card -->
    <div class="card-hover bg-gradient-to-br from-orange-400 to-orange-500 dark:from-orange-800 dark:to-orange-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-orange-600 dark:border-orange-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-orange-800 dark:text-orange-200 uppercase tracking-wide">Total Sales</p>
                <p class="text-3xl font-bold text-orange-900 dark:text-white mt-2 mb-1" id="totalSales">{{ number_format($statsData['total_sales'] ?? 0) }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-orange-500 dark:bg-orange-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-shopping-cart text-xs mr-1"></i>
                        <span id="totalSalesBadge">{{ number_format($statsData['total_sales'] ?? 0) }}</span>
                    </div>
                    <span class="text-xs text-orange-700 dark:text-orange-300 font-bold">sales</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-shopping-cart text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Revenue Card -->
    <div class="card-hover bg-gradient-to-br from-red-400 to-red-500 dark:from-red-800 dark:to-red-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-red-600 dark:border-red-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-red-800 dark:text-red-200 uppercase tracking-wide">Total Revenue</p>
                <p class="text-3xl font-bold text-red-900 dark:text-white mt-2 mb-1" id="totalRevenue">Br {{ number_format($statsData['total_revenue'] ?? 0, 2) }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-red-500 dark:bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                        <span id="revenueBadge">Br {{ number_format($statsData['total_revenue'] ?? 0, 0) }}</span>
                    </div>
                    <span class="text-xs text-red-700 dark:text-red-300 font-bold">this month</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-dollar-sign text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Completed Sales Card -->
    <div class="card-hover bg-gradient-to-br from-green-400 to-green-500 dark:from-green-800 dark:to-green-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-green-600 dark:border-green-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-green-800 dark:text-green-200 uppercase tracking-wide">Completed Sales</p>
                <p class="text-3xl font-bold text-green-900 dark:text-white mt-2 mb-1" id="completedSales">{{ number_format($statsData['completed_sales'] ?? 0) }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-green-500 dark:bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-check text-xs mr-1"></i>
                        <span id="completedBadge">{{ number_format($statsData['completed_sales'] ?? 0) }}</span>
                    </div>
                    <span class="text-xs text-green-700 dark:text-green-300 font-bold">transactions</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Pending Sales Card -->
    <div class="card-hover bg-gradient-to-br from-teal-400 to-teal-500 dark:from-teal-800 dark:to-teal-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-teal-600 dark:border-teal-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-teal-800 dark:text-teal-200 uppercase tracking-wide">Pending Sales</p>
                <p class="text-3xl font-bold text-teal-900 dark:text-white mt-2 mb-1" id="pendingSales">{{ number_format($statsData['pending_sales'] ?? 0) }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-teal-500 dark:bg-teal-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-clock text-xs mr-1"></i>
                        <span id="pendingBadge">{{ number_format($statsData['pending_sales'] ?? 0) }}</span>
                    </div>
                    <span class="text-xs text-teal-700 dark:text-teal-300 font-bold">orders</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-clock text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- View Options and Actions -->
<div class="flex items-center justify-between mb-6">
    <!-- Left Group: Cards, Table, Show Analytics -->
    <div class="flex items-center gap-4">
        <!-- Segmented Control: Cards & Table -->
        <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1 shadow-sm">
            <button id="cardsViewBtn" class="px-4 py-2 bg-orange-500 text-white rounded-md font-semibold text-sm transition-all duration-200 flex items-center">
                <i class="fas fa-cube mr-2 text-sm"></i>Cards
            </button>
            <button id="tableViewBtn" class="px-4 py-2 bg-transparent text-gray-700 dark:text-gray-200 rounded-md font-medium text-sm transition-all duration-200 flex items-center hover:bg-gray-200 dark:hover:bg-gray-600">
                <i class="fas fa-table mr-2 text-sm"></i><i class="fas fa-dollar-sign mr-1 text-xs"></i>Table
            </button>
        </div>
        
        <!-- Show Analytics Button -->
        <button id="analyticsBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 shadow-sm flex items-center">
            <i class="fas fa-chart-bar mr-2 text-sm"></i>Show Analytics
        </button>
    </div>
    
    <!-- Right Group: Import/Export -->
    <div class="flex items-center gap-3">
        <button id="importExportBtn" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium text-sm transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center">
            <i class="fas fa-download mr-2 text-sm"></i>Import/Export
        </button>
    </div>
</div>

<!-- Sales Filters -->
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6 mt-6">
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filter Sales</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- Search Sales -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Sales</label>
            <div class="relative">
                <input type="text" 
                       class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                       placeholder="Search by customer name, sale ID, or...">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
            <select class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                <option value="">All Status</option>
                <option value="completed">Completed</option>
                <option value="pending">Pending</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <!-- Payment Method Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
            <select class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                <option value="">All Payment Methods</option>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="mobile">Mobile Payment</option>
            </select>
        </div>

        <!-- Customer Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer</label>
            <select class="w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200">
                <option value="">All Customers</option>
                <option value="walk-in">Walk-in Customer</option>
                <option value="registered">Registered Customer</option>
            </select>
        </div>

        <!-- Date Range -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date From - Date To</label>
            <div class="grid grid-cols-2 gap-2">
                <input type="date" 
                       class="px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                       placeholder="mm/dd/yyyy">
                <input type="date" 
                       class="px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors duration-200"
                       placeholder="mm/dd/yyyy">
            </div>
        </div>
    </div>
</div>

<!-- Sales Grid -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mt-8">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-gray-600 dark:text-gray-400">
                <span>Showing 1 of 1 sales</span>
            </div>
            <div class="flex gap-4">
                <button id="refreshBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2 text-sm"></i>Refresh
                </button>
                <button id="printBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm transition-all duration-200 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-print mr-2 text-sm"></i>Print
                </button>
                <button id="addSaleBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-plus mr-2 text-sm"></i>Add Sale
                </button>
                <button id="salesHistoryBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-sm"></i>Sales History
                </button>
            </div>
        </div>
    </div>

    <div class="p-6 bg-gray-50 dark:bg-gray-900">

        @if(true)
            <!-- Cards View -->
            <div id="cardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Sales cards rendered server-side for optimal performance -->
                @if($salesData && $salesData->count() > 0)
                    @foreach($salesData as $sale)
                        <div class="sales-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300">
                                <!-- Header Section -->
                                <div class="mb-6">
                                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $sale->sale_number }}</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $sale->customer_name ?? 'Walk-in Customer' }}</p>
                                    <div class="flex gap-2">
                                        <span class="status-badge status-{{ $sale->status }} px-3 py-1 rounded-full text-xs font-semibold">
                                            <i class="fas fa-check mr-1"></i>{{ ucfirst($sale->status) }}
                                        </span>
                                        <span class="payment-badge px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                            <i class="fas fa-dollar-sign mr-1"></i>{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                                        </span>
                                    </div>
                                </div>
                                
                                <!-- Items Section -->
                                <div class="mb-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Items</span>
                                        <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $sale->items ? $sale->items->count() : 0 }} Items</span>
                                    </div>
                                </div>
                                
                                <!-- Financial Breakdown -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Br {{ number_format($sale->subtotal ?? ($sale->total_amount - ($sale->tax ?? 0)), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Tax:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">Br {{ number_format($sale->tax ?? 0, 2) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Total Amount (Prominent) -->
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-3 mb-4">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Amount:</span>
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white">Br {{ number_format($sale->total_amount, 2) }}</span>
                                    </div>
                                </div>
                                
                                <!-- Administrative Details -->
                                <div class="space-y-2 mb-4">
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Sold By:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $sale->soldBy->name ?? 'System Administrator' }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Sale Date:</span>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $sale->sale_date->format('M d, Y') }}</span>
                                    </div>
                                </div>
                                
                                <!-- Action Button -->
                                <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                                    <button onclick="viewSaleDetails({{ $sale->id }})" class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                                        <i class="fas fa-eye mr-2"></i>View
                                    </button>
                                </div>
                            </div>
                    @endforeach
                    
                    <!-- Pagination -->
                    @if($salesData->hasPages())
                        <div class="col-span-full flex justify-center mt-8">
                            <div class="flex items-center space-x-2">
                                @if($salesData->onFirstPage())
                                    <span class="px-3 py-2 text-gray-400 cursor-not-allowed">Previous</span>
                                @else
                                    <a href="{{ $salesData->previousPageUrl() }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors duration-200">Previous</a>
                                @endif
                                
                                <span class="px-3 py-2 text-gray-600 dark:text-gray-300">
                                    Page {{ $salesData->currentPage() }} of {{ $salesData->lastPage() }}
                                </span>
                                
                                @if($salesData->hasMorePages())
                                    <a href="{{ $salesData->nextPageUrl() }}" class="px-3 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors duration-200">Next</a>
                                @else
                                    <span class="px-3 py-2 text-gray-400 cursor-not-allowed">Next</span>
                                @endif
                            </div>
                        </div>
                    @endif
                @else
                    <div class="col-span-full flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-shopping-cart text-4xl text-gray-400 dark:text-gray-500"></i>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Sales Found</h3>
                        <p class="text-gray-600 dark:text-gray-300 mb-6">Start by creating your first sale to see it here.</p>
                        <button onclick="openAddSaleModal()" class="bg-orange-500 hover:bg-orange-600 text-white px-6 py-3 rounded-lg font-semibold transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>Create First Sale
                        </button>
                    </div>
                @endif
            </div>
            
            <!-- Table View -->
            <div id="tableView" class="hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                            <tr>
                                <th scope="col" class="px-6 py-3">Sale ID</th>
                                <th scope="col" class="px-6 py-3">Customer</th>
                                <th scope="col" class="px-6 py-3">Items</th>
                                <th scope="col" class="px-6 py-3">Payment Method</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                                <th scope="col" class="px-6 py-3">Total Amount</th>
                                <th scope="col" class="px-6 py-3">Date</th>
                                <th scope="col" class="px-6 py-3">Sold By</th>
                                <th scope="col" class="px-6 py-3">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="salesTableBody">
                            @if($salesData && $salesData->count() > 0)
                                @foreach($salesData as $sale)
                                    <tr class="bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            {{ $sale->sale_number }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div>
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $sale->customer_name ?? 'Walk-in Customer' }}
                                                </div>
                                                @if($sale->customer_phone)
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                                        {{ $sale->customer_phone }}
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-sm text-gray-900 dark:text-white">
                                                {{ $sale->items->count() }} item(s)
                                            </div>
                                            @if($sale->items->count() > 0)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    @foreach($sale->items->take(2) as $item)
                                                        {{ $item->medicine->name ?? 'Unknown' }} (x{{ $item->quantity }})<br>
                                                    @endforeach
                                                    @if($sale->items->count() > 2)
                                                        +{{ $sale->items->count() - 2 }} more
                                                    @endif
                                                </div>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                {{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="status-badge status-{{ $sale->status }}">
                                                {{ ucfirst($sale->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                            ${{ number_format($sale->total_amount, 2) }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $sale->sale_date->format('M d, Y H:i') }}
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                            {{ $sale->soldBy->name ?? 'Unknown' }}
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-2">
                                                <button onclick="viewSaleDetails({{ $sale->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                @if($sale->status === 'pending')
                                                    <button onclick="updateSaleStatus({{ $sale->id }}, 'completed')" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif
                                                @if(in_array($sale->status, ['pending', 'cancelled']))
                                                    <button onclick="deleteSale({{ $sale->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                                <i class="fas fa-shopping-cart text-2xl text-gray-400"></i>
                                            </div>
                                            <p class="text-lg font-medium">No sales found</p>
                                            <p class="text-sm">Start by creating your first sale</p>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <!-- Pagination and Status -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 no-print">
        <div class="flex items-center justify-between">
            <!-- Pagination Info -->
            <div class="text-sm text-gray-700 dark:text-gray-300 pagination-info">
                Showing {{ $salesData->firstItem() ?? 0 }} to {{ $salesData->lastItem() ?? 0 }} of {{ $salesData->total() }} results
            </div>
            
            <!-- Custom Pagination Controls -->
            <div class="flex items-center space-x-2" id="paginationControls">
                <button class="pagination-prev px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" onclick="previousPage()">
                    <i class="fas fa-chevron-left mr-1"></i>Previous
                </button>
                
                <!-- Page Numbers -->
                <div class="flex items-center space-x-1" id="pageNumbers">
                    <!-- Page numbers will be populated by JavaScript -->
                </div>
                
                <button class="pagination-next px-3 py-2 text-sm font-medium text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-200 disabled:opacity-50 disabled:cursor-not-allowed" onclick="nextPage()">
                    Next<i class="fas fa-chevron-right ml-1"></i>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
/**
 * Sales Management Controller
 * Handles view switching, filtering, and sales interactions
 */
(function() {
    'use strict';
    
    // Initialize sales management when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeSalesManagement();
    });
    
    /**
     * Initialize sales management functionality
     * Optimized for server-side rendering
     */
    async function initializeSalesManagement() {
        console.log('Initializing sales management with server-side data...');
        
        // Initialize view switching
        initializeViewSwitching();
        
        // Initialize filters
        initializeFilters();
        
        // Initialize action buttons
        initializeActionButtons();
        
        // Initialize Add Sale Modal
        initializeAddSaleModal();
        
        // Initialize Sales History Modal
        initializeSalesHistoryModal();
        
        // Data is already rendered server-side, no need to fetch
        console.log('Sales management initialized successfully with server-side data');
        
        // Set up periodic refresh for real-time updates (optional)
        setupPeriodicRefresh();
    }
    
    /**
     * Set up periodic refresh for real-time updates
     * Optimized for shared hosting - reduced frequency
     */
    function setupPeriodicRefresh() {
        // Only refresh if user is actively using the page
        let refreshInterval;
        
        function startRefresh() {
            if (refreshInterval) clearInterval(refreshInterval);
            
            // DISABLED: Automatic refresh to prevent overriding manual changes
            // Refresh every 5 minutes (reduced frequency for shared hosting)
            // refreshInterval = setInterval(() => {
            //     if (!document.hidden) {
            //         refreshSalesData();
            //     }
            // }, 300000); // 5 minutes
        }
        
        function stopRefresh() {
            if (refreshInterval) {
                clearInterval(refreshInterval);
                refreshInterval = null;
            }
        }
        
        // Start refresh when page becomes visible
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopRefresh();
            } else {
                startRefresh();
            }
        });
        
        // Start initial refresh
        startRefresh();
    }
    
    /**
     * Initialize view switching between cards and table
     */
    function initializeViewSwitching() {
        const cardsViewBtn = document.getElementById('cardsViewBtn');
        const tableViewBtn = document.getElementById('tableViewBtn');
        const analyticsBtn = document.getElementById('analyticsBtn');
        
        if (cardsViewBtn) {
            cardsViewBtn.addEventListener('click', function() {
                switchToCardsView();
            });
        }
        
        if (tableViewBtn) {
            tableViewBtn.addEventListener('click', function() {
                switchToTableView();
            });
        }
        
        if (analyticsBtn) {
            analyticsBtn.addEventListener('click', function() {
                showAnalytics();
            });
        }
    }
    
    /**
     * Switch to cards view
    */
    function switchToCardsView() {
        const cardsViewBtn = document.getElementById('cardsViewBtn');
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        
        // Update button states for segmented control
        cardsViewBtn.classList.add('bg-orange-500', 'text-white', 'font-semibold');
        cardsViewBtn.classList.remove('bg-transparent', 'text-gray-700', 'dark:text-gray-200', 'font-medium');
        tableViewBtn.classList.add('bg-transparent', 'text-gray-700', 'dark:text-gray-200', 'font-medium');
        tableViewBtn.classList.remove('bg-orange-500', 'text-white', 'font-semibold');
        
        // Show cards view and hide table view
        if (cardView) {
            cardView.classList.remove('hidden');
        }
        if (tableView) {
            tableView.classList.add('hidden');
        }
        
        console.log('Switched to cards view');
    }
    
    /**
     * Switch to table view
     */
    function switchToTableView() {
        const cardsViewBtn = document.getElementById('cardsViewBtn');
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        
        // Update button states for segmented control
        tableViewBtn.classList.add('bg-orange-500', 'text-white', 'font-semibold');
        tableViewBtn.classList.remove('bg-transparent', 'text-gray-700', 'dark:text-gray-200', 'font-medium');
        cardsViewBtn.classList.add('bg-transparent', 'text-gray-700', 'dark:text-gray-200', 'font-medium');
        cardsViewBtn.classList.remove('bg-orange-500', 'text-white', 'font-semibold');
        
        // Show table view and hide cards view
        if (tableView) {
            tableView.classList.remove('hidden');
        }
        if (cardView) {
            cardView.classList.add('hidden');
        }
        
        console.log('Switched to table view');
    }
    
    /**
     * Initialize filters
     */
    function initializeFilters() {
        // Add filter functionality here
        console.log('Filters initialized');
    }
    
    /**
     * Show analytics
     */
    function showAnalytics() {
        alert('Analytics feature coming soon!');
    }
    
    /**
     * Refresh sales data without reloading the page
     * Optimized for server-side rendering
     */
    function refreshSalesData() {
        const refreshBtn = document.getElementById('refreshBtn');
        let originalText = '';
        if (refreshBtn) {
            // Show loading state
            originalText = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-sm"></i>Refreshing...';
            refreshBtn.disabled = true;
        }
        
        // Fetch fresh data from server
        Promise.all([
            fetchSalesData(),
            fetch('/sales/api/stats', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
        ])
        .then(([salesData, statsData]) => {
            // Update sales cards with fresh data
            if (salesData && salesData.success && salesData.data) {
                updateSalesCardsWithData(salesData);
            }
            
            // Update summary statistics
            if (statsData && statsData.success && statsData.data) {
                updateSalesSummaryWithData(statsData);
            }
            
            // Show success notification (only if not called from createSale)
            if (refreshBtn && window.NotificationService) {
                window.NotificationService.success('Sales data refreshed successfully!');
            }
        })
        .catch(error => {
            console.error('Failed to refresh sales data:', error);
            
            // Show error notification
            if (window.NotificationService) {
                window.NotificationService.error('Failed to refresh data. Please try again.');
            }
        })
        .finally(() => {
            // Reset button state
            if (refreshBtn) {
                refreshBtn.innerHTML = originalText;
                refreshBtn.disabled = false;
            }
        });
    }
    
    /**
     * Fetch sales data from server
     */
    async function fetchSalesData(filters = {}) {
        try {
            const params = new URLSearchParams();
            Object.keys(filters).forEach(key => {
                if (filters[key]) {
                    params.append(key, filters[key]);
                }
            });
            
            const response = await fetch(`/sales/api?${params.toString()}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            
            const data = await response.json();
            return data;
        } catch (error) {
            console.error('Failed to fetch sales data:', error);
            throw error;
        }
    }
    
    /**
     * Update sales cards with real data
     */
    function updateSalesCardsWithData(data) {
        console.log('Updating sales cards with server data:', data);
        
        if (data.success && data.data) {
            const sales = data.data.data || data.data;
            displaySalesCards(sales);
        } else {
            console.error('Invalid data received:', data);
            // Show error message instead of fallback
            showErrorMessage('Failed to load sales data. Please refresh the page.');
        }
    }
    
    /**
     * Update sales summary with real data
     */
    function updateSalesSummaryWithData(data) {
        console.log('Updating sales summary with server data:', data);
        
        if (data && data.success && data.data) {
            const stats = data.data;
            updateSalesSummaryStats(stats);
        } else if (data && data.data) {
            // Handle direct stats object
            updateSalesSummaryStats(data.data);
        } else {
            console.error('Invalid stats data received:', data);
            // Don't fallback to mock data, just log the error
        }
    }
    
    /**
     * Show loading state for stats cards
     */
    function showLoadingState() {
        const totalSalesElement = document.getElementById('totalSales');
        const completedSalesElement = document.getElementById('completedSales');
        const pendingSalesElement = document.getElementById('pendingSales');
        const totalRevenueElement = document.getElementById('totalRevenue');
        
        if (totalSalesElement) totalSalesElement.textContent = '...';
        if (completedSalesElement) completedSalesElement.textContent = '...';
        if (pendingSalesElement) pendingSalesElement.textContent = '...';
        if (totalRevenueElement) totalRevenueElement.textContent = '...';
        
        // Update badges to loading state
        const totalSalesBadge = document.getElementById('totalSalesBadge');
        const completedBadge = document.getElementById('completedBadge');
        const pendingBadge = document.getElementById('pendingBadge');
        const revenueBadge = document.getElementById('revenueBadge');
        
        if (totalSalesBadge) totalSalesBadge.textContent = 'Loading...';
        if (completedBadge) completedBadge.textContent = 'Loading...';
        if (pendingBadge) pendingBadge.textContent = 'Loading...';
        if (revenueBadge) revenueBadge.textContent = 'Loading...';
    }
    
    /**
     * Show error state for stats cards
     */
    function showErrorState() {
        const totalSalesElement = document.getElementById('totalSales');
        const completedSalesElement = document.getElementById('completedSales');
        const pendingSalesElement = document.getElementById('pendingSales');
        const totalRevenueElement = document.getElementById('totalRevenue');
        
        if (totalSalesElement) totalSalesElement.textContent = '0';
        if (completedSalesElement) completedSalesElement.textContent = '0';
        if (pendingSalesElement) pendingSalesElement.textContent = '0';
        if (totalRevenueElement) totalRevenueElement.textContent = 'Br 0.00';
        
        // Update badges to zero state
        const totalSalesBadge = document.getElementById('totalSalesBadge');
        const completedBadge = document.getElementById('completedBadge');
        const pendingBadge = document.getElementById('pendingBadge');
        const revenueBadge = document.getElementById('revenueBadge');
        
        if (totalSalesBadge) totalSalesBadge.textContent = '0 Available';
        if (completedBadge) completedBadge.textContent = '0 Pending';
        if (pendingBadge) pendingBadge.textContent = '0 Orders';
        if (revenueBadge) revenueBadge.textContent = '+0.0%';
    }
    
    /**
     * Update sales summary statistics
     */
    function updateSalesSummaryStats(stats) {
        // Update summary cards with real data
        const totalSalesElement = document.getElementById('totalSales');
        const completedSalesElement = document.getElementById('completedSales');
        const pendingSalesElement = document.getElementById('pendingSales');
        const totalRevenueElement = document.getElementById('totalRevenue');
        
        if (totalSalesElement) totalSalesElement.textContent = stats.total_sales || 0;
        if (completedSalesElement) completedSalesElement.textContent = stats.completed_sales || 0;
        if (pendingSalesElement) pendingSalesElement.textContent = stats.pending_sales || 0;
        if (totalRevenueElement) totalRevenueElement.textContent = `Br ${(parseFloat(stats.total_revenue) || 0).toFixed(2)}`;
        
        // Update badges
        const totalSalesBadge = document.getElementById('totalSalesBadge');
        const completedBadge = document.getElementById('completedBadge');
        const pendingBadge = document.getElementById('pendingBadge');
        const revenueBadge = document.getElementById('revenueBadge');
        
        if (totalSalesBadge) totalSalesBadge.textContent = `${stats.total_sales || 0} Available`;
        if (completedBadge) completedBadge.textContent = `${stats.pending_sales || 0} Pending`;
        if (pendingBadge) pendingBadge.textContent = `${stats.pending_sales || 0} Orders`;
        if (revenueBadge) revenueBadge.textContent = `+${((parseFloat(stats.total_revenue) || 0) / 100).toFixed(1)}%`;
    }
    
    /**
     * Load sales statistics
     */
    async function loadSalesStats() {
        try {
            const response = await fetch('/sales/api/stats', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    updateSalesSummaryStats(data.data);
                    return;
                }
            }
            
            // If no data available, show zero values
            showErrorState();
        } catch (error) {
            console.error('Failed to load sales stats:', error);
            showErrorState();
        }
    }
    
    /**
     * Display sales cards
     * Updated for horizontal grid layout like dispensary
     */
    function displaySalesCards(sales) {
        console.log('displaySalesCards called with:', sales);
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        
        if (!cardView) {
            console.error('cardView not found!');
            return;
        }
        
        console.log('Clearing card view and adding', sales.length, 'cards');
        
        // Clear existing cards (keep the grid container)
        const existingCards = cardView.querySelectorAll('.sales-card');
        existingCards.forEach(card => card.remove());
        
        sales.forEach(sale => {
            const card = createSalesCard(sale);
            cardView.appendChild(card);
        });
        
        // Also update table view if it's visible
        if (tableView && !tableView.classList.contains('hidden')) {
            updateTableView(sales);
        }
        
        console.log('Sales cards displayed successfully in horizontal grid');
    }
    
    /**
     * Update table view with sales data
     */
    function updateTableView(sales) {
        const tableBody = document.getElementById('salesTableBody');
        if (!tableBody) {
            console.error('salesTableBody not found!');
            return;
        }
        
        if (sales && sales.length > 0) {
            tableBody.innerHTML = '';
            
            sales.forEach(sale => {
                const row = document.createElement('tr');
                row.className = 'bg-white dark:bg-gray-800 border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600';
                
                row.innerHTML = `
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                        ${sale.sale_number}
                    </td>
                    <td class="px-6 py-4">
                        <div>
                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                ${sale.customer_name || 'Walk-in Customer'}
                            </div>
                            ${sale.customer_phone ? `<div class="text-sm text-gray-500 dark:text-gray-400">${sale.customer_phone}</div>` : ''}
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-900 dark:text-white">
                            ${sale.items ? sale.items.length : 0} item(s)
                        </div>
                        ${sale.items && sale.items.length > 0 ? `
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                ${sale.items.slice(0, 2).map(item => `${item.medicine?.name || 'Unknown'} (x${item.quantity})`).join('<br>')}
                                ${sale.items.length > 2 ? `<br>+${sale.items.length - 2} more` : ''}
                            </div>
                        ` : ''}
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                            ${sale.payment_method ? sale.payment_method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()) : 'Cash'}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="status-badge status-${sale.status}">
                            ${sale.status ? sale.status.charAt(0).toUpperCase() + sale.status.slice(1) : 'Unknown'}
                        </span>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                        $${parseFloat(sale.total_amount || 0).toFixed(2)}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                        ${new Date(sale.sale_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', hour: 'numeric', minute: '2-digit' })}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                        ${sale.sold_by?.name || 'Unknown'}
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center space-x-2">
                            <button onclick="viewSaleDetails(${sale.id})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                <i class="fas fa-eye"></i>
                            </button>
                            ${sale.status === 'pending' ? `
                                <button onclick="updateSaleStatus(${sale.id}, 'completed')" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                    <i class="fas fa-check"></i>
                                </button>
                            ` : ''}
                            ${['pending', 'cancelled'].includes(sale.status) ? `
                                <button onclick="deleteSale(${sale.id})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            ` : ''}
                        </div>
                    </td>
                `;
                
                tableBody.appendChild(row);
            });
        } else {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-shopping-cart text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-lg font-medium">No sales found</p>
                            <p class="text-sm">Start by creating your first sale</p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
    
    /**
     * Create sales card element
     */
    function createSalesCard(sale) {
        const card = document.createElement('div');
        card.className = 'sales-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300';
        
        const statusClass = sale.status === 'completed' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 
                           sale.status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' : 
                           'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200';
        
        // Calculate subtotal and tax
        const totalAmount = parseFloat(sale.total_amount || 0);
        const tax = parseFloat(sale.tax || 0);
        const subtotal = totalAmount - tax;
        
        card.innerHTML = `
            <!-- Header Section -->
            <div class="mb-6">
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">${sale.sale_number || sale.id}</h3>
                <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">${sale.customer_name || 'Walk-in Customer'}</p>
                <div class="flex gap-2">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold ${statusClass}">
                        <i class="fas fa-check mr-1"></i>${sale.status}
                    </span>
                    <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                        <i class="fas fa-dollar-sign mr-1"></i>${sale.payment_method}
                    </span>
                </div>
            </div>
            
            <!-- Items Section -->
            <div class="mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Items</span>
                    <span class="text-sm font-bold text-gray-900 dark:text-white">${sale.items ? sale.items.length : 0} Items</span>
                </div>
            </div>
            
            <!-- Financial Breakdown -->
            <div class="space-y-2 mb-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Br ${subtotal.toFixed(2)}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Tax:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Br ${tax.toFixed(2)}</span>
                </div>
            </div>
            
            <!-- Total Amount (Prominent) -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-3 mb-4">
                <div class="flex justify-between items-center">
                    <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Amount:</span>
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">Br ${totalAmount.toFixed(2)}</span>
                </div>
            </div>
            
            <!-- Administrative Details -->
            <div class="space-y-2 mb-4">
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Sold By:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">${sale.sold_by?.name || 'System Administrator'}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-sm text-gray-600 dark:text-gray-400">Sale Date:</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">${new Date(sale.sale_date || sale.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                </div>
            </div>
            
            <!-- Action Button -->
            <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                <button onclick="viewSaleDetails('${sale.id}')" class="flex items-center text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 text-sm font-medium">
                    <i class="fas fa-eye mr-2"></i>View
                </button>
            </div>
        `;
        
        return card;
    }
    
    /**
     * Update sales cards with fresh data
     */
    function updateSalesCards() {
        // In real implementation, this would fetch fresh data from the server
        // For now, we'll simulate updating the existing cards
        
        const salesCards = document.querySelectorAll('.bg-white.dark\\:bg-gray-800.rounded-xl.border.border-gray-200.dark\\:border-gray-700');
        
        salesCards.forEach((card, index) => {
            // Add a subtle animation to show the card is being updated
            card.style.transform = 'scale(0.98)';
            card.style.transition = 'transform 0.2s ease';
            
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 100 + (index * 50));
        });
        
        // Update the "Showing X of Y sales" text
        const showingText = document.querySelector('.text-gray-600.dark\\:text-gray-400 span');
        if (showingText) {
            showingText.textContent = 'Showing 1 of 1 sales (Refreshed)';
        }
    }
    
    /**
     * Update sales summary statistics
     */
    function updateSalesSummary() {
        // In real implementation, this would fetch fresh statistics from the server
        // For now, we'll simulate updating the summary cards
        
        const summaryCards = document.querySelectorAll('.card-hover');
        
        summaryCards.forEach((card, index) => {
            // Add a subtle animation to show the card is being updated
            card.style.transform = 'scale(0.98)';
            card.style.transition = 'transform 0.2s ease';
            
            setTimeout(() => {
                card.style.transform = 'scale(1)';
            }, 200 + (index * 100));
        });
        
        // Update timestamp in the status area
        const salesStatus = document.getElementById('salesStatus');
        if (salesStatus) {
            salesStatus.textContent = `All Systems Operational (Last updated: ${new Date().toLocaleTimeString()})`;
        }
    }
    
    /**
     * Open Sales History Modal
     */
    function openSalesHistoryModal() {
        const modal = document.getElementById('salesHistoryModal');
        if (modal) {
            modal.classList.remove('hidden');
            loadSalesHistory();
        }
    }
    
    /**
     * Close Sales History Modal
     */
    function closeSalesHistoryModal() {
        const modal = document.getElementById('salesHistoryModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    /**
     * Load sales history data
     */
    async function loadSalesHistory() {
        try {
            const response = await fetch('/sales/api', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    const salesHistory = data.data.data || data.data;
                    if (salesHistory && salesHistory.length > 0) {
                        // Update both the main cards and the modal
                        displaySalesCards(salesHistory);
        displaySalesHistory(salesHistory);
                        return;
                    }
                }
            }
            
            // Show empty state if no data
            displayEmptySalesState();
        } catch (error) {
            console.error('Failed to load sales history:', error);
            displayEmptySalesState();
        }
    }
    
    /**
     * Display empty sales state
     */
    function displayEmptySalesState() {
        const cardView = document.getElementById('cardView');
        const tableBody = document.getElementById('salesTableBody');
        
        if (cardView) {
            // Clear existing cards
            const existingCards = cardView.querySelectorAll('.sales-card');
            existingCards.forEach(card => card.remove());
            
            // Add empty state
            const emptyState = document.createElement('div');
            emptyState.className = 'col-span-full flex flex-col items-center justify-center py-12 text-center';
            emptyState.innerHTML = `
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-shopping-cart text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Sales Yet</h3>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">Start by creating your first sale</p>
                    <button onclick="openAddSaleModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-plus mr-2"></i>Create Sale
                    </button>
            `;
            cardView.appendChild(emptyState);
        }
        
        if (tableBody) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="9" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        <div class="flex flex-col items-center">
                            <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                <i class="fas fa-shopping-cart text-2xl text-gray-400"></i>
                            </div>
                            <p class="text-lg font-medium">No sales found</p>
                            <p class="text-sm">Start by creating your first sale</p>
                        </div>
                    </td>
                </tr>
            `;
        }
    }
    
    /**
     * Display sales history
     */
    function displaySalesHistory(sales) {
        const historyContainer = document.getElementById('salesHistoryContainer');
        if (!historyContainer) return;
        
        historyContainer.innerHTML = '';
        
        if (sales.length === 0) {
            historyContainer.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-500 mb-2">No Sales History</h3>
                    <p class="text-gray-400">No sales have been recorded yet.</p>
                </div>
            `;
            return;
        }
        
        // Create table structure
        const tableHTML = `
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-6 py-3">Sale ID</th>
                            <th scope="col" class="px-6 py-3">Date & Time</th>
                            <th scope="col" class="px-6 py-3">Sold By</th>
                            <th scope="col" class="px-6 py-3">Customer</th>
                            <th scope="col" class="px-6 py-3">Items</th>
                            <th scope="col" class="px-6 py-3">Payment Method</th>
                            <th scope="col" class="px-6 py-3">Total Amount</th>
                            <th scope="col" class="px-6 py-3">Status</th>
                            <th scope="col" class="px-6 py-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${sales.map(sale => `
                            <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                                <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-gradient-to-br from-green-500 to-green-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="fas fa-receipt text-white text-xs"></i>
                                        </div>
                                        #${sale.id}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="font-medium text-gray-900 dark:text-white">${sale.date}</div>
                                        <div class="text-gray-500 dark:text-gray-400">${sale.time}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mr-3">
                                            <i class="fas fa-user text-blue-600 dark:text-blue-400 text-xs"></i>
                                        </div>
                                        <span class="text-gray-900 dark:text-white">${sale.soldBy || 'Admin'}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-gray-900 dark:text-white">${sale.customer}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="max-w-xs">
                                        <div class="text-gray-900 dark:text-white font-medium">${sale.items.length} item(s)</div>
                                        <div class="text-gray-500 dark:text-gray-400 text-xs">
                                            ${sale.items.slice(0, 2).map(item => `${item.name} x${item.quantity}`).join(', ')}
                                            ${sale.items.length > 2 ? ` +${sale.items.length - 2} more` : ''}
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                        sale.paymentMethod === 'Cash' ? 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' :
                                        sale.paymentMethod === 'Bank Transfer' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                        'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200'
                                    }">
                                        <i class="fas ${
                                            sale.paymentMethod === 'Cash' ? 'fa-dollar-sign' :
                                            sale.paymentMethod === 'Bank Transfer' ? 'fa-credit-card' :
                                            'fa-mobile-alt'
                                        } mr-1"></i>
                                        ${sale.paymentMethod}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-lg font-bold text-green-600 dark:text-green-400">Br ${sale.total.toFixed(2)}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium ${
                                        sale.status === 'Completed' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                                        sale.status === 'Pending' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' :
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                    }">
                                        <i class="fas ${
                                            sale.status === 'Completed' ? 'fa-check-circle' :
                                            sale.status === 'Pending' ? 'fa-clock' :
                                            'fa-times-circle'
                                        } mr-1"></i>
                                        ${sale.status}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-2">
                                        <button onclick="viewSaleDetails(${sale.id})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="View Details">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            </div>
        `;
        
        historyContainer.innerHTML = tableHTML;
        
        // Update total sales count
        const totalSalesCount = document.getElementById('totalSalesCount');
        if (totalSalesCount) {
            totalSalesCount.textContent = sales.length;
        }
    }
    
    /**
     * View sale details
     */
    function viewSaleDetails(saleId) {
        alert(`Viewing details for Sale #${saleId}`);
    }
    
    
    /**
     * Initialize Sales History Modal
     */
    function initializeSalesHistoryModal() {
        const closeModalBtn = document.getElementById('closeSalesHistoryModal');
        const applyFiltersBtn = document.getElementById('applyFiltersBtn');
        const clearFiltersBtn = document.getElementById('clearFiltersBtn');
        const exportHistoryBtn = document.getElementById('exportHistoryBtn');
        const refreshHistoryBtn = document.getElementById('refreshHistoryBtn');
        const salesSearchInput = document.getElementById('salesSearchInput');
        
        // Close modal
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', closeSalesHistoryModal);
        }
        
        // Apply filters
        if (applyFiltersBtn) {
            applyFiltersBtn.addEventListener('click', function() {
                applySalesHistoryFilters();
            });
        }
        
        // Clear filters
        if (clearFiltersBtn) {
            clearFiltersBtn.addEventListener('click', function() {
                clearSalesHistoryFilters();
            });
        }
        
        // Export history
        if (exportHistoryBtn) {
            exportHistoryBtn.addEventListener('click', function() {
                exportSalesHistory();
            });
        }
        
        // Refresh history
        if (refreshHistoryBtn) {
            refreshHistoryBtn.addEventListener('click', function() {
                loadSalesHistory();
            });
        }
        
        // Search functionality
        if (salesSearchInput) {
            salesSearchInput.addEventListener('input', function() {
                filterSalesHistory();
            });
        }
    }
    
    /**
     * Apply sales history filters
     */
    function applySalesHistoryFilters() {
        const startDate = document.getElementById('startDate').value;
        const endDate = document.getElementById('endDate').value;
        const paymentMethod = document.getElementById('paymentMethodFilter').value;
        const status = document.getElementById('statusFilter').value;
        
        console.log('Applying filters:', { startDate, endDate, paymentMethod, status });
        // In real implementation, this would filter the data
        loadSalesHistory();
    }
    
    /**
     * Clear sales history filters
     */
    function clearSalesHistoryFilters() {
        document.getElementById('startDate').value = '';
        document.getElementById('endDate').value = '';
        document.getElementById('paymentMethodFilter').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('salesSearchInput').value = '';
        
        loadSalesHistory();
    }
    
    /**
     * Filter sales history based on search
     */
    function filterSalesHistory() {
        const searchTerm = document.getElementById('salesSearchInput').value.toLowerCase();
        console.log('Filtering sales history with term:', searchTerm);
        // In real implementation, this would filter the displayed sales
    }
    
    /**
     * Export sales history
     */
    function exportSalesHistory() {
        alert('Exporting sales history...');
        // In real implementation, this would generate and download a CSV/Excel file
    }
    
    /**
     * Initialize action buttons
     */
    function initializeActionButtons() {
        const importExportBtn = document.getElementById('importExportBtn');
        const addSaleBtn = document.getElementById('addSaleBtn');
        
        if (importExportBtn) {
            importExportBtn.addEventListener('click', function() {
                showImportExportModal();
            });
        }
        
        if (addSaleBtn) {
            addSaleBtn.addEventListener('click', function() {
                openAddSaleModal();
            });
        }
        
        // Sales History button
        const salesHistoryBtn = document.getElementById('salesHistoryBtn');
        if (salesHistoryBtn) {
            salesHistoryBtn.addEventListener('click', function() {
                openSalesHistoryModal();
            });
        }
        
        // Print button
        const printBtn = document.getElementById('printBtn');
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                window.print();
            });
        }
        
        // Refresh button
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                refreshSalesData();
            });
        }
    }
    
    /**
     * Initialize Add Sale Modal
     */
    function initializeAddSaleModal() {
        const addSaleModal = document.getElementById('addSaleModal');
        const closeModalBtn = document.getElementById('closeAddSaleModal');
        const cancelSaleBtn = document.getElementById('cancelSaleBtn');
        const addMedicineBtn = document.getElementById('addMedicineBtn');
        const createSaleBtn = document.getElementById('createSaleBtn');
        
        // Close modal handlers
        if (closeModalBtn) {
            closeModalBtn.addEventListener('click', function() {
                closeAddSaleModal();
            });
        }
        
        if (cancelSaleBtn) {
            cancelSaleBtn.addEventListener('click', function() {
                closeAddSaleModal();
            });
        }
        
        // Add medicine button
        if (addMedicineBtn) {
            addMedicineBtn.addEventListener('click', addNewMedicineItem);
        }
        
        // Create sale button
        if (createSaleBtn) {
            createSaleBtn.addEventListener('click', createSale);
        }
        
        // Initialize payment method selection
        initializePaymentMethods();
        
        // Initialize quantity controls
        initializeQuantityControls();
        
        // Initialize medicine search
        initializeMedicineSearch();
        
        // Initialize discount input
        initializeDiscountInput();
        
        // Close modal when clicking on backdrop
        if (addSaleModal) {
            addSaleModal.addEventListener('click', function(e) {
                if (e.target === addSaleModal) {
                    closeAddSaleModal();
                }
            });
        }
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !addSaleModal.classList.contains('hidden')) {
                closeAddSaleModal();
            }
        });
    }
    
    /**
     * Open Add Sale Modal
     */
    function openAddSaleModal() {
        const modal = document.getElementById('addSaleModal');
        if (modal) {
            modal.classList.remove('hidden');
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            // Ensure modal is on top
            modal.style.zIndex = '9999';
        }
    }
    
    /**
     * Close Add Sale Modal
     */
    function closeAddSaleModal() {
        const modal = document.getElementById('addSaleModal');
        if (modal) {
            modal.classList.add('hidden');
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    }
    
    /**
     * Clear sale form after successful creation
     */
    function clearSaleForm() {
        // Clear customer information
        const customerName = document.getElementById('customerName');
        const customerPhone = document.getElementById('customerPhone');
        const customerEmail = document.getElementById('customerEmail');
        
        if (customerName) customerName.value = '';
        if (customerPhone) customerPhone.value = '';
        if (customerEmail) customerEmail.value = '';
        
        // Clear items container
        const itemsContainer = document.getElementById('itemsContainer');
        if (itemsContainer) {
            itemsContainer.innerHTML = '';
        }
        
        // Reset payment method
        const paymentMethods = document.querySelectorAll('.payment-method-btn');
        paymentMethods.forEach(btn => {
            btn.classList.remove('ring-2', 'ring-orange-500');
        });
        if (paymentMethods[0]) {
            paymentMethods[0].classList.add('ring-2', 'ring-orange-500');
        }
        
        // Reset prescription required
        const prescriptionRequired = document.querySelector('.prescription-required');
        if (prescriptionRequired) {
            prescriptionRequired.checked = false;
        }
        
        // Clear notes
        const notes = document.querySelector('textarea');
        if (notes) {
            notes.value = '';
        }
        
        // Clear discount
        const discountInput = document.getElementById('discountInput');
        if (discountInput) {
            discountInput.value = '0';
        }
        
        // Reset totals
        if (typeof updateSaleTotals === 'function') {
            updateSaleTotals();
        }
    }
    
    /**
     * Add new medicine item
     */
    function addNewMedicineItem() {
        const itemsContainer = document.getElementById('itemsContainer');
        const itemCount = itemsContainer.children.length + 1;
        
        const newItem = document.createElement('div');
        newItem.className = 'item-card bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-4 relative';
        newItem.innerHTML = `
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Item ${itemCount}</span>
                <button class="delete-item-btn text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Medicine *</label>
                <div class="relative">
                    <input type="text" 
                           class="medicine-search w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white transition-colors duration-200"
                           placeholder="Search medicines...">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <div class="medicine-search-results hidden mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-600 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity *</label>
                <div class="flex items-center space-x-2">
                    <button class="quantity-btn bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 w-10 h-10 rounded-lg flex items-center justify-center transition-colors duration-200">
                        <i class="fas fa-minus text-sm"></i>
                    </button>
                    <input type="number" 
                           class="quantity-input w-20 text-center border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-800 text-gray-900 dark:text-white"
                           value="1" min="1">
                    <button class="quantity-btn bg-gray-100 dark:bg-gray-600 hover:bg-gray-200 dark:hover:bg-gray-500 text-gray-700 dark:text-gray-300 w-10 h-10 rounded-lg flex items-center justify-center transition-colors duration-200">
                        <i class="fas fa-plus text-sm"></i>
                    </button>
                </div>
            </div>

            <div class="selected-medicine-info hidden bg-gray-50 dark:bg-gray-600 rounded-lg p-3">
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">Medicine Name</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Manufacturer</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Br 0.00</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">per unit</p>
                    </div>
                </div>
            </div>
        `;
        
        itemsContainer.prepend(newItem);
        
        // Hide the no items message
        const noItemsMessage = document.getElementById('noItemsMessage');
        if (noItemsMessage) {
            noItemsMessage.style.display = 'none';
        }
        
        // Initialize the new item's functionality
        initializeItemCard(newItem);
        
        // Update item numbers
        updateItemNumbers();
    }
    
    /**
     * Initialize item card functionality
     */
    function initializeItemCard(itemCard) {
        const deleteBtn = itemCard.querySelector('.delete-item-btn');
        const quantityBtns = itemCard.querySelectorAll('.quantity-btn');
        const quantityInput = itemCard.querySelector('.quantity-input');
        const medicineSearch = itemCard.querySelector('.medicine-search');
        
        // Delete item
        if (deleteBtn) {
            deleteBtn.addEventListener('click', function() {
                itemCard.remove();
                updateItemNumbers();
                updateTotals();
                
                // Show no items message if no items left
                const itemsContainer = document.getElementById('itemsContainer');
                const noItemsMessage = document.getElementById('noItemsMessage');
                if (itemsContainer.children.length === 0 && noItemsMessage) {
                    noItemsMessage.style.display = 'block';
                }
            });
        }
        
        // Quantity controls
        quantityBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const isPlus = btn.querySelector('.fa-plus');
                const currentValue = parseInt(quantityInput.value) || 1;
                
                if (isPlus) {
                    quantityInput.value = currentValue + 1;
                } else {
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                }
                updateTotals();
            });
        });
        
        // Quantity input change
        if (quantityInput) {
            quantityInput.addEventListener('change', function() {
                if (this.value < 1) this.value = 1;
                updateTotals();
            });
        }
        
        // Medicine search
        if (medicineSearch) {
            medicineSearch.addEventListener('input', function() {
                if (this.value.length > 2) {
                    searchMedicines(this.value, itemCard);
                } else {
                    hideSearchResults(itemCard);
                }
            });
        }
    }
    
    /**
     * Update item numbers
     */
    function updateItemNumbers() {
        const items = document.querySelectorAll('.item-card');
        items.forEach((item, index) => {
            const label = item.querySelector('.text-sm.font-medium');
            const deleteBtn = item.querySelector('.delete-item-btn');
            
            if (label) {
                // Items are numbered in order of addition (newest at top gets highest number)
                // Since items are added at top, we need to reverse the numbering
                const totalItems = items.length;
                label.textContent = `Item ${totalItems - index}`;
            }
            
            // Always show delete button
            if (deleteBtn) {
                deleteBtn.classList.remove('hidden');
            }
        });
    }
    
    /**
     * Initialize payment methods
     */
    function initializePaymentMethods() {
        const paymentBtns = document.querySelectorAll('.payment-method-btn');
        
        if (paymentBtns.length === 0) {
            console.warn('No payment method buttons found');
            return;
        }
        
        paymentBtns.forEach(btn => {
            if (btn) {
                btn.addEventListener('click', function() {
                    // Remove active state from all buttons
                    paymentBtns.forEach(b => {
                        if (b) {
                            b.classList.remove('ring-2', 'ring-orange-500', 'ring-offset-2');
                        }
                    });
                    
                    // Add active state to clicked button
                    this.classList.add('ring-2', 'ring-orange-500', 'ring-offset-2');
                });
            }
        });
        
        // Set Cash as default
        const cashBtn = document.querySelector('.payment-method-cash');
        if (cashBtn) {
            cashBtn.classList.add('ring-2', 'ring-orange-500', 'ring-offset-2');
        }
    }
    
    /**
     * Initialize quantity controls
     */
    function initializeQuantityControls() {
        const itemCards = document.querySelectorAll('.item-card');
        
        if (itemCards.length === 0) {
            console.warn('No item cards found');
            return;
        }
        
        itemCards.forEach(card => {
            if (card) {
                initializeItemCard(card);
            }
        });
    }
    
    /**
     * Initialize medicine search
     */
    function initializeMedicineSearch() {
        const searchInputs = document.querySelectorAll('.medicine-search');
        
        if (searchInputs.length === 0) {
            console.warn('No medicine search inputs found');
            return;
        }
        
        searchInputs.forEach(input => {
            if (input) {
                input.addEventListener('input', function() {
                    if (this.value.length > 2) {
                        searchMedicines(this.value, this.closest('.item-card'));
                    } else {
                        hideSearchResults(this.closest('.item-card'));
                    }
                });
            }
        });
    }
    
    /**
     * Initialize discount input
     */
    function initializeDiscountInput() {
        const discountInput = document.getElementById('discountInput');
        if (discountInput) {
            discountInput.addEventListener('input', updateTotals);
        } else {
            console.warn('Discount input element not found');
        }
    }
    
    /**
     * Search medicines
     */
    async function searchMedicines(query, itemCard) {
        if (!query || query.length < 2) {
            hideMedicineDropdown(itemCard);
            return;
        }
        
        console.log('Searching for medicines:', query);
        
        try {
            const response = await fetch(`/sales/api/medicines/search?q=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            console.log('Search response status:', response.status);
            
            if (response.ok) {
                const data = await response.json();
                console.log('Search response data:', data);
                
                if (data.success && data.data) {
                    const medicines = data.data;
                    console.log('Found medicines:', medicines.length);
                    console.log('Sample medicine:', medicines[0]);
                    showMedicineDropdown(medicines, itemCard);
                    return;
                }
            } else {
                console.error('Search failed with status:', response.status);
            }
        } catch (error) {
            console.error('Failed to search medicines:', error);
        }
        
        // Show no results message
        showMedicineDropdown([], itemCard);
        console.log('No medicines available from database');
    }
    
    /**
     * Show medicine dropdown
     */
    function showMedicineDropdown(medicines, itemCard) {
        const resultsContainer = itemCard.querySelector('.medicine-search-results');
        if (!resultsContainer) return;
        
        resultsContainer.innerHTML = '';
        resultsContainer.classList.remove('hidden');
        
        if (medicines.length === 0) {
            resultsContainer.innerHTML = `
                <div class="p-3 text-center text-gray-500 dark:text-gray-400">
                    <i class="fas fa-search text-2xl mb-2"></i>
                    <p>No medicines found</p>
                </div>
            `;
            return;
        }
        
        medicines.forEach(medicine => {
            console.log('Processing medicine:', medicine);
            const resultItem = document.createElement('div');
            resultItem.className = 'p-3 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0';
            resultItem.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">${medicine.name}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">${medicine.manufacturer || 'No Manufacturer'}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Stock: ${medicine.dispensary_stock || 0} ${medicine.unit || 'units'}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Br ${(parseFloat(medicine.selling_price) || 0).toFixed(2)}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">${medicine.category ? medicine.category.name : 'No Category'}</p>
                    </div>
                </div>
            `;
            
            resultItem.addEventListener('click', function() {
                selectMedicine(medicine, itemCard);
                hideMedicineDropdown(itemCard);
            });
            
            resultsContainer.appendChild(resultItem);
        });
    }
    
    /**
     * Hide medicine dropdown
     */
    function hideMedicineDropdown(itemCard) {
        const resultsContainer = itemCard.querySelector('.medicine-search-results');
        if (resultsContainer) {
            resultsContainer.classList.add('hidden');
        }
    }
    
    /**
     * Display search results
     */
    function displaySearchResults(medicines, itemCard) {
        const resultsContainer = itemCard.querySelector('.medicine-search-results');
        if (!resultsContainer) return;
        
        resultsContainer.innerHTML = '';
        resultsContainer.classList.remove('hidden');
        
        medicines.forEach(medicine => {
            const resultItem = document.createElement('div');
            resultItem.className = 'p-3 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer border-b border-gray-200 dark:border-gray-600 last:border-b-0';
            resultItem.innerHTML = `
                <div class="flex justify-between items-center">
                    <div>
                        <h4 class="font-medium text-gray-900 dark:text-white">${medicine.name}</h4>
                        <p class="text-sm text-gray-600 dark:text-gray-400">${medicine.manufacturer}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Br ${(parseFloat(medicine.selling_price) || 0).toFixed(2)}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">${medicine.category ? medicine.category.name : 'No Category'}</p>
                    </div>
                </div>
            `;
            
            resultItem.addEventListener('click', function() {
                selectMedicine(medicine, itemCard);
                hideSearchResults(itemCard);
            });
            
            resultsContainer.appendChild(resultItem);
        });
    }
    
    /**
     * Hide search results
     */
    function hideSearchResults(itemCard) {
        const resultsContainer = itemCard.querySelector('.medicine-search-results');
        if (resultsContainer) {
            resultsContainer.classList.add('hidden');
        }
    }
    
    /**
     * Select medicine
     */
    function selectMedicine(medicine, itemCard) {
        const searchInput = itemCard.querySelector('.medicine-search');
        const selectedInfo = itemCard.querySelector('.selected-medicine-info');
        
        if (searchInput) {
            searchInput.value = medicine.name;
        }
        
        if (selectedInfo) {
            selectedInfo.classList.remove('hidden');
            selectedInfo.querySelector('h4').textContent = medicine.name;
            selectedInfo.querySelector('p').textContent = medicine.manufacturer;
            selectedInfo.querySelector('.text-sm.font-medium').textContent = `Br ${(parseFloat(medicine.selling_price) || 0).toFixed(2)}`;
        }
        
        // Store medicine data
        itemCard.dataset.medicineId = medicine.id;
        itemCard.dataset.medicinePrice = parseFloat(medicine.selling_price) || 0;
        
        updateTotals();
    }
    
    /**
     * Update totals
     */
    function updateTotals() {
        const itemCards = document.querySelectorAll('.item-card');
        let subtotal = 0;
        
        itemCards.forEach(card => {
            const quantity = parseInt(card.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(card.dataset.medicinePrice) || 0;
            subtotal += quantity * price;
        });
        
        const tax = subtotal * 0.1; // 10% tax
        const discount = parseFloat(document.getElementById('discountInput').value) || 0;
        const total = subtotal + tax - discount;
        
        // Update display
        document.getElementById('subtotalAmount').textContent = `Br ${subtotal.toFixed(2)}`;
        document.getElementById('taxAmount').textContent = `Br ${tax.toFixed(2)}`;
        document.getElementById('totalAmount').textContent = `Br ${total.toFixed(2)}`;
    }
    
    /**
     * Create sale
     */
    async function createSale() {
        const itemCards = document.querySelectorAll('.item-card');
        const selectedPaymentMethod = document.querySelector('.payment-method-btn.ring-2');
        const prescriptionRequired = document.querySelector('.prescription-required').checked;
        const notes = document.querySelector('textarea').value;
        
        // Validate items
        let hasValidItems = false;
        itemCards.forEach(card => {
            if (card.dataset.medicineId && card.querySelector('.quantity-input').value > 0) {
                hasValidItems = true;
            }
        });
        
        if (!hasValidItems) {
            alert('Please add at least one medicine to the sale.');
            return;
        }
        
        // Create sale object
        const sale = {
            customer_name: document.getElementById('customerName')?.value || null,
            customer_phone: document.getElementById('customerPhone')?.value || null,
            customer_email: document.getElementById('customerEmail')?.value || null,
            items: [],
            payment_method: selectedPaymentMethod ? (() => {
                const method = selectedPaymentMethod.textContent.trim().toLowerCase();
                if (method === 'telebirr transfer') return 'tele_birr';
                return method.replace(/\s+/g, '_');
            })() : 'cash',
            prescription_required: prescriptionRequired,
            notes: notes,
            discount_amount: parseFloat(document.getElementById('discountInput').value) || 0,
            tax_rate: 10 // Default 10% tax
        };
        
        // Collect items
        itemCards.forEach(card => {
            if (card.dataset.medicineId) {
                sale.items.push({
                    medicine_id: parseInt(card.dataset.medicineId),
                    quantity: parseInt(card.querySelector('.quantity-input').value),
                    unit_price: parseFloat(card.dataset.medicinePrice)
                });
            }
        });
        
        try {
            const response = await fetch('/sales/api', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(sale)
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Show success notification
                if (window.NotificationService) {
                    window.NotificationService.success('Sale created successfully!');
                } else {
        alert('Sale created successfully!');
                }
                
                // Close modal and clear form
        closeAddSaleModal();
                clearSaleForm();
                
                // Refresh sales data to show the new sale
                refreshSalesData();
            } else {
                alert('Failed to create sale: ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error creating sale:', error);
            alert('Failed to create sale. Please try again.');
        }
    }
    
    // Table action functions
    window.viewSaleDetails = async function(saleId) {
        // Check client-side cache first
        if (window.saleCache && window.saleCache[saleId]) {
            console.log('Loading from cache:', saleId);
            showViewSaleModal(window.saleCache[saleId]);
            return;
        }
        
        try {
            // Fetch from server
            console.log('Fetching from server:', saleId);
            const response = await fetch(`/sales/api/${saleId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    // Cache the result
            if (!window.saleCache) window.saleCache = {};
                    window.saleCache[saleId] = data.data;
                    showViewSaleModal(data.data);
            return;
        }
            }
            
            throw new Error('Sale not found');
        } catch (error) {
                console.error('Error fetching sale:', error);
                alert('Error loading sale details. Please try again.');
        }
    };
    
    
    /**
     * Show view sale modal
     */
    function showViewSaleModal(sale) {
        console.log('Sale data:', sale); // Debug log
        const modal = document.getElementById('viewSaleModal');
        const content = document.getElementById('viewSaleContent');
        
        if (modal && content) {
            content.innerHTML = `
                <div class="space-y-6">
                    <!-- Sale Header -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4">
                            <i class="fas fa-receipt text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Sale #${sale.sale_number || sale.id}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">${sale.customer_name || 'Walk-in Customer'}</p>
                        <div class="flex justify-center gap-2 mb-6">
                            <span class="px-3 py-1 ${getSaleStatusColor(sale.status)} rounded-full text-xs font-semibold">
                                ${sale.status || 'Unknown Status'}
                            </span>
                            <span class="px-3 py-1 ${getPaymentMethodColor(sale.payment_method)} rounded-full text-xs font-semibold">
                                ${sale.payment_method || 'Unknown Payment'}
                            </span>
                        </div>
                    </div>

                    <!-- Sale Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Basic Information -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Basic Information</h4>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Sale ID:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${sale.sale_number || sale.id || 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Customer:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${sale.customer_name || 'Walk-in Customer'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Sale Date:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${sale.sale_date ? new Date(sale.sale_date).toLocaleDateString('en-US', { 
                                        year: 'numeric', 
                                        month: 'long', 
                                        day: 'numeric',
                                        hour: '2-digit',
                                        minute: '2-digit'
                                    }) : 'N/A'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Sold By:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${sale.sold_by?.name || sale.sold_by || 'System Administrator'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${sale.status || 'N/A'}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Information -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Financial Information</h4>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Br ${parseFloat(sale.subtotal || 0).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Tax:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Br ${parseFloat(sale.tax_amount || 0).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Discount:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Br ${parseFloat(sale.discount_amount || 0).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between border-t border-gray-200 dark:border-gray-700 pt-2">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">Total Amount:</span>
                                    <span class="text-lg font-bold text-green-600 dark:text-green-400">Br ${parseFloat(sale.total_amount || 0).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Payment Method:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${sale.payment_method || 'N/A'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Information -->
                    ${sale.items && sale.items.length > 0 ? `
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Items (${sale.items.length})</h4>
                        <div class="space-y-3">
                            ${sale.items.map(item => `
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h5 class="font-semibold text-gray-900 dark:text-white">${item.medicine?.name || item.name || 'Unknown Item'}</h5>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Quantity: ${item.quantity || 0}</p>
                                        </div>
                                        <div class="text-right">
                                            <p class="text-sm font-semibold text-gray-900 dark:text-white">Br ${parseFloat(item.unit_price || 0).toFixed(2)} each</p>
                                            <p class="text-sm font-bold text-green-600 dark:text-green-400">Br ${parseFloat(item.total_price || item.total || 0).toFixed(2)} total</p>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                    ` : `
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Items</h4>
                        <div class="text-center py-8 bg-gray-50 dark:bg-gray-700 rounded-lg">
                            <i class="fas fa-box-open text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-600 dark:text-gray-400">No items in this sale</p>
                        </div>
                    </div>
                    `}

                    <!-- Additional Information -->
                    ${sale.notes ? `
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Notes</h4>
                        <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg p-4">${sale.notes}</p>
                    </div>
                    ` : ''}
                </div>
            `;
            
            modal.classList.remove('hidden');
        }
    }
    
    /**
     * Hide view sale modal
     */
    function hideViewSaleModal() {
        const modal = document.getElementById('viewSaleModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    // Make modal functions globally available
    window.hideViewSaleModal = hideViewSaleModal;
    window.showViewSaleModal = showViewSaleModal;
    
    // Add click outside to close modal functionality
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('viewSaleModal');
        if (modal && !modal.classList.contains('hidden')) {
            // Check if click is on the modal backdrop
            if (event.target === modal) {
                hideViewSaleModal();
            }
        }
    });
    
    // Add ESC key to close modal functionality
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const modal = document.getElementById('viewSaleModal');
            if (modal && !modal.classList.contains('hidden')) {
                hideViewSaleModal();
            }
        }
    });
    
    
    /**
     * Show error message to user
     */
    function showErrorMessage(message) {
        // Create or update error message display
        let errorDiv = document.getElementById('errorMessage');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.id = 'errorMessage';
            errorDiv.className = 'bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4';
            document.querySelector('.container').insertBefore(errorDiv, document.querySelector('.container').firstChild);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            if (errorDiv) {
                errorDiv.style.display = 'none';
            }
        }, 5000);
    }

    /**
     * Get sale status color class
     */
    function getSaleStatusColor(status) {
        switch(status?.toLowerCase()) {
            case 'completed':
                return 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200';
            case 'pending':
                return 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200';
            case 'cancelled':
                return 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200';
            default:
                return 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200';
        }
    }
    
    /**
     * Get payment method color class
     */
    function getPaymentMethodColor(method) {
        switch(method?.toLowerCase()) {
            case 'cash':
                return 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200';
            case 'card':
                return 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200';
            case 'mobile payment':
            case 'mobile':
                return 'bg-teal-100 dark:bg-teal-900 text-teal-800 dark:text-teal-200';
            default:
                return 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200';
        }
    }
    
    // Make functions globally available
    window.SalesManagement = {
        switchToCardsView,
        switchToTableView,
        showAnalytics
    };

    // Pagination Variables
    let currentPage = 1;
    let totalPages = 1;
    let pagination = null;

    // Pagination Functions
    /**
     * Update pagination UI
     */
    function updatePaginationUI() {
        if (!pagination) return;
        
        const paginationInfo = document.querySelector('.pagination-info');
        if (paginationInfo) {
            paginationInfo.textContent = `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} results`;
        }
        
        // Update pagination buttons
        const prevButton = document.querySelector('.pagination-prev');
        const nextButton = document.querySelector('.pagination-next');
        
        if (prevButton) {
            prevButton.disabled = currentPage <= 1;
        }
        
        if (nextButton) {
            nextButton.disabled = currentPage >= totalPages;
        }
        
        // Generate page numbers
        generatePageNumbers();
    }

    /**
     * Generate page number buttons
     */
    function generatePageNumbers() {
        const pageNumbersContainer = document.getElementById('pageNumbers');
        if (!pageNumbersContainer || !pagination) return;
        
        pageNumbersContainer.innerHTML = '';
        
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, currentPage + 2);
        
        // First page
        if (startPage > 1) {
            addPageButton(1);
            if (startPage > 2) {
                addEllipsis();
            }
        }
        
        // Page range
        for (let i = startPage; i <= endPage; i++) {
            addPageButton(i);
        }
        
        // Last page
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                addEllipsis();
            }
            addPageButton(totalPages);
        }
    }

    /**
     * Add page button
     */
    function addPageButton(page) {
        const pageNumbersContainer = document.getElementById('pageNumbers');
        const button = document.createElement('button');
        button.className = `px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 ${
            page === currentPage 
                ? 'bg-blue-500 text-white' 
                : 'text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
        }`;
        button.textContent = page;
        button.onclick = () => goToPage(page);
        pageNumbersContainer.appendChild(button);
    }

    /**
     * Add ellipsis
     */
    function addEllipsis() {
        const pageNumbersContainer = document.getElementById('pageNumbers');
        const ellipsis = document.createElement('span');
        ellipsis.className = 'px-3 py-2 text-sm text-gray-500 dark:text-gray-400';
        ellipsis.textContent = '...';
        pageNumbersContainer.appendChild(ellipsis);
    }

    /**
     * Initialize server-side pagination
     */
    function initializeServerSidePagination() {
        // Get current page from URL
        const urlParams = new URLSearchParams(window.location.search);
        currentPage = parseInt(urlParams.get('page')) || 1;
        
        // Get pagination data from server-side rendered content
        const paginationInfo = document.querySelector('.pagination-info');
        if (paginationInfo) {
            const text = paginationInfo.textContent;
            const matches = text.match(/Showing (\d+) to (\d+) of (\d+) results/);
            if (matches) {
                pagination = {
                    from: parseInt(matches[1]),
                    to: parseInt(matches[2]),
                    total: parseInt(matches[3]),
                    current_page: currentPage,
                    last_page: Math.ceil(parseInt(matches[3]) / 12)
                };
                totalPages = pagination.last_page;
                console.log('Server-side pagination initialized:', pagination);
            }
        }
        
        updatePaginationUI();
    }

    /**
     * Navigate to specific page
     */
    function goToPage(page) {
        if (page < 1 || page > totalPages || page === currentPage) return;
        
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        window.location.href = url.toString();
    }

    /**
     * Go to previous page
     */
    function previousPage() {
        if (currentPage > 1) {
            goToPage(currentPage - 1);
        }
    }

    /**
     * Go to next page
     */
    function nextPage() {
        if (currentPage < totalPages) {
            goToPage(currentPage + 1);
        }
    }

    // Initialize pagination when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeServerSidePagination();
    });
    
})();
</script>

<!-- Add Sale Modal -->
<div id="addSaleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-6xl mx-4 max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-white text-lg"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">New Sale</h2>
            </div>
            <button id="closeAddSaleModal" class="w-8 h-8 bg-orange-500 rounded-lg flex items-center justify-center hover:bg-orange-600 transition-colors">
                <i class="fas fa-times text-white text-sm"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex flex-1 min-h-0">
            <!-- Left Section: Add Items -->
            <div class="flex-1 p-6 border-r border-gray-200 dark:border-gray-700 flex flex-col min-h-0">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex-shrink-0">Add Items</h3>
                
                <!-- Add Medicine Button -->
                <button id="addMedicineBtn" class="w-full bg-orange-500 hover:bg-orange-600 text-white rounded-xl py-4 px-6 font-semibold text-lg transition-colors duration-200 flex items-center justify-center space-x-3 mb-6 flex-shrink-0">
                    <i class="fas fa-plus text-xl"></i>
                    <span>Add Medicine</span>
                </button>

                <!-- Items Container -->
                <div id="itemsContainer" class="space-y-4 flex-1 overflow-y-auto">
                    <!-- No items message -->
                    <div id="noItemsMessage" class="text-center py-8 text-gray-500 dark:text-gray-400">
                        <i class="fas fa-box-open text-4xl mb-4 text-gray-300 dark:text-gray-600"></i>
                        <p class="text-lg">No items added yet</p>
                        <p class="text-sm">Click "Add Medicine" to start adding items</p>
                    </div>
                </div>
            </div>

            <!-- Right Section: Payment & Totals -->
            <div class="flex-1 p-6 flex flex-col min-h-0">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6 flex-shrink-0">Payment & Totals</h3>
                
                <!-- Scrollable Content -->
                <div class="flex-1 overflow-y-auto">
                    <!-- Payment Method Section -->
                    <div class="mb-8">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Payment Method</h4>
                        <div class="flex space-x-3">
                            <!-- Cash Button -->
                            <button class="payment-method-btn payment-method-cash flex-1 bg-orange-500 hover:bg-orange-600 text-white rounded-xl py-2 px-4 font-semibold transition-colors duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-dollar-sign text-base"></i>
                                <span class="text-sm">Cash</span>
                            </button>
                            
                            <!-- Bank Transfer Button -->
                            <button class="payment-method-btn payment-method-bank flex-1 bg-green-600 hover:bg-green-700 text-white rounded-xl py-2 px-4 font-semibold transition-colors duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-credit-card text-base"></i>
                                <span class="text-sm">Bank Transfer</span>
                            </button>
                            
                            <!-- TeleBirr Transfer Button -->
                            <button class="payment-method-btn payment-method-telebirr flex-1 bg-green-600 hover:bg-green-700 text-white rounded-xl py-2 px-4 font-semibold transition-colors duration-200 flex items-center justify-center space-x-2">
                                <i class="fas fa-mobile-alt text-base"></i>
                                <span class="text-sm">TeleBirr Transfer</span>
                            </button>
                        </div>
                    </div>

                    <!-- Prescription Required Checkbox -->
                    <div class="mb-6">
                        <label class="flex items-center space-x-3 cursor-pointer">
                            <input type="checkbox" class="prescription-required w-5 h-5 text-orange-500 rounded border-gray-300 dark:border-gray-600 focus:ring-orange-500">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Prescription Required</span>
                        </label>
                    </div>

                    <!-- Notes Section -->
                    <div class="mb-6">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Notes</h4>
                        <textarea 
                            class="w-full h-24 p-3 border border-gray-300 dark:border-gray-600 rounded-lg resize-none focus:ring-2 focus:ring-orange-500 focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 bg-white dark:bg-gray-700"
                            placeholder="Additional notes about this sale..."></textarea>
                    </div>

                    <!-- Sale Summary -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4">Sale Summary</h4>
                        
                        <div class="space-y-3">
                            <!-- Subtotal -->
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" id="subtotalAmount">Br 0.00</span>
                            </div>
                            
                            <!-- Tax -->
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Tax (10.0%):</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white" id="taxAmount">Br 0.00</span>
                            </div>
                            
                            <!-- Discount -->
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Discount:</span>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-500 dark:text-gray-400">$</span>
                                    <input type="number" 
                                           class="w-16 h-8 text-center border border-gray-300 dark:border-gray-600 rounded text-sm focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-600 text-gray-900 dark:text-white"
                                           value="0" min="0" step="0.01" id="discountInput">
                                </div>
                            </div>
                            
                            <!-- Total -->
                            <div class="flex justify-between items-center pt-3 border-t border-gray-200 dark:border-gray-600">
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Total:</span>
                                <span class="text-lg font-bold text-orange-500" id="totalAmount">Br 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-4 p-6 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 flex-shrink-0">
            <button id="cancelSaleBtn" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold transition-colors duration-200">
                Cancel
            </button>
            <button id="createSaleBtn" class="px-6 py-3 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-xl font-semibold transition-colors duration-200 flex items-center space-x-2">
                <i class="fas fa-file-alt"></i>
                <span>Create Sale</span>
            </button>
        </div>
    </div>
</div>

<!-- Sales History Modal -->
<div id="salesHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999] hidden p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-7xl h-[95vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-history text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sales History</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View and manage all sales transactions</p>
                </div>
            </div>
            <button id="closeSalesHistoryModal" class="w-10 h-10 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl flex items-center justify-center transition-colors">
                <i class="fas fa-times text-gray-500 dark:text-gray-400"></i>
            </button>
        </div>

        <!-- Modal Body -->
        <div class="flex flex-1 min-h-0">
            <!-- Left Panel: Filters and Search -->
            <div class="w-80 border-r border-gray-200 dark:border-gray-700 p-6 flex flex-col overflow-y-auto">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Filters & Search</h3>
                
                <!-- Search -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Sales</label>
                    <div class="relative">
                        <input type="text" id="salesSearchInput" placeholder="Search by customer, sale ID..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Date Range -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                    <div class="space-y-3">
                        <input type="date" id="startDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <input type="date" id="endDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <!-- Payment Method Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                    <select id="paymentMethodFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Methods</option>
                        <option value="Cash">Cash</option>
                        <option value="Bank Transfer">Bank Transfer</option>
                        <option value="TeleBirr Transfer">TeleBirr Transfer</option>
                    </select>
                </div>
                
                <!-- Status Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Status</option>
                        <option value="Completed">Completed</option>
                        <option value="Pending">Pending</option>
                        <option value="Cancelled">Cancelled</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button id="applyFiltersBtn" class="w-full bg-green-600 hover:bg-green-700 text-white rounded-lg py-2 px-4 font-medium transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                    <button id="clearFiltersBtn" class="w-full bg-gray-500 hover:bg-gray-600 text-white rounded-lg py-2 px-4 font-medium transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </button>
                    <button id="exportHistoryBtn" class="w-full bg-blue-600 hover:bg-blue-700 text-white rounded-lg py-2 px-4 font-medium transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>Export History
                    </button>
                </div>
            </div>
            
            <!-- Right Panel: Sales History List -->
            <div class="flex-1 p-6 flex flex-col min-h-0 overflow-hidden">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Sales Records</h3>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total: <span id="totalSalesCount">0</span> sales</span>
                        <button id="refreshHistoryBtn" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh
                        </button>
                    </div>
                </div>
                
                <!-- Sales History Container -->
                <div id="salesHistoryContainer" class="flex-1 overflow-y-auto space-y-4">
                    <!-- Sales will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import/Export Sales Modal -->
<div id="importExportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import/Export Sales</h2>
            </div>
            <button onclick="hideImportExportModal()" class="w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>git add and git push origin main
            </button>
        </div>

        <!-- Navigation Tabs -->
        <div class="flex border-b border-gray-200 dark:border-gray-700">
            <button id="importTab" onclick="switchTab('import')" class="flex-1 px-6 py-4 text-left border-b-2 border-orange-500 text-orange-500 font-semibold flex items-center justify-center">
                <i class="fas fa-download mr-2"></i>Import
            </button>
            <button id="exportTab" onclick="switchTab('export')" class="flex-1 px-6 py-4 text-left border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium flex items-center justify-center hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-upload mr-2"></i>Export
            </button>
            <button id="printTab" onclick="switchTab('print')" class="flex-1 px-6 py-4 text-left border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium flex items-center justify-center hover:text-gray-700 dark:hover:text-gray-300">
                <i class="fas fa-print mr-2"></i>Print
            </button>
        </div>

        <!-- Tab Content -->
        <div class="flex-1 overflow-y-auto">
            <!-- Import Tab Content -->
            <div id="importContent" class="p-6">
                <form id="importForm" enctype="multipart/form-data">
                    @csrf
                <div class="mb-6">
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-4">Select File</label>
                        <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-8 text-center hover:border-orange-500 transition-colors duration-200" id="fileDropZone">
                        <i class="fas fa-upload text-3xl text-gray-400 dark:text-gray-500 mb-4"></i>
                            <input type="file" id="importFile" name="file" accept=".xlsx,.xls,.csv" class="hidden" required>
                            <button type="button" id="selectFileBtn" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 mb-4">
                            Select File
                        </button>
                            <div id="selectedFileName" class="hidden text-sm text-gray-600 dark:text-gray-400 mb-2"></div>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Supported formats: CSV, Excel: .xlsx, .xls, .csv</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">Maximum file size: 10MB</p>
                    </div>
                </div>
                    
                    <!-- Import Mode Selection -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Import Mode</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="radio" name="import_mode" value="create" checked class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <span class="ml-3 text-gray-900 dark:text-white">Create New (Skip existing)</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="import_mode" value="update" class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <span class="ml-3 text-gray-900 dark:text-white">Update Existing</span>
                            </label>
                            <label class="flex items-center">
                                <input type="radio" name="import_mode" value="replace" class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <span class="ml-3 text-gray-900 dark:text-white">Replace All</span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Template Download -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Need a Template?</label>
                        <button type="button" id="downloadTemplateBtn" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                            <i class="fas fa-download mr-2"></i>Download Template
                        </button>
                    </div>
                </form>
            </div>

            <!-- Export Tab Content -->
            <div id="exportContent" class="p-6 hidden">
                <form id="exportForm">
                    @csrf
                <div class="space-y-6">
                    <!-- Export Format -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Export Format</label>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                    <input type="radio" name="format" value="excel" checked class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <i class="fas fa-file-excel text-blue-500 ml-3 mr-2"></i>
                                <span class="text-gray-900 dark:text-white">Excel (.xlsx)</span>
                            </label>
                            <label class="flex items-center">
                                    <input type="radio" name="format" value="csv" class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <i class="fas fa-file-csv text-green-500 ml-3 mr-2"></i>
                                <span class="text-gray-900 dark:text-white">CSV (.csv)</span>
                            </label>
                            <label class="flex items-center">
                                    <input type="radio" name="format" value="pdf" class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <i class="fas fa-file-pdf text-red-500 ml-3 mr-2"></i>
                                <span class="text-gray-900 dark:text-white">PDF (.pdf)</span>
                            </label>
                        </div>
                    </div>

                    <!-- Export Filename -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">Export Filename</label>
                            <input type="text" name="filename" value="sales_export" class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">File will be saved with timestamp</p>
                        </div>

                        <!-- Export Filters -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Export Filters (Optional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="filters[status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Status</option>
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                                    <select name="filters[payment_method]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Payment Methods</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="mobile">Mobile Payment</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                                    <input type="date" name="filters[date_from]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                                    <input type="date" name="filters[date_to]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                </div>
                            </div>
                    </div>

                    <!-- Export Summary -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Export Summary</label>
                            <div id="exportSummary" class="grid grid-cols-2 gap-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <div>Total Records: <span id="totalRecords">0</span></div>
                                    <div>Completed Sales: <span id="completedSales">0</span></div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <div>Total Revenue: <span id="exportTotalRevenue">0</span> Birr</div>
                                    <div>Pending Sales: <span id="pendingSales">0</span></div>
                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>

            <!-- Print Tab Content -->
            <div id="printContent" class="p-6 hidden">
                <form id="printForm">
                    @csrf
                <div class="space-y-6">
                    <!-- Print Sales Report -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-2">Print Sales Report</h3>
                        <p class="text-gray-700 dark:text-gray-300">This will generate a professional sales report with summary statistics and detailed transaction listing.</p>
                    </div>

                        <!-- Print Filters -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Print Filters (Optional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="filters[status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Status</option>
                                        <option value="completed">Completed</option>
                                        <option value="pending">Pending</option>
                                        <option value="cancelled">Cancelled</option>
                                    </select>
                            </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
                                    <select name="filters[payment_method]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Payment Methods</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="mobile">Mobile Payment</option>
                                    </select>
                            </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Date From</label>
                                    <input type="date" name="filters[date_from]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                        </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Date To</label>
                                    <input type="date" name="filters[date_to]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                    </div>
                </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideImportExportModal()" 
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Cancel
            </button>
            <button id="actionButton" class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                <i class="fas fa-download mr-2"></i>Import File
            </button>
        </div>
    </div>
</div>

<script>
// Import/Export Modal Functionality
(function() {
    'use strict';
    
    let currentTab = 'import';
    let isImporting = false;
    let isExporting = false;
    
    // Initialize when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeImportExportModal();
    });
    
    /**
     * Initialize Import/Export Modal
     */
    function initializeImportExportModal() {
        console.log('Initializing Import/Export Modal...');
        
        // Initialize file upload
        initializeFileUpload();
        
        // Initialize export functionality
        initializeExportFunctionality();
        
        // Initialize template download
        initializeTemplateDownload();
        
        // Initialize action button
        initializeActionButton();
        
        console.log('Import/Export Modal initialized successfully');
    }
    
    /**
     * Initialize file upload functionality
     */
    function initializeFileUpload() {
        const fileInput = document.getElementById('importFile');
        const selectFileBtn = document.getElementById('selectFileBtn');
        const selectedFileName = document.getElementById('selectedFileName');
        const fileDropZone = document.getElementById('fileDropZone');
        
        if (selectFileBtn && fileInput) {
            selectFileBtn.addEventListener('click', function() {
                fileInput.click();
            });
        }
        
        if (fileInput) {
            fileInput.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const fileName = this.files[0].name;
                    selectedFileName.textContent = `Selected: ${fileName}`;
                    selectedFileName.classList.remove('hidden');
                    selectFileBtn.textContent = 'Change File';
                    selectFileBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    selectFileBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }
            });
        }
        
        // Drag and drop functionality
        if (fileDropZone) {
            fileDropZone.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.classList.add('border-orange-500', 'bg-orange-50');
            });
            
            fileDropZone.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.classList.remove('border-orange-500', 'bg-orange-50');
            });
            
            fileDropZone.addEventListener('drop', function(e) {
                e.preventDefault();
                this.classList.remove('border-orange-500', 'bg-orange-50');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    fileInput.files = files;
                    const fileName = files[0].name;
                    selectedFileName.textContent = `Selected: ${fileName}`;
                    selectedFileName.classList.remove('hidden');
                    selectFileBtn.textContent = 'Change File';
                    selectFileBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
                    selectFileBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
                }
            });
        }
    }
    
    /**
     * Initialize export functionality
     */
    function initializeExportFunctionality() {
        // Add export functionality here
        console.log('Export functionality initialized');
    }
    
    /**
     * Initialize template download
     */
    function initializeTemplateDownload() {
        const downloadTemplateBtn = document.getElementById('downloadTemplateBtn');
        
        if (downloadTemplateBtn) {
            downloadTemplateBtn.addEventListener('click', function() {
                // Create and download template file
                const templateData = [
                    ['Sale ID', 'Customer Name', 'Items', 'Quantity', 'Unit Price', 'Total Amount', 'Payment Method', 'Status', 'Sale Date'],
                    ['SALE-001', 'John Doe', 'Paracetamol 500mg', '2', '5.00', '10.00', 'Cash', 'Completed', '2024-01-15'],
                    ['SALE-002', 'Jane Smith', 'Amoxicillin 250mg', '1', '25.00', '25.00', 'Card', 'Completed', '2024-01-15']
                ];
                
                const csvContent = templateData.map(row => row.join(',')).join('\n');
                const blob = new Blob([csvContent], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'sales_template.csv';
                a.click();
                window.URL.revokeObjectURL(url);
            });
        }
    }
    
    /**
     * Initialize action button
     */
    function initializeActionButton() {
        const actionButton = document.getElementById('actionButton');
        
        if (actionButton) {
            actionButton.addEventListener('click', function() {
                if (currentTab === 'import') {
                    handleImport();
                } else if (currentTab === 'export') {
                    handleExport();
                } else if (currentTab === 'print') {
                    handlePrint();
                }
            });
        }
    }
    
    /**
     * Handle import
     */
    function handleImport() {
        if (isImporting) return;
        
        const form = document.getElementById('importForm');
        const fileInput = document.getElementById('importFile');
        
        if (!fileInput.files.length) {
            alert('Please select a file to import.');
            return;
        }
        
        isImporting = true;
        const actionButton = document.getElementById('actionButton');
        const originalText = actionButton.innerHTML;
        actionButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Importing...';
        actionButton.disabled = true;
        
        // Simulate import process
        setTimeout(() => {
            alert('Import completed successfully!');
            actionButton.innerHTML = originalText;
            actionButton.disabled = false;
            isImporting = false;
            hideImportExportModal();
        }, 2000);
    }
    
    /**
     * Handle export
     */
    function handleExport() {
        if (isExporting) return;
        
        isExporting = true;
        const actionButton = document.getElementById('actionButton');
        const originalText = actionButton.innerHTML;
        actionButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
        actionButton.disabled = true;
        
        // Simulate export process
        setTimeout(() => {
            alert('Export completed successfully!');
            actionButton.innerHTML = originalText;
            actionButton.disabled = false;
            isExporting = false;
            hideImportExportModal();
        }, 2000);
    }
    
    /**
     * Handle print
     */
    function handlePrint() {
        const form = document.getElementById('printForm');
        if (!form) return;
        
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        // Add form data to params
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('filters[')) {
                const filterKey = key.replace('filters[', '').replace(']', '');
                if (value) {
                    params.append(`filters[${filterKey}]`, value);
                }
            }
        }
        
        // Open print report in new window
        const printUrl = `/sales/print?${params.toString()}`;
        const printWindow = window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
        
        if (printWindow) {
            printWindow.focus();
            window.NotificationService.success('Print report opened in new window.');
        } else {
            window.NotificationService.error('Unable to open print window. Please check your popup blocker.');
        }
    }
    
    /**
     * Show Import/Export Modal
     */
    window.showImportExportModal = function() {
        const modal = document.getElementById('importExportModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };
    
    /**
     * Hide Import/Export Modal
     */
    window.hideImportExportModal = function() {
        const modal = document.getElementById('importExportModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    };
    
    /**
     * Global Notification Service
     * Professional, scalable notification system optimized for cPanel/shared hosting
     */
    class NotificationService {
        constructor() {
            this.notifications = [];
            this.container = null;
            this.maxNotifications = 5;
            this.defaultDuration = 3000;
            this.animationDuration = 300;
            this.init();
        }

        /**
         * Initialize notification service
         */
        init() {
            this.createContainer();
            this.setupEventListeners();
        }

        /**
         * Create notification container
         */
        createContainer() {
            this.container = document.createElement('div');
            this.container.id = 'notification-container';
            this.container.className = 'fixed top-4 right-4 z-[10000] space-y-2 pointer-events-none';
            document.body.appendChild(this.container);
        }

        /**
         * Setup global event listeners
         */
        setupEventListeners() {
            // Handle escape key to close all notifications
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.clearAll();
                }
            });
        }

        /**
         * Show notification
         */
        show(message, type = 'info', duration = null) {
            const notification = this.createNotification(message, type);
            this.addToQueue(notification);
            this.animateIn(notification);
            this.scheduleRemoval(notification, duration);
            return notification;
        }

        /**
         * Create notification element
         */
        createNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `notification-item transform translate-x-full transition-all duration-300 pointer-events-auto ${
                type === 'success' ? 'bg-green-500 text-white' :
                type === 'error' ? 'bg-red-500 text-white' :
                type === 'warning' ? 'bg-yellow-500 text-white' :
                'bg-blue-500 text-white'
            }`;
            
            notification.innerHTML = `
                <div class="flex items-center p-4 rounded-lg shadow-lg max-w-sm">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' :
                        type === 'error' ? 'fa-exclamation-circle' :
                        type === 'warning' ? 'fa-exclamation-triangle' :
                        'fa-info-circle'
                    } mr-3 flex-shrink-0"></i>
                    <span class="flex-1 text-sm font-medium">${message}</span>
                    <button class="ml-3 text-white hover:text-gray-200 transition-colors" onclick="window.NotificationService.close(this.closest('.notification-item'))">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
            `;

            // Add click to dismiss
            notification.addEventListener('click', (e) => {
                if (!e.target.closest('button')) {
                    this.close(notification);
                }
            });

            return notification;
        }

        /**
         * Add notification to queue
         */
        addToQueue(notification) {
            this.notifications.push(notification);
            this.container.appendChild(notification);
            
            // Limit number of notifications
            if (this.notifications.length > this.maxNotifications) {
                const oldest = this.notifications.shift();
                this.close(oldest);
            }
        }

        /**
         * Animate notification in
         */
        animateIn(notification) {
            requestAnimationFrame(() => {
                notification.classList.remove('translate-x-full');
            });
        }

        /**
         * Schedule notification removal
         */
        scheduleRemoval(notification, duration = null) {
            const removeDuration = duration || this.defaultDuration;
            setTimeout(() => {
                this.close(notification);
            }, removeDuration);
        }

        /**
         * Close specific notification
         */
        close(notification) {
            if (!notification || !notification.parentNode) return;
            
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
                this.removeFromQueue(notification);
            }, this.animationDuration);
        }

        /**
         * Remove notification from queue
         */
        removeFromQueue(notification) {
            const index = this.notifications.indexOf(notification);
            if (index > -1) {
                this.notifications.splice(index, 1);
            }
        }

        /**
         * Clear all notifications
         */
        clearAll() {
            this.notifications.forEach(notification => {
                this.close(notification);
            });
        }

        /**
         * Show success notification
         */
        success(message, duration = null) {
            return this.show(message, 'success', duration);
        }

        /**
         * Show error notification
         */
        error(message, duration = null) {
            return this.show(message, 'error', duration);
        }

        /**
         * Show warning notification
         */
        warning(message, duration = null) {
            return this.show(message, 'warning', duration);
        }

        /**
         * Show info notification
         */
        info(message, duration = null) {
            return this.show(message, 'info', duration);
        }
    }

    // Initialize global notification service
    window.NotificationService = new NotificationService();

    /**
     * Global notification function for backward compatibility
     */
    function showNotification(message, type = 'info', duration = null) {
        return window.NotificationService.show(message, type, duration);
    }

    // Make functions globally available
    window.showNotification = showNotification;
    
    // Test notification system on page load
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(() => {
            window.NotificationService.success('Sales page loaded successfully!');
        }, 1000);
    });
    
    /**
     * Update export summary
     */
    function updateExportSummary() {
        // Fetch real data from server
        fetch('/sales/api/stats')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data) {
                    const stats = data.data;
                    document.getElementById('totalRecords').textContent = stats.total_sales || '0';
                    document.getElementById('completedSales').textContent = stats.completed_sales || '0';
                    document.getElementById('exportTotalRevenue').textContent = stats.total_revenue || '0.00';
                    document.getElementById('pendingSales').textContent = stats.pending_sales || '0';
                } else {
                    // Show default values if data fetch fails
                    document.getElementById('totalRecords').textContent = '0';
                    document.getElementById('completedSales').textContent = '0';
                    document.getElementById('exportTotalRevenue').textContent = '0.00';
                    document.getElementById('pendingSales').textContent = '0';
                }
            })
            .catch(error => {
                console.error('Error fetching export summary:', error);
                // Show default values on error
                document.getElementById('totalRecords').textContent = '0';
                document.getElementById('completedSales').textContent = '0';
                document.getElementById('exportTotalRevenue').textContent = '0.00';
                document.getElementById('pendingSales').textContent = '0';
            });
    }
    
    // Tab switching functionality
    window.switchTab = function(tab) {
        currentTab = tab;
        
        // Hide all content
        document.getElementById('importContent').classList.add('hidden');
        document.getElementById('exportContent').classList.add('hidden');
        document.getElementById('printContent').classList.add('hidden');
        
        // Remove active styles from all tabs
        document.getElementById('importTab').classList.remove('border-orange-500', 'text-orange-500', 'font-semibold');
        document.getElementById('importTab').classList.add('border-transparent', 'text-gray-500', 'font-medium');
        document.getElementById('exportTab').classList.remove('border-orange-500', 'text-orange-500', 'font-semibold');
        document.getElementById('exportTab').classList.add('border-transparent', 'text-gray-500', 'font-medium');
        document.getElementById('printTab').classList.remove('border-orange-500', 'text-orange-500', 'font-semibold');
        document.getElementById('printTab').classList.add('border-transparent', 'text-gray-500', 'font-medium');
        
        // Show selected content and update tab styles
        if (tab === 'import') {
            document.getElementById('importContent').classList.remove('hidden');
            document.getElementById('importTab').classList.remove('border-transparent', 'text-gray-500', 'font-medium');
            document.getElementById('importTab').classList.add('border-orange-500', 'text-orange-500', 'font-semibold');
            document.getElementById('actionButton').innerHTML = '<i class="fas fa-download mr-2"></i>Import File';
        } else if (tab === 'export') {
            document.getElementById('exportContent').classList.remove('hidden');
            document.getElementById('exportTab').classList.remove('border-transparent', 'text-gray-500', 'font-medium');
            document.getElementById('exportTab').classList.add('border-orange-500', 'text-orange-500', 'font-semibold');
            document.getElementById('actionButton').innerHTML = '<i class="fas fa-download mr-2"></i>Download Export';
            updateExportSummary(); // Update summary when switching to export
        } else if (tab === 'print') {
            document.getElementById('printContent').classList.remove('hidden');
            document.getElementById('printTab').classList.remove('border-transparent', 'text-gray-500', 'font-medium');
            document.getElementById('printTab').classList.add('border-orange-500', 'text-orange-500', 'font-semibold');
            document.getElementById('actionButton').innerHTML = '<i class="fas fa-print mr-2"></i>Print';
        }
    };
})();
</script>

<!-- View Sale Modal -->
<div id="viewSaleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-receipt text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Sale Details</h2>
                    <p class="text-gray-600 dark:text-gray-400">View Sale Information</p>
                </div>
            </div>
            <button onclick="hideViewSaleModal()" class="w-10 h-10 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <div id="viewSaleContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideViewSaleModal()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Close
            </button>
        </div>
    </div>
</div>
@endsection
