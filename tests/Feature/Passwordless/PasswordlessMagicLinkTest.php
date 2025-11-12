<?php

namespace Tests\Feature\Passwordless;

use App\Models\Subscriber;
use App\Notifications\PasswordlessDashboardLinkNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordlessMagicLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_dashboard_token_stores_hash_and_sets_expiry(): void
    {
        $subscriber = Subscriber::factory()->create([
            'dashboard_token' => null,
            'dashboard_token_expires_at' => null,
        ]);

        $rawToken = $subscriber->generateDashboardToken();
        $subscriber->refresh();

        $this->assertNotSame($rawToken, $subscriber->dashboard_token);
        $this->assertSame(hash('sha256', $rawToken), $subscriber->dashboard_token);
        $this->assertNotNull($subscriber->dashboard_token_expires_at);
        $this->assertTrue($subscriber->hasValidDashboardToken());
    }

    public function test_dashboard_route_authenticates_with_valid_token(): void
    {
        Notification::fake();

        $subscriber = Subscriber::factory()->create([
            'dashboard_token' => null,
            'dashboard_token_expires_at' => null,
        ]);

        $rawToken = $subscriber->generateDashboardToken();

    $response = $this->get(route('dashboard.token', ['token' => $rawToken]));

    $response->assertRedirect(route('subscriber.dashboard', ['token' => $rawToken]));
        $this->assertAuthenticatedAs($subscriber, 'subscriber');
    }

    public function test_expired_token_shows_expired_view_and_sends_new_link(): void
    {
        Notification::fake();

        $subscriber = Subscriber::factory()->create([
            'dashboard_token' => hash('sha256', 'expired-token'),
            'dashboard_token_expires_at' => now()->subDay(),
        ]);

        $response = $this->get(route('dashboard.token', ['token' => 'expired-token']));

        $response->assertStatus(200)->assertViewIs('auth.passwordless-expired');
        $this->assertGuest('subscriber');
        Notification::assertSentTo($subscriber, PasswordlessDashboardLinkNotification::class, function (PasswordlessDashboardLinkNotification $notification) {
            return !$notification->isForNewAccount();
        });
    }
}
