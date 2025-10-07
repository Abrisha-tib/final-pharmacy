@extends('layouts.app')

@section('title', 'Store Management - Analog Pharmacy Management System')
@section('page-title', 'Store Management')
@section('page-description', 'Manage your pharmacy\'s medicine store')

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
</style>

<!-- Welcome Section -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-blue-200 dark:border-gray-600">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Store Management</h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg">Manage your pharmacy's medicine store</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">System Status</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white" id="inventoryStatus">All Systems Operational</p>
            </div>
        </div>
    </div>
</div>

<!-- Inventory Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Items Card -->
    <div class="card-hover bg-gradient-to-br from-blue-400 to-indigo-500 dark:from-blue-800 dark:to-indigo-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-blue-600 dark:border-blue-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-blue-800 dark:text-blue-200 uppercase tracking-wide">Total Items</p>
                <p class="text-3xl font-bold text-blue-900 dark:text-white mt-2 mb-1" id="totalItems">{{ $totalMedicines }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-blue-500 dark:bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-boxes text-xs mr-1"></i>
                        {{ $activeMedicines }} Active
                    </div>
                    <span class="text-xs text-blue-700 dark:text-blue-300 font-bold">medicines</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-boxes text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Value Card -->
    <div class="card-hover bg-gradient-to-br from-emerald-400 to-emerald-500 dark:from-emerald-800 dark:to-emerald-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-emerald-600 dark:border-emerald-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-emerald-800 dark:text-emerald-200 uppercase tracking-wide">Total Value</p>
                <p class="text-3xl font-bold text-emerald-900 dark:text-white mt-2 mb-1" id="totalValue">Br {{ number_format($totalValue, 2) }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-emerald-500 dark:bg-emerald-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-chart-line text-xs mr-1"></i>
                        +5.2%
                    </div>
                    <span class="text-xs text-emerald-700 dark:text-emerald-300 font-bold">this month</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-dollar-sign text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- In Stock Card -->
    <div class="card-hover bg-gradient-to-br from-green-400 to-green-500 dark:from-green-800 dark:to-green-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-green-600 dark:border-green-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-green-800 dark:text-green-200 uppercase tracking-wide">In Stock</p>
                <p class="text-3xl font-bold text-green-900 dark:text-white mt-2 mb-1" id="inStockCount">{{ $inStock }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-green-500 dark:bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-check-circle text-xs mr-1"></i>
                        {{ $totalMedicines > 0 ? round(($inStock / $totalMedicines) * 100) : 0 }}%
                    </div>
                    <span class="text-xs text-green-700 dark:text-green-300 font-bold">available</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Out of Stock Card -->
    <div class="card-hover bg-gradient-to-br from-red-400 to-red-500 dark:from-red-800 dark:to-red-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-red-600 dark:border-red-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-red-800 dark:text-red-200 uppercase tracking-wide">Out of Stock</p>
                <p class="text-3xl font-bold text-red-900 dark:text-white mt-2 mb-1" id="outOfStockCount">{{ $outOfStock }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-red-500 dark:bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-exclamation-triangle text-xs mr-1"></i>
                        Alert
                    </div>
                    <span class="text-xs text-red-700 dark:text-red-300 font-bold">none</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
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
        
        <!-- Right Group: Categories & Import/Export -->
        <div class="flex items-center gap-3 no-print">
            <button id="manageCategoriesBtn" class="px-4 py-2 bg-white dark:bg-gray-800 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg font-medium text-sm transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-700 flex items-center">
                <i class="fas fa-tags mr-2 text-sm"></i>Manage Categories
            </button>
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
            <label class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Search Medicines</label>
            <div class="relative">
                <input type="text" id="medicineSearch" placeholder="Search by name, generic name, or batch number" 
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            </div>
        </div>

        <!-- Category Filter -->
        <div class="flex flex-col">
            <label class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Category</label>
            <select id="categoryFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                <option value="">All Categories</option>
                <!-- Categories will be populated dynamically -->
            </select>
        </div>

        <!-- Batch Number Filter -->
        <div class="flex flex-col">
            <label class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Batch Number</label>
            <select id="batchFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                <option value="">All Batch Numbers</option>
                <!-- Batch numbers will be populated dynamically -->
            </select>
        </div>

        <!-- Stock Status Filter -->
        <div class="flex flex-col">
            <label class="text-sm font-bold text-gray-700 dark:text-gray-300 mb-1">Stock Status</label>
            <select id="stockFilter" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white transition-all duration-200">
                <option value="">All</option>
                <option value="in-stock">In Stock</option>
                <option value="low-stock">Low Stock</option>
                <option value="out-of-stock">Out of Stock</option>
            </select>
        </div>
    </div>
</div>

<!-- Inventory Table -->
<div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden transition-all duration-300">
    <!-- Table Header -->
    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
        <div class="flex items-center justify-between">
            <div class="text-gray-600 dark:text-gray-400 text-sm" id="medicineCount">
                Showing {{ $medicines->count() }} of {{ $medicines->total() }} medicines
            </div>
            <div class="flex gap-3 no-print">
                <button onclick="refreshMedicines()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 shadow-sm flex items-center">
                    <i class="fas fa-sync-alt mr-2 text-sm"></i>Refresh
                </button>
                <button onclick="printInventory()" class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 shadow-sm flex items-center">
                    <i class="fas fa-print mr-2 text-sm"></i>Print
                </button>
                <button onclick="showBatchAddModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 shadow-sm flex items-center">
                    <i class="fas fa-upload mr-2 text-sm"></i>Batch Add
                </button>
                <button onclick="showAddMedicineModal()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 shadow-sm flex items-center">
                    <i class="fas fa-plus mr-2 text-sm"></i>Add Medicine
                </button>
            </div>
        </div>
    </div>

    <!-- Card View -->
    <div id="cardView" class="p-6 bg-gray-50 dark:bg-gray-900">
        <div id="medicinesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($medicines as $medicine)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 p-6 hover:shadow-xl transition-all duration-300" data-medicine-id="{{ $medicine->id }}">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">{{ $medicine->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $medicine->generic_name ?: 'No generic name' }}</p>
                    <div class="flex gap-2 mb-4">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-semibold" style="background-color: {{ $medicine->category?->color ?? '#3B82F6' }}20; color: {{ $medicine->category?->color ?? '#3B82F6' }}">
                            {{ $medicine->category?->name ?? 'No Category' }}
                        </span>
                        <span class="px-3 py-1 {{ $medicine->is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' }} rounded-full text-xs font-semibold">
                            {{ $medicine->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Strength & Form:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $medicine->strength }} {{ $medicine->form }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Barcode:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $medicine->barcode }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Batch Number:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $medicine->batch_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Manufacturer:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $medicine->manufacturer }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Stock Quantity:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $medicine->stock_quantity }} {{ $medicine->unit }}</span>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Selling Price:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Br {{ number_format($medicine->selling_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Cost Price:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Br {{ number_format($medicine->cost_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Prescription:</span>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $medicine->prescription_required === 'yes' ? 'Required' : 'Not Required' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ $medicine->is_active ? 'Active' : 'Inactive' }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2 no-print">
                    <button onclick="viewMedicine({{ $medicine->id }})" class="flex-1 px-3 py-2 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-sm font-semibold hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-eye mr-2"></i>View
                    </button>
                    <button onclick="editMedicine({{ $medicine->id }})" class="flex-1 px-3 py-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg text-sm font-semibold hover:bg-green-200 dark:hover:bg-green-800 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </button>
                    <button onclick="deleteMedicine({{ $medicine->id }})" class="flex-1 px-3 py-2 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg text-sm font-semibold hover:bg-red-200 dark:hover:bg-red-800 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-pills text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Medicines Found</h3>
                <p class="text-gray-500 dark:text-gray-500">Add your first medicine to get started</p>
                    </div>
            @endforelse
        </div>
    </div>

    <!-- Table Content -->
    <div id="tableView" class="hidden overflow-x-auto bg-gray-50 dark:bg-gray-900">
        <table class="w-full">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Medicine</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Category</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Strength & Form</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Batch</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Stock</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Price</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Cost</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Expiry</th>
                    <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700" id="inventoryTableBody">
                @forelse($medicines as $medicine)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, {{ $medicine->category?->color ?? '#3B82F6' }}, {{ $medicine->category?->color ?? '#3B82F6' }}CC);">
                                <i class="fas fa-pills text-white"></i>
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $medicine->name }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $medicine->generic_name ?: 'No generic name' }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background-color: {{ $medicine->category?->color ?? '#3B82F6' }}20; color: {{ $medicine->category?->color ?? '#3B82F6' }}">
                            {{ $medicine->category?->name ?? 'No Category' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-3 py-1 {{ $medicine->is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200' }} rounded-full text-xs font-semibold">
                            {{ $medicine->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $medicine->strength }} {{ $medicine->form }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $medicine->barcode }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $medicine->stock_quantity }} {{ $medicine->unit }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">Br {{ number_format($medicine->selling_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Br {{ number_format($medicine->cost_price, 2) }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"></td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium no-print">
                        <div class="flex space-x-2">
                            <button onclick="viewMedicine({{ $medicine->id }})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button onclick="editMedicine({{ $medicine->id }})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors duration-200">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button onclick="deleteMedicine({{ $medicine->id }})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="px-6 py-12 text-center">
                        <div class="text-center">
                            <i class="fas fa-pills text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Medicines Found</h3>
                            <p class="text-gray-500 dark:text-gray-500">Add your first medicine to get started</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
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
@endsection

@section('scripts')
<script>
    // Pass server-side filter data to JavaScript
    window.serverFilterData = {
        category: '{{ $request->input("category", "") }}',
        batch: '{{ $request->input("batch", "") }}',
        stock: '{{ $request->input("stock", "") }}',
        search: '{{ $request->input("search", "") }}'
    };
    console.log('Server filter data:', window.serverFilterData);
</script>
<script>
/**
 * Store Management Controller
 * Handles search, filtering, and table interactions
 */
(function() {
    'use strict';
    
    // Initialize inventory management when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        initializeInventoryManagement();
    });
    
    /**
     * Initialize inventory management functionality
     */
    function initializeInventoryManagement() {
        console.log('Initializing inventory management...');
        console.log('DOM ready, setting up event listeners...');
        
        // Initialize search functionality
        initializeSearch();
        
        // Initialize filters
        initializeFilters();
        
        // Initialize table interactions
        initializeTableInteractions();
        
        // Initialize action buttons
        initializeActionButtons();
        
        // Initialize navigation tabs
        initializeNavigationTabs();
        
        console.log('Inventory management initialized successfully');
    }
    
    /**
     * Initialize search functionality
     */
    function initializeSearch() {
        const searchInput = document.getElementById('medicineSearch');
        if (!searchInput) return;
        
        // Get search term from server-side data
        const serverData = window.serverFilterData || {};
        const searchTerm = serverData.search || '';
        searchInput.value = searchTerm;
        
        // Add debouncing to search
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                const searchTerm = this.value;
                performServerSideSearch(searchTerm);
            }, 500); // Wait 500ms after user stops typing
        });
    }
    
    /**
     * Initialize filter functionality
     */
    function initializeFilters() {
        const categoryFilter = document.getElementById('categoryFilter');
        const batchFilter = document.getElementById('batchFilter');
        const stockFilter = document.getElementById('stockFilter');
        
        // Add event listeners (but don't set values yet - they'll be set after population)
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function() {
                performServerSideFilter('category', this.value);
            });
        }
        
        if (batchFilter) {
            batchFilter.addEventListener('change', function() {
                console.log('Batch filter changed to:', this.value);
                performServerSideFilter('batch', this.value);
            });
        }
        
        if (stockFilter) {
            stockFilter.addEventListener('change', function() {
                console.log('Stock filter changed to:', this.value);
                performServerSideFilter('stock', this.value);
            });
        }
    }
    
    /**
     * Initialize filter values from URL parameters (called after filters are populated)
     */
    function initializeFiltersFromURL() {
        console.log('Initializing filters from POST data...');
        
        const categoryFilter = document.getElementById('categoryFilter');
        const batchFilter = document.getElementById('batchFilter');
        const stockFilter = document.getElementById('stockFilter');
        
        console.log('Filter elements found:', {
            categoryFilter: !!categoryFilter,
            batchFilter: !!batchFilter,
            stockFilter: !!stockFilter
        });
        
        // Get filter values from server-side data (passed from Laravel)
        // These values are set by the server when rendering the page
        const serverData = window.serverFilterData || {};
        
        if (categoryFilter) {
            const categoryValue = serverData.category || '';
            console.log('Setting category filter to:', categoryValue);
            console.log('Category filter options:', categoryFilter.options.length);
            categoryFilter.value = categoryValue;
            console.log('Category filter value after setting:', categoryFilter.value);
        }
        
        if (batchFilter) {
            const batchValue = serverData.batch || '';
            console.log('Setting batch filter to:', batchValue);
            console.log('Batch filter options:', batchFilter.options.length);
            batchFilter.value = batchValue;
            console.log('Batch filter value after setting:', batchFilter.value);
        }
        
        if (stockFilter) {
            const stockValue = serverData.stock || '';
            console.log('Setting stock filter to:', stockValue);
            console.log('Stock filter options:', stockFilter.options.length);
            stockFilter.value = stockValue;
            console.log('Stock filter value after setting:', stockFilter.value);
        }
    }
    
    /**
     * Filter table based on criteria
     */
    function filterTable(value, type) {
        const tableBody = document.getElementById('inventoryTableBody');
        const cardsContainer = document.getElementById('medicinesContainer');
        
        // Filter table view
        if (tableBody) {
            filterTableView(tableBody, value, type);
        }
        
        // Filter card view
        if (cardsContainer) {
            filterCardView(cardsContainer, value, type);
        }
    }

    /**
     * Filter table view
     */
    function filterTableView(tableBody, value, type) {
        
        const rows = tableBody.querySelectorAll('tr');
        let visibleCount = 0;
        
        rows.forEach(row => {
            let shouldShow = true;
            
            if (type === 'search' && value) {
                const text = row.textContent.toLowerCase();
                shouldShow = text.includes(value);
            } else if (type === 'category' && value) {
                const categoryCell = row.querySelector('td:nth-child(2) span');
                if (categoryCell) {
                    const category = categoryCell.textContent.toLowerCase();
                    shouldShow = category.includes(value.toLowerCase());
                }
            } else if (type === 'batch' && value) {
                const batchCell = row.querySelector('td:nth-child(5)');
                if (batchCell) {
                    const batch = batchCell.textContent.toLowerCase();
                    shouldShow = batch.includes(value.toLowerCase());
                }
            } else if (type === 'stock' && value) {
                const stockCell = row.querySelector('td:nth-child(4)');
                if (stockCell) {
                    const stockText = stockCell.textContent;
                    // Extract numeric stock quantity
                    const stockMatch = stockText.match(/(\d+)/);
                    const stockQuantity = stockMatch ? parseInt(stockMatch[1]) : 0;
                    
                    console.log('Stock filtering:', value, 'Stock quantity:', stockQuantity);
                    
                    if (value === 'in-stock') {
                        shouldShow = stockQuantity > 10;
                    } else if (value === 'low-stock') {
                        shouldShow = stockQuantity > 0 && stockQuantity <= 10;
                    } else if (value === 'out-of-stock') {
                        shouldShow = stockQuantity <= 0;
                    }
                }
            }
            
            if (shouldShow) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });
        
        // Update medicine count
        updateMedicineCount(visibleCount, rows.length);
    }

    /**
     * Filter card view
     */
    function filterCardView(cardsContainer, value, type) {
        const cards = cardsContainer.querySelectorAll('[data-medicine-id]');
        let visibleCount = 0;
        
        cards.forEach(card => {
            let shouldShow = true;
            
            if (type === 'search' && value) {
                const text = card.textContent.toLowerCase();
                shouldShow = text.includes(value);
            } else if (type === 'category' && value) {
                const categorySpan = card.querySelector('.bg-blue-100, .bg-blue-900');
                if (categorySpan) {
                    const category = categorySpan.textContent.toLowerCase();
                    shouldShow = category.includes(value.toLowerCase());
                }
            } else if (type === 'batch' && value) {
                const batchText = card.textContent.toLowerCase();
                shouldShow = batchText.includes(value.toLowerCase());
            } else if (type === 'stock' && value) {
                const stockText = card.textContent;
                // Extract numeric stock quantity from card text
                const stockMatch = stockText.match(/Stock Quantity:\s*(\d+)/i);
                const stockQuantity = stockMatch ? parseInt(stockMatch[1]) : 0;
                
                console.log('Card stock filtering:', value, 'Stock quantity:', stockQuantity);
                
                if (value === 'in-stock') {
                    shouldShow = stockQuantity > 10;
                } else if (value === 'low-stock') {
                    shouldShow = stockQuantity > 0 && stockQuantity <= 10;
                } else if (value === 'out-of-stock') {
                    shouldShow = stockQuantity <= 0;
                }
            }
            
            if (shouldShow) {
                card.style.display = '';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update medicine count
        updateMedicineCount(visibleCount, cards.length);
    }
    
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
                    last_page: Math.ceil(parseInt(matches[3]) / 12),
                    per_page: 12
                };
                totalPages = pagination.last_page;
                
                console.log('Server-side pagination initialized:', pagination);
                updatePaginationUI();
            }
        }
    }

    /**
     * Navigate to specific page (with clean URL)
     */
    function goToPage(page) {
        if (page >= 1 && page <= totalPages) {
            console.log('Navigating to page:', page);
            
            // Create a hidden form to submit page data
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("inventory.filter") }}';
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
            
            // Add current search term
            const searchInput = document.getElementById('medicineSearch');
            if (searchInput && searchInput.value) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'search';
                input.value = searchInput.value;
                form.appendChild(input);
            }
            
            // Add current filter values
            const categoryFilter = document.getElementById('categoryFilter');
            const batchFilter = document.getElementById('batchFilter');
            const stockFilter = document.getElementById('stockFilter');
            
            if (categoryFilter && categoryFilter.value) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'category';
                input.value = categoryFilter.value;
                form.appendChild(input);
            }
            
            if (batchFilter && batchFilter.value) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'batch';
                input.value = batchFilter.value;
                form.appendChild(input);
            }
            
            if (stockFilter && stockFilter.value) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'stock';
                input.value = stockFilter.value;
                form.appendChild(input);
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

    /**
     * Perform server-side search
     */
    function performServerSideSearch(searchTerm) {
        console.log('Performing server-side search:', searchTerm);
        
        // Don't perform search if on cashier page
        if (window.location.pathname === '/cashier') {
            console.log('Search function called on cashier page - skipping');
            return;
        }
        
        // Create a hidden form to submit search data
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.location.pathname;
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add search term
        const searchInput = document.createElement('input');
        searchInput.type = 'hidden';
        searchInput.name = 'search';
        searchInput.value = searchTerm || '';
        form.appendChild(searchInput);
        
        // Add current filter values
        const categoryFilter = document.getElementById('categoryFilter');
        const batchFilter = document.getElementById('batchFilter');
        const stockFilter = document.getElementById('stockFilter');
        
        if (categoryFilter && categoryFilter.value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'category';
            input.value = categoryFilter.value;
            form.appendChild(input);
        }
        
        if (batchFilter && batchFilter.value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'batch';
            input.value = batchFilter.value;
            form.appendChild(input);
        }
        
        if (stockFilter && stockFilter.value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'stock';
            input.value = stockFilter.value;
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
    }

    /**
     * Perform server-side filtering
     */
    function performServerSideFilter(filterType, filterValue) {
        console.log('Performing server-side filter:', filterType, filterValue);
        
        // Don't perform filter if on cashier page
        if (window.location.pathname === '/cashier') {
            console.log('Filter function called on cashier page - skipping');
            return;
        }
        
        // Create a hidden form to submit filter data
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = window.location.pathname;
        form.style.display = 'none';
        
        // Add CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = csrfToken;
        form.appendChild(csrfInput);
        
        // Add the changed filter
        if (filterValue && filterValue.trim()) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = filterType;
            input.value = filterValue;
            form.appendChild(input);
        }
        
        // Add current search term
        const searchInput = document.getElementById('medicineSearch');
        if (searchInput && searchInput.value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'search';
            input.value = searchInput.value;
            form.appendChild(input);
        }
        
        // Add other filter values (preserve existing filters)
        const categoryFilter = document.getElementById('categoryFilter');
        const batchFilter = document.getElementById('batchFilter');
        const stockFilter = document.getElementById('stockFilter');
        
        if (filterType !== 'category' && categoryFilter && categoryFilter.value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'category';
            input.value = categoryFilter.value;
            form.appendChild(input);
        }
        
        if (filterType !== 'batch' && batchFilter && batchFilter.value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'batch';
            input.value = batchFilter.value;
            form.appendChild(input);
        }
        
        if (filterType !== 'stock' && stockFilter && stockFilter.value) {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'stock';
            input.value = stockFilter.value;
            form.appendChild(input);
        }
        
        document.body.appendChild(form);
        form.submit();
    }

    /**
     * Print inventory
     */
    function printInventory() {
        // Get current date and time
        const now = new Date();
        const dateTime = now.toLocaleDateString() + ' ' + now.toLocaleTimeString();
        
        // Create print content
        const printContent = `
            <!DOCTYPE html>
            <html>
            <head>
                <title>Inventory Report - ${dateTime}</title>
                <style>
                    @media print {
                        @page {
                            margin: 0.5in;
                            size: A4;
                        }
                        body {
                            font-family: Arial, sans-serif;
                            font-size: 12px;
                            line-height: 1.4;
                        }
                        .print-header {
                            text-align: center;
                            margin-bottom: 20px;
                            border-bottom: 2px solid #333;
                            padding-bottom: 10px;
                        }
                        .print-title {
                            font-size: 24px;
                            font-weight: bold;
                            margin-bottom: 5px;
                        }
                        .print-subtitle {
                            font-size: 14px;
                            color: #666;
                            margin-bottom: 5px;
                        }
                        .print-date {
                            font-size: 12px;
                            color: #888;
                            font-style: italic;
                        }
                        .print-info {
                            display: flex;
                            justify-content: space-between;
                            margin-bottom: 15px;
                            font-size: 11px;
                        }
                        .print-summary {
                            display: flex;
                            justify-content: space-between;
                            margin-bottom: 20px;
                            padding: 10px;
                            background-color: #f8f9fa;
                            border: 1px solid #dee2e6;
                            border-radius: 4px;
                            font-size: 11px;
                        }
                        .print-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                        }
                        .print-table th,
                        .print-table td {
                            border: 1px solid #333;
                            padding: 8px;
                            text-align: left;
                        }
                        .print-table th {
                            background-color: #f5f5f5;
                            font-weight: bold;
                        }
                        .print-table tr:nth-child(even) {
                            background-color: #f9f9f9;
                        }
                        .status-badge {
                            padding: 2px 6px;
                            border-radius: 3px;
                            font-size: 10px;
                            font-weight: bold;
                            text-transform: uppercase;
                        }
                        .status-badge.active {
                            background-color: #d4edda;
                            color: #155724;
                            border: 1px solid #c3e6cb;
                        }
                        .status-badge.inactive {
                            background-color: #f8d7da;
                            color: #721c24;
                            border: 1px solid #f5c6cb;
                        }
                        .print-footer {
                            margin-top: 30px;
                            text-align: center;
                            font-size: 10px;
                            color: #666;
                            border-top: 1px solid #333;
                            padding-top: 10px;
                        }
                        .print-footer p {
                            margin: 2px 0;
                        }
                        .no-print {
                            display: none !important;
                        }
                    }
                </style>
            </head>
            <body>
                <div class="print-header">
                    <div class="print-title">PHARMACY STORE REPORT</div>
                    <div class="print-subtitle">Store Management System</div>
                    <div class="print-date">Report Date: ${dateTime}</div>
                </div>
                
                <div class="print-info">
                    <div><strong>Total Items:</strong> ${medicines.length}</div>
                    <div><strong>Page:</strong> ${currentPage} of ${totalPages}</div>
                    <div><strong>Report ID:</strong> INV-${new Date().getTime().toString().slice(-6)}</div>
                </div>
                
                <div class="print-summary">
                    <div><strong>Active Items:</strong> ${medicines.filter(m => m.is_active).length}</div>
                    <div><strong>Inactive Items:</strong> ${medicines.filter(m => !m.is_active).length}</div>
                    <div><strong>Total Stock Value:</strong> ${medicines.reduce((sum, m) => sum + (m.selling_price * m.stock_quantity), 0).toLocaleString()} Br</div>
                </div>
                
                <table class="print-table">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Medicine Name</th>
                            <th>Generic Name</th>
                            <th>Category</th>
                            <th>Stock Qty</th>
                            <th>Unit</th>
                            <th>Batch No.</th>
                            <th>Expiry Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${medicines.map((medicine, index) => `
                            <tr>
                                <td>${index + 1}</td>
                                <td><strong>${medicine.name}</strong></td>
                                <td>${medicine.generic_name || '-'}</td>
                                <td>${medicine.category?.name || '-'}</td>
                                <td><strong>${medicine.stock_quantity}</strong></td>
                                <td>${medicine.unit || 'units'}</td>
                                <td>${medicine.batch_number || '-'}</td>
                                <td>${new Date(medicine.expiry_date).toLocaleDateString()}</td>
                                <td><span class="status-badge ${medicine.is_active ? 'active' : 'inactive'}">${medicine.is_active ? 'Active' : 'Inactive'}</span></td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
                
                <div class="print-footer">
                    <p><strong>Pharmacy Management System - Inventory Report</strong></p>
                    <p>Generated on ${dateTime} | Page ${currentPage} of ${totalPages} | Report ID: INV-${new Date().getTime().toString().slice(-6)}</p>
                    <p><em>This report contains essential inventory information only. For detailed analytics and management features, please access the system dashboard.</em></p>
                    <p>© ${new Date().getFullYear()} Pharmacy Management System. All rights reserved.</p>
                </div>
            </body>
            </html>
        `;
        
        // Open print window
        const printWindow = window.open('', '_blank', 'width=800,height=600');
        printWindow.document.write(printContent);
        printWindow.document.close();
        
        // Wait for content to load, then print
        printWindow.onload = function() {
            printWindow.focus();
            printWindow.print();
            printWindow.close();
        };
    }

    /**
     * Update medicine count display
     */
    function updateMedicineCount(visible, total) {
        const countElement = document.getElementById('medicineCount');
        if (countElement) {
            // Use server-side pagination data if available
            if (pagination) {
                countElement.textContent = `Showing ${pagination.from || 0} to ${pagination.to || 0} of ${pagination.total} medicines`;
            } else {
            countElement.textContent = `Showing ${visible} of ${total} medicines`;
            }
        }
    }
    
    /**
     * Initialize table interactions
     */
    function initializeTableInteractions() {
        const tableBody = document.getElementById('inventoryTableBody');
        if (!tableBody) return;
        
        // Add hover effects and click handlers
        const rows = tableBody.querySelectorAll('tr');
        rows.forEach(row => {
            row.addEventListener('mouseenter', function() {
                this.style.backgroundColor = 'rgba(59, 130, 246, 0.05)';
            });
            
            row.addEventListener('mouseleave', function() {
                this.style.backgroundColor = '';
            });
        });
    }
    
    /**
     * Initialize action buttons
     */
    function initializeActionButtons() {
        console.log('Initializing action buttons...');
        
        // Show Analytics button
        const analyticsBtn = document.querySelector('button:has(.fa-chart-bar)');
        if (analyticsBtn) {
            analyticsBtn.addEventListener('click', function() {
                console.log('Show Analytics clicked');
                // TODO: Implement analytics functionality
            });
        }
        
        // Add Medicine button
        const addMedicineBtn = document.querySelector('button:has(.fa-plus)');
        if (addMedicineBtn) {
            addMedicineBtn.addEventListener('click', function() {
                console.log('Add Medicine clicked');
                // TODO: Implement add medicine functionality
            });
        }
        
        // Import/Export button
        const importExportBtn = document.getElementById('importExportBtn');
        if (importExportBtn) {
            console.log('Import/Export button found');
            importExportBtn.addEventListener('click', function() {
                console.log('Import/Export clicked');
                showImportExportModal();
            });
        } else {
            console.log('Import/Export button not found');
        }
        
        // Refresh button
        const refreshBtn = document.querySelector('button:has(.fa-sync)');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', function() {
                console.log('Refresh clicked');
                location.reload();
            });
        }
        
        
        // View and Edit buttons for each medicine
        const viewButtons = document.querySelectorAll('button:has(.fa-eye)');
        const editButtons = document.querySelectorAll('button:has(.fa-edit)');
        
        viewButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('View medicine clicked');
                // TODO: Implement view medicine functionality
            });
        });
        
        editButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                console.log('Edit medicine clicked');
                // TODO: Implement edit medicine functionality
            });
        });
        
    }
    
    /**
     * Initialize navigation tabs functionality
     */
    function initializeNavigationTabs() {
        // Segmented Control: Cards & Table
        const cardsBtn = document.getElementById('cardsBtn');
        const tableBtn = document.getElementById('tableBtn');
        
        if (cardsBtn && tableBtn) {
            // Cards button (active by default)
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
                // TODO: Implement analytics functionality
            });
        }
        
        // Manage Categories button
        const categoriesBtn = document.getElementById('manageCategoriesBtn');
        if (categoriesBtn) {
            categoriesBtn.addEventListener('click', function() {
                console.log('Manage Categories clicked');
                showCategoryManagementModal();
            });
        } else {
            // Fallback: try to find by icon
            const allButtons = document.querySelectorAll('button');
            allButtons.forEach(button => {
                const icon = button.querySelector('.fa-tags');
                if (icon) {
                    button.addEventListener('click', function() {
                        console.log('Manage Categories clicked (fallback)');
                showCategoryManagementModal();
                    });
                }
            });
        }
        
        // Import/Export button
        const importExportBtn = document.querySelector('button:has(.fa-download)');
        if (importExportBtn) {
            importExportBtn.addEventListener('click', function() {
                console.log('Import/Export clicked');
                showImportExportModal();
            });
        }
    }
    
    /**
     * Show Category Management Modal
     */
    function showCategoryManagementModal() {
        console.log('showCategoryManagementModal called');
        const modal = document.getElementById('categoryModal');
        console.log('Modal element:', modal);
        if (modal) {
            console.log('Removing hidden class from modal');
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
            console.log('Modal should now be visible');
        } else {
            console.error('Category modal not found!');
        }
    }
    
    /**
     * Hide Category Management Modal
     */
    function hideCategoryManagementModal() {
        const modal = document.getElementById('categoryModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    
    /**
     * Show Create Category Modal
     */
    function showCreateCategoryModal() {
        const modal = document.getElementById('createCategoryModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    /**
     * Hide Create Category Modal
     */
    function hideCreateCategoryModal() {
        const modal = document.getElementById('createCategoryModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    
    /**
     * Show Add Medicine Modal
     */
    function showAddMedicineModal() {
        const modal = document.getElementById('addMedicineModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    /**
     * Hide Add Medicine Modal
     */
    function hideAddMedicineModal() {
        const modal = document.getElementById('addMedicineModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
    }
    
    /**
     * Show Batch Add Modal
     */
    function showBatchAddModal() {
        const modal = document.getElementById('batchAddModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }
    
    /**
     * Hide Batch Add Modal
     */
    function hideBatchAddModal() {
        const modal = document.getElementById('batchAddModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            
            // Reset the form and preview
            resetBatchAddForm();
        }
    }

    /**
     * Clear Batch Data
     */
    function clearBatchData() {
        // Check if there's any data to clear
        const csvInput = document.getElementById('csvDataInput');
        const hasData = csvInput && csvInput.value.trim() !== '';
        const hasProcessedData = batchMedicines.length > 0;
        
        if (!hasData && !hasProcessedData) {
            showNotification('No data to clear!', 'info');
            return;
        }
        
        // Clear CSV data
        if (csvInput) {
            csvInput.value = '';
        }
        
        // Hide preview
        const preview = document.getElementById('processedMedicinesPreview');
        if (preview) {
            preview.classList.add('hidden');
        }
        
        // Reset button states
        const addBtn = document.getElementById('addMedicinesBtn');
        if (addBtn) {
            addBtn.disabled = true;
            addBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        
        // Reset count
        const countElement = document.getElementById('medicineCount');
        if (countElement) {
            countElement.textContent = '0 medicines ready to add';
        }
        
        // Clear batch medicines array
        batchMedicines = [];
        
        // Disable clear button
        const clearBtn = document.getElementById('clearDataBtn');
        if (clearBtn) {
            clearBtn.disabled = true;
            clearBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        
        // Show notification
        showNotification('Batch data cleared successfully!', 'success');
    }

    /**
     * Reset Batch Add Form
     */
    function resetBatchAddForm() {
        // Clear CSV data
        const csvInput = document.getElementById('csvDataInput');
        if (csvInput) {
            csvInput.value = '';
        }
        
        // Hide preview
        const preview = document.getElementById('processedMedicinesPreview');
        if (preview) {
            preview.classList.add('hidden');
        }
        
        // Reset button states
        const addBtn = document.getElementById('addMedicinesBtn');
        if (addBtn) {
            addBtn.disabled = true;
            addBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }
        
        // Reset count
        const countElement = document.getElementById('medicineCount');
        if (countElement) {
            countElement.textContent = '0 medicines ready to add';
        }
        
        // Clear batch medicines array
        batchMedicines = [];
        
        // Disable clear button
        const clearBtn = document.getElementById('clearDataBtn');
        if (clearBtn) {
            clearBtn.disabled = true;
            clearBtn.classList.add('opacity-50', 'cursor-not-allowed');
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

    // Export functions for external use
    window.InventoryManagement = {
        filterTable,
        updateMedicineCount,
        initializeInventoryManagement,
        showCategoryManagementModal,
        hideCategoryManagementModal
    };
    
    // Also export functions globally for onclick handlers
    window.showCategoryManagementModal = showCategoryManagementModal;
    window.hideCategoryManagementModal = hideCategoryManagementModal;
    window.showCreateCategoryModal = showCreateCategoryModal;
    window.hideCreateCategoryModal = hideCreateCategoryModal;
    window.showAddMedicineModal = showAddMedicineModal;
    window.hideAddMedicineModal = hideAddMedicineModal;
    window.showBatchAddModal = showBatchAddModal;
    window.hideBatchAddModal = hideBatchAddModal;
    window.showImportExportModal = showImportExportModal;
    window.hideImportExportModal = hideImportExportModal;
    window.hideDeleteMedicineModal = hideDeleteMedicineModal;
    window.handleDeleteMedicineConfirmation = handleDeleteMedicineConfirmation;
    window.confirmDeleteMedicine = confirmDeleteMedicine;

    // Medicine Management Functionality
    let medicines = [];
    let currentMedicine = null;
    let currentPage = 1;
    let totalPages = 1;
    let pagination = null;
    
    // Client-side caching for shared hosting optimization
    window.medicineCache = window.medicineCache || {};
    window.categoryCache = window.categoryCache || {};
    
    // Performance monitoring for shared hosting
    window.performanceMetrics = {
        cacheHits: 0,
        serverRequests: 0,
        startTime: Date.now()
    };

    // Category Management Functionality
    let categories = [];
    let currentCategory = null;

    /**
     * Global function to handle session expiration
     */
    function handleSessionExpiration(response) {
        const contentType = response.headers.get('content-type');
        
        // Check if response is HTML (redirect to login) or 401/403 status
        if (contentType && contentType.includes('text/html')) {
            console.log('Session expired - HTML response detected');
            showNotification('Session expired. Please login again.', 'error');
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
            return true;
        }
        
        // Check for authentication errors
        if (response.status === 401 || response.status === 403) {
            console.log('Session expired - Authentication failed');
            showNotification('Session expired. Please login again.', 'error');
            setTimeout(() => {
                window.location.href = '/login';
            }, 2000);
            return true;
        }
        
        return false;
    }

    /**
     * Show category statistics
     */
    function showCategoryStatistics() {
        // Check if categories are loaded
        if (!categories || categories.length === 0) {
            showNotification('No categories loaded. Please refresh the categories first.', 'error');
            return;
        }
        
        const totalCategories = categories.length;
        const activeCategories = categories.filter(cat => cat.is_active).length;
        const inactiveCategories = totalCategories - activeCategories;
        
        // Calculate statistics
        const activePercentage = totalCategories > 0 ? Math.round((activeCategories / totalCategories) * 100) : 0;
        const inactivePercentage = totalCategories > 0 ? Math.round((inactiveCategories / totalCategories) * 100) : 0;
        
        // Create statistics modal content
        const statsContent = `
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-chart-bar text-white text-lg"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Category Statistics</h2>
                            <p class="text-gray-600 dark:text-gray-400">Overview of your inventory categories</p>
                        </div>
                    </div>
                    <button onclick="hideCategoryStatistics()" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg flex items-center justify-center transition-all duration-200">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Statistics Content -->
                <div class="p-6 overflow-y-auto flex-1">
                    <!-- Summary Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <!-- Total Categories -->
                        <div class="bg-blue-50 dark:bg-blue-900 rounded-xl p-6 border border-blue-200 dark:border-blue-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-600 dark:text-blue-400 text-sm font-semibold">Total Categories</p>
                                    <p class="text-3xl font-bold text-blue-900 dark:text-blue-100">${totalCategories}</p>
                                </div>
                                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-tags text-white text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Active Categories -->
                        <div class="bg-green-50 dark:bg-green-900 rounded-xl p-6 border border-green-200 dark:border-green-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-green-600 dark:text-green-400 text-sm font-semibold">Active Categories</p>
                                    <p class="text-3xl font-bold text-green-900 dark:text-green-100">${activeCategories}</p>
                                    <p class="text-xs text-green-600 dark:text-green-400 mt-1">${activePercentage}% of total</p>
                                </div>
                                <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-check-circle text-white text-xl"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Inactive Categories -->
                        <div class="bg-red-50 dark:bg-red-900 rounded-xl p-6 border border-red-200 dark:border-red-700">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-red-600 dark:text-red-400 text-sm font-semibold">Inactive Categories</p>
                                    <p class="text-3xl font-bold text-red-900 dark:text-red-100">${inactiveCategories}</p>
                                    <p class="text-xs text-red-600 dark:text-red-400 mt-1">${inactivePercentage}% of total</p>
                                </div>
                                <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-times-circle text-white text-xl"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bars -->
                    <div class="space-y-6">
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Active Categories</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">${activeCategories}/${totalCategories}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full transition-all duration-500" style="width: ${activePercentage}%"></div>
                            </div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Inactive Categories</span>
                                <span class="text-sm text-gray-600 dark:text-gray-400">${inactiveCategories}/${totalCategories}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                                <div class="bg-red-500 h-3 rounded-full transition-all duration-500" style="width: ${inactivePercentage}%"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Category List -->
                    <div class="mt-8">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Category Breakdown</h3>
                        <div class="space-y-3">
                            ${categories.map(category => `
                                <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: ${category.color}">
                                            <i class="fas fa-${category.icon} text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900 dark:text-white">${category.name}</p>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">${category.description || 'No description'}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full ${category.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}">
                                            ${category.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400">0 items</span>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
                    <button onclick="hideCategoryStatistics()" class="px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-semibold text-sm transition-all duration-200">
                        Close
                    </button>
                </div>
            </div>
        `;
        
        // Create and show statistics modal
        const statsModal = document.createElement('div');
        statsModal.id = 'categoryStatisticsModal';
        statsModal.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[10000] p-4';
        statsModal.innerHTML = statsContent;
        
        document.body.appendChild(statsModal);
        document.body.style.overflow = 'hidden';
    }

    /**
     * Hide category statistics modal
     */
    function hideCategoryStatistics() {
        const modal = document.getElementById('categoryStatisticsModal');
        if (modal) {
            modal.remove();
            document.body.style.overflow = 'auto';
        }
    }

    /**
     * Refresh categories - reload from API and clear filters
     */
    async function refreshCategories() {
        // Clear search and filter inputs
        const searchInput = document.getElementById('categorySearch');
        const statusFilter = document.getElementById('categoryStatusFilter');
        
        if (searchInput) {
            searchInput.value = '';
        }
        if (statusFilter) {
            statusFilter.value = '';
        }
        
        // Reload categories from API
        await loadCategories();
        
        // Show notification
        showNotification('Categories refreshed successfully!', 'success');
    }

    /**
     * Load categories from API
     */
    async function loadCategories() {
        try {
            const response = await fetch('/categories', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            // Check for session expiration
            if (handleSessionExpiration(response)) {
                return;
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            if (data.success) {
                categories = data.data;
                renderCategories();
                populateCategoryFilter();
            } else {
                showNotification(data.message || 'Error loading categories', 'error');
            }
        } catch (error) {
            console.error('Error loading categories:', error);
            showNotification('Error loading categories. Please refresh the page and try again.', 'error');
        }
    }

    /**
     * Populate category filter with dynamic data
     */
    function populateCategoryFilter() {
        const categoryFilter = document.getElementById('categoryFilter');
        if (!categoryFilter) return;
        
        // Clear existing options except "All Categories"
        categoryFilter.innerHTML = '<option value="">All Categories</option>';
        
        // Add category options
        if (categories && categories.length > 0) {
            categories.forEach(category => {
                const option = document.createElement('option');
                option.value = category.name.toLowerCase();
                option.textContent = category.name;
                categoryFilter.appendChild(option);
            });
        }
    }

    /**
     * Populate batch number filter with dynamic data
     */
    function populateBatchFilter() {
        const batchFilter = document.getElementById('batchFilter');
        if (!batchFilter) return;
        
        console.log('Populating batch filter with medicines:', medicines.length);
        
        // Clear existing options except "All Batch Numbers"
        batchFilter.innerHTML = '<option value="">All Batch Numbers</option>';
        
        let batchNumbers = [];
        
        // Try to get batch numbers from medicines array first
        if (medicines && medicines.length > 0) {
            batchNumbers = [...new Set(medicines.map(medicine => medicine.batch_number).filter(batch => batch))];
        }
        
        // Fallback: Extract batch numbers from server-side rendered HTML
        if (batchNumbers.length === 0) {
            console.log('No batch numbers from medicines array, extracting from HTML...');
            const medicineCards = document.querySelectorAll('[data-medicine-id]');
            const htmlBatchNumbers = [];
            
            medicineCards.forEach(card => {
                const batchText = card.textContent;
                const batchMatch = batchText.match(/Batch Number:\s*([^\n\r]+)/i);
                if (batchMatch) {
                    htmlBatchNumbers.push(batchMatch[1].trim());
                }
            });
            
            batchNumbers = [...new Set(htmlBatchNumbers)];
        }
        
        console.log('Found batch numbers:', batchNumbers);
        
        // Add batch number options
        batchNumbers.forEach(batch => {
            const option = document.createElement('option');
            option.value = batch.toLowerCase();
            option.textContent = batch;
            batchFilter.appendChild(option);
        });
        
        console.log('Batch filter populated with', batchNumbers.length, 'options');
    }

    /**
     * Populate stock status filter with dynamic data
     */
    function populateStockFilter() {
        const stockFilter = document.getElementById('stockFilter');
        if (!stockFilter) return;
        
        console.log('Populating stock filter with medicines:', medicines.length);
        
        // Clear existing options except "All Stock Statuses"
        stockFilter.innerHTML = '<option value="">All Stock Statuses</option>';
        
        // Calculate stock status counts
        let inStockCount = 0;
        let lowStockCount = 0;
        let outOfStockCount = 0;
        
        if (medicines && medicines.length > 0) {
            medicines.forEach(medicine => {
                const stockQuantity = parseInt(medicine.stock_quantity) || 0;
                if (stockQuantity > 10) {
                    inStockCount++;
                } else if (stockQuantity > 0 && stockQuantity <= 10) {
                    lowStockCount++;
                } else if (stockQuantity <= 0) {
                    outOfStockCount++;
                }
            });
        } else {
            // Fallback: extract from HTML
            const medicineCards = document.querySelectorAll('[data-medicine-id]');
            medicineCards.forEach(card => {
                const stockText = card.textContent;
                const stockMatch = stockText.match(/Stock Quantity:\s*(\d+)/i);
                const stockQuantity = stockMatch ? parseInt(stockMatch[1]) : 0;
                
                if (stockQuantity > 10) {
                    inStockCount++;
                } else if (stockQuantity > 0 && stockQuantity <= 10) {
                    lowStockCount++;
                } else if (stockQuantity <= 0) {
                    outOfStockCount++;
                }
            });
        }
        
        console.log('Stock counts - In Stock:', inStockCount, 'Low Stock:', lowStockCount, 'Out of Stock:', outOfStockCount);
        
        // Add stock status options with counts
        const stockStatuses = [
            { value: 'in-stock', text: `In Stock (${inStockCount})` },
            { value: 'low-stock', text: `Low Stock (${lowStockCount})` },
            { value: 'out-of-stock', text: `Out of Stock (${outOfStockCount})` }
        ];
        
        stockStatuses.forEach(status => {
            const option = document.createElement('option');
            option.value = status.value;
            option.textContent = status.text;
            stockFilter.appendChild(option);
        });
        
        console.log('Stock filter populated with counts');
    }

    /**
     * Filter and search categories
     */
    function filterCategories() {
        const searchTerm = document.getElementById('categorySearch').value.toLowerCase();
        const statusFilter = document.getElementById('categoryStatusFilter').value;
        
        const filteredCategories = categories.filter(category => {
            const matchesSearch = category.name.toLowerCase().includes(searchTerm) || 
                                 (category.description && category.description.toLowerCase().includes(searchTerm));
            const matchesStatus = statusFilter === '' || 
                                (statusFilter === 'active' && category.is_active) ||
                                (statusFilter === 'inactive' && !category.is_active);
            
            return matchesSearch && matchesStatus;
        });
        
        renderFilteredCategories(filteredCategories);
    }

    /**
     * Render filtered categories
     */
    function renderFilteredCategories(filteredCategories) {
        const container = document.getElementById('categoriesContainer');
        if (!container) return;

        if (filteredCategories.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-search text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Categories Found</h3>
                    <p class="text-gray-500 dark:text-gray-500">Try adjusting your search or filter criteria</p>
                </div>
            `;
            return;
        }

        container.innerHTML = filteredCategories.map(category => `
            <div class="category-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-200" data-category-id="${category.id}">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: ${category.color}">
                        <i class="fas fa-${category.icon} text-white text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">${category.name}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${category.description || 'No description'}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${category.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}">
                                ${category.is_active ? 'Active' : 'Inactive'}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">0 items</span>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons - Centered relative to the entire card -->
                <div class="flex items-center justify-center space-x-2 mt-4">
                    <button onclick="editCategory(${category.id})" class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-all duration-200 text-sm font-medium" title="Edit Category">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button onclick="toggleCategoryStatus(${category.id})" class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900 rounded-lg transition-all duration-200 text-sm font-medium" title="Toggle Status">
                        <i class="fas fa-toggle-${category.is_active ? 'on' : 'off'} mr-1"></i>Toggle
                    </button>
                    <button onclick="showDeleteCategoryModal(${category.id})" class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg transition-all duration-200 text-sm font-medium" title="Delete Category">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </div>
            </div>
        `).join('');
    }

    /**
     * Render categories in the category management modal
     */
    function renderCategories() {
        const container = document.getElementById('categoriesContainer');
        if (!container) return;

        if (categories.length === 0) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-tags text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Categories Found</h3>
                    <p class="text-gray-500 dark:text-gray-500">Create your first category to organize your inventory</p>
                </div>
            `;
            return;
        }

        container.innerHTML = categories.map(category => `
            <div class="category-card bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-200" data-category-id="${category.id}">
                <div class="flex items-start space-x-4">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: ${category.color}">
                        <i class="fas fa-${category.icon} text-white text-xl"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">${category.name}</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">${category.description || 'No description'}</p>
                        <div class="flex items-center space-x-2 mt-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full ${category.is_active ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}">
                                ${category.is_active ? 'Active' : 'Inactive'}
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">0 items</span>
                        </div>
                    </div>
                </div>
                <!-- Action Buttons - Centered relative to the entire card -->
                <div class="flex items-center justify-center space-x-2 mt-4">
                    <button onclick="editCategory(${category.id})" class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900 rounded-lg transition-all duration-200 text-sm font-medium" title="Edit Category">
                        <i class="fas fa-edit mr-1"></i>Edit
                    </button>
                    <button onclick="toggleCategoryStatus(${category.id})" class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-yellow-600 dark:hover:text-yellow-400 hover:bg-yellow-50 dark:hover:bg-yellow-900 rounded-lg transition-all duration-200 text-sm font-medium" title="Toggle Status">
                        <i class="fas fa-toggle-${category.is_active ? 'on' : 'off'} mr-1"></i>Toggle
                    </button>
                    <button onclick="showDeleteCategoryModal(${category.id})" class="px-3 py-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg transition-all duration-200 text-sm font-medium" title="Delete Category">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </div>
            </div>
        `).join('');
    }

    /**
     * Create new category
     */
    async function createCategory(formData) {
        try {
            const response = await fetch('/categories', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            // Check for session expiration
            if (handleSessionExpiration(response)) {
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success) {
                showNotification('Category created successfully!', 'success');
                hideCreateCategoryModal();
                loadCategories();
                resetCreateCategoryForm();
            } else {
                showNotification(data.message || 'Error creating category', 'error');
            }
        } catch (error) {
            console.error('Error creating category:', error);
            showNotification('Error creating category', 'error');
        }
    }

    /**
     * Update existing category
     */
    async function updateCategory(categoryId, formData) {
        try {
            const response = await fetch(`/categories/${categoryId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });
            
            // Check for session expiration
            if (handleSessionExpiration(response)) {
                return;
            }
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Category updated successfully!', 'success');
                hideCreateCategoryModal();
                loadCategories();
                resetCreateCategoryForm();
            } else {
                showNotification(data.message || 'Error updating category', 'error');
            }
        } catch (error) {
            console.error('Error updating category:', error);
            showNotification('Error updating category', 'error');
        }
    }

    /**
     * Edit category
     */
    function editCategory(categoryId) {
        const category = categories.find(c => c.id === categoryId);
        if (!category) return;

        currentCategory = category;
        
        // Populate form with category data
        document.getElementById('categoryName').value = category.name;
        document.getElementById('categoryDescription').value = category.description || '';
        document.getElementById('categoryColor').value = category.color || '#3B82F6';
        document.getElementById('categoryIcon').value = category.icon;
        document.getElementById('activeCategory').checked = category.is_active;
        
        // Update preview
        updatePreview();
        
        // Show create modal in edit mode
        showCreateCategoryModal();
        document.getElementById('createCategoryBtnText').textContent = 'Update Category';
    }

    /**
     * Toggle category status
     */
    async function toggleCategoryStatus(categoryId) {
        try {
            const response = await fetch(`/categories/${categoryId}/toggle-status`, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            // Check for session expiration
            if (handleSessionExpiration(response)) {
                return;
            }
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Category status updated!', 'success');
                loadCategories();
            } else {
                showNotification(data.message || 'Error updating category', 'error');
            }
        } catch (error) {
            console.error('Error toggling category status:', error);
            showNotification('Error updating category', 'error');
        }
    }

    /**
     * Delete category
     */
    /**
     * Show delete category modal
     */
    function showDeleteCategoryModal(categoryId) {
        const category = categories.find(c => c.id === categoryId);
        if (!category) return;

        // Populate modal with category data
        document.getElementById('deleteCategoryId').value = categoryId;
        document.getElementById('deleteCategoryName').textContent = category.name;
        document.getElementById('deleteCategoryItemCount').textContent = '0 items'; // You can calculate this if needed
        document.getElementById('deleteCategoryStatus').textContent = category.is_active ? 'Active' : 'Inactive';
        document.getElementById('deleteCategoryStatus').className = category.is_active ? 'text-green-400' : 'text-red-400';
        document.getElementById('deleteCategoryCreatedAt').textContent = 'Not available'; // You can format the date if needed
        
        // Show modal
        const modal = document.getElementById('deleteCategoryModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Hide delete category modal
     */
    function hideDeleteCategoryModal() {
        const modal = document.getElementById('deleteCategoryModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        // Reset confirmation input
        document.getElementById('deleteConfirmationInput').value = '';
        document.getElementById('deleteCategoryBtn').disabled = true;
    }

    /**
     * Handle delete confirmation input
     */
    function handleDeleteConfirmation() {
        const input = document.getElementById('deleteConfirmationInput');
        const deleteBtn = document.getElementById('deleteCategoryBtn');
        
        if (input.value === 'DELETE') {
            deleteBtn.disabled = false;
            deleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            deleteBtn.classList.add('hover:bg-red-700');
        } else {
            deleteBtn.disabled = true;
            deleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
            deleteBtn.classList.remove('hover:bg-red-700');
        }
    }

    /**
     * Delete category
     */
    async function deleteCategory(categoryId) {
        try {
            const response = await fetch(`/categories/${categoryId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            // Check for session expiration
            if (handleSessionExpiration(response)) {
                return;
            }
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Category deleted successfully!', 'success');
                hideDeleteCategoryModal();
                loadCategories();
            } else {
                showNotification(data.message || 'Error deleting category', 'error');
            }
        } catch (error) {
            console.error('Error deleting category:', error);
            showNotification('Error deleting category', 'error');
        }
    }

    /**
     * Reset create category form
     */
    function resetCreateCategoryForm() {
        document.getElementById('createCategoryForm').reset();
        document.getElementById('categoryColor').value = '#3B82F6';
        document.getElementById('categoryIcon').value = 'tag';
        document.getElementById('activeCategory').checked = true;
        currentCategory = null;
        document.getElementById('createCategoryBtnText').textContent = 'Create Category';
        updatePreview();
    }

    /**
     * Update preview section
     */
    function updatePreview() {
        const name = document.getElementById('categoryName').value || 'Category Name';
        const color = document.getElementById('categoryColor').value || '#3B82F6';
        const icon = document.getElementById('categoryIcon').value || 'tag';
        const isActive = document.getElementById('activeCategory').checked;

        document.getElementById('previewName').textContent = name;
        document.getElementById('previewIcon').style.backgroundColor = color;
        document.getElementById('previewIconClass').className = `fas fa-${icon} text-white text-xl`;
        document.getElementById('previewStatus').textContent = isActive ? 'Active' : 'Inactive';
        document.getElementById('previewStatus').className = `px-3 py-1 rounded-full text-xs font-semibold ${isActive ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'}`;
    }

    /**
     * Show notification
     */
    function showNotification(message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-[10000] px-6 py-4 rounded-lg shadow-lg transition-all duration-300 transform translate-x-full ${
            type === 'success' ? 'bg-green-500 text-white' :
            type === 'error' ? 'bg-red-500 text-white' :
            'bg-blue-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-${type === 'success' ? 'check' : type === 'error' ? 'times' : 'info'}-circle mr-3"></i>
                <span>${message}</span>
            </div>
        `;

        document.body.appendChild(notification);

        // Animate in
        setTimeout(() => {
            notification.classList.remove('translate-x-full');
        }, 100);

        // Remove after 3 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Event listeners
    document.addEventListener('DOMContentLoaded', function() {
        // Category form submission
        const createCategoryForm = document.getElementById('createCategoryForm');
        if (createCategoryForm) {
            createCategoryForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    name: document.getElementById('categoryName').value,
                    description: document.getElementById('categoryDescription').value,
                    color: document.getElementById('categoryColor').value,
                    icon: document.getElementById('categoryIcon').value,
                    is_active: document.getElementById('activeCategory').checked
                };

                if (currentCategory) {
                    // Update existing category
                    updateCategory(currentCategory.id, formData);
                } else {
                    // Create new category
                    createCategory(formData);
                }
            });
        }

        // Color picker
        const colorPicker = document.getElementById('colorPicker');
        if (colorPicker) {
            colorPicker.addEventListener('click', function() {
                // Simple color picker implementation
                const colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899', '#6B7280', '#F97316'];
                const currentColor = document.getElementById('categoryColor').value;
                const currentIndex = colors.indexOf(currentColor);
                const nextIndex = (currentIndex + 1) % colors.length;
                document.getElementById('categoryColor').value = colors[nextIndex];
                updatePreview();
            });
        }

        // Icon picker
        const iconPicker = document.getElementById('iconPicker');
        if (iconPicker) {
            iconPicker.addEventListener('click', function() {
                const icons = ['tag', 'pills', 'heart', 'shield-virus', 'fire', 'heartbeat', 'virus', 'stethoscope'];
                const currentIcon = document.getElementById('categoryIcon').value;
                const currentIndex = icons.indexOf(currentIcon);
                const nextIndex = (currentIndex + 1) % icons.length;
                document.getElementById('categoryIcon').value = icons[nextIndex];
                updatePreview();
            });
        }

        // Form field listeners for preview updates
        ['categoryName', 'categoryColor', 'categoryIcon', 'activeCategory'].forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.addEventListener('input', updatePreview);
                element.addEventListener('change', updatePreview);
            }
        });

        // Load categories when category modal is shown
        const categoryModal = document.getElementById('categoryModal');
        if (categoryModal) {
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                        if (!categoryModal.classList.contains('hidden')) {
                            loadCategories();
                        }
                    }
                });
            });
            observer.observe(categoryModal, { attributes: true });
        }
    });

    // Add event listeners for search and filter
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('categorySearch');
        const statusFilter = document.getElementById('categoryStatusFilter');
        
        if (searchInput) {
            searchInput.addEventListener('input', filterCategories);
        }
        
        if (statusFilter) {
            statusFilter.addEventListener('change', filterCategories);
        }
    });

    // Make functions globally available
    window.loadCategories = loadCategories;
    window.refreshCategories = refreshCategories;
    window.showCategoryStatistics = showCategoryStatistics;
    window.hideCategoryStatistics = hideCategoryStatistics;
    window.editCategory = editCategory;
    window.toggleCategoryStatus = toggleCategoryStatus;
    window.showDeleteCategoryModal = showDeleteCategoryModal;
    window.hideDeleteCategoryModal = hideDeleteCategoryModal;
    window.handleDeleteConfirmation = handleDeleteConfirmation;
    window.deleteCategory = deleteCategory;
    window.updateCategory = updateCategory;
    window.updatePreview = updatePreview;
    window.showNotification = showNotification;
    window.filterCategories = filterCategories;
    window.populateCategoryFilter = populateCategoryFilter;
    window.goToPage = goToPage;
    window.previousPage = previousPage;
    window.nextPage = nextPage;
    window.printInventory = printInventory;

    /**
     * Refresh medicines - reload from API and show notification
     */
    async function refreshMedicines() {
        try {
            // Show loading state
            const refreshBtn = document.querySelector('button[onclick="refreshMedicines()"]');
            if (refreshBtn) {
                const originalText = refreshBtn.innerHTML;
                refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-sm"></i>Refreshing...';
                refreshBtn.disabled = true;
                
                // Reload medicines
                await loadMedicines();
                
                // Restore button state
                refreshBtn.innerHTML = originalText;
                refreshBtn.disabled = false;
                
                // Show success notification
                showNotification('Medicines refreshed successfully!', 'success');
            }
        } catch (error) {
            console.error('Error refreshing medicines:', error);
            showNotification('Error refreshing medicines. Please try again.', 'error');
        }
    }

    /**
     * Load medicines from API with pagination
     */
    async function loadMedicines(page = 1) {
        try {
            const response = await fetch(`/medicines?page=${page}&per_page=12`, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            // Check for session expiration
            if (handleSessionExpiration(response)) {
                return;
            }
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            if (data.success) {
                medicines = data.data;
                pagination = data.pagination;
                currentPage = pagination.current_page;
                totalPages = pagination.last_page;
                
                renderMedicines();
                renderMedicinesTable();
                updateMedicineCount();
                populateBatchFilter();
                populateStockFilter();
                updatePaginationUI();
                // Update stats after loading medicines
                loadInventoryStats();
            } else {
                showNotification(data.message || 'Error loading medicines', 'error');
            }
        } catch (error) {
            console.error('Error loading medicines:', error);
            showNotification('Error loading medicines. Please refresh the page and try again.', 'error');
        }
    }

    /**
     * Render medicines in the table view
     */
    function renderMedicinesTable() {
        const tableBody = document.getElementById('inventoryTableBody');
        if (!tableBody) return;

        if (medicines.length === 0) {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="10" class="px-6 py-12 text-center">
                        <div class="text-center">
                            <i class="fas fa-pills text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Medicines Found</h3>
                            <p class="text-gray-500 dark:text-gray-500">Add your first medicine to get started</p>
                        </div>
                    </td>
                </tr>
            `;
            return;
        }

        tableBody.innerHTML = medicines.map(medicine => `
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg" style="background: linear-gradient(135deg, ${medicine.category?.color || '#3B82F6'}, ${medicine.category?.color || '#3B82F6'}CC);">
                            <i class="fas fa-pills text-white"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-bold text-gray-900 dark:text-white">${medicine.name}</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">${medicine.generic_name || 'No generic name'}</div>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold" style="background-color: ${medicine.category?.color || '#3B82F6'}20; color: ${medicine.category?.color || '#3B82F6'}">
                        ${medicine.category?.name || 'No Category'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 ${medicine.is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'} rounded-full text-xs font-semibold">
                        ${medicine.is_active ? 'Active' : 'Inactive'}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${medicine.strength} ${medicine.form}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${medicine.batch_number}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">${medicine.stock_quantity} ${medicine.unit}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 dark:text-white">Br ${parseFloat(medicine.selling_price).toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Br ${parseFloat(medicine.cost_price).toFixed(2)}</td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white"></td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex space-x-2">
                        <button onclick="viewMedicine(${medicine.id})" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300 transition-colors duration-200">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button onclick="editMedicine(${medicine.id})" class="text-green-600 hover:text-green-900 dark:text-green-400 dark:hover:text-green-300 transition-colors duration-200">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="deleteMedicine(${medicine.id})" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300 transition-colors duration-200">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    /**
     * Render medicines in the card view
     */
    function renderMedicines() {
        const container = document.getElementById('medicinesContainer');
        if (!container) return;

        if (medicines.length === 0) {
            container.innerHTML = `
                <div class="col-span-full text-center py-12">
                    <i class="fas fa-pills text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-400 mb-2">No Medicines Found</h3>
                    <p class="text-gray-500 dark:text-gray-500">Add your first medicine to get started</p>
                </div>
            `;
            return;
        }

        container.innerHTML = medicines.map(medicine => `
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-600 p-6 hover:shadow-xl transition-all duration-300" data-medicine-id="${medicine.id}">
                <div class="mb-4">
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-1">${medicine.name}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">${medicine.generic_name || 'No generic name'}</p>
                    <div class="flex gap-2 mb-4">
                        <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-semibold" style="background-color: ${medicine.category?.color || '#3B82F6'}20; color: ${medicine.category?.color || '#3B82F6'}">
                            ${medicine.category?.name || 'No Category'}
                        </span>
                        <span class="px-3 py-1 ${medicine.is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'} rounded-full text-xs font-semibold">
                            ${medicine.is_active ? 'Active' : 'Inactive'}
                        </span>
                    </div>
                </div>
                
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Strength & Form:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.strength} ${medicine.form}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Barcode:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.barcode}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Batch Number:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.batch_number}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Manufacturer:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.manufacturer}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Stock Quantity:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.stock_quantity} ${medicine.unit}</span>
                    </div>
                </div>
                
                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 mb-4">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Selling Price:</span>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">Br ${parseFloat(medicine.selling_price).toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Cost Price:</span>
                        <span class="text-sm font-semibold text-gray-900 dark:text-white">Br ${parseFloat(medicine.cost_price).toFixed(2)}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Prescription:</span>
                        <div class="text-right">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.prescription_required === 'yes' ? 'Required' : 'Not Required'}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">${medicine.is_active ? 'Active' : 'Inactive'}</div>
                        </div>
                    </div>
                </div>
                
                <div class="flex gap-2">
                    <button onclick="viewMedicine(${medicine.id})" class="flex-1 px-3 py-2 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-lg text-sm font-semibold hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-eye mr-2"></i>View
                    </button>
                    <button onclick="editMedicine(${medicine.id})" class="flex-1 px-3 py-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-lg text-sm font-semibold hover:bg-green-200 dark:hover:bg-green-800 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-edit mr-2"></i>Edit
                    </button>
                    <button onclick="deleteMedicine(${medicine.id})" class="flex-1 px-3 py-2 bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 rounded-lg text-sm font-semibold hover:bg-red-200 dark:hover:bg-red-800 transition-colors duration-200 flex items-center justify-center">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                </div>
            </div>
        `).join('');
    }


    /**
     * Update medicine count display
     */
    function updateMedicineCount() {
        const countElement = document.getElementById('medicineCount');
        if (countElement) {
            countElement.textContent = `Showing ${medicines.length} of ${medicines.length} medicines`;
        }
    }

    /**
     * View medicine details (optimized with client-side caching)
     */
    function viewMedicine(medicineId) {
        // Check client-side cache first
        if (window.medicineCache[medicineId]) {
            console.log('Loading from cache:', medicineId);
            window.performanceMetrics.cacheHits++;
            showViewMedicineModal(window.medicineCache[medicineId]);
            return;
        }
        
        // Try to find medicine in client-side array
        let medicine = medicines.find(m => m.id === medicineId);
        
        if (medicine) {
            // Cache the medicine data
            window.medicineCache[medicineId] = medicine;
            showViewMedicineModal(medicine);
            return;
        }
        
        // If not found, fetch from server and cache
        console.log('Fetching from server:', medicineId);
        window.performanceMetrics.serverRequests++;
        fetch(`/medicines/${medicineId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cache the result
                    window.medicineCache[medicineId] = data.data;
                    showViewMedicineModal(data.data);
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
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.strength} ${medicine.form}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Barcode:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.barcode}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Batch Number:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.batch_number }</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Manufacturer:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.manufacturer }</span>
                                </div>
                            </div>
                        </div>

                        <!-- Stock & Pricing -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Stock & Pricing</h4>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Stock Quantity:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">${medicine.stock_quantity } units</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Selling Price:</span>
                                    <span class="text-lg font-bold text-gray-900 dark:text-white">Br ${parseFloat(medicine.selling_price ).toFixed(2)}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Cost Price:</span>
                                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Br ${parseFloat(medicine.cost_price ).toFixed(2)}</span>
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
     * Edit medicine (optimized with client-side caching)
     */
    function editMedicine(medicineId) {
        // Check client-side cache first
        if (window.medicineCache[medicineId]) {
            console.log('Loading from cache for edit:', medicineId);
            populateEditForm(window.medicineCache[medicineId]);
            showEditMedicineModal();
            return;
        }
        
        // Try to find medicine in client-side array
        let medicine = medicines.find(m => m.id === medicineId);
        
        if (medicine) {
            // Cache the medicine data
            window.medicineCache[medicineId] = medicine;
            populateEditForm(medicine);
            showEditMedicineModal();
            return;
        }
        
        // If not found, fetch from server and cache
        console.log('Fetching from server for edit:', medicineId);
        fetch(`/medicines/${medicineId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Cache the result
                    window.medicineCache[medicineId] = data.data;
                    populateEditForm(data.data);
                    showEditMedicineModal();
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
     * Show edit medicine modal
     */
    function showEditMedicineModal() {
        const modal = document.getElementById('editMedicineModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Hide edit medicine modal
     */
    function hideEditMedicineModal() {
        const modal = document.getElementById('editMedicineModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
            resetEditMedicineForm();
        }
    }

    /**
     * Populate edit form with medicine data
     */
    function populateEditForm(medicine) {
        console.log('Medicine data for edit:', medicine); // Debug log
        // Set medicine ID
        document.getElementById('editMedicineId').value = medicine.id;
        
        // Basic information
        document.getElementById('editMedicineName').value = medicine.name || '';
        document.getElementById('editGenericName').value = medicine.generic_name || '';
        document.getElementById('editManufacturer').value = medicine.manufacturer || '';
        document.getElementById('editMedicineCategory').value = medicine.category_id || '';
        document.getElementById('editMedicineStrength').value = medicine.strength || '';
        document.getElementById('editMedicineFormField').value = medicine.form || '';
        document.getElementById('editMedicineUnit').value = medicine.unit || '';
        document.getElementById('editBarcode').value = medicine.barcode || '';
        
        // Pricing and stock
        document.getElementById('editSellingPrice').value = medicine.selling_price ;
        document.getElementById('editCostPrice').value = medicine.cost_price ;
        document.getElementById('editStockQuantity').value = medicine.stock_quantity ;
        
        // Set initial reorder level (use existing value or calculate 10% of stock)
        const stockQuantity = parseInt(medicine.stock_quantity) ;
        const reorderLevel = medicine.reorder_level || Math.ceil(stockQuantity * 0.1);
        document.getElementById('editReorderLevel').value = reorderLevel;
        // Format expiry date for HTML date input (YYYY-MM-DD)
        const expiryDate = medicine.expiry_date ? new Date(medicine.expiry_date).toISOString().split('T')[0] : '';
        document.getElementById('editExpiryDate').value = expiryDate;
        document.getElementById('editBatchNumber').value = medicine.batch_number || '';
        document.getElementById('editPrescriptionRequired').value = medicine.prescription_required || 'no';
        document.getElementById('editActiveMedicine').checked = medicine.is_active || false;
        document.getElementById('editMedicineDescription').value = medicine.description || '';
    }

    /**
     * Reset edit medicine form
     */
    function resetEditMedicineForm() {
        document.getElementById('editMedicineForm').reset();
        document.getElementById('editMedicineId').value = '';
    }

    /**
     * Show delete medicine modal
     */
    function deleteMedicine(medicineId) {
        // Try to find medicine in client-side array first
        let medicine = medicines.find(m => m.id === medicineId);
        
        if (medicine) {
            showDeleteMedicineModal(medicine);
            return;
        }
        
        // If not found, fetch from server
        fetch(`/medicines/${medicineId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showDeleteMedicineModal(data.data);
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
     * Show delete medicine modal
     */
    function showDeleteMedicineModal(medicine) {
        // Populate modal with medicine data
        document.getElementById('deleteMedicineName').textContent = medicine.name;
        document.getElementById('deleteMedicineGeneric').textContent = medicine.generic_name || 'N/A';
        document.getElementById('deleteMedicineCategory').textContent = medicine.category?.name || 'N/A';
        document.getElementById('deleteMedicineStock').textContent = medicine.stock_quantity + ' ' + (medicine.unit || 'units');
        document.getElementById('deleteMedicineStatus').textContent = medicine.is_active ? 'Active' : 'Inactive';
        document.getElementById('deleteMedicineStatus').className = medicine.is_active ? 'text-green-400' : 'text-red-400';
        
        // Store medicine ID for deletion
        window.currentDeleteMedicineId = medicine.id;
        
        // Show modal
        const modal = document.getElementById('deleteMedicineModal');
        if (modal) {
            modal.classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Hide delete medicine modal
     */
    function hideDeleteMedicineModal() {
        const modal = document.getElementById('deleteMedicineModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        // Reset confirmation input
        document.getElementById('deleteMedicineConfirmationInput').value = '';
        document.getElementById('deleteMedicineBtn').disabled = true;
        window.currentDeleteMedicineId = null;
    }

    /**
     * Handle delete medicine confirmation input
     */
    function handleDeleteMedicineConfirmation() {
        const input = document.getElementById('deleteMedicineConfirmationInput');
        const deleteBtn = document.getElementById('deleteMedicineBtn');
        
        if (input.value === 'DELETE') {
            deleteBtn.disabled = false;
            deleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            deleteBtn.classList.add('hover:bg-red-700');
        } else {
            deleteBtn.disabled = true;
            deleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
            deleteBtn.classList.remove('hover:bg-red-700');
        }
    }

    /**
     * Confirm delete medicine
     */
    async function confirmDeleteMedicine() {
        if (!window.currentDeleteMedicineId) {
            showNotification('No medicine selected for deletion', 'error');
            return;
        }

        try {
            const response = await fetch(`/medicines/${window.currentDeleteMedicineId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            // Check for session expiration
            if (handleSessionExpiration(response)) {
                return;
            }
            
            const data = await response.json();
            
            if (data.success) {
                showNotification('Medicine deleted successfully!', 'success');
                hideDeleteMedicineModal();
                
                // Clear client-side cache for this medicine
                if (window.medicineCache && window.medicineCache[window.currentDeleteMedicineId]) {
                    delete window.medicineCache[window.currentDeleteMedicineId];
                    console.log('Cleared client-side cache for deleted medicine:', window.currentDeleteMedicineId);
                }
                
                // Reload the page to refresh data
                location.reload();
            } else {
                showNotification(data.message || 'Error deleting medicine', 'error');
            }
        } catch (error) {
            console.error('Error deleting medicine:', error);
            showNotification('Error deleting medicine', 'error');
        }
    }

    /**
     * Create new medicine
     */
    async function createMedicine(formData) {
        try {
            const response = await fetch('/medicines', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            // Check for session expiration
            if (handleSessionExpiration(response)) {
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showNotification('Medicine created successfully!', 'success');
                hideAddMedicineModal();
                // Reload medicines and refresh the page
                location.reload();
            } else {
                showNotification(data.message || 'Error creating medicine', 'error');
            }
        } catch (error) {
            console.error('Error creating medicine:', error);
            showNotification('Error creating medicine. Please try again.', 'error');
        }
    }

    /**
     * Update existing medicine
     */
    async function updateMedicine(medicineId, formData) {
        try {
            const response = await fetch(`/medicines/${medicineId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(formData)
            });

            // Check for session expiration
            if (handleSessionExpiration(response)) {
                return;
            }

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            if (data.success) {
                showNotification('Medicine updated successfully!', 'success');
                hideEditMedicineModal();
                
                // Clear client-side cache for this medicine
                const medicineId = document.getElementById('editMedicineId').value;
                if (window.medicineCache && window.medicineCache[medicineId]) {
                    delete window.medicineCache[medicineId];
                    console.log('Cleared client-side cache for medicine:', medicineId);
                }
                
                // Reload medicines and refresh the page
                location.reload();
            } else {
                showNotification(data.message || 'Error updating medicine', 'error');
            }
        } catch (error) {
            console.error('Error updating medicine:', error);
            showNotification('Error updating medicine. Please try again.', 'error');
        }
    }

    /**
     * Reset add medicine form
     */
    function resetAddMedicineForm() {
        document.getElementById('addMedicineForm').reset();
    }

    // Batch Add Medicines Variables
    let batchMedicines = [];
    
    /**
     * Generate Unique Barcode
     */
    function generateUniqueBarcode() {
        const timestamp = Date.now();
        const random = Math.floor(Math.random() * 1000);
        return `1234567890${timestamp.toString().slice(-3)}${random.toString().padStart(3, '0')}`;
    }
    
    // Generate unique barcodes to prevent duplicates
    const timestamp = Date.now();
    const barcode1 = `1234567890${timestamp.toString().slice(-3)}`;
    const barcode2 = `1234567890${(timestamp + 1).toString().slice(-3)}`;
    
    let csvTemplate = `name,generic_name,manufacturer,category_id,strength,form,unit,barcode,selling_price,cost_price,stock_quantity,reorder_level,expiry_date,batch_number,prescription_required,is_active,description`;

    /**
     * Show CSV Template
     */
    function showCSVTemplate() {
        const csvDataInput = document.getElementById('csvDataInput');
        if (csvDataInput) {
            csvDataInput.value = csvTemplate;
            showNotification('CSV template loaded! You can edit the data as needed.', 'success');
        }
    }

    /**
     * Download CSV Template
     */
    function downloadCSVTemplate() {
        const blob = new Blob([csvTemplate], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'medicine_template.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        showNotification('CSV template downloaded successfully!', 'success');
    }

    /**
     * Process CSV Data
     */
    function processCSVData() {
        const csvData = document.getElementById('csvDataInput').value.trim();
        
        if (!csvData) {
            showNotification('Please paste CSV data first.', 'error');
            return;
        }

        try {
            const lines = csvData.split('\n');
            const headers = lines[0].split(',').map(h => h.trim());
            
            // Validate headers
            const requiredHeaders = ['name', 'generic_name', 'category_id', 'strength', 'form', 'unit', 'selling_price', 'cost_price', 'stock_quantity', 'expiry_date', 'batch_number'];
            const missingHeaders = requiredHeaders.filter(h => !headers.includes(h));
            
            if (missingHeaders.length > 0) {
                showNotification(`Missing required columns: ${missingHeaders.join(', ')}`, 'error');
                return;
            }

            batchMedicines = [];
            
            for (let i = 1; i < lines.length; i++) {
                const line = lines[i].trim();
                if (!line) continue;
                
                const values = line.split(',').map(v => v.trim());
                if (values.length !== headers.length) {
                    showNotification(`Row ${i + 1}: Column count mismatch`, 'error');
                    return;
                }
                
                const medicine = {};
                headers.forEach((header, index) => {
                    let value = values[index];
                    
                    // Handle boolean values
                    if (header === 'is_active') {
                        value = value.toLowerCase() === 'true' || value === '1';
                    }
                    
                    // Handle numeric values
                    if (['category_id', 'stock_quantity', 'reorder_level', 'selling_price', 'cost_price'].includes(header)) {
                        value = value ? parseFloat(value) : (header === 'category_id' ? 1 : 0);
                    }
                    
                    medicine[header] = value;
                });
                
                // Set defaults for optional fields
                medicine.manufacturer = medicine.manufacturer || '';
                medicine.barcode = medicine.barcode || generateUniqueBarcode();
                medicine.reorder_level = medicine.reorder_level || Math.ceil(medicine.stock_quantity * 0.1);
                medicine.prescription_required = medicine.prescription_required || 'no';
                medicine.is_active = medicine.is_active !== false;
                medicine.description = medicine.description || '';
                
                // Remove unit field if present (not used in our system)
                if (medicine.unit) {
                    delete medicine.unit;
                }
                
                
                batchMedicines.push(medicine);
            }
            
            // Update UI
            document.getElementById('medicineCount').textContent = `${batchMedicines.length} medicines ready to add`;
            document.getElementById('addMedicinesBtn').disabled = false;
            document.getElementById('addMedicinesBtn').classList.remove('opacity-50', 'cursor-not-allowed');
            
            // Enable clear button
            const clearBtn = document.getElementById('clearDataBtn');
            if (clearBtn) {
                clearBtn.disabled = false;
                clearBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            
            // Show preview
            showMedicinesPreview();
            
            showNotification(`Successfully processed ${batchMedicines.length} medicines!`, 'success');
            
        } catch (error) {
            console.error('Error processing CSV:', error);
            showNotification('Error processing CSV data. Please check the format.', 'error');
        }
    }

    /**
     * Show Medicines Preview
     */
    function showMedicinesPreview() {
        const previewContainer = document.getElementById('processedMedicinesPreview');
        const medicinesList = document.getElementById('medicinesPreviewList');
        
        if (!previewContainer || !medicinesList || batchMedicines.length === 0) {
            return;
        }
        
        // Show the preview section
        previewContainer.classList.remove('hidden');
        
        // Generate preview HTML
        let previewHTML = '';
        batchMedicines.forEach((medicine, index) => {
            previewHTML += `
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h4 class="text-lg font-bold text-gray-900 dark:text-white">${medicine.name}</h4>
                                <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 rounded-full text-xs font-semibold">
                                    ${medicine.strength} ${medicine.form}
                                </span>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Generic:</span>
                                    <span class="text-gray-900 dark:text-white font-medium">${medicine.generic_name}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Manufacturer:</span>
                                    <span class="text-gray-900 dark:text-white font-medium">${medicine.manufacturer }</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Stock:</span>
                                    <span class="text-gray-900 dark:text-white font-medium">${medicine.stock_quantity} units</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Price:</span>
                                    <span class="text-gray-900 dark:text-white font-medium">Br ${parseFloat(medicine.selling_price).toFixed(2)}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Batch:</span>
                                    <span class="text-gray-900 dark:text-white font-medium">${medicine.batch_number}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Expiry:</span>
                                    <span class="text-gray-900 dark:text-white font-medium">${medicine.expiry_date}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Prescription:</span>
                                    <span class="text-gray-900 dark:text-white font-medium">${medicine.prescription_required === 'yes' ? 'Required' : 'Not Required'}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600 dark:text-gray-400">Status:</span>
                                    <span class="px-2 py-1 ${medicine.is_active ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200'} rounded-full text-xs font-semibold">
                                        ${medicine.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                            </div>
                            ${medicine.description ? `
                                <div class="mt-2">
                                    <span class="text-gray-600 dark:text-gray-400 text-sm">Description:</span>
                                    <p class="text-gray-900 dark:text-white text-sm">${medicine.description}</p>
                                </div>
                            ` : ''}
                        </div>
                        <div class="ml-4">
                            <span class="text-xs text-gray-500 dark:text-gray-400">#${index + 1}</span>
                        </div>
                    </div>
                </div>
            `;
        });
        
        medicinesList.innerHTML = previewHTML;
    }

    /**
     * Add Batch Medicines
     */
    async function addBatchMedicines() {
        if (batchMedicines.length === 0) {
            showNotification('No medicines to add. Please process CSV data first.', 'error');
            return;
        }

        const addBtn = document.getElementById('addMedicinesBtn');
        const originalText = addBtn.innerHTML;
        
        try {
            addBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
            addBtn.disabled = true;
            
            let successCount = 0;
            let errorCount = 0;
            
            for (const medicine of batchMedicines) {
                try {
                    const response = await fetch('/medicines', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(medicine)
                    });
                    
                    if (response.ok) {
                        const result = await response.json();
                        successCount++;
                    } else {
                        const errorText = await response.text();
                        
                        // Parse error response to check for duplicate barcode
                        try {
                            const errorData = JSON.parse(errorText);
                            if (errorData.message && errorData.message.includes('Duplicate entry') && errorData.message.includes('barcode')) {
                                // Don't count as error, just skip
                                continue;
                            }
                        } catch (e) {
                            // If we can't parse the error, count it as an error
                        }
                        
                        errorCount++;
                    }
                } catch (error) {
                    errorCount++;
                }
            }
            
            // Show results
            if (successCount > 0) {
                showNotification(`Successfully added ${successCount} medicines!`, 'success');
                hideBatchAddModal();
                loadMedicines(); // Refresh the medicines list
            }
            
            if (errorCount > 0) {
                showNotification(`${errorCount} medicines failed to add. Please check the data.`, 'error');
            }
            
            // Show summary if there were duplicates
            const totalProcessed = successCount + errorCount;
            const skipped = batchMedicines.length - totalProcessed;
            if (skipped > 0) {
                showNotification(`${skipped} medicines were skipped (duplicate barcodes already exist).`, 'info');
            }
            
        } catch (error) {
            console.error('Error adding batch medicines:', error);
            showNotification('Error adding medicines. Please try again.', 'error');
        } finally {
            addBtn.innerHTML = originalText;
            addBtn.disabled = false;
        }
    }

    // Make medicine functions globally available
    window.loadMedicines = loadMedicines;
    window.refreshMedicines = refreshMedicines;
    window.renderMedicinesTable = renderMedicinesTable;
    window.viewMedicine = viewMedicine;
    window.showViewMedicineModal = showViewMedicineModal;
    window.hideViewMedicineModal = hideViewMedicineModal;
    window.editMedicine = editMedicine;
    window.showEditMedicineModal = showEditMedicineModal;
    window.hideEditMedicineModal = hideEditMedicineModal;
    window.populateEditForm = populateEditForm;
    window.resetEditMedicineForm = resetEditMedicineForm;
    window.updateMedicine = updateMedicine;
    window.deleteMedicine = deleteMedicine;
    window.createMedicine = createMedicine;
    window.resetAddMedicineForm = resetAddMedicineForm;
    window.showCSVTemplate = showCSVTemplate;
    window.downloadCSVTemplate = downloadCSVTemplate;
    window.processCSVData = processCSVData;
    window.addBatchMedicines = addBatchMedicines;
    window.showMedicinesPreview = showMedicinesPreview;
    window.resetBatchAddForm = resetBatchAddForm;
    window.clearBatchData = clearBatchData;
    window.generateUniqueBarcode = generateUniqueBarcode;
    window.showAnalyticsModal = showAnalyticsModal;
    window.hideAnalyticsModal = hideAnalyticsModal;
    window.exportAnalytics = exportAnalytics;

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
     * Load Enhanced Business Intelligence Analytics
     */
    async function loadAnalyticsData() {
        try {
            // Show loading state
            document.getElementById('analyticsLoading').classList.remove('hidden');
            document.getElementById('analyticsData').classList.add('hidden');

            // Fetch business intelligence data
            const response = await fetch('/analytics/business-intelligence', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch business intelligence data');
            }

            const result = await response.json();
            const analytics = result.data || {};

            // Update UI with enhanced analytics
            updateBusinessIntelligenceUI(analytics);

            // Hide loading, show data
            document.getElementById('analyticsLoading').classList.add('hidden');
            document.getElementById('analyticsData').classList.remove('hidden');

        } catch (error) {
            console.error('Error loading business intelligence:', error);
            showNotification('Error loading business intelligence data', 'error');
        }
    }

    /**
     * Calculate Analytics
     */
    function calculateAnalytics(medicines) {
        const totalMedicines = medicines.length;
        const activeMedicines = medicines.filter(m => m.is_active).length;
        const totalValue = medicines.reduce((sum, m) => sum + (parseFloat(m.selling_price ) * parseInt(m.stock_quantity )), 0);
        const lowStockCount = medicines.filter(m => parseInt(m.stock_quantity ) <= 10 && parseInt(m.stock_quantity ) > 0).length;

        // Category distribution
        const categoryStats = {};
        medicines.forEach(medicine => {
            const categoryName = medicine.category?.name || 'Uncategorized';
            categoryStats[categoryName] = (categoryStats[categoryName] ) + 1;
        });

        // Stock status
        const stockStats = {
            inStock: medicines.filter(m => parseInt(m.stock_quantity ) > 10).length,
            lowStock: medicines.filter(m => parseInt(m.stock_quantity ) <= 10 && parseInt(m.stock_quantity ) > 0).length,
            outOfStock: medicines.filter(m => parseInt(m.stock_quantity ) <= 0).length
        };

        // Top categories
        const topCategories = Object.entries(categoryStats)
            .sort(([,a], [,b]) => b - a)
            .slice(0, 5);

        // Expiring soon (within 30 days)
        const expiringMedicines = medicines.filter(m => {
            if (!m.expiry_date) return false;
            const expiryDate = new Date(m.expiry_date);
            const today = new Date();
            const diffTime = expiryDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return diffDays <= 30 && diffDays > 0;
        }).slice(0, 5);

        // Low stock items
        const lowStockItems = medicines.filter(m => parseInt(m.stock_quantity ) <= 10 && parseInt(m.stock_quantity ) > 0).slice(0, 5);

        // Price analysis
        const validPrices = medicines.filter(m => parseFloat(m.selling_price ) > 0 && parseFloat(m.cost_price ) > 0);
        const avgSellingPrice = validPrices.length > 0 ? validPrices.reduce((sum, m) => sum + parseFloat(m.selling_price), 0) / validPrices.length : 0;
        const avgCostPrice = validPrices.length > 0 ? validPrices.reduce((sum, m) => sum + parseFloat(m.cost_price), 0) / validPrices.length : 0;
        const avgProfitMargin = avgSellingPrice > 0 && avgCostPrice > 0 ? ((avgSellingPrice - avgCostPrice) / avgSellingPrice) * 100 : 0;

        return {
            totalMedicines,
            activeMedicines,
            totalValue,
            lowStockCount,
            categoryStats,
            stockStats,
            topCategories,
            expiringMedicines,
            lowStockItems,
            avgSellingPrice,
            avgCostPrice,
            avgProfitMargin
        };
    }

    /**
     * Update Analytics UI
     */
    function updateAnalyticsUI(analytics) {
        // Update overview cards
        document.getElementById('totalMedicinesCount').textContent = analytics.totalMedicines;
        document.getElementById('activeMedicinesCount').textContent = analytics.activeMedicines;
        document.getElementById('totalValueAmount').textContent = `Br ${analytics.totalValue.toFixed(2)}`;
        document.getElementById('lowStockCount').textContent = analytics.lowStockCount;

        // Update price analysis
        document.getElementById('avgSellingPrice').textContent = `Br ${analytics.avgSellingPrice.toFixed(2)}`;
        document.getElementById('avgCostPrice').textContent = `Br ${analytics.avgCostPrice.toFixed(2)}`;
        document.getElementById('avgProfitMargin').textContent = `${analytics.avgProfitMargin.toFixed(1)}%`;

        // Update top categories
        const topCategoriesContainer = document.getElementById('topCategories');
        topCategoriesContainer.innerHTML = analytics.topCategories.map(([name, count]) => `
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <span class="text-sm font-medium text-gray-900 dark:text-white">${name}</span>
                <span class="text-sm font-bold text-blue-600 dark:text-blue-400">${count}</span>
            </div>
        `).join('');

        // Update expiring medicines
        const expiringContainer = document.getElementById('expiringMedicines');
        expiringContainer.innerHTML = analytics.expiringMedicines.map(medicine => {
            const expiryDate = new Date(medicine.expiry_date);
            const today = new Date();
            const diffTime = expiryDate - today;
            const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
            return `
                <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900 rounded-lg">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">${medicine.name}</p>
                        <p class="text-xs text-gray-600 dark:text-gray-400">${diffDays} days left</p>
                    </div>
                    <span class="text-xs text-orange-600 dark:text-orange-400 font-semibold">${medicine.expiry_date}</span>
                </div>
            `;
        }).join('');

        // Update low stock items
        const lowStockContainer = document.getElementById('lowStockItems');
        lowStockContainer.innerHTML = analytics.lowStockItems.map(medicine => `
            <div class="flex items-center justify-between p-3 bg-red-50 dark:bg-red-900 rounded-lg">
                <div>
                    <p class="text-sm font-medium text-gray-900 dark:text-white">${medicine.name}</p>
                    <p class="text-xs text-gray-600 dark:text-gray-400">${medicine.category?.name || 'Uncategorized'}</p>
                </div>
                <span class="text-xs text-red-600 dark:text-red-400 font-semibold">${medicine.stock_quantity} units</span>
            </div>
        `).join('');

        // Create charts
        createCategoryChart(analytics.categoryStats);
        createStockChart(analytics.stockStats);
    }

    /**
     * Update Enhanced Business Intelligence UI
     */
    function updateBusinessIntelligenceUI(analytics) {
        const { overview, financial, category_performance, predictive, recommendations, performance } = analytics;

        // Update overview cards with insights
        updateOverviewCards(overview, financial, performance);
        
        // Update financial intelligence
        updateFinancialIntelligence(financial);
        
        // Update predictive analytics
        updatePredictiveAnalytics(predictive);
        
        // Update performance indicators
        updatePerformanceIndicators(performance);
        
        // Update AI recommendations
        updateAIRecommendations(recommendations);
        
        // Update category performance
        updateCategoryPerformance(category_performance);
        
        // Update reorder recommendations
        updateReorderRecommendations(predictive.reorder_recommendations);
        
        // Create advanced charts with error handling
        try {
            createRevenueChart(financial);
        } catch (error) {
            console.warn('Error creating revenue chart:', error);
        }
        
        try {
            createCategoryDistributionChart(category_performance);
        } catch (error) {
            console.warn('Error creating category chart:', error);
        }
    }

    /**
     * Update Overview Cards with Business Insights
     */
    function updateOverviewCards(overview, financial, performance) {
        // Total Inventory Value
        const totalValue = overview.total_inventory_value || 0;
        document.getElementById('totalInventoryValue').textContent = `Br ${totalValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        document.getElementById('valueInsight').textContent = totalValue > 100000 ? 'Excellent inventory value!' : 'Good inventory value';

        // Stock Health Score
        const stockHealth = overview.stock_health_percentage || 0;
        document.getElementById('stockHealthScore').textContent = `${stockHealth}%`;
        document.getElementById('stockInsight').textContent = stockHealth > 80 ? 'Healthy stock levels' : 'Some stock issues detected';

        // Profit Margin
        const profitMargin = financial.avg_profit_margin || 0;
        document.getElementById('profitMargin').textContent = `${profitMargin}%`;
        document.getElementById('profitInsight').textContent = profitMargin > 20 ? 'Excellent profit margins!' : 'Consider optimizing pricing';

        // Growth Potential
        const growthPotential = performance.growth_potential || 'low';
        document.getElementById('growthPotential').textContent = growthPotential.charAt(0).toUpperCase() + growthPotential.slice(1);
        document.getElementById('growthInsight').textContent = growthPotential === 'high' ? 'High growth potential!' : 'Room for growth';
    }

    /**
     * Update Financial Intelligence Section
     */
    function updateFinancialIntelligence(financial) {
        document.getElementById('revenuePotential').textContent = `Br ${(financial.total_revenue_potential || 0).toLocaleString()}`;
        document.getElementById('costValue').textContent = `Br ${(financial.total_cost_value || 0).toLocaleString()}`;
        document.getElementById('grossProfit').textContent = `Br ${(financial.gross_profit_potential || 0).toLocaleString()}`;
        
        // Update profit margin bar
        const profitMargin = financial.avg_profit_margin || 0;
        document.getElementById('profitMarginBar').style.width = `${Math.min(profitMargin, 100)}%`;
    }

    /**
     * Update Predictive Analytics Section
     */
    function updatePredictiveAnalytics(predictive) {
        const expiry = predictive.expiry_predictions || {};
        document.getElementById('expiring7Days').textContent = expiry.expiring_7_days || 0;
        document.getElementById('expiring30Days').textContent = expiry.expiring_30_days || 0;
        document.getElementById('riskLevel').textContent = expiry.risk_level || 'Low';
        
        // Update risk bar based on risk level
        const riskPercentage = expiry.risk_level === 'critical' ? 100 : 
                              expiry.risk_level === 'high' ? 75 : 
                              expiry.risk_level === 'medium' ? 50 : 25;
        document.getElementById('riskBar').style.width = `${riskPercentage}%`;
    }

    /**
     * Update Performance Indicators
     */
    function updatePerformanceIndicators(performance) {
        document.getElementById('efficiencyScore').textContent = `${performance.efficiency_score || 0}%`;
        document.getElementById('turnoverRate').textContent = `${performance.inventory_turnover_rate || 0}x`;
        document.getElementById('growthScore').textContent = performance.growth_potential || 'Low';
        
        // Update efficiency bar
        const efficiency = performance.efficiency_score || 0;
        document.getElementById('efficiencyBar').style.width = `${efficiency}%`;
    }

    /**
     * Update AI Recommendations
     */
    function updateAIRecommendations(recommendations) {
        const container = document.getElementById('aiRecommendations');
        
        if (!recommendations || recommendations.length === 0) {
            container.innerHTML = `
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">All Good!</h3>
                    <p class="text-gray-600 dark:text-gray-400">No urgent recommendations at this time</p>
                </div>
            `;
            return;
        }

        container.innerHTML = recommendations.map(rec => `
            <div class="flex items-start p-4 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-sm">
                <div class="flex-shrink-0 mr-4">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center ${
                        rec.priority === 'high' ? 'bg-red-100 text-red-600' :
                        rec.priority === 'medium' ? 'bg-orange-100 text-orange-600' :
                        'bg-blue-100 text-blue-600'
                    }">
                        <i class="fas fa-${rec.type === 'stock_optimization' ? 'boxes' : 
                                         rec.type === 'profit_optimization' ? 'chart-line' :
                                         rec.type === 'expiry_management' ? 'clock' : 'lightbulb'} text-sm"></i>
                    </div>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-2">
                        <h4 class="text-sm font-semibold text-gray-900 dark:text-white">${rec.title}</h4>
                        <span class="px-2 py-1 text-xs font-medium rounded-full ${
                            rec.priority === 'high' ? 'bg-red-100 text-red-800' :
                            rec.priority === 'medium' ? 'bg-orange-100 text-orange-800' :
                            'bg-blue-100 text-blue-800'
                        }">${rec.priority}</span>
                    </div>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">${rec.description}</p>
                    <div class="flex items-center justify-between">
                        <span class="text-xs text-gray-500">${rec.action}</span>
                        <span class="text-xs font-medium text-green-600">${rec.impact}</span>
                    </div>
                </div>
            </div>
        `).join('');
    }

    /**
     * Update Category Performance
     */
    function updateCategoryPerformance(categoryPerformance) {
        const container = document.getElementById('categoryPerformance');
        const categories = categoryPerformance.categories || [];
        
        if (categories.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-4">No category data available</p>';
            return;
        }
        
        container.innerHTML = categories.slice(0, 5).map(category => `
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                <div class="flex items-center">
                    <div class="w-3 h-3 rounded-full mr-3" style="background-color: ${category.color}"></div>
                    <span class="font-medium text-gray-900 dark:text-white">${category.name}</span>
                </div>
                <div class="text-right">
                    <div class="text-sm font-bold text-blue-600">${category.medicine_count} items</div>
                    <div class="text-xs text-gray-500">Br ${category.category_value.toLocaleString()}</div>
                    <div class="text-xs text-green-600">${category.profit_margin}% margin</div>
                </div>
            </div>
        `).join('');
    }

    /**
     * Update Reorder Recommendations
     */
    function updateReorderRecommendations(recommendations) {
        const container = document.getElementById('reorderRecommendations');
        
        if (!recommendations || recommendations.length === 0) {
            container.innerHTML = '<p class="text-gray-500 text-center py-4">No reorder recommendations</p>';
            return;
        }
        
        container.innerHTML = recommendations.map(rec => `
            <div class="flex items-center justify-between p-3 bg-orange-50 dark:bg-orange-900 rounded-lg border border-orange-200 dark:border-orange-700">
                <div class="flex-1">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white">${rec.name}</h4>
                    <p class="text-xs text-gray-600 dark:text-gray-400">Current: ${rec.current_stock} | Reorder at: ${rec.reorder_level}</p>
                </div>
                <div class="text-right">
                    <span class="px-2 py-1 text-xs font-medium rounded-full ${
                        rec.urgency === 'high' ? 'bg-red-100 text-red-800' : 'bg-orange-100 text-orange-800'
                    }">${rec.urgency}</span>
                    <div class="text-xs text-gray-500 mt-1">Br ${rec.selling_price.toFixed(2)}</div>
                </div>
            </div>
        `).join('');
    }

    /**
     * Create Revenue Chart
     */
    function createRevenueChart(financial) {
        const ctx = document.getElementById('revenueChartCanvas');
        if (!ctx) return;
        
        // Check if Chart.js is available
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded, showing fallback content');
            ctx.parentElement.innerHTML = `
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-pie text-blue-600 dark:text-blue-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Revenue Analysis</h3>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Revenue Potential:</span>
                                <span class="font-semibold text-green-600">Br ${(financial.total_revenue_potential || 0).toLocaleString()}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Cost Value:</span>
                                <span class="font-semibold text-orange-600">Br ${(financial.total_cost_value || 0).toLocaleString()}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-gray-400">Gross Profit:</span>
                                <span class="font-semibold text-blue-600">Br ${(financial.gross_profit_potential || 0).toLocaleString()}</span>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            return;
        }
        
        // Simple revenue visualization
        const revenueData = {
            labels: ['Revenue Potential', 'Cost Value', 'Gross Profit'],
            datasets: [{
                data: [
                    financial.total_revenue_potential || 0,
                    financial.total_cost_value || 0,
                    financial.gross_profit_potential || 0
                ],
                backgroundColor: ['#10B981', '#F59E0B', '#3B82F6'],
                borderWidth: 0
            }]
        };
        
        new Chart(ctx, {
            type: 'doughnut',
            data: revenueData,
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

    /**
     * Create Category Distribution Chart
     */
    function createCategoryDistributionChart(categoryPerformance) {
        const ctx = document.getElementById('categoryChartCanvas');
        if (!ctx) return;
        
        const categories = categoryPerformance.categories || [];
        
        // Check if Chart.js is available
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js not loaded, showing fallback content');
            ctx.parentElement.innerHTML = `
                <div class="flex items-center justify-center h-full">
                    <div class="text-center">
                        <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-chart-pie text-purple-600 dark:text-purple-400 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Category Distribution</h3>
                        <div class="space-y-2 text-sm max-h-60 overflow-y-auto">
                            ${categories.slice(0, 5).map(category => `
                                <div class="flex items-center justify-between p-2 bg-gray-50 dark:bg-gray-700 rounded">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-2" style="background-color: ${category.color}"></div>
                                        <span class="text-gray-900 dark:text-white">${category.name}</span>
                                    </div>
                                    <span class="font-semibold text-blue-600">Br ${category.category_value.toLocaleString()}</span>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            `;
            return;
        }
        
        const chartData = {
            labels: categories.map(cat => cat.name),
            datasets: [{
                data: categories.map(cat => cat.category_value),
                backgroundColor: categories.map(cat => cat.color),
                borderWidth: 0
            }]
        };
        
        new Chart(ctx, {
            type: 'pie',
            data: chartData,
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

    /**
     * Refresh Analytics Data
     */
    function refreshAnalytics() {
        loadAnalyticsData();
    }

    /**
     * Clear Analytics Cache
     */
    async function clearAnalyticsCache() {
        try {
            const response = await fetch('/analytics/clear-cache', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            if (response.ok) {
                showNotification('Analytics cache cleared successfully', 'success');
                loadAnalyticsData();
            } else {
                showNotification('Failed to clear analytics cache', 'error');
            }
        } catch (error) {
            console.error('Error clearing analytics cache:', error);
            showNotification('Error clearing analytics cache', 'error');
        }
    }

    // Expose analytics functions to global scope
    window.clearAnalyticsCache = clearAnalyticsCache;
    window.refreshAnalytics = refreshAnalytics;
    window.loadAnalyticsData = loadAnalyticsData;
    window.showAnalyticsModal = showAnalyticsModal;
    window.hideAnalyticsModal = hideAnalyticsModal;

    /**
     * Create Category Chart
     */
    function createCategoryChart(categoryStats) {
        const canvas = document.getElementById('categoryChartCanvas');
        const ctx = canvas.getContext('2d');
        
        // Clear previous chart
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        const categories = Object.keys(categoryStats);
        const values = Object.values(categoryStats);
        const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#06B6D4', '#84CC16'];
        
        if (categories.length === 0) {
            ctx.fillStyle = '#6B7280';
            ctx.font = '16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('No data available', canvas.width / 2, canvas.height / 2);
            return;
        }

        // Sort categories by value for better display
        const sortedData = categories.map((cat, index) => ({
            category: cat,
            value: values[index],
            color: colors[index % colors.length]
        })).sort((a, b) => b.value - a.value);

        // Use vertical bar chart like Monthly Sales
        const maxValue = Math.max(...values);
        const chartHeight = canvas.height - 80; // Leave space for labels and title
        const chartWidth = canvas.width - 40;
        const barWidth = Math.min(60, (chartWidth / sortedData.length) * 0.8);
        const barSpacing = (chartWidth / sortedData.length) * 0.2;
        const startX = 20;
        const startY = 40;

        // Draw grid lines
        ctx.strokeStyle = '#E5E7EB';
        ctx.lineWidth = 1;
        for (let i = 0; i <= 5; i++) {
            const y = startY + (chartHeight / 5) * i;
            ctx.beginPath();
            ctx.moveTo(startX, y);
            ctx.lineTo(startX + chartWidth, y);
            ctx.stroke();
        }

        // Draw Y-axis labels
        ctx.fillStyle = '#6B7280';
        ctx.font = '10px Arial';
        ctx.textAlign = 'right';
        for (let i = 0; i <= 5; i++) {
            const value = Math.round((maxValue / 5) * (5 - i));
            const y = startY + (chartHeight / 5) * i;
            ctx.fillText(value.toString(), startX - 5, y + 3);
        }

        // Draw bars
        sortedData.forEach((item, index) => {
            const x = startX + index * (barWidth + barSpacing) + barSpacing / 2;
            const barHeight = (item.value / maxValue) * chartHeight;
            const y = startY + chartHeight - barHeight;
            
            // Draw bar background
            ctx.fillStyle = '#F3F4F6';
            ctx.fillRect(x, startY, barWidth, chartHeight);
            
            // Draw bar
            ctx.fillStyle = item.color;
            ctx.fillRect(x, y, barWidth, barHeight);
            
            // Draw value on top of bar
            if (barHeight > 20) {
                ctx.fillStyle = '#FFFFFF';
                ctx.font = 'bold 11px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(item.value.toString(), x + barWidth / 2, y + 15);
            }
            
            // Draw category name (rotated if needed)
            ctx.fillStyle = '#374151';
            ctx.font = '10px Arial';
            ctx.textAlign = 'center';
            
            // Truncate long category names
            const displayName = item.category.length > 8 ? item.category.substring(0, 8) + '...' : item.category;
            ctx.fillText(displayName, x + barWidth / 2, startY + chartHeight + 15);
        });

        // Draw title
        ctx.fillStyle = '#374151';
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Medicines by Category', canvas.width / 2, 20);
    }

    /**
     * Create Stock Chart
     */
    function createStockChart(stockStats) {
        const canvas = document.getElementById('stockChartCanvas');
        const ctx = canvas.getContext('2d');
        
        // Clear previous chart
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        
        const labels = ['In Stock', 'Low Stock', 'Out of Stock'];
        const values = [stockStats.inStock, stockStats.lowStock, stockStats.outOfStock];
        const colors = ['#10B981', '#F59E0B', '#EF4444'];
        
        if (values.every(v => v === 0)) {
            ctx.fillStyle = '#6B7280';
            ctx.font = '16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('No data available', canvas.width / 2, canvas.height / 2);
            return;
        }

        // Create a cleaner donut chart
        const centerX = canvas.width / 2;
        const centerY = canvas.height / 2;
        const outerRadius = Math.min(centerX, centerY) - 40;
        const innerRadius = outerRadius * 0.6;
        
        let currentAngle = 0;
        const total = values.reduce((sum, val) => sum + val, 0);
        
        // Draw background circle
        ctx.beginPath();
        ctx.arc(centerX, centerY, outerRadius, 0, 2 * Math.PI);
        ctx.fillStyle = '#F3F4F6';
        ctx.fill();
        
        values.forEach((value, index) => {
            if (value > 0) {
                const sliceAngle = (value / total) * 2 * Math.PI;
                
                // Draw outer arc
                ctx.beginPath();
                ctx.moveTo(centerX, centerY);
                ctx.arc(centerX, centerY, outerRadius, currentAngle, currentAngle + sliceAngle);
                ctx.closePath();
                ctx.fillStyle = colors[index];
                ctx.fill();
                
                // Draw inner arc (donut effect)
                ctx.beginPath();
                ctx.arc(centerX, centerY, innerRadius, currentAngle, currentAngle + sliceAngle);
                ctx.closePath();
                ctx.fillStyle = '#FFFFFF';
                ctx.fill();
                
                // Draw label with percentage
                const labelAngle = currentAngle + sliceAngle / 2;
                const percentage = ((value / total) * 100).toFixed(1);
                const labelX = centerX + Math.cos(labelAngle) * (outerRadius + 30);
                const labelY = centerY + Math.sin(labelAngle) * (outerRadius + 30);
                
                ctx.fillStyle = '#374151';
                ctx.font = 'bold 12px Arial';
                ctx.textAlign = 'center';
                ctx.fillText(`${labels[index]}`, labelX, labelY - 5);
                ctx.fillText(`${value} (${percentage}%)`, labelX, labelY + 15);
                
                currentAngle += sliceAngle;
            }
        });

        // Draw title
        ctx.fillStyle = '#374151';
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('Stock Distribution', canvas.width / 2, 20);
    }

    /**
     * Export Analytics
     */
    function exportAnalytics() {
        // Simple export functionality
        const analyticsData = {
            timestamp: new Date().toISOString(),
            totalMedicines: document.getElementById('totalMedicinesCount').textContent,
            activeMedicines: document.getElementById('activeMedicinesCount').textContent,
            totalValue: document.getElementById('totalValueAmount').textContent,
            lowStock: document.getElementById('lowStockCount').textContent
        };
        
        const dataStr = JSON.stringify(analyticsData, null, 2);
        const dataBlob = new Blob([dataStr], {type: 'application/json'});
        const url = URL.createObjectURL(dataBlob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `inventory-analytics-${new Date().toISOString().split('T')[0]}.json`;
        link.click();
        URL.revokeObjectURL(url);
        
        showNotification('Analytics report exported successfully!', 'success');
    }

    /**
     * Initialize server-side data to prevent blank page
     */
    function initializeServerSideData() {
        // Check if medicines are already rendered on the page
        const medicineCards = document.querySelectorAll('[data-medicine-id]');
        if (medicineCards.length > 0) {
            console.log('Server-side medicines found, initializing data...');
            
            // Extract medicine data from server-side rendered cards
            medicines = Array.from(medicineCards).map(card => {
                const medicineId = card.getAttribute('data-medicine-id');
                const name = card.querySelector('h3')?.textContent || '';
                const genericName = card.querySelector('p')?.textContent || '';
                
                // Extract batch number from the card content
                const batchText = card.textContent;
                const batchMatch = batchText.match(/Batch Number:\s*([^\n\r]+)/i);
                const batchNumber = batchMatch ? batchMatch[1].trim() : '';
                
                // Extract stock quantity
                const stockMatch = batchText.match(/Stock Quantity:\s*(\d+)/i);
                const stockQuantity = stockMatch ? parseInt(stockMatch[1]) : 0;
                
                // Extract selling price
                const priceMatch = batchText.match(/Selling Price:\s*Br\s*([\d,]+\.?\d*)/i);
                const sellingPrice = priceMatch ? parseFloat(priceMatch[1].replace(',', '')) : 0;
                
                return {
                    id: medicineId,
                    name: name,
                    generic_name: genericName,
                    batch_number: batchNumber,
                    stock_quantity: stockQuantity,
                    selling_price: sellingPrice,
                    is_active: true // Assume active for server-side data
                };
            });
            
            console.log(`Initialized with ${medicines.length} server-side medicines`);
            console.log('Sample medicine data:', medicines[0]);
        }
    }

    /**
     * Initialize Server-Sent Events for real-time updates
     */
    function initializeSSE() {
        // SSE temporarily disabled to prevent 404 errors
        // Will use regular AJAX updates instead
        console.log('SSE disabled - using regular AJAX updates');
        
        // Fallback to regular AJAX updates every 60 seconds (reduced frequency to prevent memory issues)
        setInterval(() => {
            // Only update if user is actively using the page
            if (!document.hidden) {
            loadMedicines();
            }
        }, 60000);
    }

    /**
     * Load inventory statistics
     */
    function loadInventoryStats() {
        // Calculate stats directly from medicines array
        if (medicines.length > 0) {
            updateInventoryStats();
        }
    }

    /**
     * Update inventory statistics display
     */
    function updateInventoryStats() {
        // Update total items
        const totalItemsElement = document.getElementById('totalItems');
        if (totalItemsElement) {
            totalItemsElement.textContent = medicines.length;
        }

        // Update total value (calculate from medicines) - DISABLED to preserve server-side calculation
        // The server-side calculation is more accurate and complete
        // const totalValueElement = document.getElementById('totalValue');
        // if (totalValueElement && medicines.length > 0) {
        //     const totalValue = medicines.reduce((sum, medicine) => {
        //         const sellingPrice = parseFloat(medicine.selling_price );
        //         const stockQuantity = parseInt(medicine.stock_quantity );
        //         return sum + (sellingPrice * stockQuantity);
        //     }, 0);
        //     totalValueElement.textContent = `Br ${totalValue.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2})}`;
        // }

        // Update in stock count (active medicines)
        const inStockElement = document.getElementById('inStockCount');
        if (inStockElement) {
            const activeCount = medicines.filter(m => m.is_active).length;
            inStockElement.textContent = activeCount;
        }

        // Update out of stock count (inactive medicines)
        const outOfStockElement = document.getElementById('outOfStockCount');
        if (outOfStockElement) {
            const inactiveCount = medicines.filter(m => !m.is_active).length;
            outOfStockElement.textContent = inactiveCount;
        }

        // Update percentage for in stock
        const activeCount = medicines.filter(m => m.is_active).length;
        const inStockPercentage = medicines.length > 0 ? Math.round((activeCount / medicines.length) * 100) : 0;
        const percentageElement = document.querySelector('#inStockCount').parentElement.querySelector('.bg-green-500');
        if (percentageElement) {
            percentageElement.textContent = `${inStockPercentage}%`;
        }

        // Update the "Active" text to show actual active medicines
        const activeElement = document.querySelector('#totalItems').parentElement.querySelector('.bg-blue-500');
        if (activeElement) {
            const activeCount = medicines.filter(m => m.is_active).length;
            activeElement.textContent = `${activeCount} Active`;
        }
    }

    // Load medicines on page load (stats will be updated automatically)
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize SSE for real-time updates
        initializeSSE();
        
        // Use server-side data initially instead of AJAX to prevent conflicts
        console.log('Using server-side data for initial load');
        
        // Initialize medicines array with server-side data
        initializeServerSideData();
        
        // Initialize pagination with server-side data
        initializeServerSidePagination();
        
        // Populate filters with server-side data
        populateBatchFilter();
        populateStockFilter();
        
        // Load categories with a small delay to ensure DOM is ready
        setTimeout(() => {
            loadCategories();
        }, 100);
        
        // Also try to populate category filter after a longer delay as backup
        setTimeout(() => {
            if (categories && categories.length > 0) {
                populateCategoryFilter();
            }
        }, 500);
        
        // Initialize filters AFTER they are populated (with a delay to ensure options are loaded)
        setTimeout(() => {
            initializeFiltersFromURL();
        }, 800);
        
        // Auto-calculate reorder level based on stock quantity
        const stockQuantityInput = document.getElementById('stockQuantity');
        const reorderLevelInput = document.getElementById('reorderLevel');
        
        if (stockQuantityInput && reorderLevelInput) {
            // Calculate initial reorder level if stock quantity has a value
            const initialStockQuantity = parseInt(stockQuantityInput.value) ;
            if (initialStockQuantity > 0) {
                const initialReorderLevel = Math.ceil(initialStockQuantity * 0.1);
                reorderLevelInput.value = initialReorderLevel;
            }
            
            // Auto-calculate when stock quantity changes
            stockQuantityInput.addEventListener('input', function() {
                const stockQuantity = parseInt(this.value) ;
                const reorderLevel = Math.ceil(stockQuantity * 0.1); // 10% of stock quantity
                reorderLevelInput.value = reorderLevel;
            });
        }
        
        // Enable clear button when CSV data is entered
        const csvInput = document.getElementById('csvDataInput');
        const clearBtn = document.getElementById('clearDataBtn');
        
        if (csvInput && clearBtn) {
            csvInput.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    clearBtn.disabled = false;
                    clearBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                } else if (batchMedicines.length === 0) {
                    clearBtn.disabled = true;
                    clearBtn.classList.add('opacity-50', 'cursor-not-allowed');
                }
            });
        }

        // Add Medicine form submission
        const addMedicineForm = document.getElementById('addMedicineForm');
        if (addMedicineForm) {
            addMedicineForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = {
                    name: document.getElementById('medicineName').value,
                    generic_name: document.getElementById('genericName').value,
                    manufacturer: document.getElementById('manufacturer').value,
                    category_id: document.getElementById('medicineCategory').value,
                    strength: document.getElementById('medicineStrength').value,
                    form: document.getElementById('medicineForm').value,
                    unit: document.getElementById('medicineUnit').value,
                    barcode: document.getElementById('barcode').value,
                    selling_price: document.getElementById('sellingPrice').value,
                    cost_price: document.getElementById('costPrice').value,
                    stock_quantity: document.getElementById('stockQuantity').value,
                    reorder_level: document.getElementById('reorderLevel').value,
                    expiry_date: document.getElementById('expiryDate').value,
                    batch_number: document.getElementById('batchNumber').value,
                    prescription_required: document.getElementById('prescriptionRequired').value,
                    is_active: document.getElementById('activeMedicine').checked,
                    description: document.getElementById('medicineDescription').value
                };
                
                createMedicine(formData);
            });
        }

        // Edit Medicine form submission
        const editMedicineForm = document.getElementById('editMedicineForm');
        if (editMedicineForm) {
            editMedicineForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const medicineId = document.getElementById('editMedicineId').value;
                const formData = {
                    name: document.getElementById('editMedicineName').value,
                    generic_name: document.getElementById('editGenericName').value,
                    manufacturer: document.getElementById('editManufacturer').value,
                    category_id: document.getElementById('editMedicineCategory').value,
                    strength: document.getElementById('editMedicineStrength').value,
                    form: document.getElementById('editMedicineFormField').value,
                    unit: document.getElementById('editMedicineUnit').value,
                    barcode: document.getElementById('editBarcode').value,
                    selling_price: document.getElementById('editSellingPrice').value,
                    cost_price: document.getElementById('editCostPrice').value,
                    stock_quantity: document.getElementById('editStockQuantity').value,
                    reorder_level: document.getElementById('editReorderLevel').value,
                    expiry_date: document.getElementById('editExpiryDate').value,
                    batch_number: document.getElementById('editBatchNumber').value,
                    prescription_required: document.getElementById('editPrescriptionRequired').value,
                    is_active: document.getElementById('editActiveMedicine').checked,
                    description: document.getElementById('editMedicineDescription').value
                };
                
                updateMedicine(medicineId, formData);
            });
        }

        // Auto-calculate reorder level for edit modal
        const editStockQuantityInput = document.getElementById('editStockQuantity');
        const editReorderLevelInput = document.getElementById('editReorderLevel');
        if (editStockQuantityInput && editReorderLevelInput) {
            // Auto-calculate when stock quantity changes
            editStockQuantityInput.addEventListener('input', function() {
                const stockQuantity = parseInt(this.value) ;
                const reorderLevel = Math.ceil(stockQuantity * 0.1); // 10% of stock quantity
                editReorderLevelInput.value = reorderLevel;
            });
        }
    });
})();
</script>

<!-- Category Management Modal -->
<div id="categoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-6xl max-h-[90vh] overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-tags text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Category Management</h2>
                    <p class="text-gray-600 dark:text-gray-400">Organize and manage your inventory categories</p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="refreshCategories()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2"></i>Refresh
                </button>
                <button onclick="showCategoryStatistics()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-chart-bar mr-2"></i>Statistics
                </button>
                <button onclick="hideCategoryManagementModal()" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg flex items-center justify-center transition-all duration-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <!-- Search and Filter Bar -->
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <input type="text" id="categorySearch" placeholder="Search categories..." 
                           class="w-full pl-10 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
                <select id="categoryStatusFilter" class="px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                    <option value="">All Status</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
                <button onclick="showCreateCategoryModal()" class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-plus mr-2"></i>+ Add Category
                </button>
            </div>
        </div>

        <!-- Category Cards Grid -->
        <div class="p-6 overflow-y-auto max-h-[60vh]">
            <div id="categoriesContainer" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Anti-Fungal Category -->
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-star text-white text-xl"></i>
                        </div>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-800 transition-all duration-200">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button class="w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center hover:bg-red-200 dark:hover:bg-red-800 transition-all duration-200">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Anti-Fungal</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">0 items</p>
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">Active</span>
                    </div>
                </div>

                <!-- Antibiotics Category -->
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-red-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-bolt text-white text-xl"></i>
                        </div>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-800 transition-all duration-200">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button class="w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center hover:bg-red-200 dark:hover:bg-red-800 transition-all duration-200">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Antibiotics</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">0 items</p>
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">Active</span>
                    </div>
                </div>

                <!-- Cartoon Category -->
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-square text-white text-xl"></i>
                        </div>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-800 transition-all duration-200">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button class="w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center hover:bg-red-200 dark:hover:bg-red-800 transition-all duration-200">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Cartoon</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">0 items</p>
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">Active</span>
                    </div>
                </div>

                <!-- Diabetics Category -->
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-green-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-microscope text-white text-xl"></i>
                        </div>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-800 transition-all duration-200">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button class="w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center hover:bg-red-200 dark:hover:bg-red-800 transition-all duration-200">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Diabetics</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">0 items</p>
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">Active</span>
                    </div>
                </div>

                <!-- Hypertensive Drugs Category -->
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-leaf text-white text-xl"></i>
                        </div>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-800 transition-all duration-200">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button class="w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center hover:bg-red-200 dark:hover:bg-red-800 transition-all duration-200">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Hypertensive Drugs</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">0 items</p>
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">Active</span>
                    </div>
                </div>

                <!-- Infections Drug Category -->
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-purple-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-square text-white text-xl"></i>
                        </div>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-800 transition-all duration-200">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button class="w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center hover:bg-red-200 dark:hover:bg-red-800 transition-all duration-200">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Infections Drug</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">0 items</p>
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">Active</span>
                    </div>
                </div>

                <!-- Inflammatory Drugs Category -->
                <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 relative">
                    <div class="flex items-start justify-between mb-4">
                        <div class="w-12 h-12 bg-orange-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-box text-white text-xl"></i>
                        </div>
                        <div class="flex gap-2">
                            <button class="w-8 h-8 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-lg flex items-center justify-center hover:bg-blue-200 dark:hover:bg-blue-800 transition-all duration-200">
                                <i class="fas fa-edit text-sm"></i>
                            </button>
                            <button class="w-8 h-8 bg-red-100 dark:bg-red-900 text-red-600 dark:text-red-400 rounded-lg flex items-center justify-center hover:bg-red-200 dark:hover:bg-red-800 transition-all duration-200">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Inflammatory Drugs</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">0 items</p>
                    <div class="flex items-center justify-between">
                        <span class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">Active</span>
                    </div>
                </div>

                <!-- Add New Category Card -->
                <div onclick="showCreateCategoryModal()" class="bg-gray-50 dark:bg-gray-700 border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-6 hover:border-orange-500 hover:bg-orange-50 dark:hover:bg-orange-900/20 transition-all duration-300 cursor-pointer flex flex-col items-center justify-center">
                    <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-plus text-orange-500 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-600 dark:text-gray-400 mb-2">Add New Category</h3>
                    <p class="text-sm text-gray-500 dark:text-gray-500 text-center">Click to create a new category</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Category Modal -->
<div id="createCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-tags text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Category</h2>
                    <p class="text-gray-600 dark:text-gray-400">Add a new category to organize your inventory</p>
                </div>
            </div>
            <button onclick="hideCreateCategoryModal()" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <form id="createCategoryForm" class="space-y-6">
                @csrf
                <!-- Category Name -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Category Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="categoryName" placeholder="Enter category name" required
                           class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                    <div id="nameError" class="text-red-500 text-sm mt-1 hidden"></div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Description
                    </label>
                    <textarea name="description" id="categoryDescription" placeholder="Enter category description (optional)" rows="3"
                              class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200 resize-none"></textarea>
                </div>

                <!-- Color Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Color
                    </label>
                    <div class="flex items-center gap-3">
                        <div id="colorPreview" class="w-8 h-8 bg-blue-500 rounded-lg border-2 border-gray-300 dark:border-gray-600"></div>
                        <input type="text" name="color" id="categoryColor" value="#3B82F6" 
                               class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                        <button type="button" id="colorPicker" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg flex items-center justify-center transition-all duration-200">
                            <i class="fas fa-palette"></i>
                        </button>
                    </div>
                </div>

                <!-- Icon Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Icon
                    </label>
                    <div class="flex items-center gap-3">
                        <div id="iconPreview" class="w-12 h-12 bg-blue-100 dark:bg-blue-900 border-2 border-blue-500 rounded-lg flex items-center justify-center">
                            <i id="iconPreviewIcon" class="fas fa-tags text-blue-500 text-xl"></i>
                        </div>
                        <input type="text" name="icon" id="categoryIcon" value="tag" readonly
                               class="flex-1 px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg dark:text-white">
                        <button type="button" id="iconPicker" class="w-10 h-10 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-400 rounded-lg flex items-center justify-center transition-all duration-200">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Active Category Toggle -->
                <div>
                    <div class="flex items-start gap-3">
                        <input type="checkbox" name="is_active" id="activeCategory" checked
                               class="w-5 h-5 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                        <div>
                            <label for="activeCategory" class="block text-sm font-bold text-gray-900 dark:text-white">
                                Active Category
                            </label>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                Active categories are available for use in inventory items
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Preview Section -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Preview
                    </label>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center gap-4">
                            <div id="previewIcon" class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                                <i id="previewIconClass" class="fas fa-tags text-white text-xl"></i>
                            </div>
                            <div>
                                <h3 id="previewName" class="text-lg font-bold text-gray-900 dark:text-white">Category Name</h3>
                                <div class="flex items-center gap-2 mt-2">
                                    <span id="previewStatus" class="px-3 py-1 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 rounded-full text-xs font-semibold">Active</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideCreateCategoryModal()" 
                    class="px-6 py-3 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-semibold text-sm transition-all duration-200">
                Cancel
            </button>
            <button type="submit" form="createCategoryForm" id="createCategoryBtn"
                    class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                <i class="fas fa-plus mr-2"></i>
                <span id="createCategoryBtnText">Create Category</span>
            </button>
        </div>
    </div>
</div>

<!-- Add Medicine Modal -->
<div id="addMedicineModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-box text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Medicine</h2>
                    <p class="text-gray-600 dark:text-gray-400">Medicine Information</p>
                </div>
            </div>
            <button onclick="hideAddMedicineModal()" class="w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <form id="addMedicineForm" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: Medicine Information -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Medicine Information</h3>
                        
                        <!-- Medicine Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Medicine Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="medicineName" placeholder="Enter medicine name" required
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Generic Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Generic Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="generic_name" id="genericName" placeholder="Enter generic name" required
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Manufacturer -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Manufacturer
                            </label>
                            <input type="text" name="manufacturer" id="manufacturer" placeholder="Enter manufacturer name"
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Category -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-900 dark:text-white">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <button type="button" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center">
                                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                                </button>
                            </div>
                            <select name="category_id" id="medicineCategory" required
                                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Strength -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Strength <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="strength" id="medicineStrength" placeholder="e.g., 500mg" required
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Form -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Form <span class="text-red-500">*</span>
                            </label>
                            <select name="form" id="medicineForm" required
                                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <option value="">Select Form</option>
                                @foreach($pharmaceuticalForms as $form)
                                <option value="{{ $form->name }}" data-category="{{ $form->category }}">
                                    {{ $form->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Unit -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Unit <span class="text-red-500">*</span>
                            </label>
                            <select name="unit" id="medicineUnit" required
                                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <option value="">Select Unit</option>
                                @foreach($pharmaceuticalUnits as $unit)
                                <option value="{{ $unit->name }}" data-symbol="{{ $unit->symbol }}" data-category="{{ $unit->category }}">
                                    {{ $unit->name }} ({{ $unit->symbol }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Barcode -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Barcode
                            </label>
                            <input type="text" name="barcode" id="barcode" placeholder="Enter barcode number"
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>
                    </div>

                    <!-- Right Column: Pricing & Stock -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pricing</h3>
                        
                        <!-- Selling Price -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Selling Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                                <input type="number" name="selling_price" id="sellingPrice" value="0" step="0.01" required
                                       class="w-full pl-8 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            </div>
                        </div>

                        <!-- Cost Price -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Cost Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                                <input type="number" name="cost_price" id="costPrice" value="0" step="0.01" required
                                       class="w-full pl-8 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            </div>
                        </div>

                        <!-- Stock Quantity -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Stock Quantity
                            </label>
                            <input type="number" name="stock_quantity" id="stockQuantity" value="0" min="0" required
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Reorder Level -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Reorder Level
                            </label>
                            <input type="number" name="reorder_level" id="reorderLevel" value="0" min="0"
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200"
                                   title="Auto-calculated as 10% of stock quantity, but you can edit this value">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Auto-calculated as 10% of stock quantity</p>
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Expiry Date <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" name="expiry_date" id="expiryDate" required
                                       class="w-full pl-10 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Batch Number -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Batch Number <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">#</span>
                                <input type="text" name="batch_number" id="batchNumber" value="B20251001-635012-WXYK" required
                                       class="w-full pl-8 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            </div>
                        </div>

                        <!-- Prescription Required -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Prescription Required
                            </label>
                            <select name="prescription_required" id="prescriptionRequired"
                                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>

                        <!-- Active Medicine -->
                        <div>
                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="is_active" id="activeMedicine" checked
                                       class="w-5 h-5 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                <label for="activeMedicine" class="block text-sm font-bold text-gray-900 dark:text-white">
                                    Active Medicine
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Description
                    </label>
                    <textarea name="description" id="medicineDescription" placeholder="Additional notes about this medicine..." rows="3"
                              class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200 resize-none"></textarea>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideAddMedicineModal()" 
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Cancel
            </button>
            <button type="submit" form="addMedicineForm" class="px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Save Medicine
            </button>
        </div>
    </div>
</div>

<!-- Edit Medicine Modal -->
<div id="editMedicineModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-edit text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Medicine</h2>
                    <p class="text-gray-600 dark:text-gray-400">Update Medicine Information</p>
                </div>
            </div>
            <button onclick="hideEditMedicineModal()" class="w-10 h-10 bg-green-500 hover:bg-green-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Form Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <form id="editMedicineForm" class="space-y-6">
                @csrf
                <input type="hidden" id="editMedicineId" name="medicine_id">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Left Column: Medicine Information -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Medicine Information</h3>
                        
                        <!-- Medicine Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Medicine Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="editMedicineName" placeholder="Enter medicine name" required
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Generic Name -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Generic Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="generic_name" id="editGenericName" placeholder="Enter generic name" required
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Manufacturer -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Manufacturer
                            </label>
                            <input type="text" name="manufacturer" id="editManufacturer" placeholder="Enter manufacturer name"
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Category -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-bold text-gray-900 dark:text-white">
                                    Category <span class="text-red-500">*</span>
                                </label>
                                <button type="button" class="text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 flex items-center">
                                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                                </button>
                            </div>
                            <select name="category_id" id="editMedicineCategory" required
                                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <option value="">Select Category</option>
                                @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Strength -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Strength <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="strength" id="editMedicineStrength" placeholder="e.g., 500mg" required
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Form -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Form <span class="text-red-500">*</span>
                            </label>
                            <select name="form" id="editMedicineFormField" required
                                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <option value="">Select Form</option>
                                @foreach($pharmaceuticalForms as $form)
                                <option value="{{ $form->name }}" data-category="{{ $form->category }}">
                                    {{ $form->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Unit -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Unit <span class="text-red-500">*</span>
                            </label>
                            <select name="unit" id="editMedicineUnit" required
                                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <option value="">Select Unit</option>
                                @foreach($pharmaceuticalUnits as $unit)
                                <option value="{{ $unit->name }}" data-symbol="{{ $unit->symbol }}" data-category="{{ $unit->category }}">
                                    {{ $unit->name }} ({{ $unit->symbol }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Barcode -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Barcode
                            </label>
                            <input type="text" name="barcode" id="editBarcode" placeholder="Enter barcode number"
                                   class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>
                    </div>

                    <!-- Right Column: Pricing & Stock -->
                    <div class="space-y-6">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pricing</h3>
                        
                        <!-- Selling Price -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Selling Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                                <input type="number" name="selling_price" id="editSellingPrice" value="0" step="0.01" required
                                       class="w-full pl-8 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            </div>
                        </div>

                        <!-- Cost Price -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Cost Price <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">$</span>
                                <input type="number" name="cost_price" id="editCostPrice" value="0" step="0.01" required
                                       class="w-full pl-8 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            </div>
                        </div>

                        <!-- Stock Quantity -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Stock Quantity
                            </label>
                            <input type="number" name="stock_quantity" id="editStockQuantity" value="0" min="0" required
                               class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                        </div>

                        <!-- Reorder Level -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Reorder Level
                            </label>
                            <input type="number" name="reorder_level" id="editReorderLevel" value="0" min="0"
                               class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200"
                               title="Auto-calculated as 10% of stock quantity, but you can edit this value">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Auto-calculated as 10% of stock quantity</p>
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Expiry Date <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="date" name="expiry_date" id="editExpiryDate" required
                                       class="w-full pl-10 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <i class="fas fa-calendar absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                            </div>
                        </div>

                        <!-- Batch Number -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Batch Number <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 dark:text-gray-400">#</span>
                                <input type="text" name="batch_number" id="editBatchNumber" value="B20251001-635012-WXYK" required
                                       class="w-full pl-8 pr-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            </div>
                        </div>

                        <!-- Prescription Required -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                                Prescription Required
                            </label>
                            <select name="prescription_required" id="editPrescriptionRequired"
                                    class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                                <option value="no">No</option>
                                <option value="yes">Yes</option>
                            </select>
                        </div>

                        <!-- Active Medicine -->
                        <div>
                            <div class="flex items-start gap-3">
                                <input type="checkbox" name="is_active" id="editActiveMedicine" checked
                                       class="w-5 h-5 text-blue-600 bg-gray-100 dark:bg-gray-800 border-gray-300 dark:border-gray-600 rounded focus:ring-blue-500 focus:ring-2">
                                <label for="editActiveMedicine" class="block text-sm font-bold text-gray-900 dark:text-white">
                                    Active Medicine
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label class="block text-sm font-bold text-gray-900 dark:text-white mb-2">
                        Description
                    </label>
                    <textarea name="description" id="editMedicineDescription" placeholder="Additional notes about this medicine..." rows="3"
                              class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200 resize-none"></textarea>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideEditMedicineModal()" 
                    class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Cancel
            </button>
            <button type="submit" form="editMedicineForm" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Update Medicine
            </button>
        </div>
    </div>
</div>

<!-- Delete Medicine Modal -->
<div id="deleteMedicineModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4" style="backdrop-filter: blur(4px);">
    <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-red-600 rounded-t-2xl p-6 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-700 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Delete Medicine</h2>
                    <p class="text-red-100 text-sm">Permanently delete medicine from inventory</p>
                </div>
            </div>
            <button onclick="hideDeleteMedicineModal()" class="w-8 h-8 bg-red-700 hover:bg-red-800 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body - Scrollable Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <!-- Warning Section -->
            <div class="bg-red-600 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-white mr-3 mt-1"></i>
                    <div>
                        <p class="text-white font-semibold">Warning:</p>
                        <p class="text-red-100 text-sm">This action cannot be undone. All medicine data will be permanently removed.</p>
                    </div>
                </div>
            </div>

            <!-- Medicine Details -->
            <div class="flex items-center mb-3">
                <i class="fas fa-pills text-gray-400 mr-2"></i>
                <h3 class="text-white font-semibold">Medicine Details</h3>
            </div>
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-400">Medicine Name:</span>
                    <span id="deleteMedicineName" class="text-white font-semibold"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Generic Name:</span>
                    <span id="deleteMedicineGeneric" class="text-white"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Category:</span>
                    <span id="deleteMedicineCategory" class="text-white"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Stock Quantity:</span>
                    <span id="deleteMedicineStock" class="text-white"></span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-400">Status:</span>
                    <span id="deleteMedicineStatus" class="font-semibold"></span>
                </div>
            </div>

            <!-- Confirmation Input -->
            <div class="mb-6">
                <p class="text-white mb-2">
                    Confirm Deletion <span class="text-red-400 font-bold">DELETE</span> in the box below:
                </p>
                <input type="text" id="deleteMedicineConfirmationInput" placeholder="Type DELETE to confirm"
                       oninput="handleDeleteMedicineConfirmation()"
                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
            </div>

            <!-- Impact Warning -->
            <div class="bg-orange-600 border border-orange-500 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-orange-200 mr-3 mt-1"></i>
                    <div>
                        <p class="text-white font-semibold mb-2">Deleting may affect:</p>
                        <ul class="text-orange-100 text-sm space-y-1">
                            <li>• Inventory stock levels and tracking</li>
                            <li>• Sales records and transaction history</li>
                            <li>• Prescription and dispensing records</li>
                            <li>• Analytics and reporting data</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-700 flex-shrink-0">
            <button onclick="hideDeleteMedicineModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200">
                Cancel
            </button>
            <button id="deleteMedicineBtn" onclick="confirmDeleteMedicine()" disabled
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all duration-200 opacity-50 cursor-not-allowed">
                Delete Medicine
            </button>
        </div>
    </div>
</div>

<!-- Delete Category Modal -->
<div id="deleteCategoryModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4" style="backdrop-filter: blur(4px);">
    <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-red-600 rounded-t-2xl p-6 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-700 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Delete Category</h2>
                    <p class="text-red-100 text-sm">Permanently delete category from system</p>
                </div>
            </div>
            <button onclick="hideDeleteCategoryModal()" class="w-8 h-8 bg-red-700 hover:bg-red-800 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Body - Scrollable Content -->
        <div class="p-6 overflow-y-auto flex-1">
        <!-- Warning Section -->
            <div class="bg-red-600 rounded-lg p-4 mb-6">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-white mr-3 mt-1"></i>
                <div>
                    <p class="text-white font-semibold">Warning:</p>
                    <p class="text-red-100 text-sm">This action cannot be undone. All associated data will be permanently removed.</p>
                </div>
            </div>
        </div>

        <!-- Category Details -->
            <div class="bg-gray-700 rounded-lg p-4 mb-6">
                <div class="flex items-center mb-3">
                    <i class="fas fa-tag text-gray-400 mr-2"></i>
                    <h3 class="text-white font-semibold">Category Details</h3>
                </div>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Category Name:</span>
                        <span id="deleteCategoryName" class="text-white font-semibold"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Item Count:</span>
                        <span id="deleteCategoryItemCount" class="text-white"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Status:</span>
                        <span id="deleteCategoryStatus" class="font-semibold"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Created At:</span>
                        <span id="deleteCategoryCreatedAt" class="text-white"></span>
                    </div>
                </div>
            </div>

            <!-- Confirmation Input -->
            <div class="mb-6">
                <p class="text-white mb-2">
                    Confirm Deletion <span class="text-red-400 font-bold">DELETE</span> in the box below:
                </p>
                <input type="text" id="deleteConfirmationInput" placeholder="Type DELETE to confirm"
                       oninput="handleDeleteConfirmation()"
                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
            </div>

            <!-- Impact Warning -->
            <div class="bg-orange-600 border border-orange-500 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-orange-200 mr-3 mt-1"></i>
                    <div>
                        <p class="text-white font-semibold mb-2">Deleting may affect:</p>
                        <ul class="text-orange-100 text-sm space-y-1">
                            <li>• Medicine categorization and organization</li>
                            <li>• Inventory reports and analytics</li>
                            <li>• Analytics and reporting data</li>
                            <li>• Audit trails and history</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-700 flex-shrink-0">
            <button onclick="hideDeleteCategoryModal()" 
                    class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Cancel
            </button>
            <button id="deleteCategoryBtn" onclick="deleteCategory(document.getElementById('deleteCategoryId').value)" 
                    disabled
                    class="px-6 py-3 bg-red-600 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center opacity-50 cursor-not-allowed">
                <i class="fas fa-trash mr-2"></i>Delete Category
            </button>
        </div>
    </div>
</div>

<!-- Hidden input for category ID -->
<input type="hidden" id="deleteCategoryId" value="">

<!-- Batch Add Medicines Modal -->
<div id="batchAddModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-box text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Batch Add Medicines</h2>
                    <p class="text-gray-600 dark:text-gray-400">Manual Medicine Entry</p>
                </div>
            </div>
            <button onclick="hideBatchAddModal()" class="w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 flex-1 overflow-y-auto">
            <!-- Instructions -->
            <div class="mb-6">
                <p class="text-gray-700 dark:text-gray-300 mb-4">
                    Upload a CSV file or paste CSV data to add multiple medicines at once.
                </p>
                
                <!-- Template Buttons -->
                <div class="flex gap-3 mb-6">
                    <button onclick="showCSVTemplate()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                        <i class="fas fa-file-alt mr-2"></i>CSV Template
                    </button>
                    <button onclick="downloadCSVTemplate()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                        <i class="fas fa-download mr-2"></i>Download Template
                    </button>
                </div>
            </div>

            <!-- CSV Data Input Area -->
            <div class="mb-6">
                <div class="relative">
                    <textarea id="csvDataInput" placeholder="Paste your CSV data here..." rows="12"
                              class="w-full px-4 py-4 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200 resize-none"></textarea>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Paste CSV data and it will automatically populate the form fields below
                    </p>
                </div>
            </div>

            <!-- Process Data Button -->
            <div class="flex justify-between mb-6">
                <button id="clearDataBtn" onclick="clearBatchData()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center opacity-50 cursor-not-allowed" disabled>
                    <i class="fas fa-trash mr-2"></i>Clear Data
                </button>
                <button onclick="processCSVData()" class="px-6 py-3 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-upload mr-2"></i>Process Data
                </button>
            </div>

            <!-- Processed Medicines Preview -->
            <div id="processedMedicinesPreview" class="hidden mb-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Processed Medicines Preview</h3>
                <div id="medicinesPreviewList" class="space-y-3 max-h-60 overflow-y-auto">
                    <!-- Medicines will be populated here -->
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-between p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="text-gray-600 dark:text-gray-400">
                <span id="medicineCount">0 medicines ready to add</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="hideBatchAddModal()" 
                        class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                    Cancel
                </button>
                <button id="addMedicinesBtn" onclick="addBatchMedicines()" disabled class="px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white rounded-lg font-semibold text-sm transition-all duration-200 flex items-center opacity-50 cursor-not-allowed">
                    <i class="fas fa-file-alt mr-2"></i>Add Medicines
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
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import/Export Medicines</h2>
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
                            <input type="text" name="filename" value="medicines_export" class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">File will be saved with timestamp</p>
                        </div>

                        <!-- Export Filters -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Export Filters (Optional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Category</label>
                                    <select name="filters[category_id]" id="exportCategoryFilter" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Categories</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Stock Status</label>
                                    <select name="filters[stock_status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Stock Status</option>
                                        <option value="in_stock">In Stock</option>
                                        <option value="low_stock">Low Stock</option>
                                        <option value="out_of_stock">Out of Stock</option>
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
                                    <div>Low Stock Items: <span id="lowStockItems">0</span></div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <div>Total Value: <span id="exportTotalValue">0</span> Birr</div>
                                    <div>Out of Stock: <span id="outOfStockItems">0</span></div>
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
                    <!-- Print Inventory Report -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-2">Print Inventory Report</h3>
                        <p class="text-gray-700 dark:text-gray-300">This will generate a professional inventory report with summary statistics and detailed item listing.</p>
                    </div>

                        <!-- Print Filters -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Print Filters (Optional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Category</label>
                                    <select name="filters[category_id]" id="printCategoryFilter" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Categories</option>
                                    </select>
                            </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Stock Status</label>
                                    <select name="filters[stock_status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Stock Status</option>
                                        <option value="in_stock">In Stock</option>
                                        <option value="low_stock">Low Stock</option>
                                        <option value="out_of_stock">Out of Stock</option>
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
        fetch('/import-export/categories')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const categoryFilter = document.getElementById('exportCategoryFilter');
                    if (categoryFilter) {
                        categoryFilter.innerHTML = '<option value="">All Categories</option>';
                        data.data.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            categoryFilter.appendChild(option);
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
        fetch('/import-export/stats?' + params.toString())
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Only update export-specific elements, not main inventory stats
                    const totalRecordsElement = document.getElementById('totalRecords');
                    const lowStockItemsElement = document.getElementById('lowStockItems');
                    const outOfStockItemsElement = document.getElementById('outOfStockItems');
                    
                    if (totalRecordsElement) totalRecordsElement.textContent = data.data.total_records;
                    if (lowStockItemsElement) lowStockItemsElement.textContent = data.data.low_stock_items;
                    if (outOfStockItemsElement) outOfStockItemsElement.textContent = data.data.out_of_stock_items;
                    
                    // Update export-specific total value (not main inventory stats)
                    const exportTotalValueElement = document.getElementById('exportTotalValue');
                    if (exportTotalValueElement) exportTotalValueElement.textContent = data.data.total_value;
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
                window.location.href = '/import-export/template';
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
        
        fetch('/import-export/import', {
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
        const downloadUrl = `/import-export/export?${params.toString()}`;
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
        const printUrl = `/import-export/print?${params.toString()}`;
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
})();
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

<!-- Enhanced Business Intelligence Analytics Modal -->
<div id="analyticsModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-2" style="backdrop-filter: blur(4px);">
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-2xl w-full max-w-6xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                    <i class="fas fa-brain text-white text-sm"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">Business Intelligence</h2>
                    <p class="text-gray-600 dark:text-gray-400 text-sm">Analytics & insights</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <button onclick="refreshAnalytics()" class="px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold text-xs transition-all duration-200">
                    <i class="fas fa-sync-alt mr-1"></i>Refresh
                </button>
                <button onclick="clearAnalyticsCache()" class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg font-semibold text-xs transition-all duration-200">
                    <i class="fas fa-trash mr-1"></i>Clear
                </button>
                <button onclick="hideAnalyticsModal()" class="w-8 h-8 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        </div>

        <!-- Modal Content -->
        <div class="p-4 overflow-y-auto flex-1">
            <div id="analyticsContent">
                <!-- Loading State -->
                <div id="analyticsLoading" class="flex items-center justify-center py-16">
                    <div class="text-center">
                        <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-500 mx-auto mb-6"></div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Loading Business Intelligence...</h3>
                        <p class="text-gray-600 dark:text-gray-400">Analyzing your pharmacy data for advanced insights</p>
                    </div>
                </div>

                <!-- Enhanced Analytics Content -->
                <div id="analyticsData" class="hidden">
                    <!-- Business Intelligence Overview Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                        <!-- Total Inventory Value -->
                        <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-4 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-emerald-100 text-xs font-medium uppercase tracking-wide">Total Inventory Value</p>
                                    <p id="totalInventoryValue" class="text-2xl font-bold mt-1">Br 0.00</p>
                                    <p class="text-emerald-200 text-xs mt-1" id="valueInsight">Loading insight...</p>
                                </div>
                                <div class="w-10 h-10 bg-emerald-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-coins text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Stock Health Score -->
                        <div class="bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-blue-100 text-xs font-medium uppercase tracking-wide">Stock Health</p>
                                    <p id="stockHealthScore" class="text-2xl font-bold mt-1">0%</p>
                                    <p class="text-blue-200 text-xs mt-1" id="stockInsight">Loading insight...</p>
                                </div>
                                <div class="w-10 h-10 bg-blue-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-heartbeat text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Profit Margin -->
                        <div class="bg-gradient-to-br from-purple-500 to-pink-600 rounded-xl p-4 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-purple-100 text-xs font-medium uppercase tracking-wide">Profit Margin</p>
                                    <p id="profitMargin" class="text-2xl font-bold mt-1">0%</p>
                                    <p class="text-purple-200 text-xs mt-1" id="profitInsight">Loading insight...</p>
                                </div>
                                <div class="w-10 h-10 bg-purple-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-chart-line text-lg"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Growth Potential -->
                        <div class="bg-gradient-to-br from-orange-500 to-red-600 rounded-xl p-4 text-white shadow-lg">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-orange-100 text-xs font-medium uppercase tracking-wide">Growth Potential</p>
                                    <p id="growthPotential" class="text-2xl font-bold mt-1">0%</p>
                                    <p class="text-orange-200 text-xs mt-1" id="growthInsight">Loading insight...</p>
                                </div>
                                <div class="w-10 h-10 bg-orange-400 bg-opacity-30 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-rocket text-lg"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Analytics Grid -->
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
                        <!-- Financial Intelligence -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-dollar-sign text-green-600 dark:text-green-400"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Financial Intelligence</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Revenue Potential</span>
                                    <span id="revenuePotential" class="font-bold text-green-600">Br 0.00</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Cost Value</span>
                                    <span id="costValue" class="font-bold">Br 0.00</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Gross Profit</span>
                                    <span id="grossProfit" class="font-bold text-blue-600">Br 0.00</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div id="profitMarginBar" class="bg-green-500 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Predictive Analytics -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-crystal-ball text-purple-600 dark:text-purple-400"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Predictive Analytics</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Expiring in 7 days</span>
                                    <span id="expiring7Days" class="font-bold text-red-600">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Expiring in 30 days</span>
                                    <span id="expiring30Days" class="font-bold text-orange-600">0</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Risk Level</span>
                                    <span id="riskLevel" class="font-bold text-blue-600">Low</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div id="riskBar" class="bg-purple-500 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Performance Indicators -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg border border-gray-100 dark:border-gray-700">
                            <div class="flex items-center mb-4">
                                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mr-3">
                                    <i class="fas fa-tachometer-alt text-blue-600 dark:text-blue-400"></i>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">Performance</h3>
                            </div>
                            <div class="space-y-4">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Efficiency Score</span>
                                    <span id="efficiencyScore" class="font-bold text-green-600">0%</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Turnover Rate</span>
                                    <span id="turnoverRate" class="font-bold text-blue-600">0x</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600 dark:text-gray-400">Growth Score</span>
                                    <span id="growthScore" class="font-bold text-purple-600">Low</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div id="efficiencyBar" class="bg-blue-500 h-2 rounded-full" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AI Recommendations Section -->
                    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900 dark:to-purple-900 rounded-2xl p-6 mb-8 border border-blue-200 dark:border-blue-700">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-purple-600 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-robot text-white"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">AI-Powered Recommendations</h3>
                        </div>
                        <div id="aiRecommendations" class="space-y-4">
                            <!-- Will be populated by JavaScript -->
                        </div>
                    </div>

                    <!-- Category Performance Analysis -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Top Performing Categories -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Category Performance</h3>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-chart-pie mr-2"></i>
                                    Top Categories
                                </div>
                            </div>
                            <div id="categoryPerformance" class="space-y-4">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>

                        <!-- Reorder Recommendations -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">Reorder Recommendations</h3>
                                <div class="flex items-center text-sm text-gray-500">
                                    <i class="fas fa-shopping-cart mr-2"></i>
                                    Smart Suggestions
                                </div>
                            </div>
                            <div id="reorderRecommendations" class="space-y-4">
                                <!-- Will be populated by JavaScript -->
                            </div>
                        </div>
                    </div>

                    <!-- Advanced Charts Section -->
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                        <!-- Revenue Trend Analysis -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Revenue Analysis</h3>
                            <div id="revenueChart" class="h-64 flex items-center justify-center">
                                <canvas id="revenueChartCanvas"></canvas>
                            </div>
                        </div>

                        <!-- Category Distribution -->
                        <div class="bg-white dark:bg-gray-800 rounded-2xl p-6 shadow-lg">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Category Distribution</h3>
                            <div id="categoryChart" class="h-64 flex items-center justify-center">
                                <canvas id="categoryChartCanvas"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-2 p-4 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="exportAnalytics()" class="px-4 py-2 bg-blue-500 hover:bg-blue-600 text-white rounded-lg font-semibold text-xs transition-all duration-200 flex items-center">
                <i class="fas fa-download mr-1"></i>Export
            </button>
            <button onclick="hideAnalyticsModal()" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold text-xs transition-all duration-200">
                Close
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush
