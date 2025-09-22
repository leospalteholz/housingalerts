<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class UnsubscribeController extends Controller
{
    /**
     * Show the unsubscribe confirmation page
     */
    public function show(Request $request)
    {
        // Validate the signed URL
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired unsubscribe link.');
        }

        $email = $request->query('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            abort(404, 'User not found.');
        }

        // Check if user is already unsubscribed
        if ($user->unsubscribed_at) {
            return view('unsubscribe.already-unsubscribed', compact('user'));
        }

        return view('unsubscribe.confirm', compact('user'));
    }

    /**
     * Process the unsubscribe request
     */
    public function unsubscribe(Request $request)
    {
        // Validate the signed URL
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired unsubscribe link.');
        }

        $email = $request->query('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            abort(404, 'User not found.');
        }

        // Unsubscribe the user by setting the timestamp
        $user->unsubscribed_at = now();
        $user->save();

        return view('unsubscribe.success', compact('user'));
    }

    /**
     * Generate a signed unsubscribe URL for a user
     */
    public static function generateUnsubscribeUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'unsubscribe.show',
            now()->addDays(30), // Valid for 30 days
            ['email' => $user->email]
        );
    }
}