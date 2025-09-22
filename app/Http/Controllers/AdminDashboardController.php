<?php

namespace App\Http\Controllers;

use App\Models\Hearing;
use App\Models\Organization;
use App\Models\Region;
use App\Models\User;

class AdminDashboardController extends Controller
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

            return view('admin.dashboard', compact('organizations', 'stats'));
        }
        // For admin users, show statistics for their organization
        elseif (auth()->user()->is_admin) {
            $orgId = auth()->user()->organization_id;

            $stats = [
                'totalUsers' => User::where('organization_id', $orgId)->count(),
                'totalRegions' => Region::where('organization_id', $orgId)->count(),
                'totalHearings' => Hearing::where('organization_id', $orgId)->count(),
            ];

            return view('admin.dashboard', compact('stats'));
        }

        // If somehow a regular user gets here, redirect to user dashboard
        return redirect()->route('user.dashboard');
    }
}
