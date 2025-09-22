<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Allow access if user is authenticated and is an admin
        if (auth()->check() && auth()->user()->is_admin) {
            return $next($request);
        }

        // If user is authenticated but not an admin, just redirect to dashboard without error
        if (auth()->check()) {
            return redirect()->route('dashboard')->with('error', 'You do not have permission to access this area.');
        }

        // If not authenticated at all, redirect to login
        return redirect()->route('login');
    }
}
