<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        $user = User::findOrCreatePasswordless(
            $request->email, 
            $request->name
        );

        // Set organization ID to 2 (Homes for Living) for new users
        // FIXME obviously this is a temporary hack until we have proper org management
        if ($user->wasRecentlyCreated) {
            $user->organization_id = 2;
            $user->save();
        }

        // Auto-login the user immediately
        auth()->login($user);

        // Send verification email with dashboard link for future access
        if ($user->wasRecentlyCreated) {
            $mailSent = true;

            try {
                $user->sendEmailVerificationNotification();
            } catch (Throwable $e) {
                $mailSent = false;

                Log::error('Passwordless signup email failed to send.', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'exception' => $e->getMessage(),
                ]);
            }
            
            $redirectUrl = RouteServiceProvider::homeRoute($user);

            $message = $mailSent
                ? 'Welcome! We sent a confirmation email with your personal dashboard link for future access.'
                : 'Welcome! We could not send the confirmation email right now, but you are logged in and can access your dashboard below.';

            return redirect()->to($redirectUrl)->with('success', $message);
        }

        // Existing user - just log them in
        $redirectUrl = RouteServiceProvider::homeRoute($user);

        return redirect()->to($redirectUrl)->with('success', 
            'Welcome back! You\'re now logged in to manage your housing alerts.'
        );
    }

    /**
     * Access dashboard via permanent magic link
     */
    public function dashboard(Request $request, string $token)
    {
        $user = User::where('dashboard_token', $token)->first();

        if (!$user) {
            abort(404, 'Invalid dashboard link');
        }

        // Auto-login the user for this session
        auth()->login($user);

        // Redirect to the regular dashboard - no need for a separate view
        $redirectUrl = RouteServiceProvider::homeRoute($user);

        return redirect()->to($redirectUrl)->with('success', 
            'Welcome! You can manage your housing alert preferences below.'
        );
    }
}
