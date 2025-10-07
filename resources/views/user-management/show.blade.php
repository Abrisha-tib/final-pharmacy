@extends('layouts.app')

@section('title', 'User Details - Analog Pharmacy')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">User Details</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">View user information and activity</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('users.edit', $user) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit User
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

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- User Profile Card -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6">
                        <!-- Avatar and Basic Info -->
                        <div class="text-center">
                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                 class="w-24 h-24 rounded-full mx-auto mb-4 object-cover">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $user->name }}</h2>
                            <p class="text-gray-600 dark:text-gray-400">{{ $user->email }}</p>
                            
                            <!-- Status Badge -->
                            <div class="mt-3">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Active
                                    </span>
                                @elseif($user->status === 'inactive')
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                        <i class="fas fa-pause-circle mr-1"></i>
                                        Inactive
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                        <i class="fas fa-ban mr-1"></i>
                                        Suspended
                                    </span>
                                @endif
                                
                                @if($user->isLocked())
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 ml-2">
                                        <i class="fas fa-lock mr-1"></i>
                                        Locked
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="mt-6 space-y-4">
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $user->phone ?? 'Not provided' }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Department</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $user->department ?? 'Not assigned' }}</p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Login</label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                                </p>
                            </div>
                            
                            <div>
                                <label class="text-sm font-medium text-gray-500 dark:text-gray-400">Member Since</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $user->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-6 space-y-2">
                            <button onclick="resetPassword({{ $user->id }})" 
                                    class="w-full px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-key mr-2"></i>
                                Reset Password
                            </button>
                            
                            <button onclick="toggleLock({{ $user->id }})" 
                                    class="w-full px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                <i class="fas fa-lock mr-2"></i>
                                {{ $user->isLocked() ? 'Unlock' : 'Lock' }} Account
                            </button>
                            
                            @if($user->id !== auth()->id())
                                <button onclick="deleteUser({{ $user->id }})" 
                                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                                    <i class="fas fa-trash mr-2"></i>
                                    Delete User
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Details and Activity -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Roles and Permissions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Roles & Permissions</h3>
                    </div>
                    <div class="p-6">
                        <!-- Roles -->
                        <div class="mb-6">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Assigned Roles</h4>
                            @if($user->roles->count() > 0)
                                <div class="flex flex-wrap gap-2">
                                    @foreach($user->roles as $role)
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            <i class="fas fa-user-tag mr-1"></i>
                                            {{ ucfirst($role->name) }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">No roles assigned</p>
                            @endif
                        </div>

                        <!-- Permissions -->
                        <div>
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Direct Permissions</h4>
                            @if($user->permissions->count() > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($user->permissions as $permission)
                                        <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            <i class="fas fa-key mr-1"></i>
                                            {{ $permission->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">No direct permissions assigned</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- User Information -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">User Information</h3>
                    </div>
                    <div class="p-6">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Full Name</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email Address</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->email }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Phone Number</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->phone ?? 'Not provided' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Department</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $user->department ?? 'Not assigned' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    <span class="capitalize">{{ $user->status }}</span>
                                    @if($user->isLocked())
                                        <span class="text-red-600 dark:text-red-400 ml-2">(Locked)</span>
                                    @endif
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Activity</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->last_activity_at ? $user->last_activity_at->diffForHumans() : 'Never' }}
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->createdBy ? $user->createdBy->name : 'System' }}
                                </dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated By</dt>
                                <dd class="mt-1 text-sm text-gray-900 dark:text-white">
                                    {{ $user->updatedBy ? $user->updatedBy->name : 'System' }}
                                </dd>
                            </div>
                        </dl>

                        @if($user->notes)
                            <div class="mt-6">
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Notes</dt>
                                <dd class="text-sm text-gray-900 dark:text-white bg-gray-50 dark:bg-gray-700 p-3 rounded-lg">
                                    {{ $user->notes }}
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Activity Log -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Recent Activity</h3>
                    </div>
                    <div class="p-6">
                        @if(count($activities) > 0)
                            <div class="space-y-4">
                                @foreach($activities as $activity)
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                                                <i class="{{ $activity->icon }} {{ $activity->color }} text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm text-gray-900 dark:text-white">{{ $activity->description }}</p>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                                                @if($activity->performedBy)
                                                    <span class="text-xs text-gray-500 dark:text-gray-400">by {{ $activity->performedBy->name }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No recent activity found</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetPasswordModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white">Reset Password</h3>
            </div>
            <form id="resetPasswordForm" method="POST" class="p-6">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                    <input type="password" name="password" required 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required 
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeResetPasswordModal()" 
                            class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Reset Password Functions
function resetPassword(userId) {
    document.getElementById('resetPasswordForm').action = `/users/${userId}/reset-password`;
    document.getElementById('resetPasswordModal').classList.remove('hidden');
}

function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.add('hidden');
}

// Toggle Lock Function
function toggleLock(userId) {
    if (confirm('Are you sure you want to toggle the lock status of this user?')) {
        fetch(`/users/${userId}/toggle-lock`, {
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
            alert('An error occurred while toggling lock status.');
        });
    }
}

// Delete User Function
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch(`/users/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            },
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '{{ route("users.index") }}';
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the user.');
        });
    }
}
</script>
@endsection
