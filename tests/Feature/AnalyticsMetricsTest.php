<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Enums\ActivityType;
use App\Enums\LeadStatus;
use App\Models\Activity;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalyticsMetricsTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected User $manager;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->org = Organization::factory()->create();
        $this->manager = User::factory()->create([
            'organization_id' => $this->org->id,
            'status' => 'active',
        ]);
        $this->manager->assignRole('SalesManager');
    }

    public function test_executive_analytics_dashboard_computes_accurate_kpis(): void
    {
        $pipeline = Pipeline::factory()->create([
            'organization_id' => $this->org->id,
            'is_default' => true,
        ]);

        $stage1 = PipelineStage::factory()->create([
            'pipeline_id' => $pipeline->id,
            'win_probability' => 20,
            'order_column' => 1,
        ]);

        $stage2 = PipelineStage::factory()->create([
            'pipeline_id' => $pipeline->id,
            'win_probability' => 80,
            'order_column' => 2,
        ]);

        // Deal 1: $10,000 in 20% stage -> weighted $2,000
        Deal::factory()->create([
            'organization_id' => $this->org->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage1->id,
            'assigned_to' => $this->manager->id,
            'amount' => 10000.00,
            'status' => 'open',
        ]);

        // Deal 2: $50,000 in 80% stage -> weighted $40,000
        Deal::factory()->create([
            'organization_id' => $this->org->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage2->id,
            'assigned_to' => $this->manager->id,
            'amount' => 50000.00,
            'status' => 'open',
        ]);

        // Deal 3: Won Deal $20,000
        Deal::factory()->create([
            'organization_id' => $this->org->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage2->id,
            'assigned_to' => $this->manager->id,
            'amount' => 20000.00,
            'status' => 'won',
        ]);

        // Completed Activity
        Activity::factory()->create([
            'organization_id' => $this->org->id,
            'user_id' => $this->manager->id,
            'completed_at' => now(),
        ]);

        // Converted Lead
        Lead::factory()->create([
            'organization_id' => $this->org->id,
            'status' => LeadStatus::CONVERTED,
            'created_at' => now()->subDays(4),
            'converted_at' => now(),
        ]);

        $response = $this->actingAs($this->manager, 'sanctum')
            ->getJson('/api/v1/analytics/dashboard');

        $response->assertOk()
            ->assertJsonPath('data.kpis.total_pipeline_value', 60000)
            ->assertJsonPath('data.kpis.weighted_forecast_value', 42000)
            ->assertJsonPath('data.kpis.open_deals_count', 2)
            ->assertJsonPath('data.kpis.won_deals_count', 1)
            ->assertJsonPath('data.lead_velocity.converted_leads', 1)
            ->assertJsonPath('data.rep_performance.0.user_id', $this->manager->id)
            ->assertJsonPath('data.rep_performance.0.won_revenue', 20000);
    }
}
