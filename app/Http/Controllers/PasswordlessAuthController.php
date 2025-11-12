<?php

namespace App\Http\Controllers;

use App\Models\Subscriber;
use App\Models\User;
use App\Notifications\PasswordlessDashboardLinkNotification;
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

        $subscriber = Subscriber::where('email', $request->email)->first();

        if (!$subscriber) {
            $subscriber = Subscriber::findOrCreateByEmail(
                $request->email,
                $request->name
            );
        } elseif ($request->filled('name') && empty($subscriber->name)) {
            $subscriber->name = $request->name;
            $subscriber->save();
        }

        $isNewAccount = $subscriber->wasRecentlyCreated ?? false;

        $emailDispatched = $this->sendDashboardLink(
            $subscriber,
            $isNewAccount
        );

        return view('auth.passwordless-link-sent', [
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
            $emailDispatched = $this->sendDashboardLink($subscriber, false);

            return view('auth.passwordless-expired', [
                'email' => $subscriber->email,
                'emailDispatched' => $emailDispatched,
            ]);
        }

        // Auto-login the user for this session
        Auth::guard('subscriber')->login($subscriber);

        if (!$subscriber->hasVerifiedEmail()) {
            $subscriber->markEmailAsVerified();
        }

        // Redirect to the regular dashboard - no need for a separate view
        $redirectUrl = RouteServiceProvider::homeRoute($subscriber);

        return redirect()->to($redirectUrl)->with('success', 
            'Welcome! You can manage your housing alert preferences below.'
        );
    }

    private function sendDashboardLink(Subscriber $subscriber, bool $isNewAccount): bool
    {
        $dashboardUrl = route('dashboard.token', ['token' => $subscriber->generateDashboardToken()]);

        return $this->dispatchNotification(
            $subscriber,
            function () use ($subscriber, $dashboardUrl, $isNewAccount) {
                $subscriber->notify(new PasswordlessDashboardLinkNotification($dashboardUrl, $isNewAccount));
            },
            $isNewAccount ? 'Passwordless signup email failed to send.' : 'Passwordless dashboard link email failed to send.'
        );
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
