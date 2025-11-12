<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use App\Http\Controllers\UnsubscribeController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnsubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_unsubscribe_url_generation()
    {
        $subscriber = Subscriber::factory()->create([
            'name' => 'Test Subscriber',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $unsubscribeUrl = UnsubscribeController::generateUnsubscribeUrl($subscriber);

        // Verify the URL contains the expected components
        $this->assertStringContainsString('/unsubscribe', $unsubscribeUrl);
        $this->assertStringContainsString('email=test%40example.com', $unsubscribeUrl);
        $this->assertStringContainsString('signature=', $unsubscribeUrl);
    }

    public function test_unsubscribe_shows_confirmation_page()
    {
        $subscriber = Subscriber::factory()->create([
            'name' => 'Test Subscriber',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $unsubscribeUrl = UnsubscribeController::generateUnsubscribeUrl($subscriber);

        $response = $this->get($unsubscribeUrl);

        $response->assertStatus(200);
        $response->assertSee('Unsubscribe from All Notifications');
        $response->assertSee('test@example.com');
        $response->assertSee('Yes, Unsubscribe Me');
    }

    public function test_unsubscribe_process_works()
    {
        $subscriber = Subscriber::factory()->create([
            'name' => 'Test Subscriber',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $this->assertNull($subscriber->unsubscribed_at);

        $unsubscribeUrl = UnsubscribeController::generateUnsubscribeUrl($subscriber);

        $response = $this->post($unsubscribeUrl);

    $response->assertStatus(200);
    $response->assertSeeText('Been Unsubscribed');
    $response->assertSee('test@example.com');

    $subscriber->refresh();
    $this->assertNotNull($subscriber->unsubscribed_at);
    }

    public function test_already_unsubscribed_user_sees_appropriate_message()
    {
        $subscriber = Subscriber::factory()->unsubscribed()->create([
            'name' => 'Test Subscriber',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $unsubscribeUrl = UnsubscribeController::generateUnsubscribeUrl($subscriber);

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