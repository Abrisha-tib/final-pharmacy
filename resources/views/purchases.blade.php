@extends('layouts.app')

@section('title', 'Purchase Management - Analog Pharmacy')

@section('content')
<style>
    .card-hover {
        transition: all 0.3s ease;
    }
    
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .dark-card {
        background-color: #20232B;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .dark-input {
        background-color: #323741;
        border: 1px solid #4A5568;
        color: #E2E8F0;
    }
    
    .dark-input:focus {
        border-color: #68D391;
        box-shadow: 0 0 0 3px rgba(104, 211, 145, 0.1);
    }
    
    .dark-input::placeholder {
        color: #A0AEC0;
    }
    
    /* Clean Medicine search styling */
    .medicine-search-container {
        position: relative;
    }
    
    .medicine-search-input {
        transition: all 0.2s ease;
    }
    
    .medicine-search-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.1);
    }
    
    .medicine-search-input:focus + .search-icon {
        color: #f97316;
    }
    
    .search-icon {
        transition: color 0.2s ease;
        color: #9ca3af;
    }
    
    .medicine-dropdown {
        max-height: 280px;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #cbd5e1 #f1f5f9;
        border: 2px solid #e5e7eb;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        animation: dropdownSlideIn 0.3s ease-out;
    }
    
    @keyframes dropdownSlideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .medicine-option {
        transition: all 0.2s ease;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .medicine-option:hover {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        transform: translateX(4px);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    
    .medicine-option:last-child {
        border-bottom: none;
    }
    
    .medicine-name {
        font-weight: 600;
        color: #1f2937;
        transition: color 0.2s ease;
    }
    
    .medicine-option:hover .medicine-name {
        color: #f97316;
    }
    
    .medicine-generic {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .medicine-stock {
        color: #059669;
        font-weight: 500;
    }
    
    .medicine-price {
        color: #dc2626;
        font-weight: 600;
    }
    
    .selected-medicine-card {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%);
        border: 2px solid #10b981;
        border-radius: 12px;
        animation: cardSlideIn 0.4s ease-out;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);
    }
    
    @keyframes cardSlideIn {
        from {
            opacity: 0;
            transform: translateY(10px) scale(0.95);
        }
        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }
    
    .selected-medicine-name {
        color: #065f46;
        font-weight: 700;
        font-size: 1rem;
    }
    
    .selected-medicine-details {
        color: #047857;
        font-size: 0.875rem;
    }
    
    .clear-selection-btn {
        transition: all 0.2s ease;
        color: #dc2626;
    }
    
    .clear-selection-btn:hover {
        color: #b91c1c;
        transform: scale(1.1);
        background: rgba(220, 38, 38, 0.1);
        border-radius: 50%;
        padding: 4px;
    }
    
    .loading-spinner {
        display: inline-block;
        width: 16px;
        height: 16px;
        border: 2px solid #f3f4f6;
        border-radius: 50%;
        border-top-color: #f97316;
        animation: spin 1s ease-in-out infinite;
    }
    
    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }
    
    .no-results {
        padding: 2rem;
        text-align: center;
        color: #6b7280;
    }
    
    .no-results-icon {
        font-size: 2rem;
        color: #d1d5db;
        margin-bottom: 0.5rem;
    }
    
    .error-message {
        color: #dc2626;
        background: #fef2f2;
        border: 1px solid #fecaca;
        border-radius: 8px;
        padding: 0.75rem;
    }
    
    /* Dark mode enhancements */
    .dark .medicine-search-input {
        border-color: #4b5563;
    }
    
    .dark .medicine-search-input:focus {
        border-color: #f97316;
        box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.2);
    }
    
    .dark .medicine-dropdown {
        border-color: #4b5563;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
    }
    
    .dark .medicine-option {
        border-bottom-color: #374151;
    }
    
    .dark .medicine-option:hover {
        background: linear-gradient(135deg, #374151 0%, #4b5563 100%);
    }
    
    .dark .medicine-name {
        color: #f9fafb;
    }
    
    .dark .medicine-generic {
        color: #9ca3af;
    }
    
    .dark .selected-medicine-card {
        background: linear-gradient(135deg, #064e3b 0%, #065f46 100%);
        border-color: #10b981;
    }
    
    .dark .selected-medicine-name {
        color: #a7f3d0;
    }
    
    .dark .selected-medicine-details {
        color: #6ee7b7;
    }
    
    /* Scrollbar styling */
    .medicine-dropdown::-webkit-scrollbar {
        width: 8px;
    }
    
    .medicine-dropdown::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    
    .medicine-dropdown::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #cbd5e1 0%, #94a3b8 100%);
        border-radius: 4px;
    }
    
    .medicine-dropdown::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
    }
    
    .dark .medicine-dropdown::-webkit-scrollbar-track {
        background: #374151;
    }
    
    .dark .medicine-dropdown::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
    }
    
    .dark .medicine-dropdown::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
    }
    
    /* Enhanced purchase header styling */
    .purchase-header-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
        border: 1px solid #cbd5e1;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
    }
    
    .dark .purchase-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        border: 1px solid #64748b;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
    }
    
    /* Perfect Modal Centering - Optimized for cPanel/Shared Hosting */
    #addPurchaseModal {
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        position: fixed !important;
        top: 0 !important;
        left: 0 !important;
        right: 0 !important;
        bottom: 0 !important;
        z-index: 9999 !important;
        padding: 1rem !important;
        backdrop-filter: blur(4px) !important;
    }
    
    #addPurchaseModal.hidden {
        display: none !important;
    }
    
    /* Ensure modal content is perfectly centered */
    #addPurchaseModal .modal-content {
        max-width: 100% !important;
        width: 100% !important;
        max-height: 90vh !important;
        margin: 0 auto !important;
    }
    
    /* Responsive centering for all screen sizes */
    @media (max-width: 768px) {
        #addPurchaseModal {
            padding: 0.5rem !important;
        }
    }
    
    @media (max-width: 480px) {
        #addPurchaseModal {
            padding: 0.25rem !important;
        }
    }
    
    /* Enhanced text styling for better readability */
    .purchase-header-card h1 {
        background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .dark .purchase-header-card h1 {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    /* Purchase specific styles */
    .purchase-card {
        border-left: 4px solid #10b981;
    }
    
    .purchase-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
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
    
    .status-received {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-cancelled {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    /* Metric cards styling */
    .metric-card {
        background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
        border-radius: 16px;
        padding: 24px;
        color: white;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    
    .metric-card-green {
        --gradient-start: #10b981;
        --gradient-end: #059669;
    }
    
    .metric-card-orange {
        --gradient-start: #f59e0b;
        --gradient-end: #d97706;
    }
    
    .metric-card-red {
        --gradient-start: #ef4444;
        --gradient-end: #dc2626;
    }
    
    .metric-card-teal {
        --gradient-start: #14b8a6;
        --gradient-end: #0d9488;
    }
</style>

<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="purchase-header-card relative rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Purchase Management</h1>
                    <p class="text-slate-600 dark:text-slate-300 text-lg">Manage your pharmacy's purchase orders and supplier relationships</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Purchase Status</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-white" id="purchaseStatus">Active Management</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Value Card -->
        <div class="card-hover bg-gradient-to-br from-green-400 to-green-500 dark:from-green-800 dark:to-green-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-green-600 dark:border-green-600 p-6 transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-bold text-green-800 dark:text-green-200 uppercase tracking-wide">Total Value</p>
                    <p class="text-3xl font-bold text-green-900 dark:text-white mt-2 mb-1">{{ number_format($stats['totalValue'], 2) }} Birr</p>
                    <div class="flex items-center space-x-1">
                        <div class="flex items-center bg-green-500 dark:bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                            <i class="fas fa-chart-line text-xs mr-1"></i>
                            <span>From all orders</span>
                        </div>
                    </div>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-dollar-sign text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Orders Card -->
        <div class="card-hover bg-gradient-to-br from-orange-400 to-orange-500 dark:from-orange-800 dark:to-orange-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-orange-600 dark:border-orange-600 p-6 transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-bold text-orange-800 dark:text-orange-200 uppercase tracking-wide">Pending Orders</p>
                    <p class="text-3xl font-bold text-orange-900 dark:text-white mt-2 mb-1">{{ $stats['pendingOrders'] }}</p>
                    <div class="flex items-center space-x-1">
                        <div class="flex items-center bg-orange-500 dark:bg-orange-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                            <i class="fas fa-clock text-xs mr-1"></i>
                            <span>Awaiting processing</span>
                        </div>
                    </div>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-clock text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Pending Delivery Card -->
        <div class="card-hover bg-gradient-to-br from-red-400 to-red-500 dark:from-red-800 dark:to-red-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-red-600 dark:border-red-600 p-6 transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-bold text-red-800 dark:text-red-200 uppercase tracking-wide">Pending Delivery</p>
                    <p class="text-3xl font-bold text-red-900 dark:text-white mt-2 mb-1">{{ $stats['pendingDelivery'] }}</p>
                    <div class="flex items-center space-x-1">
                        <div class="flex items-center bg-red-500 dark:bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                            <i class="fas fa-truck text-xs mr-1"></i>
                            <span>In transit</span>
                        </div>
                    </div>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-truck text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Purchases Card -->
        <div class="card-hover bg-gradient-to-br from-blue-400 to-blue-500 dark:from-blue-800 dark:to-blue-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-blue-600 dark:border-blue-600 p-6 transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-bold text-blue-800 dark:text-blue-200 uppercase tracking-wide">Total Purchases</p>
                    <p class="text-3xl font-bold text-blue-900 dark:text-white mt-2 mb-1">{{ $stats['totalPurchases'] }}</p>
                    <div class="flex items-center space-x-1">
                        <div class="flex items-center bg-blue-500 dark:bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                            <i class="fas fa-shopping-cart text-xs mr-1"></i>
                            <span>All purchases</span>
                        </div>
                    </div>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-shopping-cart text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- View Options and Action Buttons -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 space-y-4 sm:space-y-0">
        <!-- View Options -->
        <div class="flex items-center space-x-4">
            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-xl p-1">
                <button id="cardsViewBtn" class="px-4 py-2 text-sm font-semibold text-white bg-orange-500 rounded-lg shadow-md transition-all duration-200">
                    <i class="fas fa-th-large mr-2"></i>Cards
                </button>
                <button id="tableViewBtn" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-all duration-200">
                    <i class="fas fa-table mr-2"></i>Table
                </button>
            </div>
            
            <button onclick="showAnalyticsModal()" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl shadow-md transition-all duration-200">
                <i class="fas fa-chart-bar mr-2"></i>Show Analytics
            </button>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center space-x-4">
            <div class="flex space-x-2">
                <button onclick="showImportExportModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                    <i class="fas fa-download mr-2"></i>Import / Export
                </button>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 mb-6">
        <div class="p-6">
            <form method="GET" action="{{ route('purchases.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <!-- Search Input -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Purchases</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ $request->search }}" 
                               placeholder="Search by purchase ID, supplier..." 
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark-input">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Supplier Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                    <select name="supplier" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark-input">
                        <option value="">All Suppliers</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ $request->supplier == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark-input">
                        <option value="">All Status</option>
                        <option value="pending" {{ $request->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="received" {{ $request->status == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="cancelled" {{ $request->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date</label>
                    <input type="date" name="date" value="{{ $request->date }}" 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark-input">
                </div>

                <!-- Hidden view type -->
                <input type="hidden" name="view" value="{{ $viewType }}">

                <!-- Filter Button -->
                <div class="md:col-span-4 flex justify-end">
                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                        <i class="fas fa-filter mr-2"></i>Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

<!-- Purchase Grid -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mt-8">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-gray-600 dark:text-gray-400">
                <span>Showing 1 of 1 purchases</span>
            </div>
            <div class="flex gap-4">
                <button onclick="refreshPurchases()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2 text-sm"></i>Refresh
                </button>
                <button onclick="printPurchases()" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm transition-all duration-200 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-print mr-2 text-sm"></i>Print
                </button>
                <button onclick="showAddPurchaseModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-plus mr-2 text-sm"></i>Add Purchase
                </button>
                <button onclick="showPurchaseHistory()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-sm"></i>Purchase History
                </button>
            </div>
        </div>
    </div>

    <div class="p-6 bg-gray-50 dark:bg-gray-900">
        <!-- Cards View -->
        <div id="cardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($purchases->count() > 0)
                @foreach($purchases as $purchase)
                <!-- Purchase Card: {{ $purchase->purchase_number }} -->
                <div class="purchase-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl border border-gray-200 dark:border-gray-700 p-6 transition-all duration-300">
                    <!-- Header Section -->
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $purchase->purchase_number }}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">{{ $purchase->supplier->name ?? 'Unknown Supplier' }}</p>
                        <div class="flex gap-2">
                            <span class="status-badge px-3 py-1 rounded-full text-xs font-semibold {{ 
                                $purchase->status === 'pending' ? 'status-pending' : 
                                ($purchase->status === 'received' ? 'status-received' : 'status-cancelled') 
                            }}">
                                <i class="fas fa-{{ $purchase->status === 'pending' ? 'clock' : ($purchase->status === 'received' ? 'check' : 'times') }} mr-1"></i>
                                {{ strtoupper($purchase->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Items Section -->
                    <div class="mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Items</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $purchase->items->count() }} Items</span>
                        </div>
                    </div>
                    
                    <!-- Total Amount (Prominent) -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-3 mb-4">
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">Total Amount:</span>
                            <span class="text-2xl font-bold text-gray-900 dark:text-white">Br {{ number_format($purchase->total_amount, 2) }}</span>
                        </div>
                    </div>
                    
                    <!-- Administrative Details -->
                    <div class="space-y-2 mb-4">
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Created By:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $purchase->createdBy->name ?? 'Unknown' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Order Date:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $purchase->order_date ? \Carbon\Carbon::parse($purchase->order_date)->format('M d, Y') : 'N/A' }}</span>
                        </div>
                        @if($purchase->expected_delivery)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Expected Delivery:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($purchase->expected_delivery)->format('M d, Y') }}</span>
                        </div>
                        @endif
                        @if($purchase->delivery_date)
                        <div class="flex justify-between">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Delivery Date:</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($purchase->delivery_date)->format('M d, Y') }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="border-t border-gray-200 dark:border-gray-600 pt-4">
                        <div class="flex justify-between items-center">
                            <button onclick="viewPurchase({{ $purchase->id }})" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                                <i class="fas fa-eye mr-2"></i>View
                            </button>
                            <button onclick="editPurchase({{ $purchase->id }})" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                                <i class="fas fa-edit mr-2"></i>Edit
                            </button>
                            <button onclick="deletePurchase({{ $purchase->id }})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                                <i class="fas fa-trash mr-2"></i>Delete
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <!-- Empty State -->
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-500 mb-2">No Purchases Found</h3>
                    <p class="text-gray-400 mb-6">No purchase orders have been created yet.</p>
                    <button onclick="showAddPurchaseModal()" class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold transition-all duration-200">
                        <i class="fas fa-plus mr-2"></i>Create First Purchase
                    </button>
                </div>
            @endif
        </div>
    </div>
</div>

    <!-- Pagination and Status -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 no-print">
        <div class="flex items-center justify-between">
            <!-- Pagination Info -->
            <div class="text-sm text-gray-700 dark:text-gray-300 pagination-info">
                Showing {{ $purchases->firstItem() ?? 0 }} to {{ $purchases->lastItem() ?? 0 }} of {{ $purchases->total() }} results
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

        <!-- Table View -->
        <div id="tableView" class="hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Purchase ID
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Supplier
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Payment Method
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Items
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Total Amount
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            <!-- Purchase Row 1 -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">PURCHASE-000017</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">Walk-in Supplier</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge status-pending px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-check mr-1"></i>PENDING
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="payment-badge px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        <i class="fas fa-dollar-sign mr-1"></i>Cash
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">1 Items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">Br 115.50</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">Oct 05, 2025</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewPurchase(17)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editPurchase(17)" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deletePurchase(17)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Purchase Row 2 -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">PURCHASE-000016</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">Walk-in Supplier</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge status-completed px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-check mr-1"></i>COMPLETED
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="payment-badge px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        <i class="fas fa-dollar-sign mr-1"></i>Bank Transfer
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">1 Items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">Br 346.50</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">Oct 04, 2025</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewPurchase(16)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editPurchase(16)" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deletePurchase(16)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Purchase Row 3 -->
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-white">PURCHASE-000015</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">Walk-in Supplier</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge status-completed px-3 py-1 rounded-full text-xs font-semibold">
                                        <i class="fas fa-check mr-1"></i>COMPLETED
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="payment-badge px-3 py-1 rounded-full text-xs font-semibold bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                        <i class="fas fa-dollar-sign mr-1"></i>Cash
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">1 Items</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">Br 115.50</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">Oct 03, 2025</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        <button onclick="viewPurchase(15)" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button onclick="editPurchase(15)" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button onclick="deletePurchase(15)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Add Purchase Modal -->
<div id="addPurchaseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden flex items-center justify-center z-[9999] p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-box text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">New Purchase Order</h3>
                </div>
            </div>
            <button id="closeAddModal" class="w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <form id="addPurchaseForm">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Section - Purchase Information -->
                    <div class="space-y-6">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Purchase Information</h4>
                        
                        <!-- Supplier Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Supplier *</label>
                            <select name="supplier_id" id="supplierSelect" required class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark-input">
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                            <div class="text-xs text-gray-500 mt-1">Debug: {{ $suppliers->count() }} suppliers loaded</div>
                        </div>

                        <!-- Order Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Date *</label>
                            <input type="date" name="order_date" id="orderDate" required value="{{ date('Y-m-d') }}" 
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark-input">
                        </div>

                        <!-- Expected Delivery -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Delivery *</label>
                            <div class="relative">
                                <i class="fas fa-truck absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                                <input type="date" name="expected_delivery" id="expectedDelivery" required 
                                       class="w-full pl-10 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark-input">
                                <i class="fas fa-calendar absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select name="status" id="statusSelect" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark-input">
                                <option value="pending">Pending</option>
                                <option value="ordered">Ordered</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                            <textarea name="notes" id="notes" rows="3" 
                                      class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark-input"
                                      placeholder="Additional notes about this purchase order..."></textarea>
                        </div>
                    </div>

                    <!-- Right Section - Items and Financial Summary -->
                    <div class="space-y-6">
                        <!-- Items Section -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Items</h4>
                            
                            <!-- Add Item Buttons -->
                            <div class="flex gap-3 mb-4">
                                <button type="button" id="quickAddMedicine" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                                    <i class="fas fa-plus mr-2"></i>Quick Add Medicine
                                </button>
                                <button type="button" id="addItem" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                                    <i class="fas fa-plus mr-2"></i>Add Item
                                </button>
                            </div>

                            <!-- Items Container -->
                            <div id="itemsContainer" class="space-y-4">
                                <!-- No items message -->
                                <div id="noItemsMessage" class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                    <div class="flex items-center">
                                        <div class="w-4 h-4 bg-orange-500 rounded-full mr-3"></div>
                                        <p class="text-sm text-yellow-800 dark:text-yellow-200">Select Supplier: Try adjusting your search criteria</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Summary -->
                        <div>
                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Financial Summary</h4>
                            
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Subtotal:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" id="subtotalAmount">0.00 Birr</span>
                                </div>
                                
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Tax:</span>
                                    <span class="text-sm font-medium text-gray-900 dark:text-white" id="taxAmount">0.00 Birr</span>
                                </div>
                                
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Shipping:</span>
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 mr-1">$</span>
                                        <input type="number" id="shippingAmount" value="0" min="0" step="0.01" 
                                               class="w-20 px-2 py-1 text-sm border border-gray-300 dark:border-gray-600 rounded focus:ring-2 focus:ring-orange-500 focus:border-transparent dark-input">
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-300 dark:border-gray-600 pt-3">
                                    <div class="flex justify-between">
                                        <span class="text-lg font-bold text-gray-900 dark:text-white">Total:</span>
                                        <span class="text-lg font-bold text-orange-600 dark:text-orange-400" id="totalAmount">0.00 Birr</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button type="button" id="cancelPurchase" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                <i class="fas fa-times mr-1"></i>Cancel
            </button>
            <button type="button" id="createPurchase" class="px-6 py-2 text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-lg transition-all duration-200 flex items-center">
                <i class="fas fa-file-alt mr-2"></i>Create Purchase
            </button>
        </div>
    </div>
</div>

<!-- Add New Medicine Modal -->
<div id="addNewMedicineModal" class="fixed inset-0 bg-black bg-opacity-50 z-[10000] hidden flex items-center justify-center p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-hidden flex flex-col">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                            <i class="fas fa-plus text-white text-lg"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-white">Add New Medicine</h3>
                            <p class="text-orange-100 text-sm">Add a new medicine to your purchase order</p>
                        </div>
                    </div>
                    <button onclick="closeAddNewMedicineModal()" class="text-white hover:text-orange-200 transition-colors duration-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="flex-1 overflow-y-auto p-6">
                <form id="addNewMedicineForm" class="space-y-6">
                    <!-- Medicine Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Medicine Name *</label>
                        <input type="text" 
                               name="medicine_name" 
                               id="newMedicineName" 
                               required 
                               placeholder="Enter medicine name..."
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input">
                    </div>

                    <!-- Generic Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Generic Name</label>
                        <input type="text" 
                               name="generic_name" 
                               id="newGenericName" 
                               placeholder="Enter generic name..."
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input">
                    </div>

                    <!-- Manufacturer -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Manufacturer</label>
                        <input type="text" 
                               name="manufacturer" 
                               id="newManufacturer" 
                               placeholder="Enter manufacturer name..."
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input">
                    </div>

                    <!-- Batch Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Batch Number</label>
                        <input type="text" 
                               name="batch_number" 
                               id="newBatchNumber" 
                               placeholder="Enter batch number..."
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input">
                    </div>

                    <!-- Quantity and Price -->
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity *</label>
                            <input type="number" 
                                   name="quantity" 
                                   id="newQuantity" 
                                   required 
                                   min="1" 
                                   placeholder="Enter quantity..."
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit Price (Br) *</label>
                            <input type="number" 
                                   name="unit_price" 
                                   id="newUnitPrice" 
                                   required 
                                   min="0" 
                                   step="0.01" 
                                   placeholder="Enter unit price..."
                                   class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input">
                        </div>
                    </div>

                    <!-- Expiry Date -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expiry Date</label>
                        <input type="date" 
                               name="expiry_date" 
                               id="newExpiryDate" 
                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input">
                    </div>

                    <!-- Notes -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                        <textarea name="notes" 
                                  id="newMedicineNotes" 
                                  rows="3" 
                                  placeholder="Additional notes about this medicine..."
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input"></textarea>
                    </div>
                </form>
            </div>

            <!-- Modal Footer -->
            <div class="bg-gray-50 dark:bg-gray-700 px-6 py-4 flex items-center justify-between flex-shrink-0">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <i class="fas fa-info-circle mr-1"></i>
                    This medicine will be added to your purchase order
                </div>
                <div class="flex space-x-2">
                    <button onclick="closeAddNewMedicineModal()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-times mr-1"></i>Cancel
                    </button>
                    <button onclick="addNewMedicineToPurchase()" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-sm font-medium transition-colors duration-200">
                        <i class="fas fa-plus mr-1"></i>Add to Purchase
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Purchase History Modal -->
<div id="purchaseHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999] hidden p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-7xl h-[95vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center space-x-3">
                <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                    <i class="fas fa-history text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase History</h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">View and manage all purchase transactions</p>
                </div>
            </div>
            <button id="closePurchaseHistoryModal" class="w-10 h-10 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl flex items-center justify-center transition-colors">
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
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Purchases</label>
                    <div class="relative">
                        <input type="text" id="purchaseSearchInput" placeholder="Search by supplier, purchase ID..." 
                               class="w-full pl-10 pr-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    </div>
                </div>
                
                <!-- Date Range -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date Range</label>
                    <div class="space-y-3">
                        <input type="date" id="startDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <input type="date" id="endDate" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                </div>
                
                <!-- Supplier Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                    <select id="supplierFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Suppliers</option>
                        <!-- Suppliers will be populated dynamically -->
                    </select>
                </div>
                
                <!-- Status Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                    <select id="statusFilter" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="">All Status</option>
                        <option value="pending">Pending</option>
                        <option value="ordered">Ordered</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                    </select>
                </div>
                
                <!-- Action Buttons -->
                <div class="space-y-3">
                    <button id="applyFiltersBtn" class="w-full bg-orange-600 hover:bg-orange-700 text-white rounded-lg py-2 px-4 font-medium transition-colors duration-200 flex items-center justify-center">
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
            
            <!-- Right Panel: Purchase History List -->
            <div class="flex-1 p-6 flex flex-col min-h-0 overflow-hidden">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Purchase Records</h3>
                    <div class="flex items-center space-x-3">
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total: <span id="totalPurchasesCount">0</span> purchases</span>
                        <button id="refreshHistoryBtn" class="px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                            <i class="fas fa-sync-alt mr-2"></i>Refresh
                        </button>
                    </div>
                </div>
                
                <!-- Purchase History Container -->
                <div id="purchaseHistoryContainer" class="flex-1 overflow-y-auto space-y-4">
                    <!-- Purchases will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// View toggle functionality
// These event listeners are now handled by the initializeViewSwitching() function

// Modal functionality
document.getElementById('closeAddModal').addEventListener('click', function() {
    const modal = document.getElementById('addPurchaseModal');
    if (modal) {
        modal.classList.add('hidden');
        // Restore body scroll
        document.body.style.overflow = 'auto';
    }
});

document.getElementById('cancelPurchase').addEventListener('click', function() {
    const modal = document.getElementById('addPurchaseModal');
    if (modal) {
        modal.classList.add('hidden');
        // Restore body scroll
        document.body.style.overflow = 'auto';
    }
});

// Close modal when clicking on backdrop
document.getElementById('addPurchaseModal').addEventListener('click', function(e) {
    if (e.target === this) {
        this.classList.add('hidden');
        // Restore body scroll
        document.body.style.overflow = 'auto';
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('addPurchaseModal');
        if (modal && !modal.classList.contains('hidden')) {
            modal.classList.add('hidden');
            // Restore body scroll
            document.body.style.overflow = 'auto';
        }
    }
});

// Purchase functions
function showAddPurchaseModal() {
    const modal = document.getElementById('addPurchaseModal');
    if (modal) {
        // Show the modal with perfect centering
        modal.classList.remove('hidden');
        // Ensure body doesn't scroll when modal is open
        document.body.style.overflow = 'hidden';
        // Force reflow to ensure centering
        modal.offsetHeight;
    }
    
    // Set default expected delivery to 7 days from now
    const today = new Date();
    const nextWeek = new Date(today.getTime() + 7 * 24 * 60 * 60 * 1000);
    const expectedDeliveryField = document.getElementById('expectedDelivery');
    if (expectedDeliveryField) {
        expectedDeliveryField.value = nextWeek.toISOString().split('T')[0];
    }
}

function viewPurchase(id) {
    // Fetch purchase details and show modal
    fetch(`/purchases/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            showViewPurchaseModal(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading purchase details');
        });
}

// Global variable to store current purchase ID
let currentPurchaseId = null;

/**
 * Helper functions for loading states (copied from suppliers page)
 */
function showGlobalLoading(message = 'Loading...') {
    // Remove existing loading if any
    hideGlobalLoading();
    
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'globalLoading';
    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
    loadingDiv.innerHTML = `
        <div class="text-center">
            <!-- Purchase icon container -->
            <div class="relative w-16 h-16 mx-auto mb-6">
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-shopping-cart text-blue-500 text-3xl animate-pulse"></i>
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

function showActionLoading(action) {
    const messages = {
        'viewing': 'Loading purchase details...',
        'editing': 'Loading purchase data...',
        'deleting': 'Preparing deletion...',
        'saving': 'Saving changes...'
    };
    showGlobalLoading(messages[action] || 'Loading...');
}

function showLoadingWithStyle(type, message) {
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'globalLoading';
    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
    
    const colorClass = type === 'success' ? 'text-green-500' : 
                      type === 'error' ? 'text-red-500' : 
                      'text-blue-500';
    
    const iconClass = type === 'success' ? 'fa-check-circle' : 
                     type === 'error' ? 'fa-exclamation-triangle' : 
                     'fa-shopping-cart';
    
    loadingDiv.innerHTML = `
        <div class="text-center">
            <!-- Purchase icon container -->
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

function startProgressAnimation() {
    let progress = 0;
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('loadingProgress');
    
    if (!progressBar || !progressText) return;
    
    window.loadingAnimation = setInterval(() => {
        progress += Math.random() * 15;
        if (progress > 100) progress = 100;
        
        progressBar.style.width = progress + '%';
        progressText.textContent = Math.round(progress);
        
        if (progress >= 100) {
            clearInterval(window.loadingAnimation);
            window.loadingAnimation = null;
        }
    }, 200);
}

/**
 * Edit purchase (following exact pattern from suppliers page)
 */
window.editPurchase = async function(purchaseId) {
    try {
        // Set current purchase ID for save function
        currentPurchaseId = purchaseId;
        
        // Show global loading spinner
        showActionLoading('editing');
        
        // Fetch purchase data from server
        const response = await fetch(`/purchases/${purchaseId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const purchase = await response.json();
            
            if (purchase) {
                // Show modal and populate form
                document.getElementById('editPurchaseModal').classList.remove('hidden');
                populateEditForm(purchase);
            } else {
                throw new Error('Purchase not found');
            }
        } else {
            throw new Error(`Failed to fetch purchase data: ${response.status}`);
        }
        
    } catch (error) {
        console.error('Error fetching purchase:', error);
        if (window.NotificationService) {
            window.NotificationService.error('Failed to load purchase data. Please try again.');
        }
        hideEditPurchaseModal();
    } finally {
        // Hide global loading spinner
        hideGlobalLoading();
    }
};

/**
 * Populate edit form with purchase data
 */
function populateEditForm(purchase) {
    document.getElementById('editPurchaseNumber').value = purchase.purchase_number;
    document.getElementById('editSupplier').value = purchase.supplier_id;
    
    // Handle date formatting for HTML date inputs
    if (purchase.order_date) {
        const orderDate = new Date(purchase.order_date);
        if (!isNaN(orderDate.getTime())) {
            const year = orderDate.getFullYear();
            const month = String(orderDate.getMonth() + 1).padStart(2, '0');
            const day = String(orderDate.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;
            document.getElementById('editOrderDate').value = formattedDate;
        }
    }
    
    if (purchase.expected_delivery) {
        const expectedDate = new Date(purchase.expected_delivery);
        if (!isNaN(expectedDate.getTime())) {
            const year = expectedDate.getFullYear();
            const month = String(expectedDate.getMonth() + 1).padStart(2, '0');
            const day = String(expectedDate.getDate()).padStart(2, '0');
            const formattedDate = `${year}-${month}-${day}`;
            document.getElementById('editExpectedDelivery').value = formattedDate;
        }
    } else {
        document.getElementById('editExpectedDelivery').value = '';
    }
    
    document.getElementById('editStatus').value = purchase.status;
    document.getElementById('editTotalAmount').value = purchase.total_amount;
    document.getElementById('editNotes').value = purchase.notes || '';
    
    // Populate purchase items
    const itemsContainer = document.getElementById('editPurchaseItems');
    itemsContainer.innerHTML = '';
    
    if (purchase.items && purchase.items.length > 0) {
        purchase.items.forEach((item, index) => {
            const itemHtml = `
                <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-4 bg-gray-50 dark:bg-gray-700">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">${item.medicine ? item.medicine.name : 'Unknown Medicine'}</h4>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Item ${index + 1}</span>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity</label>
                            <input type="number" name="items[${index}][quantity]" value="${item.quantity}" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Unit Price (Birr)</label>
                            <input type="number" name="items[${index}][unit_price]" value="${item.unit_price}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Price (Birr)</label>
                            <input type="number" value="${item.total_price}" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-600 dark:text-white" readonly>
                        </div>
                    </div>
                    
                    <input type="hidden" name="items[${index}][medicine_id]" value="${item.medicine_id}">
                    <input type="hidden" name="items[${index}][batch_number]" value="${item.batch_number || ''}">
                    <input type="hidden" name="items[${index}][expiry_date]" value="${item.expiry_date || ''}">
                    <input type="hidden" name="items[${index}][notes]" value="${item.notes || ''}">
                </div>
            `;
            itemsContainer.innerHTML += itemHtml;
        });
        
        // Add event listeners for automatic total calculation
        addItemCalculationListeners();
    }
}

/**
 * Add event listeners for automatic total calculation
 */
function addItemCalculationListeners() {
    const itemsContainer = document.getElementById('editPurchaseItems');
    const quantityInputs = itemsContainer.querySelectorAll('input[name*="[quantity]"]');
    const unitPriceInputs = itemsContainer.querySelectorAll('input[name*="[unit_price]"]');
    
    // Add listeners to quantity inputs
    quantityInputs.forEach((input, index) => {
        input.addEventListener('input', () => calculateItemTotal(index));
    });
    
    // Add listeners to unit price inputs
    unitPriceInputs.forEach((input, index) => {
        input.addEventListener('input', () => calculateItemTotal(index));
    });
}

/**
 * Calculate total for a specific item and update the total amount
 */
function calculateItemTotal(itemIndex) {
    const itemsContainer = document.getElementById('editPurchaseItems');
    const quantityInput = itemsContainer.querySelector(`input[name="items[${itemIndex}][quantity]"]`);
    const unitPriceInput = itemsContainer.querySelector(`input[name="items[${itemIndex}][unit_price]"]`);
    const totalPriceInput = itemsContainer.querySelectorAll('input[readonly]')[itemIndex];
    
    if (quantityInput && unitPriceInput && totalPriceInput) {
        const quantity = parseFloat(quantityInput.value) || 0;
        const unitPrice = parseFloat(unitPriceInput.value) || 0;
        const total = quantity * unitPrice;
        
        totalPriceInput.value = total.toFixed(2);
        
        // Update the overall total amount
        updateTotalAmount();
    }
}

/**
 * Update the overall total amount
 */
function updateTotalAmount() {
    const itemsContainer = document.getElementById('editPurchaseItems');
    const totalInputs = itemsContainer.querySelectorAll('input[readonly]');
    let totalAmount = 0;
    
    totalInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        totalAmount += value;
    });
    
    document.getElementById('editTotalAmount').value = totalAmount.toFixed(2);
}

/**
 * Save purchase changes (following exact pattern from suppliers page)
 */
window.savePurchase = async function() {
    if (!currentPurchaseId) return;
    
    try {
        // Show global loading spinner
        showActionLoading('saving');
        
        const form = document.getElementById('editPurchaseForm');
        const formData = new FormData(form);
        
        // Convert FormData to object and handle items array
        const data = Object.fromEntries(formData);
        const items = [];
        
        // Collect items data
        const itemElements = document.querySelectorAll('[name^="items["]');
        const itemGroups = {};
        
        itemElements.forEach(element => {
            const name = element.name;
            const match = name.match(/items\[(\d+)\]\[(\w+)\]/);
            if (match) {
                const index = match[1];
                const field = match[2];
                
                if (!itemGroups[index]) {
                    itemGroups[index] = {};
                }
                itemGroups[index][field] = element.value;
            }
        });
        
        // Convert to array format
        Object.values(itemGroups).forEach(item => {
            if (item.medicine_id && item.quantity && item.unit_price) {
                items.push({
                    medicine_id: item.medicine_id,
                    quantity: parseInt(item.quantity),
                    unit_price: parseFloat(item.unit_price),
                    batch_number: item.batch_number || null,
                    expiry_date: item.expiry_date || null,
                    notes: item.notes || null
                });
            }
        });
        
        data.items = items;
        
        const response = await fetch(`/purchases/${currentPurchaseId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            const data = await response.json();
            
            if (data.success) {
                // Show success loading
                showLoadingWithStyle('success', 'Purchase updated successfully!');
                
                setTimeout(() => {
                    hideGlobalLoading();
                    hideEditPurchaseModal();
                    // Reload page to show updated data
                    window.location.reload();
                }, 1500);
            } else {
                throw new Error(data.message || 'Failed to update purchase');
            }
        } else {
            throw new Error(`Failed to update purchase: ${response.status}`);
        }
        
    } catch (error) {
        console.error('Error updating purchase:', error);
        // Show error loading
        showLoadingWithStyle('error', 'Failed to update purchase. Please try again.');
        
        setTimeout(() => {
            hideGlobalLoading();
        }, 2000);
    }
};

/**
 * Hide edit purchase modal
 */
window.hideEditPurchaseModal = function() {
    document.getElementById('editPurchaseModal').classList.add('hidden');
    currentPurchaseId = null;
};

function deletePurchase(id) {
    if (confirm('Are you sure you want to delete this purchase?')) {
        // Implementation for deleting purchase
        console.log('Delete purchase:', id);
    }
}

/**
 * Format date for display
 */
function formatDate(dateString) {
    if (!dateString) return 'N/A';
    try {
        // Handle both ISO strings and date objects
        const date = new Date(dateString);
        if (isNaN(date.getTime())) {
            return 'Invalid Date';
        }
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    } catch (e) {
        console.error('Date formatting error:', e, 'Input:', dateString);
        return 'Invalid Date';
    }
}

/**
 * Show view purchase modal with detailed information
 */
function showViewPurchaseModal(purchase) {
    const modal = document.getElementById('viewPurchaseModal');
    const content = document.getElementById('viewPurchaseContent');
    
    if (modal && content) {
        content.innerHTML = `
            <div class="space-y-6">
                <!-- Purchase Header -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4">
                        <i class="fas fa-shopping-bag text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">${purchase.purchase_number || 'Unknown Purchase'}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">${purchase.supplier?.name || 'Unknown Supplier'}</p>
                    <div class="flex justify-center gap-2 mb-6">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold ${
                            purchase.status === 'pending' ? 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200' :
                            purchase.status === 'received' ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' :
                            'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'
                        }">
                            <i class="fas fa-${purchase.status === 'pending' ? 'clock' : purchase.status === 'received' ? 'check' : 'times'} mr-1"></i>
                            ${purchase.status ? purchase.status.charAt(0).toUpperCase() + purchase.status.slice(1) : 'Pending'}
                        </span>
                    </div>
                </div>

                <!-- Purchase Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">Purchase Information</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Purchase Number:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${purchase.purchase_number || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Order Date:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${formatDate(purchase.order_date)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Expected Delivery:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${formatDate(purchase.expected_delivery)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Delivery Date:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${purchase.delivery_date ? formatDate(purchase.delivery_date) : 'Not delivered'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Created By:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${purchase.created_by?.name || 'Unknown'}</span>
                            </div>
                            ${purchase.approved_by ? `
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Approved By:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${purchase.approved_by.name}</span>
                            </div>
                            ` : ''}
                        </div>
                    </div>

                    <!-- Financial Information -->
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-xl p-6">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4 border-b border-gray-200 dark:border-gray-600 pb-2">Financial Details</h4>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Total Amount:</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Br ${parseFloat(purchase.total_amount || 0).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Items Count:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${purchase.items?.length || 0} items</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${purchase.status ? purchase.status.charAt(0).toUpperCase() + purchase.status.slice(1) : 'Pending'}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Items -->
                ${purchase.items && purchase.items.length > 0 ? `
                <div class="space-y-4">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Purchase Items</h4>
                    <div class="space-y-3">
                        ${purchase.items.map(item => `
                        <div class="bg-white dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h5 class="font-semibold text-gray-900 dark:text-white">${item.medicine?.name || 'Unknown Medicine'}</h5>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">${item.medicine?.generic_name || 'No generic name'}</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">Br ${parseFloat(item.total_price || 0).toFixed(2)}</span>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Quantity:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white ml-1">${item.quantity || 0}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Unit Price:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white ml-1">Br ${parseFloat(item.unit_price || 0).toFixed(2)}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Batch:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white ml-1">${item.batch_number || 'N/A'}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Expiry:</span>
                                    <span class="font-semibold text-gray-900 dark:text-white ml-1">${formatDate(item.expiry_date)}</span>
                                </div>
                            </div>
                            ${item.notes ? `
                            <div class="mt-2 pt-2 border-t border-gray-200 dark:border-gray-600">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Notes:</span>
                                <span class="text-sm text-gray-900 dark:text-white ml-1">${item.notes}</span>
                            </div>
                            ` : ''}
                        </div>
                        `).join('')}
                    </div>
                </div>
                ` : `
                <div class="text-center py-8">
                    <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400">No items found for this purchase</p>
                </div>
                `}

                <!-- Notes Section -->
                ${purchase.notes ? `
                <div class="space-y-4">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Notes</h4>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <p class="text-sm text-gray-700 dark:text-gray-300">${purchase.notes}</p>
                    </div>
                </div>
                ` : ''}
            </div>
        `;
        
        modal.classList.remove('hidden');
        // Prevent body scroll when modal is open
        document.body.style.overflow = 'hidden';
    }
}

/**
 * Hide view purchase modal
 */
function hideViewPurchaseModal() {
    const modal = document.getElementById('viewPurchaseModal');
    if (modal) {
        modal.classList.add('hidden');
        // Restore body scroll
        document.body.style.overflow = 'auto';
    }
}

function refreshPurchases() {
    location.reload();
}

function showAnalyticsModal() {
    // Implementation for analytics modal
    console.log('Show analytics');
}

function showImportExportModal() {
    // Implementation for import/export modal
    console.log('Show import/export');
}

function printPurchases() {
    // Implementation for printing
    console.log('Print purchases');
}

function showPurchaseHistory() {
    showPurchaseHistoryModal();
}

// Modal functionality
document.addEventListener('DOMContentLoaded', function() {
    // Close modal functionality
    document.getElementById('closeAddModal').addEventListener('click', function() {
        document.getElementById('addPurchaseModal').classList.add('hidden');
    });

    document.getElementById('cancelPurchase').addEventListener('click', function() {
        document.getElementById('addPurchaseModal').classList.add('hidden');
    });

    // Create purchase functionality
    document.getElementById('createPurchase').addEventListener('click', function() {
        submitPurchaseForm();
    });

    // Add item functionality
    document.getElementById('addItem').addEventListener('click', function() {
        addPurchaseItem();
    });

    // Quick add medicine functionality
    document.getElementById('quickAddMedicine').addEventListener('click', function() {
        quickAddMedicine();
    });

    // Supplier selection change
    document.getElementById('supplierSelect').addEventListener('change', function() {
        const supplierId = this.value;
        if (supplierId) {
            document.getElementById('noItemsMessage').style.display = 'none';
            // Enable add item buttons
            document.getElementById('addItem').disabled = false;
            document.getElementById('quickAddMedicine').disabled = false;
        } else {
            document.getElementById('noItemsMessage').style.display = 'block';
            // Disable add item buttons
            document.getElementById('addItem').disabled = true;
            document.getElementById('quickAddMedicine').disabled = true;
        }
    });

    // Shipping amount change
    document.getElementById('shippingAmount').addEventListener('input', function() {
        updateFinancialSummary();
    });
});

function addPurchaseItem() {
    const supplierId = document.getElementById('supplierSelect').value;
    if (!supplierId) {
        // Try the notification system first
        if (typeof showNotification === 'function') {
            showNotification('Please select a supplier first to add items', 'warning');
        } else {
            // Fallback to a simple visual indicator
            const supplierSelect = document.getElementById('supplierSelect');
            supplierSelect.style.borderColor = '#ef4444';
            supplierSelect.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
            supplierSelect.focus();
            
            // Create a simple inline notification
            const existingAlert = document.getElementById('supplier-alert');
            if (existingAlert) existingAlert.remove();
            
            const alertDiv = document.createElement('div');
            alertDiv.id = 'supplier-alert';
            alertDiv.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm';
            alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Please select a supplier first to add items';
            
            supplierSelect.parentNode.appendChild(alertDiv);
            
            // Remove the alert after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
                supplierSelect.style.borderColor = '';
                supplierSelect.style.boxShadow = '';
            }, 5000);
        }
        return;
    }

    const itemsContainer = document.getElementById('itemsContainer');
    const itemIndex = itemsContainer.children.length;
    
    const itemDiv = document.createElement('div');
    itemDiv.className = 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200';
    itemDiv.innerHTML = `
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plus text-orange-600 dark:text-orange-400 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">Medicine Item</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Select medicine and quantity</p>
                </div>
            </div>
            <button type="button" onclick="removePurchaseItem(this)" class="flex items-center space-x-1 px-3 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200">
                <i class="fas fa-trash text-xs"></i>
                <span class="text-xs font-medium">Remove</span>
            </button>
        </div>
        
        <div class="space-y-6">
            <!-- Medicine Search -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Medicine *</label>
                <div class="medicine-search-container">
                    <div class="relative">
                        <input type="text" 
                               id="medicineSearch_${itemIndex}" 
                               name="items[${itemIndex}][medicine_search]" 
                               placeholder="Search medicine by name, generic name, or batch..."
                               class="medicine-search-input w-full px-4 py-3 pl-11 pr-4 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input"
                               autocomplete="off">
                        <div class="search-icon absolute left-4 top-1/2 transform -translate-y-1/2">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <div id="loadingIndicator_${itemIndex}" class="absolute right-4 top-1/2 transform -translate-y-1/2 hidden">
                            <div class="loading-spinner"></div>
                        </div>
                        <input type="hidden" name="items[${itemIndex}][medicine_id]" id="medicineId_${itemIndex}">
                        <input type="hidden" name="items[${itemIndex}][unit_price]" id="unitPrice_${itemIndex}">
                    </div>
                    
                    <div id="medicineDropdown_${itemIndex}" class="absolute z-30 w-full bg-white dark:bg-gray-800 rounded-lg shadow-lg medicine-dropdown hidden border border-gray-200 dark:border-gray-600">
                        <!-- Medicine options will be populated here -->
                    </div>
                    
                    <div id="selectedMedicine_${itemIndex}" class="selected-medicine-card mt-3 p-4 hidden">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check text-green-600 dark:text-green-400"></i>
                                </div>
                                <div>
                                    <p class="selected-medicine-name text-sm font-semibold text-gray-900 dark:text-white" id="selectedMedicineName_${itemIndex}"></p>
                                    <p class="selected-medicine-details text-xs text-gray-600 dark:text-gray-400" id="selectedMedicineDetails_${itemIndex}"></p>
                                </div>
                            </div>
                            <button type="button" onclick="clearMedicineSelection(${itemIndex})" class="clear-selection-btn p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200">
                                <i class="fas fa-times text-red-500"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quantity -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Quantity *</label>
                <input type="number" 
                       name="items[${itemIndex}][quantity]" 
                       required min="1" 
                       placeholder="Enter quantity..."
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input"
                       onchange="updateFinancialSummary()">
            </div>
        </div>
    `;
    
    itemsContainer.insertBefore(itemDiv, itemsContainer.firstChild);
    
    // Initialize medicine search for this item
    initializeMedicineSearch(itemIndex);
    updateFinancialSummary();
}

// Initialize medicine modal on page load
document.addEventListener('DOMContentLoaded', function() {
    // Add event listeners for modal close buttons
    const closeButton = document.querySelector('#addNewMedicineModal button[onclick="closeAddNewMedicineModal()"]');
    if (closeButton) {
        closeButton.addEventListener('click', closeAddNewMedicineModal);
    }
    
    // Add event listener for X button in header
    const xButton = document.querySelector('#addNewMedicineModal .bg-gradient-to-r button');
    if (xButton) {
        xButton.addEventListener('click', closeAddNewMedicineModal);
    }
    
    // Close modal when clicking on backdrop
    const modal = document.getElementById('addNewMedicineModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeAddNewMedicineModal();
            }
        });
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('addNewMedicineModal');
            if (modal && !modal.classList.contains('hidden')) {
                closeAddNewMedicineModal();
            }
        }
    });
});

function removePurchaseItem(button) {
    button.closest('.bg-white').remove();
    updateFinancialSummary();
}

// Medicine search functionality
function initializeMedicineSearch(itemIndex) {
    const searchInput = document.getElementById(`medicineSearch_${itemIndex}`);
    const dropdown = document.getElementById(`medicineDropdown_${itemIndex}`);
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        
        // Clear previous timeout
        clearTimeout(searchTimeout);
        
        if (query.length < 2) {
            dropdown.classList.add('hidden');
            return;
        }

        // Debounce search
        searchTimeout = setTimeout(() => {
            searchMedicines(query, itemIndex);
        }, 300);
    });

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
}

async function searchMedicines(query, itemIndex) {
    const dropdown = document.getElementById(`medicineDropdown_${itemIndex}`);
    const loadingIndicator = document.getElementById(`loadingIndicator_${itemIndex}`);
    
    // Show loading state
    loadingIndicator.classList.remove('hidden');
    dropdown.classList.add('hidden');
    
    try {
        const response = await fetch(`/medicines?search=${encodeURIComponent(query)}&per_page=10`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        
        // Hide loading state
        loadingIndicator.classList.add('hidden');
        
        if (data.success && data.data && data.data.length > 0) {
            dropdown.innerHTML = '';
            data.data.forEach((medicine, index) => {
                const option = document.createElement('div');
                option.className = 'medicine-option px-4 py-4 cursor-pointer';
                option.style.animationDelay = `${index * 0.05}s`;
                option.innerHTML = `
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-pills text-blue-600 dark:text-blue-400 text-sm"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="medicine-name text-sm font-semibold">${medicine.name}</p>
                                    <p class="medicine-generic text-xs">${medicine.generic_name || 'Generic name not available'}</p>
                                    <div class="flex items-center space-x-4 mt-1">
                                        <span class="medicine-stock text-xs">
                                            <i class="fas fa-boxes mr-1"></i>Stock: ${medicine.stock_quantity || 0}
                                        </span>
                                        <span class="medicine-price text-xs">
                                            <i class="fas fa-dollar-sign mr-1"></i>Br ${medicine.selling_price || 0}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right ml-4">
                            <div class="flex flex-col items-end space-y-1">
                                <span class="text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded-full">
                                    ${medicine.batch_number || 'No batch'}
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 bg-blue-100 dark:bg-blue-900 px-2 py-1 rounded-full">
                                    ${medicine.category?.name || 'No category'}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
                option.addEventListener('click', () => selectMedicine(medicine, itemIndex));
                dropdown.appendChild(option);
            });
            dropdown.classList.remove('hidden');
        } else {
            dropdown.innerHTML = `
                <div class="no-results">
                    <div class="no-results-icon">
                        <i class="fas fa-search"></i>
                    </div>
                    <p class="text-sm">No medicines found</p>
                    <p class="text-xs text-gray-400 mt-1">Try a different search term</p>
                </div>
            `;
            dropdown.classList.remove('hidden');
        }
    } catch (error) {
        console.error('Error searching medicines:', error);
        loadingIndicator.classList.add('hidden');
        dropdown.innerHTML = `
            <div class="error-message">
                <div class="flex items-center space-x-2">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                    <span class="text-sm">Error loading medicines</span>
                </div>
                <p class="text-xs text-red-400 mt-1">Please try again</p>
            </div>
        `;
        dropdown.classList.remove('hidden');
    }
}

function selectMedicine(medicine, itemIndex) {
    // Update hidden fields
    document.getElementById(`medicineId_${itemIndex}`).value = medicine.id;
    document.getElementById(`unitPrice_${itemIndex}`).value = medicine.selling_price || 0;
    
    // Update search input with animation
    const searchInput = document.getElementById(`medicineSearch_${itemIndex}`);
    searchInput.value = medicine.name;
    searchInput.classList.add('bg-green-50', 'border-green-300');
    
    // Show selected medicine info with enhanced details
    const selectedDiv = document.getElementById(`selectedMedicine_${itemIndex}`);
    document.getElementById(`selectedMedicineName_${itemIndex}`).textContent = medicine.name;
    document.getElementById(`selectedMedicineDetails_${itemIndex}`).innerHTML = `
        <span class="inline-flex items-center space-x-4">
            <span><i class="fas fa-tag mr-1"></i>${medicine.generic_name || 'Generic name not available'}</span>
            <span class="text-green-600 dark:text-green-400"><i class="fas fa-boxes mr-1"></i>Stock: ${medicine.stock_quantity || 0}</span>
            <span class="text-red-600 dark:text-red-400 font-semibold"><i class="fas fa-dollar-sign mr-1"></i>Br ${medicine.selling_price || 0}</span>
        </span>
    `;
    
    selectedDiv.classList.remove('hidden');
    
    // Hide dropdown with animation
    const dropdown = document.getElementById(`medicineDropdown_${itemIndex}`);
    dropdown.style.opacity = '0';
    dropdown.style.transform = 'translateY(-10px)';
    setTimeout(() => {
        dropdown.classList.add('hidden');
        dropdown.style.opacity = '';
        dropdown.style.transform = '';
    }, 200);
    
    // Add success animation to the selected medicine card
    selectedDiv.style.animation = 'cardSlideIn 0.4s ease-out';
    
    // Update financial summary
    updateFinancialSummary();
    
    // Show success notification
    showMedicineSelectionSuccess(medicine.name);
}

function clearMedicineSelection(itemIndex) {
    // Clear all fields
    const searchInput = document.getElementById(`medicineSearch_${itemIndex}`);
    searchInput.value = '';
    searchInput.classList.remove('bg-green-50', 'border-green-300');
    
    document.getElementById(`medicineId_${itemIndex}`).value = '';
    document.getElementById(`unitPrice_${itemIndex}`).value = '';
    
    // Hide selected medicine info with animation
    const selectedDiv = document.getElementById(`selectedMedicine_${itemIndex}`);
    selectedDiv.style.animation = 'cardSlideOut 0.3s ease-in';
    setTimeout(() => {
        selectedDiv.classList.add('hidden');
        selectedDiv.style.animation = '';
    }, 300);
    
    // Update financial summary
    updateFinancialSummary();
}

function showMedicineSelectionSuccess(medicineName) {
    showNotification(`${medicineName} selected successfully!`, 'success');
}

// Professional notification system
function showNotification(message, type = 'info', duration = 4000) {
    console.log('Showing notification:', message, type); // Debug log
    
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(notification => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    });

    // Create notification container if it doesn't exist
    let container = document.getElementById('notification-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notification-container';
        container.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        `;
        document.body.appendChild(container);
    }

    // Define notification styles based on type
    const notificationStyles = {
        success: {
            bg: '#10b981',
            border: '#059669',
            icon: 'fas fa-check-circle',
            iconColor: '#ffffff'
        },
        warning: {
            bg: '#f59e0b',
            border: '#d97706',
            icon: 'fas fa-exclamation-triangle',
            iconColor: '#ffffff'
        },
        error: {
            bg: '#ef4444',
            border: '#dc2626',
            icon: 'fas fa-times-circle',
            iconColor: '#ffffff'
        },
        info: {
            bg: '#3b82f6',
            border: '#2563eb',
            icon: 'fas fa-info-circle',
            iconColor: '#ffffff'
        }
    };

    const style = notificationStyles[type] || notificationStyles.info;

    // Create notification element with inline styles for better reliability
    const notification = document.createElement('div');
    notification.className = 'notification-toast';
    notification.style.cssText = `
        background-color: ${style.bg};
        border: 2px solid ${style.border};
        color: white;
        padding: 16px 24px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        max-width: 400px;
        min-width: 300px;
        transform: translateX(100%);
        transition: all 0.3s ease-in-out;
        pointer-events: auto;
        position: relative;
        margin-bottom: 10px;
    `;
    
    notification.innerHTML = `
        <div style="display: flex; align-items: center; gap: 12px;">
            <div style="flex-shrink: 0;">
                <i class="${style.icon}" style="color: ${style.iconColor}; font-size: 18px;"></i>
            </div>
            <div style="flex: 1;">
                <p style="margin: 0; font-size: 14px; font-weight: 500; line-height: 1.4;">${message}</p>
            </div>
            <button onclick="this.parentElement.parentElement.remove()" style="
                flex-shrink: 0;
                background: none;
                border: none;
                color: white;
                cursor: pointer;
                padding: 4px;
                border-radius: 4px;
                transition: background-color 0.2s;
            " onmouseover="this.style.backgroundColor='rgba(255,255,255,0.2)'" onmouseout="this.style.backgroundColor='transparent'">
                <i class="fas fa-times" style="font-size: 14px;"></i>
            </button>
        </div>
    `;

    // Add to container
    container.appendChild(notification);

    // Force a reflow and then animate in
    notification.offsetHeight; // Force reflow
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 50);

    // Auto remove after duration
    setTimeout(() => {
        if (notification.parentNode) {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }
    }, duration);

    // Add click to dismiss
    notification.addEventListener('click', function(e) {
        if (e.target.closest('button')) return;
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 300);
    });
}

// Make sure the function is globally accessible
window.showNotification = showNotification;

// Test function to verify notifications work
window.testNotification = function() {
    showNotification('Test notification - this should be visible!', 'success', 3000);
};

// Test function to verify new medicine modal works
window.testNewMedicineModal = function() {
    showAddNewMedicineModal();
};

/**
 * Open Purchase History Modal
 */
function showPurchaseHistoryModal() {
    const modal = document.getElementById('purchaseHistoryModal');
    if (modal) {
        modal.classList.remove('hidden');
        loadPurchaseHistory();
    }
}

/**
 * Close Purchase History Modal
 */
function closePurchaseHistoryModal() {
    const modal = document.getElementById('purchaseHistoryModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

/**
 * Load purchase history data
 */
async function loadPurchaseHistory() {
    try {
        const response = await fetch('/purchases/api', {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const data = await response.json();
            if (data.success && data.data) {
                const purchaseHistory = data.data.data || data.data;
                if (purchaseHistory && purchaseHistory.length > 0) {
                    // Update both the main cards and the modal
                    displayPurchaseCards(purchaseHistory);
                    displayPurchaseHistory(purchaseHistory);
                    return;
                }
            }
        }
        
        // Show empty state if no data
        displayEmptyPurchaseState();
    } catch (error) {
        console.error('Failed to load purchase history:', error);
        displayEmptyPurchaseState();
    }
}

/**
 * Display empty purchase state
 */
function displayEmptyPurchaseState() {
    const historyContainer = document.getElementById('purchaseHistoryContainer');
    if (!historyContainer) return;
    
    historyContainer.innerHTML = `
        <div class="text-center py-12">
            <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-500 mb-2">No Purchase History</h3>
            <p class="text-gray-400">No purchases have been recorded yet.</p>
        </div>
    `;
}

/**
 * Display purchase history
 */
function displayPurchaseHistory(purchases) {
    const historyContainer = document.getElementById('purchaseHistoryContainer');
    if (!historyContainer) return;
    
    historyContainer.innerHTML = '';
    
    if (purchases.length === 0) {
        historyContainer.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-box text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-500 mb-2">No Purchase History</h3>
                <p class="text-gray-400">No purchases have been recorded yet.</p>
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
                        <th scope="col" class="px-6 py-3">Purchase ID</th>
                        <th scope="col" class="px-6 py-3">Date & Time</th>
                        <th scope="col" class="px-6 py-3">Supplier</th>
                        <th scope="col" class="px-6 py-3">Items</th>
                        <th scope="col" class="px-6 py-3">Expected Delivery</th>
                        <th scope="col" class="px-6 py-3">Total Amount</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    ${purchases.map(purchase => `
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">#${purchase.id}</td>
                            <td class="px-6 py-4">${purchase.order_date || 'N/A'}</td>
                            <td class="px-6 py-4">${purchase.supplier_name || 'Unknown Supplier'}</td>
                            <td class="px-6 py-4">${purchase.items_count || 0} items</td>
                            <td class="px-6 py-4">${purchase.expected_delivery || 'N/A'}</td>
                            <td class="px-6 py-4 font-medium text-gray-900 dark:text-white">Br ${purchase.total_amount || '0.00'}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-medium rounded-full ${
                                    purchase.status === 'delivered' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                    purchase.status === 'shipped' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' :
                                    purchase.status === 'ordered' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                    'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300'
                                }">
                                    ${purchase.status ? purchase.status.charAt(0).toUpperCase() + purchase.status.slice(1) : 'Pending'}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex space-x-2">
                                    <button onclick="viewPurchaseDetails(${purchase.id})" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button onclick="editPurchase(${purchase.id})" class="text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-300">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button onclick="deletePurchase(${purchase.id})" class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                        <i class="fas fa-trash"></i>
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
    
    // Update total purchases count
    const totalPurchasesCount = document.getElementById('totalPurchasesCount');
    if (totalPurchasesCount) {
        totalPurchasesCount.textContent = purchases.length;
    }
}

// Display sample data for demonstration
function displaySamplePurchaseHistory() {
    const sampleData = [
        {
            id: 1,
            supplier_name: 'MedSupply Co.',
            status: 'delivered',
            order_date: '2024-01-15',
            expected_delivery: '2024-01-20',
            total_amount: '15,450.00',
            items_count: 12
        },
        {
            id: 2,
            supplier_name: 'PharmaDirect',
            status: 'shipped',
            order_date: '2024-01-18',
            expected_delivery: '2024-01-25',
            total_amount: '8,750.00',
            items_count: 8
        },
        {
            id: 3,
            supplier_name: 'HealthCare Plus',
            status: 'ordered',
            order_date: '2024-01-20',
            expected_delivery: '2024-01-28',
            total_amount: '22,300.00',
            items_count: 15
        },
        {
            id: 4,
            supplier_name: 'MedTech Solutions',
            status: 'pending',
            order_date: '2024-01-22',
            expected_delivery: '2024-01-30',
            total_amount: '5,200.00',
            items_count: 5
        }
    ];
    
    displayPurchaseHistory(sampleData);
    document.getElementById('historyCount').textContent = sampleData.length;
}

// View purchase details
function viewPurchaseDetails(purchaseId) {
    showNotification(`Viewing details for Purchase Order #${purchaseId}`, 'info');
    // Implement detailed view functionality
}

// Edit purchase - handled by window.editPurchase function above

// Delete purchase
function deletePurchase(purchaseId) {
    if (confirm(`Are you sure you want to delete Purchase Order #${purchaseId}?`)) {
        showNotification(`Purchase Order #${purchaseId} deleted`, 'success');
        // Implement delete functionality
        loadPurchaseHistory();
    }
}

/**
 * View purchase details
 */
function viewPurchaseDetails(purchaseId) {
    showNotification(`Viewing details for Purchase #${purchaseId}`, 'info');
    // Implement detailed view functionality
}

/**
 * Edit purchase - handled by window.editPurchase function above
 */

/**
 * Delete purchase
 */
function deletePurchase(purchaseId) {
    if (confirm(`Are you sure you want to delete Purchase #${purchaseId}?`)) {
        showNotification(`Purchase #${purchaseId} deleted`, 'success');
        // Implement delete functionality
        loadPurchaseHistory();
    }
}

/**
 * Export purchase history
 */
function exportPurchaseHistory() {
    showNotification('Exporting purchase history...', 'info');
    // Implement export functionality
}

/**
 * Initialize Purchase History Modal
 */
function initializePurchaseHistoryModal() {
    const closeModalBtn = document.getElementById('closePurchaseHistoryModal');
    const applyFiltersBtn = document.getElementById('applyFiltersBtn');
    const clearFiltersBtn = document.getElementById('clearFiltersBtn');
    const exportHistoryBtn = document.getElementById('exportHistoryBtn');
    const refreshHistoryBtn = document.getElementById('refreshHistoryBtn');
    const purchaseSearchInput = document.getElementById('purchaseSearchInput');
    
    // Close modal
    if (closeModalBtn) {
        closeModalBtn.addEventListener('click', closePurchaseHistoryModal);
    }
    
    // Apply filters
    if (applyFiltersBtn) {
        applyFiltersBtn.addEventListener('click', function() {
            applyPurchaseHistoryFilters();
        });
    }
    
    // Clear filters
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', function() {
            clearPurchaseHistoryFilters();
        });
    }
    
    // Export history
    if (exportHistoryBtn) {
        exportHistoryBtn.addEventListener('click', function() {
            exportPurchaseHistory();
        });
    }
    
    // Refresh history
    if (refreshHistoryBtn) {
        refreshHistoryBtn.addEventListener('click', function() {
            loadPurchaseHistory();
        });
    }
    
    // Search functionality
    if (purchaseSearchInput) {
        purchaseSearchInput.addEventListener('input', function() {
            filterPurchaseHistory();
        });
    }
}

/**
 * Apply purchase history filters
 */
function applyPurchaseHistoryFilters() {
    showNotification('Applying filters...', 'info');
    // Implement filter logic
    loadPurchaseHistory();
}

/**
 * Clear purchase history filters
 */
function clearPurchaseHistoryFilters() {
    document.getElementById('purchaseSearchInput').value = '';
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    document.getElementById('supplierFilter').value = '';
    document.getElementById('statusFilter').value = '';
    showNotification('Filters cleared', 'info');
    loadPurchaseHistory();
}

/**
 * Filter purchase history
 */
function filterPurchaseHistory() {
    // Implement real-time filtering
    loadPurchaseHistory();
}

// Add New Medicine Modal Functions
function showAddNewMedicineModal() {
    const modal = document.getElementById('addNewMedicineModal');
    if (modal) {
        console.log('Opening new medicine modal...'); // Debug log
        modal.classList.remove('hidden');
        modal.style.display = 'flex'; // Ensure it's displayed as flex
        document.body.style.overflow = 'hidden';
        
        // Clear form
        document.getElementById('addNewMedicineForm').reset();
        
        // Focus on first input
        setTimeout(() => {
            document.getElementById('newMedicineName').focus();
        }, 100);
    } else {
        console.error('New medicine modal not found!');
    }
}

function closeAddNewMedicineModal() {
    const modal = document.getElementById('addNewMedicineModal');
    if (modal) {
        console.log('Closing new medicine modal...'); // Debug log
        modal.classList.add('hidden');
        modal.style.display = 'none'; // Ensure it's hidden
        document.body.style.overflow = 'auto';
        
        // Clear form
        document.getElementById('addNewMedicineForm').reset();
    } else {
        console.error('New medicine modal not found for closing!');
    }
}

// Add new medicine to purchase
function addNewMedicineToPurchase() {
    const form = document.getElementById('addNewMedicineForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const medicineName = document.getElementById('newMedicineName').value.trim();
    const quantity = document.getElementById('newQuantity').value;
    const unitPrice = document.getElementById('newUnitPrice').value;
    
    if (!medicineName) {
        showNotification('Please enter a medicine name', 'warning');
        document.getElementById('newMedicineName').focus();
        return;
    }
    
    if (!quantity || quantity < 1) {
        showNotification('Please enter a valid quantity', 'warning');
        document.getElementById('newQuantity').focus();
        return;
    }
    
    if (!unitPrice || unitPrice < 0) {
        showNotification('Please enter a valid unit price', 'warning');
        document.getElementById('newUnitPrice').focus();
        return;
    }
    
    // Create new medicine item
    const itemsContainer = document.getElementById('itemsContainer');
    const itemIndex = itemsContainer.children.length;
    
    const itemDiv = document.createElement('div');
    itemDiv.className = 'bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-6 shadow-sm hover:shadow-md transition-all duration-200';
    itemDiv.innerHTML = `
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                    <i class="fas fa-plus text-orange-600 dark:text-orange-400 text-sm"></i>
                </div>
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">New Medicine Item</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400">New medicine not in inventory</p>
                </div>
            </div>
            <button type="button" onclick="removePurchaseItem(this)" class="flex items-center space-x-1 px-3 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 dark:text-red-400 dark:hover:text-red-300 dark:hover:bg-red-900/20 rounded-lg transition-colors duration-200">
                <i class="fas fa-trash text-xs"></i>
                <span class="text-xs font-medium">Remove</span>
            </button>
        </div>
        
        <div class="space-y-6">
            <!-- Medicine Information -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Medicine *</label>
                <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pills text-blue-600 dark:text-blue-400"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-blue-800 dark:text-blue-200">${medicineName}</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">${formData.get('generic_name') || 'Generic name not provided'} | Manufacturer: ${formData.get('manufacturer') || 'Not specified'}</p>
                            <p class="text-xs text-blue-600 dark:text-blue-400">Batch: ${formData.get('batch_number') || 'Not specified'} | Price: Br ${unitPrice}</p>
                        </div>
                    </div>
                </div>
                <input type="hidden" name="items[${itemIndex}][medicine_name]" value="${medicineName}">
                <input type="hidden" name="items[${itemIndex}][generic_name]" value="${formData.get('generic_name')}">
                <input type="hidden" name="items[${itemIndex}][manufacturer]" value="${formData.get('manufacturer')}">
                <input type="hidden" name="items[${itemIndex}][batch_number]" value="${formData.get('batch_number')}">
                <input type="hidden" name="items[${itemIndex}][expiry_date]" value="${formData.get('expiry_date')}">
                <input type="hidden" name="items[${itemIndex}][notes]" value="${formData.get('notes')}">
            </div>
            
            <!-- Quantity and Price -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Quantity *</label>
                    <input type="number" 
                           name="items[${itemIndex}][quantity]" 
                           required min="1" 
                           value="${quantity}"
                           placeholder="Enter quantity..."
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input"
                           onchange="updateFinancialSummary()">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Unit Price (Br) *</label>
                    <input type="number" 
                           name="items[${itemIndex}][unit_price]" 
                           required min="0" 
                           step="0.01" 
                           value="${unitPrice}"
                           placeholder="Enter unit price..."
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark-input"
                           onchange="updateFinancialSummary()">
                </div>
            </div>
        </div>
    `;
    
    itemsContainer.insertBefore(itemDiv, itemsContainer.firstChild);
    updateFinancialSummary();
    
    // Show success notification
    showNotification(`Successfully added "${medicineName}" to purchase order`, 'success');
    
    // Close modal
    closeAddNewMedicineModal();
}

// Add CSS for card slide out animation
const style = document.createElement('style');
style.textContent = `
    @keyframes cardSlideOut {
        from {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
        to {
            opacity: 0;
            transform: translateY(-10px) scale(0.95);
        }
    }
`;
document.head.appendChild(style);

function quickAddMedicine() {
    const supplierId = document.getElementById('supplierSelect').value;
    if (!supplierId) {
        // Try the notification system first
        if (typeof showNotification === 'function') {
            showNotification('Please select a supplier first to add medicines', 'warning');
        } else {
            // Fallback to a simple visual indicator
            const supplierSelect = document.getElementById('supplierSelect');
            supplierSelect.style.borderColor = '#ef4444';
            supplierSelect.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
            supplierSelect.focus();
            
            // Create a simple inline notification
            const existingAlert = document.getElementById('supplier-alert');
            if (existingAlert) existingAlert.remove();
            
            const alertDiv = document.createElement('div');
            alertDiv.id = 'supplier-alert';
            alertDiv.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm';
            alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Please select a supplier first to add medicines';
            
            supplierSelect.parentNode.appendChild(alertDiv);
            
            // Remove the alert after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
                supplierSelect.style.borderColor = '';
                supplierSelect.style.boxShadow = '';
            }, 5000);
        }
        return;
    }
    
    // Open the add new medicine modal
    showAddNewMedicineModal();
}

