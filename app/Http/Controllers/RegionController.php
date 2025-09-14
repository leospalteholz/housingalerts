<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->is_superuser) {
            // Superusers can see all regions across organizations
            $regions = \App\Models\Region::with('organization')->get();
        } else {
            // All authenticated users can see regions within their organization
            $regions = \App\Models\Region::where('organization_id', auth()->user()->organization_id)->get();
        }
        
        // For regular users, also get their monitored regions to show which ones they're following
        if (!auth()->user()->is_admin) {
            $monitoredRegionIds = auth()->user()->regions()->pluck('regions.id');
            $regions = $regions->map(function ($region) use ($monitoredRegionIds) {
                $region->is_monitored = $monitoredRegionIds->contains($region->id);
                return $region;
            });
        }
        
        return view('regions.index', compact('regions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('regions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'comments_email' => 'nullable|email|max:255',
            'remote_instructions' => 'nullable|string',
            'inperson_instructions' => 'nullable|string',
        ]);

        $region = new \App\Models\Region();
        $region->name = $validated['name'];
        $region->comments_email = $validated['comments_email'];
        $region->remote_instructions = $validated['remote_instructions'];
        $region->inperson_instructions = $validated['inperson_instructions'];
        $region->organization_id = auth()->user()->organization_id;
        $region->save();

        return redirect()->route('regions.index')->with('success', 'Region created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $region = \App\Models\Region::with(['organization', 'hearings'])->findOrFail($id);
        
        // Check access permissions
        if (auth()->user()->is_superuser) {
            // Superusers can view any region
        } else {
            // All other users can only view regions in their organization
            if ($region->organization_id !== auth()->user()->organization_id) {
                abort(403, 'You do not have permission to view this region.');
            }
        }
        
        // For regular users, check if they're monitoring this region
        $isMonitored = false;
        if (!auth()->user()->is_admin) {
            $isMonitored = auth()->user()->regions()->where('regions.id', $region->id)->exists();
        }
        
        return view('regions.show', compact('region', 'isMonitored'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $region = \App\Models\Region::findOrFail($id);
        return view('regions.edit', compact('region'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'comments_email' => 'nullable|email|max:255',
            'remote_instructions' => 'nullable|string',
            'inperson_instructions' => 'nullable|string',
        ]);

        $region = \App\Models\Region::findOrFail($id);
        $region->fill($validated);
        $region->save();

        return redirect()->route('regions.index')->with('success', 'Region updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $region = \App\Models\Region::findOrFail($id);
        
        // Check if region has any hearings
        if ($region->hearings()->count() > 0) {
            return redirect()->route('regions.index')
                ->withErrors(['error' => 'Cannot delete region "' . $region->name . '" because it contains hearings. Please move or delete the hearings first.']);
        }
        
        $region->delete();
        return redirect()->route('regions.index')->with('success', 'Region deleted successfully!');
    }

    /**
     * Subscribe user to a region
     */
    public function subscribe($id)
    {
        $region = \App\Models\Region::findOrFail($id);
        $user = auth()->user();
        
        // Check if user can access this region (same organization)
        if (!$user->is_superuser && $region->organization_id !== $user->organization_id) {
            return response()->json(['error' => 'You can only subscribe to regions in your organization.'], 403);
        }
        
        // Add user to region if not already subscribed
        if (!$user->regions()->where('regions.id', $region->id)->exists()) {
            $user->regions()->attach($region->id);
        }
        
        return response()->json(['success' => true, 'message' => 'Successfully subscribed to ' . $region->name]);
    }

    /**
     * Unsubscribe user from a region
     */
    public function unsubscribe($id)
    {
        $region = \App\Models\Region::findOrFail($id);
        $user = auth()->user();
        
        // Remove user from region
        $user->regions()->detach($region->id);
        
        return response()->json(['success' => true, 'message' => 'Successfully unsubscribed from ' . $region->name]);
    }
}
