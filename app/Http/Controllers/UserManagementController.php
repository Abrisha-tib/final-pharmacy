<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\UsersExport;
use App\Imports\UsersImport;
use App\Models\UserActivity;
use Carbon\Carbon;

class UserManagementController extends Controller
{
    /**
     * Display a listing of users with advanced filtering and search.
     */
    public function index(Request $request)
    {
        $this->authorize('view-users');

        $query = User::with(['roles', 'permissions'])
            ->withCount(['roles', 'permissions']);

        // Advanced search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        // Filter by department
        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        // Date range filtering
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // Real-time data without caching (like sales/inventory pages)
        $users = $query->paginate(15);

        // Get filter options for dropdowns
        $roles = Role::all();
        $departments = User::distinct()->pluck('department')->filter();
        $statuses = ['active', 'inactive', 'suspended'];

        return view('user-management.index', compact('users', 'roles', 'departments', 'statuses'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $this->authorize('create-users');
        
        $roles = Role::all();
        $permissions = Permission::all();
        $departments = User::distinct()->pluck('department')->filter();
        
        return view('user-management.create', compact('roles', 'permissions', 'departments'));
    }

    /**
     * Store a newly created user.
     */
    public function store(Request $request)
    {
        $this->authorize('create-users');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'new_department' => 'required_if:department,new|nullable|string|max:100|sometimes',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,suspended',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->phone = $request->phone;
            
            // Handle department assignment
            if ($request->department === 'new' && $request->filled('new_department')) {
                $user->department = $request->new_department;
            } else {
                $user->department = $request->department;
            }
            
            $user->notes = $request->notes;
            $user->status = $request->status;
            $user->created_by = auth()->id();
            $user->updated_by = auth()->id();

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                $avatar = $request->file('avatar');
                $filename = Str::uuid() . '.' . $avatar->getClientOriginalExtension();
                $path = $avatar->storeAs('avatars', $filename, 'public');
                $user->avatar = $path;
            }

            $user->save();

            // Assign roles
            if ($request->filled('roles')) {
                $roleIds = $request->roles;
                $roles = Role::whereIn('id', $roleIds)->get();
                $user->assignRole($roles);
            }

            // Assign permissions
            if ($request->filled('permissions')) {
                $permissionIds = $request->permissions;
                $permissions = Permission::whereIn('id', $permissionIds)->get();
                $user->givePermissionTo($permissions);
            }

            // Log user creation activity
            $this->logUserActivity($user->id, 'user_created', 'User created successfully');

            DB::commit();

            // Clear cache
            Cache::forget('users_*');

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User created successfully.',
                    'user' => $user
                ]);
            }

            return redirect()->route('users.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create user: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $this->authorize('view-users');
        
        $user->load(['roles', 'permissions', 'createdBy', 'updatedBy']);
        
        // Get user activity log
        $activities = $this->getUserActivities($user->id);
        
        return view('user-management.show', compact('user', 'activities'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $this->authorize('edit-users');
        
        $roles = Role::all();
        $permissions = Permission::all();
        $departments = User::distinct()->pluck('department')->filter();
        
        $user->load(['roles', 'permissions']);
        
        return view('user-management.edit', compact('user', 'roles', 'permissions', 'departments'));
    }

    /**
     * Update the specified user.
     */
    public function update(Request $request, User $user)
    {
        $this->authorize('edit-users');

        // Debug logging
        \Log::info('User update request received', [
            'user_id' => $user->id,
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('avatar'),
            'file_size' => $request->hasFile('avatar') ? $request->file('avatar')->getSize() : null
        ]);

        // Simplified validation for debugging
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'department' => 'nullable|string|max:100',
            'new_department' => 'required_if:department,new|nullable|string|max:100|sometimes',
            'notes' => 'nullable|string|max:1000',
            'status' => 'required|in:active,inactive,suspended',
            'roles' => 'array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,id',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        if ($validator->fails()) {
            \Log::error('User update validation failed', [
                'errors' => $validator->errors()->toArray(),
                'user_id' => $user->id
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            // Simple update without transaction for debugging
            $user->name = $request->name;
            $user->email = $request->email;
            
            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }
            
            $user->phone = $request->phone;
            
            // Handle department assignment
            if ($request->department === 'new' && $request->filled('new_department')) {
                $user->department = $request->new_department;
            } else {
                $user->department = $request->department;
            }
            
            $user->notes = $request->notes;
            $user->status = $request->status;
            $user->updated_by = auth()->id();

            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                $avatar = $request->file('avatar');
                $filename = Str::uuid() . '.' . $avatar->getClientOriginalExtension();
                $path = $avatar->storeAs('avatars', $filename, 'public');
                $user->avatar = $path;
            }

            $saveResult = $user->save();
            
            \Log::info('User save result', [
                'user_id' => $user->id,
                'save_result' => $saveResult,
                'was_changed' => $user->wasChanged(),
                'changes' => $user->getChanges()
            ]);

            // Update roles
            if ($request->has('roles')) {
                // Convert role IDs to role names
                $roleIds = $request->roles ?? [];
                $roleNames = Role::whereIn('id', $roleIds)->pluck('name')->toArray();
                $user->syncRoles($roleNames);
            }

            // Update permissions
            if ($request->has('permissions')) {
                // Convert permission IDs to permission names
                $permissionIds = $request->permissions ?? [];
                $permissionNames = Permission::whereIn('id', $permissionIds)->pluck('name')->toArray();
                $user->syncPermissions($permissionNames);
            }

            // Log user update activity
            try {
                $this->logUserActivity($user->id, 'user_updated', 'User updated successfully');
            } catch (\Exception $e) {
                \Log::error('Failed to log user activity', [
                    'error' => $e->getMessage()
                ]);
            }

            // Clear cache
            Cache::forget('users_*');

            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'User updated successfully.',
                    'user' => $user
                ]);
            }

            return redirect()->route('users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            \Log::error('User update failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update user: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        $this->authorize('delete-users');

        // Prevent deletion of the current user
        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot delete your own account.');
        }

        DB::beginTransaction();
        try {
            // Delete avatar file
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Log deletion activity
            $this->logUserActivity($user->id, 'user_deleted', 'User deleted successfully');

            $user->delete();

            DB::commit();

            // Clear cache
            Cache::forget('users_*');

            return redirect()->route('users.index')
                ->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to delete user: ' . $e->getMessage());
        }
    }

    /**
     * Bulk operations on users.
     */
    public function bulkAction(Request $request)
    {
        $this->authorize('view-users');

        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,suspend,delete,assign_role',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'role_id' => 'required_if:action,assign_role|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Invalid bulk action parameters.');
        }

        $userIds = $request->user_ids;
        $action = $request->action;

        DB::beginTransaction();
        try {
            switch ($action) {
                case 'activate':
                    User::whereIn('id', $userIds)->update(['status' => 'active', 'updated_by' => auth()->id()]);
                    $message = 'Users activated successfully.';
                    break;
                
                case 'deactivate':
                    User::whereIn('id', $userIds)->update(['status' => 'inactive', 'updated_by' => auth()->id()]);
                    $message = 'Users deactivated successfully.';
                    break;
                
                case 'suspend':
                    User::whereIn('id', $userIds)->update(['status' => 'suspended', 'updated_by' => auth()->id()]);
                    $message = 'Users suspended successfully.';
                    break;
                
                case 'delete':
                    // Prevent deletion of current user
                    $userIds = array_filter($userIds, function($id) {
                        return $id != auth()->id();
                    });
                    
                    if (empty($userIds)) {
                        return redirect()->back()
                            ->with('error', 'Cannot delete your own account.');
                    }
                    
                    User::whereIn('id', $userIds)->delete();
                    $message = 'Users deleted successfully.';
                    break;
                
                case 'assign_role':
                    $role = Role::findOrFail($request->role_id);
                    $users = User::whereIn('id', $userIds)->get();
                    foreach ($users as $user) {
                        $user->assignRole($role);
                    }
                    $message = 'Role assigned to users successfully.';
                    break;
            }

            // Log bulk action
            $this->logUserActivity(null, 'bulk_action', "Bulk action '{$action}' performed on " . count($userIds) . " users");

            DB::commit();

            // Clear cache
            Cache::forget('users_*');

            return redirect()->route('users.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to perform bulk action: ' . $e->getMessage());
        }
    }

    /**
     * Export users to Excel.
     */
    public function export(Request $request)
    {
        $this->authorize('view-users');

        $filters = $request->only(['search', 'status', 'role', 'department', 'date_from', 'date_to']);
        
        return Excel::download(new UsersExport($filters), 'users_' . date('Y-m-d_H-i-s') . '.xlsx');
    }

    /**
     * Import users from Excel.
     */
    public function import(Request $request)
    {
        $this->authorize('view-users');

        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimes:xlsx,xls,csv|max:10240'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Invalid file format. Please upload Excel or CSV file.');
        }

        try {
            Excel::import(new UsersImport, $request->file('file'));
            
            // Clear cache
            Cache::forget('users_*');
            
            return redirect()->route('users.index')
                ->with('success', 'Users imported successfully.');
                
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to import users: ' . $e->getMessage());
        }
    }

    /**
     * Reset user password.
     */
    public function resetPassword(Request $request, User $user)
    {
        $this->authorize('view-users');

        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', 'Password must be at least 8 characters and confirmed.');
        }

        $user->password = Hash::make($request->password);
        $user->updated_by = auth()->id();
        $user->save();

        // Log password reset
        $this->logUserActivity($user->id, 'password_reset', 'Password reset by administrator');

        return redirect()->back()
            ->with('success', 'Password reset successfully.');
    }

    /**
     * Lock/unlock user account.
     */
    public function toggleLock(User $user)
    {
        $this->authorize('view-users');

        if ($user->id === auth()->id()) {
            return redirect()->back()
                ->with('error', 'You cannot lock your own account.');
        }

        $user->locked_until = $user->locked_until ? null : now()->addDays(30);
        $user->updated_by = auth()->id();
        $user->save();

        $action = $user->locked_until ? 'locked' : 'unlocked';
        
        // Log lock action
        $this->logUserActivity($user->id, 'account_' . $action, 'Account ' . $action . ' by administrator');

        return redirect()->back()
            ->with('success', "Account {$action} successfully.");
    }

    /**
     * Get user statistics for dashboard.
     */
    public function getStats()
    {
        $this->authorize('view-users');

        $stats = Cache::remember('user_stats', 300, function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('status', 'active')->count(),
                'inactive_users' => User::where('status', 'inactive')->count(),
                'suspended_users' => User::where('status', 'suspended')->count(),
                'locked_users' => User::whereNotNull('locked_until')->count(),
                'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
                'last_login_today' => User::whereDate('last_login_at', today())->count(),
            ];
        });

        return response()->json($stats);
    }

    /**
     * Log user activity.
     */
    private function logUserActivity($userId, $action, $description, $metadata = null)
    {
        UserActivity::create([
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'metadata' => $metadata,
            'performed_by' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Get user activities.
     */
    private function getUserActivities($userId)
    {
        return UserActivity::where('user_id', $userId)
            ->with('performedBy')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();
    }


    /**
     * API endpoint for real-time user data (like sales/inventory pages)
     */
    public function api(Request $request)
    {
        $this->authorize('view-users');

        try {
            $query = User::with(['roles', 'permissions'])
                ->withCount(['roles', 'permissions']);

            // Advanced search functionality
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhere('department', 'like', "%{$search}%");
                });
            }

            // Filter by status
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            // Filter by role
            if ($request->filled('role')) {
                $query->whereHas('roles', function ($q) use ($request) {
                    $q->where('name', $request->role);
                });
            }

            // Filter by department
            if ($request->filled('department')) {
                $query->where('department', $request->department);
            }

            // Date range filtering
            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            // Sorting
            $sortBy = $request->get('sort_by', 'created_at');
            $sortOrder = $request->get('sort_order', 'desc');
            $query->orderBy($sortBy, $sortOrder);

            // Get current page
            $page = $request->get('page', 1);
            $users = $query->paginate(15, ['*'], 'page', $page);

            // Get filter options for dropdowns
            $roles = Role::all();
            $departments = User::distinct()->pluck('department')->filter();
            $statuses = ['active', 'inactive', 'suspended'];

            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total(),
                    'from' => $users->firstItem(),
                    'to' => $users->lastItem(),
                ],
                'filters' => [
                    'roles' => $roles,
                    'departments' => $departments,
                    'statuses' => $statuses,
                ]
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch users: ' . $e->getMessage()
            ], 500);
        }
    }
}
