@extends('layouts.app')

@section('title', 'Role Details - Analog Pharmacy')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Role Details</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">View role information and permissions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('roles.edit', $role) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-edit mr-2"></i>
                        Edit Role
                    </a>
                    <a href="{{ route('roles.index') }}" 
                       class="inline-flex items-center px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back to Roles
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Role Information -->
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="p-6">
                        <!-- Role Icon and Name -->
                        <div class="text-center">
                            <div class="w-20 h-20 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-user-tag text-green-600 dark:text-green-400 text-2xl"></i>
                            </div>
                            <h2 class="text-2xl font-bold text-gray-900 dark:text-white">{{ ucfirst($role->name) }}</h2>
                            <p class="text-gray-600 dark:text-gray-400 mt-2">Role</p>
                        </div>

                        <!-- Role Stats -->
                        <div class="mt-6 space-y-4">
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Permissions</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $role->permissions->count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Users</span>
                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $role->users->count() }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center py-2 border-b border-gray-200 dark:border-gray-700">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</span>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $role->created_at->format('M d, Y') }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center py-2">
                                <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</span>
                                <span class="text-sm text-gray-900 dark:text-white">{{ $role->updated_at->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions and Users -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Permissions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Assigned Permissions</h3>
                    </div>
                    <div class="p-6">
                        @if($role->permissions->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($role->permissions as $permission)
                                    <div class="flex items-center p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex-shrink-0">
                                            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                                                <i class="fas fa-key text-blue-600 dark:text-blue-400 text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucwords(str_replace('-', ' ', $permission->name)) }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $permission->name }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No permissions assigned to this role</p>
                        @endif
                    </div>
                </div>

                <!-- Users with this Role -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white">Users with this Role</h3>
                    </div>
                    <div class="p-6">
                        @if($role->users->count() > 0)
                            <div class="space-y-3">
                                @foreach($role->users as $user)
                                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center">
                                            <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" 
                                                 class="w-10 h-10 rounded-full object-cover">
                                            <div class="ml-3">
                                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->name }}</p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                        <div class="flex items-center space-x-2">
                                            @if($user->status === 'active')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                    Active
                                                </span>
                                            @elseif($user->status === 'inactive')
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200">
                                                    Inactive
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                    Suspended
                                                </span>
                                            @endif
                                            <a href="{{ route('users.show', $user) }}" 
                                               class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500 dark:text-gray-400">No users assigned to this role</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
