<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationSettings extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
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
     * Get the user that owns the notification settings.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get or create notification settings for a user.
     */
    public static function getOrCreateForUser(User $user): self
    {
        return self::firstOrCreate(
            ['user_id' => $user->id],
            [
                'notify_development_hearings' => true,
                'notify_policy_hearings' => true,
                'send_day_of_reminders' => true,
            ]
        );
    }
}
