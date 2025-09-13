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
            // Regular admins can only see regions within their organization
            $regions = \App\Models\Region::where('organization_id', auth()->user()->organization_id)->get();
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
        ]);

        $region = new \App\Models\Region();
        $region->name = $validated['name'];
        $region->save();

        return redirect()->route('regions.index')->with('success', 'Region created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        ]);

        $region = \App\Models\Region::findOrFail($id);
        $region->name = $validated['name'];
        $region->save();

        return redirect()->route('regions.index')->with('success', 'Region updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $region = \App\Models\Region::findOrFail($id);
        $region->delete();
        return redirect()->route('regions.index')->with('success', 'Region deleted successfully!');
    }
}
