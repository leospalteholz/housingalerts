<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SubscriberNotificationSettings extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'subscriber_id',
        'notify_development_hearings',
        'notify_policy_hearings',
        'send_day_of_reminders',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'notify_development_hearings' => 'boolean',
        'notify_policy_hearings' => 'boolean',
        'send_day_of_reminders' => 'boolean',
    ];

    /**
     * Get the subscriber that owns the notification settings.
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Get or create notification settings for a subscriber.
     */
    public static function getOrCreateForSubscriber(Subscriber $subscriber): self
    {
        return self::firstOrCreate(
            ['subscriber_id' => $subscriber->id],
            [
                'notify_development_hearings' => true,
                'notify_policy_hearings' => true,
                'send_day_of_reminders' => true,
            ]
        );
    }
}
