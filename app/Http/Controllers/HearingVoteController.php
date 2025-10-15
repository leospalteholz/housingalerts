<?php

namespace App\Http\Controllers;

use App\Models\HearingVote;
use App\Models\Hearing;
use App\Models\Councillor;
use App\Models\CouncillorVote;
use Illuminate\Http\Request;

class HearingVoteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get hearings based on user role
        if (auth()->user()->is_superuser) {
            // Hearings subject to vote without votes
            $hearingsNeedingVotes = Hearing::where('subject_to_vote', true)
                ->whereDoesntHave('hearingVote')
                ->with(['region', 'region.organization'])
                ->orderBy('start_datetime', 'desc')
                ->get();
            
            // Past votes
            $pastVotes = HearingVote::with(['hearing', 'hearing.region', 'hearing.region.organization', 'councillorVotes.councillor'])
                ->orderBy('vote_date', 'desc')
                ->get();
        } else {
            // Regular admins only see hearings in their organization
            $hearingsNeedingVotes = Hearing::where('subject_to_vote', true)
                ->whereDoesntHave('hearingVote')
                ->whereHas('region', function($query) {
                    $query->where('organization_id', auth()->user()->organization_id);
                })
                ->with(['region'])
                ->orderBy('start_datetime', 'desc')
                ->get();
            
            $pastVotes = HearingVote::whereHas('hearing.region', function($query) {
                    $query->where('organization_id', auth()->user()->organization_id);
                })
                ->with(['hearing', 'hearing.region', 'councillorVotes.councillor'])
                ->orderBy('vote_date', 'desc')
                ->get();
        }
        
        return view('hearing-votes.index', compact('hearingsNeedingVotes', 'pastVotes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $hearingId = $request->query('hearing_id');
        
        if (!$hearingId) {
            return redirect()->route('hearing-votes.index')->with('error', 'No hearing specified.');
        }
        
        $hearing = Hearing::with(['region'])->findOrFail($hearingId);
        
        // Verify access
        if (!auth()->user()->is_superuser && $hearing->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to create votes for this hearing.');
        }
        
        // Check if vote already exists
        if ($hearing->hearingVote) {
            return redirect()->route('hearing-votes.edit', $hearing->hearingVote)
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
    public function store(Request $request)
    {
        $validated = $request->validate([
            'hearing_id' => 'required|exists:hearings,id',
            'vote_date' => 'required|date',
            'passed' => 'required|boolean',
            'notes' => 'nullable|string',
        ]);
        
        $hearing = Hearing::findOrFail($validated['hearing_id']);
        
        // Verify access
        if (!auth()->user()->is_superuser && $hearing->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to create votes for this hearing.');
        }
        
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
        
        return redirect()->route('hearing-votes.index')->with('success', 'Vote recorded successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(HearingVote $hearingVote)
    {
        // Verify access
        if (!auth()->user()->is_superuser && $hearingVote->hearing->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to view this vote.');
        }
        
        $hearingVote->load(['hearing', 'hearing.region', 'councillorVotes.councillor']);
        
        $tallies = $hearingVote->getTallies();
        
        return view('hearing-votes.show', compact('hearingVote', 'tallies'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(HearingVote $hearingVote)
    {
        // Verify access
        if (!auth()->user()->is_superuser && $hearingVote->hearing->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to edit this vote.');
        }
        
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
    public function update(Request $request, HearingVote $hearingVote)
    {
        // Verify access
        if (!auth()->user()->is_superuser && $hearingVote->hearing->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to update this vote.');
        }
        
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
        
        return redirect()->route('hearing-votes.index')->with('success', 'Vote updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(HearingVote $hearingVote)
    {
        // Verify access
        if (!auth()->user()->is_superuser && $hearingVote->hearing->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to delete this vote.');
        }
        
        $hearingVote->delete();
        
        return redirect()->route('hearing-votes.index')->with('success', 'Vote deleted successfully!');
    }
}
