<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Region;
use Illuminate\Http\Request;

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
            $user->sendEmailVerificationNotification();
            
            return redirect()->route('dashboard')->with('success', 
                'Welcome! We sent a confirmation email with your personal dashboard link for future access.'
            );
        }

        // Existing user - just log them in
        return redirect()->route('dashboard')->with('success', 
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
        return redirect()->route('dashboard')->with('success', 
            'Welcome! You can manage your housing alert preferences below.'
        );
    }
}
