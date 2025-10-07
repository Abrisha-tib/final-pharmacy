<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\UserActivity;

class RoleManagementController extends Controller
{
    /**
     * Display a listing of roles.
     */
    public function index(Request $request)
    {
        $this->authorize('view-roles');

        $query = Role::withCount(['users', 'permissions']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Get roles without caching to ensure fresh data
        $roles = $query->paginate(15);

        return view('role-management.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     */
    public function create()
    {
        $this->authorize('create-roles');
        
        $permissions = Permission::all();
        
        return view('role-management.create', compact('permissions'));
    }

    /**
     * Store a newly created role.
     */
    public function store(Request $request)
    {
        $this->authorize('create-roles');

        // Debug logging
        \Log::info('Role creation request received', [
            'request_data' => $request->all(),
            'user_id' => auth()->id()
        ]);

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            \Log::error('Role creation validation failed', [
                'errors' => $validator->errors()->toArray(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Create role without transaction for debugging
            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web'
            ]);

            \Log::info('Role created successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name
            ]);

            // Assign permissions
            if ($request->filled('permissions')) {
                $permissionIds = $request->permissions;
                $permissions = Permission::whereIn('id', $permissionIds)->get();
                $role->givePermissionTo($permissions);
                
                \Log::info('Permissions assigned to role', [
                    'role_id' => $role->id,
                    'permission_count' => $permissions->count(),
                    'permissions' => $permissions->pluck('name')->toArray()
                ]);
            }

            // Log role creation activity
            try {
                $this->logRoleActivity($role->id, 'role_created', "Role '{$role->name}' created successfully");
            } catch (\Exception $e) {
                \Log::error('Failed to log role activity', [
                    'error' => $e->getMessage()
                ]);
            }

            // Clear role-related cache and user management cache
            $this->clearRoleCache();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role created successfully.',
                    'role' => $role
                ]);
            }

            return redirect()->route('roles.index')
                ->with('success', 'Role created successfully.');

        } catch (\Exception $e) {
            \Log::error('Role creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create role: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to create role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified role.
     */
    public function show(Role $role)
    {
        $this->authorize('view-roles');
        
        $role->load(['permissions', 'users']);
        
        return view('role-management.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role.
     */
    public function edit(Role $role)
    {
        $this->authorize('edit-roles');
        
        $permissions = Permission::all();
        $role->load('permissions');
        
        return view('role-management.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role.
     */
    public function update(Request $request, Role $role)
    {
        $this->authorize('edit-roles');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $oldName = $role->name;
            $role->name = $request->name;
            $role->save();

            // Update permissions
            $role->syncPermissions($request->permissions ?? []);

            // Log role update activity
            $this->logRoleActivity($role->id, 'role_updated', "Role '{$oldName}' updated to '{$role->name}'");

            DB::commit();

            // Clear role-related cache and user management cache
            $this->clearRoleCache();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role updated successfully.',
                    'role' => $role
                ]);
            }

            return redirect()->route('roles.index')
                ->with('success', 'Role updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update role: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to update role: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified role.
     */
    public function destroy(Role $role)
    {
        $this->authorize('delete-roles');

        // Check if role exists
        if (!$role) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role not found.'
                ], 404);
            }
            return redirect()->back()
                ->with('error', 'Role not found.');
        }

        // Check if role has users assigned
        if ($role->users()->count() > 0) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete role. It has users assigned to it.'
                ], 400);
            }
            return redirect()->back()
                ->with('error', 'Cannot delete role. It has users assigned to it.');
        }

        try {
            $roleName = $role->name;
            $roleId = $role->id;
            
            // Log role deletion activity
            try {
                $this->logRoleActivity($roleId, 'role_deleted', "Role '{$roleName}' deleted successfully");
            } catch (\Exception $e) {
                \Log::error('Failed to log role deletion activity', [
                    'error' => $e->getMessage()
                ]);
            }

            // Force delete the role (hard delete)
            $role->forceDelete();
            
            // Verify the role is actually deleted
            $deletedRole = Role::find($roleId);
            if ($deletedRole) {
                \Log::error('Role deletion verification failed - role still exists', [
                    'role_id' => $roleId,
                    'role_name' => $roleName
                ]);
                throw new \Exception('Role deletion failed - role still exists in database');
            }
            
            \Log::info('Role hard deleted successfully and verified', [
                'role_id' => $roleId,
                'role_name' => $roleName
            ]);

            // Clear role-related cache and user management cache
            $this->clearRoleCache();

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Role deleted successfully.'
                ]);
            }

            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Role deletion failed', [
                'role_id' => $role->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete role: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Get role statistics.
     */
    public function getStats()
    {
        $this->authorize('view-roles');

        $stats = Cache::remember('role_stats', 300, function () {
            return [
                'total_roles' => Role::count(),
                'total_permissions' => Permission::count(),
                'roles_with_users' => Role::has('users')->count(),
                'most_used_role' => Role::withCount('users')->orderBy('users_count', 'desc')->first(),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Log role activity.
     */
    private function logRoleActivity($roleId, $action, $description, $metadata = null)
    {
        UserActivity::create([
            'user_id' => $roleId, // For role activities, we'll use the role ID
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'performed_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Clear role-related cache
     */
    private function clearRoleCache()
    {
        try {
            // Clear all cache to ensure fresh data
            Cache::flush();
            
            // Also clear Spatie Permission cache
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
            \Log::info('Role cache cleared successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to clear role cache', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
