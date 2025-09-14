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
            $allHearings = \App\Models\Hearing::with(['organization', 'region'])->get();
        } else {
            // Regular admins can only see hearings within their organization
            $allHearings = \App\Models\Hearing::with(['organization', 'region'])
                ->where('organization_id', auth()->user()->organization_id)
                ->get();
        }
        
        // Split hearings into upcoming and past based on start_date
        $today = now()->startOfDay();
        $upcomingHearings = $allHearings->filter(function ($hearing) use ($today) {
            return $hearing->start_date && \Carbon\Carbon::parse($hearing->start_date)->gte($today);
        })->sortBy('start_date');
        
        $pastHearings = $allHearings->filter(function ($hearing) use ($today) {
            return $hearing->start_date && \Carbon\Carbon::parse($hearing->start_date)->lt($today);
        })->sortByDesc('start_date');
        
        return view('hearings.index', compact('upcomingHearings', 'pastHearings'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get regions based on user role
        if (auth()->user()->is_superuser) {
            // Superusers can see all regions across organizations
            $regions = \App\Models\Region::with('organization')->orderBy('name')->get();
        } else {
            // Regular admins can only see regions within their organization
            $regions = \App\Models\Region::where('organization_id', auth()->user()->organization_id)
                ->orderBy('name')
                ->get();
        }
        
        return view('hearings.create', compact('regions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Base validation rules
        $rules = [
            'type' => 'required|in:development,policy',
            'description' => 'required|string',
            'remote_instructions' => 'required|string',
            'inperson_instructions' => 'required|string',
            'comments_email' => 'required|email|max:255',
            'start_date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'organization_id' => 'nullable|exists:organizations,id',
            'region_id' => 'nullable|exists:regions,id',
            'image_url' => 'nullable|string',
            'more_info_url' => 'nullable|url',
        ];

        // Add conditional validation based on hearing type
        if ($request->type === 'development') {
            $rules = array_merge($rules, [
                'street_address' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'rental' => 'required|boolean',
                'units' => 'required|integer|min:1',
                'title' => 'nullable|string|max:255',
            ]);
        } else if ($request->type === 'policy') {
            $rules = array_merge($rules, [
                'title' => 'required|string|max:255',
                'street_address' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'rental' => 'nullable|boolean',
                'units' => 'nullable|integer|min:1',
            ]);
        }

        $validated = $request->validate($rules);

        // Create a new hearing
        $hearing = new \App\Models\Hearing($validated);
        
        // Auto-generate title for development hearings if not provided
        if ($hearing->type === 'development' && empty($hearing->title)) {
            $hearing->title = "Hearing for {$hearing->street_address}";
        }
        
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
        
        // Get regions based on user role
        if (auth()->user()->is_superuser) {
            // Superusers can see all regions across organizations
            $regions = \App\Models\Region::with('organization')->orderBy('name')->get();
        } else {
            // Regular admins can only see regions within their organization
            $regions = \App\Models\Region::where('organization_id', auth()->user()->organization_id)
                ->orderBy('name')
                ->get();
        }
        
        return view('hearings.edit', compact('hearing', 'regions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $hearing = \App\Models\Hearing::findOrFail($id);
        
        // Base validation rules
        $rules = [
            'type' => 'required|in:development,policy',
            'description' => 'required|string',
            'remote_instructions' => 'required|string',
            'inperson_instructions' => 'required|string',
            'comments_email' => 'required|email|max:255',
            'start_date' => 'required|date',
            'start_time' => 'nullable',
            'end_time' => 'nullable',
            'organization_id' => 'nullable|exists:organizations,id',
            'region_id' => 'nullable|exists:regions,id',
            'image_url' => 'nullable|string',
            'more_info_url' => 'nullable|url',
        ];

        // Add conditional validation based on hearing type
        if ($request->type === 'development') {
            $rules = array_merge($rules, [
                'street_address' => 'required|string|max:255',
                'postal_code' => 'required|string|max:20',
                'rental' => 'required|boolean',
                'units' => 'required|integer|min:1',
                'title' => 'nullable|string|max:255',
            ]);
        } else if ($request->type === 'policy') {
            $rules = array_merge($rules, [
                'title' => 'required|string|max:255',
                'street_address' => 'nullable|string|max:255',
                'postal_code' => 'nullable|string|max:20',
                'rental' => 'nullable|boolean',
                'units' => 'nullable|integer|min:1',
            ]);
        }

        $validated = $request->validate($rules);

        $hearing->fill($validated);
        
        // Auto-generate title for development hearings if not provided
        if ($hearing->type === 'development' && empty($hearing->title)) {
            $hearing->title = "Hearing for {$hearing->street_address}";
        }
        
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
