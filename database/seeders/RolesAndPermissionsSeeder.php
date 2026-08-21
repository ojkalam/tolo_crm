<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Define all permissions
        $permissions = [
            // Leads
            'leads.view',
            'leads.create',
            'leads.update',
            'leads.delete',
            'leads.convert',
            'leads.assign',

            // Deals & Pipelines
            'deals.view',
            'deals.create',
            'deals.update',
            'deals.delete',
            'deals.move_stage',
            'deals.assign',
            'pipelines.view',
            'pipelines.create',
            'pipelines.update',
            'pipelines.delete',

            // Contacts
            'contacts.view',
            'contacts.create',
            'contacts.update',
            'contacts.delete',

            // Companies (Accounts)
            'companies.view',
            'companies.create',
            'companies.update',
            'companies.delete',

            // Activities & Tasks
            'activities.view',
            'activities.create',
            'activities.update',
            'activities.delete',
            'activities.complete',

            // Analytics & Reports
            'reports.view',
            'reports.export',

            // System & User Settings
            'settings.view',
            'settings.update',
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.assign_role',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'web']);
            Permission::firstOrCreate(['name' => $permissionName, 'guard_name' => 'sanctum']);
        }

        // 2. Define Roles and assign permissions
        $guards = ['web', 'sanctum'];

        foreach ($guards as $guard) {
            // SuperAdmin
            $superAdmin = Role::firstOrCreate(['name' => 'SuperAdmin', 'guard_name' => $guard]);
            $superAdmin->syncPermissions(Permission::where('guard_name', $guard)->get());

            // OrgAdmin
            $orgAdmin = Role::firstOrCreate(['name' => 'OrgAdmin', 'guard_name' => $guard]);
            $orgAdmin->syncPermissions(Permission::where('guard_name', $guard)->get());

            // SalesManager
            $salesManager = Role::firstOrCreate(['name' => 'SalesManager', 'guard_name' => $guard]);
            $salesManager->syncPermissions([
                'leads.view', 'leads.create', 'leads.update', 'leads.delete', 'leads.convert', 'leads.assign',
                'deals.view', 'deals.create', 'deals.update', 'deals.delete', 'deals.move_stage', 'deals.assign',
                'pipelines.view', 'pipelines.create', 'pipelines.update',
                'contacts.view', 'contacts.create', 'contacts.update', 'contacts.delete',
                'companies.view', 'companies.create', 'companies.update', 'companies.delete',
                'activities.view', 'activities.create', 'activities.update', 'activities.delete', 'activities.complete',
                'reports.view', 'reports.export',
                'users.view',
            ]);

            // SalesRepresentative
            $salesRep = Role::firstOrCreate(['name' => 'SalesRepresentative', 'guard_name' => $guard]);
            $salesRep->syncPermissions([
                'leads.view', 'leads.create', 'leads.update', 'leads.convert',
                'deals.view', 'deals.create', 'deals.update', 'deals.move_stage',
                'pipelines.view',
                'contacts.view', 'contacts.create', 'contacts.update',
                'companies.view', 'companies.create', 'companies.update',
                'activities.view', 'activities.create', 'activities.update', 'activities.complete',
                'reports.view',
            ]);

            // SupportAgent
            $supportAgent = Role::firstOrCreate(['name' => 'SupportAgent', 'guard_name' => $guard]);
            $supportAgent->syncPermissions([
                'leads.view',
                'deals.view',
                'contacts.view', 'contacts.update',
                'companies.view',
                'activities.view', 'activities.create', 'activities.update', 'activities.complete',
            ]);

            // Auditor
            $auditor = Role::firstOrCreate(['name' => 'Auditor', 'guard_name' => $guard]);
            $auditor->syncPermissions([
                'leads.view',
                'deals.view',
                'pipelines.view',
                'contacts.view',
                'companies.view',
                'activities.view',
                'reports.view',
                'reports.export',
            ]);
        }
    }
}
