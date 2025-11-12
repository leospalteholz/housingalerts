<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
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
        $user = auth()->user();

        // Allow access if user is authenticated and is an admin or superuser
        if ($user && ($user->is_admin || $user->is_superuser)) {
            return $next($request);
        }

        // If user is authenticated but not an admin, just redirect to dashboard without error
        if ($user) {
            return redirect(RouteServiceProvider::homeRoute($user))->with('error', 'You do not have permission to access this area.');
        }
        
        // If not authenticated at all, redirect to login
        return redirect()->route('login');
    }
}
