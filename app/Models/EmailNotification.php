<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailNotification extends Model
{
    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The name of the "updated at" column.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

     /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
    'subscriber_id',
        'hearing_id',
        'notification_type',
        'email_address',
        'sent_at',
        'status',
        'failure_reason',
        'opted_in',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'created_at' => 'datetime',
        'notification_type' => 'string',
        'status' => 'string',
        'opted_in' => 'boolean',
    ];

    /**
     * Get the subscriber that received this notification.
     */
    public function subscriber(): BelongsTo
    {
        return $this->belongsTo(Subscriber::class);
    }

    /**
     * Get the hearing this notification is about.
     */
    public function hearing(): BelongsTo
    {
        return $this->belongsTo(Hearing::class);
    }

    /**
     * Check if a notification has already been sent.
     */
    public static function alreadySent(int $subscriberId, int $hearingId, string $notificationType): bool
    {
        return self::where([
            'subscriber_id' => $subscriberId,
            'hearing_id' => $hearingId,
            'notification_type' => $notificationType,
        ])->exists();
    }

    /**
     * Log a sent notification.
     */
    public static function logSent(int $subscriberId, int $hearingId, string $notificationType, string $emailAddress, bool $optedIn = true, string $status = 'sent', ?string $failureReason = null): self
    {
        return self::create([
            'subscriber_id' => $subscriberId,
            'hearing_id' => $hearingId,
            'notification_type' => $notificationType,
            'email_address' => $emailAddress,
            'sent_at' => now(),
            'status' => $status,
            'failure_reason' => $failureReason,
            'opted_in' => $optedIn,
        ]);
    }

    /**
     * Scope to get notifications by type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('notification_type', $type);
    }

    /**
     * Scope to get failed notifications.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get sent notifications.
     */
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }
}
