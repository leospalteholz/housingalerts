<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request): RedirectResponse
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->intended(RouteServiceProvider::homeRoute($request->user()));
        }

        try {
            $request->user()->sendEmailVerificationNotification();

            return back()->with('status', 'verification-link-sent');
        } catch (\Throwable $exception) {
            report($exception);

            $errorSummary = trim(($exception->getCode() ? $exception->getCode() . ': ' : '') . $exception->getMessage());

            return back()->with('error', 'We could not send the verification email right now. Please try again later. (' . $errorSummary . ')');
        }
    }
}
