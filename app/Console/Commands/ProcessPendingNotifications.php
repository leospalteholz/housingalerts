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
    protected $delayAfterHearingCreated = 5; // minutes

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
     * Process hearing_created notifications with 1 minute delay
     */
    private function processHearingCreatedNotifications(): int
    {
        $cutoffTime = Carbon::now()->subMinutes($this->delayAfterHearingCreated);
        
        $this->line("Looking for hearing_created notifications older than: {$cutoffTime}");
        
        $notifications = EmailNotification::where('status', 'queued')
            ->where('notification_type', 'hearing_created')
            ->where('created_at', '<=', $cutoffTime)
            ->with(['user', 'hearing.region'])
            ->get();
            
        $this->line("Found {$notifications->count()} hearing_created notifications to process");
            
        $count = 0;
        
        foreach ($notifications as $notification) {
            $this->line("Processing notification ID {$notification->id} created at {$notification->created_at}");
            if ($this->sendNotification($notification, 'created')) {
                $count++;
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
                $query->whereDate('start_date', $today);
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
