<?php

namespace Tests\Feature\Auth;

use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscriber_becomes_verified_after_dashboard_login(): void
    {
        $subscriber = Subscriber::factory()->unverified()->create([
            'email' => 'new-subscriber@example.com',
        ]);

        $this->assertNull($subscriber->email_verified_at);

        $rawToken = $subscriber->generateDashboardToken();

        $this->get(route('dashboard.token', ['token' => $rawToken]))
            ->assertRedirect(route('subscriber.dashboard', ['token' => $rawToken]));

        $subscriber->refresh();

        $this->assertAuthenticatedAs($subscriber, 'subscriber');
        $this->assertNotNull($subscriber->email_verified_at);
        $this->assertTrue($subscriber->hasVerifiedEmail());

    }
}