function updateFinancialSummary() {
    let subtotal = 0;
    const items = document.querySelectorAll('[name*="[quantity]"]');
    
    items.forEach((quantityInput) => {
        const quantity = parseFloat(quantityInput.value) || 0;
        // Find the corresponding unit price hidden field
        const itemContainer = quantityInput.closest('.bg-white');
        const unitPriceInput = itemContainer.querySelector('[name*="[unit_price]"]');
        const price = parseFloat(unitPriceInput?.value) || 0;
        subtotal += quantity * price;
    });
    
    const tax = subtotal * 0.1; // 10% tax
    const shipping = parseFloat(document.getElementById('shippingAmount')?.value) || 0;
    const total = subtotal + tax + shipping;
    
    // Update the financial summary display
    const subtotalElement = document.getElementById('subtotalAmount');
    const taxElement = document.getElementById('taxAmount');
    const totalElement = document.getElementById('totalAmount');
    
    if (subtotalElement) subtotalElement.textContent = subtotal.toFixed(2) + ' Birr';
    if (taxElement) taxElement.textContent = tax.toFixed(2) + ' Birr';
    if (totalElement) totalElement.textContent = total.toFixed(2) + ' Birr';
}

function submitPurchaseForm() {
    const form = document.getElementById('addPurchaseForm');
    const formData = new FormData(form);
    
    // Validate required fields
    const supplierId = document.getElementById('supplierSelect').value;
    if (!supplierId) {
        // Try the notification system first
        if (typeof showNotification === 'function') {
            showNotification('Please select a supplier to continue', 'warning');
        } else {
            // Fallback to a simple visual indicator
            const supplierSelect = document.getElementById('supplierSelect');
            supplierSelect.style.borderColor = '#ef4444';
            supplierSelect.style.boxShadow = '0 0 0 3px rgba(239, 68, 68, 0.1)';
            supplierSelect.focus();
            
            // Create a simple inline notification
            const existingAlert = document.getElementById('supplier-alert');
            if (existingAlert) existingAlert.remove();
            
            const alertDiv = document.createElement('div');
            alertDiv.id = 'supplier-alert';
            alertDiv.className = 'mt-2 p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-sm';
            alertDiv.innerHTML = '<i class="fas fa-exclamation-triangle mr-2"></i>Please select a supplier to continue';
            
            supplierSelect.parentNode.appendChild(alertDiv);
            
            // Remove the alert after 5 seconds
            setTimeout(() => {
                if (alertDiv.parentNode) {
                    alertDiv.parentNode.removeChild(alertDiv);
                }
                supplierSelect.style.borderColor = '';
                supplierSelect.style.boxShadow = '';
            }, 5000);
        }
        return;
    }
    
    const orderDate = document.getElementById('orderDate').value;
    const expectedDelivery = document.getElementById('expectedDelivery').value;
    
    if (!orderDate || !expectedDelivery) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Check if items are added
    const items = document.querySelectorAll('[name*="[medicine_id]"]');
    if (items.length === 0) {
        alert('Please add at least one item to the purchase order');
        return;
    }
    
    // Implementation for submitting purchase form
    console.log('Submitting purchase form...');
    
    // Here you would typically send the data to the server
    // For now, just close the modal
    document.getElementById('addPurchaseModal').classList.add('hidden');
    alert('Purchase order created successfully!');
}

