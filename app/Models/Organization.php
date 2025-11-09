<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Organization extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'slug',
        'contact_email',
        'website_url',
        'about',
        'areas_active',
        'user_visible',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Organization $organization) {
            $source = $organization->slug ?: $organization->name ?: Str::random(8);
            $organization->slug = static::generateUniqueSlug($source);
        });

        static::updating(function (Organization $organization) {
            if ($organization->isDirty('slug') || empty($organization->slug)) {
                $source = $organization->slug ?: $organization->name ?: Str::random(8);
                $organization->slug = static::generateUniqueSlug($source, $organization->id);
            }
        });
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function generateUniqueSlug(string $value, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($value);

        if ($baseSlug === '') {
            $baseSlug = Str::random(8);
        }

        $slug = $baseSlug;
        $attempt = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $attempt++;
        }

        return $slug;
    }

    public function users() {
        return $this->hasMany(User::class);
    }

    public function regions() {
        return $this->hasMany(Region::class);
    }

    public function hearings() {
        return $this->hasMany(Hearing::class);
    }
}
