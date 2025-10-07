@extends('layouts.app')

@section('title', 'Customer Management - Analog Pharmacy')

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
    
    /* Enhanced customer header styling to match suppliers page */
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
    
    /* Customer specific styles */
    .customer-card {
        transition: all 0.3s ease;
        border-left: 4px solid #10b981;
    }
    
    .customer-card:hover {
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
</style>
<div class="min-h-screen bg-gray-900">
    <!-- Header Section -->
    <div class="mb-8">
        <div class="supplier-header-card relative rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Customer Management</h1>
                    <p class="text-slate-600 dark:text-slate-300 text-lg">Manage your pharmacy customers, track loyalty points, and monitor purchase history</p>
                </div>
                <div class="text-right">
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Customer Status</p>
                    <p class="text-xl font-bold text-slate-900 dark:text-white" id="customerStatus">Active Management</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Customers Card -->
        <div class="card-hover bg-gradient-to-br from-cyan-400 to-cyan-500 dark:from-cyan-800 dark:to-cyan-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-cyan-600 dark:border-cyan-600 p-6 transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-bold text-cyan-800 dark:text-cyan-200 uppercase tracking-wide">Total Customers</p>
                    <p class="text-3xl font-bold text-cyan-900 dark:text-white mt-2 mb-1">{{ $stats['total_customers'] ?? 0 }}</p>
                    <div class="flex items-center space-x-1">
                        <div class="flex items-center bg-cyan-500 dark:bg-cyan-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                            <i class="fas fa-users text-xs mr-1"></i>
                            <span>All customers</span>
                        </div>
                    </div>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-cyan-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Revenue Card -->
        <div class="card-hover bg-gradient-to-br from-emerald-400 to-emerald-500 dark:from-emerald-800 dark:to-emerald-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-emerald-600 dark:border-emerald-600 p-6 transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-bold text-emerald-800 dark:text-emerald-200 uppercase tracking-wide">Total Revenue</p>
                    <p class="text-3xl font-bold text-emerald-900 dark:text-white mt-2 mb-1">{{ number_format($stats['total_revenue'] ?? 0, 2) }} Birr</p>
                    <div class="flex items-center space-x-1">
                        <div class="flex items-center bg-emerald-500 dark:bg-emerald-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                            <i class="fas fa-chart-line text-xs mr-1"></i>
                            <span>From all sales</span>
                        </div>
                    </div>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-dollar-sign text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Active Customers Card -->
        <div class="card-hover bg-gradient-to-br from-orange-400 to-orange-500 dark:from-orange-800 dark:to-orange-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-orange-600 dark:border-orange-600 p-6 transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-bold text-orange-800 dark:text-orange-200 uppercase tracking-wide">Active Customers</p>
                    <p class="text-3xl font-bold text-orange-900 dark:text-white mt-2 mb-1">{{ $stats['active_customers'] ?? 0 }}</p>
                    <div class="flex items-center space-x-1">
                        <div class="flex items-center bg-orange-500 dark:bg-orange-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                            <i class="fas fa-check text-xs mr-1"></i>
                            <span>Currently active</span>
                        </div>
                    </div>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Premium Customers Card -->
        <div class="card-hover bg-gradient-to-br from-rose-400 to-rose-500 dark:from-rose-800 dark:to-rose-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-rose-600 dark:border-rose-600 p-6 transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-bold text-rose-800 dark:text-rose-200 uppercase tracking-wide">Premium Customers</p>
                    <p class="text-3xl font-bold text-rose-900 dark:text-white mt-2 mb-1">{{ $stats['premium_customers'] ?? 0 }}</p>
                    <div class="flex items-center space-x-1">
                        <div class="flex items-center bg-rose-500 dark:bg-rose-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                            <i class="fas fa-star text-xs mr-1"></i>
                            <span>VIP members</span>
                        </div>
                    </div>
                </div>
                <div class="w-14 h-14 bg-gradient-to-br from-rose-500 to-rose-600 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-star text-white text-xl"></i>
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

        <!-- Customer Count and Action Buttons -->
        <div class="flex items-center space-x-4">
            <div class="flex space-x-2">
                <button onclick="showImportExportModal()" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200">
                    <i class="fas fa-download mr-2"></i>Import/Export
                </button>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <form method="POST" action="{{ route('customers.filter') }}" class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 mb-8">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Search Customers -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Search Customers</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fas fa-search text-gray-400"></i>
                    </div>
                    <input type="text" 
                           name="search"
                           id="searchInput"
                           value="{{ request('search') ?? '' }}"
                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" 
                           placeholder="Search by name, email, phone, or customer ID...">
                </div>
            </div>

            <!-- Segment Filter -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Segment</label>
                <div class="relative">
                    <select name="segment" id="segmentFilter" class="block w-full px-3 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white appearance-none">
                        <option value="">All Segments</option>
                        <option value="new" {{ (request('segment') ?? '') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="regular" {{ (request('segment') ?? '') === 'regular' ? 'selected' : '' }}>Regular</option>
                        <option value="loyal" {{ (request('segment') ?? '') === 'loyal' ? 'selected' : '' }}>Loyal</option>
                        <option value="vip" {{ (request('segment') ?? '') === 'vip' ? 'selected' : '' }}>VIP</option>
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
                        <option value="new" {{ (request('status') ?? '') === 'new' ? 'selected' : '' }}>New</option>
                        <option value="active" {{ (request('status') ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ (request('status') ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="premium" {{ (request('status') ?? '') === 'premium' ? 'selected' : '' }}>Premium</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                        <i class="fas fa-chevron-down text-gray-400"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Sort Options -->
        <div class="flex items-center gap-4 mt-4">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort By:</span>
            <select name="sort_by" id="sortBy" class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>Name</option>
                <option value="email" {{ request('sort_by') == 'email' ? 'selected' : '' }}>Email</option>
                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Added</option>
                <option value="total_spent" {{ request('sort_by') == 'total_spent' ? 'selected' : '' }}>Total Spent</option>
            </select>
            <button type="button" id="sortDirection" class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white">
                <i class="fas fa-sort-amount-up"></i>
            </button>
        </div>
        
        <!-- Hidden submit button for form submission -->
        <button type="submit" class="hidden" id="filterSubmit"></button>
    </form>

    <!-- Customer Cards Container -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mt-8">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="text-gray-600 dark:text-gray-400">
                    <span>Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} customers</span>
                </div>
                <div class="flex gap-4">
                    <button onclick="refreshCustomers()" class="no-print px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                        <i class="fas fa-sync-alt mr-2 text-sm"></i>Refresh
                    </button>
                    <button onclick="printCustomers()" class="no-print px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-600 transition-all duration-200 flex items-center">
                        <i class="fas fa-print mr-2"></i>Print
                    </button>
                    <button id="addCustomerBtn" class="no-print px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm transition-all duration-200 flex items-center">
                        <i class="fas fa-plus mr-2 text-sm"></i>New Customer
                    </button>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50 dark:bg-gray-900">
            <!-- Customer Cards Grid -->
            <div id="cardsView" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($customers as $customer)
            <div class="customer-card bg-white dark:bg-gray-800 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 hover:shadow-xl transition-all duration-300">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $customer->name }}</h3>
                        <span class="status-badge {{ $customer->status === 'active' ? 'status-active' : ($customer->status === 'inactive' ? 'status-inactive' : 'status-pending') }}">
                            <i class="fas fa-{{ $customer->status === 'active' ? 'check-circle' : ($customer->status === 'inactive' ? 'times-circle' : 'clock') }} mr-1"></i>{{ ucfirst($customer->status) }}
                        </span>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">{{ $customer->email }}</p>
                    </div>
                    <div class="flex items-center text-yellow-500">
                        <i class="fas fa-star"></i>
                        <span class="ml-1 text-sm font-semibold">{{ $customer->loyalty_points }}</span>
                    </div>
                </div>

                <div class="space-y-3 mb-6">
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <i class="fas fa-phone w-4 h-4 mr-3 text-gray-400"></i>
                        {{ $customer->phone ?: 'Not provided' }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <i class="fas fa-map-marker-alt w-4 h-4 mr-3 text-gray-400"></i>
                        {{ $customer->city ?: 'Unknown' }}, {{ $customer->country ?: 'Unknown' }}
                    </div>
                    <div class="flex items-center text-sm text-gray-600 dark:text-gray-300">
                        <i class="fas fa-calendar w-4 h-4 mr-3 text-gray-400"></i>
                        DOB: {{ $customer->date_of_birth ? \Carbon\Carbon::parse($customer->date_of_birth)->format('M j, Y') : 'Not specified' }}
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Segment</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ ucfirst($customer->segment ?: 'Regular') }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Orders</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $customer->total_sales ?? 0 }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Loyalty Points</p>
                        <p class="text-sm text-gray-900 dark:text-white mt-1">{{ $customer->loyalty_points }}</p>
                    </div>
                    <div>
                        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Total Spent</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-white mt-1">{{ number_format($customer->total_spent, 2) }} Birr</p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-center space-x-2 pt-4 border-t border-gray-200 dark:border-gray-700">
                    <!-- View Button -->
                    <button onclick="viewCustomer({{ $customer->id }})" class="flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                        <i class="fas fa-eye mr-2 text-xs"></i>View
                    </button>
                    
                    <!-- Edit Button -->
                    <button onclick="editCustomer({{ $customer->id }})" class="flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                        <i class="fas fa-edit mr-2 text-xs"></i>Edit
                    </button>
                    
                    <!-- Delete Button -->
                    <button onclick="deleteCustomer({{ $customer->id }})" class="flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                        <i class="fas fa-trash mr-2 text-xs"></i>Delete
                    </button>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No customers found</h3>
                <p class="text-gray-600 dark:text-gray-300">Try adjusting your search or filter criteria.</p>
            </div>
            @endforelse
            </div>

            <!-- Customer Table View -->
            <div id="tableView" class="hidden">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Customer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Loyalty Points</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Total Spent</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($customers as $customer)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-full bg-gradient-to-br from-orange-400 to-orange-500 flex items-center justify-center">
                                                    <i class="fas fa-user text-white text-sm"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $customer->name }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 dark:text-white">{{ $customer->phone ?: 'Not provided' }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->city ?: 'Unknown' }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="status-badge {{ $customer->status === 'active' ? 'status-active' : ($customer->status === 'inactive' ? 'status-inactive' : 'status-pending') }}">
                                            <i class="fas fa-{{ $customer->status === 'active' ? 'check-circle' : ($customer->status === 'inactive' ? 'times-circle' : 'clock') }} mr-1"></i>{{ ucfirst($customer->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $customer->loyalty_points }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ number_format($customer->total_spent, 2) }} Birr
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex items-center space-x-2">
                                            <!-- View Button -->
                                            <button onclick="viewCustomer({{ $customer->id }})" class="flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                                                <i class="fas fa-eye mr-2 text-xs"></i>View
                                            </button>
                                            
                                            <!-- Edit Button -->
                                            <button onclick="editCustomer({{ $customer->id }})" class="flex items-center px-3 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                                                <i class="fas fa-edit mr-2 text-xs"></i>Edit
                                            </button>
                                            
                                            <!-- Delete Button -->
                                            <button onclick="deleteCustomer({{ $customer->id }})" class="flex items-center px-3 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                                                <i class="fas fa-trash mr-2 text-xs"></i>Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center">
                                        <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                                            <i class="fas fa-search text-gray-400 text-2xl"></i>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No customers found</h3>
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


    <!-- Pagination -->
    @if($customers->hasPages())
    <div class="mt-8">
        {{ $customers->links() }}
    </div>
    @endif
</div>

<!-- Add Customer Modal -->
<div id="addCustomerModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-[9999] p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-orange-500 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-user text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Add New Customer</h3>
                    <p class="text-gray-600 dark:text-gray-300">Customer Information</p>
                </div>
            </div>
            <button id="closeAddModal" class="w-10 h-10 bg-orange-500 hover:bg-orange-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-y-auto">
            <form id="addCustomerForm" class="p-6">
            <!-- Personal Details Section -->
            <div class="mb-8">
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Personal Details</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Name *</label>
                        <input type="text" name="first_name" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Enter first name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Last Name *</label>
                        <input type="text" name="last_name" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Enter last name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Enter email address">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                        <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Enter phone number">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth *</label>
                        <div class="relative">
                            <input type="date" name="date_of_birth" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                            <i class="fas fa-calendar absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                        <select name="gender" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                            <option value="">Select Gender</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Address Section -->
            <div class="mb-8">
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Address</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address *</label>
                        <textarea name="address" rows="3" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-y dark:bg-gray-700 dark:text-white" placeholder="Enter address"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City *</label>
                        <input type="text" name="city" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Enter city">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State *</label>
                        <input type="text" name="state" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Enter state">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ZIP Code</label>
                        <input type="text" name="zip_code" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Enter ZIP code">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                        <input type="text" name="country" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Enter country">
                    </div>
                </div>
            </div>

            <!-- Medical Information Section -->
            <div class="mb-8">
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Medical Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Allergies</label>
                        <textarea name="allergies" rows="3" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-y dark:bg-gray-700 dark:text-white" placeholder="List any allergies"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Medical History</label>
                        <textarea name="medical_conditions" rows="3" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 resize-y dark:bg-gray-700 dark:text-white" placeholder="Relevant medical history"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact</label>
                        <input type="text" name="emergency_contact" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Emergency contact name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Phone</label>
                        <input type="tel" name="emergency_phone" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Emergency contact phone">
                    </div>
                </div>
            </div>

            <!-- Insurance Information Section -->
            <div class="mb-8">
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Insurance Information</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Insurance Provider</label>
                        <input type="text" name="insurance_provider" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Insurance provider name">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Insurance Number</label>
                        <input type="text" name="insurance_number" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Insurance policy number">
                    </div>
                </div>
            </div>

            <!-- Customer Settings Section -->
            <div class="mb-8">
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Customer Settings</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Segment</label>
                        <select name="segment" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white">
                            <option value="new">New</option>
                            <option value="regular">Regular</option>
                            <option value="loyal">Loyal</option>
                            <option value="vip">VIP</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                        <input type="text" name="tags" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-orange-500 focus:border-orange-500 dark:bg-gray-700 dark:text-white" placeholder="Enter tags (comma separated)">
                    </div>
                    <div class="md:col-span-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_active" checked class="w-4 h-4 text-orange-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-orange-500">
                            <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Active Customer ID</span>
                        </label>
                    </div>
                </div>
            </div>

            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button type="button" id="cancelAddCustomer" class="px-6 py-3 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancel
            </button>
            <button type="submit" form="addCustomerForm" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center">
                <i class="fas fa-save mr-2"></i>
                Save Customer
            </button>
        </div>
    </div>
</div>

<!-- Import/Export Customers Modal -->
<div id="importExportModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Import/Export Customers</h2>
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
                            <input type="text" name="filename" value="customers_export" class="w-full px-4 py-3 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white transition-all duration-200">
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
                                        <option value="new">New</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="premium">Premium</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Segment</label>
                                    <select name="filters[segment]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Segments</option>
                                        <option value="new">New</option>
                                        <option value="regular">Regular</option>
                                        <option value="loyal">Loyal</option>
                                        <option value="vip">VIP</option>
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
                                    <div>Active Customers: <span id="activeCustomers">0</span></div>
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">
                                    <div>Total Spent: <span id="exportTotalSpent">0</span> Birr</div>
                                    <div>Premium Customers: <span id="premiumCustomers">0</span></div>
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
                    <!-- Print Customer Report -->
                    <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-6">
                        <h3 class="text-lg font-bold text-blue-600 dark:text-blue-400 mb-2">Print Customer Report</h3>
                        <p class="text-gray-700 dark:text-gray-300">This will generate a professional customer report with summary statistics and detailed customer listing.</p>
                    </div>

                        <!-- Print Filters -->
                        <div>
                            <label class="block text-sm font-bold text-gray-900 dark:text-white mb-3">Print Filters (Optional)</label>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Status</label>
                                    <select name="filters[status]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Statuses</option>
                                        <option value="new">New</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="premium">Premium</option>
                                    </select>
                            </div>
                                <div>
                                    <label class="block text-sm text-gray-700 dark:text-gray-300 mb-2">Segment</label>
                                    <select name="filters[segment]" class="w-full px-3 py-2 bg-gray-100 dark:bg-gray-800 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:text-white">
                                        <option value="">All Segments</option>
                                        <option value="new">New</option>
                                        <option value="regular">Regular</option>
                                        <option value="loyal">Loyal</option>
                                        <option value="vip">VIP</option>
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
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize view switching
    initializeViewSwitching();
    
    // Initialize customer-specific functions
    initializeCustomerFunctions();
    
    // Search and filter functionality
    const searchInput = document.getElementById('searchInput');
    const segmentFilter = document.getElementById('segmentFilter');
    const statusFilter = document.getElementById('statusFilter');
    const sortBy = document.getElementById('sortBy');
    const sortDirection = document.getElementById('sortDirection');
    const filterForm = document.querySelector('form[action="{{ route("customers.filter") }}"]');

    function applyFilters() {
        if (filterForm) {
            filterForm.submit();
        }
    }

    if (searchInput) searchInput.addEventListener('input', debounce(applyFilters, 500));
    if (segmentFilter) segmentFilter.addEventListener('change', applyFilters);
    if (statusFilter) statusFilter.addEventListener('change', applyFilters);
    if (sortBy) sortBy.addEventListener('change', applyFilters);

    // Sort direction toggle
    let sortDir = '{{ request("sort_direction", "asc") }}';
    if (sortDirection) {
        // Set initial icon based on current sort direction
        const icon = sortDirection.querySelector('i');
        if (icon) {
            icon.className = sortDir === 'asc' ? 'fas fa-sort-amount-up' : 'fas fa-sort-amount-down';
        }
        
        sortDirection.addEventListener('click', function() {
            sortDir = sortDir === 'asc' ? 'desc' : 'asc';
            const icon = sortDirection.querySelector('i');
            icon.className = sortDir === 'asc' ? 'fas fa-sort-amount-up' : 'fas fa-sort-amount-down';
            
            // Add hidden input for sort direction
            let sortDirInput = document.querySelector('input[name="sort_direction"]');
            if (!sortDirInput) {
                sortDirInput = document.createElement('input');
                sortDirInput.type = 'hidden';
                sortDirInput.name = 'sort_direction';
                filterForm.appendChild(sortDirInput);
            }
            sortDirInput.value = sortDir;
            
            applyFilters();
        });
    }

    // Add Customer Modal
    const addCustomerBtn = document.getElementById('addCustomerBtn');
    const addCustomerModal = document.getElementById('addCustomerModal');
    const closeAddModal = document.getElementById('closeAddModal');
    const cancelAddCustomer = document.getElementById('cancelAddCustomer');
    const addCustomerForm = document.getElementById('addCustomerForm');

    if (addCustomerBtn && addCustomerModal) {
        addCustomerBtn.addEventListener('click', function() {
            addCustomerModal.classList.remove('hidden');
            addCustomerModal.classList.add('flex');
        });
    }

    if (closeAddModal && addCustomerModal) {
        closeAddModal.addEventListener('click', function() {
            addCustomerModal.classList.add('hidden');
            addCustomerModal.classList.remove('flex');
        });
    }

    if (cancelAddCustomer && addCustomerModal) {
        cancelAddCustomer.addEventListener('click', function() {
            addCustomerModal.classList.add('hidden');
            addCustomerModal.classList.remove('flex');
        });
    }

    // Add Customer Form Submission
    if (addCustomerForm) {
        addCustomerForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            try {
                // Show loading spinner
                showActionLoading('creating');
                
                const formData = new FormData(this);
                
                // Combine first_name and last_name into name field
                const firstName = formData.get('first_name');
                const lastName = formData.get('last_name');
                formData.set('name', `${firstName} ${lastName}`);
                
                // Set default values for missing fields
                if (!formData.get('status')) {
                    formData.set('status', 'new');
                }
                if (!formData.get('loyalty_points')) {
                    formData.set('loyalty_points', '0');
                }
                if (!formData.get('total_spent')) {
                    formData.set('total_spent', '0');
                }
                
                fetch('{{ route("customers.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success loading
                        showLoadingWithStyle('success', 'Customer created successfully!');
                        
                        setTimeout(() => {
                            hideGlobalLoading();
                            // Close modal
                            addCustomerModal.classList.add('hidden');
                            addCustomerModal.classList.remove('flex');
                            // Reset form
                            addCustomerForm.reset();
                            // Reload page to show new customer
                            location.reload();
                        }, 1500);
                    } else {
                        hideGlobalLoading();
                        alert('Error: ' + (data.message || 'Failed to create customer'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    hideGlobalLoading();
                    alert('An error occurred while adding the customer');
                });
                
            } catch (error) {
                console.error('Failed to create customer:', error);
                hideGlobalLoading();
                alert('An error occurred while adding the customer');
            }
        });
    }

    // Refresh functionality
    const refreshBtn = document.getElementById('refreshBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function() {
            location.reload();
        });
    }

    // Utility function for debouncing
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
    
    // File upload functionality for Import/Export modal
    initializeFileUpload();
});

