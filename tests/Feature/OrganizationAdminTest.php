<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class OrganizationAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_superuser_can_create_organization(): void
    {
        $superuser = User::factory()->create([
            'is_admin' => true,
            'is_superuser' => true,
        ]);

        $payload = [
            'name' => 'Test Housing Coalition',
            'slug' => 'test-housing-coalition',
            'contact_email' => 'contact@test.org',
            'website_url' => 'https://test.org',
            'about' => 'Advocacy focused group.',
        ];

        $response = $this->actingAs($superuser)
            ->post(route('organizations.store'), $payload);

        $response->assertRedirect(route('organizations.index'))
            ->assertSessionHas('success', 'Organization created successfully!');

        $this->assertDatabaseHas('organizations', [
            'name' => 'Test Housing Coalition',
            'contact_email' => 'contact@test.org',
        ]);
    }

    public function test_regular_admin_cannot_access_superuser_routes(): void
    {
        $organization = Organization::factory()->create();

        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $response = $this->actingAs($admin)
            ->get(route('organizations.create'));

        $response->assertStatus(302)
            ->assertSessionHas('error', 'You do not have permission to access this area.');

        $this->assertStringContainsString('/' . $organization->slug . '/dashboard', $response->headers->get('Location'));
    }

    public function test_admin_can_update_own_organization(): void
    {
        $organization = Organization::factory()->create([
            'name' => 'Legacy Org',
            'contact_email' => 'legacy@example.com',
            'website_url' => 'https://legacy.example.com',
        ]);

        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $payload = [
            'name' => 'Updated Housing Org',
            'slug' => 'updated-housing-org',
            'contact_email' => 'updates@example.com',
            'website_url' => 'https://updated.example.com',
            'about' => 'Updated mission statement.',
            'areas_active' => 'Greater Victoria',
            'user_visible' => true,
        ];

        URL::defaults(['organization' => $organization->slug]);

        $response = $this->actingAs($admin)
            ->put(route('organizations.update-own', ['organization' => $organization->slug]), $payload);

        $updatedOrganization = $organization->fresh();

        $response->assertStatus(302)
            ->assertSessionHas('success', 'Organization updated successfully!');

        $this->assertStringContainsString('/' . $organization->slug . '/dashboard', $response->headers->get('Location'));

        $this->assertSame('Updated Housing Org', $updatedOrganization->name);
        $this->assertSame('updates@example.com', $updatedOrganization->contact_email);
        $this->assertSame('Greater Victoria', $updatedOrganization->areas_active);
        $this->assertTrue((bool) $updatedOrganization->user_visible);
    }
}
