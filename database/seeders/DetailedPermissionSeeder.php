<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class DetailedPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Creates comprehensive roles and permissions for the pharmacy management system.
     */
    public function run(): void
    {
        // Clear existing permissions and roles (handle foreign key constraints)
        \DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        \DB::table('model_has_permissions')->truncate();
        \DB::table('model_has_roles')->truncate();
        \DB::table('role_has_permissions')->truncate();
        Permission::truncate();
        Role::truncate();
        \DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Create comprehensive permissions
        $permissions = [
            // Dashboard & Analytics
            'view-dashboard',
            'view-analytics',
            'view-statistics',
            
            // Sales Management
            'view-sales',
            'create-sales',
            'edit-sales',
            'delete-sales',
            'view-sales-reports',
            'export-sales-data',
            'view-sales-history',
            'process-refunds',
            'view-sales-analytics',
            
            // Inventory Management
            'view-inventory',
            'manage-inventory',
            'add-inventory',
            'edit-inventory',
            'delete-inventory',
            'adjust-inventory',
            'view-inventory-reports',
            'manage-stock-levels',
            'view-low-stock-alerts',
            'manage-expiry-dates',
            'inventory-import-export',
            
            // Dispensary Operations
            'view-dispensary',
            'process-prescriptions',
            'dispense-medications',
            'view-prescription-history',
            'manage-prescription-queue',
            'verify-prescriptions',
            'counsel-patients',
            
            // Cashier Operations
            'view-cashier',
            'process-payments',
            'handle-cash-transactions',
            'process-card-payments',
            'issue-receipts',
            'view-cashier-reports',
            'manage-cash-drawer',
            'process-refunds-cashier',
            'view-transaction-history',
            
            // Purchase Management
            'view-purchases',
            'create-purchases',
            'edit-purchases',
            'delete-purchases',
            'approve-purchases',
            'view-purchase-reports',
            'manage-purchase-orders',
            'receive-goods',
            'process-invoices',
            
            // Supplier Management
            'view-suppliers',
            'create-suppliers',
            'edit-suppliers',
            'delete-suppliers',
            'view-supplier-reports',
            'manage-supplier-contracts',
            'view-supplier-performance',
            
            // Customer Management
            'view-customers',
            'create-customers',
            'edit-customers',
            'delete-customers',
            'view-customer-history',
            'manage-customer-accounts',
            'view-customer-reports',
            'process-customer-refunds',
            
            // Reports & Analytics
            'view-reports',
            'view-financial-reports',
            'view-inventory-reports',
            'view-sales-reports',
            'view-customer-reports',
            'view-supplier-reports',
            'export-reports',
            'schedule-reports',
            'view-custom-reports',
            
            // Alerts & Notifications
            'view-alerts',
            'manage-alerts',
            'create-alerts',
            'configure-alert-settings',
            'view-notifications',
            'send-notifications',
            
            // System Settings
            'view-settings',
            'manage-settings',
            'configure-system',
            'manage-backups',
            'view-system-logs',
            'manage-integrations',
            'configure-email-settings',
            'manage-tax-settings',
            'configure-payment-methods',
            
            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'manage-user-roles',
            'view-user-activity',
            'manage-user-permissions',
            'reset-user-passwords',
            'manage-user-sessions',
            
            // Role Management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',
            'assign-permissions',
            'view-role-permissions',
            
            // Security & Audit
            'view-audit-logs',
            'manage-security-settings',
            'view-login-history',
            'manage-session-timeouts',
            'view-security-reports',
            
            // Advanced Features
            'manage-bulk-operations',
            'access-advanced-features',
            'manage-data-import-export',
            'configure-automation',
            'manage-api-access',
            'view-system-performance',
            
            // Pharmacy Specific
            'manage-prescription-templates',
            'view-drug-interactions',
            'manage-medication-alerts',
            'process-insurance-claims',
            'manage-patient-profiles',
            'view-medication-history',
            'manage-pharmacy-licenses',
            'process-controlled-substances',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create detailed roles
        $this->createRoles();
        
        // Assign roles to existing users
        $this->assignRolesToUsers();
    }

    private function createRoles()
    {
        // Super Admin - All permissions
        $superAdmin = Role::create(['name' => 'super-admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Pharmacy Manager - Most permissions except super admin functions
        $pharmacyManager = Role::create(['name' => 'pharmacy-manager']);
        $pharmacyManager->givePermissionTo([
            'view-dashboard', 'view-analytics', 'view-statistics',
            'view-sales', 'create-sales', 'edit-sales', 'view-sales-reports', 'export-sales-data', 'view-sales-history', 'view-sales-analytics',
            'view-inventory', 'manage-inventory', 'add-inventory', 'edit-inventory', 'adjust-inventory', 'view-inventory-reports', 'manage-stock-levels', 'view-low-stock-alerts', 'manage-expiry-dates', 'inventory-import-export',
            'view-dispensary', 'process-prescriptions', 'dispense-medications', 'view-prescription-history', 'manage-prescription-queue', 'verify-prescriptions', 'counsel-patients',
            'view-cashier', 'process-payments', 'handle-cash-transactions', 'process-card-payments', 'issue-receipts', 'view-cashier-reports', 'manage-cash-drawer', 'view-transaction-history',
            'view-purchases', 'create-purchases', 'edit-purchases', 'approve-purchases', 'view-purchase-reports', 'manage-purchase-orders', 'receive-goods', 'process-invoices',
            'view-suppliers', 'create-suppliers', 'edit-suppliers', 'view-supplier-reports', 'manage-supplier-contracts', 'view-supplier-performance',
            'view-customers', 'create-customers', 'edit-customers', 'view-customer-history', 'manage-customer-accounts', 'view-customer-reports',
            'view-reports', 'view-financial-reports', 'view-inventory-reports', 'view-sales-reports', 'view-customer-reports', 'view-supplier-reports', 'export-reports', 'view-custom-reports',
            'view-alerts', 'manage-alerts', 'create-alerts', 'configure-alert-settings', 'view-notifications', 'send-notifications',
            'view-settings', 'manage-settings', 'configure-system', 'view-system-logs', 'configure-email-settings', 'manage-tax-settings', 'configure-payment-methods',
            'view-users', 'create-users', 'edit-users', 'manage-user-roles', 'view-user-activity', 'manage-user-permissions',
            'view-roles', 'create-roles', 'edit-roles', 'assign-permissions', 'view-role-permissions',
            'view-audit-logs', 'manage-security-settings', 'view-login-history', 'view-security-reports',
            'manage-bulk-operations', 'access-advanced-features', 'manage-data-import-export', 'configure-automation', 'view-system-performance',
            'manage-prescription-templates', 'view-drug-interactions', 'manage-medication-alerts', 'process-insurance-claims', 'manage-patient-profiles', 'view-medication-history', 'manage-pharmacy-licenses', 'process-controlled-substances',
        ]);

        // Senior Pharmacist - Clinical and dispensing focused
        $seniorPharmacist = Role::create(['name' => 'senior-pharmacist']);
        $seniorPharmacist->givePermissionTo([
            'view-dashboard', 'view-analytics',
            'view-sales', 'view-sales-reports', 'view-sales-history',
            'view-inventory', 'manage-inventory', 'add-inventory', 'edit-inventory', 'adjust-inventory', 'view-inventory-reports', 'manage-stock-levels', 'view-low-stock-alerts', 'manage-expiry-dates',
            'view-dispensary', 'process-prescriptions', 'dispense-medications', 'view-prescription-history', 'manage-prescription-queue', 'verify-prescriptions', 'counsel-patients',
            'view-customers', 'create-customers', 'edit-customers', 'view-customer-history', 'view-customer-reports',
            'view-reports', 'view-inventory-reports', 'view-sales-reports', 'view-customer-reports', 'export-reports',
            'view-alerts', 'view-notifications',
            'manage-prescription-templates', 'view-drug-interactions', 'manage-medication-alerts', 'manage-patient-profiles', 'view-medication-history', 'process-controlled-substances',
        ]);

        // Pharmacist - Basic dispensing and inventory
        $pharmacist = Role::create(['name' => 'pharmacist']);
        $pharmacist->givePermissionTo([
            'view-dashboard',
            'view-sales', 'view-sales-reports',
            'view-inventory', 'manage-inventory', 'adjust-inventory', 'view-inventory-reports', 'view-low-stock-alerts', 'manage-expiry-dates',
            'view-dispensary', 'process-prescriptions', 'dispense-medications', 'view-prescription-history', 'verify-prescriptions', 'counsel-patients',
            'view-customers', 'create-customers', 'edit-customers', 'view-customer-history',
            'view-reports', 'view-inventory-reports', 'view-sales-reports',
            'view-alerts',
            'view-drug-interactions', 'manage-medication-alerts', 'view-medication-history',
        ]);

        // Senior Cashier - Advanced cashier operations
        $seniorCashier = Role::create(['name' => 'senior-cashier']);
        $seniorCashier->givePermissionTo([
            'view-dashboard', 'view-analytics',
            'view-sales', 'create-sales', 'edit-sales', 'view-sales-reports', 'export-sales-data', 'view-sales-history', 'process-refunds', 'view-sales-analytics',
            'view-cashier', 'process-payments', 'handle-cash-transactions', 'process-card-payments', 'issue-receipts', 'view-cashier-reports', 'manage-cash-drawer', 'process-refunds-cashier', 'view-transaction-history',
            'view-customers', 'create-customers', 'edit-customers', 'view-customer-history', 'view-customer-reports', 'process-customer-refunds',
            'view-reports', 'view-financial-reports', 'view-sales-reports', 'view-customer-reports', 'export-reports',
            'view-alerts', 'view-notifications',
        ]);

        // Cashier - Basic cashier operations
        $cashier = Role::create(['name' => 'cashier']);
        $cashier->givePermissionTo([
            'view-dashboard',
            'view-sales', 'create-sales', 'view-sales-reports', 'view-sales-history',
            'view-cashier', 'process-payments', 'handle-cash-transactions', 'process-card-payments', 'issue-receipts', 'view-cashier-reports', 'view-transaction-history',
            'view-customers', 'create-customers', 'edit-customers', 'view-customer-history',
            'view-reports', 'view-sales-reports', 'view-customer-reports',
            'view-alerts',
        ]);

        // Inventory Manager - Focused on stock management
        $inventoryManager = Role::create(['name' => 'inventory-manager']);
        $inventoryManager->givePermissionTo([
            'view-dashboard', 'view-analytics',
            'view-inventory', 'manage-inventory', 'add-inventory', 'edit-inventory', 'delete-inventory', 'adjust-inventory', 'view-inventory-reports', 'manage-stock-levels', 'view-low-stock-alerts', 'manage-expiry-dates', 'inventory-import-export',
            'view-purchases', 'create-purchases', 'edit-purchases', 'view-purchase-reports', 'manage-purchase-orders', 'receive-goods', 'process-invoices',
            'view-suppliers', 'create-suppliers', 'edit-suppliers', 'view-supplier-reports', 'manage-supplier-contracts', 'view-supplier-performance',
            'view-reports', 'view-inventory-reports', 'view-purchase-reports', 'view-supplier-reports', 'export-reports',
            'view-alerts', 'manage-alerts', 'create-alerts', 'view-notifications',
            'manage-bulk-operations', 'manage-data-import-export',
        ]);

        // Sales Representative - Customer and sales focused
        $salesRep = Role::create(['name' => 'sales-representative']);
        $salesRep->givePermissionTo([
            'view-dashboard', 'view-analytics',
            'view-sales', 'create-sales', 'edit-sales', 'view-sales-reports', 'export-sales-data', 'view-sales-history', 'view-sales-analytics',
            'view-customers', 'create-customers', 'edit-customers', 'delete-customers', 'view-customer-history', 'manage-customer-accounts', 'view-customer-reports',
            'view-reports', 'view-sales-reports', 'view-customer-reports', 'export-reports',
            'view-alerts', 'view-notifications',
        ]);

        // Accountant - Financial and reporting focused
        $accountant = Role::create(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'view-dashboard', 'view-analytics', 'view-statistics',
            'view-sales', 'view-sales-reports', 'export-sales-data', 'view-sales-history', 'view-sales-analytics',
            'view-purchases', 'view-purchase-reports', 'process-invoices',
            'view-suppliers', 'view-supplier-reports', 'view-supplier-performance',
            'view-customers', 'view-customer-reports',
            'view-reports', 'view-financial-reports', 'view-sales-reports', 'view-customer-reports', 'view-supplier-reports', 'export-reports', 'schedule-reports', 'view-custom-reports',
            'view-alerts', 'view-notifications',
            'view-settings', 'configure-email-settings', 'manage-tax-settings', 'configure-payment-methods',
        ]);

        // IT Administrator - System and technical focused
        $itAdmin = Role::create(['name' => 'it-administrator']);
        $itAdmin->givePermissionTo([
            'view-dashboard', 'view-analytics', 'view-statistics',
            'view-reports', 'export-reports', 'schedule-reports', 'view-custom-reports',
            'view-alerts', 'manage-alerts', 'create-alerts', 'configure-alert-settings', 'view-notifications', 'send-notifications',
            'view-settings', 'manage-settings', 'configure-system', 'manage-backups', 'view-system-logs', 'manage-integrations', 'configure-email-settings', 'configure-payment-methods',
            'view-users', 'create-users', 'edit-users', 'delete-users', 'manage-user-roles', 'view-user-activity', 'manage-user-permissions', 'reset-user-passwords', 'manage-user-sessions',
            'view-roles', 'create-roles', 'edit-roles', 'delete-roles', 'assign-permissions', 'view-role-permissions',
            'view-audit-logs', 'manage-security-settings', 'view-login-history', 'manage-session-timeouts', 'view-security-reports',
            'access-advanced-features', 'manage-data-import-export', 'configure-automation', 'manage-api-access', 'view-system-performance',
        ]);

        // Read-Only User - View only permissions
        $readOnly = Role::create(['name' => 'read-only']);
        $readOnly->givePermissionTo([
            'view-dashboard', 'view-analytics',
            'view-sales', 'view-sales-reports', 'view-sales-history',
            'view-inventory', 'view-inventory-reports',
            'view-dispensary', 'view-prescription-history',
            'view-cashier', 'view-cashier-reports',
            'view-purchases', 'view-purchase-reports',
            'view-suppliers', 'view-supplier-reports',
            'view-customers', 'view-customer-history', 'view-customer-reports',
            'view-reports', 'view-financial-reports', 'view-inventory-reports', 'view-sales-reports', 'view-customer-reports', 'view-supplier-reports',
            'view-alerts',
        ]);
    }

    private function assignRolesToUsers()
    {
        // Assign super-admin role to existing admin user
        $adminUser = User::where('email', 'admin@example.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('super-admin');
        }

        // Assign pharmacy-manager to existing admin user (abst738@gmail.com)
        $pharmacyManagerUser = User::where('email', 'abst738@gmail.com')->first();
        if ($pharmacyManagerUser) {
            $pharmacyManagerUser->assignRole('pharmacy-manager');
        }

        // Assign cashier role to existing cashier user
        $cashierUser = User::where('email', 'aster@example.com')->first();
        if ($cashierUser) {
            $cashierUser->assignRole('cashier');
        }
    }
}