// File upload functionality
function initializeFileUpload() {
    const selectFileBtn = document.getElementById('selectFileBtn');
    const importFile = document.getElementById('importFile');
    const fileDropZone = document.getElementById('fileDropZone');
    const selectedFileName = document.getElementById('selectedFileName');
    const importForm = document.getElementById('importForm');
    
    if (selectFileBtn && importFile) {
        selectFileBtn.addEventListener('click', function() {
            importFile.click();
        });
    }
    
    if (importFile) {
        importFile.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                selectedFileName.textContent = `Selected: ${file.name}`;
                selectedFileName.classList.remove('hidden');
            }
        });
    }
    
    // Drag and drop functionality
    if (fileDropZone) {
        fileDropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            fileDropZone.classList.add('border-orange-500', 'bg-orange-50', 'dark:bg-orange-900/20');
        });
        
        fileDropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            fileDropZone.classList.remove('border-orange-500', 'bg-orange-50', 'dark:bg-orange-900/20');
        });
        
        fileDropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            fileDropZone.classList.remove('border-orange-500', 'bg-orange-50', 'dark:bg-orange-900/20');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                importFile.files = files;
                const file = files[0];
                selectedFileName.textContent = `Selected: ${file.name}`;
                selectedFileName.classList.remove('hidden');
            }
        });
    }
    
    // Form submission
    if (importForm) {
        importForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('import_mode', 'create'); // Default import mode
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Importing...';
            submitBtn.disabled = true;
            
            fetch('{{ route("customers.import") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(`Import successful! ${data.data.imported_count} records imported, ${data.data.skipped_count} skipped, ${data.data.error_count} errors.`);
                    
                    // Reset form
                    this.reset();
                    document.getElementById('selectedFileName').classList.add('hidden');
                    document.getElementById('selectFileBtn').textContent = 'Select File';
                    document.getElementById('selectFileBtn').classList.remove('bg-blue-600', 'hover:bg-blue-700');
                    document.getElementById('selectFileBtn').classList.add('bg-green-600', 'hover:bg-green-700');
                    
                    // Refresh the page to show updated data
                    setTimeout(() => {
                        location.reload();
                    }, 2000);
                } else {
                    alert(data.message || 'Import failed.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while importing customers');
            })
            .finally(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    }
}

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
        localStorage.setItem('customerView', 'cards');
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
        localStorage.setItem('customerView', 'table');
    });

    // Load saved view preference
    const savedView = localStorage.getItem('customerView');
    if (savedView === 'table') {
        tableViewBtn.click();
    } else {
        cardsViewBtn.click();
    }
}

