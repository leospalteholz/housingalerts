<?php

namespace Tests\Feature;

use App\Models\Hearing;
use App\Models\Organization;
use App\Models\Region;
use App\Models\Subscriber;
use App\Models\SubscriberNotificationSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_view_lists_monitored_regions_and_upcoming_hearings(): void
    {
        $organization = Organization::factory()->create();
        $monitoredRegion = Region::factory()->for($organization, 'organization')->create(['name' => 'Downtown']);
        $otherRegion = Region::factory()->for($organization, 'organization')->create(['name' => 'Harbour']);

        $subscriber = Subscriber::factory()->create([
            'email_verified_at' => now(),
        ]);
        $subscriber->regions()->attach($monitoredRegion->id);

        SubscriberNotificationSettings::create([
            'subscriber_id' => $subscriber->id,
            'notify_development_hearings' => true,
            'notify_policy_hearings' => true,
            'send_day_of_reminders' => true,
        ]);

        Hearing::factory()
            ->for($organization, 'organization')
            ->for($monitoredRegion, 'region')
            ->state([
                'type' => 'policy',
                'approved' => true,
                'start_datetime' => now()->addDays(2),
                'title' => 'Downtown Towers',
            ])
            ->create();

        Hearing::factory()
            ->for($organization, 'organization')
            ->for($otherRegion, 'region')
            ->state([
                'type' => 'policy',
                'approved' => true,
                'start_datetime' => now()->addDays(2),
                'title' => 'Harbour Project',
            ])
            ->create();

        $response = $this->actingAs($subscriber, 'subscriber')
            ->get(route('subscriber.dashboard'));

        $response->assertOk()
            ->assertViewIs('user.dashboard')
            ->assertViewHas('monitoredRegions', function ($regions) use ($monitoredRegion) {
                return $regions->contains('id', $monitoredRegion->id);
            })
            ->assertViewHas('upcomingHearings', function ($hearings) {
                return $hearings->count() === 1;
            });

        $response->assertSee('Downtown Towers');
        $response->assertDontSee('Harbour Project');
    }

    public function test_update_notification_preferences_applies_boolean_flags(): void
    {
        $subscriber = Subscriber::factory()->create([
            'email_verified_at' => now(),
        ]);

        SubscriberNotificationSettings::create([
            'subscriber_id' => $subscriber->id,
            'notify_development_hearings' => true,
            'notify_policy_hearings' => true,
            'send_day_of_reminders' => true,
        ]);

        $response = $this->actingAs($subscriber, 'subscriber')
            ->postJson(route('subscriber.notification-preferences'), [
                'notify_development_hearings' => true,
                // intentionally omit policy flag to ensure it toggles off
            ]);

        $response->assertOk()
            ->assertJson(['success' => true]);

        $settings = SubscriberNotificationSettings::where('subscriber_id', $subscriber->id)->first();
        $this->assertTrue((bool) $settings->notify_development_hearings);
        $this->assertFalse((bool) $settings->notify_policy_hearings);
    }

    public function test_upcoming_hearings_endpoint_returns_only_future_approved_hearings(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $subscriber = Subscriber::factory()->create([
            'email_verified_at' => now(),
        ]);
        $subscriber->regions()->attach($region->id);

        SubscriberNotificationSettings::create([
            'subscriber_id' => $subscriber->id,
            'notify_development_hearings' => true,
            'notify_policy_hearings' => true,
            'send_day_of_reminders' => true,
        ]);

        Hearing::factory()->for($organization, 'organization')->for($region, 'region')->create([
            'type' => 'policy',
            'approved' => true,
            'start_datetime' => now()->addDay(),
            'title' => 'Future Approved',
        ]);

        Hearing::factory()->for($organization, 'organization')->for($region, 'region')->create([
            'type' => 'policy',
            'approved' => false,
            'start_datetime' => now()->addDay(),
            'title' => 'Future Unapproved',
        ]);

        Hearing::factory()->for($organization, 'organization')->for($region, 'region')->create([
            'type' => 'policy',
            'approved' => true,
            'start_datetime' => now()->subDay(),
            'title' => 'Past Hearing',
        ]);

        $response = $this->actingAs($subscriber, 'subscriber')
            ->get(route('subscriber.hearings'));

        $response->assertOk();
        $content = $response->getContent();
        $this->assertStringContainsString('Future Approved', $content);
        $this->assertStringNotContainsString('Future Unapproved', $content);
        $this->assertStringNotContainsString('Past Hearing', $content);
    }
}
