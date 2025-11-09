<?php

namespace App\Http\Controllers;

use App\Models\Councillor;
use App\Models\CouncillorVote;
use App\Models\Hearing;
use App\Models\HearingVote;
use App\Models\Organization;
use Illuminate\Http\Request;

class HearingVoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization)
    {
        $hearingsNeedingVotes = Hearing::where('subject_to_vote', true)
            ->whereDoesntHave('hearingVote')
            ->whereHas('region', fn ($query) => $query->where('organization_id', $organization->id))
            ->with(['region'])
            ->orderBy('start_datetime', 'desc')
            ->get();

        $pastVotes = HearingVote::whereHas('hearing.region', fn ($query) => $query->where('organization_id', $organization->id))
            ->with(['hearing', 'hearing.region', 'councillorVotes.councillor'])
            ->orderBy('vote_date', 'desc')
            ->get();
        
        return view('hearing-votes.index', compact('hearingsNeedingVotes', 'pastVotes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization, Request $request)
    {
        $hearingId = $request->query('hearing_id');
        
        if (!$hearingId) {
            return redirect($this->orgRoute('hearing-votes.index'))->with('error', 'No hearing specified.');
        }
        
        $hearing = Hearing::with(['region'])->findOrFail($hearingId);
        
        // Verify access
        $this->ensureHearingBelongsToOrganization($hearing, $organization);
        
        // Check if vote already exists
        if ($hearing->hearingVote) {
            return redirect($this->orgRoute('hearing-votes.edit', $hearing->hearingVote))
                ->with('info', 'This hearing already has a vote. You can edit it here.');
        }
        
        // Get councillors for the hearing's region
        $councillors = Councillor::where('region_id', $hearing->region_id)
            ->where(function($query) use ($hearing) {
                // Only include councillors who were serving at the time of the hearing
                $query->where('elected_start', '<=', $hearing->start_datetime)
                    ->where(function($q) use ($hearing) {
                        $q->whereNull('elected_end')
                          ->orWhere('elected_end', '>=', $hearing->start_datetime);
                    });
            })
            ->orderBy('name')
            ->get();
        
        return view('hearing-votes.create', compact('hearing', 'councillors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Organization $organization, Request $request)
    {
        $validated = $request->validate([
            'hearing_id' => 'required|exists:hearings,id',
            'vote_date' => 'required|date',
            'passed' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);
        
        $hearing = Hearing::findOrFail($validated['hearing_id']);
        
        // Verify access
        $this->ensureHearingBelongsToOrganization($hearing, $organization);
        
        // Create the hearing vote
        $hearingVote = HearingVote::create([
            'hearing_id' => $validated['hearing_id'],
            'vote_date' => $validated['vote_date'],
            'passed' => $validated['passed'],
            'notes' => $validated['notes'],
        ]);
        
        // Process individual councillor votes from radio buttons
        foreach ($request->all() as $key => $value) {
            // Look for fields that start with "vote_"
            if (strpos($key, 'vote_') === 0 && !empty($value)) {
                $councillorId = str_replace('vote_', '', $key);
                
                // Verify this is a valid councillor ID
                if (is_numeric($councillorId)) {
                    CouncillorVote::create([
                        'hearing_vote_id' => $hearingVote->id,
                        'councillor_id' => $councillorId,
                        'vote' => $value, // 'for' or 'against'
                    ]);
                }
            }
        }
        
        return redirect($this->orgRoute('hearing-votes.index'))->with('success', 'Vote recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, HearingVote $hearingVote)
    {
        // Verify access
        $this->ensureHearingVoteBelongsToOrganization($hearingVote, $organization);
        
        $hearingVote->load(['hearing', 'hearing.region', 'councillorVotes.councillor']);
        
        $tallies = $hearingVote->getTallies();
        
        return view('hearing-votes.show', compact('hearingVote', 'tallies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, HearingVote $hearingVote)
    {
        // Verify access
        $this->ensureHearingVoteBelongsToOrganization($hearingVote, $organization);
        
        $hearingVote->load(['hearing', 'hearing.region', 'councillorVotes']);
        
        // Get councillors for the hearing's region
        $councillors = Councillor::where('region_id', $hearingVote->hearing->region_id)
            ->where(function($query) use ($hearingVote) {
                $query->where('elected_start', '<=', $hearingVote->hearing->start_datetime)
                    ->where(function($q) use ($hearingVote) {
                        $q->whereNull('elected_end')
                          ->orWhere('elected_end', '>=', $hearingVote->hearing->start_datetime);
                    });
            })
            ->orderBy('name')
            ->get();
        
        return view('hearing-votes.edit', compact('hearingVote', 'councillors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Organization $organization, Request $request, HearingVote $hearingVote)
    {
        // Verify access
        $this->ensureHearingVoteBelongsToOrganization($hearingVote, $organization);
        
        $validated = $request->validate([
            'vote_date' => 'required|date',
            'passed' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);
        
        // Update the hearing vote
        $hearingVote->update([
            'vote_date' => $validated['vote_date'],
            'passed' => $validated['passed'],
            'notes' => $validated['notes'],
        ]);
        
        // Delete existing councillor votes
        $hearingVote->councillorVotes()->delete();
        
        // Process individual councillor votes from radio buttons
        foreach ($request->all() as $key => $value) {
            // Look for fields that start with "vote_"
            if (strpos($key, 'vote_') === 0 && !empty($value)) {
                $councillorId = str_replace('vote_', '', $key);
                
                // Verify this is a valid councillor ID
                if (is_numeric($councillorId)) {
                    CouncillorVote::create([
                        'hearing_vote_id' => $hearingVote->id,
                        'councillor_id' => $councillorId,
                        'vote' => $value, // 'for' or 'against'
                    ]);
                }
            }
        }
        
        return redirect($this->orgRoute('hearing-votes.index'))->with('success', 'Vote updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, HearingVote $hearingVote)
    {
        // Verify access
        $this->ensureHearingVoteBelongsToOrganization($hearingVote, $organization);
        
        $hearingVote->delete();
        
        return redirect($this->orgRoute('hearing-votes.index'))->with('success', 'Vote deleted successfully!');
    }

    private function ensureHearingBelongsToOrganization(Hearing $hearing, Organization $organization): void
    {
        if ($hearing->region->organization_id !== $organization->id) {
            abort(404);
        }
    }

    private function ensureHearingVoteBelongsToOrganization(HearingVote $hearingVote, Organization $organization): void
    {
        $this->ensureHearingBelongsToOrganization($hearingVote->hearing, $organization);
    }
}
