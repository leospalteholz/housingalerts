<?php

namespace Tests\Feature;

use App\Models\Councillor;
use App\Models\CouncillorVote;
use App\Models\Hearing;
use App\Models\HearingVote;
use App\Models\Organization;
use App\Models\Region;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegionCouncillorEmbedTest extends TestCase
{
    use RefreshDatabase;

    public function test_region_embed_displays_vote_breakdown(): void
    {
        $organization = new Organization();
        $organization->name = 'Test Org';
        $organization->slug = Str::slug('Test Org');
        $organization->contact_email = 'contact@test.org';
        $organization->save();

        $region = new Region();
        $region->organization_id = $organization->id;
        $region->name = 'Central Region';
        $region->save();

        $councillorA = new Councillor();
        $councillorA->region_id = $region->id;
        $councillorA->name = 'Councillor Alpha';
        $councillorA->elected_start = now()->subYears(2)->toDateString();
        $councillorA->save();

        $councillorB = new Councillor();
        $councillorB->region_id = $region->id;
        $councillorB->name = 'Councillor Beta';
        $councillorB->elected_start = now()->subYears(3)->toDateString();
        $councillorB->save();

        $hearingOne = Hearing::create([
            'organization_id' => $organization->id,
            'region_id' => $region->id,
            'type' => 'development',
            'title' => 'Hearing One',
            'street_address' => '123 Main St',
            'postal_code' => 'V0V0V0',
            'rental' => true,
            'units' => 120,
            'below_market_units' => 30,
            'replaced_units' => 0,
            'subject_to_vote' => true,
            'approved' => true,
            'description' => 'Description one',
            'start_datetime' => now()->addDays(5),
            'end_datetime' => now()->addDays(5)->addHours(2),
            'more_info_url' => null,
            'remote_instructions' => 'Remote join info',
            'inperson_instructions' => 'In-person info',
            'comments_email' => 'comments@test.org',
        ]);

        $hearingTwo = Hearing::create([
            'organization_id' => $organization->id,
            'region_id' => $region->id,
            'type' => 'development',
            'title' => 'Hearing Two',
            'street_address' => '789 Side St',
            'postal_code' => 'V1V1V1',
            'rental' => false,
            'units' => 60,
            'below_market_units' => 5,
            'replaced_units' => 0,
            'subject_to_vote' => true,
            'approved' => true,
            'description' => 'Description two',
            'start_datetime' => now()->addDays(10),
            'end_datetime' => now()->addDays(10)->addHours(2),
            'more_info_url' => null,
            'remote_instructions' => 'Remote join info',
            'inperson_instructions' => 'In-person info',
            'comments_email' => 'comments@test.org',
        ]);

        $voteOne = HearingVote::create([
            'hearing_id' => $hearingOne->id,
            'vote_date' => now()->addDays(15)->toDateString(),
            'passed' => true,
            'notes' => null,
        ]);

        $voteTwo = HearingVote::create([
            'hearing_id' => $hearingTwo->id,
            'vote_date' => now()->addDays(20)->toDateString(),
            'passed' => false,
            'notes' => null,
        ]);

        CouncillorVote::create([
            'hearing_vote_id' => $voteOne->id,
            'councillor_id' => $councillorA->id,
            'vote' => 'for',
        ]);

        CouncillorVote::create([
            'hearing_vote_id' => $voteTwo->id,
            'councillor_id' => $councillorA->id,
            'vote' => 'against',
        ]);

        CouncillorVote::create([
            'hearing_vote_id' => $voteOne->id,
            'councillor_id' => $councillorB->id,
            'vote' => 'against',
        ]);

        CouncillorVote::create([
            'hearing_vote_id' => $voteTwo->id,
            'councillor_id' => $councillorB->id,
            'vote' => 'against',
        ]);

        $response = $this->get(route('regions.voting-embed', [
            'organization' => $organization->slug,
            'region' => $region,
        ]));

        $response->assertOk();
        $response->assertSee('Councillor Alpha');
        $response->assertSee('66.7%');
        $response->assertSee('120');
        $response->assertSee('60');
        $response->assertSee('Councillor Beta');
        $response->assertSee('0.0%');
        $response->assertSee('35');
    }
}
