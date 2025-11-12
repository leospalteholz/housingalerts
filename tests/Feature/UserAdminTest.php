<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_user_in_their_organization(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $payload = [
            'name' => 'Casey Member',
            'email' => 'casey@example.com',
            'password' => 'Secret123!',
            'password_confirmation' => 'Secret123!',
        ];

        $response = $this->actingAs($admin)
            ->post(route('users.store', ['organization' => $organization->slug]), $payload);

        $response->assertStatus(302)
            ->assertSessionHas('success', 'User created successfully!');

        $createdUser = User::where('email', 'casey@example.com')->first();
        $this->assertNotNull($createdUser);
        $this->assertSame($organization->id, $createdUser->organization_id);
        $this->assertTrue(Hash::check('Secret123!', $createdUser->password));
        $this->assertNotNull($createdUser->email_verified_at);
        $this->assertTrue((bool) $createdUser->is_admin);
    }

    public function test_superuser_can_create_user_for_any_organization(): void
    {
        $organization = Organization::factory()->create();
        $targetOrganization = Organization::factory()->create();
        $superuser = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => true,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $payload = [
            'name' => 'External Admin',
            'email' => 'external@example.com',
            'password' => 'Outside123!',
            'password_confirmation' => 'Outside123!',
            'organization_id' => $targetOrganization->id,
        ];

        $response = $this->actingAs($superuser)
            ->post(route('users.store', ['organization' => $organization->slug]), $payload);

        $response->assertStatus(302)
            ->assertSessionHas('success', 'User created successfully!');

        $created = User::where('email', 'external@example.com')->first();
        $this->assertNotNull($created);
        $this->assertSame($targetOrganization->id, $created->organization_id);
        $this->assertTrue((bool) $created->is_admin);
    }

    public function test_admin_cannot_delete_their_own_account(): void
    {
        $organization = Organization::factory()->create();
        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $response = $this->actingAs($admin)
            ->delete(route('users.destroy', ['organization' => $organization->slug, 'user' => $admin->id]));

        $response->assertStatus(302)
            ->assertSessionHas('error', 'You cannot delete your own account.');

        $this->assertDatabaseHas('users', ['id' => $admin->id]);
    }

    public function test_admin_cannot_manage_user_from_other_organization(): void
    {
        $organization = Organization::factory()->create();
        $otherOrganization = Organization::factory()->create();

        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $otherUser = User::factory()->create([
            'organization_id' => $otherOrganization->id,
            'is_admin' => false,
        ]);

        URL::defaults(['organization' => $organization->slug]);

        $response = $this->actingAs($admin)
            ->get(route('users.edit', ['organization' => $organization->slug, 'user' => $otherUser->id]));

        $response->assertNotFound();
    }
}