// Pagination functions
function previousPage() {
    const currentPage = parseInt(document.querySelector('.pagination-info').textContent.match(/Showing \d+ to \d+ of \d+ results/)?.[0]?.split(' ')[1]) || 1;
    if (currentPage > 1) {
        const url = new URL(window.location);
        url.searchParams.set('page', currentPage - 1);
        window.location.href = url.toString();
    }
}

function nextPage() {
    const currentPage = parseInt(document.querySelector('.pagination-info').textContent.match(/Showing \d+ to \d+ of \d+ results/)?.[0]?.split(' ')[1]) || 1;
    const totalResults = parseInt(document.querySelector('.pagination-info').textContent.match(/of (\d+) results/)?.[1]) || 0;
    const resultsPerPage = 10; // Adjust based on your pagination settings
    const totalPages = Math.ceil(totalResults / resultsPerPage);
    
    if (currentPage < totalPages) {
        const url = new URL(window.location);
        url.searchParams.set('page', currentPage + 1);
        window.location.href = url.toString();
    }
}

function goToPage(pageNumber) {
    const url = new URL(window.location);
    url.searchParams.set('page', pageNumber);
    window.location.href = url.toString();
}

// Initialize pagination on page load
document.addEventListener('DOMContentLoaded', function() {
    initializePagination();
});

