<?php

namespace Tests\Unit;

use App\Models\EmailNotification;
use App\Models\Organization;
use App\Models\Region;
use App\Models\Subscriber;
use App\Models\SubscriberNotificationSettings;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_queue_hearing_created_notifications_respects_preferences(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $hearing = \App\Models\Hearing::factory()
            ->for($organization, 'organization')
            ->for($region, 'region')
            ->state([
                'type' => 'development',
                'start_datetime' => now()->addDays(2),
            ])
            ->create();

        $eligible = Subscriber::factory()->create([
            'email' => 'eligible@example.com',
            'email_verified_at' => now(),
            'unsubscribed_at' => null,
        ]);
        $eligible->regions()->attach($region->id);
        SubscriberNotificationSettings::create([
            'subscriber_id' => $eligible->id,
            'notify_development_hearings' => true,
            'notify_policy_hearings' => false,
            'send_day_of_reminders' => true,
        ]);

        $unverified = Subscriber::factory()->unverified()->create([
            'email' => 'unverified@example.com',
        ]);
        $unverified->regions()->attach($region->id);
        SubscriberNotificationSettings::create([
            'subscriber_id' => $unverified->id,
            'notify_development_hearings' => true,
            'notify_policy_hearings' => true,
            'send_day_of_reminders' => true,
        ]);

        $otherRegion = Subscriber::factory()->create([
            'email' => 'other@example.com',
            'email_verified_at' => now(),
        ]);
        // Intentionally not attaching to region
        SubscriberNotificationSettings::create([
            'subscriber_id' => $otherRegion->id,
            'notify_development_hearings' => true,
            'notify_policy_hearings' => true,
            'send_day_of_reminders' => true,
        ]);

        $service = new NotificationService();

        $count = $service->queueHearingCreatedNotifications($hearing);

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('email_notifications', [
            'subscriber_id' => $eligible->id,
            'hearing_id' => $hearing->id,
            'notification_type' => 'hearing_created',
            'status' => 'queued',
        ]);

        $this->assertDatabaseMissing('email_notifications', [
            'subscriber_id' => $unverified->id,
            'hearing_id' => $hearing->id,
        ]);

        $this->assertDatabaseCount('email_notifications', 1);

        // rerun to ensure duplicates are skipped
        $secondRun = $service->queueHearingCreatedNotifications($hearing);
        $this->assertSame(0, $secondRun);
        $this->assertDatabaseCount('email_notifications', 1);
    }

    public function test_queue_day_of_reminders_only_includes_opted_in_subscribers(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $hearing = \App\Models\Hearing::factory()
            ->for($organization, 'organization')
            ->for($region, 'region')
            ->state([
                'type' => 'policy',
                'start_datetime' => now()->addDays(5),
            ])
            ->create();

        $dayOfSubscriber = Subscriber::factory()->create([
            'email' => 'dayof@example.com',
            'email_verified_at' => now(),
            'unsubscribed_at' => null,
        ]);
        $dayOfSubscriber->regions()->attach($region->id);
        SubscriberNotificationSettings::create([
            'subscriber_id' => $dayOfSubscriber->id,
            'notify_development_hearings' => true,
            'notify_policy_hearings' => true,
            'send_day_of_reminders' => true,
        ]);

        $noReminder = Subscriber::factory()->create([
            'email' => 'no-reminder@example.com',
            'email_verified_at' => now(),
            'unsubscribed_at' => null,
        ]);
        $noReminder->regions()->attach($region->id);
        SubscriberNotificationSettings::create([
            'subscriber_id' => $noReminder->id,
            'notify_development_hearings' => true,
            'notify_policy_hearings' => true,
            'send_day_of_reminders' => false,
        ]);

        $service = new NotificationService();

        $count = $service->queueDayOfReminderNotifications($hearing);

        $this->assertSame(1, $count);
        $this->assertDatabaseHas('email_notifications', [
            'subscriber_id' => $dayOfSubscriber->id,
            'notification_type' => 'day_of_reminder',
        ]);
        $this->assertDatabaseMissing('email_notifications', [
            'subscriber_id' => $noReminder->id,
            'notification_type' => 'day_of_reminder',
        ]);

        // Ensure queued notifications mark opted_in flag
        $notification = EmailNotification::where('subscriber_id', $dayOfSubscriber->id)
            ->where('notification_type', 'day_of_reminder')
            ->first();
        $this->assertTrue($notification->opted_in);
    }
}
