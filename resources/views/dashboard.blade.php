@extends('layouts.app')

@section('title', 'Dashboard - Analog Pharmacy Management System')
@section('page-title', 'Dashboard')
@section('page-description', 'Overview of your pharmacy operations and sales analytics')

@section('content')
<!-- Welcome Section -->
<div class="mb-8">
    <div class="bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-blue-200 dark:border-gray-600">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2" id="welcomeMessage">Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg">Overview of your pharmacy operations and sales analytics</p>
            </div>
            <div class="text-right">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">Today</p>
                <p class="text-xl font-bold text-gray-900 dark:text-white" id="currentDate">{{ date('l, F j, Y') }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Dashboard Overview Cards -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <!-- Total Sales Card -->
    <div class="card-hover bg-gradient-to-br from-emerald-400 to-emerald-500 dark:from-emerald-800 dark:to-emerald-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-emerald-600 dark:border-emerald-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-emerald-800 dark:text-emerald-200 uppercase tracking-wide">Total Sales</p>
                <p class="text-3xl font-bold text-emerald-900 dark:text-white mt-2 mb-1">{{ $userCurrencySymbol ?? 'Br' }} {{ number_format($totalSales ?? 0, 2) }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-emerald-500 dark:bg-emerald-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-arrow-up text-xs mr-1"></i>
                        {{ $totalSalesGrowth >= 0 ? '+' : '' }}{{ $totalSalesGrowth ?? 0 }}%
                    </div>
                    <span class="text-xs text-emerald-700 dark:text-emerald-300 font-bold">from last month</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-chart-line text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Inventory Items Card -->
    <div class="card-hover bg-gradient-to-br from-blue-400 to-indigo-500 dark:from-blue-800 dark:to-indigo-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-blue-600 dark:border-blue-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-blue-800 dark:text-blue-200 uppercase tracking-wide">Inventory Items</p>
                <p class="text-3xl font-bold text-blue-900 dark:text-white mt-2 mb-1">{{ number_format($inventoryCount ?? 0) }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-blue-500 dark:bg-blue-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-boxes text-xs mr-1"></i>
                        +15
                    </div>
                    <span class="text-xs text-blue-700 dark:text-blue-300 font-bold">new this week</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-boxes text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Low Stock Alerts Card -->
    <div class="card-hover bg-gradient-to-br from-red-400 to-orange-500 dark:from-red-800 dark:to-orange-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-red-600 dark:border-red-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-red-800 dark:text-red-200 uppercase tracking-wide">Low Stock Alerts</p>
                <p class="text-3xl font-bold text-red-900 dark:text-white mt-2 mb-1">{{ $lowStockCount ?? 0 }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-red-500 dark:bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-exclamation-triangle text-xs mr-1"></i>
                        Alert
                    </div>
                    <span class="text-xs text-red-700 dark:text-red-300 font-bold">requires attention</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-amber-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-exclamation-triangle text-white text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Active Customers Card -->
    <div class="card-hover bg-gradient-to-br from-purple-400 to-violet-500 dark:from-purple-800 dark:to-violet-700 rounded-2xl shadow-lg hover:shadow-xl border-2 border-purple-600 dark:border-purple-600 p-6 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between">
            <div class="flex-1">
                <p class="text-sm font-bold text-purple-800 dark:text-purple-200 uppercase tracking-wide">Active Customers</p>
                <p class="text-3xl font-bold text-purple-900 dark:text-white mt-2 mb-1">{{ number_format($activeCustomers ?? 0) }}</p>
                <div class="flex items-center space-x-1">
                    <div class="flex items-center bg-purple-500 dark:bg-purple-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-md">
                        <i class="fas fa-users text-xs mr-1"></i>
                        {{ $activeCustomersGrowth >= 0 ? '+' : '' }}{{ $activeCustomersGrowth ?? 0 }}%
                    </div>
                    <span class="text-xs text-purple-700 dark:text-purple-300 font-bold">this month</span>
                </div>
            </div>
            <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-users text-white text-xl"></i>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Monthly Sales Chart -->
    <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 dark:border-gray-700 p-8 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Monthly Sales</h3>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Sales performance analytics</p>
            </div>
            <div class="flex space-x-2">
                <button id="chart-3m" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200" data-period="3m">
                    3M
                </button>
                <button id="chart-6m" class="px-4 py-2 text-sm font-semibold text-white bg-gradient-to-r from-green-500 to-green-600 rounded-xl hover:from-green-600 hover:to-green-700 focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200 shadow-lg" data-period="6m">
                    6M
                </button>
                <button id="chart-1y" class="px-4 py-2 text-sm font-semibold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300 transition-all duration-200" data-period="1y">
                    1Y
                </button>
            </div>
        </div>
        <div class="relative h-80 bg-gradient-to-br from-gray-50 to-white dark:from-gray-700 dark:to-gray-800 rounded-xl p-4">
            <canvas id="salesChart" class="w-full h-full"></canvas>
        </div>
    </div>

    <!-- Top Selling Products -->
    <div class="card-hover bg-gradient-to-br from-white to-blue-50 dark:from-gray-800 dark:to-blue-900/20 rounded-2xl shadow-lg hover:shadow-xl border border-blue-100 dark:border-blue-800 p-8 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Top Selling Products</h3>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Best performing items this month</p>
            </div>
            <button class="px-4 py-2 text-sm font-semibold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-xl hover:text-green-700 dark:hover:text-green-300 focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200">
                View All
            </button>
        </div>
        <div class="space-y-4">
            @forelse($topProducts ?? [] as $index => $product)
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-{{ ['green', 'blue', 'purple'][$index % 3] }}-50 to-white dark:from-{{ ['green', 'blue', 'purple'][$index % 3] }}-900/20 dark:to-gray-800 rounded-xl border border-{{ ['green', 'blue', 'purple'][$index % 3] }}-100 dark:border-{{ ['green', 'blue', 'purple'][$index % 3] }}-800 hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-{{ ['green', 'blue', 'purple'][$index % 3] }}-500 to-{{ ['green', 'blue', 'purple'][$index % 3] }}-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-pills text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-lg">{{ $product['name'] }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $product['category'] }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900 dark:text-white text-lg">{{ number_format($product['units']) }} units</p>
                    <div class="flex items-center space-x-1">
                        <div class="bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 px-2 py-1 rounded-full text-xs font-semibold">
                            {{ $product['growth'] }}
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-pills text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No top products</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm">Product sales data will appear here</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Activity and Quick Actions -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Recent Sales -->
    <div class="lg:col-span-2 card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg hover:shadow-xl border border-gray-100 dark:border-gray-700 p-8 transition-all duration-300 hover:-translate-y-1">
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Recent Sales</h3>
                <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Latest transactions</p>
            </div>
            <button class="px-4 py-2 text-sm font-semibold text-green-600 dark:text-green-400 bg-green-50 dark:bg-green-900/30 hover:bg-green-100 dark:hover:bg-green-900/50 rounded-xl hover:text-green-700 dark:hover:text-green-300 focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200">
                View All
            </button>
        </div>
        <div class="space-y-4">
            @forelse($recentSales ?? [] as $index => $sale)
            <div class="flex items-center justify-between p-4 bg-gradient-to-r from-{{ ['green', 'blue', 'purple'][$index % 3] }}-50 to-white dark:from-{{ ['green', 'blue', 'purple'][$index % 3] }}-900/20 dark:to-gray-800 rounded-xl border border-{{ ['green', 'blue', 'purple'][$index % 3] }}-100 dark:border-{{ ['green', 'blue', 'purple'][$index % 3] }}-800 hover:shadow-md transition-all duration-200">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-{{ ['green', 'blue', 'purple'][$index % 3] }}-500 to-{{ ['green', 'blue', 'purple'][$index % 3] }}-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-receipt text-white text-lg"></i>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900 dark:text-white text-lg">Sale #{{ $sale['id'] }}</p>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $sale['customer'] }} • {{ $sale['items'] }} items</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-bold text-gray-900 dark:text-white text-lg">{{ $userCurrencySymbol ?? 'Br' }} {{ number_format($sale['amount'], 2) }}</p>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $sale['time'] }}</p>
                </div>
            </div>
            @empty
            <div class="text-center py-8">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-receipt text-gray-400 text-2xl"></i>
                </div>
                <p class="text-gray-500 dark:text-gray-400 text-lg font-medium">No recent sales</p>
                <p class="text-gray-400 dark:text-gray-500 text-sm">Sales will appear here once transactions are made</p>
            </div>
            @endforelse
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card-hover bg-gradient-to-br from-white to-indigo-50 dark:from-gray-800 dark:to-indigo-900/20 rounded-2xl shadow-lg hover:shadow-xl border border-indigo-100 dark:border-indigo-800 p-8 transition-all duration-300 hover:-translate-y-1">
        <div class="mb-8">
            <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Quick Actions</h3>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-300">Frequently used functions</p>
        </div>
        <div class="space-y-4">
            <!-- New Sale -->
            <a href="{{ route('sales') }}" class="quick-action-link w-full flex items-center justify-between p-4 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 rounded-xl border border-green-200 focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-plus-circle text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-white text-lg">New Sale</span>
                </div>
                <i class="fas fa-chevron-right text-white text-lg"></i>
            </a>

            <!-- Add Inventory -->
            <a href="{{ route('medicines.index') }}" class="quick-action-link w-full flex items-center justify-between p-4 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 rounded-xl border border-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-300 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-boxes text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-white text-lg">Add Inventory</span>
                </div>
                <i class="fas fa-chevron-right text-white text-lg"></i>
            </a>

            <!-- Generate Report -->
            <a href="{{ route('reports') }}" class="quick-action-link w-full flex items-center justify-between p-4 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 rounded-xl border border-purple-200 focus:outline-none focus:ring-2 focus:ring-purple-300 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-chart-bar text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-white text-lg">Generate Report</span>
                </div>
                <i class="fas fa-chevron-right text-white text-lg"></i>
            </a>

            <!-- Low Stock Alert -->
            <a href="{{ route('alerts') }}" class="quick-action-link w-full flex items-center justify-between p-4 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 rounded-xl border border-orange-200 focus:outline-none focus:ring-2 focus:ring-orange-300 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                <div class="flex items-center space-x-4">
                    <div class="w-10 h-10 bg-white bg-opacity-20 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-white text-lg">Low Stock Alert</span>
                </div>
                <i class="fas fa-chevron-right text-white text-lg"></i>
            </a>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js CDN - Using a more stable version -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

<script>
/**
 * Modern Dashboard Controller
 * Handles Chart.js initialization and dashboard interactions
 */
(function() {
    'use strict';
    
    // Wait for Chart.js to load before initializing
    function waitForChart() {
        if (typeof Chart !== 'undefined') {
            console.log('Chart.js loaded successfully');
            initializeSalesChart();
            initializeDashboardAnimations();
        } else {
            console.log('Waiting for Chart.js to load...');
            setTimeout(waitForChart, 100);
        }
    }
    
    // Alternative: Load Chart.js with error handling
    function loadChartJS() {
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js';
        script.onload = function() {
            console.log('Chart.js loaded via script injection');
            waitForChart();
        };
        script.onerror = function() {
            console.error('Failed to load Chart.js');
        };
        document.head.appendChild(script);
    }
    
    // Initialize dashboard when DOM is ready
    document.addEventListener('DOMContentLoaded', function() {
        waitForChart();
        updateDateTime();
        personalizeWelcome();
        
        // Fallback: If Chart.js doesn't load within 5 seconds, try alternative loading
        setTimeout(function() {
            if (typeof Chart === 'undefined') {
                console.log('Chart.js not loaded, trying alternative method...');
                loadChartJS();
            }
        }, 5000);
    });
    
    // Update date and time in real-time
    function updateDateTime() {
        const dateElement = document.getElementById('currentDate');
        if (!dateElement) return;
        
        function updateDate() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric' 
            };
            dateElement.textContent = now.toLocaleDateString('en-US', options);
        }
        
        // Update immediately
        updateDate();
        
        // Update every minute
        setInterval(updateDate, 60000);
    }
    
    // Personalize welcome message
    function personalizeWelcome() {
        const welcomeElement = document.getElementById('welcomeMessage');
        if (!welcomeElement) return;
        
        // Get current time for greeting
        const now = new Date();
        const hour = now.getHours();
        
        let greeting = 'Welcome back to GaDA Pharmacy!';
        
        // Add time-based greeting
        if (hour < 12) {
            greeting = 'Good morning! Welcome to GaDA Pharmacy!';
        } else if (hour < 17) {
            greeting = 'Good afternoon! Welcome to GaDA Pharmacy!';
        } else {
            greeting = 'Good evening! Welcome to GaDA Pharmacy!';
        }
        
        // TODO: Add user name when authentication is implemented
        // const userName = '{{ Auth::user()->name ?? "" }}';
        // if (userName) {
        //     greeting = `Good ${hour < 12 ? 'morning' : hour < 17 ? 'afternoon' : 'evening'}, ${userName}! Welcome to GaDA Pharmacy!`;
        // }
        
        welcomeElement.textContent = greeting;
    }
    
    /**
     * Initialize the monthly sales chart with Chart.js
     * Uses dummy data as specified: Jan: 1200, Feb: 1900, Mar: 3000
     */
    function initializeSalesChart() {
        const ctx = document.getElementById('salesChart');
        if (!ctx) {
            console.log('Sales chart canvas not found');
            return;
        }
        
        if (typeof Chart === 'undefined') {
            console.error('Chart.js is not loaded');
            return;
        }
        
        // Chart configuration with enhanced modern styling
        const config = {
            type: 'bar',
            data: {
                labels: @json($monthlySalesData['labels'] ?? ['January', 'February', 'March']),
                datasets: [{
                    label: 'Sales ($)',
                    data: @json($monthlySalesData['data'] ?? [0, 0, 0]),
                    backgroundColor: [
                        'rgba(5, 150, 105, 0.9)',   // Enhanced green
                        'rgba(245, 158, 11, 0.9)',  // Enhanced orange
                        'rgba(59, 130, 246, 0.9)'   // Enhanced blue
                    ],
                    borderColor: [
                        'rgb(5, 150, 105)',         // Solid green
                        'rgb(245, 158, 11)',        // Solid orange
                        'rgb(59, 130, 246)'         // Solid blue
                    ],
                    borderWidth: 3,
                    borderRadius: 12,
                    borderSkipped: false,
                    hoverBackgroundColor: [
                        'rgba(5, 150, 105, 1)',    // Full opacity on hover
                        'rgba(245, 158, 11, 1)',   // Full opacity on hover
                        'rgba(59, 130, 246, 1)'    // Full opacity on hover
                    ],
                    hoverBorderWidth: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(255, 255, 255, 0.3)',
                        borderWidth: 2,
                        cornerRadius: 12,
                        displayColors: true,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13,
                            weight: '500'
                        },
                        padding: 12,
                        callbacks: {
                            title: function(context) {
                                return context[0].label + ' Sales';
                            },
                            label: function(context) {
                                return 'Revenue: {{ $userCurrencySymbol ?? "Br" }} ' + context.parsed.y.toLocaleString();
                            },
                            afterLabel: function(context) {
                                const percentage = ((context.parsed.y / 3000) * 100).toFixed(1);
                                return 'Performance: ' + percentage + '%';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12
                            },
                            callback: function(value) {
                                return '{{ $userCurrencySymbol ?? "Br" }} ' + value.toLocaleString();
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#6b7280',
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    }
                },
                animation: {
                    duration: 1500,
                    easing: 'easeInOutQuart',
                    delay: function(context) {
                        return context.dataIndex * 200;
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                onHover: function(event, elements) {
                    event.native.target.style.cursor = elements.length > 0 ? 'pointer' : 'default';
                }
            }
        };
        
        // Create the chart with error handling
        try {
            const chart = new Chart(ctx, config);
            storeChartReference(chart);
            console.log('Sales chart initialized successfully');
        } catch (error) {
            console.error('Error creating sales chart:', error);
        }
    }
    
    /**
     * Initialize dashboard animations and interactions
     */
    function initializeDashboardAnimations() {
        // Add fade-in animation to cards
        const cards = document.querySelectorAll('.card-hover');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
        
        // Add hover effects to quick action buttons
        const quickActions = document.querySelectorAll('[class*="bg-"][class*="-50"]');
        quickActions.forEach(button => {
            button.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(4px)';
            });
            
            button.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    }
    
    /**
     * Performance optimization: Lazy load charts
     * Only initialize charts when they come into viewport
     */
    function initializeLazyLoading() {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const chartElement = entry.target;
                    if (chartElement.id === 'salesChart' && !chartElement.chart) {
                        initializeSalesChart();
                        observer.unobserve(chartElement);
                    }
                }
            });
        });
        
        const chartElements = document.querySelectorAll('canvas');
        chartElements.forEach(canvas => observer.observe(canvas));
    }
    
    // Initialize lazy loading for better performance
    if ('IntersectionObserver' in window) {
        initializeLazyLoading();
    }
    
    /**
     * Real-time dashboard updates
     * Updates dashboard data every 30 seconds for live monitoring
     */
    function initializeRealTimeUpdates() {
        setInterval(function() {
            fetch('/dashboard/real-time', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateDashboardData(data.data);
                }
            })
            .catch(error => {
                console.log('Real-time update failed:', error);
            });
        }, 30000); // Update every 30 seconds
    }
    
    /**
     * Update dashboard with new data
     */
    function updateDashboardData(data) {
        // Update stat cards
        const totalSalesElement = document.querySelector('.text-3xl.font-bold.text-emerald-900');
        if (totalSalesElement && data.totalSales !== undefined) {
            totalSalesElement.textContent = '{{ $userCurrencySymbol ?? "Br" }} ' + parseFloat(data.totalSales).toLocaleString('en-US', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        }
        
        const inventoryElement = document.querySelector('.text-3xl.font-bold.text-blue-900');
        if (inventoryElement && data.inventoryCount !== undefined) {
            inventoryElement.textContent = parseInt(data.inventoryCount).toLocaleString();
        }
        
        const lowStockElement = document.querySelector('.text-3xl.font-bold.text-red-900');
        if (lowStockElement && data.lowStockCount !== undefined) {
            lowStockElement.textContent = parseInt(data.lowStockCount);
        }
        
        const customersElement = document.querySelector('.text-3xl.font-bold.text-purple-900');
        if (customersElement && data.activeCustomers !== undefined) {
            customersElement.textContent = parseInt(data.activeCustomers).toLocaleString();
        }
    }
    
    // Initialize real-time updates
    initializeRealTimeUpdates();
    
    // Global chart reference
    let currentChart = null;
    
    /**
     * Initialize chart toggle functionality
     */
    function initializeChartToggles() {
        const chartButtons = document.querySelectorAll('[data-period]');
        
        chartButtons.forEach(button => {
            button.addEventListener('click', function() {
                const period = this.getAttribute('data-period');
                
                // Update button states
                chartButtons.forEach(btn => {
                    btn.classList.remove('text-white', 'bg-gradient-to-r', 'from-green-500', 'to-green-600', 'shadow-lg');
                    btn.classList.add('text-gray-600', 'dark:text-gray-300', 'bg-gray-100', 'dark:bg-gray-700');
                });
                
                this.classList.remove('text-gray-600', 'dark:text-gray-300', 'bg-gray-100', 'dark:bg-gray-700');
                this.classList.add('text-white', 'bg-gradient-to-r', 'from-green-500', 'to-green-600', 'shadow-lg');
                
                // Load chart data for the selected period
                loadChartData(period);
            });
        });
    }
    
    /**
     * Load chart data for specific period
     */
    function loadChartData(period) {
        // Show loading state
        const chartCanvas = document.getElementById('salesChart');
        if (chartCanvas) {
            chartCanvas.style.opacity = '0.5';
        }
        
        fetch(`/dashboard/chart-data/${period}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateChart(data.data);
            } else {
                console.error('Failed to load chart data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading chart data:', error);
        })
        .finally(() => {
            // Remove loading state
            if (chartCanvas) {
                chartCanvas.style.opacity = '1';
            }
        });
    }
    
    /**
     * Update the chart with new data
     */
    function updateChart(chartData) {
        const ctx = document.getElementById('salesChart');
        if (!ctx || !currentChart) {
            console.error('Chart not available for update');
            return;
        }
        
        // Update chart data
        currentChart.data.labels = chartData.labels;
        currentChart.data.datasets[0].data = chartData.data;
        
        // Update chart with smooth animation
        currentChart.update('active');
        
        console.log('Chart updated with new data:', chartData);
    }
    
    /**
     * Store chart reference for updates
     */
    function storeChartReference(chart) {
        currentChart = chart;
    }
    
    // Initialize chart toggles
    initializeChartToggles();
    
    /**
     * Show sophisticated global loading spinner (from suppliers section)
     */
    function showGlobalLoading(message = 'Loading...') {
        // Remove existing loading if any
        hideGlobalLoading();
        
        const loadingDiv = document.createElement('div');
        loadingDiv.id = 'globalLoading';
        loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
        loadingDiv.innerHTML = `
            <div class="text-center">
                <!-- Dashboard icon container -->
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
                    <div class="flex items-center space-x-2">
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
     * Start progress animation for horizontal loading bar (dynamic)
     */
    function startProgressAnimation() {
        const progressBar = document.getElementById('progressBar');
        const progressText = document.getElementById('loadingProgress');
        
        if (!progressBar || !progressText) return;
        
        let progress = 0;
        const duration = 1500; // 1.5 seconds total
        const interval = 30; // Update every 30ms for smoother animation
        const increment = 100 / (duration / interval);
        
        const animation = setInterval(() => {
            progress += increment;
            
            // Dynamic progress - doesn't need to reach 100%
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
     * Show action loading with different styles for dashboard actions
     */
    function showActionLoading(action) {
        const messages = {
            'sales': 'Opening Sales Management...',
            'inventory': 'Loading Inventory System...',
            'reports': 'Preparing Reports Dashboard...',
            'alerts': 'Loading Alert System...'
        };
        showGlobalLoading(messages[action] || 'Loading...');
    }
    
    /**
     * Initialize Quick Actions functionality with sophisticated loading
     */
    function initializeQuickActions() {
        const quickActionLinks = document.querySelectorAll('.quick-action-link');
        
        quickActionLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent immediate navigation
                
                // Determine action type based on href
                const href = this.getAttribute('href');
                let actionType = 'default';
                
                if (href.includes('sales')) actionType = 'sales';
                else if (href.includes('medicines') || href.includes('inventory')) actionType = 'inventory';
                else if (href.includes('reports')) actionType = 'reports';
                else if (href.includes('alerts')) actionType = 'alerts';
                
                // Show sophisticated loading
                showActionLoading(actionType);
                
                // Navigate after loading animation completes
                setTimeout(() => {
                    hideGlobalLoading();
                    window.location.href = href;
                }, 1500);
            });
        });
    }
    
    // Initialize quick actions
    initializeQuickActions();
    
    // Show welcome notification when dashboard loads
    setTimeout(() => {
        if (window.NotificationService) {
            window.NotificationService.success('Dashboard loaded successfully!');
        }
    }, 1000);
    
})();

/**
 * Sophisticated Notification Service (from suppliers section)
 */
(function() {
    'use strict';
    
    /**
     * Notification Service Class
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
         * Setup notification styles
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
</script>
@endsection

