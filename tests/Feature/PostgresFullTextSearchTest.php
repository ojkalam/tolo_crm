<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Contact;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostgresFullTextSearchTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->org = Organization::factory()->create();
        $this->user = User::factory()->create([
            'organization_id' => $this->org->id,
            'status' => 'active',
        ]);
        $this->user->assignRole('SalesManager');
    }

    public function test_postgres_search_finds_contacts_and_companies(): void
    {
        $company = Company::factory()->create([
            'organization_id' => $this->org->id,
            'name' => 'Aperture Science Innovations',
            'industry' => 'Quantum Physics',
        ]);

        $contact = Contact::factory()->create([
            'organization_id' => $this->org->id,
            'company_id' => $company->id,
            'first_name' => 'Cave',
            'last_name' => 'Johnson',
            'job_title' => 'Chief Executive Officer',
            'email' => 'cave@aperturescience.com',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/search?q=Aperture');

        $response->assertOk()
            ->assertJsonPath('total_count', 2);

        $titles = collect($response->json('unified'))->pluck('title')->toArray();
        $this->assertContains('Aperture Science Innovations', $titles);
        $this->assertContains('Cave Johnson', $titles);
    }

    public function test_postgres_search_type_filtering_restricts_results(): void
    {
        Company::factory()->create([
            'organization_id' => $this->org->id,
            'name' => 'Black Mesa Research Facility',
        ]);

        Lead::factory()->create([
            'organization_id' => $this->org->id,
            'first_name' => 'Gordon',
            'last_name' => 'Freeman',
            'company_name' => 'Black Mesa',
            'email' => 'gordon@blackmesa.gov',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/search?q=Mesa&type=lead');

        $response->assertOk();
        $results = $response->json('unified');
        $this->assertCount(1, $results);
        $this->assertEquals('lead', $results[0]['entity_type']);
        $this->assertEquals('Gordon Freeman', $results[0]['title']);
    }

    public function test_search_respects_multi_tenant_isolation(): void
    {
        $otherOrg = Organization::factory()->create();
        Company::factory()->create([
            'organization_id' => $otherOrg->id,
            'name' => 'Secret Vault Corp',
        ]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/v1/search?q=Vault');

        $response->assertOk()
            ->assertJsonPath('total_count', 0);
    }
}
