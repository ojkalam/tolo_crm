<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\LeadStatus;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LeadConversionTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;

    protected User $salesManager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->org = Organization::factory()->create();
        $this->salesManager = User::factory()->create([
            'organization_id' => $this->org->id,
            'status' => 'active',
        ]);
        $this->salesManager->assignRole('SalesManager');
    }

    public function test_lead_creation_automatically_calculates_lead_score(): void
    {
        $response = $this->actingAs($this->salesManager, 'sanctum')->postJson('/api/v1/leads', [
            'first_name' => 'Diana',
            'last_name' => 'Prince',
            'company_name' => 'Themyscira Global',
            'title' => 'Chief Executive Officer',
            'email' => 'diana@themysciraglobal.com',
            'phone' => '+15559876543',
            'estimated_value' => 50000.00,
            'status' => 'new',
            'source' => 'website',
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.first_name', 'Diana')
            ->assertJsonPath('data.company_name', 'Themyscira Global');

        $lead = Lead::where('email', 'diana@themysciraglobal.com')->first();
        $this->assertNotNull($lead);
        // Score should be high (25 corp email + 15 phone + 15 company + 25 CEO + 20 deal value = 100)
        $this->assertGreaterThanOrEqual(80, $lead->score);
    }

    public function test_atomic_lead_conversion_creates_company_contact_and_deal(): void
    {
        $lead = Lead::factory()->create([
            'organization_id' => $this->org->id,
            'assigned_user_id' => $this->salesManager->id,
            'first_name' => 'Clark',
            'last_name' => 'Kent',
            'company_name' => 'Daily Planet Inc',
            'email' => 'clark@dailyplanet.com',
            'phone' => '+15551112233',
            'title' => 'Senior Investigative Journalist',
            'estimated_value' => 25000.00,
            'status' => LeadStatus::QUALIFIED,
            'custom_attributes' => ['press_pass' => 'A-100'],
        ]);

        $response = $this->actingAs($this->salesManager, 'sanctum')->postJson("/api/v1/leads/{$lead->id}/convert", [
            'company' => [
                'name' => 'Daily Planet Media Corp',
                'industry' => 'Publishing & Media',
            ],
            'contact' => [
                'job_title' => 'Head of Special Investigations',
            ],
            'create_deal' => true,
            'deal' => [
                'name' => 'Daily Planet - Annual Enterprise Subscription',
                'amount' => 30000.00,
            ],
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Lead converted successfully')
            ->assertJsonPath('data.company.name', 'Daily Planet Media Corp')
            ->assertJsonPath('data.contact.first_name', 'Clark')
            ->assertJsonPath('data.contact.last_name', 'Kent')
            ->assertJsonPath('data.deal.amount', 30000)
            ->assertJsonPath('data.lead.status', 'converted');

        // Verify Database Integrity
        $this->assertDatabaseHas('companies', [
            'organization_id' => $this->org->id,
            'name' => 'Daily Planet Media Corp',
        ]);

        $company = Company::where('name', 'Daily Planet Media Corp')->first();

        $this->assertDatabaseHas('contacts', [
            'organization_id' => $this->org->id,
            'company_id' => $company->id,
            'email' => 'clark@dailyplanet.com',
        ]);

        $contact = Contact::where('email', 'clark@dailyplanet.com')->first();

        $this->assertDatabaseHas('deals', [
            'organization_id' => $this->org->id,
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'amount' => 30000.00,
        ]);

        $this->assertDatabaseHas('leads', [
            'id' => $lead->id,
            'status' => 'converted',
        ]);
    }
}
