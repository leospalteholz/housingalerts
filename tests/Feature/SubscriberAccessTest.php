<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Region;
use App\Models\Subscriber;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_subscriber_can_subscribe_to_region(): void
    {
        [$organization, $region] = $this->createOrganizationWithRegion();
        $subscriber = Subscriber::factory()->create();

        $response = $this->actingAs($subscriber, 'subscriber')
            ->postJson(route('regions.subscribe', [
                'organization' => $organization,
                'region' => $region,
            ]));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('region_subscriber', [
            'subscriber_id' => $subscriber->id,
            'region_id' => $region->id,
        ]);
    }

    public function test_subscriber_can_unsubscribe_from_region(): void
    {
        [$organization, $region] = $this->createOrganizationWithRegion();
        $subscriber = Subscriber::factory()->create();
        $subscriber->regions()->attach($region->id);

        $response = $this->actingAs($subscriber, 'subscriber')
            ->postJson(route('regions.unsubscribe', [
                'organization' => $organization,
                'region' => $region,
            ]));

        $response->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseMissing('region_subscriber', [
            'subscriber_id' => $subscriber->id,
            'region_id' => $region->id,
        ]);
    }

    public function test_guest_cannot_subscribe_to_region(): void
    {
        [$organization, $region] = $this->createOrganizationWithRegion();

        $response = $this->postJson(route('regions.subscribe', [
            'organization' => $organization,
            'region' => $region,
        ]));

        $response->assertUnauthorized();

        $this->assertDatabaseCount('region_subscriber', 0);
    }

    /**
     * @return array{0: Organization, 1: Region}
     */
    private function createOrganizationWithRegion(): array
    {
        $organization = Organization::create([
            'name' => 'Test Organization',
            'contact_email' => 'org@example.com',
            'website_url' => 'https://example.com',
            'about' => 'Test org about text',
            'areas_active' => 'Test Area',
            'user_visible' => true,
        ]);

        $region = Region::create([
            'name' => 'Test Region',
            'organization_id' => $organization->id,
            'comments_email' => 'comments@example.com',
        ]);

        return [$organization->fresh(), $region->fresh()];
    }
}
