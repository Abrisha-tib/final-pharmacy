<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates roles and permissions for the pharmacy management system.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'view-sales',
            'manage-inventory',
            'manage-cashier',
            'view-reports',
            'manage-suppliers',
            'manage-customers',
            'manage-purchases',
            'view-alerts',
            'manage-settings',
            'manage-users'
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $cashierRole = Role::firstOrCreate(['name' => 'cashier']);

        // Assign all permissions to admin
        $adminRole->givePermissionTo(Permission::all());

        // Assign limited permissions to cashier
        $cashierRole->givePermissionTo([
            'view-sales',
            'manage-cashier',
            'view-reports'
        ]);

        // Assign admin role to the admin user
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
    }
}

