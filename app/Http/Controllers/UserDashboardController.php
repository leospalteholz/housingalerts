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
                ->where('approved', true)
                ->orderBy('start_datetime', 'asc')
                ->with(['region', 'organization'])
                ->get();
        }

        // Get notification settings
        $notificationSettings = $user->getNotificationSettings();
        
        return view('user.dashboard', compact('user', 'monitoredRegions', 'upcomingHearings', 'allRegions', 'notificationSettings'));
    }

    /**
     * Resubscribe the user to notifications
     */
    public function resubscribe()
    {
        $user = auth()->user();
        $user->unsubscribed_at = null;
        $user->save();

        return redirect()->route('user.dashboard')->with('success', 'You have been resubscribed to notifications.');
    }

    /**
     * Update notification preferences
     */
    public function updateNotificationPreferences(Request $request)
    {
        $request->validate([
            'notify_development_hearings' => 'boolean',
            'notify_policy_hearings' => 'boolean',
        ]);

        $user = auth()->user();
        $settings = $user->getNotificationSettings();
        
        $settings->update([
            'notify_development_hearings' => $request->has('notify_development_hearings'),
            'notify_policy_hearings' => $request->has('notify_policy_hearings'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification preferences updated successfully!'
        ]);
    }

    /**
     * Get upcoming hearings for the current user (AJAX endpoint)
     */
    public function getUpcomingHearings()
    {
        $user = auth()->user();
        $monitoredRegions = $user->regions()->with('organization')->get();
        
        // Get upcoming hearings in the user's monitored regions
        $upcomingHearings = collect();
        if ($monitoredRegions->count() > 0) {
            $regionIds = $monitoredRegions->pluck('id');
            $upcomingHearings = Hearing::whereIn('region_id', $regionIds)
                ->where('start_datetime', '>=', now())
                ->where('approved', true)
                ->orderBy('start_datetime', 'asc')
                ->with(['region', 'organization'])
                ->get();
        }

        return view('user.partials.hearings-list', compact('upcomingHearings'))->render();
    }
}
