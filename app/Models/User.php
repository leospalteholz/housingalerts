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
}
