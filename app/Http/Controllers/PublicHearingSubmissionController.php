<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicHearingRequest;
use App\Models\Councillor;
use App\Models\CouncillorVote;
use App\Models\Hearing;
use App\Models\HearingVote;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PublicHearingSubmissionController extends Controller
{
    public function create(Organization $organization): View
    {
        $regions = $organization->regions()->orderBy('name')->get();

        $councillorsByRegion = Councillor::whereIn('region_id', $regions->pluck('id'))
            ->orderBy('name')
            ->get()
            ->groupBy('region_id')
            ->map(function ($group) {
                return $group->map(function ($councillor) {
                    return [
                        'id' => $councillor->id,
                        'name' => $councillor->name,
                        'elected_start' => optional($councillor->elected_start)->format('Y-m-d'),
                        'elected_end' => optional($councillor->elected_end)->format('Y-m-d'),
                    ];
                })->values();
            })->toArray();

        return view('public.hearings.submit', [
            'organization' => $organization,
            'regions' => $regions,
            'councillorsByRegion' => $councillorsByRegion,
        ]);
    }

    public function store(Organization $organization, PublicHearingRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $region = $organization->regions()->findOrFail($validated['region_id']);

        $hearing = new Hearing();
        $hearing->organization_id = $organization->id;
        $hearing->region_id = $region->id;
        $hearing->type = $validated['type'];
        $hearing->title = $validated['title'] ?? null;
        $hearing->street_address = $validated['street_address'] ?? null;
        $hearing->postal_code = $validated['postal_code'] ?? null;
        $hearing->rental = array_key_exists('rental', $validated) ? (bool) $validated['rental'] : null;
        $hearing->units = $validated['units'] ?? null;
        if (!is_null($hearing->units)) {
            $hearing->units = (int) $hearing->units;
        }

        $belowMarket = $validated['below_market_units'] ?? null;
        $hearing->below_market_units = is_null($belowMarket) ? 0 : (int) $belowMarket;

        $hearing->replaced_units = $validated['replaced_units'] ?? null;
        if (!is_null($hearing->replaced_units)) {
            $hearing->replaced_units = (int) $hearing->replaced_units;
        }
        $hearing->subject_to_vote = (bool) ($validated['subject_to_vote'] ?? false);
        $hearing->approved = false;
        $hearing->description = $validated['description'] ?? null;
        $hearing->more_info_url = $validated['more_info_url'] ?? null;
        $hearing->remote_instructions = $validated['remote_instructions'] ?? null;
        $hearing->inperson_instructions = $validated['inperson_instructions'] ?? null;
        $hearing->comments_email = $validated['comments_email'] ?? null;

        $hearing->setDateTimeFromForm(
            $validated['start_date'],
            $validated['start_time'] ?? null,
            $validated['end_time'] ?? null
        );

        if ($hearing->type === 'development' && empty($hearing->title)) {
            $hearing->title = $hearing->street_address;
        }

        $hearing->save();

        $hearingDate = $hearing->start_datetime;
        $today = now()->startOfDay();
        $isPastOrToday = $hearingDate ? $hearingDate->copy()->startOfDay()->lte($today) : false;

        $voteCreated = false;

        if ($hearing->subject_to_vote && $isPastOrToday) {
            $voteDate = $request->input('vote_date');
            $votePassed = $request->input('passed');
            $voteNotes = $request->input('notes');

            $councillorSelections = collect($request->all())->filter(function ($value, $key) {
                return str_starts_with($key, 'vote_') && !empty($value);
            });

            $hasVoteData = $voteDate || !is_null($votePassed) || $voteNotes || $councillorSelections->isNotEmpty();

            if ($hasVoteData) {
                $hearingVote = HearingVote::create([
                    'hearing_id' => $hearing->id,
                    'vote_date' => $voteDate,
                    'passed' => is_null($votePassed) ? null : (bool) $votePassed,
                    'notes' => $voteNotes,
                ]);

                if ($councillorSelections->isNotEmpty()) {
                    $allowedCouncillorIds = Councillor::where('region_id', $hearing->region_id)
                        ->where('elected_start', '<=', $hearing->start_datetime)
                        ->where(function ($query) use ($hearing) {
                            $query->whereNull('elected_end')
                                ->orWhere('elected_end', '>=', $hearing->start_datetime);
                        })
                        ->pluck('id')
                        ->all();

                    foreach ($councillorSelections as $key => $value) {
                        $councillorId = (int) str_replace('vote_', '', $key);

                        if ($councillorId <= 0 || !in_array($councillorId, $allowedCouncillorIds, true)) {
                            continue;
                        }

                        if (!in_array($value, ['for', 'against', 'abstain', 'absent'], true)) {
                            continue;
                        }

                        CouncillorVote::create([
                            'hearing_vote_id' => $hearingVote->id,
                            'councillor_id' => $councillorId,
                            'vote' => $value,
                        ]);
                    }
                }

                $voteCreated = true;
            }
        }

        return redirect()->route('public.hearings.submit.thank-you', [
            'organization' => $organization->slug,
        ]);
    }

    public function thankYou(Organization $organization): View
    {
        return view('public.hearings.thank-you', [
            'organization' => $organization,
        ]);
    }

    private function ensureHearingBelongsToOrganization(Hearing $hearing, Organization $organization): void
    {
        if ($hearing->organization_id !== $organization->id) {
            abort(404);
        }
    }
}
