<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'dashboard_token',
        'is_admin',
        'is_superuser',
        'organization_id',
        'email_verified_at',
        'unsubscribed_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'dashboard_token',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'password' => 'hashed',
        'dashboard_token_expires_at' => 'datetime',
    ];

    /**
     * Send the email verification notification.
     */
    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\CustomVerifyEmail);
    }

    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function regions() {
        return $this->belongsToMany(Region::class, 'user_region');
    }

    /**
     * Get the user's notification settings.
     */
    public function notificationSettings()
    {
        return $this->hasOne(UserNotificationSettings::class);
    }

    /**
     * Get the user's email notifications.
     */
    public function emailNotifications()
    {
        return $this->hasMany(EmailNotification::class);
    }

    /**
     * Get notification settings, creating default ones if they don't exist.
     */
    public function getNotificationSettings(): UserNotificationSettings
    {
        return UserNotificationSettings::getOrCreateForUser($this);
    }

    /**
     * Check if this is a passwordless regular user.
     */
    public function isPasswordless(): bool
    {
        return $this->password === null;
    }

    /**
     * Check if this is an admin/superuser with password.
     */
    public function requiresPassword(): bool
    {
        return $this->is_admin || $this->is_superuser;
    }

    /**
     * Generate a unique dashboard token for passwordless access.
     */
    public function generateDashboardToken(): string
    {
        do {
            $token = bin2hex(random_bytes(32)); // 64 character hex string
        } while (static::where('dashboard_token', $token)->exists());

        $this->dashboard_token = $token;
        $this->dashboard_token_expires_at = now()->addWeek();
        $this->save();

        return $token;
    }

    /**
     * Get the dashboard URL for this user.
     */
    public function getDashboardUrl(): string
    {
        if (!$this->dashboard_token || !$this->dashboard_token_expires_at || $this->dashboard_token_expires_at->isPast()) {
            $this->generateDashboardToken();
        }

        return route('dashboard.token', ['token' => $this->dashboard_token]);
    }

    public function hasValidDashboardToken(): bool
    {
        return !empty($this->dashboard_token)
            && !is_null($this->dashboard_token_expires_at)
            && $this->dashboard_token_expires_at->isFuture();
    }

    /**
     * Create or find a passwordless user by email.
     */
    public static function findOrCreatePasswordless(string $email, string $name = null): self
    {
        $user = static::where('email', $email)->first();

        if (!$user) {
            $user = static::create([
                'email' => $email,
                'name' => $name ?: explode('@', $email)[0], // Use email prefix as default name
                'password' => null // Explicitly passwordless
            ]);
            
            $user->generateDashboardToken();
        }

        return $user;
    }
}