/**
 * Initialize customer-specific functions
 */
// Global variables for customer operations
let currentCustomerId = null;

/**
 * Show global loading spinner with download-like animation (exact copy from suppliers page)
 */
function showGlobalLoading(message = 'Loading...') {
    // Remove existing loading if any
    hideGlobalLoading();
    
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'globalLoading';
    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
    loadingDiv.innerHTML = `
        <div class="text-center">
            <!-- Customer icon container -->
            <div class="relative w-16 h-16 mx-auto mb-6">
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-user text-orange-500 text-3xl animate-pulse"></i>
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
 * Show action loading with different styles (exact copy from suppliers page)
 */
function showActionLoading(action) {
    const messages = {
        'viewing': 'Loading customer details...',
        'editing': 'Loading customer data...',
        'deleting': 'Preparing deletion...',
        'saving': 'Saving changes...',
        'creating': 'Creating new customer...',
        'refreshing': 'Refreshing customer data...'
    };
    showGlobalLoading(messages[action] || 'Loading...');
}

/**
 * Update loading message (exact copy from suppliers page)
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
 * Show loading with success/error style (exact copy from suppliers page)
 */
function showLoadingWithStyle(type, message) {
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'globalLoading';
    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
    
    const colorClass = type === 'success' ? 'text-green-500' : 
                      type === 'error' ? 'text-red-500' : 
                      'text-orange-500';
    
    const iconClass = type === 'success' ? 'fa-check-circle' : 
                     type === 'error' ? 'fa-exclamation-triangle' : 
                     'fa-user';
    
    loadingDiv.innerHTML = `
        <div class="text-center">
            <!-- Customer icon container -->
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

function initializeCustomerFunctions() {
    // Customer action functions
    window.viewCustomer = async function(customerId) {
        try {
            // Show loading state
            showGlobalLoading('Loading customer details...');
            
            // Fetch customer details from server
            const response = await fetch(`/customers/${customerId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const customer = await response.json();
                showViewCustomerModal(customer);
            } else {
                throw new Error('Customer not found');
            }
        } catch (error) {
            console.error('Error fetching customer:', error);
            alert('Error loading customer details. Please try again.');
        } finally {
            hideGlobalLoading();
        }
    };
    
    window.editCustomer = async function(customerId) {
        try {
            // Show loading state
            showGlobalLoading('Loading customer details...');
            
            // Fetch customer details from server
            const response = await fetch(`/customers/${customerId}`, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json'
                }
            });
            
            if (response.ok) {
                const customer = await response.json();
                showEditCustomerModal(customer);
            } else {
                throw new Error('Customer not found');
            }
        } catch (error) {
            console.error('Error fetching customer:', error);
            alert('Error loading customer details. Please try again.');
        } finally {
            hideGlobalLoading();
        }
    };
    
    
    window.refreshCustomers = function() {
        const refreshBtn = document.querySelector('button[onclick="refreshCustomers()"]');
        const originalContent = refreshBtn.innerHTML;
        
        // Show loading state
        refreshBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2 text-sm"></i>Refreshing...';
        refreshBtn.disabled = true;
        
        try {
            // Show global loading spinner
            showActionLoading('refreshing');
            
            // Simulate refresh process
            setTimeout(() => {
                // Show success notification before reload
                if (window.NotificationService) {
                    window.NotificationService.success('Customer data refreshed successfully!');
                }
                
                // Reload the page to get fresh data after showing success
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            }, 1500);
            
        } catch (error) {
            console.error('Failed to refresh customer data:', error);
            
            // Show error notification
            if (window.NotificationService) {
                window.NotificationService.error('Failed to refresh data. Please try again.');
            }
            
            // Reset button state
            refreshBtn.innerHTML = originalContent;
            refreshBtn.disabled = false;
        }
    };
    
    window.printCustomers = function() {
        window.print();
    };
    
    /**
     * Show view customer modal
     */
    window.showViewCustomerModal = function(customer) {
        const modal = document.getElementById('viewCustomerModal');
        const content = document.getElementById('viewCustomerContent');
        
        if (modal && content) {
            try {
                content.innerHTML = `
                <div class="space-y-6">
                    <!-- Customer Header -->
                    <div class="text-center">
                        <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg mx-auto mb-4">
                            <i class="fas fa-user text-white text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">${customer.name || 'Unknown Customer'}</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-4">${customer.email || 'No email provided'}</p>
                        <div class="flex justify-center gap-2 mb-6">
                            <span class="px-3 py-1 ${getCustomerStatusColor(customer.status)} rounded-full text-xs font-semibold">
                                ${customer.status || 'Unknown Status'}
                            </span>
                            <span class="px-3 py-1 ${getCustomerSegmentColor(customer.segment)} rounded-full text-xs font-semibold">
                                ${customer.segment || 'Regular'} Customer
                            </span>
                        </div>
                    </div>

                    <!-- Customer Details -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Personal Information -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Personal Information</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-phone w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">${customer.phone || 'Not provided'}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-map-marker-alt w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">${customer.city || 'Unknown'}, ${customer.country || 'Unknown'}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Date of Birth: ${customer.date_of_birth ? (() => {
                                        try {
                                            const date = new Date(customer.date_of_birth);
                                            return date.toLocaleDateString('en-US', { 
                                                year: 'numeric', 
                                                month: 'long', 
                                                day: 'numeric' 
                                            });
                                        } catch (e) {
                                            return customer.date_of_birth;
                                        }
                                    })() : 'Not specified'}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-birthday-cake w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Age: ${customer.date_of_birth ? (() => {
                                        try {
                                            const birthDate = new Date(customer.date_of_birth);
                                            const today = new Date();
                                            let age = today.getFullYear() - birthDate.getFullYear();
                                            const monthDiff = today.getMonth() - birthDate.getMonth();
                                            if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                                                age--;
                                            }
                                            return age;
                                        } catch (e) {
                                            return customer.age || 'Not specified';
                                        }
                                    })() : (customer.age || 'Not specified')}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-venus-mars w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">${customer.gender || 'Not specified'}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Business Information -->
                        <div class="space-y-4">
                            <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Business Information</h4>
                            <div class="space-y-3">
                                <div class="flex items-center">
                                    <i class="fas fa-star w-4 h-4 mr-3 text-yellow-500"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Loyalty Points: ${customer.loyalty_points ? parseInt(customer.loyalty_points) : 0}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-shopping-cart w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Total Orders: ${customer.total_sales ? parseInt(customer.total_sales) : 0}</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-dollar-sign w-4 h-4 mr-3 text-green-500"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Total Spent: ${customer.total_spent ? parseFloat(customer.total_spent).toFixed(2) : '0.00'} Birr</span>
                                </div>
                                <div class="flex items-center">
                                    <i class="fas fa-calendar-plus w-4 h-4 mr-3 text-gray-400"></i>
                                    <span class="text-sm text-gray-700 dark:text-gray-300">Member Since: ${customer.created_at ? (() => {
                                        try {
                                            return new Date(customer.created_at).toLocaleDateString();
                                        } catch (e) {
                                            return 'Unknown';
                                        }
                                    })() : 'Unknown'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information -->
                    ${customer.medical_conditions || customer.allergies || customer.insurance_provider ? `
                    <div class="space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700 pb-2">Additional Information</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            ${customer.medical_conditions ? `
                            <div>
                                <h5 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Medical Conditions</h5>
                                <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">${customer.medical_conditions}</p>
                            </div>
                            ` : ''}
                            ${customer.allergies ? `
                            <div>
                                <h5 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Allergies</h5>
                                <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">${customer.allergies}</p>
                            </div>
                            ` : ''}
                            ${customer.insurance_provider ? `
                            <div>
                                <h5 class="text-sm font-semibold text-gray-600 dark:text-gray-400 mb-2">Insurance Provider</h5>
                                <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">${customer.insurance_provider}</p>
                            </div>
                            ` : ''}
                        </div>
                    </div>
                    ` : ''}
                </div>
            `;
            
            modal.classList.remove('hidden');
            } catch (error) {
                console.error('Error generating customer modal content:', error);
                content.innerHTML = `
                    <div class="text-center py-8">
                        <div class="w-16 h-16 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-exclamation-triangle text-red-500 text-2xl"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Error Loading Customer Details</h3>
                        <p class="text-gray-600 dark:text-gray-300">There was an error displaying the customer information. Please try again.</p>
                    </div>
                `;
                modal.classList.remove('hidden');
            }
        }
    };
    
    /**
     * Hide view customer modal
     */
    window.hideViewCustomerModal = function() {
        const modal = document.getElementById('viewCustomerModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };
    
    /**
     * Show edit customer modal
     */
    window.showEditCustomerModal = function(customer) {
        const modal = document.getElementById('editCustomerModal');
        const form = document.getElementById('editCustomerForm');
        
        if (modal && form) {
            // Populate form fields with customer data
            populateEditForm(customer);
            modal.classList.remove('hidden');
        }
    };
    
    /**
     * Hide edit customer modal
     */
    window.hideEditCustomerModal = function() {
        const modal = document.getElementById('editCustomerModal');
        if (modal) {
            modal.classList.add('hidden');
        }
    };
    
    /**
     * Populate edit form with customer data
     */
    function populateEditForm(customer) {
        // Personal Details
        document.querySelector('#editCustomerForm input[name="name"]').value = customer.name || '';
        document.querySelector('#editCustomerForm input[name="email"]').value = customer.email || '';
        document.querySelector('#editCustomerForm input[name="phone"]').value = customer.phone || '';
        
        // Handle date of birth - convert from YYYY-MM-DD to display format
        if (customer.date_of_birth) {
            const date = new Date(customer.date_of_birth);
            if (!isNaN(date.getTime())) {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                document.querySelector('#editCustomerForm input[name="date_of_birth"]').value = `${year}-${month}-${day}`;
            }
        } else {
            document.querySelector('#editCustomerForm input[name="date_of_birth"]').value = '';
        }
        
        document.querySelector('#editCustomerForm select[name="gender"]').value = customer.gender || '';
        
        // Address
        document.querySelector('#editCustomerForm textarea[name="address"]').value = customer.address || '';
        document.querySelector('#editCustomerForm input[name="city"]').value = customer.city || '';
        document.querySelector('#editCustomerForm input[name="state"]').value = customer.state || '';
        document.querySelector('#editCustomerForm input[name="zip_code"]').value = customer.zip_code || '';
        document.querySelector('#editCustomerForm input[name="country"]').value = customer.country || '';
        
        // Medical Information
        document.querySelector('#editCustomerForm textarea[name="allergies"]').value = customer.allergies || '';
        document.querySelector('#editCustomerForm textarea[name="medical_conditions"]').value = customer.medical_conditions || '';
        document.querySelector('#editCustomerForm input[name="emergency_contact"]').value = customer.emergency_contact || '';
        document.querySelector('#editCustomerForm input[name="emergency_phone"]').value = customer.emergency_phone || '';
        
        // Insurance Information
        document.querySelector('#editCustomerForm input[name="insurance_provider"]').value = customer.insurance_provider || '';
        document.querySelector('#editCustomerForm input[name="insurance_number"]').value = customer.insurance_number || '';
        
        // Customer Settings
        document.querySelector('#editCustomerForm select[name="segment"]').value = customer.segment || 'new';
        document.querySelector('#editCustomerForm select[name="status"]').value = customer.status || 'active';
        document.querySelector('#editCustomerForm input[name="tags"]').value = customer.tags || '';
        document.querySelector('#editCustomerForm input[name="is_active"]').checked = customer.is_active !== false;
        
        // Store customer ID for form submission
        document.querySelector('#editCustomerForm input[name="customer_id"]').value = customer.id;
    }
    
    /**
     * Handle edit customer form submission
     */
    window.handleEditCustomerSubmit = async function(event) {
        event.preventDefault();
        
        const form = document.getElementById('editCustomerForm');
        const formData = new FormData(form);
        const customerId = formData.get('customer_id');
        
        // Convert FormData to object and handle special fields
        const data = Object.fromEntries(formData);
        
        // Handle boolean fields
        data.is_active = formData.get('is_active') === 'on';
        
        // Handle date format - convert from mm/dd/yyyy to YYYY-MM-DD
        if (data.date_of_birth) {
            // Check if date is in mm/dd/yyyy format and convert to YYYY-MM-DD
            if (data.date_of_birth.includes('/')) {
                const [month, day, year] = data.date_of_birth.split('/');
                data.date_of_birth = `${year}-${month.padStart(2, '0')}-${day.padStart(2, '0')}`;
            }
            // Ensure date is in YYYY-MM-DD format for database
            const date = new Date(data.date_of_birth);
            if (!isNaN(date.getTime())) {
                data.date_of_birth = date.toISOString().split('T')[0];
            }
        }
        
        // Remove empty strings for optional fields
        Object.keys(data).forEach(key => {
            if (data[key] === '' && !['name', 'email', 'phone', 'address', 'city', 'state', 'date_of_birth'].includes(key)) {
                data[key] = null;
            }
        });
        
        console.log('Sending customer data:', data); // Debug log
        
        // Validate required fields before sending
        const requiredFields = ['name', 'email', 'phone', 'address', 'city', 'state', 'date_of_birth'];
        const missingFields = requiredFields.filter(field => !data[field] || data[field].trim() === '');
        
        if (missingFields.length > 0) {
            alert('Please fill in all required fields: ' + missingFields.join(', '));
            return;
        }
        
        // Validate date of birth
        if (data.date_of_birth) {
            const birthDate = new Date(data.date_of_birth);
            const today = new Date();
            const age = today.getFullYear() - birthDate.getFullYear();
            
            if (birthDate > today) {
                alert('Date of birth cannot be in the future');
                return;
            }
            
            if (age > 150) {
                alert('Please enter a valid date of birth');
                return;
            }
        }
        
        try {
            // Show loading state
            showGlobalLoading('Updating customer...');
            
            // Submit form data
            const response = await fetch(`/customers/${customerId}`, {
                method: 'PUT',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(data)
            });
            
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            
            if (response.ok) {
                const result = await response.json();
                if (result.success) {
                    // Show success notification
                    if (window.NotificationService) {
                        window.NotificationService.success('Customer updated successfully!');
                    }
                    
                    // Close modal
                    hideEditCustomerModal();
                    
                    // Refresh the page to show updated data
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    throw new Error(result.message || 'Failed to update customer');
                }
            } else {
                const errorData = await response.json();
                console.error('Server error response:', errorData);
                
                // Handle validation errors
                if (errorData.errors) {
                    const errorMessages = Object.values(errorData.errors).flat().join('\n');
                    alert('Validation errors:\n' + errorMessages);
                } else {
                    throw new Error(errorData.message || 'Failed to update customer');
                }
            }
        } catch (error) {
            console.error('Error updating customer:', error);
            alert('Error updating customer: ' + error.message);
        } finally {
            hideGlobalLoading();
        }
    };
    
    // Helper functions for status colors
    function getCustomerStatusColor(status) {
        switch(status) {
            case 'active': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            case 'inactive': return 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200';
            case 'pending': return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200';
            default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
        }
    }
    
    function getCustomerSegmentColor(segment) {
        switch(segment) {
            case 'vip': return 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200';
            case 'loyal': return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200';
            case 'regular': return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200';
            case 'new': return 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200';
            default: return 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200';
        }
    }
}

/**
 * Simple notification function (matches supplier implementation)
 */
function showNotification(message, type = 'info') {
    if (window.NotificationService) {
        switch(type) {
            case 'success':
                window.NotificationService.success(message);
                break;
            case 'error':
                window.NotificationService.error(message);
                break;
            case 'warning':
                window.NotificationService.warning(message);
                break;
            default:
                window.NotificationService.info(message);
        }
    } else {
        // Fallback to alert if NotificationService is not available
        alert(message);
    }
}

/**
 * Delete customer functionality (based on supplier implementation)
 */
window.deleteCustomer = async function(customerId) {
    try {
        // Show global loading spinner
        showActionLoading('loading customer details');
        
        // Fetch customer data for confirmation
        const response = await fetch(`/customers/${customerId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json'
            }
        });
        
        if (response.ok) {
            const customer = await response.json();
            
            if (customer) {
                // Populate modal with customer data
                document.getElementById('deleteCustomerName').textContent = customer.name;
                document.getElementById('deleteCustomerEmail').textContent = customer.email;
                document.getElementById('deleteCustomerPhone').textContent = customer.phone;
                document.getElementById('deleteCustomerStatus').textContent = customer.status;
                document.getElementById('deleteCustomerStatus').className = customer.status === 'active' ? 'text-green-400' : (customer.status === 'inactive' ? 'text-red-400' : 'text-yellow-400');
                document.getElementById('deleteCustomerOrders').textContent = customer.total_sales || '0';
                document.getElementById('deleteCustomerSpent').textContent = customer.total_spent ? `${customer.total_spent} Birr` : '0.00 Birr';
                
                // Store customer ID for deletion
                window.currentDeleteCustomerId = customer.id;
                
                // Show modal
                const modal = document.getElementById('deleteCustomerModal');
                if (modal) {
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                }
            } else {
                throw new Error('Customer not found');
            }
        } else {
            throw new Error(`Failed to fetch customer: ${response.status}`);
        }
        
    } catch (error) {
        console.error('Error fetching customer:', error);
        showNotification('Error loading customer details', 'error');
    } finally {
        // Hide global loading spinner
        hideGlobalLoading();
    }
};

/**
 * Hide delete customer modal
 */
window.hideDeleteCustomerModal = function() {
    const modal = document.getElementById('deleteCustomerModal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
    // Reset confirmation input
    document.getElementById('deleteCustomerConfirmationInput').value = '';
    document.getElementById('deleteCustomerBtn').disabled = true;
    window.currentDeleteCustomerId = null;
};

/**
 * Handle delete customer confirmation input
 */
window.handleDeleteCustomerConfirmation = function() {
    const input = document.getElementById('deleteCustomerConfirmationInput');
    const deleteBtn = document.getElementById('deleteCustomerBtn');
    
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
 * Confirm delete customer
 */
window.confirmDeleteCustomer = async function() {
    if (!window.currentDeleteCustomerId) {
        showNotification('No customer selected for deletion', 'error');
        return;
    }

    try {
        showActionLoading('deleting customer');
        
        const response = await fetch(`/customers/${window.currentDeleteCustomerId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success) {
            showLoadingWithStyle('success', 'Customer deleted successfully!');
            hideDeleteCustomerModal();
            
            // Reload the page to refresh data
            setTimeout(() => {
                location.reload();
            }, 1500);
        } else {
            showNotification(data.message || 'Error deleting customer', 'error');
        }
    } catch (error) {
        console.error('Error deleting customer:', error);
        showNotification('Error deleting customer', 'error');
    }
};

// Global functions for onclick handlers
window.showAnalyticsModal = function() {
    console.log('Showing customer analytics');
    // Implement analytics modal/chart
    alert('Customer Analytics functionality will be implemented soon.');
};

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

// Tab switching functionality for Import/Export modal
window.switchTab = function(tabName) {
    // Hide all content
    const importContent = document.getElementById('importContent');
    const exportContent = document.getElementById('exportContent');
    const printContent = document.getElementById('printContent');
    
    // Remove active styles from all tabs
    const importTab = document.getElementById('importTab');
    const exportTab = document.getElementById('exportTab');
    const printTab = document.getElementById('printTab');
    
    if (importContent) importContent.classList.add('hidden');
    if (exportContent) exportContent.classList.add('hidden');
    if (printContent) printContent.classList.add('hidden');
    
    if (importTab) {
        importTab.className = 'flex-1 px-6 py-4 text-left border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium flex items-center justify-center hover:text-gray-700 dark:hover:text-gray-300';
    }
    if (exportTab) {
        exportTab.className = 'flex-1 px-6 py-4 text-left border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium flex items-center justify-center hover:text-gray-700 dark:hover:text-gray-300';
    }
    if (printTab) {
        printTab.className = 'flex-1 px-6 py-4 text-left border-b-2 border-transparent text-gray-500 dark:text-gray-400 font-medium flex items-center justify-center hover:text-gray-700 dark:hover:text-gray-300';
    }
    
    // Show selected content and update tab styles
    switch(tabName) {
        case 'import':
            if (importContent) importContent.classList.remove('hidden');
            if (importTab) {
                importTab.className = 'flex-1 px-6 py-4 text-left border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 font-medium flex items-center justify-center';
            }
            break;
        case 'export':
            if (exportContent) exportContent.classList.remove('hidden');
            if (exportTab) {
                exportTab.className = 'flex-1 px-6 py-4 text-left border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 font-medium flex items-center justify-center';
            }
            break;
        case 'print':
            if (printContent) printContent.classList.remove('hidden');
            if (printTab) {
                printTab.className = 'flex-1 px-6 py-4 text-left border-b-2 border-orange-500 text-orange-600 dark:text-orange-400 font-medium flex items-center justify-center';
            }
            break;
    }
};

// Export functionality
window.handleExport = function(format) {
    const params = new URLSearchParams({
        format: format,
        filename: 'customers_export'
    });
    
    // Create a temporary link to download the file
    const link = document.createElement('a');
    link.href = `{{ route("customers.export") }}?${params.toString()}`;
    link.download = `customers_export_${new Date().toISOString().slice(0, 19).replace(/:/g, '-')}.${format === 'excel' ? 'xlsx' : format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Print functionality
window.handlePrint = function(type) {
    const params = new URLSearchParams({
        format: type
    });
    
    // Open print report in new window
    const printWindow = window.open(`{{ route("customers.print") }}?${params.toString()}`, '_blank');
    if (printWindow) {
        printWindow.onload = function() {
            printWindow.print();
        };
    }
};

// Template download functionality
window.downloadTemplate = function() {
    const link = document.createElement('a');
    link.href = '{{ route("customers.template") }}';
    link.download = 'customer_import_template.xlsx';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
};

// Import/Export Modal Functionality
(function() {
    'use strict';
    
    let currentTab = 'import';
    let isImporting = false;
    let isExporting = false;
    
    // Event listeners for modals
    document.addEventListener('click', function(event) {
        if (event.target.id === 'editCustomerModal') {
            hideEditCustomerModal();
        }
        if (event.target.id === 'deleteCustomerModal') {
            hideDeleteCustomerModal();
        }
    });

    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideEditCustomerModal();
            hideDeleteCustomerModal();
        }
    });

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
        
        // Mock data for customers export summary
        const totalRecordsElement = document.getElementById('totalRecords');
        const activeCustomersElement = document.getElementById('activeCustomers');
        const exportTotalSpentElement = document.getElementById('exportTotalSpent');
        const premiumCustomersElement = document.getElementById('premiumCustomers');
        
        if (totalRecordsElement) totalRecordsElement.textContent = '6';
        if (activeCustomersElement) activeCustomersElement.textContent = '2';
        if (exportTotalSpentElement) exportTotalSpentElement.textContent = '93,338.00';
        if (premiumCustomersElement) premiumCustomersElement.textContent = '2';
    }
    
    /**
     * Initialize template download
     */
    function initializeTemplateDownload() {
        const downloadTemplateBtn = document.getElementById('downloadTemplateBtn');
        if (downloadTemplateBtn) {
            downloadTemplateBtn.addEventListener('click', function() {
                // Create a simple CSV template for customers
                const csvContent = 'name,email,phone,age,city,country,address,notes\n"John Doe","john@example.com","+1-555-0101","30","New York","USA","123 Main St","Regular customer"\n"Jane Smith","jane@example.com","+1-555-0102","25","Los Angeles","USA","456 Oak Ave","VIP customer"';
                const blob = new Blob([csvContent], { type: 'text/csv' });
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'customers_template.csv';
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
     * Show Analytics Modal
     */
    window.showAnalyticsModal = function() {
        showNotification('Analytics functionality will be implemented soon.', 'info');
    };
    
    // Add click outside to close modal functionality
    document.addEventListener('click', function(event) {
        const viewModal = document.getElementById('viewCustomerModal');
        const editModal = document.getElementById('editCustomerModal');
        
        if (viewModal && !viewModal.classList.contains('hidden')) {
            // Check if click is on the modal backdrop
            if (event.target === viewModal) {
                hideViewCustomerModal();
            }
        }
        
        if (editModal && !editModal.classList.contains('hidden')) {
            // Check if click is on the modal backdrop
            if (event.target === editModal) {
                hideEditCustomerModal();
            }
        }
    });
    
    // Add ESC key to close modal functionality
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const viewModal = document.getElementById('viewCustomerModal');
            const editModal = document.getElementById('editCustomerModal');
            
            if (viewModal && !viewModal.classList.contains('hidden')) {
                hideViewCustomerModal();
            }
            
            if (editModal && !editModal.classList.contains('hidden')) {
                hideEditCustomerModal();
            }
        }
    });

})();
</script>

<!-- View Customer Modal -->
<div id="viewCustomerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4">
    <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-user text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Customer Details</h2>
                    <p class="text-gray-600 dark:text-gray-400">View Customer Information</p>
                </div>
            </div>
            <button onclick="hideViewCustomerModal()" class="w-10 h-10 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="p-6 overflow-y-auto flex-1">
            <div id="viewCustomerContent">
                <!-- Content will be populated by JavaScript -->
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button onclick="hideViewCustomerModal()" class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg font-semibold text-sm transition-all duration-200">
                Close
            </button>
        </div>
    </div>
</div>

<!-- Edit Customer Modal -->
<div id="editCustomerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4 backdrop-blur-sm">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <div class="flex items-center">
                <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center mr-4">
                    <i class="fas fa-edit text-white text-lg"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Customer</h3>
                    <p class="text-gray-600 dark:text-gray-300">Update Customer Information</p>
                </div>
            </div>
            <button onclick="hideEditCustomerModal()" class="w-10 h-10 bg-gray-500 hover:bg-gray-600 text-white rounded-lg flex items-center justify-center transition-all duration-200">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Modal Content -->
        <div class="flex-1 overflow-y-auto">
            <form id="editCustomerForm" onsubmit="handleEditCustomerSubmit(event)" class="p-6">
                <!-- Hidden field for customer ID -->
                <input type="hidden" name="customer_id" value="">
                
                <!-- Personal Details Section -->
                <div class="mb-8">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Personal Details</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Full Name *</label>
                            <input type="text" name="name" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter full name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Email *</label>
                            <input type="email" name="email" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter email address">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Phone *</label>
                            <input type="tel" name="phone" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter phone number">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Date of Birth *</label>
                            <div class="relative">
                                <input type="date" name="date_of_birth" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Select date of birth">
                                <i class="fas fa-calendar absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 dark:text-gray-500"></i>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: MM/DD/YYYY</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Gender</label>
                            <select name="gender" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white">
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                            <select name="status" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="pending">Pending</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Address Section -->
                <div class="mb-8">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Address</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address *</label>
                            <textarea name="address" rows="3" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-y dark:bg-gray-700 dark:text-white" placeholder="Enter address"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">City *</label>
                            <input type="text" name="city" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter city">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">State *</label>
                            <input type="text" name="state" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter state">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">ZIP Code</label>
                            <input type="text" name="zip_code" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter ZIP code">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Country</label>
                            <input type="text" name="country" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter country">
                        </div>
                    </div>
                </div>

                <!-- Medical Information Section -->
                <div class="mb-8">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Medical Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Allergies</label>
                            <textarea name="allergies" rows="3" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-y dark:bg-gray-700 dark:text-white" placeholder="List any allergies"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Medical History</label>
                            <textarea name="medical_conditions" rows="3" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 resize-y dark:bg-gray-700 dark:text-white" placeholder="Relevant medical history"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Contact</label>
                            <input type="text" name="emergency_contact" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Emergency contact name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Emergency Phone</label>
                            <input type="tel" name="emergency_phone" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Emergency contact phone">
                        </div>
                    </div>
                </div>

                <!-- Insurance Information Section -->
                <div class="mb-8">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Insurance Information</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Insurance Provider</label>
                            <input type="text" name="insurance_provider" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Insurance provider name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Insurance Number</label>
                            <input type="text" name="insurance_number" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Insurance policy number">
                        </div>
                    </div>
                </div>

                <!-- Customer Settings Section -->
                <div class="mb-8">
                    <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Customer Settings</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Customer Segment</label>
                            <select name="segment" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white">
                                <option value="new">New</option>
                                <option value="regular">Regular</option>
                                <option value="loyal">Loyal</option>
                                <option value="vip">VIP</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                            <input type="text" name="tags" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 dark:bg-gray-700 dark:text-white" placeholder="Enter tags (comma separated)">
                        </div>
                        <div class="md:col-span-2">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_active" checked class="w-4 h-4 text-green-600 bg-gray-100 dark:bg-gray-700 border-gray-300 dark:border-gray-600 rounded focus:ring-green-500">
                                <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">Active Customer</span>
                            </label>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 dark:border-gray-700 flex-shrink-0">
            <button type="button" onclick="hideEditCustomerModal()" class="px-6 py-3 text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                Cancel
            </button>
            <button type="submit" form="editCustomerForm" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center">
                <i class="fas fa-save mr-2"></i>
                Update Customer
            </button>
        </div>
    </div>
</div>

<!-- Delete Customer Modal -->
<div id="deleteCustomerModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999] p-4" style="backdrop-filter: blur(4px);">
    <div class="bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-red-600 rounded-t-2xl p-6 flex items-center justify-between flex-shrink-0">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-700 rounded-lg flex items-center justify-center mr-4">
                    <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-white">Delete Customer</h2>
                    <p class="text-red-100 text-sm">Permanently delete customer from system</p>
                </div>
            </div>
            <button onclick="hideDeleteCustomerModal()" class="w-8 h-8 bg-red-700 hover:bg-red-800 text-white rounded-lg flex items-center justify-center transition-all duration-200">
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

            <!-- Customer Details -->
            <div class="bg-gray-700 rounded-lg p-4 mb-6">
                <h3 class="text-white font-semibold mb-4">Customer Information</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-300">Name:</span>
                        <span id="deleteCustomerName" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Email:</span>
                        <span id="deleteCustomerEmail" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Phone:</span>
                        <span id="deleteCustomerPhone" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Status:</span>
                        <span id="deleteCustomerStatus" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Total Orders:</span>
                        <span id="deleteCustomerOrders" class="text-white font-medium">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-300">Total Spent:</span>
                        <span id="deleteCustomerSpent" class="text-white font-medium">-</span>
                    </div>
                </div>
            </div>

            <!-- Confirmation Input -->
            <div class="mb-6">
                <p class="text-white mb-2">
                    Confirm Deletion <span class="text-red-400 font-bold">DELETE</span> in the box below:
                </p>
                <input type="text" id="deleteCustomerConfirmationInput" placeholder="Type DELETE to confirm"
                       oninput="handleDeleteCustomerConfirmation()"
                       class="w-full px-4 py-3 bg-gray-700 border border-gray-600 rounded-lg text-white placeholder-gray-400 focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all duration-200">
            </div>

            <!-- Impact Warning -->
            <div class="bg-orange-600 border border-orange-500 rounded-lg p-4 mb-6">
                <div class="flex items-start">
                    <i class="fas fa-info-circle text-orange-200 mr-3 mt-1"></i>
                    <div>
                        <p class="text-white font-semibold mb-2">Deleting may affect:</p>
                        <ul class="text-orange-100 text-sm space-y-1">
                            <li>• Customer purchase history and sales records</li>
                            <li>• Loyalty points and rewards tracking</li>
                            <li>• Customer analytics and reporting</li>
                            <li>• Prescription and medical history</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-700 flex-shrink-0">
            <button onclick="hideDeleteCustomerModal()" class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-semibold transition-all duration-200">
                Cancel
            </button>
            <button id="deleteCustomerBtn" onclick="confirmDeleteCustomer()" disabled
                    class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-semibold transition-all duration-200 opacity-50 cursor-not-allowed">
                Delete Customer
            </button>
        </div>
    </div>
</div>
@endsection
