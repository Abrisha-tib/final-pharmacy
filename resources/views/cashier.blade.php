@extends('layouts.app')

@section('title', 'Cashier Station - Analog Pharmacy Management System')
@section('page-title', 'Cashier Station')
@section('page-description', 'Process sales and generate receipts')

@push('head')
<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
<meta http-equiv="Pragma" content="no-cache">
<meta http-equiv="Expires" content="0">
@endpush

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
    
    /* Cashier specific styles */
    .cashier-card {
        transition: all 0.3s ease;
        border-left: 4px solid #f59e0b;
    }
    
    .cashier-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    /* Enhanced sale card header styling */
    .sale-card h3 {
        letter-spacing: -0.025em;
        line-height: 1.2;
        font-weight: 800;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    /* Status and payment badge improvements */
    .status-badge {
        transition: all 0.2s ease;
    }
    
    .status-badge:hover {
        transform: scale(1.05);
    }
    
    .payment-badge {
        transition: all 0.2s ease;
    }
    
    .payment-badge:hover {
        transform: scale(1.05);
    }
    
    /* Status badge colors */
    .status-badge.status-completed {
        background-color: #10b981;
        color: white;
    }
    
    .status-badge.status-pending {
        background-color: #f59e0b;
        color: white;
    }
    
    /* Payment badge colors */
    .payment-badge.cash {
        background-color: #059669;
        color: white;
    }
    
    .payment-badge.card {
        background-color: #3b82f6;
        color: white;
    }
    
    .payment-badge.mobile_payment {
        background-color: #8b5cf6;
        color: white;
    }
    
    .payment-badge.bank_transfer {
        background-color: #6b7280;
        color: white;
    }
    
    /* Enhanced total amount section for better light theme support */
    .total-amount {
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    /* Enhanced total amount styling for better theme support */
    .total-amount-section {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border: 1px solid #93c5fd;
    }
    
    .dark .total-amount-section {
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        border: 1px solid #3b82f6;
    }
    
    .total-amount-label {
        color: #1e40af;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .dark .total-amount-label {
        color: #93c5fd;
    }
    
    .total-amount-value {
        color: #1e3a8a;
        font-weight: 800;
        text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .dark .total-amount-value {
        color: #dbeafe;
    }
    
    /* Enhanced cashier header styling to match the subtle image design */
    .cashier-header-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
        border: 1px solid #cbd5e1;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
    }
    
    .dark .cashier-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        border: 1px solid #64748b;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
    }
    
    /* Removed the purple gradient line from header */
    
    /* Enhanced text styling for better readability */
    .cashier-header-card h1 {
        background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .dark .cashier-header-card h1 {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Card hover effects matching sales page */
    .card-hover {
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .metrics-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 2rem;
        padding: 1.5rem;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        min-height: 140px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    
    .metrics-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.2);
    }
    
    .dark .metrics-card {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    }
    
    .metrics-card.orange {
        background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
    }
    
    .metrics-card.green {
        background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
    }
    
    .metrics-card.blue {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    }
    
    .metrics-card.purple {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
    }
    
    .metrics-card-icon-btn {
        width: 3.5rem;
        height: 3.5rem;
        background: rgba(255, 255, 255, 0.15);
        border-radius: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(10px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 2px 4px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }
    
    .metrics-card-icon-btn:hover {
        background: rgba(255, 255, 255, 0.25);
        transform: scale(1.05);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.3), 0 4px 8px rgba(0, 0, 0, 0.15);
    }
    
    .metrics-card-badge {
        background: rgba(255, 255, 255, 0.15);
        border-radius: 9999px;
        padding: 0.25rem 0.75rem;
        font-size: 0.75rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.2), 0 1px 2px rgba(0, 0, 0, 0.1);
    }
    
    .sale-card {
        transition: all 0.3s ease;
        border-left: 4px solid #10b981;
    }
    
    .sale-card.pending {
        border-left-color: #f59e0b;
    }
    
    .sale-card.completed {
        border-left-color: #10b981;
    }
    
    .sale-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .dark .sale-card:hover {
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
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
    
    .status-pending {
        background-color: #fef3c7;
        color: #92400e;
    }
    
    .status-completed {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .payment-badge {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .payment-badge.mobile_money {
        background-color: #e5e7eb;
        color: #374151;
    }
    
    .payment-badge.card {
        background-color: #dbeafe;
        color: #1e40af;
    }
    
    .dark .status-pending {
        background-color: #451a03;
        color: #fbbf24;
    }
    
    .dark .status-completed {
        background-color: #064e3b;
        color: #6ee7b7;
    }
    
    .dark .payment-badge {
        background-color: #064e3b;
        color: #6ee7b7;
    }
    
    .dark .payment-badge.mobile_money {
        background-color: #374151;
        color: #d1d5db;
    }
    
    .dark .payment-badge.card {
        background-color: #1e3a8a;
        color: #93c5fd;
    }
    
    .total-amount {
        font-size: 1.25rem;
        font-weight: 700;
        color: #ffffff;
    }
    
    .dark .total-amount {
        color: #f9fafb;
    }
    
    .filter-section {
        background: white;
        border-radius: 1rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }
    
    .dark .filter-section {
        background: #1f2937;
        border-color: #374151;
    }
    
    .sales-filter-btn {
        transition: all 0.2s ease;
    }
    
    .sales-filter-btn.active {
        background-color: #f97316;
        color: white;
    }
    
    .sales-filter-btn:not(.active) {
        background-color: white;
        color: #6b7280;
    }
    
    .sales-filter-btn:not(.active):hover {
        background-color: #f3f4f6;
    }
    
    .dark .sales-filter-btn:not(.active) {
        background-color: #374151;
        color: #d1d5db;
    }
    
    .dark .sales-filter-btn:not(.active):hover {
        background-color: #4b5563;
    }
    
    /* Enhanced filter button styling for better UX */
    .sales-filter-btn.active {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
        transform: translateY(-1px);
    }
    
    .sales-filter-btn.active:hover {
        background: linear-gradient(135deg, #d97706 0%, #b45309 100%);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.4);
    }
    
    /* Incoming button specific styling */
    #incomingBtn.active {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    #incomingBtn.active:hover {
        background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }
    
    /* Processed button specific styling */
    #processedBtn.active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    #processedBtn.active:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
    }
    
    /* All sales button specific styling */
    #allSalesBtn.active {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }
    
    #allSalesBtn.active:hover {
        background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
        box-shadow: 0 6px 16px rgba(99, 102, 241, 0.4);
    }
</style>

<!-- Cashier Header -->
<div class="mb-8">
    <div class="cashier-header-card relative rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Cashier Station</h1>
                <p class="text-slate-600 dark:text-slate-300 text-lg">Process sales and generate receipts</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Cashier Status</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white" id="cashierStatus">Ready to Process</p>
            </div>
        </div>
    </div>
</div>

<!-- Metrics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Incoming Sales -->
    <div class="card-hover bg-gradient-to-br from-cyan-400 to-cyan-500 dark:from-cyan-800 dark:to-cyan-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-cyan-600 dark:border-cyan-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-cyan-800 dark:text-cyan-200 uppercase tracking-wide">Incoming Sales</p>
                <p class="text-3xl font-bold text-cyan-900 dark:text-white mt-2 mb-1" id="incomingSales">{{ $metrics['incoming_sales'] ?? 0 }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-cyan-500 dark:bg-cyan-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                        <span>Ready to process</span>
                    </div>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-clock text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Processed Sales -->
    <div class="card-hover bg-gradient-to-br from-emerald-400 to-emerald-500 dark:from-emerald-800 dark:to-emerald-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-emerald-600 dark:border-emerald-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-emerald-800 dark:text-emerald-200 uppercase tracking-wide">Processed Sales</p>
                <p class="text-3xl font-bold text-emerald-900 dark:text-white mt-2 mb-1" id="processedSales">{{ $metrics['processed_sales'] ?? 0 }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-emerald-500 dark:bg-emerald-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-check text-xs mr-1"></i>
                        <span>Completed today</span>
                    </div>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-check text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="card-hover bg-gradient-to-br from-amber-400 to-amber-500 dark:from-amber-800 dark:to-amber-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-amber-600 dark:border-amber-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-amber-800 dark:text-amber-200 uppercase tracking-wide">Total Revenue</p>
                <p class="text-3xl font-bold text-amber-900 dark:text-white mt-2 mb-1" id="totalRevenue">Br {{ $metrics['total_revenue'] ?? '0.00' }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-amber-500 dark:bg-amber-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-chart-line text-xs mr-1"></i>
                        <span>From processed sales</span>
                    </div>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-dollar-sign text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Average Processing Time -->
    <div class="card-hover bg-gradient-to-br from-violet-400 to-violet-500 dark:from-violet-800 dark:to-violet-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-violet-600 dark:border-violet-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-violet-800 dark:text-violet-200 uppercase tracking-wide">Avg. Processing Time</p>
                <p class="text-3xl font-bold text-violet-900 dark:text-white mt-2 mb-1" id="avgProcessingTime">{{ $metrics['avg_processing_time'] ?? '0s' }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-violet-500 dark:bg-violet-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-bolt text-xs mr-1"></i>
                        <span>Efficient workflow</span>
                    </div>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-violet-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-bolt text-white text-xl"></i>
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

<!-- Search and Filter Section -->
<div class="filter-section p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Search Sales -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Sales</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" id="searchInput" placeholder="Search by customer name, sale ID, or medicine..." 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
            <select id="statusFilter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>

        <!-- Payment Method Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Payment Method</label>
            <select id="paymentFilter" class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                <option value="">All Payment Method</option>
                <option value="cash">Cash</option>
                <option value="card">Card</option>
                <option value="mobile_payment">Mobile Payment</option>
                <option value="bank_transfer">Bank Transfer</option>
            </select>
        </div>

        <!-- Date Filter -->
        <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
            <div class="relative">
                <input type="date" id="dateFilter" 
                       class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            </div>
        </div>
    </div>
</div>

<!-- Sales Navigation and Filters -->
<div class="flex items-center justify-between mb-6">
    <!-- Sales Filter Buttons -->
    <div class="flex items-center gap-3">
        <button id="incomingBtn" class="sales-filter-btn active px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
            <i class="fas fa-clock mr-2 text-sm"></i>Incoming (<span id="incomingCount">{{ $metrics['incoming_sales'] ?? 0 }}</span>)
        </button>
        <button id="processedBtn" class="sales-filter-btn px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
            <i class="fas fa-check mr-2 text-sm"></i>Processed (<span id="processedCount">{{ $metrics['processed_sales'] ?? 0 }}</span>)
        </button>
        <button id="allSalesBtn" class="sales-filter-btn px-4 py-2 rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
            <i class="fas fa-shopping-cart mr-2 text-sm"></i>All sales
        </button>
    </div>
</div>

<!-- Sales Grid -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mt-8">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-gray-600 dark:text-gray-400">
                <span>Showing {{ $salesData->firstItem() ?? 0 }} to {{ $salesData->lastItem() ?? 0 }} of {{ $salesData->total() }} sales</span>
            </div>
            <div class="flex gap-4">
                <button id="refreshBtn" class="no-print px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2 text-sm"></i>Refresh
                </button>
                <button id="printBtn" class="no-print px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm transition-all duration-200 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-print mr-2 text-sm"></i>Print
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
                    @foreach($salesData->where('status', 'pending') as $sale)
                        <div class="sale-card {{ $sale->status }} bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300">
                            <!-- Sale Header -->
                            <div class="mb-4">
                                <!-- Sale ID - Prominently displayed at top -->
                                <div class="flex items-center mb-3">
                                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $sale->sale_number }}</h3>
                                    @if($sale->status === 'completed')
                                        <div class="w-3 h-3 bg-green-500 rounded-full ml-2"></div>
                                    @endif
                                </div>
                                
                                <!-- Status and Payment Method - Below the ID -->
                                <div class="flex gap-2">
                                    <span class="status-badge status-{{ $sale->status }} px-3 py-1 rounded-full text-xs font-semibold">
                                        @if($sale->status === 'pending')
                                            <i class="fas fa-clock mr-1"></i>Pending
                                        @else
                                            <i class="fas fa-check mr-1"></i>Completed
                                        @endif
                                    </span>
                                    <span class="payment-badge {{ $sale->payment_method }} px-3 py-1 rounded-full text-xs font-semibold">
                                        @if($sale->payment_method === 'cash')
                                            <i class="fas fa-dollar-sign mr-1"></i>Br Cash
                                        @elseif($sale->payment_method === 'mobile_payment')
                                            <i class="fas fa-mobile-alt mr-1"></i>mobile_money
                                        @elseif($sale->payment_method === 'card')
                                            <i class="fas fa-credit-card mr-1"></i>Card
                                        @else
                                            <i class="fas fa-university mr-1"></i>{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Total Amount -->
                            <div class="total-amount-section rounded-lg p-4 mb-4">
                                <div class="text-center">
                                    <p class="total-amount-value text-2xl font-bold">{{ number_format($sale->total_amount, 2) }} Birr</p>
                                    <p class="total-amount-label text-sm font-medium">Total Amount</p>
                                </div>
                            </div>

                            <!-- Items Section -->
                            <div class="mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Items</span>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">{{ $sale->items->count() }} Items</span>
                                </div>
                                <div class="max-h-32 overflow-y-auto">
                                    @foreach($sale->items as $item)
                                        <div class="flex items-center justify-between py-1">
                                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $item->medicine->name ?? 'Unknown' }} {{ $item->medicine->strength ?? '' }} {{ $item->medicine->form ?? '' }}</span>
                                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $item->quantity }}x</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Transaction Time -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center text-gray-500 dark:text-gray-400">
                                    <i class="fas fa-clock mr-2"></i>
                                    <span class="text-sm">{{ $sale->sale_date->format('g:i:s A') }}</span>
                                </div>
                                @if($sale->status === 'completed')
                                    <span class="text-sm text-gray-500 dark:text-gray-400">Processing Time 0s</span>
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            @if($sale->status === 'pending')
                                <button onclick="processSale({{ $sale->id }})" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center mb-3">
                                    <i class="fas fa-check mr-2"></i>Process
                                </button>
                            @endif
                            
                            <div class="flex gap-2">
                                <button onclick="viewSale({{ $sale->id }})" class="flex-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-600 flex items-center justify-center">
                                    <i class="fas fa-eye mr-2"></i>View
                                </button>
                                <button onclick="generateReceipt({{ $sale->id }})" class="flex-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-600 flex items-center justify-center">
                                    <i class="fas fa-print mr-2"></i>{{ $sale->status === 'completed' ? 'Reprint' : 'Generate Receipt' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-span-full text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No sales found</h3>
                        <p class="text-gray-500 dark:text-gray-400">No sales match your current filters.</p>
                    </div>
                @endif
            </div>
            
            <!-- Table View -->
            <div id="tableView" class="hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Sale ID</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Payment</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Items</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @if($salesData && $salesData->count() > 0)
                                @foreach($salesData->where('status', 'pending') as $sale)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-8 h-8 bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg flex items-center justify-center mr-3">
                                                    <i class="fas fa-receipt text-white text-xs"></i>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $sale->sale_number }}</div>
                                                    <div class="text-sm text-gray-500 dark:text-gray-400">#{{ $sale->id }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $sale->customer_name ?? 'Walk-in Customer' }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge status-{{ $sale->status }} px-2 py-1 rounded-full text-xs font-semibold">
                                                @if($sale->status === 'pending')
                                                    <i class="fas fa-clock mr-1"></i>Pending
                                                @else
                                                    <i class="fas fa-check mr-1"></i>Completed
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="payment-badge {{ $sale->payment_method }} px-2 py-1 rounded-full text-xs font-semibold">
                                                @if($sale->payment_method === 'cash')
                                                    <i class="fas fa-dollar-sign mr-1"></i>Br Cash
                                                @elseif($sale->payment_method === 'mobile_payment')
                                                    <i class="fas fa-mobile-alt mr-1"></i>Mobile
                                                @elseif($sale->payment_method === 'card')
                                                    <i class="fas fa-credit-card mr-1"></i>Card
                                                @else
                                                    <i class="fas fa-university mr-1"></i>{{ ucfirst(str_replace('_', ' ', $sale->payment_method)) }}
                                                @endif
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ number_format($sale->total_amount, 2) }} Birr</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-white">{{ $sale->items->count() }} items</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $sale->sale_date->format('g:i A') }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                @if($sale->status === 'pending')
                                                    <button onclick="processSale({{ $sale->id }})" class="bg-orange-500 hover:bg-orange-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors duration-200">
                                                        <i class="fas fa-check mr-1"></i>Process
                                                    </button>
                                                @endif
                                                <button onclick="viewSale({{ $sale->id }})" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors duration-200">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </button>
                                                <button onclick="generateReceipt({{ $sale->id }})" class="bg-gray-500 hover:bg-gray-600 text-white px-3 py-1 rounded text-xs font-medium transition-colors duration-200">
                                                    <i class="fas fa-print mr-1"></i>{{ $sale->status === 'completed' ? 'Reprint' : 'Receipt' }}
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No sales found</h3>
                                        <p class="text-gray-500 dark:text-gray-400">No sales match your current filters.</p>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
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


