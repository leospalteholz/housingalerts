<?php

namespace App\Http\Controllers;

use App\Models\Councillor;
use App\Models\Region;
use Illuminate\Http\Request;

class CouncillorController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get regions based on user role
        if (auth()->user()->is_superuser) {
            $councillors = Councillor::with(['region', 'region.organization'])
                ->orderBy('region_id')
                ->orderBy('name')
                ->get();
        } else {
            // Regular admins can only see councillors in their organization's regions
            $councillors = Councillor::with(['region'])
                ->whereHas('region', function($query) {
                    $query->where('organization_id', auth()->user()->organization_id);
                })
                ->orderBy('region_id')
                ->orderBy('name')
                ->get();
        }
        
        // Separate current and past councillors
        $currentCouncillors = $councillors->filter(function($councillor) {
            return $councillor->isCurrentlyServing();
        });
        
        $pastCouncillors = $councillors->filter(function($councillor) {
            return !$councillor->isCurrentlyServing();
        });
        
        return view('councillors.index', compact('currentCouncillors', 'pastCouncillors'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get regions based on user role
        if (auth()->user()->is_superuser) {
            $regions = Region::with('organization')->orderBy('name')->get();
        } else {
            $regions = Region::where('organization_id', auth()->user()->organization_id)
                ->orderBy('name')
                ->get();
        }
        
        return view('councillors.create', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:255',
            'elected_start' => 'required|date',
            'elected_end' => 'nullable|date|after:elected_start',
        ]);
        
        // Verify the region belongs to the admin's organization (if not superuser)
        if (!auth()->user()->is_superuser) {
            $region = Region::findOrFail($validated['region_id']);
            if ($region->organization_id !== auth()->user()->organization_id) {
                abort(403, 'You can only create councillors for regions in your organization.');
            }
        }
        
        Councillor::create($validated);
        
        return redirect()->route('councillors.index')->with('success', 'Councillor created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Councillor $councillor)
    {
        // Verify access
        if (!auth()->user()->is_superuser && $councillor->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to view this councillor.');
        }
        
        $councillor->load(['region', 'councillorVotes.hearingVote.hearing']);
        
        // Get voting statistics
        $votingStats = $councillor->getVotingStats();
        
        return view('councillors.show', compact('councillor', 'votingStats'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Councillor $councillor)
    {
        // Verify access
        if (!auth()->user()->is_superuser && $councillor->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to edit this councillor.');
        }
        
        // Get regions based on user role
        if (auth()->user()->is_superuser) {
            $regions = Region::with('organization')->orderBy('name')->get();
        } else {
            $regions = Region::where('organization_id', auth()->user()->organization_id)
                ->orderBy('name')
                ->get();
        }
        
        return view('councillors.edit', compact('councillor', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Councillor $councillor)
    {
        // Verify access
        if (!auth()->user()->is_superuser && $councillor->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to update this councillor.');
        }
        
        $validated = $request->validate([
            'region_id' => 'required|exists:regions,id',
            'name' => 'required|string|max:255',
            'elected_start' => 'required|date',
            'elected_end' => 'nullable|date|after:elected_start',
        ]);
        
        // Verify the region belongs to the admin's organization (if not superuser)
        if (!auth()->user()->is_superuser) {
            $region = Region::findOrFail($validated['region_id']);
            if ($region->organization_id !== auth()->user()->organization_id) {
                abort(403, 'You can only assign councillors to regions in your organization.');
            }
        }
        
        $councillor->update($validated);
        
        return redirect()->route('councillors.index')->with('success', 'Councillor updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Councillor $councillor)
    {
        // Verify access
        if (!auth()->user()->is_superuser && $councillor->region->organization_id !== auth()->user()->organization_id) {
            abort(403, 'You do not have permission to delete this councillor.');
        }
        
        $councillor->delete();
        
        return redirect()->route('councillors.index')->with('success', 'Councillor deleted successfully!');
    }
}
