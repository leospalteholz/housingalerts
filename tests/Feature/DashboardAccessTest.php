<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_route_redirects_admin_to_admin_dashboard(): void
    {
        $organization = Organization::factory()->create();

        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard', ['organization' => $organization]));

        $response->assertRedirect(route('admin.dashboard', ['organization' => $organization]));
    }

    public function test_regular_user_cannot_access_other_organization_dashboard(): void
    {
        $organizationA = Organization::factory()->create();
        $organizationB = Organization::factory()->create();

        $user = User::factory()->create([
            'organization_id' => $organizationA->id,
            'is_admin' => false,
            'is_superuser' => false,
        ]);

        $response = $this->actingAs($user)->get(route('dashboard', ['organization' => $organizationB]));

        $response->assertForbidden();
    }

    public function test_superuser_can_access_dashboard_for_another_organization(): void
    {
        $organizationA = Organization::factory()->create();
        $organizationB = Organization::factory()->create();

        $superuser = User::factory()->create([
            'organization_id' => $organizationA->id,
            'is_admin' => true,
            'is_superuser' => true,
        ]);

        $response = $this->actingAs($superuser)->get(route('dashboard', ['organization' => $organizationB]));

        $response->assertRedirect(route('admin.dashboard', ['organization' => $organizationB]));
    }

    public function test_guest_is_redirected_to_login_for_organization_dashboard(): void
    {
        $organization = Organization::factory()->create();

        $response = $this->get(route('dashboard', ['organization' => $organization]));

        $response->assertRedirect(route('login'));
    }
}
