<?php

namespace App\Services;

use App\Models\EmailNotification;
use App\Models\Subscriber;
use App\Models\Hearing;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Queue hearing created notifications for all users in the hearing's region
     */
    public function queueHearingCreatedNotifications(Hearing $hearing): int
    {
        $subscribers = Subscriber::query()
            ->whereNull('unsubscribed_at')
            ->whereHas('regions', function ($query) use ($hearing) {
                $query->where('region_id', $hearing->region_id);
            })
            ->whereNotNull('email_verified_at')
            ->whereHas('notificationSettings', function ($query) use ($hearing) {
                if ($hearing->type === 'policy') {
                    $query->where('notify_policy_hearings', true);
                } else {
                    $query->where('notify_development_hearings', true);
                }
            })
            ->get();
        
        $queuedCount = 0;
        
        foreach ($subscribers as $subscriber) {
            if (EmailNotification::alreadySent($subscriber->id, $hearing->id, 'hearing_created')) {
                continue;
            }
            
            EmailNotification::create([
                'subscriber_id' => $subscriber->id,
                'hearing_id' => $hearing->id,
                'notification_type' => 'hearing_created',
                'email_address' => $subscriber->email,
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
        $subscribers = Subscriber::query()
            ->whereNull('unsubscribed_at')
            ->whereHas('regions', function ($query) use ($hearing) {
                $query->where('region_id', $hearing->region_id);
            })
            ->whereNotNull('email_verified_at')
            ->whereHas('notificationSettings', function ($query) {
                $query->where('send_day_of_reminders', true);
            })
            ->get();
        
        $queuedCount = 0;
        
        foreach ($subscribers as $subscriber) {
            if (EmailNotification::alreadySent($subscriber->id, $hearing->id, 'day_of_reminder')) {
                continue;
            }
            
            EmailNotification::create([
                'subscriber_id' => $subscriber->id,
                'hearing_id' => $hearing->id,
                'notification_type' => 'day_of_reminder',
                'email_address' => $subscriber->email,
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
