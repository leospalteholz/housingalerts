<?php

namespace App\Console\Commands;

use App\Models\EmailNotification;
use App\Models\User;
use App\Models\Hearing;
use App\Mail\HearingNotificationMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class ProcessPendingNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:process-pending';
    protected $delayAfterHearingCreated = 0; // minutes

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process queued email notifications based on timing rules';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing pending notifications...');
        
        $processedCount = 0;
        
        // Process hearing_created notifications (1 minute delay)
        $processedCount += $this->processHearingCreatedNotifications();
        
        // Process day_of_reminder notifications (send on hearing date)
        $processedCount += $this->processDayOfReminderNotifications();
        
        $this->info("Processed {$processedCount} notifications.");
        
        return 0;
    }
    
    /**
     * Process hearing_created notifications - check all hearings and send to subscribed users who haven't received them
     */
    private function processHearingCreatedNotifications(): int
    {
        $cutoffTime = Carbon::now()->subMinutes($this->delayAfterHearingCreated);
        
        $this->line("Looking for hearings created before: {$cutoffTime}");
        
        // Fetch all hearings that are in the future and were created before the cutoff time
        $hearings = Hearing::where('start_datetime', '>=', Carbon::now())
            ->where('created_at', '<=', $cutoffTime)
            ->with(['region'])
            ->get();
            
        $this->line("Found {$hearings->count()} hearings to check for notifications");
            
        $count = 0;
        
        foreach ($hearings as $hearing) {
            $this->line("Checking hearing ID {$hearing->id}: {$hearing->title}");
            
            // Get all users who should receive notifications for this hearing
            $subscribedUsers = User::whereHas('regions', function ($query) use ($hearing) {
                $query->where('regions.id', $hearing->region_id);
            })
            ->whereHas('notificationSettings', function ($query) use ($hearing) {
                if ($hearing->type === 'development') {
                    $query->where('notify_development_hearings', true);
                } else if ($hearing->type === 'policy') {
                    $query->where('notify_policy_hearings', true);
                } else {
                    // For any other type, check both
                    $query->where(function ($q) {
                        $q->where('notify_development_hearings', true)
                          ->orWhere('notify_policy_hearings', true);
                    });
                }
            })
            ->whereNull('unsubscribed_at')  // User is opted in (not unsubscribed)
            ->whereNotNull('email_verified_at')  // User has verified their email
            ->get();
            
            $this->line("  Found {$subscribedUsers->count()} subscribed users for this hearing");
            
            foreach ($subscribedUsers as $user) {
                // Check if this user has already received a hearing_created notification for this hearing
                $existingNotification = EmailNotification::where('user_id', $user->id)
                    ->where('hearing_id', $hearing->id)
                    ->where('notification_type', 'hearing_created')
                    ->first();
                
                if (!$existingNotification) {
                    // Create and send notification
                    $notification = EmailNotification::create([
                        'user_id' => $user->id,
                        'hearing_id' => $hearing->id,
                        'notification_type' => 'hearing_created',
                        'email_address' => $user->email,
                        'status' => 'queued',
                        'opted_in' => is_null($user->unsubscribed_at)  // True if not unsubscribed
                    ]);
                    
                    $this->line("  Created notification for user {$user->email}");
                    
                    if ($this->sendNotification($notification, 'created')) {
                        $count++;
                    }
                } else {
                    $this->line("  User {$user->email} already has notification (status: {$existingNotification->status})");
                }
            }
        }
        
        if ($count > 0) {
            $this->info("Sent {$count} hearing created notifications.");
        }
        
        return $count;
    }
    
    /**
     * Process day_of_reminder notifications (send on hearing date)
     */
    private function processDayOfReminderNotifications(): int
    {
        $today = Carbon::today();
        
        $notifications = EmailNotification::where('status', 'queued')
            ->where('notification_type', 'day_of_reminder')
            ->whereHas('hearing', function ($query) use ($today) {
                $query->whereDate('start_datetime', $today);
            })
            ->with(['user', 'hearing.region'])
            ->get();
            
        $count = 0;
        
        foreach ($notifications as $notification) {
            if ($this->sendNotification($notification, 'day_of_reminder')) {
                $count++;
            }
        }
        
        if ($count > 0) {
            $this->info("Sent {$count} day-of reminder notifications.");
        }
        
        return $count;
    }
    
    /**
     * Send a notification email
     */
    private function sendNotification(EmailNotification $notification, string $template): bool
    {
        try {
            // Skip if user or hearing no longer exists
            if (!$notification->user || !$notification->hearing) {
                $notification->update([
                    'status' => 'failed',
                    'failure_reason' => 'User or hearing no longer exists'
                ]);
                return false;
            }
            
            // Send the email
            Mail::to($notification->email_address)
                ->send(new HearingNotificationMail(
                    $notification->user,
                    $notification->hearing,
                    $template
                ));
            
            // Mark as sent
            $notification->update([
                'status' => 'sent',
                'sent_at' => Carbon::now()
            ]);
            
            $this->line("✓ Sent {$notification->notification_type} to {$notification->email_address}");
            
            return true;
            
        } catch (\Exception $e) {
            // Mark as failed
            $notification->update([
                'status' => 'failed',
                'failure_reason' => $e->getMessage()
            ]);
            
            $this->error("✗ Failed to send {$notification->notification_type} to {$notification->email_address}: {$e->getMessage()}");
            
            return false;
        }
    }
}
