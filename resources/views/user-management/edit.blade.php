@extends('layouts.app')

@section('title', 'Edit User - Analog Pharmacy')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit User</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Update user information and permissions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('users.show', $user) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-eye mr-2"></i>
                        View User
                    </a>
                    <a href="{{ route('users.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Users
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Section -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <form id="editUserForm" class="p-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Basic Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Full Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Email Address <span class="text-red-500">*</span>
                            </label>
                            <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" required
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('email') border-red-500 @enderror">
                            @error('email')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                New Password (leave blank to keep current)
                            </label>
                            <div class="relative">
                                <input type="password" name="password" id="password"
                                       class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('password') border-red-500 @enderror">
                                <button type="button" onclick="togglePassword('password')" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <i class="fas fa-eye" id="password-eye"></i>
                                </button>
                            </div>
                            @error('password')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Confirm New Password
                            </label>
                            <div class="relative">
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                       class="w-full px-3 py-2 pr-10 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <button type="button" onclick="togglePassword('password_confirmation')" 
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <i class="fas fa-eye" id="password_confirmation-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Contact Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Phone Number
                            </label>
                            <input type="tel" name="phone" id="phone" value="{{ old('phone', $user->phone) }}"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('phone') border-red-500 @enderror">
                            @error('phone')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Department
                            </label>
                            <select name="department" id="department"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('department') border-red-500 @enderror">
                                <option value="">Select Department</option>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept }}" {{ old('department', $user->department) == $dept ? 'selected' : '' }}>
                                        {{ $dept }}
                                    </option>
                                @endforeach
                                <option value="new" {{ old('department') == 'new' ? 'selected' : '' }}>+ Add New Department</option>
                            </select>
                            @error('department')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- New Department Input -->
                    <div id="newDepartmentDiv" class="mt-4 hidden">
                        <label for="new_department" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            New Department Name
                        </label>
                        <input type="text" name="new_department" id="new_department"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Profile Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Status -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Status <span class="text-red-500">*</span>
                            </label>
                            <select name="status" id="status" required
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('status') border-red-500 @enderror">
                                <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Active</option>
                                <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspended</option>
                            </select>
                            @error('status')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Avatar -->
                        <div>
                            <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Profile Picture
                            </label>
                            @if($user->avatar)
                                <div class="mb-2">
                                    <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="w-16 h-16 rounded-full object-cover">
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Current avatar</p>
                                </div>
                            @endif
                            <input type="file" name="avatar" id="avatar" accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('avatar') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">JPG, PNG, GIF up to 2MB</p>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mt-6">
                        <label for="notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Notes
                        </label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('notes') border-red-500 @enderror">{{ old('notes', $user->notes) }}</textarea>
                        @error('notes')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Roles and Permissions -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Roles and Permissions</h3>
                    
                    <!-- Roles -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Assign Roles
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($roles as $role)
                                <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                    <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                           {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($role->name) }}</div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ $role->permissions->count() }} permissions</div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('roles')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Permissions -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            Assign Permissions
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 max-h-64 overflow-y-auto">
                            @foreach($permissions as $permission)
                                <label class="flex items-center p-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                           {{ in_array($permission->id, old('permissions', $user->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                           class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                    <span class="ml-2 text-sm text-gray-900 dark:text-white">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('permissions')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('users.index') }}" 
                       class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Update User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Department selection handler
document.getElementById('department').addEventListener('change', function() {
    const newDeptDiv = document.getElementById('newDepartmentDiv');
    if (this.value === 'new') {
        newDeptDiv.classList.remove('hidden');
        document.getElementById('new_department').required = true;
    } else {
        newDeptDiv.classList.add('hidden');
        document.getElementById('new_department').required = false;
    }
});

// Password confirmation validation
document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (password && password !== confirmPassword) {
        this.setCustomValidity('Passwords do not match');
    } else {
        this.setCustomValidity('');
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('password_confirmation').value;
    
    if (password && password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match');
        return false;
    }
});

// Sophisticated Notification System (from suppliers page)
(function() {
    'use strict';
    
    class NotificationService {
        constructor() {
            this.container = null;
            this.notifications = [];
            this.maxNotifications = 5;
            this.defaultDuration = 4000;
            this.animationDuration = 300;
            this.init();
        }

        init() {
            this.createContainer();
        }

        createContainer() {
            this.container = document.createElement('div');
            this.container.id = 'notification-container';
            this.container.className = 'fixed top-4 right-4 z-[9999] space-y-2 pointer-events-none';
            document.body.appendChild(this.container);
        }

        show(message, type = 'info', duration = null) {
            const notification = this.createNotification(message, type);
            this.addToQueue(notification);
            this.animateIn(notification);
            this.scheduleRemoval(notification, duration);
            return notification;
        }

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

        addToQueue(notification) {
            this.notifications.push(notification);
            this.container.appendChild(notification);
            
            // Remove oldest if over limit
            if (this.notifications.length > this.maxNotifications) {
                this.removeOldest();
            }
        }

        animateIn(notification) {
            setTimeout(() => {
                notification.classList.add('translate-x-0');
            }, 10);
        }

        scheduleRemoval(notification, duration = null) {
            const removeDuration = duration || this.defaultDuration;
            setTimeout(() => {
                this.close(notification);
            }, removeDuration);
        }

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

        removeOldest() {
            if (this.notifications.length > 0) {
                this.close(this.notifications[0]);
            }
        }

        clear() {
            this.notifications.forEach(notification => {
                this.close(notification);
            });
        }

        success(message, duration = null) {
            return this.show(message, 'success', duration);
        }

        error(message, duration = null) {
            return this.show(message, 'error', duration);
        }

        warning(message, duration = null) {
            return this.show(message, 'warning', duration);
        }

        info(message, duration = null) {
            return this.show(message, 'info', duration);
        }
    }

    // Initialize global notification service
    window.NotificationService = new NotificationService();
})();

