<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HearingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (auth()->user()->is_superuser) {
            // Superusers can see all hearings across organizations
            $hearings = \App\Models\Hearing::with(['organization', 'region'])->get();
        } else {
            // Regular admins can only see hearings within their organization
            $hearings = \App\Models\Hearing::where('organization_id', auth()->user()->organization_id)->get();
        }
        return view('hearings.index', compact('hearings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('hearings.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'start_date' => 'required|date',
            'start_time' => 'nullable',
            'region_id' => 'required|exists:regions,id',
            'image_url' => 'nullable|string',
            'more_info_url' => 'nullable|url',
        ]);

        // Create a new hearing
        $hearing = new \App\Models\Hearing($validated);
        
        // Force organization_id to match the user's organization unless superuser
        if (!auth()->user()->is_superuser && $request->has('organization_id')) {
            $hearing->organization_id = auth()->user()->organization_id;
        } else if (auth()->user()->is_superuser && $request->has('organization_id')) {
            $hearing->organization_id = $request->organization_id;
        } else {
            $hearing->organization_id = auth()->user()->organization_id;
        }
        
        $hearing->save();

        return redirect()->route('hearings.index')->with('success', 'Hearing created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        return view('hearings.show', compact('hearing'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        return view('hearings.edit', compact('hearing'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'start_date' => 'required|date',
            'start_time' => 'nullable',
            'organization_id' => 'nullable|exists:organizations,id',
            'region_id' => 'nullable|exists:regions,id',
            'image_url' => 'nullable|string',
            'more_info_url' => 'nullable|url',
        ]);

        $hearing = \App\Models\Hearing::findOrFail($id);
        $hearing->fill($validated);
        $hearing->save();

        return redirect()->route('hearings.index')->with('success', 'Hearing updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        $hearing->delete();
        return redirect()->route('hearings.index')->with('success', 'Hearing deleted successfully!');
    }
}
