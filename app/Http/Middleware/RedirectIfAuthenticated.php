<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();

                if ($user) {
                    return redirect(RouteServiceProvider::homeRoute($user));
                }

                return redirect(RouteServiceProvider::HOME);
            }
        }

        if (!in_array('subscriber', $guards, true) && Auth::guard('subscriber')->check()) {
            $subscriber = Auth::guard('subscriber')->user();

            if ($subscriber) {
                return redirect(RouteServiceProvider::homeRoute($subscriber));
            }

            return redirect(RouteServiceProvider::HOME);
        }

        return $next($request);
    }
}
