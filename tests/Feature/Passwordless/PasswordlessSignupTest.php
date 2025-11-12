<?php

namespace Tests\Feature\Passwordless;

use App\Models\Organization;
use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\CustomVerifyEmail;
use App\Notifications\ExistingPasswordlessUserNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordlessSignupTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_email_creates_subscriber_and_sends_verification(): void
    {
        Notification::fake();

        $response = $this->post(route('signup.passwordless'), [
            'email' => 'new-user@example.com',
            'name' => 'New User',
        ]);

        $response->assertRedirect(route('subscriber.dashboard'));
        $response->assertSessionHas('success');

        $subscriber = Subscriber::where('email', 'new-user@example.com')->first();
        $this->assertNotNull($subscriber);
        $this->assertNull($subscriber->email_verified_at);
        $this->assertNotNull($subscriber->dashboard_token);
        $this->assertAuthenticatedAs($subscriber, 'subscriber');

        Notification::assertSentTo($subscriber, CustomVerifyEmail::class);
    }

    public function test_admin_email_is_redirected_to_password_login(): void
    {
        $organization = Organization::factory()->create();
        User::factory()->create([
            'email' => 'admin@example.com',
            'is_admin' => true,
            'is_superuser' => false,
            'organization_id' => $organization->id,
        ]);

        $response = $this->post(route('signup.passwordless'), [
            'email' => 'admin@example.com',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', 'Please sign in with your password to continue.');
        $this->assertGuest('subscriber');
    }

    public function test_unverified_subscriber_gets_resend(): void
    {
        Notification::fake();

        $subscriber = Subscriber::factory()->unverified()->create([
            'email' => 'pending@example.com',
        ]);

        $response = $this->post(route('signup.passwordless'), [
            'email' => 'pending@example.com',
        ]);

        $response->assertRedirect(route('subscriber.dashboard'));
        $response->assertSessionHas('success', 'Welcome back! Please verify your email to receive housing alerts.');
        $this->assertAuthenticatedAs($subscriber->fresh(), 'subscriber');

        Notification::assertSentTo($subscriber, CustomVerifyEmail::class);
    }

    public function test_verified_subscriber_receives_dashboard_link_view(): void
    {
        Notification::fake();

        $subscriber = Subscriber::factory()->create([
            'email' => 'verified@example.com',
        ]);

        $response = $this->post(route('signup.passwordless'), [
            'email' => 'verified@example.com',
        ]);

        $response->assertOk();
        $response->assertViewIs('auth.passwordless-existing');
        $response->assertViewHas('email', 'verified@example.com');
        $this->assertGuest('subscriber');

        Notification::assertSentTo($subscriber, ExistingPasswordlessUserNotification::class);
    }
}
