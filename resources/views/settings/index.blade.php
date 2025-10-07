@extends('layouts.app')

@section('title', 'Settings - Analog Pharmacy')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">System Settings</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Manage system configuration and user settings</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="clearSystemCache()" 
                            class="inline-flex items-center px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-broom mr-2"></i>
                        Clear Cache
                    </button>
                    <button onclick="getSystemInfo()" 
                            class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-info-circle mr-2"></i>
                        System Info
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- System Health Status -->
        <div class="mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">System Health</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center
                                {{ $stats['system_health']['database'] === 'connected' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                <i class="fas fa-database {{ $stats['system_health']['database'] === 'connected' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"></i>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Database</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $stats['system_health']['database'] }}</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center
                                {{ $stats['system_health']['cache'] === 'working' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                <i class="fas fa-memory {{ $stats['system_health']['cache'] === 'working' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"></i>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Cache</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $stats['system_health']['cache'] }}</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center
                                {{ $stats['system_health']['storage'] === 'writable' ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }}">
                                <i class="fas fa-hdd {{ $stats['system_health']['storage'] === 'writable' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}"></i>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Storage</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $stats['system_health']['storage'] }}</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="w-12 h-12 mx-auto mb-3 rounded-full flex items-center justify-center
                                {{ $stats['system_health']['overall'] === 'healthy' ? 'bg-green-100 dark:bg-green-900' : ($stats['system_health']['overall'] === 'warning' ? 'bg-yellow-100 dark:bg-yellow-900' : 'bg-red-100 dark:bg-red-900') }}">
                                <i class="fas fa-heartbeat {{ $stats['system_health']['overall'] === 'healthy' ? 'text-green-600 dark:text-green-400' : ($stats['system_health']['overall'] === 'warning' ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400') }}"></i>
                            </div>
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">Overall</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400 capitalize">{{ $stats['system_health']['overall'] }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Settings Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- User Management -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-users text-green-600 dark:text-green-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Management</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage users, roles, and permissions</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total Users</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $stats['total_users'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Active Users</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $stats['active_users'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Locked Users</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $stats['locked_users'] }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('users.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-arrow-right mr-2"></i>
                            Manage Users
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Information -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-server text-blue-600 dark:text-blue-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">System Information</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">View system details and configuration</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">PHP Version</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ PHP_VERSION }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Laravel Version</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ app()->version() }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Memory Limit</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ ini_get('memory_limit') }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button onclick="getSystemInfo()" 
                                class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-info-circle mr-2"></i>
                            View Details
                        </button>
                    </div>
                </div>
            </div>

            <!-- Activity Log -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-history text-purple-600 dark:text-purple-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Activity Log</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Monitor user activities and system events</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Total Activities</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $stats['recent_activities'] }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Today</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ \App\Models\UserActivity::whereDate('created_at', today())->count() }}</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">This Week</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ \App\Models\UserActivity::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count() }}</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('users.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-eye mr-2"></i>
                            View Activities
                        </a>
                    </div>
                </div>
            </div>

            <!-- Security Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-shield-alt text-red-600 dark:text-red-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Security Settings</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Configure security policies and access control</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Password Policy</span>
                            <span class="font-medium text-green-600 dark:text-green-400">Active</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Account Locking</span>
                            <span class="font-medium text-green-600 dark:text-green-400">Enabled</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Session Management</span>
                            <span class="font-medium text-green-600 dark:text-green-400">Active</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-cog mr-2"></i>
                            Configure
                        </button>
                    </div>
                </div>
            </div>

            <!-- Backup & Restore -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-yellow-100 dark:bg-yellow-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-database text-yellow-600 dark:text-yellow-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Backup & Restore</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Manage database backups and system restore</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">Last Backup</span>
                            <span class="font-medium text-gray-900 dark:text-white">Never</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Backup Size</span>
                            <span class="font-medium text-gray-900 dark:text-white">N/A</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Auto Backup</span>
                            <span class="font-medium text-gray-500 dark:text-gray-400">Disabled</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-download mr-2"></i>
                            Create Backup
                        </button>
                    </div>
                </div>
            </div>

            <!-- Email Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow hover:shadow-lg transition-shadow duration-200">
                <div class="p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                <i class="fas fa-envelope text-indigo-600 dark:text-indigo-400 text-xl"></i>
                            </div>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Email Settings</h3>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Configure email notifications and SMTP</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500 dark:text-gray-400">SMTP Status</span>
                            <span class="font-medium text-gray-500 dark:text-gray-400">Not Configured</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Notifications</span>
                            <span class="font-medium text-gray-500 dark:text-gray-400">Disabled</span>
                        </div>
                        <div class="flex justify-between text-sm mt-1">
                            <span class="text-gray-500 dark:text-gray-400">Email Queue</span>
                            <span class="font-medium text-gray-500 dark:text-gray-400">0</span>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                            <i class="fas fa-cog mr-2"></i>
                            Configure
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Info Modal -->
<div id="systemInfoModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-2xl w-full">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">System Information</h3>
            </div>
            <div class="p-6">
                <div id="systemInfoContent" class="space-y-4">
                    <!-- System info will be loaded here -->
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button onclick="closeSystemInfoModal()" 
                        class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Clear system cache
function clearSystemCache() {
    if (confirm('Are you sure you want to clear the system cache? This may temporarily slow down the application.')) {
        fetch('/settings/clear-cache', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while clearing cache.');
        });
    }
}

// Get system information
function getSystemInfo() {
    fetch('/settings/system-info', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        const content = document.getElementById('systemInfoContent');
        content.innerHTML = '';
        
        Object.entries(data).forEach(([key, value]) => {
            const div = document.createElement('div');
            div.className = 'flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700';
            div.innerHTML = `
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">${key.replace(/_/g, ' ').toUpperCase()}</span>
                <span class="text-sm text-gray-900 dark:text-white">${value}</span>
            `;
            content.appendChild(div);
        });
        
        document.getElementById('systemInfoModal').classList.remove('hidden');
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while fetching system information.');
    });
}

// Close system info modal
function closeSystemInfoModal() {
    document.getElementById('systemInfoModal').classList.add('hidden');
}
</script>
@endsection
