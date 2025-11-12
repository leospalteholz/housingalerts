<?php

namespace Tests\Feature;

use App\Models\Hearing;
use App\Models\Organization;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class RegionAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_region(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $payload = [
            'name' => 'Uptown',
            'comments_email' => 'uptown@example.com',
            'remote_instructions' => 'Zoom link will be provided.',
            'inperson_instructions' => 'City Hall Room 201',
        ];

        $response = $this->actingAs($admin)
            ->post(route('regions.store', ['organization' => $organization->slug]), $payload);

        $response->assertStatus(302)
            ->assertSessionHas('success', 'Region created successfully!');

        $this->assertDatabaseHas('regions', [
            'organization_id' => $organization->id,
            'name' => 'Uptown',
            'comments_email' => 'uptown@example.com',
        ]);
    }

    public function test_non_admin_is_redirected_when_accessing_region_create(): void
    {
        $organization = Organization::factory()->create();
        $user = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => false,
            'is_superuser' => false,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $response = $this->actingAs($user)
            ->get(route('regions.create', ['organization' => $organization->slug]));

        $response->assertStatus(302)
            ->assertSessionHas('error', 'You do not have permission to access this area.');

        $this->assertStringContainsString('/' . $organization->slug . '/dashboard', $response->headers->get('Location'));
    }

    public function test_admin_cannot_access_region_from_other_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();
        $region = Region::factory()->for($otherOrganization, 'organization')->create();

        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $response = $this->actingAs($admin)
            ->get(route('regions.edit', ['organization' => $organization->slug, 'region' => $region->slug]));

        $response->assertNotFound();
    }

    public function test_region_with_hearings_cannot_be_deleted(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();
        Hearing::factory()->for($organization, 'organization')->for($region, 'region')->create();

        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $response = $this->actingAs($admin)
            ->delete(route('regions.destroy', ['organization' => $organization->slug, 'region' => $region->slug]));

        $response->assertStatus(302)
            ->assertSessionHasErrors('error');

        $this->assertDatabaseHas('regions', ['id' => $region->id]);
    }

    public function test_admin_can_delete_empty_region(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $response = $this->actingAs($admin)
            ->delete(route('regions.destroy', ['organization' => $organization->slug, 'region' => $region->slug]));

        $response->assertStatus(302)
            ->assertSessionHas('success', 'Region deleted successfully!');

        $this->assertDatabaseMissing('regions', ['id' => $region->id]);
    }
}
