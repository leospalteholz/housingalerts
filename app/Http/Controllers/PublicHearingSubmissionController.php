<?php

namespace App\Http\Controllers;

use App\Http\Requests\PublicHearingRequest;
use App\Http\Requests\PublicHearingVoteRequest;
use App\Models\Councillor;
use App\Models\CouncillorVote;
use App\Models\Hearing;
use App\Models\HearingVote;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class PublicHearingSubmissionController extends Controller
{
    public function create(Organization $organization): View
    {
        $regions = $organization->regions()->orderBy('name')->get();

        return view('public.hearings.submit', [
            'organization' => $organization,
            'regions' => $regions,
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
        $hearing->below_market_units = $validated['below_market_units'] ?? 0;
        $hearing->replaced_units = $validated['replaced_units'] ?? null;
        $hearing->subject_to_vote = (bool) ($validated['subject_to_vote'] ?? false);
        $hearing->approved = false;
        $hearing->description = $validated['description'];
        $hearing->more_info_url = $validated['more_info_url'] ?? null;
        $hearing->remote_instructions = $validated['remote_instructions'];
        $hearing->inperson_instructions = $validated['inperson_instructions'];
        $hearing->comments_email = $validated['comments_email'];

        $hearing->setDateTimeFromForm($validated['start_date'], $validated['start_time'], $validated['end_time']);

        if ($hearing->type === 'development' && empty($hearing->title)) {
            $hearing->title = $hearing->street_address;
        }

        $hearing->save();

        if (!$hearing->subject_to_vote) {
            return redirect()->route('public.hearings.submit.thank-you', [
                'organization' => $organization->slug,
            ])->with('status', 'Thank you! Your hearing has been submitted and will be reviewed.');
        }

        $signedUrl = URL::temporarySignedRoute(
            'public.hearings.submit.vote',
            now()->addMinutes(60),
            [
                'organization' => $organization->slug,
                'hearing' => $hearing->id,
            ]
        );

        return redirect($signedUrl);
    }

    public function createVote(Organization $organization, Hearing $hearing): View|RedirectResponse
    {
        $this->ensureHearingBelongsToOrganization($hearing, $organization);

        if (!$hearing->subject_to_vote) {
            abort(404);
        }

        if ($hearing->hearingVote) {
            return redirect()->route('public.hearings.submit.thank-you', [
                'organization' => $organization->slug,
            ])->with('status', 'Thanks! We already have vote information for this hearing.');
        }

        $councillors = Councillor::where('region_id', $hearing->region_id)
            ->where(function ($query) use ($hearing) {
                $query->where('elected_start', '<=', $hearing->start_datetime)
                    ->where(function ($q) use ($hearing) {
                        $q->whereNull('elected_end')
                            ->orWhere('elected_end', '>=', $hearing->start_datetime);
                    });
            })
            ->orderBy('name')
            ->get();

        return view('public.hearings.vote', [
            'organization' => $organization,
            'hearing' => $hearing,
            'councillors' => $councillors,
        ]);
    }

    public function storeVote(Organization $organization, Hearing $hearing, PublicHearingVoteRequest $request): RedirectResponse
    {
        $this->ensureHearingBelongsToOrganization($hearing, $organization);

        if (!$hearing->subject_to_vote) {
            abort(404);
        }

        if ($hearing->hearingVote) {
            return redirect()->route('public.hearings.submit.thank-you', [
                'organization' => $organization->slug,
            ])->with('status', 'Thanks! We already have vote information for this hearing.');
        }

        $validated = $request->validated();

        $allowedCouncillorIds = Councillor::where('region_id', $hearing->region_id)
            ->pluck('id')
            ->all();

        $hearingVote = HearingVote::create([
            'hearing_id' => $hearing->id,
            'vote_date' => $validated['vote_date'],
            'passed' => (bool) $validated['passed'],
            'notes' => $validated['notes'] ?? null,
        ]);

        foreach ($request->all() as $key => $value) {
            if (str_starts_with($key, 'vote_') && !empty($value)) {
                $councillorId = (int) str_replace('vote_', '', $key);

                if ($councillorId > 0 && in_array($councillorId, $allowedCouncillorIds, true)) {
                    CouncillorVote::create([
                        'hearing_vote_id' => $hearingVote->id,
                        'councillor_id' => $councillorId,
                        'vote' => $value,
                    ]);
                }
            }
        }

        return redirect()->route('public.hearings.submit.thank-you', [
            'organization' => $organization->slug,
        ])->with('status', 'Thank you! Your hearing details have been submitted for review.');
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
