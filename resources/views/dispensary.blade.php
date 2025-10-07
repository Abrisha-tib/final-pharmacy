@extends('layouts.app')

@section('title', 'Dispensary Management - Analog Pharmacy Management System')
@section('page-title', 'Dispensary Management')
@section('page-description', 'Manage your pharmacy\'s dispensary stock and sales floor inventory')

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
    
    /* Dispensary specific styles */
    .dispensary-card {
        transition: all 0.3s ease;
        border-left: 4px solid #10b981;
    }
    
    .dispensary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }
    
    .stock-indicator {
        position: absolute;
        top: 8px;
        right: 8px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
    }
    
    .stock-high { background-color: #10b981; }
    .stock-medium { background-color: #f59e0b; }
    .stock-low { background-color: #ef4444; }
    
    /* Transfer Modal Styles */
    #transferModal {
        overflow-y: auto;
    }
    
    #transferModal .modal-content {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    
    #transferModal .modal-body {
        flex: 1;
        overflow-y: auto;
        min-height: 0;
    }
    
    #transferModal .modal-footer {
        flex-shrink: 0;
        margin-top: auto;
    }
    
    @media (max-height: 600px) {
        #transferModal .modal-content {
            max-height: 95vh;
        }
    }
    
</style>

<!-- Welcome Section -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-green-50 to-emerald-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-green-200 dark:border-gray-600">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Dispensary Management</h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg">Manage your pharmacy's dispensary stock and sales floor inventory</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Dispensary Status</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white" id="dispensaryStatus">Ready for Dispensing</p>
            </div>
        </div>
    </div>
</div>