function initializePagination() {
    const paginationInfo = document.querySelector('.pagination-info');
    if (!paginationInfo) return;
    
    const currentPage = parseInt(paginationInfo.textContent.match(/Showing \d+ to \d+ of \d+ results/)?.[0]?.split(' ')[1]) || 1;
    const totalResults = parseInt(paginationInfo.textContent.match(/of (\d+) results/)?.[1]) || 0;
    const resultsPerPage = 10; // Adjust based on your pagination settings
    const totalPages = Math.ceil(totalResults / resultsPerPage);
    
    // Update Previous button state
    const prevButton = document.querySelector('.pagination-prev');
    if (prevButton) {
        prevButton.disabled = currentPage <= 1;
        if (currentPage <= 1) {
            prevButton.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            prevButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    // Update Next button state
    const nextButton = document.querySelector('.pagination-next');
    if (nextButton) {
        nextButton.disabled = currentPage >= totalPages;
        if (currentPage >= totalPages) {
            nextButton.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            nextButton.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    // Generate page numbers
    const pageNumbersContainer = document.getElementById('pageNumbers');
    if (pageNumbersContainer && totalPages > 1) {
        pageNumbersContainer.innerHTML = '';
        
        // Show up to 5 page numbers
        const startPage = Math.max(1, currentPage - 2);
        const endPage = Math.min(totalPages, startPage + 4);
        
        for (let i = startPage; i <= endPage; i++) {
            const pageButton = document.createElement('button');
            pageButton.className = `px-3 py-2 text-sm font-medium rounded-lg transition-colors duration-200 ${
                i === currentPage 
                    ? 'bg-green-600 text-white' 
                    : 'text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600'
            }`;
            pageButton.textContent = i;
            pageButton.onclick = () => goToPage(i);
            pageNumbersContainer.appendChild(pageButton);
        }
    }
}

// View switching functionality
function initializeViewSwitching() {
    const cardsViewBtn = document.getElementById('cardsViewBtn');
    const tableViewBtn = document.getElementById('tableViewBtn');
    const cardsView = document.getElementById('cardView');
    const tableView = document.getElementById('tableView');

    if (!cardsViewBtn || !tableViewBtn || !cardsView || !tableView) return;

    // Cards view button click
    cardsViewBtn.addEventListener('click', function() {
        // Update button styles
        cardsViewBtn.className = 'px-4 py-2 text-sm font-semibold text-white bg-orange-500 rounded-lg shadow-md transition-all duration-200';
        tableViewBtn.className = 'px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-all duration-200';
        
        // Show cards view, hide table view
        cardsView.classList.remove('hidden');
        tableView.classList.add('hidden');
        
        // Store preference in localStorage
        localStorage.setItem('purchaseView', 'cards');
    });

    // Table view button click
    tableViewBtn.addEventListener('click', function() {
        // Update button styles
        tableViewBtn.className = 'px-4 py-2 text-sm font-semibold text-white bg-orange-500 rounded-lg shadow-md transition-all duration-200';
        cardsViewBtn.className = 'px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-all duration-200';
        
        // Show table view, hide cards view
        tableView.classList.remove('hidden');
        cardsView.classList.add('hidden');
        
        // Store preference in localStorage
        localStorage.setItem('purchaseView', 'table');
    });

    // Load saved preference
    const savedView = localStorage.getItem('purchaseView');
    if (savedView === 'table') {
        tableViewBtn.click();
    } else {
        cardsViewBtn.click();
    }
}

// Initialize view switching when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeViewSwitching();
    initializePurchaseHistoryModal();
    
    // Add event listeners for view purchase modal
    const viewPurchaseModal = document.getElementById('viewPurchaseModal');
    if (viewPurchaseModal) {
        // Close modal when clicking on backdrop
        viewPurchaseModal.addEventListener('click', function(e) {
            if (e.target === this) {
                hideViewPurchaseModal();
            }
        });
        
        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && !viewPurchaseModal.classList.contains('hidden')) {
                hideViewPurchaseModal();
            }
        });
    }
});
</script>

