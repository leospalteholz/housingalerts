<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Region;
use App\Models\Hearing;

class UserDashboardController extends Controller
{
    public function index()
    {
        // For regular users, show their monitored regions and upcoming hearings
        $user = auth()->user();
        $monitoredRegions = $user->regions()->with('organization')->get();
        $monitoredRegionIds = $monitoredRegions->pluck('id');
        
        // Get all regions in the user's organization with monitoring status
        $allRegions = Region::where('organization_id', $user->organization_id)
            ->with('organization')
            ->get()
            ->map(function ($region) use ($monitoredRegionIds) {
                $region->is_monitored = $monitoredRegionIds->contains($region->id);
                return $region;
            });
        
        // Get upcoming hearings in the user's monitored regions
        $upcomingHearings = collect();
        if ($monitoredRegions->count() > 0) {
            $regionIds = $monitoredRegions->pluck('id');
            $upcomingHearings = Hearing::whereIn('region_id', $regionIds)
                ->where('start_datetime', '>=', now())
                ->orderBy('start_datetime', 'asc')
                ->with(['region', 'organization'])
                ->get();
        }
        
        return view('user.dashboard', compact('user', 'monitoredRegions', 'upcomingHearings', 'allRegions'));
    }
}
