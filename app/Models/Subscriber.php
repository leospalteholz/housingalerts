<?php

namespace App\Models;

use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\Notifiable;

class Subscriber extends Model implements Authenticatable, MustVerifyEmail
{
    use HasFactory;
    use Notifiable;
    use AuthenticatableTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'dashboard_token',
        'dashboard_token_expires_at',
        'unsubscribed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'dashboard_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'dashboard_token_expires_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
    ];

    /**
     * Regions that the subscriber monitors.
     */
    public function regions(): BelongsToMany
    {
        return $this->belongsToMany(Region::class, 'region_subscriber')
            ->withTimestamps();
    }

    /**
     * Access the subscriber's notification preferences.
     */
    public function notificationSettings(): HasOne
    {
        return $this->hasOne(SubscriberNotificationSettings::class);
    }

    /**
     * Email notifications sent to the subscriber.
     */
    public function emailNotifications(): HasMany
    {
        return $this->hasMany(EmailNotification::class);
    }

    /**
     * Retrieve the subscriber's notification settings, creating defaults if absent.
     */
    public function getNotificationSettings(): SubscriberNotificationSettings
    {
        return SubscriberNotificationSettings::getOrCreateForSubscriber($this);
    }

    /**
     * Generate a new dashboard token and return the raw token.
     */
    public function generateDashboardToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32));
            $hashed = hash('sha256', $token);
        } while (static::where('dashboard_token', $hashed)->exists());

        $this->dashboard_token = $hashed;
        $this->dashboard_token_expires_at = now()->addWeek();
        $this->save();

        return $token;
    }

    /**
     * Get the dashboard URL for passwordless access.
     */
    public function getDashboardUrl(): string
    {
        $token = $this->generateDashboardToken();

        return route('dashboard.token', ['token' => $token]);
    }

    public function hasValidDashboardToken(): bool
    {
        return filled($this->dashboard_token)
            && !is_null($this->dashboard_token_expires_at)
            && $this->dashboard_token_expires_at->isFuture();
    }

    /**
     * Find an existing subscriber or create a new record by email.
     */
    public static function findOrCreateByEmail(string $email, ?string $name = null): self
    {
        $subscriber = static::where('email', $email)->first();

        if (!$subscriber) {
            $subscriber = static::create([
                'email' => $email,
                'name' => $name ?: explode('@', $email)[0],
                'email_verified_at' => null,
            ]);

            $subscriber->generateDashboardToken();
        }

        return $subscriber;
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    public function markEmailAsVerified(): bool
    {
        if ($this->hasVerifiedEmail()) {
            return false;
        }

        $this->forceFill([
            'email_verified_at' => $this->freshTimestamp(),
        ])->save();

        return true;
    }

    public function getEmailForVerification(): string
    {
        return $this->email;
    }

    /**
     * Email verification messages are delivered via the passwordless dashboard link flow.
     */
    public function sendEmailVerificationNotification(): void
    {
        // Verification emails are sent alongside the dashboard link notification.
    }
}
