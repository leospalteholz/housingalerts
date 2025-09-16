<?php

namespace App\Console\Commands;

use App\Models\EmailNotification;
use App\Models\User;
use App\Models\Hearing;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Carbon\Carbon;

class TestNotificationSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test the notification system by queuing and processing sample notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing notification system...');
        
        // Get the first hearing and user for testing
        $hearing = Hearing::first();
        $user = User::first();
        
        if (!$hearing || !$user) {
            $this->error('Need at least one hearing and one user in the database to test.');
            return 1;
        }
        
        $this->info("Testing with hearing: {$hearing->title}");
        $this->info("Testing with user: {$user->email}");
        
        // Clear any existing notifications for this test
        EmailNotification::where('user_id', $user->id)
            ->where('hearing_id', $hearing->id)
            ->delete();
        
        // Create a test notification that's old enough to be processed (2 minutes ago)
        $testCreatedAt = Carbon::now()->subMinutes(2);
        $notification = new EmailNotification([
            'user_id' => $user->id,
            'hearing_id' => $hearing->id,
            'notification_type' => 'hearing_created',
            'email_address' => $user->email,
            'status' => 'queued',
            'opted_in' => true,
        ]);
        
        // Manually set the created_at timestamp
        $notification->created_at = $testCreatedAt;
        $notification->save();
        
        $this->info("✓ Created test notification (2 minutes old)");
        $this->info("   Created at: {$notification->created_at}");
        $this->info("   Status: {$notification->status}");
        $this->info("   Type: {$notification->notification_type}");
        
        // Check what notifications exist in the database
        $allNotifications = EmailNotification::where('user_id', $user->id)
            ->where('hearing_id', $hearing->id)
            ->get();
        
        $this->info("Total notifications for this user/hearing: {$allNotifications->count()}");
        foreach ($allNotifications as $n) {
            $this->info("   ID: {$n->id}, Status: {$n->status}, Type: {$n->notification_type}, Created: {$n->created_at}");
        }
        
        // Now process pending notifications
        $this->info('Processing pending notifications...');
        $this->call('notifications:process-pending');
        
        // Check the result
        $notification->refresh();
        
        if ($notification->status === 'sent') {
            $this->info('✅ SUCCESS! Notification was sent.');
            $this->info("   Sent at: {$notification->sent_at}");
        } elseif ($notification->status === 'failed') {
            $this->error('❌ FAILED! Notification failed to send.');
            $this->error("   Reason: {$notification->failure_reason}");
        } else {
            $this->warn('⏳ Notification is still queued (may not be old enough yet)');
        }
        
        return 0;
    }
}
