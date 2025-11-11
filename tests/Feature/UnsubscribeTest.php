<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Organization;
use App\Http\Controllers\UnsubscribeController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class UnsubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsubscribe_url_generation()
    {
        // Create a test organization and user
        $organization = Organization::create([
            'name' => 'Test Organization',
            'contact_email' => 'contact@test.org',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);

        $unsubscribeUrl = UnsubscribeController::generateUnsubscribeUrl($user);

        // Verify the URL contains the expected components
        $this->assertStringContainsString('/unsubscribe', $unsubscribeUrl);
        $this->assertStringContainsString('email=test%40example.com', $unsubscribeUrl);
        $this->assertStringContainsString('signature=', $unsubscribeUrl);
    }

    public function test_unsubscribe_shows_confirmation_page()
    {
        // Create a test organization and user
        $organization = Organization::create([
            'name' => 'Test Organization',
            'contact_email' => 'contact@test.org',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);

        $unsubscribeUrl = UnsubscribeController::generateUnsubscribeUrl($user);

        $response = $this->get($unsubscribeUrl);

        $response->assertStatus(200);
        $response->assertSee('Unsubscribe from All Notifications');
        $response->assertSee('test@example.com');
        $response->assertSee('Yes, Unsubscribe Me');
    }

    public function test_unsubscribe_process_works()
    {
        // Create a test organization and user
        $organization = Organization::create([
            'name' => 'Test Organization',
            'contact_email' => 'contact@test.org',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
        ]);

        $this->assertNull($user->unsubscribed_at);

        $unsubscribeUrl = UnsubscribeController::generateUnsubscribeUrl($user);

        $response = $this->post($unsubscribeUrl);

    $response->assertStatus(200);
    $response->assertSeeText('Been Unsubscribed');
    $response->assertSee('test@example.com');

        // Verify user is now unsubscribed
        $user->refresh();
        $this->assertNotNull($user->unsubscribed_at);
    }

    public function test_already_unsubscribed_user_sees_appropriate_message()
    {
        // Create a test organization and user
        $organization = Organization::create([
            'name' => 'Test Organization',
            'contact_email' => 'contact@test.org',
        ]);

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'organization_id' => $organization->id,
            'email_verified_at' => now(),
            'unsubscribed_at' => now(),
        ]);

        $unsubscribeUrl = UnsubscribeController::generateUnsubscribeUrl($user);

        $response = $this->get($unsubscribeUrl);

        $response->assertStatus(200);
        $response->assertSee('Already Unsubscribed');
        $response->assertSee('test@example.com');
    }

    public function test_invalid_signature_returns_403()
    {
        $response = $this->get('/unsubscribe?email=test@example.com&signature=invalid');

        $response->assertStatus(403);
    }
}