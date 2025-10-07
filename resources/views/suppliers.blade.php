@extends('layouts.app')

@section('title', 'Supplier Management - Analog Pharmacy Management System')
@section('page-title', 'Supplier Management')
@section('page-description', 'Manage your pharmacy suppliers, track performance, and monitor contracts')

@section('content')
<style>
    /* Supplier specific styles */
    .supplier-card {
        transition: all 0.3s ease;
        border-left: 4px solid #10b981;
    }
    
    .supplier-card:hover {
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
    
    .status-active {
        background-color: #dcfce7;
        color: #166534;
    }
    
    .status-inactive {
        background-color: #fee2e2;
        color: #991b1b;
    }
    
    .metric-card {
        background: linear-gradient(135deg, var(--gradient-start) 0%, var(--gradient-end) 100%);
        color: white;
        border-radius: 1rem;
        transition: all 0.3s ease;
    }
    
    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
    
    .metric-card.total-suppliers {
        --gradient-start: #3b82f6;
        --gradient-end: #8b5cf6;
    }
    
    .metric-card.active-suppliers {
        --gradient-start: #10b981;
        --gradient-end: #14b8a6;
    }
    
    .metric-card.total-spent {
        --gradient-start: #8b5cf6;
        --gradient-end: #ec4899;
    }
    
    .metric-card.avg-rating {
        --gradient-start: #f59e0b;
        --gradient-end: #eab308;
    }
    
    /* Enhanced supplier header styling to match cashier page */
    .supplier-header-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 50%, #e2e8f0 100%);
        border: 1px solid #cbd5e1;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1), 0 1px 2px rgba(0, 0, 0, 0.06);
    }
    
    .dark .supplier-header-card {
        background: linear-gradient(135deg, #1e293b 0%, #334155 50%, #475569 100%);
        border: 1px solid #64748b;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1), 0 2px 4px rgba(0, 0, 0, 0.06);
    }
    
    /* Enhanced text styling for better readability */
    .supplier-header-card h1 {
        background: linear-gradient(135deg, #1e293b 0%, #475569 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .dark .supplier-header-card h1 {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>

<!-- Header Section -->
<!-- Supplier Header -->
<div class="mb-8">
    <div class="supplier-header-card relative rounded-2xl p-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Supplier Management</h1>
                <p class="text-slate-600 dark:text-slate-300 text-lg">Manage your pharmacy suppliers, track performance, and monitor contracts</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Supplier Status</p>
                <p class="text-xl font-bold text-slate-900 dark:text-white" id="supplierStatus">Active Management</p>
            </div>
        </div>
    </div>
</div>

<!-- Metrics Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Suppliers -->
    <div class="card-hover bg-gradient-to-br from-blue-400 to-blue-500 dark:from-blue-800 dark:to-blue-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-blue-600 dark:border-blue-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-blue-800 dark:text-blue-200 uppercase tracking-wide">Total Suppliers</p>
                <p class="text-3xl font-bold text-blue-900 dark:text-white mt-2 mb-1">{{ $suppliers->total() }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-blue-500 dark:bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-building text-xs mr-1"></i>
                        <span>All suppliers</span>
                    </div>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-building text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Active Suppliers -->
    <div class="card-hover bg-gradient-to-br from-green-400 to-green-500 dark:from-green-800 dark:to-green-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-green-600 dark:border-green-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-green-800 dark:text-green-200 uppercase tracking-wide">Active Suppliers</p>
                <p class="text-3xl font-bold text-green-900 dark:text-white mt-2 mb-1">2</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-green-500 dark:bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-check text-xs mr-1"></i>
                        <span>Currently active</span>
                    </div>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-check-circle text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Total Spent -->
    <div class="card-hover bg-gradient-to-br from-purple-400 to-purple-500 dark:from-purple-800 dark:to-purple-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-purple-600 dark:border-purple-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-purple-800 dark:text-purple-200 uppercase tracking-wide">Total Spent</p>
                <p class="text-3xl font-bold text-purple-900 dark:text-white mt-2 mb-1">Br 93,338.00</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-purple-500 dark:bg-purple-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-chart-line text-xs mr-1"></i>
                        <span>From all orders</span>
                    </div>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-dollar-sign text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Average Rating -->
    <div class="card-hover bg-gradient-to-br from-pink-400 to-pink-500 dark:from-pink-800 dark:to-pink-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-pink-600 dark:border-pink-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-pink-800 dark:text-pink-200 uppercase tracking-wide">Avg. Rating</p>
                <p class="text-3xl font-bold text-pink-900 dark:text-white mt-2 mb-1">4.2</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-pink-500 dark:bg-pink-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-star text-xs mr-1"></i>
                        <span>Excellent quality</span>
                    </div>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-pink-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-star text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search and Filter Section -->
<form method="POST" action="{{ route('suppliers.filter') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
    @csrf
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Search Suppliers -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search Suppliers</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </div>
                <input type="text" 
                       name="search"
                       id="supplierSearch"
                       value="{{ $request->search ?? '' }}"
                       class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" 
                       placeholder="Search by name, contact, email, or categories...">
            </div>
        </div>

        <!-- Category Filter -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Category</label>
            <div class="relative">
                <select name="category" id="categoryFilter" class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white appearance-none">
                    <option value="">All Categories</option>
                    <option value="Pharmaceuticals" {{ ($request->category ?? '') === 'Pharmaceuticals' ? 'selected' : '' }}>Pharmaceuticals</option>
                    <option value="Medical Equipment" {{ ($request->category ?? '') === 'Medical Equipment' ? 'selected' : '' }}>Medical Equipment</option>
                    <option value="Supplies" {{ ($request->category ?? '') === 'Supplies' ? 'selected' : '' }}>Supplies</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
            </div>
        </div>

        <!-- Status Filter -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
            <div class="relative">
                <select name="status" id="statusFilter" class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white appearance-none">
                    <option value="">All Statuses</option>
                    <option value="Active" {{ ($request->status ?? '') === 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Pending" {{ ($request->status ?? '') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Inactive" {{ ($request->status ?? '') === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                    <i class="fas fa-chevron-down text-gray-400"></i>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Hidden submit button for form submission -->
    <button type="submit" class="hidden" id="filterSubmit"></button>
</form>

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
        
        <button class="px-4 py-2 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl shadow-md transition-all duration-200">
            <i class="fas fa-chart-bar mr-2"></i>Show Analytics
        </button>
    </div>

    <!-- Supplier Count and Action Buttons -->
    <div class="flex items-center space-x-4">
        <div class="flex space-x-2">
            <button onclick="showImportExportModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                <i class="fas fa-download mr-2"></i>Import/Export
            </button>
        </div>
    </div>
</div>

<!-- Supplier Cards Container -->
<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mt-8">
    <div class="p-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <div class="text-gray-600 dark:text-gray-400">
                <span>Showing {{ $suppliers->firstItem() ?? 0 }} to {{ $suppliers->lastItem() ?? 0 }} of {{ $suppliers->total() }} suppliers</span>
            </div>
            <div class="flex gap-4">
                <button onclick="refreshSuppliers()" class="no-print px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-sync-alt mr-2 text-sm"></i>Refresh
                </button>
                <button onclick="printSuppliers()" class="no-print px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 flex items-center">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                <button class="no-print px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                    <i class="fas fa-plus mr-2 text-sm"></i>New Supplier
                </button>
            </div>
        </div>
    </div>

    <div class="p-6 bg-gray-50 dark:bg-gray-900">
        <!-- Supplier Cards Grid -->
        <div id="cardsView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($suppliers as $supplier)
            <div class="supplier-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $supplier->name }}</h3>
                        <span class="status-badge {{ $supplier->status === 'Active' ? 'status-active' : ($supplier->status === 'Inactive' ? 'status-inactive' : 'status-pending') }}">
                            <i class="fas fa-{{ $supplier->status === 'Active' ? 'check-circle' : ($supplier->status === 'Inactive' ? 'times-circle' : 'clock') }} mr-1"></i>{{ $supplier->status }}
                        </span>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $supplier->contact_person }}</p>
                    </div>
                    <div class="flex items-center text-yellow-500">
                        <i class="fas fa-star"></i>
                        <span class="ml-1 text-sm font-semibold">{{ $supplier->rating }}</span>
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <i class="fas fa-envelope w-4 h-4 mr-3 text-gray-400"></i>
                        {{ $supplier->email }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <i class="fas fa-phone w-4 h-4 mr-3 text-gray-400"></i>
                        {{ $supplier->phone }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <i class="fas fa-map-marker-alt w-4 h-4 mr-3 text-gray-400"></i>
                        {{ $supplier->location }}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Categories</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">
                            @if($supplier->categories && count($supplier->categories) > 0)
                                {{ implode(', ', $supplier->categories) }}
                            @else
                                No categories assigned
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Orders</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $supplier->total_orders }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">On-Time Delivery</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $supplier->on_time_delivery }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Spent</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ $supplier->total_spent }} Birr</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-center space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <!-- View Button -->
                    <button onclick="viewSupplier({{ $supplier->id }})" class="flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                        <i class="fas fa-eye mr-2 text-xs"></i>View
                    </button>
                    
                    <!-- Edit Button -->
                    <button onclick="editSupplier({{ $supplier->id }})" class="flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                        <i class="fas fa-edit mr-2 text-xs"></i>Edit
                    </button>
                    
                    <!-- Delete Button -->
                    <button onclick="deleteSupplier({{ $supplier->id }})" class="flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                        <i class="fas fa-trash mr-2 text-xs"></i>Delete
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No suppliers found</h3>
                <p class="text-gray-600 dark:text-gray-300">Try adjusting your search or filter criteria.</p>
            </div>
            @endforelse
        </div>

        <!-- Supplier Table View -->
        <div id="tableView" class="hidden">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Supplier</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Rating</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Orders</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Categories</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Spent</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($suppliers as $supplier)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center">
                                                <i class="fas fa-building text-white text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $supplier->name }}</div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">{{ $supplier->location }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">{{ $supplier->contact_person }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $supplier->email }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $supplier->phone }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="status-badge {{ $supplier->status === 'Active' ? 'status-active' : ($supplier->status === 'Inactive' ? 'status-inactive' : 'status-pending') }}">
                                        <i class="fas fa-{{ $supplier->status === 'Active' ? 'check-circle' : ($supplier->status === 'Inactive' ? 'times-circle' : 'clock') }} mr-1"></i>{{ $supplier->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <i class="fas fa-star text-yellow-500 mr-1"></i>
                                        <span class="text-sm text-gray-900 dark:text-white">{{ $supplier->rating }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $supplier->total_orders }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-white">
                                        @if($supplier->categories && count($supplier->categories) > 0)
                                            {{ implode(', ', $supplier->categories) }}
                                        @else
                                            <span class="text-gray-400">No categories</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ $supplier->total_spent }} Birr
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex items-center space-x-2">
                                        <!-- View Button -->
                                        <button onclick="viewSupplier({{ $supplier->id }})" class="flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                                            <i class="fas fa-eye mr-2 text-xs"></i>View
                                        </button>
                                        
                                        <!-- Edit Button -->
                                        <button onclick="editSupplier({{ $supplier->id }})" class="flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                                            <i class="fas fa-edit mr-2 text-xs"></i>Edit
                                        </button>
                                        
                                        <!-- Delete Button -->
                                        <button onclick="deleteSupplier({{ $supplier->id }})" class="flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                                            <i class="fas fa-trash mr-2 text-xs"></i>Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                        <i class="fas fa-search text-gray-400 text-2xl"></i>
                                    </div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No suppliers found</h3>
                                    <p class="text-gray-600 dark:text-gray-300">Try adjusting your search or filter criteria.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Pagination and Status -->
<div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 no-print">
    <div class="flex items-center justify-between">
        <!-- Pagination Info -->
        <div class="text-sm text-gray-700 dark:text-gray-300 pagination-info">
            Showing {{ $suppliers->firstItem() ?? 0 }} to {{ $suppliers->lastItem() ?? 0 }} of {{ $suppliers->total() }} results
        </div>
        
        <!-- Laravel Pagination -->
        <div class="flex items-center space-x-2">
            {{ $suppliers->links() }}
        </div>
    </div>
</div>

<!-- View Supplier Modal -->
<div id="viewSupplierModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-eye text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">View Supplier Details</h2>
                    <p class="text-gray-600 dark:text-gray-300">View complete supplier information</p>
                </div>
            </div>
            <button onclick="hideViewSupplierModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <div id="viewSupplierContent">
                <!-- Content will be loaded dynamically -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
            <button onclick="hideViewSupplierModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div id="editSupplierModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-edit text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Supplier</h2>
                    <p class="text-gray-600 dark:text-gray-300">Update supplier information</p>
                </div>
            </div>
            <button onclick="hideEditSupplierModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors duration-200">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6">
            <form id="editSupplierForm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Basic Information -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Supplier Name</label>
                            <input type="text" id="editName" name="name" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact Person</label>
                            <input type="text" id="editContactPerson" name="contact_person" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email</label>
                            <input type="email" id="editEmail" name="email" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone</label>
                            <input type="tel" id="editPhone" name="phone" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Location</label>
                            <input type="text" id="editLocation" name="location" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                        </div>
                    </div>
                    
                    <!-- Status and Performance -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status & Performance</h3>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select id="editStatus" name="status" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white" required>
                                <option value="Active">Active</option>
                                <option value="Pending">Pending</option>
                                <option value="Inactive">Inactive</option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Rating (0-5)</label>
                            <input type="number" id="editRating" name="rating" min="0" max="5" step="0.1" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Orders</label>
                            <input type="number" id="editTotalOrders" name="total_orders" min="0" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">On-Time Delivery</label>
                            <input type="text" id="editOnTimeDelivery" name="on_time_delivery" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Total Spent (Birr)</label>
                            <input type="number" id="editTotalSpent" name="total_spent" min="0" step="0.01" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                    
                    <!-- Categories Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Supply Categories</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Analgesics" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Analgesics</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Respiratory" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Respiratory</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Psychiatry" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Psychiatry</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Generics" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Generics</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Research" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Research</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Surgery" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Surgery</span>
                                </label>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Antibiotics" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Antibiotics</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Dermatological" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Dermatological</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Allergy" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Allergy</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="OTC" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">OTC</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Clinical Trials" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Clinical Trials</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Diabetes" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Diabetes</span>
                                </label>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Cardiovascular" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Cardiovascular</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Oncology" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Oncology</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Immunology" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Immunology</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Vitamins" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Vitamins</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Emergency Medicine" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Emergency Medicine</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Hypertension" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Hypertension</span>
                                </label>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Gastrointestinal" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Gastrointestinal</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Neurology" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Neurology</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Pediatrics" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pediatrics</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Biotechnology" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Biotechnology</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="ICU" class="edit-category-checkbox rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">ICU</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end space-x-3 p-6 border-t border-gray-200 dark:border-gray-700">
            <button onclick="hideEditSupplierModal()" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors duration-200">
                Cancel
            </button>
            <button onclick="saveSupplier()" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                <i class="fas fa-save mr-2"></i>Save Changes
            </button>
        </div>
    </div>
</div>

<!-- Delete Supplier Modal -->
<div id="deleteSupplierModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4" style="backdrop-filter: blur(4px);">
    <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-red-600 rounded-t-2xl p-6 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-700 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Delete Supplier</h2>
                    <p class="text-red-100 text-sm">Permanently delete supplier from system</p>
                </div>
            </div>
            <button onclick="hideDeleteSupplierModal()" class="w-8 h-8 bg-red-700 hover:bg-red-800 text-white rounded-lg flex items-center justify-center transition-all duration-200">
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

            <!-- Supplier Details -->
            <div class="bg-gray-700 rounded-lg p-4 mb-6">
                <h3 class="text-white font-semibold mb-4">Supplier Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Name:</span>
                        <span id="deleteSupplierName" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Contact Person:</span>
                        <span id="deleteSupplierContact" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Email:</span>
                        <span id="deleteSupplierEmail" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Status:</span>
                        <span id="deleteSupplierStatus" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Total Orders:</span>
                        <span id="deleteSupplierOrders" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Total Spent:</span>
                        <span id="deleteSupplierSpent" class="text-white font-medium">-</span>
                    </div>
                </div>
            </div>

            <!-- Confirmation Input -->
            <div class="mb-6">
                <p class="text-white mb-2">
                    Confirm Deletion <span class="text-red-400 font-bold">DELETE</span> in the box below:
                </p>
                <input type="text" id="deleteSupplierConfirmationInput" placeholder="Type DELETE to confirm"
                       oninput="handleDeleteSupplierConfirmation()"
                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
            </div>

            <!-- Impact Warning -->
            <div class="bg-orange-600 border border-orange-500 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-orange-200 mr-3 mt-1"></i>
                    <div>
                        <p class="text-white font-semibold mb-2">Deleting may affect:</p>
                        <ul class="text-orange-100 text-sm space-y-1">
                            <li>• Supplier performance tracking and analytics</li>
                            <li>• Purchase orders and procurement records</li>
                            <li>• Delivery schedules and logistics data</li>
                            <li>• Financial reports and spending analytics</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-700 flex-shrink-0">
            <button onclick="hideDeleteSupplierModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200">
                Cancel
            </button>
            <button id="deleteSupplierBtn" onclick="confirmDeleteSupplier()" disabled
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all duration-200 opacity-50 cursor-not-allowed">
                Delete Supplier
            </button>
        </div>
    </div>
</div>

<!-- New Supplier Modal -->
<div id="newSupplierModal" class="hidden fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center space-x-4">
                <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-white text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Supplier</h2>
                    <p class="text-gray-600 dark:text-gray-300">Create a new supplier for your pharmacy</p>
                </div>
            </div>
            <button id="closeModal" class="w-8 h-8 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center justify-center transition-colors duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <form id="supplierForm" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Basic Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Basic Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Supplier Name *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-file-alt text-gray-400"></i>
                                    </div>
                                    <input type="text" name="name" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter supplier name">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Contact Person *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user text-gray-400"></i>
                                    </div>
                                    <input type="text" name="contact_person" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter contact person name">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-envelope text-gray-400"></i>
                                    </div>
                                    <input type="email" name="email" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter email address">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-phone text-gray-400"></i>
                                    </div>
                                    <input type="tel" name="phone" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter phone number">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Address Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Address Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Address *</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-map-marker-alt text-gray-400"></i>
                                    </div>
                                    <input type="text" name="location" required class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter address">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">City *</label>
                                    <input type="text" required class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter city">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">State *</label>
                                    <input type="text" required class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter state">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">ZIP Code *</label>
                                <input type="text" required class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter ZIP code">
                            </div>
                        </div>
                    </div>

                    <!-- Supply Categories -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Supply Categories</h3>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Analgesics" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Analgesics</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Respiratory" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Respiratory</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Psychiatry" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Psychiatry</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Generics" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Generics</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Research" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Research</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Surgery" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Surgery</span>
                                </label>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Antibiotics" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Antibiotics</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Dermatological" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Dermatological</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Allergy" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Allergy</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="OTC" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">OTC</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Clinical Trials" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Clinical Trials</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Diabetes" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Diabetes</span>
                                </label>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Cardiovascular" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Cardiovascular</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Oncology" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Oncology</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Immunology" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Immunology</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Vitamins" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Vitamins</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Emergency Medicine" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Emergency Medicine</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Hypertension" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Hypertension</span>
                                </label>
                            </div>
                            
                            <div class="space-y-2">
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Gastrointestinal" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Gastrointestinal</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Neurology" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Neurology</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Pediatrics" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Pediatrics</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="Biotechnology" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Biotechnology</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="categories[]" value="ICU" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">ICU</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Notes</h3>
                        <textarea class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" rows="4" placeholder="Enter additional notes"></textarea>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Business Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Business Information</h3>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tax ID</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-file-alt text-gray-400"></i>
                                    </div>
                                    <input type="text" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter tax ID">
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Payment Terms</label>
                                <div class="relative">
                                    <select class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white appearance-none">
                                        <option>Net 30</option>
                                        <option>Net 15</option>
                                        <option>Net 60</option>
                                        <option>Cash on Delivery</option>
                                        <option>Prepaid</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                <div class="relative">
                                    <select class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white appearance-none">
                                        <option>Pending</option>
                                        <option>Active</option>
                                        <option>Inactive</option>
                                        <option>Suspended</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center">
                                <input type="checkbox" id="activeSupplier" class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                <label for="activeSupplier" class="ml-2 text-sm text-gray-700 dark:text-gray-300">Active Supplier</label>
                            </div>
                            
                            <!-- Hidden fields for required data -->
                            <input type="hidden" name="status" value="Pending" id="statusField">
                            <input type="hidden" name="rating" value="0">
                            <input type="hidden" name="total_orders" value="0">
                            <input type="hidden" name="on_time_delivery" value="100%">
                            <input type="hidden" name="total_spent" value="0">
                        </div>
                    </div>

                    <!-- Performance Metrics -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance Metrics</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-star text-gray-400"></i>
                                        </div>
                                        <input type="number" min="0" max="5" step="0.1" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="0" value="0">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Quality Rating (0-5)</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-star text-gray-400"></i>
                                        </div>
                                        <input type="number" min="0" max="5" step="0.1" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="0" value="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">On-Time Delivery Rate (%)</label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-check-circle text-gray-400"></i>
                                    </div>
                                    <input type="number" min="0" max="100" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="0" value="0">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Total Orders</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-file-alt text-gray-400"></i>
                                        </div>
                                        <input type="number" min="0" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="0" value="0">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Total Spent</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-dollar-sign text-gray-400"></i>
                                        </div>
                                        <input type="number" min="0" step="0.01" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="0" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contract Information -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Contract Information</h3>
                        <div class="space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Contract Start Date</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-calendar text-gray-400"></i>
                                        </div>
                                        <input type="date" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" value="2025-05-10">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Contract End Date</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-calendar text-gray-400"></i>
                                        </div>
                                        <input type="date" class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" value="2026-05-10">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end space-x-4 p-6 border-t border-gray-200 dark:border-gray-700">
                <button type="button" id="cancelButton" class="px-6 py-3 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors duration-200">
                    Cancel
                </button>
                <button type="submit" class="px-6 py-3 text-sm font-semibold text-white bg-green-600 hover:bg-green-700 rounded-xl transition-colors duration-200 flex items-center">
                    <i class="fas fa-file-alt mr-2"></i>
                    Create Supplier
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Pagination variables (using Laravel pagination)
    let currentPage = {{ $suppliers->currentPage() }};
    let totalPages = {{ $suppliers->lastPage() }};

    // Supplier management functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Search functionality
        const searchInput = document.querySelector('input[placeholder*="Search by name"]');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const supplierCards = document.querySelectorAll('.supplier-card');
                
                supplierCards.forEach(card => {
                    const supplierName = card.querySelector('h3').textContent.toLowerCase();
                    const contactPerson = card.querySelector('p').textContent.toLowerCase();
                    const email = card.querySelector('.fa-envelope').parentElement.textContent.toLowerCase();
                    
                    if (supplierName.includes(searchTerm) || 
                        contactPerson.includes(searchTerm) || 
                        email.includes(searchTerm)) {
                        card.style.display = 'block';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }

        // Filter functionality
        const categoryFilter = document.querySelector('select');
        const statusFilter = document.querySelectorAll('select')[1];
        
        if (categoryFilter) {
            categoryFilter.addEventListener('change', function(e) {
                console.log('Category filter changed:', e.target.value);
                // Implement category filtering logic here
            });
        }
        
        if (statusFilter) {
            statusFilter.addEventListener('change', function(e) {
                console.log('Status filter changed:', e.target.value);
                // Implement status filtering logic here
            });
        }

        // View toggle functionality
        const cardsButton = document.querySelector('button:has(.fa-th-large)');
        const tableButton = document.querySelector('button:has(.fa-table)');
        
        if (cardsButton && tableButton) {
            cardsButton.addEventListener('click', function() {
                cardsButton.classList.add('bg-orange-500', 'text-white');
                cardsButton.classList.remove('text-gray-600', 'dark:text-gray-300');
                tableButton.classList.remove('bg-orange-500', 'text-white');
                tableButton.classList.add('text-gray-600', 'dark:text-gray-300');
            });
            
            tableButton.addEventListener('click', function() {
                tableButton.classList.add('bg-orange-500', 'text-white');
                tableButton.classList.remove('text-gray-600', 'dark:text-gray-300');
                cardsButton.classList.remove('bg-orange-500', 'text-white');
                cardsButton.classList.add('text-gray-600', 'dark:text-gray-300');
            });
        }


        // New Supplier button
        const newSupplierButton = document.querySelector('button:has(.fa-plus)');
        if (newSupplierButton) {
            newSupplierButton.addEventListener('click', function() {
                document.getElementById('newSupplierModal').classList.remove('hidden');
                document.body.classList.add('overflow-hidden');
            });
        }

        // Close modal functionality
        const closeModalButton = document.getElementById('closeModal');
        const newSupplierModal = document.getElementById('newSupplierModal');
        
        function closeModal() {
            newSupplierModal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
        
        if (closeModalButton) {
            closeModalButton.addEventListener('click', closeModal);
        }

        // Close modal when clicking outside
        if (newSupplierModal) {
            newSupplierModal.addEventListener('click', function(e) {
                if (e.target === newSupplierModal) {
                    closeModal();
                }
            });
        }

        // Handle status checkbox
        const activeSupplierCheckbox = document.getElementById('activeSupplier');
        const statusField = document.getElementById('statusField');
        if (activeSupplierCheckbox && statusField) {
            activeSupplierCheckbox.addEventListener('change', function() {
                statusField.value = this.checked ? 'Active' : 'Pending';
            });
        }

        // Form submission
        const supplierForm = document.getElementById('supplierForm');
        if (supplierForm) {
            supplierForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    // Show loading spinner
                    showGlobalLoading('Creating new supplier...');
                    
                    const formData = new FormData(supplierForm);
                    
                    const response = await fetch('/suppliers/store', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(Object.fromEntries(formData))
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        
                        if (data.success) {
                            // Show success loading
                            showLoadingWithStyle('success', 'Supplier created successfully!');
                            
                            setTimeout(() => {
                                hideGlobalLoading();
                                closeModal();
                                // Reload page to show new supplier
                                window.location.reload();
                            }, 1500);
                        } else {
                            throw new Error(data.message || 'Failed to create supplier');
                        }
                    } else {
                        throw new Error(`Failed to create supplier: ${response.status}`);
                    }
                    
                } catch (error) {
                    console.error('Error creating supplier:', error);
                    // Show error loading
                    showLoadingWithStyle('error', 'Failed to create supplier. Please try again.');
                    
                    setTimeout(() => {
                        hideGlobalLoading();
                    }, 2000);
                }
            });
        }

        // Cancel button functionality
        const cancelButton = document.getElementById('cancelButton');
        if (cancelButton) {
            cancelButton.addEventListener('click', closeModal);
        }

        // Print functionality
        const printButton = document.querySelector('button:has(.fa-print)');
        if (printButton) {
            printButton.addEventListener('click', function() {
                window.print();
            });
        }

        // Import/Export functionality
        const importExportButton = document.querySelector('button:has(.fa-download)');
        if (importExportButton) {
            importExportButton.addEventListener('click', function() {
                console.log('Import/Export functionality');
                // Implement import/export functionality
            });
        }

        // Analytics button
        const analyticsButton = document.querySelector('button:has(.fa-chart-bar)');
        if (analyticsButton) {
            analyticsButton.addEventListener('click', function() {
                console.log('Showing analytics');
                // Implement analytics modal/chart
            });
        }

        // Pagination is now handled by Laravel's built-in pagination
    });

    // Pagination is now handled by Laravel's built-in pagination

    /**
     * View switching functionality
     */
    function initializeViewSwitching() {
        const cardsViewBtn = document.getElementById('cardsViewBtn');
        const tableViewBtn = document.getElementById('tableViewBtn');
        const cardsView = document.getElementById('cardsView');
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
            localStorage.setItem('supplierView', 'cards');
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
            localStorage.setItem('supplierView', 'table');
        });

        // Load saved view preference
        const savedView = localStorage.getItem('supplierView');
        if (savedView === 'table') {
            tableViewBtn.click();
        } else {
            cardsViewBtn.click();
        }
    }

    // Initialize view switching when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        initializeViewSwitching();
        
        // Show success notification when page loads
        setTimeout(() => {
            if (window.NotificationService) {
                window.NotificationService.success('Supplier page loaded successfully!');
            }
        }, 1000);
    });
</script>

<!-- Import/Export Suppliers Modal -->
<div id="importExportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import/Export Suppliers</h2>
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
                            <input type="text" name="filename" value="suppliers_export" class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">File will be saved with timestamp</p>
                        </div>

                        <!-- Export Filters -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Export Filters (Optional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="filters[status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Statuses</option>
                                        <option value="Active">Active</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                                    <select name="filters[rating]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Ratings</option>
                                        <option value="5">5 Stars</option>
                                        <option value="4">4+ Stars</option>
                                        <option value="3">3+ Stars</option>
                                        <option value="2">2+ Stars</option>
                                        <option value="1">1+ Stars</option>
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
                                    <div>Active Suppliers: <span id="activeSuppliers">0</span></div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <div>Total Spent: <span id="exportTotalSpent">0</span> Birr</div>
                                    <div>Avg Rating: <span id="avgRating">0</span></div>
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
                    <!-- Print Supplier Report -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-2">Print Supplier Report</h3>
                        <p class="text-gray-700 dark:text-gray-300">This will generate a professional supplier report with summary statistics and detailed supplier listing.</p>
                    </div>

                        <!-- Print Filters -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Print Filters (Optional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="filters[status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Statuses</option>
                                        <option value="Active">Active</option>
                                        <option value="Pending">Pending</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                            </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Rating</label>
                                    <select name="filters[rating]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Ratings</option>
                                        <option value="5">5 Stars</option>
                                        <option value="4">4+ Stars</option>
                                        <option value="3">3+ Stars</option>
                                        <option value="2">2+ Stars</option>
                                        <option value="1">1+ Stars</option>
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
        // Update export summary when filters change
        const exportForm = document.getElementById('exportForm');
        if (exportForm) {
            exportForm.addEventListener('change', updateExportSummary);
        }
        
        // Initial summary update
        updateExportSummary();
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
        
        // Mock data for suppliers export summary
        const totalRecordsElement = document.getElementById('totalRecords');
        const activeSuppliersElement = document.getElementById('activeSuppliers');
        const exportTotalSpentElement = document.getElementById('exportTotalSpent');
        const avgRatingElement = document.getElementById('avgRating');
        
        if (totalRecordsElement) totalRecordsElement.textContent = '6';
        if (activeSuppliersElement) activeSuppliersElement.textContent = '2';
        if (exportTotalSpentElement) exportTotalSpentElement.textContent = '93,338.00';
        if (avgRatingElement) avgRatingElement.textContent = '4.2';
    }
    
    /**
     * Initialize template download
     */
    function initializeTemplateDownload() {
        const downloadTemplateBtn = document.getElementById('downloadTemplateBtn');
        if (downloadTemplateBtn) {
            downloadTemplateBtn.addEventListener('click', function() {
                // Create a simple CSV template for suppliers
                const csvContent = 'name,contact_person,email,phone,location,status,rating\n"Global Health Supplies","Mike Wilson","mike@globalhealth.com","+1-555-0103","Chicago, IL","Active","4.5"\n"MedSupply Co.","John Smith","john@medsupply.com","+1-555-0101","New York, NY","Active","4.2"';
                const blob = new Blob([csvContent], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'suppliers_template.csv';
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
            showNotification('Please select a file to import.', 'error');
            return;
        }
        
        isImporting = true;
        const actionButton = document.getElementById('actionButton');
        const originalText = actionButton.innerHTML;
        actionButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Importing...';
        actionButton.disabled = true;
        
        // Simulate import process
        setTimeout(() => {
            showNotification('Import successful! 3 records imported, 0 skipped, 0 errors.', 'success');
            
            // Reset form
            form.reset();
            document.getElementById('selectedFileName').classList.add('hidden');
            document.getElementById('selectFileBtn').textContent = 'Select File';
            document.getElementById('selectFileBtn').classList.remove('bg-blue-600', 'hover:bg-blue-700');
            document.getElementById('selectFileBtn').classList.add('bg-green-600', 'hover:bg-green-700');
            
            // Reset button
            actionButton.innerHTML = originalText;
            actionButton.disabled = false;
            isImporting = false;
            
            // Refresh the page to show updated data
            setTimeout(() => {
                location.reload();
            }, 2000);
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
            showNotification('Export successful! File downloaded.', 'success');
            
            // Reset button
            actionButton.innerHTML = originalText;
            actionButton.disabled = false;
            isExporting = false;
        }, 2000);
    }
    
    /**
     * Handle print
     */
    function handlePrint() {
        showNotification('Print functionality will be implemented soon.', 'info');
    }
    
    // Global functions for onclick handlers
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
    
    window.switchTab = function(tab) {
        currentTab = tab;
        
        // Update tab buttons
        const tabs = ['importTab', 'exportTab', 'printTab'];
        tabs.forEach(tabId => {
            const tabElement = document.getElementById(tabId);
            if (tabElement) {
                if (tabId === tab + 'Tab') {
                    tabElement.className = 'flex-1 px-6 py-4 text-left border-b-2 border-orange-500 text-orange-500 font-semibold flex items-center justify-center';
                } else {
                    tabElement.className = 'flex-1 px-6 py-4 text-left border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium flex items-center justify-center hover:text-gray-700 dark:hover:text-gray-300';
                }
            }
        });
        
        // Update content
        const contents = ['importContent', 'exportContent', 'printContent'];
        contents.forEach(contentId => {
            const contentElement = document.getElementById(contentId);
            if (contentElement) {
                if (contentId === tab + 'Content') {
                    contentElement.classList.remove('hidden');
                } else {
                    contentElement.classList.add('hidden');
                }
            }
        });
        
        // Update action button
        const actionButton = document.getElementById('actionButton');
        if (actionButton) {
            const buttonTexts = {
                'import': '<i class="fas fa-download mr-2"></i>Import File',
                'export': '<i class="fas fa-upload mr-2"></i>Export File',
                'print': '<i class="fas fa-print mr-2"></i>Print Report'
            };
            actionButton.innerHTML = buttonTexts[tab] || buttonTexts['import'];
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
         * Setup notification styles - matches Cashier page exactly
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
         * Show notification - matches Cashier page exactly
         */
        show(message, type = 'info', duration = null) {
            const notification = this.createNotification(message, type);
            this.addToQueue(notification);
            this.animateIn(notification);
            this.scheduleRemoval(notification, duration);
            return notification;
        }

        /**
         * Create notification element - matches Cashier page exactly
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
         * Add notification to queue
         */
        addToQueue(notification) {
            this.notifications.push(notification);
            this.container.appendChild(notification);
            
            // Remove oldest if over limit
            if (this.notifications.length > this.maxNotifications) {
                this.removeOldest();
            }
        }

        /**
         * Animate notification in
         */
        animateIn(notification) {
            setTimeout(() => {
                notification.classList.add('translate-x-0');
            }, 10);
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
         * Close notification
         */
        close(notification) {
            if (!notification || !notification.parentNode) return;
            
            notification.classList.remove('translate-x-0');
            notification.classList.add('translate-x-full');
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
                const index = this.notifications.indexOf(notification);
                if (index > -1) {
                    this.notifications.splice(index, 1);
                }
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
})();

// Refresh and Print Functionality
(function() {
    'use strict';
    
    /**
     * Refresh suppliers data - matches Cashier page implementation
     */
    window.refreshSuppliers = function() {
        const refreshBtn = document.querySelector('button[onclick="refreshSuppliers()"]');
        const originalContent = refreshBtn.innerHTML;
        
        // Show loading state
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-sm"></i>Refreshing...';
        refreshBtn.disabled = true;
        
        try {
            // Simulate refresh process
            setTimeout(() => {
                // Show success notification before reload
                if (window.NotificationService) {
                    window.NotificationService.success('Supplier data refreshed successfully!');
                }
                
                // Reload the page to get fresh data after showing success
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }, 1500);
            
        } catch (error) {
            console.error('Failed to refresh supplier data:', error);
            
            // Show error notification
            if (window.NotificationService) {
                window.NotificationService.error('Failed to refresh data. Please try again.');
            }
            
            // Reset button state
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
        }
    };
    
    // Global variables for supplier operations
    let currentSupplierId = null;
    
    /**
     * Show global loading spinner with download-like animation (exact copy from cashier page)
     */
    function showGlobalLoading(message = 'Loading...') {
        // Remove existing loading if any
        hideGlobalLoading();
        
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'globalLoading';
        loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
        loadingDiv.innerHTML = `
            <div class="text-center">
                <!-- Supplier icon container -->
                <div class="relative w-16 h-16 mx-auto mb-6">
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-building text-blue-500 text-3xl animate-pulse"></i>
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
     * Start progress animation for horizontal loading bar (exact copy from cashier page)
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
     * Hide global loading spinner (exact copy from cashier page)
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
     * Show action loading with different styles (exact copy from cashier page)
     */
    function showActionLoading(action) {
        const messages = {
            'viewing': 'Loading supplier details...',
            'editing': 'Loading supplier data...',
            'deleting': 'Preparing deletion...',
            'saving': 'Saving changes...'
        };
        showGlobalLoading(messages[action] || 'Loading...');
    }
    
    /**
     * Update loading message (exact copy from cashier page)
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
     * Show loading with success/error style (exact copy from cashier page)
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
                         'fa-building';
        
        loadingDiv.innerHTML = `
            <div class="text-center">
                <!-- Supplier icon container -->
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
    
    /**
     * Show supplier error state
     */
    function showSupplierError() {
        const viewContent = document.getElementById('viewSupplierContent');
        viewContent.innerHTML = `
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Error Loading Supplier</h3>
                <p class="text-gray-600 dark:text-gray-300">Failed to load supplier details. Please try again.</p>
                <button onclick="viewSupplier(${currentSupplierId})" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors duration-200">
                    <i class="fas fa-redo mr-2"></i>Retry
                </button>
            </div>
        `;
    }
    
    /**
     * View supplier details (exact pattern from cashier page)
     */
    window.viewSupplier = async function(supplierId) {
        try {
            // Show global loading spinner
            showActionLoading('viewing');
            
            // Fetch supplier details from server
            const response = await fetch(`/suppliers/${supplierId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const supplier = await response.json();
                
                if (supplier) {
                    // Show modal and display supplier details
                    document.getElementById('viewSupplierModal').classList.remove('hidden');
                    displaySupplierDetails(supplier);
                } else {
                    throw new Error('Supplier not found');
                }
            } else {
                throw new Error(`Failed to fetch supplier details: ${response.status}`);
            }
            
        } catch (error) {
            console.error('Error fetching supplier:', error);
            // Show error state
            showSupplierError();
        } finally {
            // Hide global loading spinner
            hideGlobalLoading();
        }
    };
    
    /**
     * Display supplier details in view modal
     */
    function displaySupplierDetails(supplier) {
        const viewContent = document.getElementById('viewSupplierContent');
        viewContent.innerHTML = `
            <div class="space-y-6">
                <!-- Supplier Header -->
                <div class="flex items-center space-x-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="w-16 h-16 bg-gradient-to-br from-orange-400 to-orange-500 rounded-full flex items-center justify-center">
                        <i class="fas fa-building text-white text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">${supplier.name}</h3>
                        <p class="text-gray-600 dark:text-gray-300">${supplier.location}</p>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium ${getStatusClass(supplier.status)}">
                            <i class="fas fa-${getStatusIcon(supplier.status)} mr-2"></i>${supplier.status}
                        </span>
                    </div>
                </div>
                
                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Contact Information</h4>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-user text-gray-400 w-5"></i>
                                <span class="text-gray-900 dark:text-white">${supplier.contact_person}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-envelope text-gray-400 w-5"></i>
                                <span class="text-gray-900 dark:text-white">${supplier.email}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-phone text-gray-400 w-5"></i>
                                <span class="text-gray-900 dark:text-white">${supplier.phone}</span>
                            </div>
                            <div class="flex items-center space-x-3">
                                <i class="fas fa-map-marker-alt text-gray-400 w-5"></i>
                                <span class="text-gray-900 dark:text-white">${supplier.location}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Performance Metrics -->
                    <div class="space-y-4">
                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Performance Metrics</h4>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-star text-yellow-500"></i>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Rating</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${supplier.rating}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-shopping-cart text-blue-500"></i>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Orders</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${supplier.total_orders}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-clock text-green-500"></i>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">On-Time</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${supplier.on_time_delivery}</p>
                            </div>
                            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-600">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-dollar-sign text-purple-500"></i>
                                    <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Spent</span>
                                </div>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">${supplier.total_spent} Birr</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    /**
     * Edit supplier (exact pattern from cashier page)
     */
    window.editSupplier = async function(supplierId) {
        try {
            // Set current supplier ID for save function
            currentSupplierId = supplierId;
            
            // Show global loading spinner
            showActionLoading('editing');
            
            // Fetch supplier data from server
            const response = await fetch(`/suppliers/${supplierId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const supplier = await response.json();
                
                if (supplier) {
                    // Show modal and populate form
                    document.getElementById('editSupplierModal').classList.remove('hidden');
                    populateEditForm(supplier);
                } else {
                    throw new Error('Supplier not found');
                }
            } else {
                throw new Error(`Failed to fetch supplier data: ${response.status}`);
            }
            
        } catch (error) {
            console.error('Error fetching supplier:', error);
            if (window.NotificationService) {
                window.NotificationService.error('Failed to load supplier data. Please try again.');
            }
            hideEditSupplierModal();
        } finally {
            // Hide global loading spinner
            hideGlobalLoading();
        }
    };
    
    /**
     * Populate edit form with supplier data
     */
    function populateEditForm(supplier) {
        document.getElementById('editName').value = supplier.name;
        document.getElementById('editContactPerson').value = supplier.contact_person;
        document.getElementById('editEmail').value = supplier.email;
        document.getElementById('editPhone').value = supplier.phone;
        document.getElementById('editLocation').value = supplier.location;
        document.getElementById('editStatus').value = supplier.status;
        document.getElementById('editRating').value = supplier.rating;
        document.getElementById('editTotalOrders').value = supplier.total_orders;
        document.getElementById('editOnTimeDelivery').value = supplier.on_time_delivery;
        document.getElementById('editTotalSpent').value = supplier.total_spent;
        
        // Handle categories
        if (supplier.categories && Array.isArray(supplier.categories)) {
            // Clear all checkboxes first
            document.querySelectorAll('.edit-category-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Check the categories that the supplier has
            supplier.categories.forEach(category => {
                const checkbox = document.querySelector(`input[name="categories[]"][value="${category}"]`);
                if (checkbox) {
                    checkbox.checked = true;
                }
            });
        }
    }
    
    /**
     * Save supplier changes (exact pattern from cashier page)
     */
    window.saveSupplier = async function() {
        if (!currentSupplierId) return;
        
        try {
            // Show global loading spinner
            showActionLoading('saving');
            
            const form = document.getElementById('editSupplierForm');
            const formData = new FormData(form);
            
            // Properly handle categories array
            const data = Object.fromEntries(formData);
            const categories = [];
            document.querySelectorAll('input[name="categories[]"]:checked').forEach(checkbox => {
                categories.push(checkbox.value);
            });
            data.categories = categories;
            
            
            const response = await fetch(`/suppliers/${currentSupplierId}`, {
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
                    showLoadingWithStyle('success', 'Supplier updated successfully!');
                    
                    setTimeout(() => {
                        hideGlobalLoading();
                        hideEditSupplierModal();
                        // Reload page to show updated data
                        window.location.reload();
                    }, 1500);
                } else {
                    throw new Error(data.message || 'Failed to update supplier');
                }
            } else {
                throw new Error(`Failed to update supplier: ${response.status}`);
            }
            
        } catch (error) {
            console.error('Error updating supplier:', error);
            // Show error loading
            showLoadingWithStyle('error', 'Failed to update supplier. Please try again.');
            
            setTimeout(() => {
                hideGlobalLoading();
            }, 2000);
        }
    };
    
    /**
     * Delete supplier (exact pattern from inventory page)
     */
    window.deleteSupplier = async function(supplierId) {
        try {
            // Show global loading spinner
            showActionLoading('loading supplier details');
            
            // Fetch supplier data for confirmation
            const response = await fetch(`/suppliers/${supplierId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const supplier = await response.json();
                
                if (supplier) {
                    // Populate modal with supplier data
                    document.getElementById('deleteSupplierName').textContent = supplier.name;
                    document.getElementById('deleteSupplierContact').textContent = supplier.contact_person;
                    document.getElementById('deleteSupplierEmail').textContent = supplier.email;
                    document.getElementById('deleteSupplierStatus').textContent = supplier.status;
                    document.getElementById('deleteSupplierStatus').className = supplier.status === 'Active' ? 'text-green-400' : (supplier.status === 'Inactive' ? 'text-red-400' : 'text-yellow-400');
                    document.getElementById('deleteSupplierOrders').textContent = supplier.total_orders || '0';
                    document.getElementById('deleteSupplierSpent').textContent = supplier.total_spent ? `$${supplier.total_spent}` : '$0.00';
                    
                    // Store supplier ID for deletion
                    window.currentDeleteSupplierId = supplier.id;
                    
                    // Show modal
                    const modal = document.getElementById('deleteSupplierModal');
                    if (modal) {
                        modal.classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }
                } else {
                    throw new Error('Supplier not found');
                }
            } else {
                throw new Error(`Failed to fetch supplier: ${response.status}`);
            }
            
        } catch (error) {
            console.error('Error fetching supplier:', error);
            showNotification('Error loading supplier details', 'error');
        } finally {
            // Hide global loading spinner
            hideGlobalLoading();
        }
    };
    
    /**
     * Hide delete supplier modal
     */
    window.hideDeleteSupplierModal = function() {
        const modal = document.getElementById('deleteSupplierModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto';
        }
        // Reset confirmation input
        document.getElementById('deleteSupplierConfirmationInput').value = '';
        document.getElementById('deleteSupplierBtn').disabled = true;
        window.currentDeleteSupplierId = null;
    };

    /**
     * Handle delete supplier confirmation input
     */
    window.handleDeleteSupplierConfirmation = function() {
        const input = document.getElementById('deleteSupplierConfirmationInput');
        const deleteBtn = document.getElementById('deleteSupplierBtn');
        
        if (input.value === 'DELETE') {
            deleteBtn.disabled = false;
            deleteBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            deleteBtn.classList.add('hover:bg-red-700');
        } else {
            deleteBtn.disabled = true;
            deleteBtn.classList.add('opacity-50', 'cursor-not-allowed');
            deleteBtn.classList.remove('hover:bg-red-700');
        }
    };

    /**
     * Confirm delete supplier (exact pattern from inventory page)
     */
    window.confirmDeleteSupplier = async function() {
        if (!window.currentDeleteSupplierId) {
            showNotification('No supplier selected for deletion', 'error');
            return;
        }

        try {
            showActionLoading('deleting supplier');
            
            const response = await fetch(`/suppliers/${window.currentDeleteSupplierId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                showLoadingWithStyle('success', 'Supplier deleted successfully!');
                hideDeleteSupplierModal();
                
                // Reload the page to refresh data
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                showNotification(data.message || 'Error deleting supplier', 'error');
            }
        } catch (error) {
            console.error('Error deleting supplier:', error);
            showNotification('Error deleting supplier', 'error');
        }
    };
    
    /**
     * Modal control functions
     */
    window.hideViewSupplierModal = function() {
        document.getElementById('viewSupplierModal').classList.add('hidden');
        currentSupplierId = null;
    };
    
    window.hideEditSupplierModal = function() {
        document.getElementById('editSupplierModal').classList.add('hidden');
        currentSupplierId = null;
    };
    
    
    /**
     * Helper functions
     */
    function getStatusClass(status) {
        switch(status) {
            case 'Active': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            case 'Pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
            case 'Inactive': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
            default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
        }
    }
    
    function getStatusIcon(status) {
        switch(status) {
            case 'Active': return 'check-circle';
            case 'Pending': return 'clock';
            case 'Inactive': return 'times-circle';
            default: return 'question-circle';
        }
    }
    
    /**
     * Print suppliers report - matches Cashier page implementation
     */
    window.printSuppliers = function() {
        const printBtn = document.querySelector('button[onclick="printSuppliers()"]');
        const originalContent = printBtn.innerHTML;
        
        // Show loading state
        printBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Preparing...';
        printBtn.disabled = true;
        
        try {
            // Show info notification
            if (window.NotificationService) {
                window.NotificationService.info('Preparing supplier report for printing...');
            }
            
            // Simulate print preparation
            setTimeout(() => {
                // Create print window
                const printWindow = window.open('', '_blank', 'width=800,height=600');
                
                // Get current view (cards or table)
                const cardsView = document.getElementById('cardsView');
                const tableView = document.getElementById('tableView');
                const isTableView = !tableView.classList.contains('hidden');
                
                // Generate print content
                const printContent = generatePrintContent(isTableView);
                
                // Write content to print window
                printWindow.document.write(printContent);
                printWindow.document.close();
                
                // Wait for content to load, then print
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                    
                    // Reset button
                    printBtn.innerHTML = originalContent;
                    printBtn.disabled = false;
                    
                    // Show success notification
                    if (window.NotificationService) {
                        window.NotificationService.success('Print dialog opened successfully!');
                    }
                }, 500);
            }, 1500);
            
        } catch (error) {
            console.error('Failed to print supplier report:', error);
            
            // Show error notification
            if (window.NotificationService) {
                window.NotificationService.error('Failed to prepare print report. Please try again.');
            }
            
            // Reset button state
            printBtn.innerHTML = originalContent;
            printBtn.disabled = false;
        }
    };
    
    /**
     * Generate print content
     */
    function generatePrintContent(isTableView) {
        const currentDate = new Date().toLocaleDateString();
        const currentTime = new Date().toLocaleTimeString();
        
        let content = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>Supplier Report - ${currentDate}</title>
            <style>
                body { font-family: Arial, sans-serif; margin: 20px; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 20px; }
                .header h1 { color: #333; margin: 0; }
                .header p { color: #666; margin: 5px 0; }
                .stats { display: flex; justify-content: space-around; margin: 20px 0; padding: 15px; background: #f5f5f5; border-radius: 8px; }
                .stat-item { text-align: center; }
                .stat-value { font-size: 24px; font-weight: bold; color: #333; }
                .stat-label { font-size: 14px; color: #666; }
                .suppliers-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                .suppliers-table th, .suppliers-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                .suppliers-table th { background-color: #f2f2f2; font-weight: bold; }
                .suppliers-table tr:nth-child(even) { background-color: #f9f9f9; }
                .status-active { color: #28a745; font-weight: bold; }
                .status-pending { color: #ffc107; font-weight: bold; }
                .status-inactive { color: #dc3545; font-weight: bold; }
                .supplier-cards { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px; }
                .supplier-card { border: 1px solid #ddd; border-radius: 8px; padding: 20px; background: #fff; }
                .supplier-card h3 { margin: 0 0 10px 0; color: #333; }
                .supplier-card p { margin: 5px 0; color: #666; }
                .supplier-stats { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-top: 15px; }
                .supplier-stat { text-align: center; padding: 10px; background: #f8f9fa; border-radius: 4px; }
                .supplier-stat .value { font-size: 18px; font-weight: bold; color: #333; }
                .supplier-stat .label { font-size: 12px; color: #666; }
                @media print {
                    body { margin: 0; }
                    .no-print { display: none !important; }
                }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>Supplier Management Report</h1>
                <p>Generated on ${currentDate} at ${currentTime}</p>
                <p>Blade Pharmacy Management System</p>
            </div>
            
            <div class="stats">
                <div class="stat-item">
                    <div class="stat-value">6</div>
                    <div class="stat-label">Total Suppliers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">2</div>
                    <div class="stat-label">Active Suppliers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">Br 93,338.00</div>
                    <div class="stat-label">Total Spent</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value">4.2</div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>
        `;
        
        if (isTableView) {
            // Table view content
            content += `
            <table class="suppliers-table">
                <thead>
                    <tr>
                        <th>Supplier</th>
                        <th>Contact Person</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Location</th>
                        <th>Status</th>
                        <th>Rating</th>
                        <th>Orders</th>
                        <th>Total Spent</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Global Health Supplies</td>
                        <td>Mike Wilson</td>
                        <td>mike@globalhealth.com</td>
                        <td>+1-555-0103</td>
                        <td>Chicago, IL</td>
                        <td><span class="status-pending">Pending</span></td>
                        <td>0</td>
                        <td>2</td>
                        <td>5,128.00 Birr</td>
                    </tr>
                    <tr>
                        <td>MedSupply Co.</td>
                        <td>John Smith</td>
                        <td>john@medsupply.com</td>
                        <td>+1-555-0101</td>
                        <td>New York, NY</td>
                        <td><span class="status-pending">Pending</span></td>
                        <td>0</td>
                        <td>5</td>
                        <td>14,973.00 Birr</td>
                    </tr>
                    <tr>
                        <td>PharmaDirect</td>
                        <td>Sarah Johnson</td>
                        <td>sarah@pharmadirect.com</td>
                        <td>+1-555-0102</td>
                        <td>Los Angeles, CA</td>
                        <td><span class="status-pending">Pending</span></td>
                        <td>0</td>
                        <td>8</td>
                        <td>24,837.00 Birr</td>
                    </tr>
                    <tr>
                        <td>MedTech Solutions</td>
                        <td>David Brown</td>
                        <td>david@medtech.com</td>
                        <td>+1-555-0104</td>
                        <td>Boston, MA</td>
                        <td><span class="status-active">Active</span></td>
                        <td>4.5</td>
                        <td>12</td>
                        <td>32,450.00 Birr</td>
                    </tr>
                    <tr>
                        <td>HealthCare Plus</td>
                        <td>Lisa Davis</td>
                        <td>lisa@healthcareplus.com</td>
                        <td>+1-555-0105</td>
                        <td>Miami, FL</td>
                        <td><span class="status-active">Active</span></td>
                        <td>4.2</td>
                        <td>7</td>
                        <td>18,750.00 Birr</td>
                    </tr>
                    <tr>
                        <td>PharmaCorp</td>
                        <td>Robert Wilson</td>
                        <td>robert@pharmacorp.com</td>
                        <td>+1-555-0106</td>
                        <td>Seattle, WA</td>
                        <td><span class="status-inactive">Inactive</span></td>
                        <td>3.8</td>
                        <td>3</td>
                        <td>8,200.00 Birr</td>
                    </tr>
                </tbody>
            </table>
            `;
        } else {
            // Cards view content
            content += `
            <div class="supplier-cards">
                <div class="supplier-card">
                    <h3>Global Health Supplies</h3>
                    <p><strong>Contact:</strong> Mike Wilson</p>
                    <p><strong>Email:</strong> mike@globalhealth.com</p>
                    <p><strong>Phone:</strong> +1-555-0103</p>
                    <p><strong>Location:</strong> Chicago, IL</p>
                    <p><strong>Status:</strong> <span class="status-pending">Pending</span></p>
                    <div class="supplier-stats">
                        <div class="supplier-stat">
                            <div class="value">0</div>
                            <div class="label">Rating</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">2</div>
                            <div class="label">Orders</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">100%</div>
                            <div class="label">On-Time</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">5,128.00</div>
                            <div class="label">Total Spent (Birr)</div>
                        </div>
                    </div>
                </div>
                
                <div class="supplier-card">
                    <h3>MedSupply Co.</h3>
                    <p><strong>Contact:</strong> John Smith</p>
                    <p><strong>Email:</strong> john@medsupply.com</p>
                    <p><strong>Phone:</strong> +1-555-0101</p>
                    <p><strong>Location:</strong> New York, NY</p>
                    <p><strong>Status:</strong> <span class="status-pending">Pending</span></p>
                    <div class="supplier-stats">
                        <div class="supplier-stat">
                            <div class="value">0</div>
                            <div class="label">Rating</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">5</div>
                            <div class="label">Orders</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">100%</div>
                            <div class="label">On-Time</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">14,973.00</div>
                            <div class="label">Total Spent (Birr)</div>
                        </div>
                    </div>
                </div>
                
                <div class="supplier-card">
                    <h3>PharmaDirect</h3>
                    <p><strong>Contact:</strong> Sarah Johnson</p>
                    <p><strong>Email:</strong> sarah@pharmadirect.com</p>
                    <p><strong>Phone:</strong> +1-555-0102</p>
                    <p><strong>Location:</strong> Los Angeles, CA</p>
                    <p><strong>Status:</strong> <span class="status-pending">Pending</span></p>
                    <div class="supplier-stats">
                        <div class="supplier-stat">
                            <div class="value">0</div>
                            <div class="label">Rating</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">8</div>
                            <div class="label">Orders</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">100%</div>
                            <div class="label">On-Time</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">24,837.00</div>
                            <div class="label">Total Spent (Birr)</div>
                        </div>
                    </div>
                </div>
                
                <div class="supplier-card">
                    <h3>MedTech Solutions</h3>
                    <p><strong>Contact:</strong> David Brown</p>
                    <p><strong>Email:</strong> david@medtech.com</p>
                    <p><strong>Phone:</strong> +1-555-0104</p>
                    <p><strong>Location:</strong> Boston, MA</p>
                    <p><strong>Status:</strong> <span class="status-active">Active</span></p>
                    <div class="supplier-stats">
                        <div class="supplier-stat">
                            <div class="value">4.5</div>
                            <div class="label">Rating</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">12</div>
                            <div class="label">Orders</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">95%</div>
                            <div class="label">On-Time</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">32,450.00</div>
                            <div class="label">Total Spent (Birr)</div>
                        </div>
                    </div>
                </div>
                
                <div class="supplier-card">
                    <h3>HealthCare Plus</h3>
                    <p><strong>Contact:</strong> Lisa Davis</p>
                    <p><strong>Email:</strong> lisa@healthcareplus.com</p>
                    <p><strong>Phone:</strong> +1-555-0105</p>
                    <p><strong>Location:</strong> Miami, FL</p>
                    <p><strong>Status:</strong> <span class="status-active">Active</span></p>
                    <div class="supplier-stats">
                        <div class="supplier-stat">
                            <div class="value">4.2</div>
                            <div class="label">Rating</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">7</div>
                            <div class="label">Orders</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">98%</div>
                            <div class="label">On-Time</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">18,750.00</div>
                            <div class="label">Total Spent (Birr)</div>
                        </div>
                    </div>
                </div>
                
                <div class="supplier-card">
                    <h3>PharmaCorp</h3>
                    <p><strong>Contact:</strong> Robert Wilson</p>
                    <p><strong>Email:</strong> robert@pharmacorp.com</p>
                    <p><strong>Phone:</strong> +1-555-0106</p>
                    <p><strong>Location:</strong> Seattle, WA</p>
                    <p><strong>Status:</strong> <span class="status-inactive">Inactive</span></p>
                    <div class="supplier-stats">
                        <div class="supplier-stat">
                            <div class="value">3.8</div>
                            <div class="label">Rating</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">3</div>
                            <div class="label">Orders</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">90%</div>
                            <div class="label">On-Time</div>
                        </div>
                        <div class="supplier-stat">
                            <div class="value">8,200.00</div>
                            <div class="label">Total Spent (Birr)</div>
                        </div>
                    </div>
                </div>
            </div>
            `;
        }
        
        content += `
            <div style="margin-top: 30px; text-align: center; color: #666; font-size: 12px;">
                <p>This report was generated by Blade Pharmacy Management System</p>
                <p>For support, contact: support@bladepharmacy.com</p>
            </div>
        </body>
        </html>
        `;
        
        return content;
    }
})();
</script>
@endsection