<!-- Import/Export Sales Modal -->
<div id="importExportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import/Export Sales</h2>
            </div>
            <button onclick="hideImportExportModal()" class="w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
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

<!-- View Sale Modal -->
<div id="viewSaleModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
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

<!-- Receipt Modal -->
<div id="receiptModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-print text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Receipt</h2>
                    <p class="text-gray-600 dark:text-gray-400">Print or Save Receipt</p>
                </div>
            </div>
            <button onclick="hideReceiptModal()" class="w-10 h-10 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Receipt Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <div id="receiptContent" class="bg-white dark:bg-gray-800 p-8 font-mono text-sm text-gray-900 dark:text-gray-100">
                <!-- Receipt content will be populated by JavaScript -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideReceiptModal()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Close
            </button>
            <div class="flex gap-3">
                <button onclick="printReceipt()" class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <button onclick="downloadReceipt()" class="px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-download mr-2"></i>Download
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
/**
 * Cashier Station Controller
 * Handles real-time updates, filtering, and sales processing
 * Optimized for shared hosting with efficient caching
 */
(function() {
    'use strict';
    
    // State management
    let currentFilter = 'pending'; // Default to showing incoming sales
    let currentSales = @json($salesData ?? []);
    let isLoading = false;
    let currentView = 'cards'; // 'cards' or 'table'
    
    // Pagination Variables
    let currentPage = 1;
    let totalPages = 1;
    let pagination = null;
    
    // Simple AJAX polling (like Sales page)
    let refreshInterval = null;
    
    // Initialize cashier station when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeCashierStation();
        
        // Prevent any accidental form submissions to cashier route
        document.addEventListener('submit', function(e) {
            if (e.target.action && e.target.action.includes('/cashier') && e.target.method === 'POST') {
                e.preventDefault();
                console.error('Blocked POST submission to cashier route:', e.target.action);
                alert('Form submission blocked. Please use the pagination buttons instead.');
            }
        });
    });
    
    // Cleanup refresh interval when page is unloaded
    window.addEventListener('beforeunload', function() {
        if (refreshInterval) {
            clearInterval(refreshInterval);
            console.log('Refresh interval cleared');
        }
    });
    
    /**
     * Initialize cashier station functionality
     */
    async function initializeCashierStation() {
        console.log('Initializing cashier station...');
        
        // Initialize filter buttons
        initializeFilterButtons();
        
        // Initialize search and filters
        initializeFilters();
        
        // Initialize action buttons
        initializeActionButtons();
        
        // Initialize view toggle
        initializeViewToggle();
        
        // Initialize import/export modal
        initializeImportExportModal();
        
        // Initialize refresh button
        initializeRefreshButton();
        
        // Initialize print button
        initializePrintButton();
        
        // Set up real-time updates
        setupRealTimeUpdates();
        
        // Initialize pagination
        initializeServerSidePagination();
        
        // Button counts will be updated by metrics (server-side data)
        
        // Load initial data and apply filter (optimized)
        await loadInitialData();
        applyFilters();
        
        console.log('Cashier station initialized successfully');
    }
    
    /**
     * Initialize filter buttons
     */
    function initializeFilterButtons() {
        const buttons = ['incomingBtn', 'processedBtn', 'allSalesBtn'];
        
        buttons.forEach(buttonId => {
            document.getElementById(buttonId).addEventListener('click', function() {
                // Remove active class from all buttons
                buttons.forEach(id => {
                    document.getElementById(id).classList.remove('active');
                });
                
                // Add active class to clicked button
                this.classList.add('active');
                
                // Update current filter
                if (buttonId === 'incomingBtn') {
                    currentFilter = 'pending';
                } else if (buttonId === 'processedBtn') {
                    currentFilter = 'completed';
                } else {
                    currentFilter = 'all';
                }
                
                // Apply filters to current data
                applyFilters();
            });
        });
    }
    
    /**
     * Initialize search and filter inputs
     */
    function initializeFilters() {
        const searchInput = document.getElementById('searchInput');
        const statusFilter = document.getElementById('statusFilter');
        const paymentFilter = document.getElementById('paymentFilter');
        const dateFilter = document.getElementById('dateFilter');
        
        // Debounced search
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                applyFilters();
            }, 300);
        });
        
        // Filter change handlers
        [statusFilter, paymentFilter, dateFilter].forEach(filter => {
            filter.addEventListener('change', applyFilters);
        });
    }
    
    /**
     * Initialize action buttons
     */
    function initializeActionButtons() {
        // Import/Export button
        document.getElementById('importExportBtn').addEventListener('click', function() {
            showImportExportModal();
        });
    }
    
    /**
     * Initialize view toggle functionality
     */
    function initializeViewToggle() {
        const cardsViewBtn = document.getElementById('cardsViewBtn');
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardView = document.getElementById('cardView');
        const tableView = document.getElementById('tableView');
        
        // Cards view button
        cardsViewBtn.addEventListener('click', function() {
            switchToCardsView();
        });
        
        // Table view button
        tableViewBtn.addEventListener('click', function() {
            switchToTableView();
        });
        
        // Initialize with cards view
        switchToCardsView();
    }
    
    /**
     * Switch to cards view
     */
    function switchToCardsView() {
        currentView = 'cards';
        
        // Update button states
        document.getElementById('cardsViewBtn').className = 'px-4 py-2 bg-orange-500 text-white rounded-md font-semibold text-sm transition-all duration-200 flex items-center';
        document.getElementById('tableViewBtn').className = 'px-4 py-2 bg-transparent text-gray-700 dark:text-gray-200 rounded-md font-medium text-sm transition-all duration-200 flex items-center hover:bg-gray-200 dark:hover:bg-gray-600';
        
        // Show/hide views
        document.getElementById('cardView').classList.remove('hidden');
        document.getElementById('tableView').classList.add('hidden');
        
        console.log('Switched to cards view');
    }
    
    /**
     * Switch to table view
     */
    function switchToTableView() {
        currentView = 'table';
        
        // Update button states
        document.getElementById('tableViewBtn').className = 'px-4 py-2 bg-orange-500 text-white rounded-md font-semibold text-sm transition-all duration-200 flex items-center';
        document.getElementById('cardsViewBtn').className = 'px-4 py-2 bg-transparent text-gray-700 dark:text-gray-200 rounded-md font-medium text-sm transition-all duration-200 flex items-center hover:bg-gray-200 dark:hover:bg-gray-600';
        
        // Show/hide views
        document.getElementById('tableView').classList.remove('hidden');
        document.getElementById('cardView').classList.add('hidden');
        
        console.log('Switched to table view');
    }
    
    // Import/Export Modal Variables
    let currentTab = 'import';
    let isImporting = false;
    let isExporting = false;
    
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
        initializeModalActionButton();
        
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
                // Create template with proper headers only (no sample data)
                const templateData = [
                    ['Sale ID', 'Customer Name', 'Items', 'Quantity', 'Unit Price', 'Total Amount', 'Payment Method', 'Status', 'Sale Date']
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
     * Initialize modal action button
     */
    function initializeModalActionButton() {
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
        const printUrl = `/cashier/print?${params.toString()}`;
        const printWindow = window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
        
        if (printWindow) {
            printWindow.focus();
            window.NotificationService.success('Cashier print report opened in new window.');
        } else {
            window.NotificationService.error('Unable to open print window. Please check your popup blocker.');
        }
    }
    
    /**
     * Show Import/Export Modal
     */
    function showImportExportModal() {
        const modal = document.getElementById('importExportModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    /**
     * Hide Import/Export Modal
     */
    function hideImportExportModal() {
        const modal = document.getElementById('importExportModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    
    /**
     * Switch tab functionality
     */
    function switchTab(tab) {
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
        
        // Show selected content and update button
        const actionButton = document.getElementById('actionButton');
        
        if (tab === 'import') {
            document.getElementById('importContent').classList.remove('hidden');
            document.getElementById('importTab').classList.remove('border-transparent', 'text-gray-500', 'font-medium');
            document.getElementById('importTab').classList.add('border-orange-500', 'text-orange-500', 'font-semibold');
            actionButton.innerHTML = '<i class="fas fa-download mr-2"></i>Import File';
        } else if (tab === 'export') {
            document.getElementById('exportContent').classList.remove('hidden');
            document.getElementById('exportTab').classList.remove('border-transparent', 'text-gray-500', 'font-medium');
            document.getElementById('exportTab').classList.add('border-orange-500', 'text-orange-500', 'font-semibold');
            actionButton.innerHTML = '<i class="fas fa-upload mr-2"></i>Export Data';
        } else if (tab === 'print') {
            document.getElementById('printContent').classList.remove('hidden');
            document.getElementById('printTab').classList.remove('border-transparent', 'text-gray-500', 'font-medium');
            document.getElementById('printTab').classList.add('border-orange-500', 'text-orange-500', 'font-semibold');
            actionButton.innerHTML = '<i class="fas fa-print mr-2"></i>Print Report';
        }
    }
    
    // Make functions globally available
    window.showImportExportModal = showImportExportModal;
    window.hideImportExportModal = hideImportExportModal;
    window.switchTab = switchTab;
    
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
         * Initialize notification container
         */
        init() {
            this.createContainer();
            this.setupStyles();
        }

        /**
         * Create notification container
         */
        createContainer() {
            this.container = document.createElement('div');
            this.container.id = 'notification-container';
            this.container.className = 'fixed top-4 right-4 z-[9999] space-y-2 w-auto';
            document.body.appendChild(this.container);
        }

        /**
         * Setup notification styles - matches Sales page exactly
         */
        setupStyles() {
            const style = document.createElement('style');
            style.textContent = `
                .notification-item {
                    transform: translateX(100%);
                    transition: all ${this.animationDuration}ms ease-in-out;
                    width: fit-content;
                    min-width: 300px;
                    max-width: 400px;
                }
                .notification-item.translate-x-0 {
                    transform: translateX(0);
                }
                #notification-container {
                    width: auto !important;
                    max-width: 400px;
                }
            `;
            document.head.appendChild(style);
        }

        /**
         * Show notification - matches Sales page exactly
         */
        show(message, type = 'info', duration = null) {
            const notification = this.createNotification(message, type);
            this.addToQueue(notification);
            this.animateIn(notification);
            this.scheduleRemoval(notification, duration);
            return notification;
        }

        /**
         * Create notification element - matches Sales page exactly
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
                <div class="flex items-center p-4 rounded-lg shadow-lg max-w-sm w-auto">
                    <i class="fas ${
                        type === 'success' ? 'fa-check-circle' :
                        type === 'error' ? 'fa-exclamation-circle' :
                        type === 'warning' ? 'fa-exclamation-triangle' :
                        'fa-info-circle'
                    } mr-3 flex-shrink-0"></i>
                    <span class="flex-1 text-sm font-medium whitespace-nowrap">${message}</span>
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
         * Add notification to queue - matches Sales page
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
         * Animate notification in - matches Sales page
         */
        animateIn(notification) {
            requestAnimationFrame(() => {
                notification.classList.remove('translate-x-full');
                notification.classList.add('translate-x-0');
            });
        }

        /**
         * Schedule notification removal - matches Sales page
         */
        scheduleRemoval(notification, duration) {
            const autoRemoveDuration = duration || this.defaultDuration;
            if (autoRemoveDuration > 0) {
                setTimeout(() => {
                    this.close(notification);
                }, autoRemoveDuration);
            }
        }

        /**
         * Close notification - matches Sales page
         */
        close(notification) {
            if (!notification || !notification.parentNode) return;

            notification.classList.add('translate-x-full');
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
                this.notifications = this.notifications.filter(n => n !== notification);
            }, this.animationDuration);
        }

        /**
         * Remove oldest notification
         */
        removeOldest() {
            if (this.notifications.length > 0) {
                this.close(this.notifications[0]);
            }
        }

        /**
         * Clear all notifications
         */
        clear() {
            this.notifications.forEach(notification => {
                this.close(notification);
            });
        }

        /**
         * Success notification
         */
        success(message, duration = null) {
            return this.show(message, 'success', duration);
        }

        /**
         * Error notification
         */
        error(message, duration = null) {
            return this.show(message, 'error', duration);
        }

        /**
         * Warning notification
         */
        warning(message, duration = null) {
            return this.show(message, 'warning', duration);
        }

        /**
         * Info notification
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
            window.NotificationService.success('Cashier page loaded successfully!');
        }, 1000);
        
        // Test view functionality with first available sale (disabled for production)
        // setTimeout(() => {
        //     if (currentSales && currentSales.length > 0) {
        //         console.log('Testing view functionality with first sale:', currentSales[0]);
        //         // Uncomment the line below to test the view functionality
        //         // window.viewSale(currentSales[0].id);
        //     }
        // }, 2000);
    });
    
    /**
     * Initialize refresh button functionality
     */
    function initializeRefreshButton() {
        const refreshBtn = document.getElementById('refreshBtn');
        
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                handleRefresh();
            });
        }
    }
    
    /**
     * Initialize print button functionality
     */
    function initializePrintButton() {
        const printBtn = document.getElementById('printBtn');
        
        if (printBtn) {
            printBtn.addEventListener('click', function() {
                window.print();
            });
        }
    }
    
    /**
     * Handle refresh button click - matches Sales page implementation
     */
    async function handleRefresh() {
        if (isLoading) return;
        
        const refreshBtn = document.getElementById('refreshBtn');
        const originalText = refreshBtn.innerHTML;
        
        // Show loading state
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-sm"></i>Refreshing...';
        refreshBtn.disabled = true;
        
        try {
            // Refresh the data
            await refreshData();
            
            // Show success notification (only if not called from createSale)
            if (refreshBtn && window.NotificationService) {
                window.NotificationService.success('Cashier data refreshed successfully!');
            }
            
        } catch (error) {
            console.error('Failed to refresh cashier data:', error);
            
            // Show error notification
            if (window.NotificationService) {
                window.NotificationService.error('Failed to refresh data. Please try again.');
            }
        } finally {
            // Reset button state
            if (refreshBtn) {
                refreshBtn.innerHTML = originalText;
                refreshBtn.disabled = false;
            }
        }
    }
    
    /**
     * Apply current filters to sales data
     */
    function applyFilters() {
        if (isLoading) return;
        
        console.log('applyFilters called with currentFilter:', currentFilter);
        console.log('currentSales length:', currentSales ? currentSales.length : 'undefined');
        
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const paymentFilter = document.getElementById('paymentFilter').value;
        const dateFilter = document.getElementById('dateFilter').value;
        
        let filteredSales = currentSales;
        
        // Apply primary status filter (from button clicks)
        if (currentFilter === 'pending') {
            filteredSales = filteredSales.filter(sale => sale.status === 'pending');
        } else if (currentFilter === 'completed') {
            filteredSales = filteredSales.filter(sale => sale.status === 'completed');
        }
        // If currentFilter is 'all', show all sales
        
        // Apply additional filters
        if (statusFilter) {
            filteredSales = filteredSales.filter(sale => sale.status === statusFilter);
        }
        
        if (paymentFilter) {
            filteredSales = filteredSales.filter(sale => sale.payment_method === paymentFilter);
        }
        
        if (dateFilter) {
            const filterDate = new Date(dateFilter).toDateString();
            filteredSales = filteredSales.filter(sale => {
                const saleDate = new Date(sale.sale_date).toDateString();
                return saleDate === filterDate;
            });
        }
        
        // Apply search filter
        if (searchTerm) {
            filteredSales = filteredSales.filter(sale => {
                return sale.sale_number.toLowerCase().includes(searchTerm) ||
                       (sale.customer_name && sale.customer_name.toLowerCase().includes(searchTerm)) ||
                       (sale.customer_phone && sale.customer_phone.includes(searchTerm));
            });
        }
        
        // Render filtered sales
        console.log('Filtered sales count:', filteredSales.length);
        console.log('Filtered sales:', filteredSales);
        renderSales(filteredSales);
    }
    
    /**
     * Render sales cards
     */
    function renderSales(sales) {
        const container = document.getElementById('cardView');
        
        if (!container) {
            console.error('cardView container not found');
            return;
        }
        
        if (sales.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-shopping-cart text-gray-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No sales found</h3>
                    <p class="text-gray-500 dark:text-gray-400">No sales match your current filters.</p>
                </div>
            `;
            return;
        }
        
        container.innerHTML = sales.map(sale => createSaleCard(sale)).join('');
    }
    
    /**
     * Create sale card HTML
     */
    function createSaleCard(sale) {
        const statusClass = sale.status === 'pending' ? 'pending' : 'completed';
        const statusIcon = sale.status === 'pending' ? 'fa-clock' : 'fa-check';
        const statusText = sale.status === 'pending' ? 'Pending' : 'Completed';
        
        const paymentIcon = sale.payment_method === 'cash' ? 'fa-dollar-sign' : 
                           sale.payment_method === 'mobile_payment' ? 'fa-mobile-alt' : 
                           sale.payment_method === 'card' ? 'fa-credit-card' : 'fa-university';
        
        const paymentText = sale.payment_method === 'cash' ? '$ Cash' :
                           sale.payment_method === 'mobile_payment' ? 'mobile_money' :
                           sale.payment_method === 'card' ? 'Card' :
                           sale.payment_method.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
        
        const processButton = sale.status === 'pending' ? 
            `<button onclick="processSale(${sale.id})" class="w-full bg-orange-500 hover:bg-orange-600 text-white font-semibold py-3 px-4 rounded-lg transition-all duration-200 flex items-center justify-center mb-3">
                <i class="fas fa-check mr-2"></i>Process
            </button>` : '';
        
        const receiptButtonText = sale.status === 'completed' ? 'Reprint' : 'Generate Receipt';
        
        return `
            <div class="sale-card ${statusClass} bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300">
                <div class="mb-4">
                    <!-- Sale ID - Prominently displayed at top -->
                    <div class="flex items-center mb-3">
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">${sale.sale_number}</h3>
                        ${sale.status === 'completed' ? '<div class="w-3 h-3 bg-green-500 rounded-full ml-2"></div>' : ''}
                    </div>
                    
                    <!-- Status and Payment Method - Below the ID -->
                    <div class="flex gap-2">
                        <span class="status-badge status-${sale.status} px-3 py-1 rounded-full text-xs font-semibold">
                            <i class="fas ${statusIcon} mr-1"></i>${statusText}
                        </span>
                        <span class="payment-badge ${sale.payment_method} px-3 py-1 rounded-full text-xs font-semibold">
                            <i class="fas ${paymentIcon} mr-1"></i>${paymentText}
                        </span>
                    </div>
                </div>

                <div class="total-amount-section rounded-lg p-4 mb-4">
                    <div class="text-center">
                        <p class="total-amount-value text-2xl font-bold">${parseFloat(sale.total_amount).toFixed(2)} Birr</p>
                        <p class="total-amount-label text-sm font-medium">Total Amount</p>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Items</span>
                        <span class="text-sm text-gray-500 dark:text-gray-400">${sale.items ? sale.items.length : 0} Items</span>
                    </div>
                    <div class="max-h-32 overflow-y-auto">
                        ${sale.items ? sale.items.map(item => `
                            <div class="flex items-center justify-between py-1">
                                <span class="text-sm text-gray-600 dark:text-gray-400">${item.medicine ? item.medicine.name : 'Unknown'} ${item.medicine ? (item.medicine.strength || '') : ''} ${item.medicine ? (item.medicine.form || '') : ''}</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">${item.quantity}x</span>
                            </div>
                        `).join('') : ''}
                    </div>
                </div>

                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center text-gray-500 dark:text-gray-400">
                        <i class="fas fa-clock mr-2"></i>
                        <span class="text-sm">${new Date(sale.sale_date).toLocaleTimeString()}</span>
                    </div>
                    ${sale.status === 'completed' ? '<span class="text-sm text-gray-500 dark:text-gray-400">Processing Time 0s</span>' : ''}
                </div>

                ${processButton}
                
                <div class="flex gap-2">
                    <button onclick="viewSale(${sale.id})" class="flex-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-600 flex items-center justify-center">
                        <i class="fas fa-eye mr-2"></i>View
                    </button>
                    <button onclick="generateReceipt(${sale.id})" class="flex-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-medium py-2 px-4 rounded-lg transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-600 flex items-center justify-center">
                        <i class="fas fa-print mr-2"></i>${receiptButtonText}
                    </button>
                </div>
            </div>
        `;
    }
    
    /**
     * Set up real-time updates using simple AJAX polling (like Sales page)
     * This is the EXACT same approach that works perfectly in Sales page
     */
    function setupRealTimeUpdates() {
        console.log('Setting up AJAX polling for real-time updates (like Sales page)');
        setupAJAXPolling();
    }
    
    /**
     * Initialize Server-Sent Events for real-time updates
     */
    function initializeSSE() {
        if (typeof EventSource === 'undefined') {
            console.log('EventSource not supported, using AJAX fallback');
            setupAJAXFallback();
            return;
        }
        
        try {
            const eventSource = new EventSource('/cashier/api/stream');
            window.sseConnected = false;
            
            eventSource.onopen = function(event) {
                console.log('SSE connection opened');
                window.sseConnected = true;
                resetReconnectionState(); // Reset reconnection attempts on successful connection
                hideConnectionStatus();
            };
            
            eventSource.onmessage = function(event) {
                try {
                    const data = JSON.parse(event.data);
                    handleSSEMessage(data);
                } catch (error) {
                    console.error('Error parsing SSE message:', error);
                }
            };
            
            eventSource.onerror = function(event) {
                console.error('SSE connection error:', event);
                window.sseConnected = false;
                showConnectionStatus('Connection lost, attempting to reconnect...');
                
                // Attempt to reconnect after 5 seconds
                setTimeout(() => {
                    if (!window.sseConnected) {
                        console.log('Attempting SSE reconnection...');
                        eventSource.close();
                        initializeSSE();
                    }
                }, 5000);
            };
            
            // Store reference for cleanup
            window.sseEventSource = eventSource;
            
        } catch (error) {
            console.error('Failed to initialize SSE:', error);
            setupAJAXFallback();
        }
    }
    
    /**
     * Handle incoming SSE messages
     */
    function handleSSEMessage(data) {
        console.log('SSE message received:', data);
        
        switch (data.type) {
            case 'cashier_update':
                if (data.data.metrics) {
                    updateMetrics(data.data.metrics);
                }
                if (data.data.sales_count !== undefined) {
                    // Only update button counts, don't refresh full sales data
                    // This prevents the pagination issue
                    updateSalesCountFromSSE(data.data.sales_count);
                }
                break;
                
            case 'heartbeat':
                console.log('SSE heartbeat received');
                hideConnectionStatus();
                break;
                
            case 'close':
                console.log('SSE connection closed:', data.message);
                window.sseConnected = false;
                showConnectionStatus('Connection closed, reconnecting...');
                
                // Auto-reconnect with exponential backoff
                attemptReconnection();
                break;
                
            case 'error':
                console.error('SSE error:', data.message);
                showConnectionStatus('Server error: ' + data.message);
                break;
        }
    }
    
    /**
     * Setup AJAX polling for real-time updates (EXACT same as Sales page)
     * Simple, reliable approach that works perfectly
     */
    function setupAJAXPolling() {
        console.log('Setting up AJAX polling (EXACT same as Sales page)');
        
        // Update data every 30 seconds (same as Sales page)
        refreshInterval = setInterval(() => {
            console.log('Auto-refreshing data...');
            refreshDataSimple();
        }, 30000);
        
        // Also add manual refresh button functionality (like Sales page)
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                console.log('Manual refresh triggered');
                refreshDataSimple();
            });
        }
    }
    
    /**
     * Simple data refresh function (EXACT same approach as Sales page)
     * No SSE complexity - just simple AJAX calls
     */
    function refreshDataSimple() {
        console.log('Refreshing data (simple approach like Sales page)...');
        
        // Show loading state if refresh button exists
        const refreshBtn = document.getElementById('refreshBtn');
        let originalText = '';
        if (refreshBtn) {
            originalText = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Refreshing...';
            refreshBtn.disabled = true;
        }
        
        // Fetch fresh data from server (same approach as Sales page)
        Promise.all([
            fetch('/cashier/api/metrics'),
            fetch(`/cashier/api/sales?page=${getCurrentPageFromURL()}`)
        ])
        .then(([metricsResponse, salesResponse]) => {
            return Promise.all([
                metricsResponse.json(),
                salesResponse.json()
            ]);
        })
        .then(([metricsData, salesData]) => {
            // Update metrics
            if (metricsData.success) {
                updateMetrics(metricsData.data);
            }
            
            // Update sales data
            if (salesData.success) {
                currentSales = salesData.data;
                updateButtonCounts();
                applyFilters();
                
                if (salesData.pagination) {
                    updatePaginationInfo(salesData.pagination);
                }
            }
            
            console.log('Data refreshed successfully');
        })
        .catch(error => {
            console.error('Failed to refresh data:', error);
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
     * Show connection status indicator
     */
    function showConnectionStatus(message) {
        // Create or update connection status indicator
        let statusDiv = document.getElementById('connectionStatus');
        if (!statusDiv) {
            statusDiv = document.createElement('div');
            statusDiv.id = 'connectionStatus';
            statusDiv.className = 'fixed top-4 right-4 bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-2 rounded shadow-lg z-50';
            document.body.appendChild(statusDiv);
        }
        statusDiv.textContent = message;
        statusDiv.classList.remove('hidden');
    }
    
    /**
     * Hide connection status indicator
     */
    function hideConnectionStatus() {
        const statusDiv = document.getElementById('connectionStatus');
        if (statusDiv) {
            statusDiv.classList.add('hidden');
        }
    }
    
    /**
     * Refresh only sales data (optimized for SSE updates)
     * Maintains current page context to prevent pagination issues
     */
    async function refreshSalesData() {
        if (isLoading) return;
        
        try {
            // Include current page in the request to maintain pagination
            const currentPage = getCurrentPageFromURL();
            const salesResponse = await fetch(`/cashier/api/sales?page=${currentPage}`);
            const salesData = await salesResponse.json();
            
            if (salesData.success) {
                currentSales = salesData.data;
                // Don't update button counts here - metrics handle the totals
                applyFilters();
                
                // Update pagination info if available
                if (salesData.pagination) {
                    updatePaginationInfo(salesData.pagination);
                }
                
                console.log(`Sales data refreshed via SSE (page ${currentPage})`);
            }
        } catch (error) {
            console.error('Failed to refresh sales data:', error);
        }
    }
    
    /**
     * Get current page from URL
     */
    function getCurrentPageFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        return parseInt(urlParams.get('page')) || 1;
    }
    
    /**
     * Update pagination information
     */
    function updatePaginationInfo(pagination) {
        const paginationInfo = document.querySelector('.pagination-info');
        if (paginationInfo && pagination) {
            paginationInfo.textContent = `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total || 0} results`;
        }
    }
    
    /**
     * Update sales count from SSE without refreshing full data
     * This prevents the pagination issue where 16 sales becomes 12
     */
    function updateSalesCountFromSSE(salesCount) {
        console.log('Updating sales count from SSE:', salesCount);
        
        // Update the total count in pagination info if available
        const paginationInfo = document.querySelector('.pagination-info');
        if (paginationInfo) {
            const currentText = paginationInfo.textContent;
            const matches = currentText.match(/Showing (\d+) to (\d+) of (\d+) results/);
            if (matches) {
                const newText = currentText.replace(/of \d+ results/, `of ${salesCount} results`);
                paginationInfo.textContent = newText;
            }
        }
        
        // Update button counts based on current filter
        updateButtonCounts();
        
        console.log('Sales count updated via SSE without data refresh');
    }
    
    /**
     * Attempt SSE reconnection with exponential backoff
     */
    function attemptReconnection() {
        if (reconnectAttempts >= maxReconnectAttempts) {
            console.log('Max reconnection attempts reached, falling back to AJAX');
            showConnectionStatus('SSE unavailable, using AJAX fallback');
            setupAJAXFallback();
            return;
        }
        
        reconnectAttempts++;
        const delay = reconnectDelay * Math.pow(2, reconnectAttempts - 1); // Exponential backoff
        
        console.log(`Attempting SSE reconnection ${reconnectAttempts}/${maxReconnectAttempts} in ${delay}ms`);
        showConnectionStatus(`Reconnecting... (${reconnectAttempts}/${maxReconnectAttempts})`);
        
        setTimeout(() => {
            if (window.sseEventSource) {
                window.sseEventSource.close();
            }
            initializeSSE();
        }, delay);
    }
    
    /**
     * Reset reconnection state on successful connection
     */
    function resetReconnectionState() {
        reconnectAttempts = 0;
        reconnectDelay = 1000;
    }
    
    /**
     * Load initial data (optimized for performance)
     */
    async function loadInitialData() {
        if (isLoading) return;
        
        isLoading = true;
        console.log('Loading initial data...');
        
        try {
            // Get current page to maintain pagination context
            const currentPage = getCurrentPageFromURL();
            
            // Only load sales data initially (metrics are already loaded from server-side)
            const salesResponse = await fetch(`/cashier/api/sales?page=${currentPage}`);
            const salesData = await salesResponse.json();
            
            if (salesData.success) {
                currentSales = salesData.data;
                // Don't update button counts here - metrics handle the totals
                
                // Update pagination info to maintain correct display
                if (salesData.pagination) {
                    updatePaginationInfo(salesData.pagination);
                }
                
                console.log(`Initial data loaded successfully (page ${currentPage})`);
            }
            
        } catch (error) {
            console.error('Failed to load initial data:', error);
        } finally {
            isLoading = false;
        }
    }
    
    /**
     * Refresh data from server
     * Maintains pagination context to prevent the 16->12 sales issue
     */
    async function refreshData() {
        if (isLoading) return;
        
        isLoading = true;
        console.log('Refreshing data from server...');
        
        try {
            // Get current page to maintain pagination context
            const currentPage = getCurrentPageFromURL();
            
            const [metricsResponse, salesResponse] = await Promise.all([
                fetch('/cashier/api/metrics'),
                fetch(`/cashier/api/sales?page=${currentPage}`)
            ]);
            
            const metricsData = await metricsResponse.json();
            const salesData = await salesResponse.json();
            
            if (metricsData.success) {
                updateMetrics(metricsData.data);
            }
            
            if (salesData.success) {
                currentSales = salesData.data;
                // Don't update button counts here - metrics handle the totals
                applyFilters();
                
                // Update pagination info to maintain correct display
                if (salesData.pagination) {
                    updatePaginationInfo(salesData.pagination);
                }
                
                console.log(`Sales data refreshed successfully (page ${currentPage})`);
            }
            
        } catch (error) {
            console.error('Failed to refresh data:', error);
        } finally {
            isLoading = false;
        }
    }
    
    /**
     * Update metrics display with TOTAL counts (not paginated)
     */
    function updateMetrics(metrics) {
        document.getElementById('incomingSales').textContent = metrics.incoming_sales;
        document.getElementById('processedSales').textContent = metrics.processed_sales;
        document.getElementById('totalRevenue').textContent = 'Br ' + metrics.total_revenue;
        document.getElementById('avgProcessingTime').textContent = metrics.avg_processing_time;
        
        // Update filter button counts with TOTAL metrics (not current page)
        document.getElementById('incomingCount').textContent = metrics.incoming_sales;
        document.getElementById('processedCount').textContent = metrics.processed_sales;
        
        console.log('Metrics updated with TOTAL counts:', metrics);
    }
    
    /**
     * Update button counts based on TOTAL metrics (not current page data)
     * This ensures the counts show ALL sales across all pages
     */
    function updateButtonCounts() {
        // Get the metrics from the server-rendered data or use current metrics
        const incomingSalesEl = document.getElementById('incomingSales');
        const processedSalesEl = document.getElementById('processedSales');
        
        if (incomingSalesEl && processedSalesEl) {
            // Use the total metrics values (not current page data)
            const incomingCount = parseInt(incomingSalesEl.textContent) || 0;
            const processedCount = parseInt(processedSalesEl.textContent) || 0;
            
            document.getElementById('incomingCount').textContent = incomingCount;
            document.getElementById('processedCount').textContent = processedCount;
            
            console.log('Button counts updated with TOTAL metrics:', {
                incoming: incomingCount,
                processed: processedCount
            });
        }
    }
    
    
    // Global functions for sale actions
    window.processSale = async function(saleId) {
        try {
            // Show global loading spinner
            showActionLoading('processing');
            
            const response = await fetch(`/cashier/api/sales/${saleId}/process`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                // Update loading message
                updateLoadingMessage('Updating data...');
                
                // Refresh data and re-apply current filter
                await refreshData();
                applyFilters();
                updateButtonCounts();
                
                // Show success message
                showLoadingWithStyle('success', 'Sale processed successfully!');
                setTimeout(() => {
                    hideGlobalLoading();
                }, 1500);
            } else {
                hideGlobalLoading();
                alert('Failed to process sale: ' + data.message);
            }
            
        } catch (error) {
            console.error('Failed to process sale:', error);
            hideGlobalLoading();
            alert('Failed to process sale. Please try again.');
        }
    };
    
    window.viewSale = async function(saleId) {
        try {
            // Show global loading spinner
            showGlobalLoading('Loading sale details...');
            
            // Fetch sale details from server
            const response = await fetch(`/cashier/api/sales/${saleId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                
                if (data.success && data.data) {
                    showViewSaleModal(data.data);
                } else {
                    alert('Sale not found');
                }
            } else {
                throw new Error(`Failed to fetch sale details: ${response.status}`);
            }
            
        } catch (error) {
            console.error('Error fetching sale:', error);
            alert('Error loading sale details. Please try again.');
        } finally {
            // Hide global loading spinner
            hideGlobalLoading();
        }
    };
    
    /**
     * Show global loading spinner with download-like animation
     */
    function showGlobalLoading(message = 'Loading...') {
        // Remove existing loading if any
        hideGlobalLoading();
        
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'globalLoading';
        loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
        loadingDiv.innerHTML = `
            <div class="text-center">
                <!-- Pharmacy icon container -->
                <div class="relative w-16 h-16 mx-auto mb-6">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-pills text-orange-500 text-3xl animate-pulse"></i>
                    </div>
                </div>
                
                <!-- Progress text -->
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${message}</h3>
                </div>
                
                <!-- Horizontal progress bar -->
                <div class="w-64 mx-auto mb-4">
                    <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                        <div class="bg-gradient-to-r from-orange-400 to-orange-600 h-full rounded-full transition-all duration-300 ease-out" 
                             id="progressBar" style="width: 0%"></div>
                    </div>
                </div>
                
                <!-- Progress percentage and dots -->
                <div class="flex items-center justify-center space-x-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span id="loadingProgress">0</span>%
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse"></div>
                        <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 bg-orange-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(loadingDiv);
        
        // Start progress animation
        startProgressAnimation();
    }
    
    /**
     * Start progress animation for horizontal loading bar
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
     * Hide global loading spinner
     */
    function hideGlobalLoading() {
        const loadingDiv = document.getElementById('globalLoading');
        if (loadingDiv) {
            loadingDiv.remove();
        }
        
        // Clear any running animation
        if (window.loadingAnimation) {
            clearInterval(window.loadingAnimation);
            window.loadingAnimation = null;
        }
    }
    
    /**
     * Show action loading with different styles
     */
    function showActionLoading(action) {
        const messages = {
            'processing': 'Processing sale...',
            'loading': 'Loading details...',
            'saving': 'Saving changes...'
        };
        showGlobalLoading(messages[action] || 'Loading...');
    }
    
    /**
     * Update loading message
     */
    function updateLoadingMessage(message) {
        const loadingDiv = document.getElementById('globalLoading');
        if (loadingDiv) {
            const messageEl = loadingDiv.querySelector('h3');
            if (messageEl) {
                messageEl.textContent = message;
            }
        }
    }
    
    /**
     * Show loading with success/error style (download-like animation)
     */
    function showLoadingWithStyle(type, message) {
        hideGlobalLoading();
        
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'globalLoading';
        loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
        
        const colorClass = type === 'success' ? 'text-green-500' : 
                          type === 'error' ? 'text-red-500' : 
                          'text-orange-500';
        
        const iconClass = type === 'success' ? 'fa-check-circle' : 
                         type === 'error' ? 'fa-exclamation-triangle' : 
                         'fa-pills';
        
        loadingDiv.innerHTML = `
            <div class="text-center">
                <!-- Pharmacy icon container -->
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
                             id="progressBar" style="width: 100%; background: ${type === 'success' ? 'linear-gradient(to right, #10b981, #059669)' : type === 'error' ? 'linear-gradient(to right, #ef4444, #dc2626)' : 'linear-gradient(to right, #f97316, #ea580c)'}"></div>
                    </div>
                </div>
                
                <!-- Progress percentage and dots -->
                <div class="flex items-center justify-center space-x-4">
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <span id="loadingProgress">100</span>%
                    </div>
                    <div class="flex items-center space-x-1">
                        <div class="w-2 h-2 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-orange-500'} rounded-full animate-pulse"></div>
                        <div class="w-2 h-2 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-orange-500'} rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                        <div class="w-2 h-2 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-orange-500'} rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
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

    /**
     * Show view sale modal with sale details
     */
    function showViewSaleModal(sale) {
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
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Sale #${sale.sale_number || sale.id || 'N/A'}</h3>
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
                        <!-- Customer Information -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                <i class="fas fa-user mr-2 text-blue-500"></i>
                                Customer Information
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Name:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">${sale.customer_name || 'Walk-in Customer'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Phone:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">${sale.customer_phone || 'Not provided'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Email:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">${sale.customer_email || 'Not provided'}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Sale Information -->
                        <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                                <i class="fas fa-info-circle mr-2 text-green-500"></i>
                                Sale Information
                            </h4>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Date:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">${formatDate(sale.sale_date)}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Sold By:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">${sale.sold_by?.name || 'System'}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-gray-400">Prescription:</span>
                                    <span class="font-medium text-gray-900 dark:text-white">${sale.prescription_required ? 'Required' : 'Not Required'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sale Items -->
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <i class="fas fa-shopping-cart mr-2 text-purple-500"></i>
                            Sale Items
                        </h4>
                        <div class="space-y-3">
                            ${sale.items && sale.items.length > 0 ? sale.items.map(item => `
                                <div class="flex justify-between items-center p-3 bg-white dark:bg-gray-700 rounded-lg">
                                    <div class="flex-1">
                                        <h5 class="font-medium text-gray-900 dark:text-white">${item.medicine?.name || 'Medicine not found'}</h5>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">${item.medicine?.generic_name || ''} ${item.medicine?.strength || ''}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-medium text-gray-900 dark:text-white">${item.quantity || 0} × Br ${item.unit_price || '0.00'}</p>
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Br ${item.total_price || '0.00'}</p>
                                    </div>
                                </div>
                            `).join('') : '<p class="text-gray-500 dark:text-gray-400">No items in this sale</p>'}
                        </div>
                    </div>

                    <!-- Financial Summary -->
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 dark:from-green-900/20 dark:to-blue-900/20 rounded-xl p-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3 flex items-center">
                            <i class="fas fa-calculator mr-2 text-green-500"></i>
                            Financial Summary
                        </h4>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Subtotal:</span>
                                <span class="font-medium text-gray-900 dark:text-white">Br ${parseFloat(sale.subtotal || 0).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Tax:</span>
                                <span class="font-medium text-gray-900 dark:text-white">Br ${parseFloat(sale.tax_amount || 0).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Discount:</span>
                                <span class="font-medium text-gray-900 dark:text-white">Br ${parseFloat(sale.discount_amount || 0).toFixed(2)}</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-600 pt-2">
                                <div class="flex justify-between">
                                    <span class="text-lg font-semibold text-gray-900 dark:text-white">Total:</span>
                                    <span class="text-lg font-bold text-green-600 dark:text-green-400">Br ${parseFloat(sale.total_amount || 0).toFixed(2)}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    ${sale.notes ? `
                        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4">
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-2 flex items-center">
                                <i class="fas fa-sticky-note mr-2 text-yellow-500"></i>
                                Notes
                            </h4>
                            <p class="text-gray-700 dark:text-gray-300">${sale.notes}</p>
                        </div>
                    ` : ''}
                </div>
            `;
            
            modal.classList.remove('hidden');
        } else {
            alert('Error: Modal elements not found. Please refresh the page and try again.');
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

    // Make hideViewSaleModal globally accessible
    window.hideViewSaleModal = hideViewSaleModal;

    // Add click outside to close functionality
    document.addEventListener('click', function(event) {
        const viewModal = document.getElementById('viewSaleModal');
        const receiptModal = document.getElementById('receiptModal');
        
        // View Sale Modal
        if (viewModal && !viewModal.classList.contains('hidden')) {
            if (event.target === viewModal) {
                hideViewSaleModal();
            }
        }
        
        // Receipt Modal
        if (receiptModal && !receiptModal.classList.contains('hidden')) {
            if (event.target === receiptModal) {
                hideReceiptModal();
            }
        }
    });

    // Add escape key to close functionality
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const viewModal = document.getElementById('viewSaleModal');
            const receiptModal = document.getElementById('receiptModal');
            
            if (viewModal && !viewModal.classList.contains('hidden')) {
                hideViewSaleModal();
            } else if (receiptModal && !receiptModal.classList.contains('hidden')) {
                hideReceiptModal();
            }
        }
    });

    /**
     * Get sale status color class
     */
    function getSaleStatusColor(status) {
        switch (status) {
            case 'completed': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
            case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400';
            case 'cancelled': return 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400';
            default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
        }
    }

    /**
     * Get payment method color class
     */
    function getPaymentMethodColor(method) {
        switch (method) {
            case 'cash': return 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400';
            case 'card': return 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400';
            case 'mobile_money': return 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-400';
            default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400';
        }
    }

    /**
     * Format date for display
     */
    function formatDate(dateString) {
        if (!dateString) return 'N/A';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) return 'Invalid Date';
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            });
        } catch (error) {
            console.error('Error formatting date:', error);
            return 'Invalid Date';
        }
    }

    window.generateReceipt = function(saleId) {
        console.log('Generating receipt for sale:', saleId);
        showReceiptModal(saleId);
    };
    
    /**
     * Show receipt modal with sale data
     */
    async function showReceiptModal(saleId) {
        try {
            // Show loading spinner
            showGlobalLoading('Generating receipt...');
            
            // Fetch sale details
            const response = await fetch(`/cashier/api/sales/${saleId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                if (data.success && data.data) {
                    displayReceipt(data.data);
                } else {
                    alert('Sale not found');
                }
            } else {
                throw new Error('Failed to fetch sale details');
            }
            
        } catch (error) {
            console.error('Error generating receipt:', error);
            alert('Error generating receipt. Please try again.');
        } finally {
            hideGlobalLoading();
        }
    }
    
    /**
     * Display receipt in modal
     */
    function displayReceipt(sale) {
        const modal = document.getElementById('receiptModal');
        const content = document.getElementById('receiptContent');
        
        if (modal && content) {
            // Generate receipt HTML based on the design
            content.innerHTML = generateReceiptHTML(sale);
            modal.classList.remove('hidden');
        }
    }
    
    /**
     * Generate receipt HTML based on the design
     */
    function generateReceiptHTML(sale) {
        const currentDate = new Date();
        const dateStr = currentDate.toLocaleDateString('en-GB');
        const timeStr = currentDate.toLocaleTimeString('en-GB', { hour12: false });
        
        // Calculate totals
        const subtotal = parseFloat(sale.subtotal || 0);
        const taxAmount = parseFloat(sale.tax_amount || 0);
        const discountAmount = parseFloat(sale.discount_amount || 0);
        const totalAmount = parseFloat(sale.total_amount || 0);
        
        return `
            <div class="receipt-container max-w-sm mx-auto bg-white dark:bg-gray-800 p-4 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-gray-100">
                <!-- Header -->
                <div class="text-center mb-4">
                    <div class="text-xs mb-1">TIN: 0001335070</div>
                    <div class="font-bold text-sm mb-1">PHARMACY MANAGEMENT SYSTEM</div>
                    <div class="text-xs mb-1">CITY: ARBA MINCH, S/C: GAMO</div>
                    <div class="text-xs mb-1">KE: WUHAMINCH, H.NO: NEW</div>
                    <div class="text-xs mb-1">TEL: 0910919124</div>
                </div>
                
                <!-- Transaction Details -->
                <div class="mb-4">
                    <div class="text-xs mb-1">FS No: ${sale.sale_number || sale.id}</div>
                    <div class="text-xs mb-1">DATE: ${dateStr} ${timeStr}</div>
                    <div class="border-t border-gray-400 my-2"></div>
                    <div class="text-center text-xs font-bold mb-2">====== CASH INVOICE ======</div>
                    <div class="text-xs mb-1">Ref: ${sale.sale_number || sale.id}</div>
                    <div class="text-xs mb-1">Operator: ${sale.sold_by?.name || 'SYSTEM'}</div>
                    <div class="border-t border-gray-400 my-2"></div>
                </div>
                
                <!-- Items Header -->
                <div class="mb-2">
                    <div class="grid grid-cols-12 gap-1 text-xs font-bold">
                        <div class="col-span-5">Description</div>
                        <div class="col-span-2 text-center">Qty</div>
                        <div class="col-span-2 text-center">Price</div>
                        <div class="col-span-3 text-right">AMOUNT</div>
                    </div>
                    <div class="border-t border-gray-400 my-1"></div>
                </div>
                
                <!-- Items -->
                <div class="mb-4">
                    ${sale.items && sale.items.length > 0 ? sale.items.map(item => `
                        <div class="grid grid-cols-12 gap-1 text-xs mb-1">
                            <div class="col-span-5 truncate">${item.medicine?.name || 'Medicine'}</div>
                            <div class="col-span-2 text-center">${item.quantity || 0}</div>
                            <div class="col-span-2 text-center">${parseFloat(item.unit_price || 0).toFixed(3)}</div>
                            <div class="col-span-3 text-right">*${parseFloat(item.total_price || 0).toFixed(2)}</div>
                        </div>
                    `).join('') : '<div class="text-xs text-center">No items</div>'}
                    <div class="border-t border-gray-400 my-2"></div>
                </div>
                
                <!-- Tax Information -->
                <div class="mb-4">
                    <div class="flex justify-between text-xs mb-1">
                        <span>TXBL1</span>
                        <span>*${subtotal.toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between text-xs mb-1">
                        <span>TAX1 15%</span>
                        <span>*${taxAmount.toFixed(2)}</span>
                    </div>
                    <div class="border-t border-gray-400 my-1"></div>
                </div>
                
                <!-- Total -->
                <div class="mb-4">
                    <div class="flex justify-between text-sm font-bold">
                        <span>TOTAL:</span>
                        <span>*${totalAmount.toFixed(2)}</span>
                    </div>
                    <div class="border-t border-gray-400 my-2"></div>
                    <div class="flex justify-between text-xs mb-1">
                        <span>CASH:</span>
                        <span>*${totalAmount.toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between text-xs mb-1">
                        <span>ITEM#:</span>
                        <span>${sale.items ? sale.items.length : 0}</span>
                    </div>
                    <div class="border-t border-gray-400 my-2"></div>
                </div>
                
                <!-- Footer -->
                <div class="text-center">
                    <div class="text-xs mb-1">ERCA FG10024710</div>
                    <div class="text-xs">Powered By Pharmacy System</div>
                    <div class="text-xs">Analog Softwares Solutions</div>
                </div>
            </div>
        `;
    }
    
    /**
     * Hide receipt modal
     */
    function hideReceiptModal() {
        const modal = document.getElementById('receiptModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    }
    
    /**
     * Print receipt
     */
    function printReceipt() {
        const receiptContent = document.getElementById('receiptContent');
        if (receiptContent) {
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <html>
                    <head>
                        <title>Receipt</title>
                        <style>
                            body { 
                                font-family: monospace; 
                                font-size: 12px; 
                                margin: 0; 
                                padding: 20px; 
                                background: white;
                                color: black;
                            }
                            .receipt-container { 
                                max-width: 300px; 
                                margin: 0 auto; 
                                background: white;
                                color: black;
                                border: 1px solid #ccc;
                                padding: 16px;
                            }
                            .grid { display: grid; }
                            .grid-cols-12 { grid-template-columns: repeat(12, minmax(0, 1fr)); }
                            .col-span-5 { grid-column: span 5; }
                            .col-span-2 { grid-column: span 2; }
                            .col-span-3 { grid-column: span 3; }
                            .text-center { text-align: center; }
                            .text-right { text-align: right; }
                            .truncate { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
                            @media print { 
                                body { margin: 0; padding: 0; background: white; color: black; }
                                .receipt-container { background: white; color: black; }
                            }
                        </style>
                    </head>
                    <body>
                        ${receiptContent.innerHTML}
                    </body>
                </html>
            `);
            printWindow.document.close();
            printWindow.print();
        }
    }
    
    /**
     * Download receipt as PDF
     */
    function downloadReceipt() {
        const receiptContent = document.getElementById('receiptContent');
        if (receiptContent) {
            // Create a simple text version for download
            const textContent = receiptContent.innerText;
            const blob = new Blob([textContent], { type: 'text/plain' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `receipt-${new Date().getTime()}.txt`;
            a.click();
            window.URL.revokeObjectURL(url);
        }
    }
    
    // Make functions globally available
    window.hideReceiptModal = hideReceiptModal;
    window.printReceipt = printReceipt;
    window.downloadReceipt = downloadReceipt;
    
    // Debug function to test modal display
    window.testViewModal = function() {
        const testSale = {
            id: 1,
            sale_number: 'TEST-001',
            customer_name: 'Test Customer',
            status: 'pending',
            payment_method: 'cash',
            total_amount: 150.00,
            subtotal: 140.00,
            tax_amount: 10.00,
            discount_amount: 0.00,
            sale_date: new Date().toISOString(),
            items: [
                {
                    quantity: 2,
                    unit_price: 50.00,
                    total_price: 100.00,
                    medicine: {
                        name: 'Test Medicine',
                        generic_name: 'Test Generic',
                        strength: '500mg'
                    }
                }
            ]
        };
        
        console.log('Testing modal with test sale:', testSale);
        showViewSaleModal(testSale);
    };
    
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
        console.log('Navigating to page:', page);
        console.log('Current URL:', window.location.href);
        
        // Use GET parameters instead of POST to avoid MethodNotAllowedHttpException
        const url = new URL(window.location);
        url.searchParams.set('page', page);
        console.log('New URL:', url.toString());
        window.location.href = url.toString();
    }

    /**
     * Navigate to previous page
     */
    function previousPage() {
        if (currentPage > 1) {
            goToPage(currentPage - 1);
        }
    }

    /**
     * Navigate to next page
     */
    function nextPage() {
        if (currentPage < totalPages) {
            goToPage(currentPage + 1);
        }
    }
    
})();
</script>
@endsection
