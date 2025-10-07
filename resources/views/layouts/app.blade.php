<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="color-scheme" content="light dark">
    <meta name="theme-color" content="#059669" media="(prefers-color-scheme: light)">
    <meta name="theme-color" content="#111827" media="(prefers-color-scheme: dark)">

    <title>@yield('title', 'Analog Pharmacy Management System')</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('analo.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('analo.png') }}">
    <link rel="shortcut icon" href="{{ asset('analo.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('analo.png') }}">
    
    <!-- Tailwind CSS - Modern utility-first CSS framework -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0fdf4',
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            900: '#064e3b',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Font Awesome - Professional icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous">
    <!-- Google Fonts - Modern typography -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Modern Design System Variables */
        :root {
            --primary-green: #059669;
            --primary-green-light: #10b981;
            --primary-orange: #f59e0b;
            --primary-orange-light: #fbbf24;
            --sidebar-width: 280px;
            --header-height: 64px;
        }

        /* Ambulance Lights Animation */
        .ambulance-lights-container {
            display: flex;
            gap: 3px;
            align-items: center;
            background: rgba(0,0,0,0.1);
            padding: 4px 6px;
            border-radius: 8px;
        }

        .ambulance-light {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            opacity: 1;
            animation: ambulancePulse 2s infinite ease-in-out;
            border: 1px solid rgba(0,0,0,0.2);
            background-color: #ef4444; /* Default red background */
        }

        .light-1 {
            animation-delay: 0s;
        }

        .light-2 {
            animation-delay: 0.3s;
        }

        .light-3 {
            animation-delay: 0.6s;
        }

        @keyframes ambulancePulse {
            0% {
                background-color: #ef4444 !important; /* Red */
                transform: scale(1);
                box-shadow: 0 0 4px #ef4444;
            }
            25% {
                background-color: #ef4444 !important; /* Red */
                transform: scale(1.1);
                box-shadow: 0 0 6px #ef4444;
            }
            50% {
                background-color: #ffffff !important; /* White */
                transform: scale(1.2);
                box-shadow: 0 0 8px #ffffff;
            }
            75% {
                background-color: #ffffff !important; /* White */
                transform: scale(1.1);
                box-shadow: 0 0 6px #ffffff;
            }
            100% {
                background-color: #ef4444 !important; /* Red */
                transform: scale(1);
                box-shadow: 0 0 4px #ef4444;
            }
        }

        /* Critical Alert State */
        .ambulance-lights-container.critical .ambulance-light {
            animation: criticalPulse 0.8s infinite ease-in-out;
        }

        @keyframes criticalPulse {
            0%, 100% {
                opacity: 1;
                background-color: #dc2626; /* Darker red */
                transform: scale(1.5);
                box-shadow: 0 0 12px #dc2626;
            }
            50% {
                opacity: 0.3;
                background-color: #dc2626;
                transform: scale(0.8);
                box-shadow: 0 0 4px #dc2626;
            }
        }

        /* Disabled State */
        .ambulance-lights-container.disabled .ambulance-light {
            animation: none;
            opacity: 0.3;
            background-color: #6b7280; /* Gray */
            transform: scale(0.8);
        }

        /* Fixed header and scrolling fixes */
        .main-content-wrapper {
            height: 100vh; /* Full viewport height */
            overflow-x: hidden;
            overflow-y: auto; /* Allow vertical scrolling in main content area */
        }
        
        .main-content {
            min-height: calc(100vh - 5rem); /* Account for header height */
            overflow-x: hidden;
            /* Remove any height constraints that might cause full page scroll */
        }
        
        /* Ensure body doesn't have extra scrollbars */
        body {
            overflow: hidden; /* Prevent body from scrolling */
            height: 100vh; /* Full viewport height */
        }
        
        /* Ensure header stays fixed */
        header {
            position: fixed !important;
            top: 0 !important;
            z-index: 1000 !important;
        }

        /* Custom scrollbar for sidebar */
        .sidebar-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .sidebar-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }

        .sidebar-scroll::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Smooth transitions for all interactive elements */
        * {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Sidebar navigation hover effects */
        .nav-item {
            position: relative;
            overflow: hidden;
        }

        .nav-item::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
            transition: left 0.5s;
        }

        .nav-item:hover::before {
            left: 100%;
        }

        /* Active navigation item styling */
        .nav-item.active {
            background: rgba(255, 255, 255, 0.15);
            border-right: 3px solid var(--primary-orange);
        }

        .nav-item.active .nav-icon {
            color: var(--primary-orange);
        }

        /* Main content area animations */
        .main-content {
            animation: slideInRight 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideInRight {
            from {
                opacity: 0;
                transform: translateX(20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        /* Card hover effects */
        .card-hover {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .card-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        /* Mobile sidebar overlay */
        @media (max-width: 768px) {
            .sidebar-overlay {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: rgba(0, 0, 0, 0.5);
                z-index: 40;
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
            }

            .sidebar-overlay.show {
                opacity: 1;
                visibility: visible;
            }
        }

        /* Accessibility improvements */
        .focus-visible:focus {
            outline: 2px solid var(--primary-orange);
            outline-offset: 2px;
        }

        /* High contrast mode support */
        @media (prefers-contrast: high) {
            .nav-item {
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Sidebar layout fixes */
        .sidebar-scroll {
            max-height: calc(100vh - 200px);
        }
        
        /* Ensure main content doesn't overlap with sidebar */
        @media (min-width: 1024px) {
            .main-content-wrapper {
                margin-left: 16rem; /* 256px = w-64 */
            }
        }

        /* Enhanced header animations */
        .header-animate {
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* User dropdown enhancements */
        .user-dropdown {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }

        /* Notification badge pulse animation */
        @keyframes notificationPulse {
            0%, 100% {
                transform: scale(1);
                box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
            }
            50% {
                transform: scale(1.05);
                box-shadow: 0 0 0 4px rgba(239, 68, 68, 0);
            }
        }

        .notification-pulse {
            animation: notificationPulse 2s infinite;
        }

        /* Mobile header optimizations */
        @media (max-width: 768px) {
            .header-title {
                font-size: 1.25rem;
            }
            
            .header-subtitle {
                font-size: 0.75rem;
            }
            
            .user-info {
                display: none;
            }
        }

        /* Enhanced hover effects */
        .header-button {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .header-button:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        /* Sticky header enhancements */
        header {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Dark mode test styles */
        .dark header {
            background: rgba(17, 24, 39, 0.95) !important;
            border-bottom: 1px solid rgba(75, 85, 99, 0.3) !important;
        }

        .dark body {
            background-color: #111827 !important;
        }

        .dark .main-content {
            background-color: #111827 !important;
        }

        /* Ensure sticky header works on all browsers */
        @supports (position: sticky) {
            header {
                position: sticky;
                top: 0;
            }
        }

        /* Fallback for older browsers */
        @supports not (position: sticky) {
            header {
                position: fixed;
                top: 0;
                left: 0;
                right: 0;
                width: 100%;
            }
            
            .main-content {
                margin-top: 5rem; /* 80px = h-20 */
            }
        }

        /* Theme Persistence Styles */
        html[data-theme="dark"] {
            color-scheme: dark;
        }

        html[data-theme="light"] {
            color-scheme: light;
        }

        /* Force theme persistence - prevent automatic switching */
        html.dark {
            color-scheme: dark !important;
        }

        html.light {
            color-scheme: light !important;
        }

        /* Ensure theme classes are not overridden */
        html.dark * {
            color-scheme: dark;
        }

        html.light * {
            color-scheme: light;
        }

        /* Prevent browser from changing theme automatically */
        @media (prefers-color-scheme: dark) {
            html:not([data-theme]) {
                color-scheme: light;
            }
        }

        @media (prefers-color-scheme: light) {
            html:not([data-theme]) {
                color-scheme: light;
            }
        }
    </style>
    
    <!-- Immediate Theme Application Script -->
    <script>
        // Apply theme immediately to prevent flash
        (function() {
            // Check if theme change is in progress
            const themeChangeInProgress = sessionStorage.getItem('theme_change_in_progress');
            console.log('Immediate theme application - flag status:', themeChangeInProgress);
            if (themeChangeInProgress) {
                console.log('Theme change in progress, skipping immediate application');
                return;
            }
            
            // Get theme from user preferences (database) - prioritize database over localStorage
            const userTheme = '{{ $userTheme ?? "auto" }}';
            console.log('User theme from database:', userTheme);
            
            // Always use database theme if available, ignore localStorage
            const savedTheme = userTheme;
            console.log('Final theme to apply:', savedTheme);
            const html = document.documentElement;
            
            // Apply theme immediately
            if (savedTheme === 'dark') {
                console.log('Applying dark theme');
                html.classList.add('dark');
                html.setAttribute('data-theme', 'dark');
                html.style.colorScheme = 'dark';
            } else if (savedTheme === 'light') {
                console.log('Applying light theme');
                html.classList.add('light');
                html.setAttribute('data-theme', 'light');
                html.style.colorScheme = 'light';
            } else if (savedTheme === 'auto') {
                // Auto theme - use system preference
                if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                    html.classList.add('dark');
                    html.setAttribute('data-theme', 'dark');
                    html.style.colorScheme = 'dark';
                } else {
                    html.classList.add('light');
                    html.setAttribute('data-theme', 'light');
                    html.style.colorScheme = 'light';
                }
            } else {
                // Fallback to light
                html.classList.add('light');
                html.setAttribute('data-theme', 'light');
                html.style.colorScheme = 'light';
            }
            
            // Prevent system theme from overriding
            const originalMatchMedia = window.matchMedia;
            window.matchMedia = function(query) {
                const result = originalMatchMedia.call(this, query);
                if (query === '(prefers-color-scheme: dark)' && localStorage.getItem('pharmacy_theme_preference')) {
                    return {
                        matches: false,
                        media: query,
                        onchange: null,
                        addListener: function() {},
                        removeListener: function() {},
                        addEventListener: function() {},
                        removeEventListener: function() {},
                        dispatchEvent: function() { return false; }
                    };
                }
                return result;
            };
        })();
    </script>
</head>
<body class="bg-gray-50 dark:bg-gray-900 font-sans antialiased transition-colors duration-300">
    <!-- Mobile sidebar overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    
    <div class="flex h-screen">
        <!-- Fixed Left Sidebar -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-gradient-to-b from-green-600 to-green-700 dark:from-gray-900 dark:to-gray-800 text-white transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col" id="sidebar">
            <!-- Sidebar Header -->
            <div class="flex items-center justify-between h-16 px-6 border-b border-green-500 dark:border-gray-700">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('analo.png') }}" alt="Analog Pharmacy" class="w-8 h-8 rounded-lg">
                    <div>
                        <h1 class="text-lg font-bold">Analog Pharmacy</h1>
                        <p class="text-xs text-green-200 dark:text-gray-300">Management System</p>
                    </div>
                </div>
                <!-- Mobile close button -->
                <button class="lg:hidden text-white hover:text-gray-300 focus:outline-none" id="sidebarClose">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>

            <!-- Navigation Menu -->
            <nav class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <!-- Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('dashboard') ? 'active' : '' }}"
                   aria-label="Dashboard">
                    <i class="fas fa-chart-line nav-icon w-5 h-5 mr-3"></i>
                    Dashboard
                </a>

                <!-- Store Management - view-inventory permission -->
                @can('view-inventory')
                <a href="{{ route('inventory') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('inventory') ? 'active' : '' }}"
                   aria-label="Store Management">
                    <i class="fas fa-boxes nav-icon w-5 h-5 mr-3"></i>
                    Store
                </a>
                @endcan

                <!-- Dispensary - view-dispensary permission -->
                @can('view-dispensary')
                <a href="{{ route('dispensary') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('dispensary') ? 'active' : '' }}"
                   aria-label="Dispensary">
                    <i class="fas fa-hand-holding-medical nav-icon w-5 h-5 mr-3"></i>
                    Dispensary
                </a>
                @endcan

                <!-- Sales Management - view-sales permission -->
                @can('view-sales')
                <a href="{{ route('sales') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('sales') ? 'active' : '' }}"
                   aria-label="Sales Management">
                    <i class="fas fa-shopping-cart nav-icon w-5 h-5 mr-3"></i>
                    Sales
                </a>
                @endcan

                <!-- Cashier - view-cashier permission -->
                @can('view-cashier')
                <a href="{{ route('cashier') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('cashier') ? 'active' : '' }}"
                   aria-label="Cashier Terminal">
                    <i class="fas fa-cash-register nav-icon w-5 h-5 mr-3"></i>
                    Cashier
                </a>
                @endcan

                <!-- Purchases - view-purchases permission -->
                @can('view-purchases')
                <a href="{{ route('purchases.index') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('purchases.*') ? 'active' : '' }}"
                   aria-label="Purchase Management">
                    <i class="fas fa-shopping-bag nav-icon w-5 h-5 mr-3"></i>
                    Purchases
                </a>
                @endcan

                <!-- Suppliers - view-suppliers permission -->
                @can('view-suppliers')
                <a href="{{ route('suppliers') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('suppliers') ? 'active' : '' }}"
                   aria-label="Supplier Management">
                    <i class="fas fa-truck nav-icon w-5 h-5 mr-3"></i>
                    Suppliers
                </a>
                @endcan

                <!-- Customers - view-customers permission -->
                @can('view-customers')
                <a href="{{ route('customers.index') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('customers.*') ? 'active' : '' }}"
                   aria-label="Customer Management">
                    <i class="fas fa-users nav-icon w-5 h-5 mr-3"></i>
                    Customers
                </a>
                @endcan

                <!-- Reports - view-reports permission -->
                @can('view-reports')
                <a href="{{ route('reports') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('reports*') ? 'active' : '' }}"
                   aria-label="Reports and Analytics">
                    <i class="fas fa-chart-bar nav-icon w-5 h-5 mr-3"></i>
                    Reports
                </a>
                @endcan

                <!-- Alerts - view-alerts permission -->
                @can('view-alerts')
                <a href="{{ route('alerts') }}" 
                   class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('alerts*') ? 'active' : '' }}"
                   aria-label="System Alerts">
                    <i class="fas fa-bell nav-icon w-5 h-5 mr-3"></i>
                    Alerts
                    <span class="ml-auto bg-red-500 text-xs px-2 py-1 rounded-full hidden" id="alertCount">0</span>
                </a>
                @endcan

                <!-- Settings Section -->
                @if(auth()->user()->can('view-settings') || auth()->user()->can('view-users'))
                <div class="pt-4 mt-4 border-t border-green-500 dark:border-gray-700">
                    <p class="px-4 text-xs font-semibold text-green-200 dark:text-gray-300 uppercase tracking-wider mb-2">Settings</p>
                    
                    <!-- System Settings - view-settings permission -->
                    @can('view-settings')
                    <a href="{{ route('settings.index') }}" 
                       class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 {{ request()->routeIs('settings*') ? 'active' : '' }}"
                       aria-label="System Settings">
                        <i class="fas fa-cog nav-icon w-5 h-5 mr-3"></i>
                        System
                    </a>
                    @endcan

                    <!-- User Management - view-users permission -->
                    @can('view-users')
                    <a href="{{ route('users.index') }}" 
                       class="nav-item flex items-center px-4 py-3 text-sm font-medium rounded-lg hover:bg-green-500 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-green-400 mt-3 {{ request()->routeIs('users*') ? 'active' : '' }}"
                       aria-label="User Management">
                        <i class="fas fa-users nav-icon w-5 h-5 mr-3"></i>
                        Users
                    </a>
                    @endcan
                </div>
                @endif
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-green-500 dark:border-gray-700 mt-auto">
                <div class="flex items-center space-x-3">
                    @if(Auth::user()->avatar)
                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" 
                             class="w-8 h-8 rounded-full object-cover">
                    @else
                        <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-sm"></i>
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate">{{ Auth::user()->name }}</p>
                        <p class="text-xs text-green-200 truncate">{{ Auth::user()->email }}</p>
                        <p class="text-xs text-green-300 truncate">{{ Auth::user()->roles->first()->name ?? 'User' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                                        @csrf
                    <button type="submit" 
                            class="w-full flex items-center px-3 py-2 text-sm font-medium text-green-200 hover:text-white hover:bg-green-500 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-400"
                            aria-label="Sign out">
                        <i class="fas fa-sign-out-alt w-4 h-4 mr-2"></i>
                        Sign out
                    </button>
                                    </form>
                                </div>
        </aside>

        <!-- Enhanced Fixed Top Header -->
        <header class="fixed top-0 left-0 lg:left-64 right-0 bg-white dark:bg-gray-900 shadow-lg border-b border-gray-200 dark:border-gray-700 h-20 flex items-center justify-between px-6 z-50 transition-colors duration-300">
                
                <!-- Mobile menu button -->
                <button class="lg:hidden relative z-10 p-3 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200" id="sidebarToggle">
                    <i class="fas fa-bars text-xl"></i>
                </button>

                <!-- System Health Indicator - Ambulance Lights -->
                <div class="flex items-center ml-4" id="systemHealthIndicator">
                    <div class="ambulance-lights-container" style="border: 2px solid #333; background: #f0f0f0;">
                        <div class="ambulance-light light-1" style="background-color: #ef4444 !important;"></div>
                        <div class="ambulance-light light-2" style="background-color: #ffffff !important;"></div>
                        <div class="ambulance-light light-3" style="background-color: #ef4444 !important;"></div>
                    </div>
                    <span class="ml-2 text-xs font-semibold text-gray-600 dark:text-gray-300" id="healthStatusText">System Healthy</span>
                </div>


                <!-- Enhanced Header Actions -->
                <div class="flex items-center space-x-6">
                    <!-- Theme Toggle Switch -->
                    <div class="relative">
                        <button id="themeToggle" class="relative p-3 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200 group" aria-label="Toggle theme">
                            <i id="themeIcon" class="fas fa-sun text-xl group-hover:scale-110 transition-transform duration-200"></i>
                        </button>
                        <!-- Debug indicator -->
                        <div id="themeDebug" class="absolute -bottom-8 left-0 text-xs bg-red-500 text-white px-1 rounded" style="display: none;">Debug</div>
                    </div>

                    <!-- Enhanced Notifications -->
                    <div class="relative">
                        <a href="{{ route('alerts') }}" class="relative p-3 text-gray-600 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200 group" aria-label="Notifications">
                            <i class="fas fa-bell text-xl group-hover:scale-110 transition-transform duration-200"></i>
                            <span class="absolute -top-1 -right-1 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-bold rounded-full h-6 w-6 flex items-center justify-center shadow-lg hidden" id="headerAlertCount">
                                0
                            </span>
                        </a>
                    </div>

                    <!-- Enhanced User Menu -->
                    <div class="relative group">
                        <button class="flex items-center space-x-3 p-2 text-gray-700 hover:text-gray-900 hover:bg-gray-100 dark:text-gray-300 dark:hover:text-white dark:hover:bg-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-300 transition-all duration-200 group" aria-label="User menu" id="userMenuButton">
                            <div class="relative">
                                @if(Auth::user()->avatar)
                                    <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" 
                                         class="w-10 h-10 rounded-full object-cover shadow-lg">
                                @else
                                    <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                                        <i class="fas fa-user text-white text-sm"></i>
                                    </div>
                                @endif
                                <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white dark:border-gray-900"></div>
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-sm font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->roles->first()->name ?? 'User' }}</p>
                            </div>
                            <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors duration-200"></i>
                        </button>
                        
                        <!-- User Dropdown Menu -->
                        <div class="absolute right-0 mt-2 w-64 bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform translate-y-2 group-hover:translate-y-0 z-50" id="userDropdown">
                            <div class="p-4 border-b border-gray-100 dark:border-gray-700">
                                <div class="flex items-center space-x-3">
                                    @if(Auth::user()->avatar)
                                        <img src="{{ Auth::user()->avatar_url }}" alt="{{ Auth::user()->name }}" 
                                             class="w-12 h-12 rounded-full object-cover shadow-lg">
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-full flex items-center justify-center shadow-lg">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-bold text-gray-900 dark:text-white">{{ Auth::user()->name }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ Auth::user()->email }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="py-2">
                                <a href="{{ route('users.show', Auth::user()) }}" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas fa-user-cog w-4 h-4 mr-3 text-gray-400 dark:text-gray-500"></i>
                                    View Profile
                                </a>
                                <a href="{{ route('users.edit', Auth::user()) }}" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas fa-edit w-4 h-4 mr-3 text-gray-400 dark:text-gray-500"></i>
                                    Edit Profile
                                </a>
                                <a href="{{ route('user-preferences.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas fa-cog w-4 h-4 mr-3 text-gray-400 dark:text-gray-500"></i>
                                    Preferences
                                </a>
                                <a href="{{ route('help-support.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                                    <i class="fas fa-question-circle w-4 h-4 mr-3 text-gray-400 dark:text-gray-500"></i>
                                    Help & Support
                                </a>
                                <div class="border-t border-gray-100 dark:border-gray-700 my-2"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-3 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors duration-200">
                                        <i class="fas fa-sign-out-alt w-4 h-4 mr-3"></i>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col ml-0 lg:ml-64 main-content-wrapper relative pt-20 overflow-hidden">
            <!-- Main Content -->
            <main class="flex-1 main-content p-6">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Enhanced JavaScript for sidebar and header functionality -->
    <script>
        /**
         * Modern Dashboard Controller
         * Handles sidebar toggle, user menu, and enhanced interactions
         */
        (function() {
            'use strict';
            
            // DOM elements
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarClose = document.getElementById('sidebarClose');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            const userMenuButton = document.getElementById('userMenuButton');
            const userDropdown = document.getElementById('userDropdown');
            
            // Mobile sidebar toggle
            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                sidebarOverlay.classList.toggle('show');
                document.body.classList.toggle('overflow-hidden');
            }
            
            // Close sidebar
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                sidebarOverlay.classList.remove('show');
                document.body.classList.remove('overflow-hidden');
            }
            
            // User menu functionality
            function toggleUserMenu() {
                userDropdown.classList.toggle('opacity-0');
                userDropdown.classList.toggle('invisible');
                userDropdown.classList.toggle('opacity-100');
                userDropdown.classList.toggle('visible');
                userDropdown.classList.toggle('translate-y-2');
                userDropdown.classList.toggle('translate-y-0');
            }
            
            // Close user menu when clicking outside
            function closeUserMenu(event) {
                if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                    userDropdown.classList.add('opacity-0', 'invisible', 'translate-y-2');
                    userDropdown.classList.remove('opacity-100', 'visible', 'translate-y-0');
                }
            }
            
            // Event listeners
            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarClose.addEventListener('click', closeSidebar);
            sidebarOverlay.addEventListener('click', closeSidebar);
            
            // User menu event listeners
            if (userMenuButton && userDropdown) {
                userMenuButton.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleUserMenu();
                });
                
                document.addEventListener('click', closeUserMenu);
            }
            
            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeSidebar();
                    if (userDropdown && !userDropdown.classList.contains('invisible')) {
                        closeUserMenu(e);
                    }
                }
            });
            
            // Close sidebar on window resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeSidebar();
                }
            });
            
            // Initialize sidebar state
            if (window.innerWidth < 1024) {
                sidebar.classList.add('-translate-x-full');
            }
            
            // Enhanced notification animation
            const notificationBadge = document.querySelector('.animate-pulse');
            if (notificationBadge) {
                // Add click animation
                const notificationButton = notificationBadge.closest('button');
                if (notificationButton) {
                    notificationButton.addEventListener('click', function() {
                        notificationBadge.style.transform = 'scale(1.2)';
                        setTimeout(() => {
                            notificationBadge.style.transform = 'scale(1)';
                        }, 150);
                    });
                }
            }
            
            // Add smooth scroll behavior for better UX
            document.documentElement.style.scrollBehavior = 'smooth';
            
            // Enhanced sticky header functionality
            const header = document.querySelector('header');
            let lastScrollTop = 0;
            
            // Force header to stay fixed
            if (header) {
                header.style.position = 'fixed';
                header.style.top = '0';
                header.style.zIndex = '1000';
                header.style.left = '0';
                header.style.right = '0';
                
                // On large screens, adjust left position for sidebar
                if (window.innerWidth >= 1024) {
                    header.style.left = '16rem'; // 64 * 0.25rem = 16rem
                }
            }
            
            // Handle window resize for header positioning
            window.addEventListener('resize', function() {
                if (header) {
                    if (window.innerWidth >= 1024) {
                        header.style.left = '16rem';
                    } else {
                        header.style.left = '0';
                    }
                }
            });
            
            // Add scroll-based header effects
            window.addEventListener('scroll', function() {
                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                
                // Add shadow on scroll
                if (scrollTop > 10) {
                    header.classList.add('shadow-xl');
                } else {
                    header.classList.remove('shadow-xl');
                }
                
                lastScrollTop = scrollTop;
            });
            
            // Ensure header stays visible on mobile
            if (window.innerWidth < 768) {
                header.style.position = 'sticky';
                header.style.top = '0';
                header.style.zIndex = '1000';
            }
            
    // System Health Indicator - Ambulance Lights
    const systemHealthIndicator = document.getElementById('systemHealthIndicator');
    const healthStatusText = document.getElementById('healthStatusText');
    const ambulanceLightsContainer = document.querySelector('.ambulance-lights-container');
    
    // System health status (can be updated from backend or API)
    let systemHealth = {
        status: 'healthy', // 'healthy', 'warning', 'critical', 'offline'
        alerts: 0,
        criticalAlerts: 0,
        lowStockItems: 0
    };

    // Update system health indicator
    function updateSystemHealth(newHealth) {
        systemHealth = { ...systemHealth, ...newHealth };
        
        // Remove all existing classes
        ambulanceLightsContainer.classList.remove('critical', 'disabled');
        
        // Update based on health status
        switch (systemHealth.status) {
            case 'healthy':
                ambulanceLightsContainer.classList.remove('critical', 'disabled');
                healthStatusText.textContent = 'System Healthy';
                healthStatusText.className = 'ml-2 text-xs font-semibold text-green-600 dark:text-green-400';
                break;
                
            case 'warning':
                ambulanceLightsContainer.classList.remove('critical', 'disabled');
                healthStatusText.textContent = `${systemHealth.alerts} Alert${systemHealth.alerts > 1 ? 's' : ''}`;
                healthStatusText.className = 'ml-2 text-xs font-semibold text-yellow-600 dark:text-yellow-400';
                break;
                
            case 'critical':
                ambulanceLightsContainer.classList.add('critical');
                healthStatusText.textContent = 'Critical Alert';
                healthStatusText.className = 'ml-2 text-xs font-semibold text-red-600 dark:text-red-400';
                break;
                
            case 'offline':
                ambulanceLightsContainer.classList.add('disabled');
                healthStatusText.textContent = 'System Offline';
                healthStatusText.className = 'ml-2 text-xs font-semibold text-gray-500 dark:text-gray-400';
                break;
        }
    }

    // System health is now fully driven by real database data

    // Initialize system health
    updateSystemHealth(systemHealth);
    
    // Force animation to start
    function startAmbulanceLights() {
        const lights = document.querySelectorAll('.ambulance-light');
        console.log('Found lights:', lights.length); // Debug log
        
        lights.forEach((light, index) => {
            console.log('Setting up light', index); // Debug log
            
            // Remove any existing animation
            light.style.animation = 'none';
            light.style.animationName = 'none';
            
            // Force reflow
            light.offsetHeight;
            
            // Set up the animation with a slight delay to ensure it starts
            setTimeout(() => {
                light.style.animation = 'ambulancePulse 2s infinite ease-in-out';
                light.style.animationDelay = `${index * 0.5}s`;
                light.style.animationFillMode = 'both';
                
                // Force the animation to start
                light.style.animationPlayState = 'running';
                
                // Ensure visibility
                light.style.opacity = '1';
                light.style.display = 'block';
                light.style.visibility = 'visible';
                
                console.log('Animation applied to light', index);
            }, 50);
        });
    }

    // Alternative: Manual animation using JavaScript
    function startManualAnimation() {
        const lights = document.querySelectorAll('.ambulance-light');
        console.log('Starting manual animation for', lights.length, 'lights');
        
        lights.forEach((light, index) => {
            let isRed = true;
            const delay = index * 500; // 0.5s delay between lights
            
            setTimeout(() => {
                setInterval(() => {
                    if (isRed) {
                        light.style.backgroundColor = '#ffffff';
                        light.style.boxShadow = '0 0 8px #ffffff';
                        light.style.transform = 'scale(1.2)';
                    } else {
                        light.style.backgroundColor = '#ef4444';
                        light.style.boxShadow = '0 0 8px #ef4444';
                        light.style.transform = 'scale(1)';
                    }
                    isRed = !isRed;
                }, 1000); // Change every 1 second
            }, delay);
        });
    }
    
    // Start lights animation immediately
    startAmbulanceLights();
    
    // Also start manual animation as backup
    setTimeout(() => {
        startManualAnimation();
    }, 1000);
    
    // Also start after a short delay to ensure it works
    setTimeout(() => {
        startAmbulanceLights();
    }, 500);
    
    // Animation is now controlled by the ambulance lights themselves
    // Removed test interval to prevent infinite loops
    
    // Start simulation (remove this in production and use real data)
    // Real-time system health updates from database

    // Real-time Alert System Integration with Database
    function updateAlertCounts() {
        // Fetch alert statistics from database
        fetch('/alerts/api/statistics')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const statistics = data.statistics;
                    
                    // Update alert counts from database
                    const alertCount = statistics.active || 0;
                    const criticalCount = statistics.critical || 0;
                    
                    // Update header notification badge
                    const headerAlertCount = document.getElementById('headerAlertCount');
                    const sidebarAlertCount = document.getElementById('alertCount');
                    
                    if (headerAlertCount) {
                        if (alertCount > 0) {
                            headerAlertCount.textContent = alertCount;
                            headerAlertCount.style.display = 'flex';
                            headerAlertCount.classList.add('animate-pulse');
                        } else {
                            headerAlertCount.style.display = 'none';
                            headerAlertCount.classList.remove('animate-pulse');
                        }
                    }
                    
                    if (sidebarAlertCount) {
                        if (alertCount > 0) {
                            sidebarAlertCount.textContent = alertCount;
                            sidebarAlertCount.style.display = 'flex';
                        } else {
                            sidebarAlertCount.style.display = 'none';
                        }
                    }
                    
                    // Update system health based on REAL database alerts
                    if (criticalCount > 0) {
                        updateSystemHealth({
                            status: 'critical',
                            alerts: alertCount,
                            criticalAlerts: criticalCount
                        });
                    } else if (alertCount > 5) {
                        updateSystemHealth({
                            status: 'warning',
                            alerts: alertCount,
                            criticalAlerts: criticalCount
                        });
                    } else if (alertCount > 0) {
                        updateSystemHealth({
                            status: 'warning',
                            alerts: alertCount,
                            criticalAlerts: criticalCount
                        });
                    } else {
                        updateSystemHealth({
                            status: 'healthy',
                            alerts: 0,
                            criticalAlerts: 0
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching alert statistics:', error);
                
                // On error, hide badges
                const headerAlertCount = document.getElementById('headerAlertCount');
                const sidebarAlertCount = document.getElementById('alertCount');
                
                if (headerAlertCount) {
                    headerAlertCount.style.display = 'none';
                    headerAlertCount.classList.remove('animate-pulse');
                }
                
                if (sidebarAlertCount) {
                    sidebarAlertCount.style.display = 'none';
                }
            });
    }

    // Initialize alert badges on page load
    function initializeAlertBadges() {
        const headerAlertCount = document.getElementById('headerAlertCount');
        const sidebarAlertCount = document.getElementById('alertCount');
        
        // Hide badges initially (will be shown if there are alerts)
        if (headerAlertCount) {
            headerAlertCount.style.display = 'none';
            headerAlertCount.classList.remove('animate-pulse');
        }
        
        if (sidebarAlertCount) {
            sidebarAlertCount.style.display = 'none';
        }
    }
    
    // Initialize badges immediately
    initializeAlertBadges();
    
    // Update alert counts on page load
    updateAlertCounts();

    // Update system health every 15 seconds for real-time monitoring
    setInterval(updateAlertCounts, 15000);

    // Enhanced Theme Toggle Functionality with Persistence
    const themeToggle = document.getElementById('themeToggle');
    const themeIcon = document.getElementById('themeIcon');
    const themeDebug = document.getElementById('themeDebug');
    const html = document.documentElement;
    
    // Theme persistence configuration
    const THEME_STORAGE_KEY = 'pharmacy_theme_preference';
    const THEME_ATTRIBUTE = 'data-theme';
    
    // Get saved theme - prioritize database over localStorage
    function getSavedTheme() {
        const userTheme = '{{ $userTheme ?? "auto" }}';
        // Always use database theme if available
        return userTheme;
    }
    
    // Apply theme with persistence
    function applyTheme(theme, saveToStorage = true) {
        console.log('Applying theme:', theme);
        
        // Remove any existing theme classes
        html.classList.remove('dark', 'light');
        
        if (theme === 'dark') {
            html.classList.add('dark');
            html.setAttribute(THEME_ATTRIBUTE, 'dark');
            if (themeIcon) {
                themeIcon.className = 'fas fa-moon text-xl group-hover:scale-110 transition-transform duration-200';
            }
            if (themeDebug) themeDebug.textContent = 'Dark Mode';
        } else {
            html.classList.add('light');
            html.setAttribute(THEME_ATTRIBUTE, 'light');
            if (themeIcon) {
                themeIcon.className = 'fas fa-sun text-xl group-hover:scale-110 transition-transform duration-200';
            }
            if (themeDebug) themeDebug.textContent = 'Light Mode';
        }
        
        // Save to localStorage if requested
        if (saveToStorage) {
            localStorage.setItem(THEME_STORAGE_KEY, theme);
            console.log('Theme saved to localStorage:', theme);
            
            // Only sync with database if not in the middle of a theme change
            const themeChangeInProgress = sessionStorage.getItem('theme_change_in_progress');
            if (!themeChangeInProgress) {
                syncThemeWithDatabase(theme);
            } else {
                console.log('Theme change in progress, skipping database sync');
            }
        }
        
        // Force a reflow to ensure the theme is applied
        html.offsetHeight;
    }
    
    // Sync theme with database preferences
    function syncThemeWithDatabase(theme) {
        console.log('Syncing theme with database:', theme);
        fetch('/user-preferences', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                theme: theme,
                language: '{{ $userLanguage ?? "en" }}',
                timezone: '{{ $userTimezone ?? "Africa/Addis_Ababa" }}',
                date_format: '{{ $userDateFormat ?? "Y-m-d" }}',
                time_format: '{{ $userTimeFormat ?? "24" }}',
                currency: '{{ $userCurrency ?? "ETB" }}',
                notifications: {
                    email: true,
                    sms: false,
                    push: true
                },
                dashboard_widgets: {
                    sales_chart: true,
                    inventory_alerts: true,
                    recent_sales: true,
                    quick_actions: true
                }
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Theme sync response:', data);
        })
        .catch(error => {
            console.log('Theme sync with database failed:', error);
        });
    }
    
    // Force theme refresh from database
    function refreshThemeFromDatabase() {
        console.log('Refreshing theme from database...');
        const userTheme = '{{ $userTheme ?? "auto" }}';
        console.log('Database theme:', userTheme);
        applyTheme(userTheme, true);
    }
    
    // Global function to apply theme immediately (can be called from anywhere)
    window.applyThemeImmediately = function(theme) {
        console.log('Applying theme immediately:', theme);
        const html = document.documentElement;
        
        // Clear the theme change flag since we're applying it now
        sessionStorage.removeItem('theme_change_in_progress');
        
        // Add smooth transition for theme change
        html.style.transition = 'background-color 0.3s ease, color 0.3s ease';
        
        // Remove existing theme classes
        html.classList.remove('dark', 'light');
        
        // Apply new theme
        if (theme === 'dark') {
            html.classList.add('dark');
            html.setAttribute('data-theme', 'dark');
            html.style.colorScheme = 'dark';
            console.log('Applied dark theme immediately');
        } else {
            html.classList.add('light');
            html.setAttribute('data-theme', 'light');
            html.style.colorScheme = 'light';
            console.log('Applied light theme immediately');
        }
        
        // Update localStorage to match
        localStorage.setItem(THEME_STORAGE_KEY, theme);
        
        // Update theme toggle icon if it exists
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            if (theme === 'dark') {
                themeIcon.className = 'fas fa-moon text-xl group-hover:scale-110 transition-transform duration-200';
            } else {
                themeIcon.className = 'fas fa-sun text-xl group-hover:scale-110 transition-transform duration-200';
            }
        }
        
        // Remove transition after theme is applied
        setTimeout(() => {
            html.style.transition = '';
        }, 300);
    };
    
    // Initialize theme on page load
    function initializeTheme() {
        // Check if theme change is in progress
        const themeChangeInProgress = sessionStorage.getItem('theme_change_in_progress');
        console.log('InitializeTheme - flag status:', themeChangeInProgress);
        if (themeChangeInProgress) {
            console.log('Theme change in progress, skipping initializeTheme');
            return;
        }
        
        const savedTheme = getSavedTheme();
        console.log('Initializing with theme:', savedTheme);
        applyTheme(savedTheme, false); // Don't save on initialization
        
        // Ensure theme persists across page reloads
        window.addEventListener('beforeunload', function() {
            const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
            localStorage.setItem(THEME_STORAGE_KEY, currentTheme);
        });
    }
    
    // Theme toggle event listener
    function setupThemeToggle() {
        if (themeToggle && themeIcon) {
            console.log('Setting up theme toggle');
            
            themeToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                console.log('Theme toggle clicked');
                
                const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                console.log('Switching from', currentTheme, 'to', newTheme);
                
                // Apply new theme
                applyTheme(newTheme, true);
                
                // Add toggle animation
                if (themeIcon) {
                    themeIcon.style.transform = 'rotate(360deg)';
                    setTimeout(() => {
                        themeIcon.style.transform = 'rotate(0deg)';
                    }, 300);
                }
            });
        } else {
            console.log('Theme toggle elements not found');
        }
    }
    
    // Prevent system theme from overriding user preference
    function preventSystemThemeOverride() {
        if (window.matchMedia) {
            const mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
            
            // Only apply system theme if no user preference exists
            if (!localStorage.getItem(THEME_STORAGE_KEY)) {
                mediaQuery.addEventListener('change', function(e) {
                    // Only apply if no user preference is set
                    if (!localStorage.getItem(THEME_STORAGE_KEY)) {
                        applyTheme(e.matches ? 'dark' : 'light', true);
                    }
                });
            }
        }
    }
    
    // Monitor for theme changes and ensure persistence
    function monitorThemePersistence() {
        // Check theme every 5 seconds to ensure it hasn't been overridden
        setInterval(function() {
            // Skip monitoring if theme change is in progress
            const themeChangeInProgress = sessionStorage.getItem('theme_change_in_progress');
            console.log('MonitorThemePersistence - flag status:', themeChangeInProgress);
            if (themeChangeInProgress) {
                console.log('Theme change in progress, skipping persistence check');
                return;
            }
            
            const savedTheme = getSavedTheme();
            const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
            
            console.log('Theme check - saved:', savedTheme, 'current:', currentTheme);
            
            // Only correct if there's a real mismatch and it's not a recent theme change
            if (savedTheme !== currentTheme && savedTheme !== 'auto') {
                // Check if this is a recent theme change by looking at localStorage
                const localStorageTheme = localStorage.getItem('pharmacy_theme_preference');
                if (localStorageTheme === currentTheme) {
                    console.log('Theme change in progress, skipping correction');
                    return;
                }
                
                console.log('Theme mismatch detected, correcting...');
                applyTheme(savedTheme, false);
            }
        }, 5000);
        
        // Also check on focus (when user returns to tab)
        window.addEventListener('focus', function() {
            // Skip monitoring if theme change is in progress
            const themeChangeInProgress = sessionStorage.getItem('theme_change_in_progress');
            if (themeChangeInProgress) {
                console.log('Theme change in progress, skipping focus check');
                return;
            }
            
            const savedTheme = getSavedTheme();
            const currentTheme = html.classList.contains('dark') ? 'dark' : 'light';
            
            console.log('Focus theme check - saved:', savedTheme, 'current:', currentTheme);
            
            // Only correct if there's a real mismatch and it's not a recent theme change
            if (savedTheme !== currentTheme && savedTheme !== 'auto') {
                // Check if this is a recent theme change by looking at localStorage
                const localStorageTheme = localStorage.getItem('pharmacy_theme_preference');
                if (localStorageTheme === currentTheme) {
                    console.log('Theme change in progress, skipping focus correction');
                    return;
                }
                
                console.log('Theme corrected on focus');
                applyTheme(savedTheme, false);
            }
        });
    }
    
    // Initialize everything
    // Check if we should skip initialization due to theme change
    const themeChangeInProgress = sessionStorage.getItem('theme_change_in_progress');
    console.log('Initializing functions - flag status:', themeChangeInProgress);
    
    // Only initialize theme functions if no theme change is in progress
    if (!themeChangeInProgress) {
        initializeTheme();
        monitorThemePersistence();
    } else {
        console.log('Skipping theme functions due to theme change in progress');
    }
    
    setupThemeToggle();
    preventSystemThemeOverride();
            
        })();
    </script>

    <!-- Chart.js - For analytics charts -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
    
    @yield('scripts')
</body>
</html>