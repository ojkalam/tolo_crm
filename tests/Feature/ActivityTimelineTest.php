<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ActivityType;
use App\Models\Activity;
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

class ActivityTimelineTest extends TestCase
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

    public function test_can_log_polymorphic_activity_on_contact(): void
    {
        $contact = Contact::factory()->create(['organization_id' => $this->org->id]);

        $response = $this->actingAs($this->user, 'sanctum')->postJson('/api/v1/activities', [
            'subjectable_type' => 'contact',
            'subjectable_id' => $contact->id,
            'type' => 'call',
            'title' => 'Discovery Call with VP of Sales',
            'description' => 'Discussed enterprise pricing and roadmap.',
            'due_date' => now()->addDays(2)->toIso8601String(),
            'metadata' => [
                'call_duration_minutes' => 35,
                'recording_url' => 'https://zoom.us/rec/12345',
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.title', 'Discovery Call with VP of Sales')
            ->assertJsonPath('data.subjectable_type', 'Contact')
            ->assertJsonPath('data.subjectable_id', $contact->id)
            ->assertJsonPath('data.metadata.call_duration_minutes', 35);

        $this->assertDatabaseHas('activities', [
            'organization_id' => $this->org->id,
            'subjectable_type' => Contact::class,
            'subjectable_id' => $contact->id,
            'title' => 'Discovery Call with VP of Sales',
        ]);
    }

    public function test_can_mark_activity_completed(): void
    {
        $contact = Contact::factory()->create(['organization_id' => $this->org->id]);
        $activity = Activity::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'subjectable_type' => Contact::class,
            'subjectable_id' => $contact->id,
            'completed_at' => null,
        ]);

        $response = $this->actingAs($this->user, 'sanctum')->postJson("/api/v1/activities/{$activity->id}/complete", [
            'outcome' => 'Connected - Demo Booked',
            'notes' => 'Client agreed to standard contract terms.',
        ]);

        $response->assertOk()
            ->assertJsonPath('data.is_completed', true)
            ->assertJsonPath('data.metadata.outcome', 'Connected - Demo Booked')
            ->assertJsonPath('data.metadata.completion_notes', 'Client agreed to standard contract terms.');

        $this->assertNotNull($activity->fresh()->completed_at);
    }

    public function test_unified_timeline_aggregates_activities_and_audit_logs(): void
    {
        $company = Company::factory()->create([
            'organization_id' => $this->org->id,
            'name' => 'Cyberdyne Systems',
            'annual_revenue' => 1000000.00,
        ]);

        // 1. Create an Activity
        Activity::create([
            'organization_id' => $this->org->id,
            'user_id' => $this->user->id,
            'subjectable_type' => Company::class,
            'subjectable_id' => $company->id,
            'type' => ActivityType::NOTE,
            'title' => 'Quarterly Business Review Notes',
            'description' => 'Cyberdyne planning 200 seat expansion in Q4.',
            'created_at' => now()->subMinutes(10),
        ]);

        // 2. Perform a model update to trigger Spatie ActivityLog
        activity()->performedOn($company)
            ->causedBy($this->user)
            ->withProperties(['attributes' => ['annual_revenue' => 2000000.00], 'old' => ['annual_revenue' => 1000000.00]])
            ->log('updated');

        // 3. Query Timeline API
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/v1/timeline?subject_type=company&subject_id={$company->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'category',
                        'type',
                        'title',
                        'timestamp',
                    ],
                ],
            ]);

        $items = $response->json('data');
        $this->assertGreaterThanOrEqual(2, count($items));

        $categories = collect($items)->pluck('category')->toArray();
        $this->assertContains('activity', $categories);
        $this->assertContains('audit', $categories);
    }
}
