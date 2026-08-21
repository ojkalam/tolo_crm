<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Deal;
use App\Models\Organization;
use App\Models\Pipeline;
use App\Models\PipelineStage;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->org = Organization::factory()->create();
        $this->admin = User::factory()->create([
            'organization_id' => $this->org->id,
            'status' => 'active',
        ]);
        $this->admin->assignRole('OrgAdmin');
    }

    public function test_can_import_contacts_csv_and_auto_associate_companies(): void
    {
        $csvHeader = "first_name,last_name,email,phone,job_title,department,company_name\n";
        $row1 = "Barry,Allen,barry@starlabs.com,+15554443333,Forensic Scientist,R&D,S.T.A.R. Labs\n";
        $row2 = "Cisco,Ramon,cisco@starlabs.com,+15554443334,Lead Mechanical Engineer,Engineering,S.T.A.R. Labs\n";

        $csvContent = $csvHeader.$row1.$row2;
        $file = UploadedFile::fake()->createWithContent('contacts_import.csv', $csvContent);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/contacts/import', [
                'file' => $file,
            ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Contacts imported successfully');

        $this->assertDatabaseHas('companies', [
            'organization_id' => $this->org->id,
            'name' => 'S.T.A.R. Labs',
        ]);

        $this->assertDatabaseHas('contacts', [
            'organization_id' => $this->org->id,
            'email' => 'barry@starlabs.com',
            'first_name' => 'Barry',
            'last_name' => 'Allen',
        ]);

        $this->assertDatabaseHas('contacts', [
            'organization_id' => $this->org->id,
            'email' => 'cisco@starlabs.com',
            'first_name' => 'Cisco',
        ]);
    }

    public function test_can_export_deals_to_csv(): void
    {
        $pipeline = Pipeline::factory()->create(['organization_id' => $this->org->id]);
        $stage = PipelineStage::factory()->create(['pipeline_id' => $pipeline->id]);

        Deal::factory()->count(3)->create([
            'organization_id' => $this->org->id,
            'pipeline_id' => $pipeline->id,
            'stage_id' => $stage->id,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->get('/api/v1/deals/export');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', (string) $response->headers->get('content-type'));
    }
}
