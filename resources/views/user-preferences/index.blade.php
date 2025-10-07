@extends('layouts.app')

@section('title', 'User Preferences - Analog Pharmacy Management System')
@section('page-title', 'User Preferences')
@section('page-description', 'Customize your experience and settings')

@section('content')
<div class="max-w-7xl mx-auto">
    <!-- Header -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-50 to-indigo-100 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-6 border border-blue-200 dark:border-gray-600">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">User Preferences</h1>
                    <p class="text-gray-600 dark:text-gray-300 text-lg">Customize your experience and system settings</p>
                </div>
                <div class="text-right">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-cog text-white text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Preferences Form -->
        <div class="lg:col-span-2">
            <form id="preferencesForm" class="space-y-8">
                <!-- Appearance Settings -->
                <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                            <i class="fas fa-palette text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Appearance</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Customize the look and feel</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Theme</label>
                            <select name="theme" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="auto" {{ $preferences['theme'] === 'auto' ? 'selected' : '' }}>Auto (System)</option>
                                <option value="light" {{ $preferences['theme'] === 'light' ? 'selected' : '' }}>Light</option>
                                <option value="dark" {{ $preferences['theme'] === 'dark' ? 'selected' : '' }}>Dark</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Language</label>
                            <select name="language" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="en" {{ $preferences['language'] === 'en' ? 'selected' : '' }}>English</option>
                                <option value="am" {{ $preferences['language'] === 'am' ? 'selected' : '' }}>አማርኛ</option>
                                <option value="ar" {{ $preferences['language'] === 'ar' ? 'selected' : '' }}>العربية</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Date & Time Settings -->
                <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                            <i class="fas fa-clock text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Date & Time</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Configure date and time formats</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Timezone</label>
                            <select name="timezone" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="Africa/Addis_Ababa" {{ $preferences['timezone'] === 'Africa/Addis_Ababa' ? 'selected' : '' }}>Addis Ababa (GMT+3)</option>
                                <option value="UTC" {{ $preferences['timezone'] === 'UTC' ? 'selected' : '' }}>UTC (GMT+0)</option>
                                <option value="America/New_York" {{ $preferences['timezone'] === 'America/New_York' ? 'selected' : '' }}>New York (GMT-5)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Date Format</label>
                            <select name="date_format" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="Y-m-d" {{ $preferences['date_format'] === 'Y-m-d' ? 'selected' : '' }}>2024-01-15</option>
                                <option value="m/d/Y" {{ $preferences['date_format'] === 'm/d/Y' ? 'selected' : '' }}>01/15/2024</option>
                                <option value="d/m/Y" {{ $preferences['date_format'] === 'd/m/Y' ? 'selected' : '' }}>15/01/2024</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Time Format</label>
                            <select name="time_format" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="24" {{ $preferences['time_format'] === '24' ? 'selected' : '' }}>24 Hour</option>
                                <option value="12" {{ $preferences['time_format'] === '12' ? 'selected' : '' }}>12 Hour</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Business Settings -->
                <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                            <i class="fas fa-building text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Business Settings</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Configure business preferences</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Default Currency</label>
                            <select name="currency" class="w-full px-4 py-3 border border-gray-200 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="ETB" {{ $preferences['currency'] === 'ETB' ? 'selected' : '' }}>Ethiopian Birr (ETB)</option>
                                <option value="USD" {{ $preferences['currency'] === 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                                <option value="EUR" {{ $preferences['currency'] === 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notifications</label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="notifications[email]" value="1" {{ $preferences['notifications']['email'] ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Email Notifications</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="notifications[sms]" value="1" {{ $preferences['notifications']['sms'] ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">SMS Notifications</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="notifications[push]" value="1" {{ $preferences['notifications']['push'] ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Push Notifications</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Widgets -->
                <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-8">
                    <div class="flex items-center mb-6">
                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center shadow-lg mr-4">
                            <i class="fas fa-th-large text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white">Dashboard Widgets</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Choose which widgets to display</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="dashboard_widgets[sales_chart]" value="1" {{ $preferences['dashboard_widgets']['sales_chart'] ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Sales Chart</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="dashboard_widgets[inventory_alerts]" value="1" {{ $preferences['dashboard_widgets']['inventory_alerts'] ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Inventory Alerts</span>
                            </label>
                        </div>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" name="dashboard_widgets[recent_sales]" value="1" {{ $preferences['dashboard_widgets']['recent_sales'] ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Recent Sales</span>
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" name="dashboard_widgets[quick_actions]" value="1" {{ $preferences['dashboard_widgets']['quick_actions'] ? 'checked' : '' }} class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Quick Actions</span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-save mr-2"></i>
                        Save Preferences
                    </button>
                    <button type="button" id="resetPreferences" class="flex-1 bg-gradient-to-r from-gray-500 to-gray-600 hover:from-gray-600 hover:to-gray-700 text-white font-bold py-3 px-6 rounded-xl transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                        <i class="fas fa-undo mr-2"></i>
                        Reset to Defaults
                    </button>
                </div>
            </form>
        </div>

        <!-- Quick Actions Sidebar -->
        <div class="space-y-6">
            <!-- Profile Summary -->
            <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Profile Summary</h3>
                <div class="space-y-3">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg mr-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $user->name }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">{{ $user->email }}</p>
                        </div>
                    </div>
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg mr-3">
                            <i class="fas fa-briefcase text-white"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $user->department ?? 'No Department' }}</p>
                            <p class="text-sm text-gray-600 dark:text-gray-300">Department</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="card-hover bg-gradient-to-br from-white to-gray-50 dark:from-gray-800 dark:to-gray-900 rounded-2xl shadow-lg border border-gray-100 dark:border-gray-700 p-6">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Quick Links</h3>
                <div class="space-y-3">
                    <a href="{{ route('users.show', $user) }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-user-cog w-5 h-5 text-blue-500 mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300">View Profile</span>
                    </a>
                    <a href="{{ route('users.edit', $user) }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-edit w-5 h-5 text-green-500 mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300">Edit Profile</span>
                    </a>
                    <a href="{{ route('help-support.index') }}" class="flex items-center p-3 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-question-circle w-5 h-5 text-purple-500 mr-3"></i>
                        <span class="text-gray-700 dark:text-gray-300">Help & Support</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('preferencesForm');
    const resetBtn = document.getElementById('resetPreferences');

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());
        
        // Process checkboxes
        data.notifications = {
            email: formData.has('notifications[email]'),
            sms: formData.has('notifications[sms]'),
            push: formData.has('notifications[push]')
        };
        
        data.dashboard_widgets = {
            sales_chart: formData.has('dashboard_widgets[sales_chart]'),
            inventory_alerts: formData.has('dashboard_widgets[inventory_alerts]'),
            recent_sales: formData.has('dashboard_widgets[recent_sales]'),
            quick_actions: formData.has('dashboard_widgets[quick_actions]')
        };

        // Show loading state
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
        submitBtn.disabled = true;

        fetch('/user-preferences', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Preferences saved successfully!', 'success');
                
                // Set flag to prevent middleware from overriding
                sessionStorage.setItem('theme_change_in_progress', 'true');
                console.log('Theme change flag set');
                
                // Apply theme change immediately using global function
                const theme = data.preferences?.theme || 'light';
                console.log('Applying theme:', theme);
                
                // Force theme application with aggressive approach
                const html = document.documentElement;
                
                // Remove all existing theme classes and attributes
                html.classList.remove('dark', 'light');
                html.removeAttribute('data-theme');
                html.style.colorScheme = '';
                
                // Apply new theme
                if (theme === 'dark') {
                    html.classList.add('dark');
                    html.setAttribute('data-theme', 'dark');
                    html.style.colorScheme = 'dark';
                    console.log('Applied dark theme aggressively');
                } else {
                    html.classList.add('light');
                    html.setAttribute('data-theme', 'light');
                    html.style.colorScheme = 'light';
                    console.log('Applied light theme aggressively');
                }
                
                // Update localStorage
                localStorage.setItem('pharmacy_theme_preference', theme);
                
                // Update theme toggle icon if it exists
                const themeIcon = document.getElementById('themeIcon');
                if (themeIcon) {
                    if (theme === 'dark') {
                        themeIcon.className = 'fas fa-moon text-xl group-hover:scale-110 transition-transform duration-200';
                    } else {
                        themeIcon.className = 'fas fa-sun text-xl group-hover:scale-110 transition-transform duration-200';
                    }
                }
                
                // Clear the flag after a reasonable delay to ensure all theme functions are done
                setTimeout(() => {
                    sessionStorage.removeItem('theme_change_in_progress');
                    console.log('Theme change flag cleared');
                }, 3000);
                
            } else {
                showNotification(data.message || 'Failed to save preferences', 'error');
            }
        })
        .catch(error => {
            showNotification('An error occurred while saving preferences', 'error');
        })
        .finally(() => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    });

    // Handle reset button
    resetBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to reset all preferences to defaults?')) {
            fetch('/user-preferences/reset', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showNotification('Preferences reset to defaults!', 'success');
                    location.reload();
                } else {
                    showNotification(data.message || 'Failed to reset preferences', 'error');
                }
            })
            .catch(error => {
                showNotification('An error occurred while resetting preferences', 'error');
            });
        }
    });

    // Notification function
    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 p-4 rounded-xl shadow-lg z-50 ${
            type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} mr-2"></i>
                ${message}
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endsection
