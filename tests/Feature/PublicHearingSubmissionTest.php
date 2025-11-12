<?php

namespace Tests\Feature;

use App\Models\Councillor;
use App\Models\Hearing;
use App\Models\HearingVote;
use App\Models\Organization;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicHearingSubmissionTest extends TestCase
{
    use RefreshDatabase;

    public function test_public_submission_creates_development_hearing(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $payload = [
            'type' => 'development',
            'street_address' => '123 Main St',
            'postal_code' => 'V8V 1A1',
            'rental' => true,
            'units' => 20,
            'below_market_units' => 5,
            'replaced_units' => 0,
            'subject_to_vote' => false,
            'description' => 'New homes near downtown.',
            'remote_instructions' => 'Watch on Zoom',
            'inperson_instructions' => 'City Hall',
            'comments_email' => 'comments@example.com',
            'region_id' => $region->id,
            'start_date' => now()->addWeek()->format('Y-m-d'),
            'start_time' => '18:00',
            'end_time' => '19:30',
        ];

        $response = $this->post(route('public.hearings.submit.store', $organization), $payload);

        $response->assertRedirect(route('public.hearings.submit.thank-you', $organization));

        $this->assertDatabaseHas('hearings', [
            'organization_id' => $organization->id,
            'region_id' => $region->id,
            'type' => 'development',
            'street_address' => '123 Main St',
            'title' => '123 Main St',
            'subject_to_vote' => false,
            'approved' => false,
        ]);
    }

    public function test_vote_information_is_persisted_when_present(): void
    {
        $organization = Organization::factory()->create();
        $region = Region::factory()->for($organization, 'organization')->create();

        $councillor = Councillor::create([
            'region_id' => $region->id,
            'name' => 'Councillor Annie Lee',
            'elected_start' => now()->subYear()->toDateString(),
            'elected_end' => null,
        ]);

        $payload = [
            'type' => 'development',
            'street_address' => '456 Oak Ave',
            'postal_code' => 'V8V 2B2',
            'rental' => true,
            'units' => 30,
            'below_market_units' => 6,
            'replaced_units' => 1,
            'subject_to_vote' => true,
            'description' => 'Rezoning for mid-rise building.',
            'remote_instructions' => null,
            'inperson_instructions' => 'Council chambers',
            'comments_email' => 'planning@example.com',
            'region_id' => $region->id,
            'start_date' => now()->format('Y-m-d'),
            'start_time' => '19:00',
            'end_time' => '21:00',
            'vote_date' => now()->format('Y-m-d'),
            'passed' => true,
            'notes' => 'Approved unanimously.',
            'vote_' . $councillor->id => 'for',
            'vote_9999' => 'against',
        ];

        $response = $this->post(route('public.hearings.submit.store', $organization), $payload);

        $response->assertRedirect(route('public.hearings.submit.thank-you', $organization));

        $hearing = Hearing::where('street_address', '456 Oak Ave')->first();
        $this->assertNotNull($hearing);

        $vote = HearingVote::where('hearing_id', $hearing->id)->first();
        $this->assertNotNull($vote);
        $this->assertTrue($vote->passed);
        $this->assertSame('Approved unanimously.', $vote->notes);

        $this->assertDatabaseHas('councillor_votes', [
            'hearing_vote_id' => $vote->id,
            'councillor_id' => $councillor->id,
            'vote' => 'for',
        ]);

        $this->assertDatabaseMissing('councillor_votes', [
            'hearing_vote_id' => $vote->id,
            'councillor_id' => 9999,
        ]);
    }
}
