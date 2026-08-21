<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\DealStageUpdated;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Deal;
use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class DealPipelineStageTransitionTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected User $salesRep;
    protected Pipeline $pipeline;
    protected PipelineStage $stageDiscovery;
    protected PipelineStage $stageNegotiation;
    protected PipelineStage $stageWon;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->org = Organization::factory()->create();
        $this->salesRep = User::factory()->create([
            'organization_id' => $this->org->id,
            'status' => 'active',
        ]);
        $this->salesRep->assignRole('SalesRepresentative');

        $this->pipeline = Pipeline::factory()->create([
            'organization_id' => $this->org->id,
            'name' => 'Enterprise Sales Pipeline',
            'is_default' => true,
        ]);

        $this->stageDiscovery = PipelineStage::factory()->create([
            'pipeline_id' => $this->pipeline->id,
            'name' => 'Discovery',
            'win_probability' => 20,
            'order_column' => 1,
            'color_code' => '#60A5FA',
        ]);

        $this->stageNegotiation = PipelineStage::factory()->create([
            'pipeline_id' => $this->pipeline->id,
            'name' => 'Negotiation',
            'win_probability' => 80,
            'order_column' => 2,
            'color_code' => '#F59E0B',
        ]);

        $this->stageWon = PipelineStage::factory()->create([
            'pipeline_id' => $this->pipeline->id,
            'name' => 'Closed Won',
            'win_probability' => 100,
            'order_column' => 3,
            'color_code' => '#10B981',
        ]);
    }

    public function test_can_fetch_kanban_board_with_stage_aggregates(): void
    {
        $company = Company::factory()->create(['organization_id' => $this->org->id]);
        $contact = Contact::factory()->create(['organization_id' => $this->org->id, 'company_id' => $company->id]);

        Deal::factory()->create([
            'organization_id' => $this->org->id,
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $this->stageDiscovery->id,
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'amount' => 50000.00,
            'status' => 'open',
        ]);

        Deal::factory()->create([
            'organization_id' => $this->org->id,
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $this->stageNegotiation->id,
            'company_id' => $company->id,
            'contact_id' => $contact->id,
            'amount' => 100000.00,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->salesRep, 'sanctum')
            ->getJson("/api/v1/pipelines/{$this->pipeline->id}/kanban");

        $response->assertOk()
            ->assertJsonPath('pipeline.name', 'Enterprise Sales Pipeline')
            ->assertJsonPath('pipeline.total_pipeline_value', 150000)
            ->assertJsonCount(3, 'columns');

        // Check Stage 1 aggregates (50,000 total, 10,000 weighted at 20%)
        $response->assertJsonPath('columns.0.name', 'Discovery')
            ->assertJsonPath('columns.0.total_value', 50000)
            ->assertJsonPath('columns.0.weighted_value', 10000);

        // Check Stage 2 aggregates (100,000 total, 80,000 weighted at 80%)
        $response->assertJsonPath('columns.1.name', 'Negotiation')
            ->assertJsonPath('columns.1.total_value', 100000)
            ->assertJsonPath('columns.1.weighted_value', 80000);
    }

    public function test_can_move_deal_stage_and_broadcast_event(): void
    {
        Event::fake([DealStageUpdated::class]);

        $deal = Deal::factory()->create([
            'organization_id' => $this->org->id,
            'pipeline_id' => $this->pipeline->id,
            'stage_id' => $this->stageDiscovery->id,
            'amount' => 75000.00,
            'status' => 'open',
        ]);

        $response = $this->actingAs($this->salesRep, 'sanctum')
            ->postJson("/api/v1/deals/{$deal->id}/move-stage", [
                'stage_id' => $this->stageWon->id,
            ]);

        $response->assertOk()
            ->assertJsonPath('data.id', $deal->id)
            ->assertJsonPath('data.stage.id', $this->stageWon->id)
            ->assertJsonPath('data.status', 'won');

        $this->assertDatabaseHas('deals', [
            'id' => $deal->id,
            'stage_id' => $this->stageWon->id,
            'status' => 'won',
        ]);

        Event::assertDispatched(DealStageUpdated::class, function ($event) use ($deal) {
            return $event->deal->id === $deal->id
                && $event->previousStageId === $this->stageDiscovery->id
                && $event->deal->stage_id === $this->stageWon->id;
        });
    }
}
