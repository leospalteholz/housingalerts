<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationSettingsController extends Controller
{
    /**
     * Show the notification settings form.
     */
    public function show(): View
    {
        $user = auth()->user();
        $settings = $user->getNotificationSettings();

        return view('notification-settings', compact('settings'));
    }

    /**
     * Update the notification settings.
     */
    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'notify_development_hearings' => 'boolean',
            'notify_policy_hearings' => 'boolean',
            'send_day_of_reminders' => 'boolean',
        ]);

        $user = auth()->user();
        $settings = $user->getNotificationSettings();

        $settings->update([
            'notify_development_hearings' => $request->boolean('notify_development_hearings'),
            'notify_policy_hearings' => $request->boolean('notify_policy_hearings'),
            'send_day_of_reminders' => $request->boolean('send_day_of_reminders'),
        ]);

        return redirect()->route('user.dashboard')
            ->with('success', 'Notification settings updated successfully!');
    }
}
