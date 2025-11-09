<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Arr;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    protected function currentOrganization(): ?Organization
    {
        $organization = request()->route('organization');

        return $organization instanceof Organization ? $organization : null;
    }

    protected function currentOrganizationOrFail(): Organization
    {
        $organization = $this->currentOrganization();

        if (!$organization) {
            abort(404);
        }

        return $organization;
    }

    protected function orgRoute(string $name, $parameters = [], bool $absolute = true): string
    {
        $organization = $this->currentOrganizationOrFail();

        if (!is_array($parameters)) {
            $parameters = Arr::wrap($parameters);
        }

        return route($name, array_merge(['organization' => $organization->slug], $parameters), $absolute);
    }
}
