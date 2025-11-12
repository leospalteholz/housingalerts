<?php

namespace Tests\Feature;

use App\Models\Hearing;
use App\Models\Organization;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HearingManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_hearing_with_expected_defaults(): void
    {
        Storage::fake('public');

        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $admin = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $payload = [
            'type' => 'development',
            'title' => 'Ignored Title',
            'street_address' => '123 Main St',
            'postal_code' => 'V8V1A1',
            'region_id' => $region->id,
            'rental' => true,
            'units' => 48,
            'below_market_units' => 12,
            'replaced_units' => 4,
            'subject_to_vote' => true,
            'description' => 'Brand new homes downtown.',
            'remote_instructions' => 'Join via Zoom',
            'inperson_instructions' => 'City Hall, 123 Main St',
            'comments_email' => 'comments@example.com',
            'start_date' => now()->addDays(3)->format('Y-m-d'),
            'start_time' => '10:00',
            'end_time' => '12:30',
        ];

        $response = $this->actingAs($admin)
            ->post(route('hearings.store', ['organization' => $organization]), $payload);

        $response->assertRedirect(route('hearings.index', ['organization' => $organization]));

        $this->assertDatabaseHas('hearings', [
            'organization_id' => $organization->id,
            'region_id' => $region->id,
            'street_address' => '123 Main St',
            'title' => '123 Main St',
            'approved' => true,
        ]);

        $hearing = Hearing::first();
        $this->assertTrue($hearing->start_datetime->isSameDay(now()->addDays(3)));
        $this->assertSame('10:00:00', $hearing->start_datetime->format('H:i:s'));
        $this->assertSame('12:30:00', $hearing->end_datetime->format('H:i:s'));
    }

    public function test_superuser_can_approve_pending_hearing(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $superuser = User::factory()->create([
            'organization_id' => $organization->id,
            'is_admin' => true,
            'is_superuser' => true,
        ]);

        $hearing = Hearing::factory()
            ->for($organization, 'organization')
            ->for($region, 'region')
            ->state(['approved' => false])
            ->create();

        $response = $this->actingAs($superuser)
            ->patch(route('hearings.approve', ['organization' => $organization, 'hearing' => $hearing]));

        $response->assertRedirect(route('hearings.index', ['organization' => $organization]));

        $this->assertTrue($hearing->fresh()->approved);
    }

    public function test_export_returns_csv_with_expected_headers(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        Hearing::factory()
            ->count(2)
            ->for($organization, 'organization')
            ->for($region, 'region')
            ->state(['approved' => true])
            ->create();

        $response = $this->get(route('organization.hearings.export', ['organization' => $organization]));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

    $csv = $response->streamedContent();
    $this->assertStringContainsString('Title', $csv);
    $this->assertStringContainsString($organization->name, $csv);
    }

    public function test_embed_renders_view_with_hearing_rows(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $hearing = Hearing::factory()
            ->for($organization, 'organization')
            ->for($region, 'region')
            ->state(['approved' => true])
            ->create();

        $response = $this->get(route('organization.hearings.embed', ['organization' => $organization]));

        $response->assertOk();
        $response->assertViewIs('hearings.embed');
        $response->assertViewHas('organization', function ($value) use ($organization) {
            return $value->is($organization);
        });
        $response->assertSee($hearing->title);
    }
}
