<?php

namespace Tests\Feature\Console;

use App\Mail\HearingNotificationMail;
use App\Models\EmailNotification;
use App\Models\Organization;
use App\Models\Region;
use App\Models\Subscriber;
use App\Models\SubscriberNotificationSettings;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ProcessPendingNotificationsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    public function test_command_sends_hearing_created_notifications(): void
    {
        Mail::fake();
        Carbon::setTestNow(now());

        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $hearing = \App\Models\Hearing::factory()
            ->for($organization, 'organization')
            ->for($region, 'region')
            ->state([
                'type' => 'development',
                'start_datetime' => now()->addDay(),
                'created_at' => now()->subMinutes(10),
            ])
            ->create();

        $subscriber = Subscriber::factory()->create([
            'email' => 'notify@example.com',
            'email_verified_at' => now(),
            'unsubscribed_at' => null,
        ]);
        $subscriber->regions()->attach($region->id);
        SubscriberNotificationSettings::create([
            'subscriber_id' => $subscriber->id,
            'notify_development_hearings' => true,
            'notify_policy_hearings' => true,
            'send_day_of_reminders' => true,
        ]);

        $this->artisan('notifications:process-pending')->assertExitCode(0);

        Mail::assertSent(HearingNotificationMail::class, function (HearingNotificationMail $mail) use ($subscriber, $hearing) {
            return $mail->subscriber->is($subscriber)
                && $mail->hearing->is($hearing)
                && $mail->template === 'created';
        });

        $this->assertDatabaseHas('email_notifications', [
            'subscriber_id' => $subscriber->id,
            'hearing_id' => $hearing->id,
            'notification_type' => 'hearing_created',
            'status' => 'sent',
        ]);
    }

    public function test_command_sends_day_of_reminder_notifications(): void
    {
        Mail::fake();
        Carbon::setTestNow(now());

        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $subscriber = Subscriber::factory()->create([
            'email' => 'dayof@example.com',
            'email_verified_at' => now(),
        ]);

        $hearing = \App\Models\Hearing::factory()
            ->for($organization, 'organization')
            ->for($region, 'region')
            ->state([
                'start_datetime' => now()->setTime(18, 0),
            ])
            ->create();

        $notification = EmailNotification::create([
            'subscriber_id' => $subscriber->id,
            'hearing_id' => $hearing->id,
            'notification_type' => 'day_of_reminder',
            'email_address' => 'dayof@example.com',
            'status' => 'queued',
            'opted_in' => true,
        ]);

        $this->artisan('notifications:process-pending')->assertExitCode(0);

        Mail::assertSent(HearingNotificationMail::class, function (HearingNotificationMail $mail) use ($subscriber, $hearing) {
            return $mail->subscriber->is($subscriber)
                && $mail->hearing->is($hearing)
                && $mail->template === 'day_of_reminder';
        });

        $notification->refresh();
        $this->assertSame('sent', $notification->status);
        $this->assertNotNull($notification->sent_at);
    }
}