<!-- Dispensary Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Items Card -->
    <div class="card-hover bg-gradient-to-br from-purple-400 to-purple-500 dark:from-purple-800 dark:to-purple-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-purple-600 dark:border-purple-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-purple-800 dark:text-purple-200 uppercase tracking-wide">Total Items</p>
                <p class="text-3xl font-bold text-purple-900 dark:text-white mt-2 mb-1" id="totalItems">{{ $totalMedicines }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-purple-500 dark:bg-purple-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-boxes text-xs mr-1"></i>
                        {{ $inStock }} Available
                    </div>
                    <span class="text-xs text-purple-700 dark:text-purple-300 font-bold">items</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-boxes text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Value Card -->
    <div class="card-hover bg-gradient-to-br from-teal-400 to-teal-500 dark:from-teal-800 dark:to-teal-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-teal-600 dark:border-teal-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-teal-800 dark:text-teal-200 uppercase tracking-wide">Total Value</p>
                <p class="text-3xl font-bold text-teal-900 dark:text-white mt-2 mb-1" id="totalValue">Br {{ number_format($totalValue, 2) }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-teal-500 dark:bg-teal-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-dollar-sign text-xs mr-1"></i>
                        +2.1%
                    </div>
                    <span class="text-xs text-teal-700 dark:text-teal-300 font-bold">this month</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-teal-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-dollar-sign text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- In Stock Card -->
    <div class="card-hover bg-gradient-to-br from-green-400 to-green-500 dark:from-green-800 dark:to-green-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-green-600 dark:border-green-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-green-800 dark:text-green-200 uppercase tracking-wide">In Stock</p>
                <p class="text-3xl font-bold text-green-900 dark:text-white mt-2 mb-1" id="inStock">{{ $inStock }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-green-500 dark:bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-check-circle text-xs mr-1"></i>
                        {{ $lowStockMedicines }} Low
                    </div>
                    <span class="text-xs text-green-700 dark:text-green-300 font-bold">medicines</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Out of Stock Card -->
    <div class="card-hover bg-gradient-to-br from-orange-400 to-orange-500 dark:from-orange-800 dark:to-orange-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-orange-600 dark:border-orange-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-orange-800 dark:text-orange-200 uppercase tracking-wide">Out of Stock</p>
                <p class="text-3xl font-bold text-orange-900 dark:text-white mt-2 mb-1" id="outOfStock">{{ $totalMedicines - $inStock }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-orange-500 dark:bg-orange-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-exclamation-triangle text-xs mr-1"></i>
                        Need Restock
                    </div>
                    <span class="text-xs text-orange-700 dark:text-orange-300 font-bold">items</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-times-circle text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Navigation Tabs -->
<div class="mb-8">
    <div class="flex items-center justify-between">
        <!-- Left Group: Segmented Control + Analytics -->
        <div class="flex items-center gap-4">
            <!-- Segmented Control: Cards & Table -->
            <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1 shadow-sm">
                <button id="cardsBtn" class="px-4 py-2 bg-orange-500 text-white rounded-md font-semibold text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-th-large mr-2 text-sm"></i>Cards
                </button>
                <button id="tableBtn" class="px-4 py-2 bg-transparent text-gray-700 dark:text-gray-200 rounded-md font-medium text-sm transition-all duration-200 flex items-center hover:bg-gray-200 dark:hover:bg-gray-600">
                    <i class="fas fa-table mr-2 text-sm"></i>Table
                </button>
            </div>
            
            <!-- Show Analytics Button -->
            <button onclick="showAnalyticsModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 shadow-sm flex items-center">
                <i class="fas fa-chart-bar mr-2 text-sm"></i>Show Analytics
            </button>
        </div>
        
        <!-- Right Group: Import/Export -->
        <div class="flex items-center gap-3 no-print">
            <button id="importExportBtn" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium text-sm transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center">
                <i class="fas fa-download mr-2 text-sm"></i>Import/Export
            </button>
        </div>
    </div>
</div>

<!-- Search Medicines Section -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-8 no-print">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Search Field -->
        <div class="flex flex-col">
            <label class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Search Items</label>
            <div class="relative">
                <input type="text" id="medicineSearch" placeholder="Search by name, generic name, or batch numbe" 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="flex flex-col">
            <label class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Category</label>
            <select id="categoryFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- Batch Number Filter -->
        <div class="flex flex-col">
            <label class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Batch Number</label>
            <select id="batchFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                <option value="">All Batch Numbers</option>
                <!-- Batch numbers will be populated dynamically -->
            </select>
        </div>

        <!-- Date Filter -->
        <div class="flex flex-col">
            <label class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Date</label>
            <div class="relative">
                <input type="text" id="dateFilter" placeholder="mm/dd/yyyy" 
                       class="w-full pl-4 pr-10 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                <i class="fas fa-calendar absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>
    </div>
</div>

<!-- Medicines Grid -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-gray-600 dark:text-gray-400">
                <span>Showing {{ $medicines->count() }} of {{ $totalMedicines }} items</span>
            </div>
            <div class="flex gap-4">
                <button id="refreshBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2 text-sm"></i>Refresh
                </button>
                <button id="printBtn" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg font-medium text-sm transition-all duration-200 hover:bg-gray-50 flex items-center">
                    <i class="fas fa-print mr-2 text-sm"></i>Print
                </button>
                <button id="transferFromInventoryBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-arrow-right mr-2 text-sm"></i>Transfer from Inventory
                </button>
                <button id="transferHistoryBtn" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-file-alt mr-2 text-sm"></i>Transfer History
                </button>
            </div>
        </div>
    </div>

    <div class="p-6 bg-gray-50 dark:bg-gray-900">
        @if($medicines->count() > 0)
            <!-- Cards View -->
            <div id="cardView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($medicines as $medicine)
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 relative hover:shadow-lg transition-all duration-300">
                        <!-- Medicine Header -->
                        <div class="mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-1">
                                {{ $medicine->name }}
                            </h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300 mb-3">
                                {{ $medicine->generic_name }}
                            </p>
                            
                            <!-- Tags -->
                            <div class="flex flex-wrap gap-2 mb-4">
                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $medicine->category ? 'bg-blue-600 text-white' : 'bg-gray-600 text-gray-300' }}">
                                    {{ $medicine->category->name ?? 'Uncategorized' }}
                                </span>
                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-green-600 text-white">
                                    Active
                                </span>
                            </div>
                        </div>

                        <!-- Medicine Details -->
                        <div class="space-y-3 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Strength & Form:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $medicine->strength }} {{ $medicine->form }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Barcode:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $medicine->barcode ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Batch Number:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $medicine->batch_number ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Manufacturer:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $medicine->manufacturer ?? 'N/A' }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Dispensary Stock:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $medicine->transfers->where('transfer_type', 'inventory_to_dispensary')->where('status', 'completed')->sum('quantity_remaining') }} {{ $medicine->unit }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Remaining Stock:</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $medicine->stock_quantity }} {{ $medicine->unit }}</span>
                            </div>
                        </div>

                        <!-- Pricing Section -->
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-3 mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Selling Price:</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Br {{ number_format($medicine->selling_price, 2) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Cost Price:</span>
                                <span class="text-sm text-gray-900 dark:text-white">Br {{ number_format($medicine->cost_price, 2) }}</span>
                            </div>
                        </div>

                        <!-- Prescription Section -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Prescription:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">Not Required</span>
                            </div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">Active</div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex space-x-2">
                            <button onclick="viewMedicineDetails({{ $medicine->id }})" 
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-eye mr-2"></i>View Details
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Table View -->
            <div id="tableView" class="hidden">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Medicine</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dispensary Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Remaining Stock</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Price</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($medicines as $medicine)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                                    <i class="fas fa-pills text-blue-600 dark:text-blue-400"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $medicine->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $medicine->generic_name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $medicine->category ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' }}">
                                            {{ $medicine->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <div class="flex items-center">
                                            <span class="font-medium">{{ $medicine->transfers->where('transfer_type', 'inventory_to_dispensary')->where('status', 'completed')->sum('quantity_transferred') }}</span>
                                            <span class="text-gray-500 dark:text-gray-400 ml-1">{{ $medicine->unit }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Transferred</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        <div class="flex items-center">
                                            <span class="font-medium">{{ $medicine->stock_quantity }}</span>
                                            <span class="text-gray-500 dark:text-gray-400 ml-1">{{ $medicine->unit }}</span>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Batch: {{ $medicine->batch_number ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">Br {{ number_format($medicine->selling_price, 2) }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Cost: Br {{ number_format($medicine->cost_price, 2) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($medicine->stock_quantity > 10)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                In Stock
                                            </span>
                                        @elseif($medicine->stock_quantity > 0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                                                Low Stock
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Out of Stock
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="viewMedicineDetails({{ $medicine->id }})" 
                                                    class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-pills text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Medicines Found</h3>
                <p class="text-gray-500 dark:text-gray-500">Transfer medicines from inventory to dispensary first</p>
            </div>
        @endif
    </div>

    <!-- Pagination and Status -->
    <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 no-print">
        <div class="flex items-center justify-between">
            <!-- Pagination Info -->
            <div class="text-sm text-gray-700 dark:text-gray-300 pagination-info">
                Showing {{ $medicines->firstItem() ?? 0 }} to {{ $medicines->lastItem() ?? 0 }} of {{ $medicines->total() }} results
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



<!-- Dispense Medicine Modal -->
<div id="dispenseModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] backdrop-blur-sm">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Dispense Medicine</h3>
                    <button onclick="closeDispenseModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div id="medicineDetails" class="mb-4">
                    <!-- Medicine details will be loaded here -->
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quantity to Dispense</label>
                    <input type="number" id="dispenseQuantity" min="1" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Patient Name</label>
                    <input type="text" id="patientName" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" placeholder="Enter patient name">
                </div>
                
                <div class="flex space-x-3">
                    <button onclick="confirmDispense()" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        Confirm Dispense
                    </button>
                    <button onclick="closeDispenseModal()" class="px-4 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-lg font-medium transition-colors duration-200">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer from Inventory Modal -->
<div id="transferModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] backdrop-blur-sm overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-6xl w-full max-h-[90vh] flex flex-col modal-content">
            <div class="p-6 flex-1 overflow-y-auto modal-body">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-arrow-right text-blue-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Transfer from Inventory to Dispensary</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Move stock from main inventory to sales floor</p>
                        </div>
                    </div>
                    <button onclick="closeTransferModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 min-h-0">
                    <!-- Left Panel - Select Medicine -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Select Medicine</h4>
                        
                        <!-- Search Bar -->
                        <div class="relative">
                            <input type="text" id="transferSearch" placeholder="Search medicines..." 
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                        </div>

                        <!-- Medicine List -->
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 max-h-80 overflow-y-auto">
                            <div id="medicineList" class="space-y-3">
                                <!-- Medicine items will be loaded here -->
                            </div>
                        </div>
                    </div>

                    <!-- Right Panel - Transfer Details -->
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Transfer Details</h4>
                        
                        <div id="transferDetails" class="text-center py-8">
                            <div class="w-16 h-16 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-box text-gray-400 text-2xl"></i>
                            </div>
                            <p class="text-gray-500 dark:text-gray-400">Select a medicine to transfer</p>
                        </div>

                        <div id="selectedMedicineDetails" class="hidden space-y-4">
                            <!-- Selected Medicine Overview -->
                            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                <div class="flex items-center mb-3">
                                    <i class="fas fa-box text-blue-500 text-lg mr-3"></i>
                                    <span id="selectedMedicineName" class="font-semibold text-blue-900 dark:text-blue-100"></span>
                                </div>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Available Stock:</span>
                                        <span id="availableStock" class="font-medium text-gray-900 dark:text-white"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Batch Number:</span>
                                        <span id="batchNumber" class="font-medium text-gray-900 dark:text-white"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Expiry Date:</span>
                                        <span id="expiryDate" class="font-medium text-gray-900 dark:text-white"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Category:</span>
                                        <span id="medicineCategory" class="font-medium text-gray-900 dark:text-white"></span>
                                    </div>
                                </div>
                            </div>

                            <!-- Transfer Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Transfer Quantity (Optional)</label>
                                <div class="flex items-center space-x-2">
                                    <button type="button" id="decreaseQuantity" class="w-10 h-10 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-500">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" id="transferQuantity" placeholder="Enter specific quantity (leave blank to tr" 
                                           class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    <button type="button" id="increaseQuantity" class="w-10 h-10 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg flex items-center justify-center hover:bg-gray-300 dark:hover:bg-gray-500">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                    Maximum available: <span id="maxAvailable"></span>
                                </p>
                                <div class="flex items-center mt-2 text-sm text-blue-600 dark:text-blue-400">
                                    <i class="fas fa-lightbulb mr-2"></i>
                                    Leave blank to transfer all available stock
                                </div>
                            </div>

                            <!-- Notes -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Notes (Optional)</label>
                                <textarea id="transferNotes" rows="3" placeholder="Add any notes about this transfer..." 
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
                            </div>

                            <!-- Transfer Summary -->
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h5 class="font-semibold text-gray-900 dark:text-white mb-3">Transfer Summary</h5>
                                <div class="space-y-2 text-sm">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Item:</span>
                                        <span id="summaryItem" class="font-medium text-gray-900 dark:text-white"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Quantity:</span>
                                        <span id="summaryQuantity" class="font-medium text-gray-900 dark:text-white"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Batch:</span>
                                        <span id="summaryBatch" class="font-medium text-gray-900 dark:text-white"></span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600 dark:text-gray-400">Remaining Stock:</span>
                                        <span id="summaryRemaining" class="font-medium text-gray-900 dark:text-white"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            
            <!-- Modal Footer - Fixed at bottom -->
            <div class="flex items-center justify-between p-6 pt-4 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-b-xl modal-footer">
                <div class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                    <i class="fas fa-clock mr-2"></i>
                    Transfer will be processed immediately
                </div>
                <div class="flex space-x-3">
                    <button onclick="closeTransferModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                        Cancel
                    </button>
                    <button id="confirmTransfer" onclick="confirmTransfer()" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-arrow-right mr-2"></i>
                        Transfer Stock
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Transfer History Modal -->
<div id="transferHistoryModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-[9999] backdrop-blur-sm overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen p-4 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-7xl w-full max-h-[90vh] flex flex-col modal-content">
            <div class="p-6 flex-1 overflow-y-auto modal-body">
                <!-- Modal Header -->
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-file-alt text-blue-500 text-xl mr-3"></i>
                        <div>
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">Transfer History & Analytics</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">View and manage medicine transfers from inventory to dispensary</p>
                        </div>
                    </div>
                    <button onclick="closeTransferHistoryModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <!-- Analytics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                    <!-- Transfers Card -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 border border-blue-200 dark:border-blue-800">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-box text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-blue-600 dark:text-blue-400 font-medium">Transfers</p>
                                <p class="text-2xl font-bold text-blue-900 dark:text-blue-100" id="totalTransfers">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Completed Card -->
                    <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 border border-green-200 dark:border-green-800">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-check-circle text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-green-600 dark:text-green-400 font-medium">Completed (Active in Dispensary)</p>
                                <p class="text-2xl font-bold text-green-900 dark:text-green-100" id="completedTransfers">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Pending Card -->
                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 border border-yellow-200 dark:border-yellow-800">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-yellow-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-clock text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-yellow-600 dark:text-yellow-400 font-medium">Pending</p>
                                <p class="text-2xl font-bold text-yellow-900 dark:text-yellow-100" id="pendingTransfers">0</p>
                            </div>
                        </div>
                    </div>

                    <!-- Total Value Card -->
                    <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 border border-purple-200 dark:border-purple-800">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-chart-line text-white"></i>
                            </div>
                            <div>
                                <p class="text-sm text-purple-600 dark:text-purple-400 font-medium">Total Value</p>
                                <p class="text-2xl font-bold text-purple-900 dark:text-purple-100" id="totalValue">0.00 Birr</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter and Search Section -->
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Search -->
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                            <div class="relative">
                                <input type="text" id="historySearch" placeholder="Search transfers..." 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-600 dark:text-white">
                                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Status Filter -->
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select id="statusFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-600 dark:text-white">
                                <option value="">All Status</option>
                                <option value="completed">Completed</option>
                                <option value="pending">Pending</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>

                        <!-- Date Filter -->
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                            <div class="relative">
                                <input type="date" id="dateFilter" 
                                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-600 dark:text-white">
                                <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Refresh Button -->
                        <div class="flex flex-col">
                            <label class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">&nbsp;</label>
                            <button id="refreshHistory" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200 flex items-center justify-center">
                                <i class="fas fa-sync-alt mr-2"></i>Refresh
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Transfer History Table -->
                <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Medicine</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Quantity</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Transferred By</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Date & Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="transferHistoryTable" class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                <!-- Transfer rows will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-4 flex items-center justify-between">
                    <div class="text-sm text-gray-700 dark:text-gray-300">
                        <span id="transferCount">0 transfers shown</span>
                    </div>
                    <div class="flex items-center space-x-2" id="transferPagination">
                        <!-- Pagination will be loaded here -->
                    </div>
                </div>
            </div>
            
            <!-- Modal Footer -->
            <div class="flex items-center justify-end p-6 pt-4 border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 rounded-b-xl modal-footer">
                <button onclick="closeTransferHistoryModal()" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Import/Export Medicines Modal -->
<div id="importExportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import/Export Dispensary Data</h2>
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
                            <input type="text" name="filename" value="dispensary_export" class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">File will be saved with timestamp</p>
                        </div>

                        <!-- Export Filters -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Export Filters (Optional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Medicine Category</label>
                                    <select name="filters[category_id]" id="exportCategoryFilter" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Categories</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Dispensary Status</label>
                                    <select name="filters[dispensary_status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Status</option>
                                        <option value="available">Available for Dispensing</option>
                                        <option value="low_stock">Low Stock</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                        <option value="expired">Expired</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Transfer Status</label>
                                    <select name="filters[transfer_status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Transfers</option>
                                        <option value="completed">Completed Transfers</option>
                                        <option value="pending">Pending Transfers</option>
                                        <option value="cancelled">Cancelled Transfers</option>
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
                                    <div>Total Medicines: <span id="totalMedicines">0</span></div>
                                    <div>Available for Dispensing: <span id="availableMedicines">0</span></div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <div>Total Transfers: <span id="totalTransfers">0</span></div>
                                    <div>Low Stock Items: <span id="lowStockItems">0</span></div>
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
                    <!-- Print Dispensary Report -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-2">Print Dispensary Report</h3>
                        <p class="text-gray-700 dark:text-gray-300">This will generate a professional dispensary report with medicine availability, transfer history, and dispensing statistics.</p>
                    </div>

                        <!-- Print Filters -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Print Filters (Optional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Medicine Category</label>
                                    <select name="filters[category_id]" id="printCategoryFilter" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Categories</option>
                                    </select>
                            </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Dispensary Status</label>
                                    <select name="filters[dispensary_status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Status</option>
                                        <option value="available">Available for Dispensing</option>
                                        <option value="low_stock">Low Stock</option>
                                        <option value="out_of_stock">Out of Stock</option>
                                        <option value="expired">Expired</option>
                                    </select>
                            </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Transfer Status</label>
                                    <select name="filters[transfer_status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Transfers</option>
                                        <option value="completed">Completed Transfers</option>
                                        <option value="pending">Pending Transfers</option>
                                        <option value="cancelled">Cancelled Transfers</option>
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

<!-- Analytics Modal -->
<div id="analyticsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-7xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-chart-line text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Dispensary Analytics</h2>
                    <p class="text-gray-600 dark:text-gray-400">Comprehensive insights into dispensary operations and performance</p>
                </div>
            </div>
            <button onclick="hideAnalyticsModal()" class="w-10 h-10 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Analytics Content -->
        <div class="flex-1 overflow-y-auto p-6">
            <!-- Key Metrics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Medicines Card -->
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Medicines</p>
                            <p class="text-3xl font-bold" id="analyticsTotalMedicines">0</p>
                            <p class="text-blue-200 text-sm" id="analyticsMedicinesChange">+0% from last month</p>
                        </div>
                        <div class="w-12 h-12 bg-blue-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-pills text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Available for Dispensing Card -->
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Available for Dispensing</p>
                            <p class="text-3xl font-bold" id="analyticsAvailableMedicines">0</p>
                            <p class="text-green-200 text-sm" id="analyticsAvailableChange">+0% from last month</p>
                        </div>
                        <div class="w-12 h-12 bg-green-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-hand-holding-medical text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Transfers Card -->
                <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Total Transfers</p>
                            <p class="text-3xl font-bold" id="analyticsTotalTransfers">0</p>
                            <p class="text-purple-200 text-sm" id="analyticsTransfersChange">+0% from last month</p>
                        </div>
                        <div class="w-12 h-12 bg-purple-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exchange-alt text-white text-xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Low Stock Alert Card -->
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl p-6 text-white">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Low Stock Items</p>
                            <p class="text-3xl font-bold" id="analyticsLowStockItems">0</p>
                            <p class="text-orange-200 text-sm" id="analyticsLowStockChange">+0% from last month</p>
                        </div>
                        <div class="w-12 h-12 bg-orange-400 rounded-lg flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white text-xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Medicine Distribution Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Medicine Distribution by Category</h3>
                        <div class="flex items-center space-x-2">
                            <button id="categoryChartBtn" class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg text-sm font-medium">Categories</button>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="categoryChart"></canvas>
                    </div>
                </div>

                <!-- Stock Status Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Stock Status Overview</h3>
                        <div class="flex items-center space-x-2">
                            <button id="stockChartBtn" class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-600 dark:text-green-400 rounded-lg text-sm font-medium">Stock</button>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="stockChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Transfer Analytics -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Transfer Trends Chart -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Transfer Trends (Last 30 Days)</h3>
                        <div class="flex items-center space-x-2">
                            <button id="transferTrendsBtn" class="px-3 py-1 bg-purple-100 dark:bg-purple-900 text-purple-600 dark:text-purple-400 rounded-lg text-sm font-medium">Trends</button>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="transferTrendsChart"></canvas>
                    </div>
                </div>

                <!-- Transfer Status Distribution -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Transfer Status Distribution</h3>
                        <div class="flex items-center space-x-2">
                            <button id="transferStatusBtn" class="px-3 py-1 bg-indigo-100 dark:bg-indigo-900 text-indigo-600 dark:text-indigo-400 rounded-lg text-sm font-medium">Status</button>
                        </div>
                    </div>
                    <div class="h-80">
                        <canvas id="transferStatusChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Detailed Analytics Tables -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Top Categories Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Top Medicine Categories</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 dark:text-gray-400">Category</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 dark:text-gray-400">Count</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 dark:text-gray-400">Percentage</th>
                                </tr>
                            </thead>
                            <tbody id="topCategoriesTable">
                                <!-- Data will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Transfers Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-6">Recent Transfers</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200 dark:border-gray-700">
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 dark:text-gray-400">Medicine</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 dark:text-gray-400">Quantity</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 dark:text-gray-400">Status</th>
                                    <th class="text-left py-3 px-4 text-sm font-medium text-gray-600 dark:text-gray-400">Date</th>
                                </tr>
                            </thead>
                            <tbody id="recentTransfersTable">
                                <!-- Data will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center space-x-4">
                <button onclick="refreshAnalytics()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh Data
                </button>
                <button onclick="exportAnalytics()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-download mr-2"></i>Export Report
                </button>
            </div>
            <button onclick="hideAnalyticsModal()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<script>
// Action button functionality (matching inventory page)
document.addEventListener('DOMContentLoaded', function() {
    const cardsBtn = document.getElementById('cardsBtn');
    const tableBtn = document.getElementById('tableBtn');
    
    if (cardsBtn && tableBtn) {
        // Cards button
        cardsBtn.addEventListener('click', function() {
            console.log('Cards view clicked');
            
            // Update Cards button (active)
            this.classList.add('bg-orange-500', 'text-white', 'font-semibold');
            this.classList.remove('bg-transparent', 'text-gray-700', 'dark:text-gray-200', 'font-medium');
            
            // Update Table button (inactive)
            tableBtn.classList.remove('bg-orange-500', 'text-white', 'font-semibold');
            tableBtn.classList.add('bg-transparent', 'text-gray-700', 'dark:text-gray-200', 'font-medium');
            
            // Show cards view, hide table view
            const cardView = document.getElementById('cardView');
            const tableView = document.getElementById('tableView');
            if (cardView) {
                cardView.classList.remove('hidden');
                console.log('Cards view shown');
            }
            if (tableView) {
                tableView.classList.add('hidden');
                console.log('Table view hidden');
            }
        });
        
        // Table button
        tableBtn.addEventListener('click', function() {
            console.log('Table view clicked');
            
            // Update Table button (active)
            this.classList.add('bg-orange-500', 'text-white', 'font-semibold');
            this.classList.remove('bg-transparent', 'text-gray-700', 'dark:text-gray-200', 'font-medium');
            
            // Update Cards button (inactive)
            cardsBtn.classList.remove('bg-orange-500', 'text-white', 'font-semibold');
            cardsBtn.classList.add('bg-transparent', 'text-gray-700', 'dark:text-gray-200', 'font-medium');
            
            // Show table view, hide cards view
            const cardView = document.getElementById('cardView');
            const tableView = document.getElementById('tableView');
            if (cardView) {
                cardView.classList.add('hidden');
                console.log('Cards view hidden');
            }
            if (tableView) {
                tableView.classList.remove('hidden');
                console.log('Table view shown');
            }
        });
    }
    
    // Show Analytics button
    const analyticsBtn = document.querySelector('button:has(.fa-chart-bar)');
    if (analyticsBtn) {
        analyticsBtn.addEventListener('click', function() {
            console.log('Show Analytics clicked');
            showAnalyticsModal();
        });
    }
    
    // Pagination variables
    let currentPage = 1;
    let totalPages = 1;
    let pagination = null;
    
    // Initialize pagination
    initializeServerSidePagination();
    
    // Refresh button (medicines grid section only)
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            console.log('Refresh clicked');
            refreshMedicinesList();
        });
    }
    
    /**
     * Refresh medicines list (server-side approach)
     */
    function refreshMedicinesList() {
        // Show loading state
        const refreshBtn = document.getElementById('refreshBtn');
        if (refreshBtn) {
            const originalText = refreshBtn.innerHTML;
            refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-sm"></i>Refreshing...';
            refreshBtn.disabled = true;
        }
        
        // Reload the page to get fresh server-side data
        // This is consistent with the server-side rendering approach used throughout the system
        setTimeout(() => {
            location.reload();
        }, 500);
    }
    
    // Import/Export button
    const importExportBtn = document.getElementById('importExportBtn');
    if (importExportBtn) {
        importExportBtn.addEventListener('click', function() {
            console.log('Import/Export clicked');
            showImportExportModal();
        });
    }
    
    // Action buttons functionality
    // Print button
    const printBtn = document.getElementById('printBtn');
    if (printBtn) {
        printBtn.addEventListener('click', function() {
            window.print();
        });
    }
    
    // Transfer from Inventory button
    const transferFromInventoryBtn = document.getElementById('transferFromInventoryBtn');
    if (transferFromInventoryBtn) {
        transferFromInventoryBtn.addEventListener('click', function() {
            openTransferModal();
        });
    }
    
    // Transfer History button
    const transferHistoryBtn = document.getElementById('transferHistoryBtn');
    if (transferHistoryBtn) {
        transferHistoryBtn.addEventListener('click', function() {
            openTransferHistoryModal();
        });
    }
    
});

// Search functionality - moved to setupAllEventListeners()

function filterMedicines() {
    const searchElement = document.getElementById('medicineSearch');
    const categoryElement = document.getElementById('categoryFilter');
    const batchElement = document.getElementById('batchFilter');
    const dateElement = document.getElementById('dateFilter');
    
    const search = searchElement ? searchElement.value : '';
    const category = categoryElement ? categoryElement.value : '';
    const batch = batchElement ? batchElement.value : '';
    const date = dateElement ? dateElement.value : '';
    
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (category) params.append('category', category);
    if (batch) params.append('batch', batch);
    if (date) params.append('date', date);
    
    window.location.href = '{{ route("dispensary") }}?' + params.toString();
}

// Action button functionality - moved to main DOMContentLoaded

// Dispense medicine functionality
let currentMedicineId = null;

function dispenseMedicine(medicineId) {
    currentMedicineId = medicineId;
    
    // Fetch medicine details
    fetch(`/dispensary/medicine/${medicineId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
                return;
            }
            
            document.getElementById('medicineDetails').innerHTML = `
                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 dark:text-white">${data.name}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">${data.generic_name}</p>
                    <div class="mt-2 flex justify-between text-sm">
                        <span>Stock: <strong>${data.stock_quantity} ${data.unit}</strong></span>
                        <span>Price: <strong>Br ${parseFloat(data.selling_price).toFixed(2)}</strong></span>
                    </div>
                </div>
            `;
            
            document.getElementById('dispenseQuantity').max = data.stock_quantity;
            document.getElementById('dispenseQuantity').value = 1;
            document.getElementById('patientName').value = '';
            
            document.getElementById('dispenseModal').classList.remove('hidden');
            
            // Add blur effect to main content
            const mainContent = document.querySelector('.dispensary-page');
            if (mainContent) {
                mainContent.style.filter = 'blur(2px)';
                mainContent.style.pointerEvents = 'none';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading medicine details');
        });
}

function closeDispenseModal() {
    document.getElementById('dispenseModal').classList.add('hidden');
    currentMedicineId = null;
    
    // Remove blur effect from main content
    const mainContent = document.querySelector('.dispensary-page');
    if (mainContent) {
        mainContent.style.filter = 'none';
        mainContent.style.pointerEvents = 'auto';
    }
}

function confirmDispense() {
    const quantity = parseInt(document.getElementById('dispenseQuantity').value);
    const patientName = document.getElementById('patientName').value.trim();
    
    if (!patientName) {
        alert('Please enter patient name');
        return;
    }
    
    if (quantity <= 0) {
        alert('Please enter a valid quantity');
        return;
    }
    
    // Here you would typically send the dispense request to the server
    // For now, we'll just show a success message
    alert(`Successfully dispensed ${quantity} units to ${patientName}`);
    closeDispenseModal();
}

function viewMedicineDetails(medicineId) {
    // Check client-side cache first
    if (window.medicineCache && window.medicineCache[medicineId]) {
        console.log('Loading from cache:', medicineId);
        showViewMedicineModal(window.medicineCache[medicineId]);
        return;
    }
    
    // Try to find medicine in client-side array
    let medicine = null;
    if (typeof medicines !== 'undefined' && medicines) {
        medicine = medicines.find(m => m.id === medicineId);
    }
    
    if (medicine) {
        // Cache the medicine data
        if (!window.medicineCache) window.medicineCache = {};
        window.medicineCache[medicineId] = medicine;
        showViewMedicineModal(medicine);
        return;
    }
    
    // If not found, fetch from server and cache
    console.log('Fetching from server:', medicineId);
    fetch(`/dispensary/medicine/${medicineId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success || data.name) {
                // Cache the result
                if (!window.medicineCache) window.medicineCache = {};
                window.medicineCache[medicineId] = data;
                showViewMedicineModal(data);
            } else {
                showNotification('Medicine not found', 'error');
            }
        })
        .catch(error => {
            console.error('Error fetching medicine:', error);
            showNotification('Error loading medicine details', 'error');
        });
}

/**
 * Show view medicine modal
 */
function showViewMedicineModal(medicine) {
    console.log('Medicine data:', medicine); // Debug log
    const modal = document.getElementById('viewMedicineModal');
    const content = document.getElementById('viewMedicineContent');
    
    if (modal && content) {
        content.innerHTML = `
            <div class="space-y-6">
                <!-- Medicine Header -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4" style="background: linear-gradient(135deg, ${medicine.category?.color || '#3B82F6'}, ${medicine.category?.color || '#3B82F6'}CC);">
                        <i class="fas fa-pills text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">${medicine.name || 'Unknown Medicine'}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">${medicine.generic_name || 'No generic name'}</p>
                    <div class="flex justify-center gap-2 mb-6">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background-color: ${medicine.category?.color || '#3B82F6'}20; color: ${medicine.category?.color || '#3B82F6'}">
                            ${medicine.category?.name || 'No Category'}
                        </span>
                        <span class="px-3 py-1 ${medicine.is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'} rounded-full text-xs font-semibold">
                            ${medicine.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                </div>

                <!-- Medicine Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Basic Information</h4>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Strength & Form:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.strength || 'N/A'} ${medicine.form || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Barcode:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.barcode || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Batch Number:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.batch_number || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Manufacturer:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.manufacturer || 'N/A'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Expiry Date:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.expiry_date_formatted || medicine.expiry_date || 'N/A'}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Stock & Pricing -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Stock & Pricing</h4>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Dispensary Stock:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.transfers ? medicine.transfers.filter(t => t.transfer_type === 'inventory_to_dispensary' && t.status === 'completed').reduce((sum, t) => sum + t.quantity_remaining, 0) : 0} ${medicine.unit || 'units'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Remaining Stock:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.stock_quantity || 0} ${medicine.unit || 'units'}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Selling Price:</span>
                                <span class="text-lg font-bold text-gray-900 dark:text-white">Br ${parseFloat(medicine.selling_price || 0).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Cost Price:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Br ${parseFloat(medicine.cost_price || 0).toFixed(2)}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Prescription:</span>
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.prescription_required === 'yes' ? 'Required' : 'Not Required'}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                ${medicine.description ? `
                <div class="space-y-4">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Description</h4>
                    <p class="text-sm text-gray-700 dark:text-gray-300">${medicine.description}</p>
                </div>
                ` : ''}
            </div>
        `;
        
        modal.classList.remove('hidden');
    }
}

/**
 * Hide view medicine modal
 */
function hideViewMedicineModal() {
    const modal = document.getElementById('viewMedicineModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

/**
 * Show transfer details modal with professional design
 */
function showTransferDetailsModal(transfer) {
    console.log('Transfer data:', transfer); // Debug log
    const modal = document.getElementById('transferDetailsModal');
    const content = document.getElementById('transferDetailsContent');
    
    if (modal && content) {
        content.innerHTML = `
            <div class="space-y-6">
                <!-- Transfer Header -->
                <div class="text-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4">
                        <i class="fas fa-exchange-alt text-white text-2xl"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">${transfer.medicine?.name || 'Unknown Medicine'}</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">Transfer ID: #${transfer.id}</p>
                    <div class="flex justify-center gap-2 mb-6">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-semibold">
                            ${transfer.quantity_transferred} Units
                        </span>
                        <span class="px-3 py-1 ${getStatusColor(transfer.status)} rounded-full text-xs font-semibold">
                            ${getStatusText(transfer.status)}
                        </span>
                    </div>
                </div>

                <!-- Transfer Details -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Basic Information</h4>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Medicine:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">${transfer.medicine?.name || 'Unknown'}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Batch Number:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">${transfer.batch_number || 'N/A'}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Quantity:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">${transfer.quantity_transferred} units</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Status:</span>
                                <span class="text-sm font-bold ${getStatusColor(transfer.status)}">${getStatusText(transfer.status)}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Transfer Information -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Transfer Information</h4>
                        
                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Transferred By:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">${transfer.transferred_by?.name || 'System Administrator'}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Date:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">${formatDateOnly(transfer.created_at)}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Time:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">${formatTimeOnly(transfer.created_at)}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Full Date & Time:</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">${formatDateTime(transfer.created_at)}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Information -->
                <div class="space-y-4">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Additional Information</h4>
                    
                    <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4">
                        <div class="flex justify-between items-start">
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Notes:</span>
                            <span class="text-sm font-bold text-gray-900 dark:text-white text-right max-w-xs">${transfer.notes || 'No additional notes'}</span>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        modal.classList.remove('hidden');
    }
}

/**
 * Hide transfer details modal
 */
function hideTransferDetailsModal() {
    const modal = document.getElementById('transferDetailsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

/**
 * Get status color classes
 */
function getStatusColor(status) {
    switch(status) {
        case 'completed':
            return 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200';
        case 'pending':
            return 'bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200';
        case 'failed':
            return 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200';
        default:
            return 'bg-gray-100 dark:bg-gray-900 text-gray-800 dark:text-gray-200';
    }
}



// Medicine selection functionality - moved to main DOMContentLoaded


// Transfer History Modal Functions
var transferHistoryData = [];
var currentPage = 1;
var totalPages = 1;

// Ensure variables are properly initialized
if (typeof transferHistoryData === 'undefined') {
    transferHistoryData = [];
}
if (typeof currentPage === 'undefined') {
    currentPage = 1;
}
if (typeof totalPages === 'undefined') {
    totalPages = 1;
}

// Global initialization function for transfer history
function initializeTransferHistory() {
    console.log('Initializing transfer history variables...');
    if (!transferHistoryData || !Array.isArray(transferHistoryData)) {
        transferHistoryData = [];
        console.log('Initialized transferHistoryData as empty array');
    }
    if (currentPage === undefined || currentPage === null || currentPage < 1) {
        currentPage = 1;
        console.log('Initialized currentPage as 1');
    }
    if (totalPages === undefined || totalPages === null || totalPages < 1) {
        totalPages = 1;
        console.log('Initialized totalPages as 1');
    }
    console.log('Transfer history variables initialized:', { 
        transferHistoryData: transferHistoryData.length, 
        currentPage: currentPage, 
        totalPages: totalPages 
    });
}

function openTransferHistoryModal() {
    console.log('Opening transfer history modal...');
    
    // Ensure variables are initialized
    initializeTransferHistory();
    
    const modal = document.getElementById('transferHistoryModal');
    if (modal) {
        modal.classList.remove('hidden');
        loadTransferHistory();
    } else {
        console.error('Transfer history modal not found');
        alert('Transfer history modal not found. Please refresh the page.');
    }
}

function closeTransferHistoryModal() {
    const modal = document.getElementById('transferHistoryModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function loadTransferHistory() {
    console.log('Loading transfer history...');
    
    // Ensure variables are initialized
    initializeTransferHistory();
    
    // Show loading state
    const refreshBtn = document.getElementById('refreshHistory');
    if (refreshBtn) {
        const originalText = refreshBtn.innerHTML;
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
        refreshBtn.disabled = true;
    }
    
    // Get filter parameters
    const search = document.getElementById('historySearch')?.value || '';
    const status = document.getElementById('statusFilter')?.value || '';
    const date = document.getElementById('dateFilter')?.value || '';
    
    // Build query parameters
    const params = new URLSearchParams();
    if (search) params.append('search', search);
    if (status) params.append('status', status);
    if (date) params.append('date', date);
    if (currentPage > 1) params.append('page', currentPage);
    
    const url = '/transfers/history' + (params.toString() ? '?' + params.toString() : '');
    
    console.log('Fetching transfer history from:', url);
    console.log('Current page:', currentPage);
    
    fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received transfer history data:', data);
            if (data && data.transfers) {
                transferHistoryData = data.transfers;
                currentPage = data.pagination.current_page || 1;
                totalPages = data.pagination.last_page || 1;
                
                updateAnalytics(data.analytics || {});
                displayTransferHistory(transferHistoryData);
                updatePagination(data.pagination);
            } else {
                console.error('Invalid transfer history data received:', data);
                alert('Error: Invalid data received from server');
            }
        })
        .catch(error => {
            console.error('Error loading transfer history:', error);
            alert('Error loading transfer history: ' + error.message);
        })
        .finally(() => {
            if (refreshBtn) {
                refreshBtn.innerHTML = '<i class="fas fa-sync-alt mr-2"></i>Refresh';
                refreshBtn.disabled = false;
            }
        });
}

function updateAnalytics(analytics) {
    const totalTransfers = document.getElementById('totalTransfers');
    const completedTransfers = document.getElementById('completedTransfers');
    const pendingTransfers = document.getElementById('pendingTransfers');
    const totalValue = document.getElementById('totalValue');
    
    if (totalTransfers) totalTransfers.textContent = analytics.total_transfers || 0;
    if (completedTransfers) completedTransfers.textContent = analytics.completed_transfers || 0;
    if (pendingTransfers) pendingTransfers.textContent = analytics.pending_transfers || 0;
    if (totalValue) totalValue.textContent = (analytics.total_value || 0).toFixed(2) + ' Birr';
}

function displayTransferHistory(transfers) {
    const tableBody = document.getElementById('transferHistoryTable');
    if (!tableBody) {
        console.error('Transfer history table body not found');
        return;
    }
    
    tableBody.innerHTML = '';
    
    if (!transfers || transfers.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                    No transfer history found
                </td>
            </tr>
        `;
        return;
    }
    
    transfers.forEach(transfer => {
        const row = document.createElement('tr');
        row.className = 'hover:bg-gray-50 dark:hover:bg-gray-700';
        
        const statusClass = getStatusClass(transfer.status);
        const statusText = getStatusText(transfer.status);
        const date = formatShortDate(transfer.created_at);
        const time = formatTimeOnly(transfer.created_at);
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div>
                    <div class="text-sm font-medium text-gray-900 dark:text-white">${transfer.medicine?.name || 'Unknown Medicine'}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Batch: ${transfer.batch_number || 'N/A'}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${transfer.quantity_transferred || 0}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                ${transfer.transferred_by?.name || 'System Administrator'}
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                <div title="${formatDateTime(transfer.created_at)}">
                    <div class="font-medium">${date}</div>
                    <div class="text-gray-500 dark:text-gray-400">${time}</div>
                </div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                    ${statusText}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <div class="flex items-center space-x-2">
                    <button onclick="viewTransferDetails(${transfer.id})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button onclick="deleteTransfer(${transfer.id})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </td>
        `;
        
        tableBody.appendChild(row);
    });
    
    // Update transfer count
    const transferCount = document.getElementById('transferCount');
    if (transferCount) {
        transferCount.textContent = `${transfers.length} transfers shown`;
    }
}

function getStatusClass(status) {
    switch (status) {
        case 'completed':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
        case 'pending':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
        case 'cancelled':
            return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
    }
}

function getStatusText(status) {
    switch (status) {
        case 'completed':
            return 'Completed';
        case 'pending':
            return 'Pending';
        case 'cancelled':
            return 'Cancelled';
        default:
            return 'Unknown';
    }
}

function formatDateTime(dateString) {
    const date = new Date(dateString);
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    };
    return date.toLocaleString('en-US', options);
}

function formatDateOnly(dateString) {
    const date = new Date(dateString);
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    };
    return date.toLocaleDateString('en-US', options);
}

function formatTimeOnly(dateString) {
    const date = new Date(dateString);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: true
    });
}

function formatShortDate(dateString) {
    const date = new Date(dateString);
    const options = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    };
    return date.toLocaleDateString('en-US', options);
}

function updatePagination(pagination) {
    const paginationContainer = document.getElementById('transferPagination');
    if (!paginationContainer) return;
    
    if (pagination.last_page <= 1) {
        paginationContainer.innerHTML = '';
        return;
    }
    
    let paginationHTML = '';
    
    // Previous button
    if (pagination.current_page > 1) {
        paginationHTML += `
            <button onclick="loadTransferHistoryPage(${pagination.current_page - 1})" 
                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Previous
            </button>
        `;
    }
    
    // Page numbers
    for (let i = 1; i <= pagination.last_page; i++) {
        if (i === pagination.current_page) {
            paginationHTML += `
                <button class="px-3 py-2 text-sm font-medium text-white bg-blue-600 border border-blue-600 hover:bg-blue-700">
                    ${i}
                </button>
            `;
        } else {
            paginationHTML += `
                <button onclick="loadTransferHistoryPage(${i})" 
                        class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                    ${i}
                </button>
            `;
        }
    }
    
    // Next button
    if (pagination.current_page < pagination.last_page) {
        paginationHTML += `
            <button onclick="loadTransferHistoryPage(${pagination.current_page + 1})" 
                    class="px-3 py-2 text-sm font-medium text-gray-500 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 dark:bg-gray-800 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700">
                Next
            </button>
        `;
    }
    
    paginationContainer.innerHTML = paginationHTML;
}

function loadTransferHistoryPage(page) {
    console.log('Loading transfer history page:', page);
    
    // Ensure variables are initialized
    initializeTransferHistory();
    
    currentPage = page;
    loadTransferHistory();
}

function viewTransferDetails(transferId) {
    console.log('Viewing transfer details for ID:', transferId);
    
    // Ensure variables are initialized
    initializeTransferHistory();
    
    // Find the transfer in the data
    const transfer = transferHistoryData.find(t => t.id === transferId);
    if (transfer) {
        showTransferDetailsModal(transfer);
    } else {
        showNotification('Transfer details not found', 'error');
    }
}

function deleteTransfer(transferId) {
    if (confirm('Are you sure you want to delete this transfer? This action cannot be undone.')) {
        console.log('Deleting transfer ID:', transferId);
        // Here you would typically send a DELETE request to the server
        alert('Delete transfer functionality coming soon!');
    }
}

// Initialize date picker - moved to setupAllEventListeners()

// Auto-refresh status
setInterval(function() {
    const statusElement = document.getElementById('dispensaryStatus');
    if (statusElement) {
        statusElement.textContent = 'Ready for Dispensing';
    }
}, 30000);

// Helper function to safely add event listeners
function safeAddEventListener(elementId, event, handler) {
    const element = document.getElementById(elementId);
    if (element) {
        element.addEventListener(event, handler);
        console.log(`Event listener added to ${elementId} for ${event}`);
    } else {
        console.warn(`Element ${elementId} not found, skipping event listener for ${event}`);
    }
}

// Comprehensive event listener setup
function setupAllEventListeners() {
    console.log('Setting up all event listeners...');
    
    // Search functionality
    safeAddEventListener('medicineSearch', 'input', function() {
        filterMedicines();
    });
    
    safeAddEventListener('categoryFilter', 'change', function() {
        filterMedicines();
    });
    
    safeAddEventListener('batchFilter', 'change', function() {
        filterMedicines();
    });
    
    safeAddEventListener('dateFilter', 'change', function() {
        filterMedicines();
    });
    
    safeAddEventListener('clearFilters', 'click', function() {
        const medicineSearch = document.getElementById('medicineSearch');
        const categoryFilter = document.getElementById('categoryFilter');
        const batchFilter = document.getElementById('batchFilter');
        const dateFilter = document.getElementById('dateFilter');
        
        if (medicineSearch) medicineSearch.value = '';
        if (categoryFilter) categoryFilter.value = '';
        if (batchFilter) batchFilter.value = '';
        if (dateFilter) dateFilter.value = '';
        filterMedicines();
    });
    
    // Date picker
    safeAddEventListener('dateFilter', 'focus', function() {
        this.type = 'date';
    });
    
    console.log('All event listeners setup complete');
}

// Import/Export Modal Functionality
(function() {
    'use strict';
    
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
        initializeActionButton();
        
        console.log('Import/Export Modal initialized successfully');
    }
    
    /**
     * Initialize file upload functionality
     */
    function initializeFileUpload() {
        const fileInput = document.getElementById('importFile');
        const selectFileBtn = document.getElementById('selectFileBtn');
        const fileDropZone = document.getElementById('fileDropZone');
        const selectedFileName = document.getElementById('selectedFileName');
        
        if (!fileInput || !selectFileBtn || !fileDropZone) return;
        
        // File selection button
        selectFileBtn.addEventListener('click', function() {
            fileInput.click();
        });
        
        // File input change
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleFileSelection(file);
            }
        });
        
        // Drag and drop functionality
        fileDropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            fileDropZone.classList.add('border-orange-500', 'bg-orange-50');
        });
        
        fileDropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            fileDropZone.classList.remove('border-orange-500', 'bg-orange-50');
        });
        
        fileDropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            fileDropZone.classList.remove('border-orange-500', 'bg-orange-50');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (validateFile(file)) {
                    fileInput.files = files;
                    handleFileSelection(file);
                }
            }
        });
    }
    
    /**
     * Handle file selection
     */
    function handleFileSelection(file) {
        const selectedFileName = document.getElementById('selectedFileName');
        const selectFileBtn = document.getElementById('selectFileBtn');
        
        if (validateFile(file)) {
            selectedFileName.textContent = `Selected: ${file.name} (${formatFileSize(file.size)})`;
            selectedFileName.classList.remove('hidden');
            selectFileBtn.textContent = 'Change File';
            selectFileBtn.classList.remove('bg-green-600', 'hover:bg-green-700');
            selectFileBtn.classList.add('bg-blue-600', 'hover:bg-blue-700');
        }
    }
    
    /**
     * Validate file
     */
    function validateFile(file) {
        const allowedTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // .xlsx
            'application/vnd.ms-excel', // .xls
            'text/csv' // .csv
        ];
        
        const maxSize = 10 * 1024 * 1024; // 10MB
        
        if (!allowedTypes.includes(file.type) && !file.name.match(/\.(xlsx|xls|csv)$/i)) {
            showNotification('Invalid file type. Please select Excel (.xlsx, .xls) or CSV (.csv) file.', 'error');
            return false;
        }
        
        if (file.size > maxSize) {
            showNotification('File size too large. Maximum size is 10MB.', 'error');
            return false;
        }
        
        return true;
    }
    
    /**
     * Format file size
     */
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
    
    /**
     * Initialize export functionality
     */
    function initializeExportFunctionality() {
        // Load categories for export filter
        loadExportCategories();
        
        // Update export summary when filters change
        const exportForm = document.getElementById('exportForm');
        if (exportForm) {
            exportForm.addEventListener('change', updateExportSummary);
        }
        
        // Initial summary update
        updateExportSummary();
    }
    
    /**
     * Load categories for export filter
     */
    function loadExportCategories() {
        fetch('/dispensary/categories')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const categoryFilter = document.getElementById('exportCategoryFilter');
                    const printCategoryFilter = document.getElementById('printCategoryFilter');
                    
                    if (categoryFilter) {
                        categoryFilter.innerHTML = '<option value="">All Categories</option>';
                        data.data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            categoryFilter.appendChild(option);
                        });
                    }
                    
                    if (printCategoryFilter) {
                        printCategoryFilter.innerHTML = '<option value="">All Categories</option>';
                        data.data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            printCategoryFilter.appendChild(option);
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error loading categories:', error);
            });
    }
    
    /**
     * Update export summary
     */
    function updateExportSummary() {
        const form = document.getElementById('exportForm');
        if (!form) return;
        
        const formData = new FormData(form);
        const filters = {};
        
        // Collect filter data
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('filters[') && value) {
                const filterKey = key.replace('filters[', '').replace(']', '');
                filters[filterKey] = value;
            }
        }
        
        // Get export stats
        const params = new URLSearchParams();
        Object.keys(filters).forEach(key => {
            params.append(`filters[${key}]`, filters[key]);
        });
        fetch('/dispensary/export-stats?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const totalMedicinesElement = document.getElementById('totalMedicines');
                    const availableMedicinesElement = document.getElementById('availableMedicines');
                    const totalTransfersElement = document.getElementById('totalTransfers');
                    const lowStockItemsElement = document.getElementById('lowStockItems');
                    
                    if (totalMedicinesElement) totalMedicinesElement.textContent = data.data.total_medicines;
                    if (availableMedicinesElement) availableMedicinesElement.textContent = data.data.available_medicines;
                    if (totalTransfersElement) totalTransfersElement.textContent = data.data.total_transfers;
                    if (lowStockItemsElement) lowStockItemsElement.textContent = data.data.low_stock_items;
                }
            })
            .catch(error => {
                console.error('Error loading export stats:', error);
            });
    }
    
    /**
     * Initialize template download
     */
    function initializeTemplateDownload() {
        const downloadTemplateBtn = document.getElementById('downloadTemplateBtn');
        if (downloadTemplateBtn) {
            downloadTemplateBtn.addEventListener('click', function() {
                window.location.href = '/dispensary/template';
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
            showNotification('Please select a file to import.', 'error');
            return;
        }
        
        isImporting = true;
        const actionButton = document.getElementById('actionButton');
        const originalText = actionButton.innerHTML;
        actionButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Importing...';
        actionButton.disabled = true;
        
        const formData = new FormData(form);
        
        fetch('/dispensary/import', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`Import successful! ${data.data.imported_count} records imported, ${data.data.skipped_count} skipped, ${data.data.error_count} errors.`, 'success');
                
                // Reset form
                form.reset();
                document.getElementById('selectedFileName').classList.add('hidden');
                document.getElementById('selectFileBtn').textContent = 'Select File';
                document.getElementById('selectFileBtn').classList.remove('bg-blue-600', 'hover:bg-blue-700');
                document.getElementById('selectFileBtn').classList.add('bg-green-600', 'hover:bg-green-700');
                
                // Refresh the page to show updated data
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                showNotification(data.message || 'Import failed.', 'error');
            }
        })
        .catch(error => {
            console.error('Import error:', error);
            showNotification('Import failed. Please try again.', 'error');
        })
        .finally(() => {
            isImporting = false;
            actionButton.innerHTML = originalText;
            actionButton.disabled = false;
        });
    }
    
    /**
     * Handle export
     */
    function handleExport() {
        if (isExporting) return;
        
        const form = document.getElementById('exportForm');
        if (!form) return;
        
        isExporting = true;
        const actionButton = document.getElementById('actionButton');
        const originalText = actionButton.innerHTML;
        actionButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Exporting...';
        actionButton.disabled = true;
        
        const formData = new FormData(form);
        const params = new URLSearchParams();
        
        // Add form data to params
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('filters[')) {
                const filterKey = key.replace('filters[', '').replace(']', '');
                if (value) {
                    params.append(`filters[${filterKey}]`, value);
                }
            } else {
                params.append(key, value);
            }
        }
        
        // Create download link
        const downloadUrl = `/dispensary/export?${params.toString()}`;
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = '';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showNotification('Export started. File will download shortly.', 'success');
        
        setTimeout(() => {
            isExporting = false;
            actionButton.innerHTML = originalText;
            actionButton.disabled = false;
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
        const printUrl = `/dispensary/print?${params.toString()}`;
        const printWindow = window.open(printUrl, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
        
        if (printWindow) {
            printWindow.focus();
            showNotification('Print report opened in new window.', 'success');
        } else {
            showNotification('Unable to open print window. Please check your popup blocker.', 'error');
        }
    }
    
    /**
     * Show notification
     */
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-[10000] p-4 rounded-lg shadow-lg max-w-sm ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${
                    type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-circle' :
                    type === 'warning' ? 'fa-exclamation-triangle' :
                    'fa-info-circle'
                } mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remove notification after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
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
    
    // Make functions globally available
    window.initializeImportExportModal = initializeImportExportModal;
    window.showImportExportModal = function() {
        const modal = document.getElementById('importExportModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    };
    window.hideImportExportModal = function() {
        const modal = document.getElementById('importExportModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    };
    
})();

// Analytics Modal Functionality
(function() {
    'use strict';
    
    let analyticsData = {};
    let charts = {};
    
    /**
     * Show Analytics Modal
     */
    function showAnalyticsModal() {
        const modal = document.getElementById('analyticsModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            loadAnalyticsData();
        }
    }
    
    /**
     * Hide Analytics Modal
     */
    function hideAnalyticsModal() {
        const modal = document.getElementById('analyticsModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    
    /**
     * Load Analytics Data
     */
    function loadAnalyticsData() {
        console.log('Loading analytics data...');
        
        // Show loading state
        showLoadingState();
        
        // Fetch analytics data
        fetch('/dispensary/analytics')
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Analytics data received:', data);
                if (data.success) {
                    analyticsData = data.data || {};
                    updateAnalyticsMetrics();
                    createCharts();
                    updateTables();
                    showNotification('Analytics data loaded successfully', 'success');
                } else {
                    console.error('Error loading analytics data:', data.message);
                    showNotification(data.message || 'Error loading analytics data', 'error');
                }
            })
            .catch(error => {
                console.error('Error loading analytics:', error);
                showNotification('Error loading analytics data: ' + error.message, 'error');
            })
            .finally(() => {
                hideLoadingState();
            });
    }
    
    /**
     * Show Loading State
     */
    function showLoadingState() {
        const analyticsElements = [
            'analyticsTotalMedicines',
            'analyticsAvailableMedicines', 
            'analyticsTotalTransfers',
            'analyticsLowStockItems',
            'analyticsMedicinesChange',
            'analyticsAvailableChange',
            'analyticsTransfersChange',
            'analyticsLowStockChange'
        ];
        
        analyticsElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.textContent = 'Loading...';
            }
        });
    }
    
    /**
     * Hide Loading State
     */
    function hideLoadingState() {
        // Loading state is handled by updateAnalyticsMetrics
    }
    
    /**
     * Update Analytics Metrics
     */
    function updateAnalyticsMetrics() {
        // Update key metrics cards with null checks
        const totalMedicinesEl = document.getElementById('analyticsTotalMedicines');
        if (totalMedicinesEl) {
            totalMedicinesEl.textContent = analyticsData.total_medicines || 0;
        }
        
        const availableMedicinesEl = document.getElementById('analyticsAvailableMedicines');
        if (availableMedicinesEl) {
            availableMedicinesEl.textContent = analyticsData.available_medicines || 0;
        }
        
        const totalTransfersEl = document.getElementById('analyticsTotalTransfers');
        if (totalTransfersEl) {
            totalTransfersEl.textContent = analyticsData.total_transfers || 0;
        }
        
        const lowStockItemsEl = document.getElementById('analyticsLowStockItems');
        if (lowStockItemsEl) {
            lowStockItemsEl.textContent = analyticsData.low_stock_items || 0;
        }
        
        // Update change percentages with null checks
        const medicinesChangeEl = document.getElementById('analyticsMedicinesChange');
        if (medicinesChangeEl) {
            medicinesChangeEl.textContent = `${analyticsData.medicines_change || 0}% from last month`;
        }
        
        const availableChangeEl = document.getElementById('analyticsAvailableChange');
        if (availableChangeEl) {
            availableChangeEl.textContent = `${analyticsData.available_change || 0}% from last month`;
        }
        
        const transfersChangeEl = document.getElementById('analyticsTransfersChange');
        if (transfersChangeEl) {
            transfersChangeEl.textContent = `${analyticsData.transfers_change || 0}% from last month`;
        }
        
        const lowStockChangeEl = document.getElementById('analyticsLowStockChange');
        if (lowStockChangeEl) {
            lowStockChangeEl.textContent = `${analyticsData.low_stock_change || 0}% from last month`;
        }
    }
    
    /**
     * Create Charts
     */
    function createCharts() {
        // Medicine Distribution Chart
        createCategoryChart();
        
        // Stock Status Chart
        createStockChart();
        
        // Transfer Trends Chart
        createTransferTrendsChart();
        
        // Transfer Status Chart
        createTransferStatusChart();
    }
    
    /**
     * Create Category Chart
     */
    function createCategoryChart() {
        const ctx = document.getElementById('categoryChart');
        if (!ctx) {
            console.warn('Category chart canvas not found');
            return;
        }
        
        if (charts.categoryChart) {
            charts.categoryChart.destroy();
        }
        
        const data = analyticsData.category_distribution || [];
        if (data.length === 0) {
            console.warn('No category distribution data available');
            return;
        }
        
        try {
            charts.categoryChart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: data.map(item => item.name),
                    datasets: [{
                        data: data.map(item => item.count),
                        backgroundColor: [
                            '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
                            '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6366F1'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating category chart:', error);
        }
    }
    
    /**
     * Create Stock Chart
     */
    function createStockChart() {
        const ctx = document.getElementById('stockChart');
        if (!ctx) {
            console.warn('Stock chart canvas not found');
            return;
        }
        
        if (charts.stockChart) {
            charts.stockChart.destroy();
        }
        
        const data = analyticsData.stock_status || [];
        if (data.length === 0) {
            console.warn('No stock status data available');
            return;
        }
        
        try {
            charts.stockChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: data.map(item => item.status),
                    datasets: [{
                        label: 'Count',
                        data: data.map(item => item.count),
                        backgroundColor: [
                            '#10B981', '#F59E0B', '#EF4444', '#6B7280'
                        ],
                        borderRadius: 8,
                        borderSkipped: false
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
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating stock chart:', error);
        }
    }
    
    /**
     * Create Transfer Trends Chart
     */
    function createTransferTrendsChart() {
        const ctx = document.getElementById('transferTrendsChart');
        if (!ctx) {
            console.warn('Transfer trends chart canvas not found');
            return;
        }
        
        if (charts.transferTrendsChart) {
            charts.transferTrendsChart.destroy();
        }
        
        const data = analyticsData.transfer_trends || [];
        if (data.length === 0) {
            console.warn('No transfer trends data available');
            return;
        }
        
        try {
            charts.transferTrendsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map(item => item.date),
                    datasets: [{
                        label: 'Transfers',
                        data: data.map(item => item.count),
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139, 92, 246, 0.1)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#8B5CF6',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
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
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating transfer trends chart:', error);
        }
    }
    
    /**
     * Create Transfer Status Chart
     */
    function createTransferStatusChart() {
        const ctx = document.getElementById('transferStatusChart');
        if (!ctx) {
            console.warn('Transfer status chart canvas not found');
            return;
        }
        
        if (charts.transferStatusChart) {
            charts.transferStatusChart.destroy();
        }
        
        const data = analyticsData.transfer_status || [];
        if (data.length === 0) {
            console.warn('No transfer status data available');
            return;
        }
        
        try {
            charts.transferStatusChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.map(item => item.status),
                    datasets: [{
                        data: data.map(item => item.count),
                        backgroundColor: [
                            '#10B981', '#F59E0B', '#EF4444'
                        ],
                        borderWidth: 2,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        } catch (error) {
            console.error('Error creating transfer status chart:', error);
        }
    }
    
    /**
     * Update Tables
     */
    function updateTables() {
        updateTopCategoriesTable();
        updateRecentTransfersTable();
    }
    
    /**
     * Update Top Categories Table
     */
    function updateTopCategoriesTable() {
        const tbody = document.getElementById('topCategoriesTable');
        if (!tbody) {
            console.warn('Top categories table not found');
            return;
        }
        
        const data = analyticsData.top_categories || [];
        if (data.length === 0) {
            console.warn('No top categories data available');
            tbody.innerHTML = '<tr><td colspan="3" class="py-3 px-4 text-sm text-gray-500 dark:text-gray-400 text-center">No data available</td></tr>';
            return;
        }
        
        tbody.innerHTML = '';
        
        data.forEach(category => {
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-200 dark:border-gray-700';
            row.innerHTML = `
                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">${category.name || 'Unknown'}</td>
                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">${category.count || 0}</td>
                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">${category.percentage || 0}%</td>
            `;
            tbody.appendChild(row);
        });
    }
    
    /**
     * Update Recent Transfers Table
     */
    function updateRecentTransfersTable() {
        const tbody = document.getElementById('recentTransfersTable');
        if (!tbody) {
            console.warn('Recent transfers table not found');
            return;
        }
        
        const data = analyticsData.recent_transfers || [];
        if (data.length === 0) {
            console.warn('No recent transfers data available');
            tbody.innerHTML = '<tr><td colspan="4" class="py-3 px-4 text-sm text-gray-500 dark:text-gray-400 text-center">No data available</td></tr>';
            return;
        }
        
        tbody.innerHTML = '';
        
        data.forEach(transfer => {
            const row = document.createElement('tr');
            row.className = 'border-b border-gray-200 dark:border-gray-700';
            const statusClass = getTransferStatusClass(transfer.status);
            row.innerHTML = `
                <td class="py-3 px-4 text-sm text-gray-900 dark:text-white">${transfer.medicine_name || 'Unknown'}</td>
                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">${transfer.quantity || 0}</td>
                <td class="py-3 px-4 text-sm">
                    <span class="px-2 py-1 text-xs font-semibold rounded-full ${statusClass}">
                        ${transfer.status || 'Unknown'}
                    </span>
                </td>
                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400">${transfer.date || 'Unknown'}</td>
            `;
            tbody.appendChild(row);
        });
    }
    
    /**
     * Get Transfer Status Class
     */
    function getTransferStatusClass(status) {
        switch (status.toLowerCase()) {
            case 'completed':
                return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            case 'pending':
                return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
            case 'cancelled':
                return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
            default:
                return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
        }
    }
    
    /**
     * Refresh Analytics
     */
    function refreshAnalytics() {
        console.log('Refreshing analytics...');
        loadAnalyticsData();
    }
    
    /**
     * Export Analytics
     */
    function exportAnalytics() {
        console.log('Exporting analytics...');
        window.location.href = '/dispensary/analytics/export';
    }
    
    /**
     * Show Notification
     */
    function showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-[10000] p-4 rounded-lg shadow-lg max-w-sm ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            type === 'warning' ? 'bg-yellow-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${
                    type === 'success' ? 'fa-check-circle' :
                    type === 'error' ? 'fa-exclamation-circle' :
                    type === 'warning' ? 'fa-exclamation-triangle' :
                    'fa-info-circle'
                } mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
        }, 5000);
    }
    
    // Make functions globally available
    window.showAnalyticsModal = showAnalyticsModal;
    window.hideAnalyticsModal = hideAnalyticsModal;
    window.refreshAnalytics = refreshAnalytics;
    window.exportAnalytics = exportAnalytics;
    
})();

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
    if (page >= 1 && page <= totalPages) {
        console.log('Navigating to page:', page);
        
        // Create a hidden form to submit page data
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("dispensary.filter") }}';
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add page number
        const pageInput = document.createElement('input');
        pageInput.type = 'hidden';
        pageInput.name = 'page';
        pageInput.value = page;
        form.appendChild(pageInput);
        
        // Add current search and filter parameters
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('search')) {
            const searchInput = document.createElement('input');
            searchInput.type = 'hidden';
            searchInput.name = 'search';
            searchInput.value = urlParams.get('search');
            form.appendChild(searchInput);
        }
        
        if (urlParams.get('category')) {
            const categoryInput = document.createElement('input');
            categoryInput.type = 'hidden';
            categoryInput.name = 'category';
            categoryInput.value = urlParams.get('category');
            form.appendChild(categoryInput);
        }
        
        if (urlParams.get('stock')) {
            const stockInput = document.createElement('input');
            stockInput.type = 'hidden';
            stockInput.name = 'stock';
            stockInput.value = urlParams.get('stock');
            form.appendChild(stockInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
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

// Transfer Modal Functionality
var selectedMedicine = null;
var allMedicines = [];

// Ensure variables are properly initialized
if (typeof allMedicines === 'undefined') {
    allMedicines = [];
}
if (typeof selectedMedicine === 'undefined') {
    selectedMedicine = null;
}

// Global initialization function
function initializeTransferModal() {
    console.log('Initializing transfer modal variables...');
    if (!allMedicines || !Array.isArray(allMedicines)) {
        allMedicines = [];
        console.log('Initialized allMedicines as empty array');
    }
    if (selectedMedicine === undefined || selectedMedicine === null) {
        selectedMedicine = null;
        console.log('Initialized selectedMedicine as null');
    }
    console.log('Transfer modal variables initialized:', { 
        allMedicines: allMedicines ? allMedicines.length : 'undefined', 
        selectedMedicine: selectedMedicine 
    });
}

function openTransferModal() {
    console.log('Opening transfer modal...');
    
    // Ensure variables are initialized
    initializeTransferModal();
    
    const modal = document.getElementById('transferModal');
    if (modal) {
        modal.classList.remove('hidden');
        loadInventoryMedicines();
    } else {
        console.error('Transfer modal not found');
        alert('Transfer modal not found. Please refresh the page.');
    }
}

function closeTransferModal() {
    document.getElementById('transferModal').classList.add('hidden');
    selectedMedicine = null;
    const transferDetails = document.getElementById('transferDetails');
    const selectedMedicineDetails = document.getElementById('selectedMedicineDetails');
    const transferSearch = document.getElementById('transferSearch');
    const transferQuantity = document.getElementById('transferQuantity');
    const transferNotes = document.getElementById('transferNotes');
    
    if (transferDetails) transferDetails.classList.remove('hidden');
    if (selectedMedicineDetails) selectedMedicineDetails.classList.add('hidden');
    if (transferSearch) transferSearch.value = '';
    if (transferQuantity) transferQuantity.value = '';
    if (transferNotes) transferNotes.value = '';
}

function loadInventoryMedicines() {
    console.log('Loading inventory medicines...');
    fetch('/transfers/inventory-medicines')
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data && data.medicines && Array.isArray(data.medicines)) {
                allMedicines = data.medicines;
                console.log('Loaded medicines:', allMedicines.length);
                displayMedicines(allMedicines);
            } else {
                console.error('Invalid data received:', data);
                allMedicines = []; // Ensure it's always an array
                alert('Error: Invalid data received from server');
            }
        })
        .catch(error => {
            console.error('Error loading medicines:', error);
            allMedicines = []; // Ensure it's always an array
            alert('Error loading medicines: ' + error.message);
        });
}

function displayMedicines(medicines) {
    const medicineList = document.getElementById('medicineList');
    if (!medicineList) {
        console.error('Medicine list container not found');
        return;
    }
    
    medicineList.innerHTML = '';
    
    if (!medicines || medicines.length === 0) {
        medicineList.innerHTML = '<div class="text-center py-8 text-gray-500 dark:text-gray-400">No medicines available for transfer</div>';
        return;
    }
    
    medicines.forEach(medicine => {
        if (!medicine || !medicine.name) {
            console.warn('Invalid medicine data:', medicine);
            return;
        }
        
        const medicineCard = document.createElement('div');
        medicineCard.className = 'bg-white dark:bg-gray-600 rounded-lg p-4 border border-gray-200 dark:border-gray-500 cursor-pointer hover:shadow-md transition-all duration-200';
        medicineCard.onclick = () => selectMedicine(medicine);
        
        medicineCard.innerHTML = `
            <div class="flex justify-between items-start mb-2">
                <h5 class="font-semibold text-gray-900 dark:text-white">${medicine.name || 'Unknown Medicine'}</h5>
                <span class="px-2 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs rounded-full">
                    In Stock
                </span>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">${medicine.manufacturer || 'N/A'}</p>
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs rounded-full">
                    ${medicine.category?.name || 'Uncategorized'}
                </span>
            </div>
            <div class="flex justify-between items-center text-sm">
                <span class="text-gray-600 dark:text-gray-400">${medicine.stock_quantity || 0} ${medicine.unit || ''}</span>
                <span class="text-gray-600 dark:text-gray-400">Batch: ${medicine.batch_number || 'N/A'}</span>
            </div>
        `;
        
        medicineList.appendChild(medicineCard);
    });
}

function selectMedicine(medicine) {
    if (!medicine) {
        console.error('No medicine provided to selectMedicine');
        return;
    }
    
    selectedMedicine = medicine;
    
    // Update UI
    const transferDetails = document.getElementById('transferDetails');
    const selectedMedicineDetails = document.getElementById('selectedMedicineDetails');
    
    if (transferDetails) transferDetails.classList.add('hidden');
    if (selectedMedicineDetails) selectedMedicineDetails.classList.remove('hidden');
    
    // Update medicine details
    const selectedMedicineName = document.getElementById('selectedMedicineName');
    const availableStock = document.getElementById('availableStock');
    const batchNumber = document.getElementById('batchNumber');
    const expiryDate = document.getElementById('expiryDate');
    const medicineCategory = document.getElementById('medicineCategory');
    const maxAvailable = document.getElementById('maxAvailable');
    
    if (selectedMedicineName) selectedMedicineName.textContent = medicine.name || 'Unknown Medicine';
    if (availableStock) availableStock.textContent = `${medicine.stock_quantity || 0} ${medicine.unit || ''}`;
    if (batchNumber) batchNumber.textContent = medicine.batch_number || 'N/A';
    if (expiryDate) {
        try {
            expiryDate.textContent = new Date(medicine.expiry_date).toLocaleDateString();
        } catch (e) {
            expiryDate.textContent = 'N/A';
        }
    }
    if (medicineCategory) medicineCategory.textContent = medicine.category?.name || 'Uncategorized';
    if (maxAvailable) maxAvailable.textContent = `${medicine.stock_quantity || 0} ${medicine.unit || ''}`;
    
    // Update summary
    updateTransferSummary();
}

function updateTransferSummary() {
    if (!selectedMedicine) return;
    
    const quantityInput = document.getElementById('transferQuantity');
    const quantity = quantityInput ? quantityInput.value : '';
    const transferQuantity = quantity ? parseInt(quantity) : selectedMedicine.stock_quantity;
    const remainingStock = selectedMedicine.stock_quantity - transferQuantity;
    
    const summaryItem = document.getElementById('summaryItem');
    const summaryQuantity = document.getElementById('summaryQuantity');
    const summaryBatch = document.getElementById('summaryBatch');
    const summaryRemaining = document.getElementById('summaryRemaining');
    
    if (summaryItem) summaryItem.textContent = selectedMedicine.name || 'Unknown Medicine';
    if (summaryQuantity) {
        summaryQuantity.textContent = quantity ? `${transferQuantity} ${selectedMedicine.unit || ''}` : `All available (${selectedMedicine.stock_quantity} ${selectedMedicine.unit || ''})`;
    }
    if (summaryBatch) summaryBatch.textContent = selectedMedicine.batch_number || 'N/A';
    if (summaryRemaining) summaryRemaining.textContent = `${remainingStock} ${selectedMedicine.unit || ''}`;
}

function confirmTransfer() {
    if (!selectedMedicine) {
        alert('Please select a medicine to transfer');
        return;
    }
    
    const quantityInput = document.getElementById('transferQuantity');
    const notesInput = document.getElementById('transferNotes');
    const quantity = quantityInput ? quantityInput.value : '';
    const notes = notesInput ? notesInput.value : '';
    
    const transferData = {
        medicine_id: selectedMedicine.id,
        quantity: quantity ? parseInt(quantity) : null,
        notes: notes
    };
    
    // Show loading state
    const confirmBtn = document.getElementById('confirmTransfer');
    if (!confirmBtn) {
        alert('Transfer button not found');
        return;
    }
    
    const originalText = confirmBtn.innerHTML;
    confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    confirmBtn.disabled = true;
    
    fetch('/transfers/to-dispensary', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
        },
        body: JSON.stringify(transferData)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('Transfer completed successfully!');
            closeTransferModal();
            // Refresh the page to show updated data
            location.reload();
        } else {
            alert('Transfer failed: ' + (data.error || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Transfer failed: ' + error.message);
    })
    .finally(() => {
        if (confirmBtn) {
            confirmBtn.innerHTML = originalText;
            confirmBtn.disabled = false;
        }
    });
}

// Event listeners for transfer modal
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up transfer modal event listeners...');
    
    // Initialize transfer modal variables
    initializeTransferModal();
    
    // Initialize transfer history variables
    initializeTransferHistory();
    
    // Setup all event listeners safely
    setupAllEventListeners();
    
    // Initialize Import/Export modal
    initializeImportExportModal();
    
    // Wait a bit for the modal to be ready
    setTimeout(function() {
        console.log('Setting up transfer modal event listeners...');
        
        // Search functionality
        const transferSearch = document.getElementById('transferSearch');
        if (transferSearch) {
            console.log('Setting up search functionality...');
            transferSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                console.log('Searching with term:', searchTerm, 'allMedicines length:', allMedicines ? allMedicines.length : 'undefined');
                if (allMedicines && Array.isArray(allMedicines) && allMedicines.length > 0) {
                    const filteredMedicines = allMedicines.filter(medicine => 
                        medicine.name.toLowerCase().includes(searchTerm) ||
                        (medicine.generic_name && medicine.generic_name.toLowerCase().includes(searchTerm)) ||
                        (medicine.batch_number && medicine.batch_number.toLowerCase().includes(searchTerm))
                    );
                    displayMedicines(filteredMedicines);
                } else {
                    console.log('No medicines available for search');
                }
            });
        } else {
            console.warn('Transfer search input not found');
        }
        
        // Quantity controls
        const decreaseBtn = document.getElementById('decreaseQuantity');
        const increaseBtn = document.getElementById('increaseQuantity');
        const quantityInput = document.getElementById('transferQuantity');
        
        if (decreaseBtn && quantityInput) {
            console.log('Setting up decrease button...');
            decreaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value) || 0;
                if (currentValue > 1) {
                    quantityInput.value = currentValue - 1;
                    updateTransferSummary();
                }
            });
        } else {
            console.warn('Decrease button or quantity input not found');
        }
        
        if (increaseBtn && quantityInput) {
            console.log('Setting up increase button...');
            increaseBtn.addEventListener('click', function() {
                const currentValue = parseInt(quantityInput.value) || 0;
                if (selectedMedicine && currentValue < selectedMedicine.stock_quantity) {
                    quantityInput.value = currentValue + 1;
                    updateTransferSummary();
                }
            });
        } else {
            console.warn('Increase button or quantity input not found');
        }
        
        if (quantityInput) {
            console.log('Setting up quantity input...');
            quantityInput.addEventListener('input', function() {
                if (selectedMedicine) {
                    const value = parseInt(this.value);
                    if (value > selectedMedicine.stock_quantity) {
                        this.value = selectedMedicine.stock_quantity;
                    }
                    updateTransferSummary();
                }
            });
        } else {
            console.warn('Quantity input not found');
        }
        
        console.log('Transfer modal event listeners setup complete');
    }, 500); // Increased timeout to ensure modal is ready
    
    // Transfer History Modal Event Listeners
    setTimeout(function() {
        console.log('Setting up transfer history modal event listeners...');
        
        // Search functionality
        const historySearch = document.getElementById('historySearch');
        if (historySearch) {
            historySearch.addEventListener('input', function() {
                console.log('Searching transfer history...');
                // Implement search functionality
                loadTransferHistory();
            });
        }
        
        // Status filter
        const statusFilter = document.getElementById('statusFilter');
        if (statusFilter) {
            statusFilter.addEventListener('change', function() {
                console.log('Filtering by status:', this.value);
                loadTransferHistory();
            });
        }
        
        // Date filter
        const dateFilter = document.getElementById('dateFilter');
        if (dateFilter) {
            dateFilter.addEventListener('change', function() {
                console.log('Filtering by date:', this.value);
                loadTransferHistory();
            });
        }
        
        // Refresh button
        const refreshHistory = document.getElementById('refreshHistory');
        if (refreshHistory) {
            refreshHistory.addEventListener('click', function() {
                console.log('Refreshing transfer history...');
                loadTransferHistory();
            });
        }
        
        console.log('Transfer history modal event listeners setup complete');
    }, 1000);
});
</script>

<!-- View Medicine Modal -->
<div id="viewMedicineModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-eye text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Medicine Details</h2>
                    <p class="text-gray-600 dark:text-gray-400">View Medicine Information</p>
                </div>
            </div>
            <button onclick="hideViewMedicineModal()" class="w-10 h-10 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <div id="viewMedicineContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideViewMedicineModal()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Transfer Details Modal -->
<div id="transferDetailsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-exchange-alt text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Transfer Details</h2>
                    <p class="text-gray-600 dark:text-gray-400">View Transfer Information</p>
                </div>
            </div>
            <button onclick="hideTransferDetailsModal()" class="w-10 h-10 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <div id="transferDetailsContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideTransferDetailsModal()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Close
            </button>
        </div>
    </div>
</div>

</div>
@endsection