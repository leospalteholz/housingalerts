<?php

namespace App\Http\Controllers;

use App\Models\Region;
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
            $regions = Region::with('organization')->get();
        } else {
            // All authenticated users can see regions within their organization
            $regions = Region::where('organization_id', auth()->user()->organization_id)->get();
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

        $region = new Region();
        $region->name = $validated['name'];
        $region->comments_email = $validated['comments_email'] ?? null;
        $region->remote_instructions = $validated['remote_instructions'] ?? null;
        $region->inperson_instructions = $validated['inperson_instructions'] ?? null;
        $region->organization_id = auth()->user()->organization_id;
        $region->save();

        return redirect()->route('regions.index')->with('success', 'Region created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Region $region)
    {
        $region->load(['organization', 'hearings']);
        
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
     * Render an embeddable councillor vote breakdown for a region.
     */
    public function votingEmbed(Region $region)
    {
        $region->load('organization');

        $councillors = $region->councillors()->orderBy('name')->get();

        $votesByCouncillor = \App\Models\CouncillorVote::with(['hearingVote.hearing' => function ($query) use ($region) {
            $query->where('region_id', $region->id)
                ->where('approved', true);
        }])
            ->whereIn('councillor_id', $councillors->pluck('id'))
            ->whereHas('hearingVote.hearing', function ($query) use ($region) {
                $query->where('region_id', $region->id)
                    ->where('approved', true);
            })
            ->get()
            ->groupBy('councillor_id');

        $columns = [
            'Councillor Name',
            '% Support',
            'Homes Supported',
            'Homes Opposed',
            'Rentals Opposed',
            'Below Market Opposed',
        ];

        $rows = $councillors->map(function ($councillor) use ($votesByCouncillor) {
            $votes = $votesByCouncillor->get($councillor->id, collect());

            $homesSupported = 0;
            $homesOpposed = 0;
            $rentalsOpposed = 0;
            $belowMarketOpposed = 0;
            $participatedUnits = 0;

            foreach ($votes as $vote) {
                $hearing = optional($vote->hearingVote)->hearing;

                if (!$hearing) {
                    continue;
                }

                $units = (int) ($hearing->units ?? 0);
                $belowMarketUnits = (int) ($hearing->below_market_units ?? 0);
                $isRental = (bool) $hearing->rental;

                if ($vote->vote === 'for') {
                    $homesSupported += $units;
                    $participatedUnits += $units;
                } elseif ($vote->vote === 'against') {
                    $homesOpposed += $units;
                    $participatedUnits += $units;

                    if ($isRental) {
                        $rentalsOpposed += $units;
                    }

                    $belowMarketOpposed += $belowMarketUnits;
                }
            }

            $supportPercent = $participatedUnits > 0
                ? number_format(round(($homesSupported / $participatedUnits) * 100, 1), 1)
                : null;

            return [
                $councillor->name,
                $supportPercent !== null ? $supportPercent . '%' : '—',
                number_format($homesSupported),
                number_format($homesOpposed),
                number_format($rentalsOpposed),
                number_format($belowMarketOpposed),
            ];
        })->values();

        return view('regions.embed', [
            'region' => $region,
            'columns' => $columns,
            'rows' => $rows,
            'recordCount' => $rows->count(),
            'generatedAt' => now()->format('Y-m-d H:i:s T'),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Region $region)
    {
        return view('regions.edit', compact('region'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Region $region)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'comments_email' => 'nullable|email|max:255',
            'remote_instructions' => 'nullable|string',
            'inperson_instructions' => 'nullable|string',
        ]);

        $region->fill($validated);
        $region->save();

        return redirect()->route('regions.index')->with('success', 'Region updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Region $region)
    {
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
    public function subscribe(Region $region)
    {
        try {
            \Log::info('Region subscription attempt:', [
                'region_id' => $region->id,
                'region_slug' => $region->slug,
                'user_id' => auth()->id(),
                'user_organization_id' => auth()->user()->organization_id
            ]);

            $user = auth()->user();
            
            \Log::info('Region found:', [
                'region_name' => $region->name,
                'region_organization_id' => $region->organization_id
            ]);
            
            // Check if user can access this region (same organization)
            if (!$user->is_superuser && $region->organization_id !== $user->organization_id) {
                \Log::warning('User tried to subscribe to region from different organization:', [
                    'user_org' => $user->organization_id,
                    'region_org' => $region->organization_id
                ]);
                return response()->json(['error' => 'You can only subscribe to regions in your organization.'], 403);
            }
            
            // Check if already subscribed
            $alreadySubscribed = $user->regions()->where('regions.id', $region->id)->exists();
            \Log::info('Subscription status check:', [
                'already_subscribed' => $alreadySubscribed
            ]);
            
            // Add user to region if not already subscribed
            if (!$alreadySubscribed) {
                $user->regions()->attach($region->id);
                \Log::info('User subscribed to region successfully');
            } else {
                \Log::info('User was already subscribed to region');
            }
            
            return response()->json(['success' => true, 'message' => 'Successfully subscribed to ' . $region->name]);
        } catch (\Exception $e) {
            \Log::error('Region subscription error:', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'An error occurred while subscribing: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Unsubscribe user from a region
     */
    public function unsubscribe(Region $region)
    {
        try {
            $user = auth()->user();
            
            // Remove user from region
            $user->regions()->detach($region->id);
            
            return response()->json(['success' => true, 'message' => 'Successfully unsubscribed from ' . $region->name]);
        } catch (\Exception $e) {
            \Log::error('Region unsubscription error: ' . $e->getMessage());
            return response()->json(['error' => 'An error occurred while unsubscribing: ' . $e->getMessage()], 500);
        }
    }
}
