<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationAccess
{
    public function handle(Request $request, Closure $next): Response
    {
        $organization = $request->route('organization');

        if (is_string($organization)) {
            $organization = Organization::where('slug', $organization)->first();
            if ($organization) {
                $request->route()?->setParameter('organization', $organization);
            }
        }

        if (!$organization instanceof Organization) {
            abort(404);
        }

        $user = $request->user();
        $subscriber = $request->user('subscriber');

        if (!$user && !$subscriber) {
            abort(403);
        }

        if ($user && !$user->is_superuser && $user->organization_id !== $organization->id) {
            abort(403, 'You do not have access to this organization.');
        }

        URL::defaults(['organization' => $organization->slug]);
        View::share('currentOrganization', $organization);

        return $next($request);
    }
}
