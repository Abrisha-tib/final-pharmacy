@extends('layouts.app')

@section('title', 'Edit Role - Analog Pharmacy')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Role</h1>
                    <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Update role information and permissions</p>
                </div>
                <div class="flex space-x-3">
                    <a href="{{ route('roles.show', $role) }}" 
                       class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-eye mr-2"></i>
                        View Role
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

    <!-- Form Section -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <form id="editRoleForm" class="p-6">
                @csrf
                @method('PUT')
                
                <!-- Role Information -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Role Information</h3>
                    <div class="grid grid-cols-1 gap-6">
                        <!-- Role Name -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Role Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name" id="name" value="{{ old('name', $role->name) }}" required
                                   placeholder="e.g., Manager, Supervisor, Staff"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent dark:bg-gray-700 dark:text-white @error('name') border-red-500 @enderror">
                            @error('name')
                                <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Enter a descriptive name for this role</p>
                        </div>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="mb-8">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Assign Permissions</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">Select the permissions that users with this role will have</p>
                    
                    <!-- Permission Categories -->
                    <div class="space-y-6">
                        <!-- Sales & Inventory -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <i class="fas fa-shopping-cart mr-2 text-green-600"></i>
                                Sales & Inventory Management
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($permissions->whereIn('name', ['view-sales', 'manage-inventory', 'manage-cashier']) as $permission)
                                    <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="ml-3 text-sm text-gray-900 dark:text-white">{{ ucwords(str_replace('-', ' ', $permission->name)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Reports & Analytics -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <i class="fas fa-chart-bar mr-2 text-blue-600"></i>
                                Reports & Analytics
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($permissions->whereIn('name', ['view-reports', 'view-alerts']) as $permission)
                                    <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="ml-3 text-sm text-gray-900 dark:text-white">{{ ucwords(str_replace('-', ' ', $permission->name)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Supplier & Customer Management -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <i class="fas fa-users mr-2 text-purple-600"></i>
                                Supplier & Customer Management
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($permissions->whereIn('name', ['manage-suppliers', 'manage-customers', 'manage-purchases']) as $permission)
                                    <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="ml-3 text-sm text-gray-900 dark:text-white">{{ ucwords(str_replace('-', ' ', $permission->name)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- System Administration -->
                        <div>
                            <h4 class="text-md font-medium text-gray-800 dark:text-gray-200 mb-3 flex items-center">
                                <i class="fas fa-cog mr-2 text-red-600"></i>
                                System Administration
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @foreach($permissions->whereIn('name', ['manage-settings', 'manage-users']) as $permission)
                                    <label class="flex items-center p-3 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                               {{ in_array($permission->id, old('permissions', $role->permissions->pluck('id')->toArray())) ? 'checked' : '' }}
                                               class="rounded border-gray-300 text-green-600 focus:ring-green-500">
                                        <span class="ml-3 text-sm text-gray-900 dark:text-white">{{ ucwords(str_replace('-', ' ', $permission->name)) }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @error('permissions')
                        <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <a href="{{ route('roles.index') }}" 
                       class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200">
                        <i class="fas fa-save mr-2"></i>
                        Update Role
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Select All Permissions in Category
function selectAllInCategory(category) {
    const checkboxes = document.querySelectorAll(`[data-category="${category}"] input[type="checkbox"]`);
    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = !allChecked;
    });
}

// Quick Role Templates
function applyTemplate(template) {
    // Clear all selections first
    document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
    
    const templates = {
        'admin': ['view-sales', 'manage-inventory', 'manage-cashier', 'view-reports', 'manage-suppliers', 'manage-customers', 'manage-purchases', 'view-alerts', 'manage-settings', 'manage-users'],
        'manager': ['view-sales', 'manage-inventory', 'view-reports', 'manage-suppliers', 'manage-customers', 'manage-purchases', 'view-alerts'],
        'cashier': ['view-sales', 'manage-cashier', 'view-reports'],
        'staff': ['view-sales', 'view-reports']
    };
    
    if (templates[template]) {
        templates[template].forEach(permissionName => {
            const permission = Array.from(document.querySelectorAll('input[name="permissions[]"]')).find(cb => 
                cb.closest('label').textContent.toLowerCase().includes(permissionName.replace('-', ' '))
            );
            if (permission) permission.checked = true;
        });
    }
}

// AJAX form submission with immediate refresh (like sales page)
document.getElementById('editRoleForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = document.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    try {
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Updating Role...';
        submitBtn.disabled = true;
        
        const response = await fetch('{{ route("roles.update", $role) }}', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            // Show success notification
            if (window.NotificationService) {
                window.NotificationService.success('Role updated successfully!');
            } else {
                alert('Role updated successfully!');
            }
            
            // Redirect to roles index with refresh
            window.location.href = '/roles?refresh=true';
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
                alert('Failed to update role: ' + (data.message || 'Unknown error'));
            }
        }
        
    } catch (error) {
        console.error('Error updating role:', error);
        alert('Failed to update role. Please try again.');
    } finally {
        // Reset button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});
</script>
@endsection