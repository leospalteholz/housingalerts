<?php

namespace Tests\Feature;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResubscribeTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_resubscribe_after_unsubscribing()
    {
        $subscriber = Subscriber::factory()->unsubscribed()->create([
            'name' => 'Test Subscriber',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($subscriber, 'subscriber');

        $this->assertNotNull($subscriber->unsubscribed_at);

        $response = $this->post(route('subscriber.resubscribe'));

        $response->assertRedirect(route('subscriber.dashboard'))
                ->assertSessionHas('success', 'You have been resubscribed to notifications.');

        $subscriber->refresh();
        $this->assertNull($subscriber->unsubscribed_at);
    }

    public function test_dashboard_shows_unsubscribed_notice()
    {
        $subscriber = Subscriber::factory()->unsubscribed()->create([
            'name' => 'Test Subscriber',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($subscriber, 'subscriber');

        $response = $this->get(route('subscriber.dashboard'));

        $response->assertStatus(200)
                ->assertSee('You have unsubscribed from all notifications')
                ->assertSee('Resubscribe to Notifications');
    }

    public function test_dashboard_does_not_show_unsubscribed_notice_when_user_is_subscribed()
    {
        $subscriber = Subscriber::factory()->create([
            'name' => 'Test Subscriber',
            'email' => 'test@example.com',
            'email_verified_at' => now(),
        ]);

        $this->actingAs($subscriber, 'subscriber');

        $response = $this->get(route('subscriber.dashboard'));

        $response->assertStatus(200)
                ->assertDontSee('You have unsubscribed from all notifications')
                ->assertDontSee('Resubscribe to Notifications');
    }
}