<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Subscriber;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SubscriberAdminController extends Controller
{
    public function index(Organization $organization): View
    {
        $user = auth()->user();

        if ($user?->is_superuser) {
            $subscribers = Subscriber::with(['regions.organization'])
                ->orderBy('email')
                ->get();
        } else {
            $subscribers = Subscriber::with([
                    'regions' => function ($query) use ($organization) {
                        $query
                            ->where('organization_id', $organization->id)
                            ->with('organization');
                    },
                ])
                ->whereHas('regions', fn ($query) => $query->where('organization_id', $organization->id))
                ->orderBy('email')
                ->get();
        }

        return view('subscribers.index', [
            'subscribers' => $subscribers,
            'isSuperuser' => $user?->is_superuser ?? false,
        ]);
    }

    public function destroy(Organization $organization, Subscriber $subscriber): RedirectResponse
    {
        $user = auth()->user();

        if (!$user?->is_superuser) {
            $hasAccess = $subscriber->regions()
                ->where('organization_id', $organization->id)
                ->exists();

            if (!$hasAccess) {
                abort(404);
            }
        }

        $subscriber->delete();

        return redirect($this->orgRoute('subscribers.index'))
            ->with('success', 'Subscriber deleted successfully.');
    }
}
