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
        
        // Get upcoming hearings in the user's monitored regions
        $upcomingHearings = collect();
        if ($monitoredRegions->count() > 0) {
            $regionIds = $monitoredRegions->pluck('id');
            $upcomingHearings = Hearing::whereIn('region_id', $regionIds)
                ->where('start_date', '>=', now())
                ->orderBy('start_date', 'asc')
                ->with(['region', 'organization'])
                ->limit(10)
                ->get();
        }
        
        return view('user.dashboard', compact('user', 'monitoredRegions', 'upcomingHearings'));
    }
}
