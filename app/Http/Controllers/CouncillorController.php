<?php

namespace App\Http\Controllers;

use App\Models\Councillor;
use App\Models\Organization;
use App\Models\Region;
use Illuminate\Http\Request;

class CouncillorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Organization $organization)
    {
        $regions = Region::with([
                'organization',
                'councillors' => function ($query) {
                    $query->orderBy('name');
                },
            ])
            ->where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();

        $regionGroups = $regions->map(function (Region $region) {
            $current = $region->councillors->filter(fn ($councillor) => $councillor->isCurrentlyServing());
            $past = $region->councillors->filter(fn ($councillor) => !$councillor->isCurrentlyServing());

            return [
                'region' => $region,
                'current' => $current,
                'past' => $past,
            ];
        })->values();

        $currentCount = $regionGroups->sum(fn ($group) => $group['current']->count());
        $pastCount = $regionGroups->sum(fn ($group) => $group['past']->count());

        return view('councillors.index', compact('regionGroups', 'currentCount', 'pastCount'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Organization $organization)
    {
        $regions = Region::with('organization')
            ->where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();
        
        return view('councillors.create', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Organization $organization, Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:255',
            'elected_start' => 'required|date',
            'elected_end' => 'nullable|date|after:elected_start',
        ]);
        
        $region = Region::where('organization_id', $organization->id)
            ->findOrFail($validated['region_id']);
        
        Councillor::create($validated);
        
        return redirect($this->orgRoute('councillors.index'))->with('success', 'Councillor created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Organization $organization, Councillor $councillor)
    {
        $this->ensureCouncillorBelongsToOrganization($councillor, $organization);
        
        $councillor->load(['region', 'councillorVotes.hearingVote.hearing']);
        
        // Get voting statistics
        $votingStats = $councillor->getVotingStats();
        
        return view('councillors.show', compact('councillor', 'votingStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Organization $organization, Councillor $councillor)
    {
        $this->ensureCouncillorBelongsToOrganization($councillor, $organization);
        
        $regions = Region::with('organization')
            ->where('organization_id', $organization->id)
            ->orderBy('name')
            ->get();
        
        return view('councillors.edit', compact('councillor', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Organization $organization, Request $request, Councillor $councillor)
    {
        $this->ensureCouncillorBelongsToOrganization($councillor, $organization);
        
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:255',
            'elected_start' => 'required|date',
            'elected_end' => 'nullable|date|after:elected_start',
        ]);
        
        $region = Region::where('organization_id', $organization->id)
            ->findOrFail($validated['region_id']);
        
        $councillor->update($validated);
        
        return redirect($this->orgRoute('councillors.index'))->with('success', 'Councillor updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Organization $organization, Councillor $councillor)
    {
        $this->ensureCouncillorBelongsToOrganization($councillor, $organization);
        
        $councillor->delete();
        
        return redirect($this->orgRoute('councillors.index'))->with('success', 'Councillor deleted successfully!');
    }

    private function ensureCouncillorBelongsToOrganization(Councillor $councillor, Organization $organization): void
    {
        if ($councillor->region->organization_id !== $organization->id) {
            abort(404);
        }
    }
}
