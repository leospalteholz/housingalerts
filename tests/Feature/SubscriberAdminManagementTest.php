<?php

namespace Tests\Feature;

use App\Models\Organization;
use App\Models\Region;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubscriberAdminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_only_organization_subscribers(): void
    {
        [$organizationA, $organizationB, $regionA, $regionB] = $this->createOrganizationsWithRegions();

        $subscriberA = Subscriber::factory()->create(['name' => 'Org A Subscriber']);
        $subscriberB = Subscriber::factory()->create(['name' => 'Org B Subscriber']);
        $subscriberC = Subscriber::factory()->create(['name' => 'No Region Subscriber']);

        $subscriberA->regions()->attach($regionA->id);
        $subscriberB->regions()->attach($regionB->id);

        $admin = User::factory()->create([
            'organization_id' => $organizationA->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('subscribers.index', $organizationA));

        $response->assertOk()
            ->assertSee('Org A Subscriber')
            ->assertDontSee('Org B Subscriber')
            ->assertDontSee('No Region Subscriber');

        $viewSubscribers = $response->viewData('subscribers');
        $this->assertCount(1, $viewSubscribers);
        $this->assertTrue($viewSubscribers->first()->is($subscriberA));
    }

    public function test_admin_cannot_delete_subscriber_from_other_organization(): void
    {
        [$organizationA, $organizationB, $regionA, $regionB] = $this->createOrganizationsWithRegions();

        $subscriberA = Subscriber::factory()->create();
        $subscriberB = Subscriber::factory()->create();

        $subscriberA->regions()->attach($regionA->id);
        $subscriberB->regions()->attach($regionB->id);

        $admin = User::factory()->create([
            'organization_id' => $organizationA->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $this->actingAs($admin)
            ->delete(route('subscribers.destroy', ['organization' => $organizationA, 'subscriber' => $subscriberB]))
            ->assertNotFound();

        $this->assertDatabaseHas('subscribers', ['id' => $subscriberB->id]);
    }

    public function test_superuser_sees_all_subscribers_and_can_delete(): void
    {
        [$organizationA, $organizationB, $regionA, $regionB] = $this->createOrganizationsWithRegions();

        $subscriberA = Subscriber::factory()->create(['name' => 'First Subscriber']);
        $subscriberB = Subscriber::factory()->create(['name' => 'Second Subscriber']);

        $subscriberA->regions()->attach($regionA->id);
        $subscriberB->regions()->attach($regionB->id);

        $superuser = User::factory()->create([
            'organization_id' => $organizationA->id,
            'is_admin' => true,
            'is_superuser' => true,
        ]);

        $response = $this->actingAs($superuser)
            ->get(route('subscribers.index', $organizationA));

        $response->assertOk()
            ->assertSee('First Subscriber')
            ->assertSee('Second Subscriber');

        $this->actingAs($superuser)
            ->delete(route('subscribers.destroy', ['organization' => $organizationA, 'subscriber' => $subscriberB]))
            ->assertRedirect(route('subscribers.index', $organizationA));

        $this->assertDatabaseMissing('subscribers', ['id' => $subscriberB->id]);
    }

    /**
     * @return array{0: Organization, 1: Organization, 2: Region, 3: Region}
     */
    private function createOrganizationsWithRegions(): array
    {
        $organizationA = Organization::create([
            'name' => 'Org A',
            'areas_active' => 'Area A',
            'contact_email' => 'orga@example.com',
            'website_url' => 'https://orga.test',
            'about' => 'About Org A',
            'user_visible' => true,
        ]);

        $organizationB = Organization::create([
            'name' => 'Org B',
            'areas_active' => 'Area B',
            'contact_email' => 'orgb@example.com',
            'website_url' => 'https://orgb.test',
            'about' => 'About Org B',
            'user_visible' => true,
        ]);

        $regionA = Region::create([
            'name' => 'Region A',
            'organization_id' => $organizationA->id,
            'comments_email' => 'regiona@example.com',
        ]);

        $regionB = Region::create([
            'name' => 'Region B',
            'organization_id' => $organizationB->id,
            'comments_email' => 'regionb@example.com',
        ]);

        return [$organizationA->fresh(), $organizationB->fresh(), $regionA->fresh(), $regionB->fresh()];
    }
}