<!-- Edit Purchase Modal -->
<div id="editPurchaseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-6xl w-full max-h-[90vh] flex flex-col">
        <!-- Modal Header - Fixed -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Purchase Order</h2>
                    <p class="text-gray-600 dark:text-gray-300">Update purchase order information</p>
                </div>
            </div>
            <button onclick="hideEditPurchaseModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Content - Scrollable -->
        <div class="flex-1 overflow-y-auto p-6">
            <form id="editPurchaseForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Purchase Number</label>
                            <input type="text" id="editPurchaseNumber" name="purchase_number" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier</label>
                            <select id="editSupplier" name="supplier_id" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                                <option value="">Select Supplier</option>
                                @foreach($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Order Date</label>
                            <input type="date" id="editOrderDate" name="order_date" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expected Delivery</label>
                            <input type="date" id="editExpectedDelivery" name="expected_delivery" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select id="editStatus" name="status" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                                <option value="pending">Pending</option>
                                <option value="received">Received</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Additional Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Additional Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Amount (Birr)</label>
                            <input type="number" id="editTotalAmount" name="total_amount" min="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" readonly>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                            <textarea id="editNotes" name="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Additional notes or comments..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Purchase Items Section -->
                <div class="mt-8">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Purchase Items</h3>
                    <div id="editPurchaseItems" class="space-y-4">
                        <!-- Items will be populated dynamically -->
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer - Fixed -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideEditPurchaseModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                Cancel
            </button>
            <button onclick="savePurchase()" class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-lg transition-all duration-200 flex items-center">
                <i class="fas fa-save mr-2"></i>
                Save Changes
            </button>
        </div>
    </div>
</div>

<!-- View Purchase Modal -->
<div id="viewPurchaseModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-eye text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase Details</h2>
                    <p class="text-gray-600 dark:text-gray-400">View Purchase Information</p>
                </div>
            </div>
            <button onclick="hideViewPurchaseModal()" class="w-10 h-10 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <div id="viewPurchaseContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideViewPurchaseModal()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Analytics Modal -->
<div id="analyticsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Purchase Analytics</h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Comprehensive insights into your purchase data</p>
            </div>
            <button onclick="hideAnalyticsModal()" class="w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Analytics Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Purchases</p>
                            <p class="text-3xl font-bold">156</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-shopping-cart text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Value</p>
                            <p class="text-3xl font-bold">Br 45,230</p>
                        </div>
                        <div class="w-12 h-12 bg-green-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-dollar-sign text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Avg. Order Value</p>
                            <p class="text-3xl font-bold">Br 290</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-chart-line text-xl"></i>
                        </div>
                    </div>
                </div>
                
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Pending Orders</p>
                            <p class="text-3xl font-bold">12</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-clock text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Purchase Trends Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Purchase Trends</h3>
                    <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-700 rounded-lg">
                        <div class="text-center">
                            <i class="fas fa-chart-line text-4xl text-gray-400 mb-2"></i>
                            <p class="text-gray-500 dark:text-gray-400">Chart visualization would go here</p>
                        </div>
                    </div>
                </div>

                <!-- Supplier Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Top Suppliers</h3>
                    <div class="space-y-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-blue-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-900 dark:text-white">Walk-in Supplier</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">45 orders</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-green-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-900 dark:text-white">PharmaCorp Ltd</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">32 orders</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="w-3 h-3 bg-purple-500 rounded-full mr-3"></div>
                                <span class="text-sm text-gray-900 dark:text-white">MedSupply Inc</span>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">28 orders</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status Distribution -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Purchase Status Distribution</h3>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="text-center p-4 bg-blue-50 dark:bg-blue-900 rounded-lg">
                        <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">45</div>
                        <div class="text-sm text-blue-600 dark:text-blue-400">Completed</div>
                    </div>
                    <div class="text-center p-4 bg-yellow-50 dark:bg-yellow-900 rounded-lg">
                        <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">12</div>
                        <div class="text-sm text-yellow-600 dark:text-yellow-400">Pending</div>
                    </div>
                    <div class="text-center p-4 bg-green-50 dark:bg-green-900 rounded-lg">
                        <div class="text-2xl font-bold text-green-600 dark:text-green-400">8</div>
                        <div class="text-sm text-green-600 dark:text-green-400">Shipped</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 dark:bg-red-900 rounded-lg">
                        <div class="text-2xl font-bold text-red-600 dark:text-red-400">3</div>
                        <div class="text-sm text-red-600 dark:text-red-400">Cancelled</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Import/Export Modal -->
<div id="importExportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import/Export Purchases</h2>
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
                    
                    <!-- Date Range -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Date Range</label>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">From Date</label>
                                <input type="date" name="start_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark-input">
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 dark:text-gray-400 mb-2">To Date</label>
                                <input type="date" name="end_date" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-transparent dark-input">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export Options -->
                    <div>
                        <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Include</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="include_items" checked class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <span class="ml-3 text-gray-900 dark:text-white">Purchase Items</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="include_suppliers" checked class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <span class="ml-3 text-gray-900 dark:text-white">Supplier Information</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="include_financials" checked class="w-4 h-4 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 focus:ring-blue-500">
                                <span class="ml-3 text-gray-900 dark:text-white">Financial Details</span>
                            </label>
                        </div>
                    </div>
                </div>
                </form>
            </div>

            <!-- Print Tab Content -->
            <div id="printContent" class="p-6 hidden">
                <div class="space-y-6">
                    <div class="text-center">
                        <i class="fas fa-print text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Print Purchase Reports</h3>
                        <p class="text-gray-600 dark:text-gray-400">Generate and print purchase reports</p>
                    </div>
                    
                    <div class="space-y-4">
                        <button onclick="printPurchaseReport('summary')" class="w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-print mr-2"></i>Print Summary Report
                        </button>
                        <button onclick="printPurchaseReport('detailed')" class="w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-file-alt mr-2"></i>Print Detailed Report
                        </button>
                        <button onclick="printPurchaseReport('financial')" class="w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center justify-center">
                            <i class="fas fa-chart-bar mr-2"></i>Print Financial Report
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideImportExportModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                Cancel
            </button>
            <button id="modalActionBtn" onclick="processImportExport()" class="px-6 py-2 text-sm font-semibold text-white bg-orange-500 hover:bg-orange-600 rounded-lg transition-all duration-200">
                <i class="fas fa-download mr-2"></i>Import
            </button>
        </div>
    </div>
</div>

<script>
// Analytics Modal Functions
function showAnalyticsModal() {
    document.getElementById('analyticsModal').classList.remove('hidden');
}

function hideAnalyticsModal() {
    document.getElementById('analyticsModal').classList.add('hidden');
}

// Import/Export Modal Functions
function showImportExportModal() {
    document.getElementById('importExportModal').classList.remove('hidden');
}

function hideImportExportModal() {
    document.getElementById('importExportModal').classList.add('hidden');
}

// Tab switching functionality
function switchTab(tabName) {
    // Hide all content
    document.getElementById('importContent').classList.add('hidden');
    document.getElementById('exportContent').classList.add('hidden');
    document.getElementById('printContent').classList.add('hidden');
    
    // Remove active styles from all tabs
    document.getElementById('importTab').className = 'flex-1 px-6 py-4 text-left border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium flex items-center justify-center hover:text-gray-700 dark:hover:text-gray-300';
    document.getElementById('exportTab').className = 'flex-1 px-6 py-4 text-left border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium flex items-center justify-center hover:text-gray-700 dark:hover:text-gray-300';
    document.getElementById('printTab').className = 'flex-1 px-6 py-4 text-left border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium flex items-center justify-center hover:text-gray-700 dark:hover:text-gray-300';
    
    // Show selected content and update button
    if (tabName === 'import') {
        document.getElementById('importContent').classList.remove('hidden');
        document.getElementById('importTab').className = 'flex-1 px-6 py-4 text-left border-b-2 border-orange-500 text-orange-500 font-semibold flex items-center justify-center';
        document.getElementById('modalActionBtn').innerHTML = '<i class="fas fa-download mr-2"></i>Import';
    } else if (tabName === 'export') {
        document.getElementById('exportContent').classList.remove('hidden');
        document.getElementById('exportTab').className = 'flex-1 px-6 py-4 text-left border-b-2 border-orange-500 text-orange-500 font-semibold flex items-center justify-center';
        document.getElementById('modalActionBtn').innerHTML = '<i class="fas fa-upload mr-2"></i>Export';
    } else if (tabName === 'print') {
        document.getElementById('printContent').classList.remove('hidden');
        document.getElementById('printTab').className = 'flex-1 px-6 py-4 text-left border-b-2 border-orange-500 text-orange-500 font-semibold flex items-center justify-center';
        document.getElementById('modalActionBtn').innerHTML = '<i class="fas fa-print mr-2"></i>Print';
    }
}

// Process import/export based on current tab
function processImportExport() {
    const activeTab = document.querySelector('[id$="Tab"].border-orange-500');
    if (activeTab && activeTab.id === 'importTab') {
        console.log('Processing import...');
        // Implement import logic
    } else if (activeTab && activeTab.id === 'exportTab') {
        console.log('Processing export...');
        // Implement export logic
    } else if (activeTab && activeTab.id === 'printTab') {
        console.log('Processing print...');
        // Implement print logic
    }
}

// Print report functions
function printPurchaseReport(type) {
    console.log('Printing purchase report:', type);
    // Implement print logic based on type
}

// File selection functionality
document.addEventListener('DOMContentLoaded', function() {
    const selectFileBtn = document.getElementById('selectFileBtn');
    const importFile = document.getElementById('importFile');
    const selectedFileName = document.getElementById('selectedFileName');
    
    if (selectFileBtn && importFile && selectedFileName) {
        selectFileBtn.addEventListener('click', function() {
            importFile.click();
        });
        
        importFile.addEventListener('change', function() {
            if (this.files.length > 0) {
                selectedFileName.textContent = this.files[0].name;
                selectedFileName.classList.remove('hidden');
            }
        });
    }
});
</script>
@endsection
