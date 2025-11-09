<?php

namespace Tests\Feature\Auth;

use App\Models\Organization;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_can_be_rendered(): void
    {
        $organization = Organization::create([
            'name' => 'Verification Org',
            'slug' => 'verification-org',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => null,
            'organization_id' => $organization->id,
        ]);

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertStatus(200);
    }

    public function test_email_can_be_verified(): void
    {
        $organization = Organization::create([
            'name' => 'Verify Org',
            'slug' => 'verify-org',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => null,
            'organization_id' => $organization->id,
        ]);

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user)->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    $response->assertRedirect(RouteServiceProvider::homeRoute($user, ['verified' => 1]));
    }

    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $organization = Organization::create([
            'name' => 'Invalid Hash Org',
            'slug' => 'invalid-hash-org',
        ]);

        $user = User::factory()->create([
            'email_verified_at' => null,
            'organization_id' => $organization->id,
        ]);

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
