@extends('layouts.app')

@section('title', 'System Alerts - Analog Pharmacy Management')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-bell text-red-500 mr-3"></i>
                        System Alerts
                    </h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Monitor and manage system alerts, inventory warnings, and critical notifications
                    </p>
                </div>
                
                <!-- Alert Actions -->
                <div class="flex items-center space-x-3">
                    <!-- Generate Alerts Button -->
                    <button onclick="generateAlerts()" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-sync-alt mr-2"></i>
                        Generate Alerts
                    </button>
                    
                    <!-- Clear Cache Button -->
                    <button onclick="clearAlertsCache()" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-trash-alt mr-2"></i>
                        Clear Cache
                    </button>
                    
                    <!-- Create Alert Button -->
                    <button onclick="openCreateAlertModal()" 
                            class="bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Create Alert
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="px-6 py-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-lg">
                        <i class="fas fa-bell text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Alerts</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="totalAlerts">{{ $statistics['total'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Active Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Alerts</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="activeAlerts">{{ $statistics['active'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Critical Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 dark:bg-red-900 rounded-lg">
                        <i class="fas fa-fire text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Critical Alerts</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="criticalAlerts">{{ $statistics['critical'] ?? 0 }}</p>
                    </div>
                </div>
            </div>

            <!-- Resolved Alerts -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 dark:bg-green-900 rounded-lg">
                        <i class="fas fa-check-circle text-green-600 dark:text-green-400 text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Resolved</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white" id="resolvedAlerts">{{ $statistics['resolved'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <form method="GET" action="{{ route('alerts') }}" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
                    <!-- Search -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" 
                               placeholder="Search alerts..." 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                        <select name="status" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">All Statuses</option>
                            @foreach($filterOptions['statuses'] as $status)
                                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Category Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                        <select name="category" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">All Categories</option>
                            @foreach($filterOptions['categories'] as $category)
                                <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                    {{ ucfirst($category) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Priority Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                        <select name="priority" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">All Priorities</option>
                            @foreach($filterOptions['priorities'] as $priority)
                                <option value="{{ $priority }}" {{ request('priority') == $priority ? 'selected' : '' }}>
                                    {{ ucfirst($priority) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Type Filter -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                        <select name="type" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="">All Types</option>
                            @foreach($filterOptions['types'] as $type)
                                <option value="{{ $type }}" {{ request('type') == $type ? 'selected' : '' }}>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg text-sm font-medium transition-colors duration-200 flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Apply Filters
                    </button>
                    
                    <a href="{{ route('alerts') }}" 
                       class="text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 text-sm font-medium">
                        Clear Filters
                    </a>
                </div>
            </form>
        </div>

        <!-- Alerts List -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Alerts</h2>
            </div>

            @if($alerts->count() > 0)
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($alerts as $alert)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-200">
                            <div class="flex items-start justify-between">
                                <div class="flex items-start space-x-4 flex-1">
                                    <!-- Alert Icon -->
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full flex items-center justify-center
                                            @if($alert->priority === 'critical') bg-red-100 dark:bg-red-900
                                            @elseif($alert->priority === 'high') bg-orange-100 dark:bg-orange-900
                                            @elseif($alert->priority === 'medium') bg-yellow-100 dark:bg-yellow-900
                                            @else bg-blue-100 dark:bg-blue-900
                                            @endif">
                                            <i class="{{ $alert->category_icon }} 
                                                @if($alert->priority === 'critical') text-red-600 dark:text-red-400
                                                @elseif($alert->priority === 'high') text-orange-600 dark:text-orange-400
                                                @elseif($alert->priority === 'medium') text-yellow-600 dark:text-yellow-400
                                                @else text-blue-600 dark:text-blue-400
                                                @endif text-lg"></i>
                                        </div>
                                    </div>

                                    <!-- Alert Content -->
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                {{ $alert->title }}
                                            </h3>
                                            
                                            <!-- Priority Badge -->
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($alert->priority === 'critical') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @elseif($alert->priority === 'high') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                @elseif($alert->priority === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @else bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                @endif">
                                                {{ ucfirst($alert->priority) }}
                                            </span>

                                            <!-- Status Badge -->
                                            <span class="px-2 py-1 text-xs font-medium rounded-full
                                                @if($alert->status === 'active') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                @elseif($alert->status === 'acknowledged') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                @elseif($alert->status === 'resolved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200
                                                @endif">
                                                {{ ucfirst($alert->status) }}
                                            </span>
                                        </div>

                                        <p class="text-gray-600 dark:text-gray-400 mb-3">
                                            {{ $alert->message }}
                                        </p>

                                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                            <span class="flex items-center">
                                                <i class="fas fa-tag mr-1"></i>
                                                {{ ucfirst($alert->category) }}
                                            </span>
                                            <span class="flex items-center">
                                                <i class="fas fa-clock mr-1"></i>
                                                {{ $alert->time_ago }}
                                            </span>
                                            @if($alert->user)
                                                <span class="flex items-center">
                                                    <i class="fas fa-user mr-1"></i>
                                                    {{ $alert->user->name }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Alert Actions -->
                                <div class="flex items-center space-x-2">
                                    @if($alert->status === 'active')
                                        <button onclick="acknowledgeAlert({{ $alert->id }})" 
                                                class="bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors duration-200">
                                            <i class="fas fa-check mr-1"></i>
                                            Acknowledge
                                        </button>
                                    @endif

                                    @if($alert->status === 'acknowledged')
                                        <button onclick="resolveAlert({{ $alert->id }})" 
                                                class="bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors duration-200">
                                            <i class="fas fa-check-double mr-1"></i>
                                            Resolve
                                        </button>
                                    @endif

                                    <button onclick="dismissAlert({{ $alert->id }})" 
                                            class="bg-gray-600 hover:bg-gray-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors duration-200">
                                        <i class="fas fa-times mr-1"></i>
                                        Dismiss
                                    </button>

                                    <button onclick="deleteAlert({{ $alert->id }})" 
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors duration-200">
                                        <i class="fas fa-trash mr-1"></i>
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $alerts->links() }}
                </div>
            @else
                <div class="p-12 text-center">
                    <i class="fas fa-bell-slash text-gray-400 text-4xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No Alerts Found</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">
                        @if(request()->hasAny(['search', 'status', 'category', 'priority', 'type']))
                            Try adjusting your filters to see more alerts.
                        @else
                            Your system is running smoothly with no active alerts.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'status', 'category', 'priority', 'type']))
                        <button onclick="generateAlerts()" 
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Generate Sample Alerts
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Create Alert Modal -->
<div id="createAlertModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Create New Alert</h3>
            </div>
            
            <form id="createAlertForm" class="p-6 space-y-4">
                @csrf
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Title</label>
                    <input type="text" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                    <textarea name="message" rows="3" required 
                              class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Category</label>
                        <select name="category" required 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="inventory">Inventory</option>
                            <option value="expiry">Expiry</option>
                            <option value="system">System</option>
                            <option value="sales">Sales</option>
                            <option value="customer">Customer</option>
                            <option value="purchase">Purchase</option>
                            <option value="supplier">Supplier</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Priority</label>
                        <select name="priority" required 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Type</label>
                        <select name="type" required 
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            <option value="info">Info</option>
                            <option value="warning" selected>Warning</option>
                            <option value="error">Error</option>
                            <option value="success">Success</option>
                            <option value="critical">Critical</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Expires At</label>
                        <input type="datetime-local" name="expires_at" 
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-3 pt-4">
                    <button type="button" onclick="closeCreateAlertModal()" 
                            class="px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors duration-200">
                        Create Alert
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Alert Management Functions
function acknowledgeAlert(alertId) {
    fetch(`/alerts/${alertId}/acknowledge`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Alert acknowledged successfully', 'success');
            location.reload();
        } else {
            showNotification('Failed to acknowledge alert', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function resolveAlert(alertId) {
    fetch(`/alerts/${alertId}/resolve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Alert resolved successfully', 'success');
            location.reload();
        } else {
            showNotification('Failed to resolve alert', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function dismissAlert(alertId) {
    fetch(`/alerts/${alertId}/dismiss`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Alert dismissed successfully', 'success');
            location.reload();
        } else {
            showNotification('Failed to dismiss alert', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function deleteAlert(alertId) {
    if (confirm('Are you sure you want to delete this alert? This action cannot be undone.')) {
        fetch(`/alerts/${alertId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Alert deleted successfully', 'success');
                location.reload();
            } else {
                showNotification('Failed to delete alert', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred', 'error');
        });
    }
}

function generateAlerts() {
    showNotification('Generating alerts...', 'info');
    
    fetch('/alerts/generate', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Alerts generated successfully', 'success');
            location.reload();
        } else {
            showNotification('Failed to generate alerts', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

function clearAlertsCache() {
    fetch('/alerts/clear-cache', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Cache cleared successfully', 'success');
            location.reload();
        } else {
            showNotification('Failed to clear cache', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
}

// Modal Functions
function openCreateAlertModal() {
    document.getElementById('createAlertModal').classList.remove('hidden');
}

function closeCreateAlertModal() {
    document.getElementById('createAlertModal').classList.add('hidden');
    document.getElementById('createAlertForm').reset();
}

// Create Alert Form
document.getElementById('createAlertForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch('/alerts', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Alert created successfully', 'success');
            closeCreateAlertModal();
            location.reload();
        } else {
            showNotification('Failed to create alert', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred', 'error');
    });
});

// Notification System
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg text-white font-medium transition-all duration-300 transform translate-x-full`;
    
    switch(type) {
        case 'success':
            notification.classList.add('bg-green-600');
            break;
        case 'error':
            notification.classList.add('bg-red-600');
            break;
        case 'warning':
            notification.classList.add('bg-yellow-600');
            break;
        default:
            notification.classList.add('bg-blue-600');
    }
    
    notification.textContent = message;
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.remove('translate-x-full');
    }, 100);
    
    // Animate out and remove
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Auto-refresh alerts every 30 seconds
setInterval(function() {
    fetch('/alerts/api/statistics')
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('totalAlerts').textContent = data.statistics.total;
            document.getElementById('activeAlerts').textContent = data.statistics.active;
            document.getElementById('criticalAlerts').textContent = data.statistics.critical;
            document.getElementById('resolvedAlerts').textContent = data.statistics.resolved;
        }
    })
    .catch(error => {
        console.error('Auto-refresh error:', error);
    });
}, 30000);

// Close modal when clicking outside
document.getElementById('createAlertModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeCreateAlertModal();
    }
});
</script>
@endsection
