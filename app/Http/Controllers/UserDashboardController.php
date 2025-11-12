<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Hearing;

class UserDashboardController extends Controller
{
    public function index()
    {
        $subscriber = auth('subscriber')->user();

        abort_unless($subscriber, 403);

        $monitoredRegions = $subscriber->regions()
            ->with('organization')
            ->orderBy('regions.name')
            ->get();

        $monitoredRegionIds = $monitoredRegions->pluck('id');

        $organizationIds = $monitoredRegions->pluck('organization_id')->unique()->filter();

        $allRegions = Region::with('organization')
            ->when($organizationIds->isNotEmpty(), function ($query) use ($organizationIds) {
                $query->whereIn('organization_id', $organizationIds);
            })
            ->orderBy('name')
            ->get()
            ->map(function ($region) use ($monitoredRegionIds) {
                $region->is_monitored = $monitoredRegionIds->contains($region->id);
                return $region;
            });

        $upcomingHearings = collect();

        if ($monitoredRegionIds->isNotEmpty()) {
            $upcomingHearings = Hearing::whereIn('region_id', $monitoredRegionIds)
                ->where('start_datetime', '>=', now())
                ->where('approved', true)
                ->orderBy('start_datetime', 'asc')
                ->with(['region', 'organization'])
                ->get();
        }

        $notificationSettings = $subscriber->getNotificationSettings();

        $primaryOrganization = $monitoredRegions->first()?->organization;

        return view('user.dashboard', [
            'subscriber' => $subscriber,
            'monitoredRegions' => $monitoredRegions,
            'upcomingHearings' => $upcomingHearings,
            'allRegions' => $allRegions,
            'notificationSettings' => $notificationSettings,
            'primaryOrganization' => $primaryOrganization,
        ]);
    }

    /**
     * Resubscribe the user to notifications
     */
    public function resubscribe()
    {
        $subscriber = auth('subscriber')->user();

        abort_unless($subscriber, 403);

        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        return redirect()->route('subscriber.dashboard')->with('success', 'You have been resubscribed to notifications.');
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

    $subscriber = auth('subscriber')->user();

    abort_unless($subscriber, 403);

    $settings = $subscriber->getNotificationSettings();
        
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
        $subscriber = auth('subscriber')->user();

        abort_unless($subscriber, 403);

        $monitoredRegions = $subscriber->regions()
            ->with('organization')
            ->orderBy('regions.name')
            ->get();
        
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

        return view('user.partials.hearings-list', [
            'upcomingHearings' => $upcomingHearings,
        ])->render();
    }
}
