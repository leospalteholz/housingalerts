<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Http\Request;

class SuperuserDashboardController extends Controller
{
    public function index()
    {
        // Only accessible by superusers
        $organizations = Organization::withCount(['users', 'regions', 'hearings'])->get();
        
        $stats = [
            'organizations' => $organizations->count(),
            'totalUsers' => $organizations->sum('users_count'),
            'totalRegions' => $organizations->sum('regions_count'),
            'totalHearings' => $organizations->sum('hearings_count'),
        ];
        
        return view('superuser.dashboard', compact('organizations', 'stats'));
    }
}
