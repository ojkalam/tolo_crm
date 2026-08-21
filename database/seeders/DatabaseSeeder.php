<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
        ]);

        // Default Tenant Organization
        $org = Organization::firstOrCreate(
            ['domain' => 'acmecorp.com'],
            [
                'name' => 'Acme Corporation',
                'settings' => [
                    'currency' => 'USD',
                    'timezone' => 'America/New_York',
                    'fiscal_year_start' => 'January',
                ],
                'is_active' => true,
            ]
        );

        // 1. Super Admin (Cross-tenant capable)
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@crm-enterprise.local'],
            [
                'organization_id' => $org->id,
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'password' => Hash::make('Password123!'),
                'phone' => '+15550000001',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('SuperAdmin');

        // 2. Org Admin
        $orgAdmin = User::firstOrCreate(
            ['email' => 'admin@acmecorp.com'],
            [
                'organization_id' => $org->id,
                'first_name' => 'John',
                'last_name' => 'Manager',
                'password' => Hash::make('Password123!'),
                'phone' => '+15550000002',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $orgAdmin->assignRole('OrgAdmin');

        // 3. Sales Manager
        $salesManager = User::firstOrCreate(
            ['email' => 'salesmanager@acmecorp.com'],
            [
                'organization_id' => $org->id,
                'first_name' => 'Sarah',
                'last_name' => 'Connor',
                'password' => Hash::make('Password123!'),
                'phone' => '+15550000003',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $salesManager->assignRole('SalesManager');

        // 4. Sales Representative
        $salesRep = User::firstOrCreate(
            ['email' => 'salesrep@acmecorp.com'],
            [
                'organization_id' => $org->id,
                'first_name' => 'Alex',
                'last_name' => 'Rivers',
                'password' => Hash::make('Password123!'),
                'phone' => '+15550000004',
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );
        $salesRep->assignRole('SalesRepresentative');
    }
}
