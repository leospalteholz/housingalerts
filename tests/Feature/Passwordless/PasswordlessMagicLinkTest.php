<?php

namespace Tests\Feature\Passwordless;

use App\Models\Organization;
use App\Models\User;
use App\Notifications\ExistingPasswordlessUserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordlessMagicLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_dashboard_token_stores_hash_and_sets_expiry(): void
    {
        $user = User::factory()->create([
            'password' => null,
            'dashboard_token' => null,
            'dashboard_token_expires_at' => null,
        ]);

        $rawToken = $user->generateDashboardToken();
        $user->refresh();

        $this->assertNotSame($rawToken, $user->dashboard_token);
        $this->assertSame(hash('sha256', $rawToken), $user->dashboard_token);
        $this->assertNotNull($user->dashboard_token_expires_at);
        $this->assertTrue($user->hasValidDashboardToken());
    }

    public function test_dashboard_route_authenticates_with_valid_token(): void
    {
        Notification::fake();

        $organization = Organization::create([
            'name' => 'Test Org',
            'contact_email' => 'test@example.com',
        ]);

        $user = User::factory()->create([
            'password' => null,
            'organization_id' => $organization->id,
            'is_admin' => false,
            'is_superuser' => false,
        ]);

        $rawToken = $user->generateDashboardToken();

        $response = $this->get(route('dashboard.token', ['token' => $rawToken]));

        $response->assertRedirect(route('user.dashboard', ['organization' => $organization->slug]));
        $this->assertAuthenticatedAs($user);
    }

    public function test_expired_token_shows_expired_view_and_sends_new_link(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'password' => null,
            'dashboard_token' => hash('sha256', 'expired-token'),
            'dashboard_token_expires_at' => now()->subDay(),
        ]);

        $response = $this->get(route('dashboard.token', ['token' => 'expired-token']));

        $response->assertStatus(200)->assertViewIs('auth.passwordless-expired');
        $this->assertGuest();
        Notification::assertSentTo($user, ExistingPasswordlessUserNotification::class);
    }

    public function test_legacy_plaintext_token_triggers_resend_flow(): void
    {
        Notification::fake();

        $user = User::factory()->create([
            'password' => null,
            'dashboard_token' => 'legacy-token',
            'dashboard_token_expires_at' => now()->addDay(),
        ]);

        $response = $this->get(route('dashboard.token', ['token' => 'legacy-token']));

        $response->assertStatus(200)->assertViewIs('auth.passwordless-expired');
        $this->assertGuest();
        Notification::assertSentTo($user, ExistingPasswordlessUserNotification::class);

        $user->refresh();
        $this->assertNotSame('legacy-token', $user->dashboard_token);
        $this->assertEquals(64, strlen($user->dashboard_token));
    }
}
