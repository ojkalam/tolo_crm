<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacPermissionPolicyTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $orgA;

    protected Organization $orgB;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->orgA = Organization::factory()->create();
        $this->orgB = Organization::factory()->create();
    }

    public function test_sales_representative_cannot_delete_deals(): void
    {
        $salesRep = User::factory()->create(['organization_id' => $this->orgA->id]);
        $salesRep->assignRole('SalesRepresentative');

        $pipeline = Pipeline::factory()->create(['organization_id' => $this->orgA->id]);
        $stage = PipelineStage::factory()->create(['pipeline_id' => $pipeline->id]);
        $deal = Deal::factory()->create([
            'organization_id' => $this->orgA->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
        ]);

        $response = $this->actingAs($salesRep, 'sanctum')
            ->deleteJson("/api/v1/deals/{$deal->id}");

        $response->assertForbidden();
        $this->assertDatabaseHas('deals', ['id' => $deal->id, 'deleted_at' => null]);
    }

    public function test_auditor_has_read_only_access(): void
    {
        $auditor = User::factory()->create(['organization_id' => $this->orgA->id]);
        $auditor->assignRole('Auditor');

        $company = Company::factory()->create(['organization_id' => $this->orgA->id, 'name' => 'Wayne Enterprises']);

        // Auditor can read
        $readResponse = $this->actingAs($auditor, 'sanctum')
            ->getJson("/api/v1/companies/{$company->id}");
        $readResponse->assertOk()
            ->assertJsonPath('data.name', 'Wayne Enterprises');

        // Auditor cannot create
        $createResponse = $this->actingAs($auditor, 'sanctum')
            ->postJson('/api/v1/companies', [
                'name' => 'Queen Industries',
            ]);
        $createResponse->assertForbidden();
    }

    public function test_user_cannot_access_other_organization_records(): void
    {
        $userA = User::factory()->create(['organization_id' => $this->orgA->id]);
        $userA->assignRole('OrgAdmin');

        $contactInOrgB = Contact::factory()->create(['organization_id' => $this->orgB->id]);

        $response = $this->actingAs($userA, 'sanctum')
            ->getJson("/api/v1/contacts/{$contactInOrgB->id}");

        $response->assertNotFound();
    }
}
