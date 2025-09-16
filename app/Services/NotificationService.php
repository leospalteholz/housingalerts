<?php

namespace App\Services;

use App\Models\EmailNotification;
use App\Models\User;
use App\Models\Hearing;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Queue hearing created notifications for all users in the hearing's region
     */
    public function queueHearingCreatedNotifications(Hearing $hearing): int
    {
        // Get all users who want hearing created notifications for this region
        $users = User::whereHas('regions', function ($query) use ($hearing) {
            $query->where('region_id', $hearing->region_id);
        })
        ->where('hearing_created_notifications', true)
        ->get();
        
        $queuedCount = 0;
        
        foreach ($users as $user) {
            // Check if we've already sent this notification
            if (EmailNotification::alreadySent($user->id, $hearing->id, 'hearing_created')) {
                continue;
            }
            
            // Queue the notification
            EmailNotification::create([
                'user_id' => $user->id,
                'hearing_id' => $hearing->id,
                'notification_type' => 'hearing_created',
                'email_address' => $user->email,
                'status' => 'queued',
                'opted_in' => true,
                'created_at' => Carbon::now()
            ]);
            
            $queuedCount++;
        }
        
        return $queuedCount;
    }
    
    /**
     * Queue day-of reminder notifications for all users in the hearing's region
     */
    public function queueDayOfReminderNotifications(Hearing $hearing): int
    {
        // Get all users who want day-of reminders for this region
        $users = User::whereHas('regions', function ($query) use ($hearing) {
            $query->where('region_id', $hearing->region_id);
        })
        ->where('day_of_reminders', true)
        ->get();
        
        $queuedCount = 0;
        
        foreach ($users as $user) {
            // Check if we've already sent this notification
            if (EmailNotification::alreadySent($user->id, $hearing->id, 'day_of_reminder')) {
                continue;
            }
            
            // Queue the notification
            EmailNotification::create([
                'user_id' => $user->id,
                'hearing_id' => $hearing->id,
                'notification_type' => 'day_of_reminder',
                'email_address' => $user->email,
                'status' => 'queued',
                'opted_in' => true,
                'created_at' => Carbon::now()
            ]);
            
            $queuedCount++;
        }
        
        return $queuedCount;
    }
    
    /**
     * Queue both types of notifications for a hearing
     */
    public function queueAllNotificationsForHearing(Hearing $hearing): array
    {
        $hearingCreatedCount = $this->queueHearingCreatedNotifications($hearing);
        $dayOfReminderCount = $this->queueDayOfReminderNotifications($hearing);
        
        return [
            'hearing_created' => $hearingCreatedCount,
            'day_of_reminder' => $dayOfReminderCount,
            'total' => $hearingCreatedCount + $dayOfReminderCount
        ];
    }
}
