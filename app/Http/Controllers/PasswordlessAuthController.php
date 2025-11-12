<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\ExistingPasswordlessUserNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Throwable;

class PasswordlessAuthController extends Controller
{
    /**
     * Handle email signup from homepage
     */
    public function signup(Request $request)
    {
        $request->validate([
            'email' => 'required|email|max:255',
            'name' => 'nullable|string|max:255',
        ]);

        $existingAdmin = User::where('email', $request->email)->first();

        if ($existingAdmin && $existingAdmin->requiresPassword()) {
            return redirect()->route('login')->with('status', 'Please sign in with your password to continue.');
        }

        $existingSubscriber = Subscriber::where('email', $request->email)->first();

        if (!$existingSubscriber) {
            $subscriber = Subscriber::findOrCreateByEmail(
                $request->email,
                $request->name
            );

            Auth::guard('subscriber')->login($subscriber);

            $mailSent = $this->sendVerificationEmail($subscriber, 'Passwordless signup email failed to send.');

            $redirectUrl = RouteServiceProvider::homeRoute($subscriber);

            $message = $mailSent
                ? 'Welcome! We sent a confirmation email with your personal dashboard link for future access.'
                : 'Welcome! We could not send the confirmation email right now, but you are logged in and can access your dashboard below.';

            return redirect()->to($redirectUrl)->with('success', $message);
        }

        $subscriber = $existingSubscriber;

        if (!$subscriber->hasVerifiedEmail()) {
            Auth::guard('subscriber')->login($subscriber);

            $mailSent = $this->sendVerificationEmail($subscriber, 'Passwordless verification resend failed.');

            $redirectUrl = RouteServiceProvider::homeRoute($subscriber);

            $message = $mailSent
                ? 'Welcome back! Please verify your email to receive housing alerts.'
                : 'Welcome back! We could not resend the verification email, but you can update your housing alerts below.';

            return redirect()->to($redirectUrl)->with('success', $message);
        }

        $emailDispatched = $this->sendDashboardLink($subscriber, 'Passwordless dashboard link email failed to send.');

        return view('auth.passwordless-existing', [
            'email' => $subscriber->email,
            'emailDispatched' => $emailDispatched,
        ]);
    }

    /**
     * Access dashboard via permanent magic link
     */
    public function dashboard(Request $request, string $token)
    {
        $hashedToken = hash('sha256', $token);

        $subscriber = Subscriber::where('dashboard_token', $hashedToken)->first();

        if (!$subscriber) {
            abort(404, 'Invalid dashboard link');
        }

        if (!$subscriber->hasValidDashboardToken()) {
            $emailDispatched = $this->sendDashboardLink($subscriber, 'Passwordless dashboard link expired resend failed.');

            return view('auth.passwordless-expired', [
                'email' => $subscriber->email,
                'emailDispatched' => $emailDispatched,
            ]);
        }

        // Auto-login the user for this session
        Auth::guard('subscriber')->login($subscriber);

        // Redirect to the regular dashboard - no need for a separate view
        $redirectUrl = RouteServiceProvider::homeRoute($subscriber);

        return redirect()->to($redirectUrl)->with('success', 
            'Welcome! You can manage your housing alert preferences below.'
        );
    }

    private function sendVerificationEmail(Subscriber $subscriber, string $logMessage): bool
    {
        return $this->dispatchNotification($subscriber, fn () => $subscriber->sendEmailVerificationNotification(), $logMessage);
    }

    private function sendDashboardLink(Subscriber $subscriber, string $logMessage): bool
    {
        return $this->dispatchNotification($subscriber, fn () => $subscriber->notify(new ExistingPasswordlessUserNotification()), $logMessage);
    }

    private function dispatchNotification(Subscriber $subscriber, callable $callback, string $logMessage): bool
    {
        try {
            $callback();

            return true;
        } catch (Throwable $e) {
            Log::error($logMessage, [
                'subscriber_id' => $subscriber->id,
                'email' => $subscriber->email,
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
