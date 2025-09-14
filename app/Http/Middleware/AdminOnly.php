<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated and is admin or superuser
        if (auth()->check() && (auth()->user()->is_admin || auth()->user()->is_superuser)) {
            return $next($request);
        }
        // Redirect to dashboard with error message
        return redirect()->route('dashboard')->with('error', 'You do not have admin access.');
    }
}
