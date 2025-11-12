<?php

use App\Models\Organization;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

if (!function_exists('orgRoute')) {
    /**
     * Generate a route URL that automatically scopes to the current organization slug.
     */
    function orgRoute(string $name, $parameters = [], bool $absolute = true): string
    {
        $organization = request()->route('organization');

        if (is_string($organization) && Str::of($organization)->isNotEmpty()) {
            $organization = Organization::where('slug', $organization)->first();
        }

        if (!$organization instanceof Organization) {
            $shared = View::shared('currentOrganization') ?? null;
            if ($shared instanceof Organization) {
                $organization = $shared;
            }
        }

        if (!$organization instanceof Organization) {
            $user = Auth::user();
            if ($user && $user->organization) {
                $organization = $user->organization;
            }
        }

        if (!$organization instanceof Organization) {
            $subscriber = Auth::guard('subscriber')->user();

            if ($subscriber) {
                static $subscriberOrganizationCache = [];

                $subscriberId = $subscriber->getKey();

                if (!array_key_exists($subscriberId, $subscriberOrganizationCache)) {
                    $subscriberOrganizationCache[$subscriberId] = $subscriber->regions()
                        ->with('organization')
                        ->get()
                        ->pluck('organization')
                        ->filter()
                        ->first();
                }

                $subscriberOrganization = $subscriberOrganizationCache[$subscriberId] ?? null;

                if ($subscriberOrganization instanceof Organization) {
                    $organization = $subscriberOrganization;
                }
            }
        }

        $parameters = Arr::wrap($parameters);

        if ($organization instanceof Organization) {
            $parameters = array_merge(['organization' => $organization->slug], $parameters);
        }

        return route($name, $parameters, $absolute);
    }
}