// Sophisticated Loading Spinner Implementation (from suppliers page)
function showUserLoading(message = 'Loading...') {
    // Remove existing loading if any
    hideUserLoading();
    
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'userLoading';
    loadingDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
    loadingDiv.innerHTML = `
        <div class="text-center">
            <!-- User icon container -->
            <div class="relative w-16 h-16 mx-auto mb-6">
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-user text-green-500 text-3xl animate-pulse"></i>
                </div>
            </div>
            
            <!-- Progress text -->
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${message}</h3>
            </div>
            
            <!-- Horizontal progress bar -->
            <div class="w-64 mx-auto mb-4">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-full rounded-full transition-all duration-300 ease-out" 
                         id="userProgressBar" style="width: 0%"></div>
                </div>
            </div>
            
            <!-- Progress percentage and dots -->
            <div class="flex items-center justify-center space-x-4">
                <div class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    <span id="userLoadingProgress">0</span>%
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(loadingDiv);
    
    // Start progress animation
    startUserProgressAnimation();
}

function startUserProgressAnimation() {
    const progressBar = document.getElementById('userProgressBar');
    const progressText = document.getElementById('userLoadingProgress');
    
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
    window.userLoadingAnimation = animation;
}

function hideUserLoading() {
    const loadingDiv = document.getElementById('userLoading');
    if (loadingDiv) {
        loadingDiv.remove();
    }
    
    // Clear any running animation
    if (window.userLoadingAnimation) {
        clearInterval(window.userLoadingAnimation);
        window.userLoadingAnimation = null;
    }
}

function showUserActionLoading(action) {
    const messages = {
        'updating': 'Updating user information...',
        'saving': 'Saving changes...',
        'processing': 'Processing user data...'
    };
    showUserLoading(messages[action] || 'Loading...');
}

function updateUserLoadingMessage(message) {
    const loadingDiv = document.getElementById('userLoading');
    if (loadingDiv) {
        const messageEl = loadingDiv.querySelector('h3');
        if (messageEl) {
            messageEl.textContent = message;
        }
    }
}

function showUserSuccessNotification(message) {
    // Remove existing loading if any
    hideUserLoading();
    
    const successDiv = document.createElement('div');
    successDiv.id = 'userSuccessNotification';
    successDiv.className = 'fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm flex items-center justify-center z-[9999]';
    successDiv.innerHTML = `
        <div class="text-center">
            <!-- User success icon container -->
            <div class="relative w-16 h-16 mx-auto mb-6">
                <div class="absolute inset-0 flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-500 text-3xl animate-pulse"></i>
                </div>
            </div>
            
            <!-- Success message -->
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">${message}</h3>
            </div>
            
            <!-- Horizontal progress bar -->
            <div class="w-64 mx-auto mb-4">
                <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-3 overflow-hidden">
                    <div class="h-full rounded-full transition-all duration-300 ease-out" 
                         id="userSuccessProgressBar" style="width: 100%; background: linear-gradient(to right, #10b981, #059669)"></div>
                </div>
            </div>
            
            <!-- Progress percentage and dots -->
            <div class="flex items-center justify-center space-x-4">
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <span id="userSuccessProgress">100</span>%
                </div>
                <div class="flex items-center space-x-1">
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.2s"></div>
                    <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse" style="animation-delay: 0.4s"></div>
                </div>
            </div>
        </div>
    `;
    document.body.appendChild(successDiv);
    
    // Auto-hide after 2 seconds
    setTimeout(() => {
        hideUserSuccessNotification();
    }, 2000);
}

function hideUserSuccessNotification() {
    const successDiv = document.getElementById('userSuccessNotification');
    if (successDiv) {
        successDiv.remove();
    }
}

// AJAX form submission with immediate refresh (like sales page)
document.getElementById('editUserForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    // Remove new_department from formData if department is not 'new'
    const departmentValue = formData.get('department');
    if (departmentValue !== 'new') {
        formData.delete('new_department');
    }
    
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    try {
        // Show sophisticated loading spinner
        showUserActionLoading('updating');
        
        // Show loading state on button
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating User...';
        submitBtn.disabled = true;
        
        const response = await fetch('{{ route("users.update", $user) }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Hide loading and show sophisticated success notification
            hideUserLoading();
            showUserSuccessNotification('User updated successfully!');
            
            // Redirect after delay
            setTimeout(() => {
                window.location.href = '/users?refresh=true';
            }, 2000);
        } else {
            // Show validation errors
            if (data.errors) {
                Object.keys(data.errors).forEach(field => {
                    const errorElement = document.querySelector(`#${field}-error`);
                    if (errorElement) {
                        errorElement.textContent = data.errors[field][0];
                        errorElement.style.display = 'block';
                    }
                });
            } else {
                // Hide loading and show sophisticated error notification
                hideUserLoading();
                if (window.NotificationService) {
                    window.NotificationService.error('Failed to update user: ' + (data.message || 'Unknown error'));
                }
            }
        }
        
    } catch (error) {
        console.error('Error updating user:', error);
        // Hide loading and show sophisticated error notification
        hideUserLoading();
        if (window.NotificationService) {
            window.NotificationService.error('Failed to update user. Please try again.');
        }
    } finally {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});

// Password toggle functionality
function togglePassword(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const eyeIcon = document.getElementById(fieldId + '-eye');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordField.type = 'password';
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>
@endsection
