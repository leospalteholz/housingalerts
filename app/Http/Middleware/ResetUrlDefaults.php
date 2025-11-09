<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

class ResetUrlDefaults
{
    public function handle(Request $request, Closure $next): Response
    {
        URL::defaults([]);

        return $next($request);
    }
}
