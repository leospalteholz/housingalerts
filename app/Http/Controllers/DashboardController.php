<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Region;
use App\Models\Hearing;
use App\Models\Organization;

class DashboardController extends Controller
{
    public function index()
    {
        // For superusers, show system-wide statistics
        if (auth()->user()->is_superuser) {
            $organizations = Organization::withCount(['users', 'regions', 'hearings'])->get();
            
            $stats = [
                'organizations' => $organizations->count(),
                'totalUsers' => $organizations->sum('users_count'),
                'totalRegions' => $organizations->sum('regions_count'),
                'totalHearings' => $organizations->sum('hearings_count'),
            ];
            
            return view('dashboard', compact('organizations', 'stats'));
        }
        // For admin users, show statistics for their organization
        else if (auth()->user()->is_admin) {
            $orgId = auth()->user()->organization_id;
            
            $stats = [
                'totalUsers' => User::where('organization_id', $orgId)->count(),
                'totalRegions' => Region::where('organization_id', $orgId)->count(),
                'totalHearings' => Hearing::where('organization_id', $orgId)->count(),
            ];
            
            return view('dashboard', compact('stats'));
        }
        
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
        
        return view('dashboard', compact('user', 'monitoredRegions', 'upcomingHearings'));
    }
}
