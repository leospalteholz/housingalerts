<?php

namespace Tests\Feature;

use App\Models\Hearing;
use App\Models\Organization;
use App\Models\Region;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HearingApprovalVisibilityTest extends TestCase
{
    use RefreshDatabase;

    private Organization $organization;
    private Region $region;
    private Hearing $approvedHearing;
    private Hearing $pendingHearing;

    protected function setUp(): void
    {
        parent::setUp();

        $this->organization = Organization::create([
            'name' => 'Test Org',
            'slug' => Str::slug('Test Org'),
            'contact_email' => 'contact@test.org',
        ]);

        $this->region = Region::create([
            'organization_id' => $this->organization->id,
            'name' => 'Test Region',
        ]);

        $this->approvedHearing = Hearing::create([
            'organization_id' => $this->organization->id,
            'region_id' => $this->region->id,
            'type' => 'policy',
            'title' => 'Approved Hearing',
            'below_market_units' => 0,
            'subject_to_vote' => true,
            'approved' => true,
            'description' => 'Approved hearing description',
            'start_datetime' => now()->addDays(5),
            'end_datetime' => now()->addDays(5)->addHour(),
            'remote_instructions' => 'Remote instructions',
            'inperson_instructions' => 'In-person instructions',
            'comments_email' => 'comments@test.org',
        ]);

        $this->pendingHearing = Hearing::create([
            'organization_id' => $this->organization->id,
            'region_id' => $this->region->id,
            'type' => 'policy',
            'title' => 'Pending Hearing',
            'below_market_units' => 0,
            'subject_to_vote' => true,
            'approved' => false,
            'description' => 'Pending hearing description',
            'start_datetime' => now()->addDays(6),
            'end_datetime' => now()->addDays(6)->addHour(),
            'remote_instructions' => 'Remote instructions',
            'inperson_instructions' => 'In-person instructions',
            'comments_email' => 'comments@test.org',
        ]);
    }

    public function test_admin_can_see_pending_hearings(): void
    {
        $admin = User::factory()->create([
            'organization_id' => $this->organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $response = $this->actingAs($admin)->get(route('hearings.index', [
            'organization' => $this->organization->slug,
        ]));

        $response->assertOk();
        $response->assertSee('Pending Approval');
        $response->assertSee('Approved Hearing');
        $response->assertSee('Pending Hearing');
        $response->assertSee('Approve');
    }

    public function test_admin_can_approve_pending_hearing(): void
    {
        $admin = User::factory()->create([
            'organization_id' => $this->organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['_token' => 'test-token'])
            ->patch(route('hearings.approve', [
                'organization' => $this->organization->slug,
                'hearing' => $this->pendingHearing,
            ]), ['_token' => 'test-token']);

        $response->assertRedirect(route('hearings.index', [
            'organization' => $this->organization->slug,
        ]));
        $this->assertTrue($this->pendingHearing->fresh()->approved);
    }

    public function test_admin_cannot_approve_hearing_from_other_organization(): void
    {
        $otherOrganization = Organization::create([
            'name' => 'Other Org',
            'slug' => Str::slug('Other Org'),
            'contact_email' => 'contact@other.org',
        ]);

        $otherRegion = Region::create([
            'organization_id' => $otherOrganization->id,
            'name' => 'Other Region',
        ]);

        $otherHearing = Hearing::create([
            'organization_id' => $otherOrganization->id,
            'region_id' => $otherRegion->id,
            'type' => 'policy',
            'title' => 'Other Hearing',
            'below_market_units' => 0,
            'subject_to_vote' => true,
            'approved' => false,
            'description' => 'Other description',
            'start_datetime' => now()->addDays(3),
            'end_datetime' => now()->addDays(3)->addHour(),
            'remote_instructions' => 'Remote instructions',
            'inperson_instructions' => 'In-person instructions',
            'comments_email' => 'comments@other.org',
        ]);

        $admin = User::factory()->create([
            'organization_id' => $this->organization->id,
            'is_admin' => true,
            'is_superuser' => false,
        ]);

        $response = $this->actingAs($admin)
            ->withSession(['_token' => 'test-token'])
            ->patch(route('hearings.approve', [
                'organization' => $this->organization->slug,
                'hearing' => $otherHearing,
            ]), ['_token' => 'test-token']);

        $response->assertForbidden();
        $this->assertFalse($otherHearing->fresh()->approved);
    }

    public function test_regular_user_only_sees_approved_hearings(): void
    {
        $user = User::factory()->create([
            'organization_id' => $this->organization->id,
            'is_admin' => false,
            'is_superuser' => false,
        ]);
        $user->regions()->attach($this->region->id);

        $response = $this->actingAs($user)->get(route('hearings.index', [
            'organization' => $this->organization->slug,
        ]));

        $response->assertOk();
        $response->assertSee('Approved Hearing');
        $response->assertDontSee('Pending Hearing');
    }

    public function test_public_embed_only_shows_approved_hearings(): void
    {
        $response = $this->get(route('organization.hearings.embed', [
            'organization' => $this->organization->slug,
        ]));

        $response->assertOk();
        $response->assertSee('Approved Hearing');
        $response->assertDontSee('Pending Hearing');
    }
}
