<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_organization_admin_dashboard(): void
    {
        $organization = Organization::factory()->create();

        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $response = $this->actingAs($admin)
            ->get(route('admin.dashboard', ['organization' => $organization]));

        $response->assertOk();
        $response->assertViewHas('organization', function ($boundOrganization) use ($organization) {
            return $boundOrganization->is($organization);
        });
        $response->assertViewHas('stats', function (array $stats) {
            return array_key_exists('totalUsers', $stats)
                && array_key_exists('totalRegions', $stats)
                && array_key_exists('totalHearings', $stats)
                && array_key_exists('totalVotes', $stats)
                && array_key_exists('totalCouncillors', $stats)
                && array_key_exists('totalSubscribers', $stats);
        });
    }

    public function test_non_admin_is_redirected_when_trying_to_access_admin_dashboard(): void
    {
        $organization = Organization::factory()->create();

        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => false,
            'is_superuser' => false,
        ]);

        $response = $this->actingAs($user)
            ->get(route('admin.dashboard', ['organization' => $organization]));

        $response->assertRedirect(route('dashboard', ['organization' => $organization]));
        $response->assertSessionHas('error', 'You do not have permission to access this area.');
    }

    public function test_superuser_on_root_sees_cross_organization_stats(): void
    {
        $rootOrganization = Organization::factory()->create([
            'name' => 'Root Org',
            'slug' => 'root',
        ]);

        Organization::factory()->count(2)->create();

        $superuser = User::factory()->create([
            'organization_id' => $rootOrganization->id,
            'is_admin' => true,
            'is_superuser' => true,
        ]);

        $response = $this->actingAs($superuser)
            ->get(route('admin.dashboard', ['organization' => $rootOrganization]));

        $response->assertOk();
        $response->assertViewHas('organizations');
        $response->assertViewHas('stats', function (array $stats) {
            return array_key_exists('organizations', $stats)
                && array_key_exists('totalUsers', $stats)
                && array_key_exists('totalRegions', $stats)
                && array_key_exists('totalHearings', $stats)
                && array_key_exists('totalVotes', $stats)
                && array_key_exists('totalCouncillors', $stats)
                && array_key_exists('totalSubscribers', $stats);
        });
    }
}
