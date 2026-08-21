<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthSanctumTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    public function test_user_can_register_organization_and_admin_account(): void
    {
        $response = $this->postJson('/api/v1/auth/register', [
            'organization_name' => 'Stark Industries',
            'domain' => 'stark.local',
            'first_name' => 'Tony',
            'last_name' => 'Stark',
            'email' => 'tony@stark.local',
            'password' => 'JarvisPassword123!',
            'phone' => '+15551234567',
        ]);

        $response->assertCreated()
            ->assertJsonStructure([
                'message',
                'token',
                'user' => [
                    'id',
                    'first_name',
                    'last_name',
                    'email',
                    'roles',
                    'organization' => ['id', 'name', 'domain'],
                ],
            ]);

        $this->assertDatabaseHas('organizations', ['name' => 'Stark Industries']);
        $this->assertDatabaseHas('users', ['email' => 'tony@stark.local']);

        $user = User::where('email', 'tony@stark.local')->first();
        $this->assertTrue($user->hasRole('OrgAdmin'));
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $org->id,
            'email' => 'pepper@stark.local',
            'password' => Hash::make('SecretPass123!'),
            'status' => 'active',
        ]);
        $user->assignRole('SalesManager');

        $response = $this->postJson('/api/v1/auth/login', [
            'email' => 'pepper@stark.local',
            'password' => 'SecretPass123!',
        ]);

        $response->assertOk()
            ->assertJsonStructure([
                'message',
                'token',
                'user' => ['id', 'email', 'roles', 'permissions'],
            ]);
    }

    public function test_authenticated_user_can_view_profile(): void
    {
        $org = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $org->id,
            'status' => 'active',
        ]);
        $user->assignRole('SalesRepresentative');

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/v1/auth/me');

        $response->assertOk()
            ->assertJsonPath('user.email', $user->email)
            ->assertJsonPath('user.roles.0', 'SalesRepresentative');
    }
}
