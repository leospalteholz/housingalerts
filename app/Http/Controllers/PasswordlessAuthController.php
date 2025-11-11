<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\User;
use App\Notifications\ExistingPasswordlessUserNotification;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
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

        $existingUser = User::where('email', $request->email)->first();

        if ($existingUser && $existingUser->requiresPassword()) {
            return redirect()->route('login')->with('status', 'Please sign in with your password to continue.');
        }

        if (!$existingUser) {
            $user = User::findOrCreatePasswordless(
                $request->email,
                $request->name
            );

            if ($user->wasRecentlyCreated) {
                // Temporarily assign new users to Homes for Living organization via slug lookup
                $defaultOrganization = Organization::where('slug', 'hfl')->first();

                if ($defaultOrganization) {
                    $user->organization()->associate($defaultOrganization);
                    $user->save();
                } else {
                    Log::warning('Default organization slug "hfl" not found during passwordless signup.', [
                        'user_id' => $user->id,
                        'email' => $user->email,
                    ]);
                }
            }

            auth()->login($user);

            $mailSent = $this->sendVerificationEmail($user, 'Passwordless signup email failed to send.');

            $redirectUrl = RouteServiceProvider::homeRoute($user);

            $message = $mailSent
                ? 'Welcome! We sent a confirmation email with your personal dashboard link for future access.'
                : 'Welcome! We could not send the confirmation email right now, but you are logged in and can access your dashboard below.';

            return redirect()->to($redirectUrl)->with('success', $message);
        }

        $user = $existingUser;

        if (!$user->hasVerifiedEmail()) {
            auth()->login($user);

            $mailSent = $this->sendVerificationEmail($user, 'Passwordless verification resend failed.');

            $redirectUrl = RouteServiceProvider::homeRoute($user);

            $message = $mailSent
                ? 'Welcome back! Please verify your email to receive housing alerts.'
                : 'Welcome back! We could not resend the verification email, but you can update your housing alerts below.';

            return redirect()->to($redirectUrl)->with('success', $message);
        }

        $emailDispatched = $this->sendDashboardLink($user, 'Passwordless dashboard link email failed to send.');

        return view('auth.passwordless-existing', [
            'email' => $user->email,
            'emailDispatched' => $emailDispatched,
        ]);
    }

    /**
     * Access dashboard via permanent magic link
     */
    public function dashboard(Request $request, string $token)
    {
        $hashedToken = hash('sha256', $token);

        $user = User::where('dashboard_token', $hashedToken)->first();

        if (!$user) {
            abort(404, 'Invalid dashboard link');
        }

        if (!$user->hasValidDashboardToken()) {
            $emailDispatched = $this->sendDashboardLink($user, 'Passwordless dashboard link expired resend failed.');

            return view('auth.passwordless-expired', [
                'email' => $user->email,
                'emailDispatched' => $emailDispatched,
            ]);
        }

        // Auto-login the user for this session
        auth()->login($user);

        // Redirect to the regular dashboard - no need for a separate view
        $redirectUrl = RouteServiceProvider::homeRoute($user);

        return redirect()->to($redirectUrl)->with('success', 
            'Welcome! You can manage your housing alert preferences below.'
        );
    }

    private function sendVerificationEmail(User $user, string $logMessage): bool
    {
        return $this->dispatchNotification($user, fn () => $user->sendEmailVerificationNotification(), $logMessage);
    }

    private function sendDashboardLink(User $user, string $logMessage): bool
    {
        return $this->dispatchNotification($user, fn () => $user->notify(new ExistingPasswordlessUserNotification()), $logMessage);
    }

    private function dispatchNotification(User $user, callable $callback, string $logMessage): bool
    {
        try {
            $callback();

            return true;
        } catch (Throwable $e) {
            Log::error($logMessage, [
                'user_id' => $user->id,
                'email' => $user->email,
                'exception' => $e->getMessage(),
            ]);

            return false;
        }
    }
}
