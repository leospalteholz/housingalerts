<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
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
        $subscriber = Subscriber::where('email', $email)->first();

        if (!$subscriber) {
            abort(404, 'Subscriber not found.');
        }

        if ($subscriber->unsubscribed_at) {
            return view('unsubscribe.already-unsubscribed', ['subscriber' => $subscriber]);
        }

        return view('unsubscribe.confirm', ['subscriber' => $subscriber]);
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
        $subscriber = Subscriber::where('email', $email)->first();

        if (!$subscriber) {
            abort(404, 'Subscriber not found.');
        }

        $subscriber->unsubscribed_at = now();
        $subscriber->save();

        return view('unsubscribe.success', ['subscriber' => $subscriber]);
    }

    /**
     * Generate a signed unsubscribe URL for a user
     */
    public static function generateUnsubscribeUrl(Subscriber $subscriber): string
    {
        return URL::temporarySignedRoute(
            'unsubscribe.show',
            now()->addDays(30), // Valid for 30 days
            ['email' => $subscriber->email]
        );
    }
}