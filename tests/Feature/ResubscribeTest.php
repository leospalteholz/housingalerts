<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_resubscribe_after_unsubscribing()
    {
        // Create a test organization and user
        $organization = Organization::create([
            'name' => 'Test Organization',
            'domain' => 'test.com',
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
            'unsubscribed_at' => now(), // User is initially unsubscribed
        ]);

        // Authenticate the user
        $this->actingAs($user);

        // Verify user is unsubscribed
        $this->assertNotNull($user->unsubscribed_at);

        // Post to resubscribe route
        $response = $this->post(route('user.resubscribe'));

        // Should redirect to dashboard with success message
        $response->assertRedirect(route('user.dashboard'))
                ->assertSessionHas('success', 'You have been resubscribed to notifications.');

        // Verify user is now resubscribed
        $user->refresh();
        $this->assertNull($user->unsubscribed_at);
    }

    public function test_dashboard_shows_unsubscribed_notice()
    {
        // Create a test organization and user
        $organization = Organization::create([
            'name' => 'Test Organization',
            'domain' => 'test.com',
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
            'unsubscribed_at' => now(), // User is unsubscribed
        ]);

        // Authenticate the user
        $this->actingAs($user);

        // Visit dashboard
        $response = $this->get(route('user.dashboard'));

        // Should see unsubscribed notice
        $response->assertStatus(200)
                ->assertSee('You have unsubscribed from all notifications')
                ->assertSee('Resubscribe to Notifications');
    }

    public function test_dashboard_does_not_show_unsubscribed_notice_when_user_is_subscribed()
    {
        // Create a test organization and user
        $organization = Organization::create([
            'name' => 'Test Organization',
            'domain' => 'test.com',
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
            'unsubscribed_at' => null, // User is subscribed
        ]);

        // Authenticate the user
        $this->actingAs($user);

        // Visit dashboard
        $response = $this->get(route('user.dashboard'));

        // Should not see unsubscribed notice
        $response->assertStatus(200)
                ->assertDontSee('You have unsubscribed from all notifications')
                ->assertDontSee('Resubscribe to Notifications');
    }
}