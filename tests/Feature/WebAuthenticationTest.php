<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WebAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected Organization $org;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);

        $this->org = Organization::create([
            'name' => 'Acme Corporation',
            'domain' => 'acmecorp.com',
            'settings' => ['currency' => 'USD', 'timezone' => 'UTC'],
            'is_active' => true,
        ]);

        $this->adminUser = User::create([
            'organization_id' => $this->org->id,
            'first_name' => 'John',
            'last_name' => 'Manager',
            'email' => 'admin@acmecorp.com',
            'password' => Hash::make('Password123!'),
            'status' => 'active',
            'email_verified_at' => now(),
        ]);
        $this->adminUser->assignRole('OrgAdmin');
    }

    public function test_unauthenticated_guests_visiting_app_are_redirected_to_login(): void
    {
        $response = $this->get('/app');
        $response->assertRedirect('/login');
    }

    public function test_users_can_log_in_via_web_portal_and_access_app(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@acmecorp.com',
            'password' => 'Password123!',
        ]);

        $response->assertRedirect(route('app.dashboard'));
        $this->assertAuthenticatedAs($this->adminUser);

        $appResponse = $this->get('/app');
        $appResponse->assertOk();
        $appResponse->assertSee('Acme Corporation');
        $appResponse->assertSee('John Manager');
    }

    public function test_invalid_login_credentials_are_rejected(): void
    {
        $response = $this->post('/login', [
            'email' => 'admin@acmecorp.com',
            'password' => 'WrongPassword!',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_authenticated_users_can_switch_roles_instantly(): void
    {
        $salesRep = User::create([
            'organization_id' => $this->org->id,
            'first_name' => 'Alex',
            'last_name' => 'Rivers',
            'email' => 'salesrep@acmecorp.com',
            'password' => Hash::make('Password123!'),
            'status' => 'active',
        ]);
        $salesRep->assignRole('SalesRepresentative');

        $this->actingAs($this->adminUser);

        $response = $this->post('/switch-role', ['role' => 'SalesRepresentative']);
        $response->assertRedirect(route('app.dashboard'));
        $this->assertAuthenticatedAs($salesRep);
    }

    public function test_authenticated_users_can_log_out(): void
    {
        $this->actingAs($this->adminUser);
        $this->assertAuthenticated();

        $response = $this->post('/logout');
        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }
}
