<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanyContactCrudTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;

    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->org = Organization::factory()->create();
        $this->adminUser = User::factory()->create([
            'organization_id' => $this->org->id,
            'status' => 'active',
        ]);
        $this->adminUser->assignRole('OrgAdmin');
    }

    public function test_can_create_company_with_jsonb_custom_attributes(): void
    {
        $response = $this->actingAs($this->adminUser, 'sanctum')->postJson('/api/v1/companies', [
            'name' => 'Wayne Enterprises',
            'domain' => 'wayne.com',
            'industry' => 'Defense & Technology',
            'annual_revenue' => 15000000.50,
            'employees_count' => 12000,
            'custom_attributes' => [
                'tier' => 'Enterprise Plus',
                'account_health' => 'Green',
                'security_clearance' => 'Level 5',
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Wayne Enterprises')
            ->assertJsonPath('data.custom_attributes.tier', 'Enterprise Plus')
            ->assertJsonPath('data.custom_attributes.security_clearance', 'Level 5');

        $this->assertDatabaseHas('companies', [
            'name' => 'Wayne Enterprises',
            'organization_id' => $this->org->id,
        ]);
    }

    public function test_can_update_company_custom_attributes_seamlessly(): void
    {
        $company = Company::factory()->create([
            'organization_id' => $this->org->id,
            'custom_attributes' => ['tier' => 'Silver', 'region' => 'North America'],
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')->putJson("/api/v1/companies/{$company->id}", [
            'annual_revenue' => 2500000,
            'custom_attributes' => ['tier' => 'Gold', 'contract_signed' => true],
        ]);

        $response->assertOk()
            ->assertJsonPath('data.custom_attributes.tier', 'Gold')
            ->assertJsonPath('data.custom_attributes.region', 'North America')
            ->assertJsonPath('data.custom_attributes.contract_signed', true);
    }

    public function test_can_create_contact_associated_with_company(): void
    {
        $company = Company::factory()->create(['organization_id' => $this->org->id]);

        $response = $this->actingAs($this->adminUser, 'sanctum')->postJson('/api/v1/contacts', [
            'company_id' => $company->id,
            'first_name' => 'Bruce',
            'last_name' => 'Wayne',
            'email' => 'bruce@wayne.com',
            'job_title' => 'CEO',
            'lifecycle_stage' => 'customer',
            'custom_attributes' => [
                'vip' => true,
                'preferred_meeting_time' => 'Evening',
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Bruce Wayne')
            ->assertJsonPath('data.company.id', $company->id)
            ->assertJsonPath('data.custom_attributes.vip', true);

        $this->assertDatabaseHas('contacts', [
            'email' => 'bruce@wayne.com',
            'company_id' => $company->id,
        ]);
    }

    public function test_can_filter_contacts_by_company_and_lifecycle(): void
    {
        $company1 = Company::factory()->create(['organization_id' => $this->org->id]);
        $company2 = Company::factory()->create(['organization_id' => $this->org->id]);

        Contact::factory()->create([
            'organization_id' => $this->org->id,
            'company_id' => $company1->id,
            'lifecycle_stage' => 'customer',
        ]);
        Contact::factory()->create([
            'organization_id' => $this->org->id,
            'company_id' => $company2->id,
            'lifecycle_stage' => 'lead',
        ]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/v1/contacts?filter[company_id]={$company1->id}&filter[lifecycle_stage]=customer");

        $response->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_tenant_isolation_prevents_viewing_other_organization_company(): void
    {
        $otherOrg = Organization::factory()->create();
        $otherCompany = Company::factory()->create(['organization_id' => $otherOrg->id]);

        $response = $this->actingAs($this->adminUser, 'sanctum')
            ->getJson("/api/v1/companies/{$otherCompany->id}");

        $response->assertNotFound();
    }
}
