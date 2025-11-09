<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Region extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'organization_id',
        'comments_email',
        'remote_instructions',
        'inperson_instructions'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Region $region) {
            $source = $region->slug ?: $region->name ?: Str::random(8);
            $region->slug = static::generateUniqueSlug($source);
        });

        static::updating(function (Region $region) {
            if ($region->isDirty('slug') || empty($region->slug)) {
                $source = $region->slug ?: $region->name ?: Str::random(8);
                $region->slug = static::generateUniqueSlug($source, $region->id);
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
        $counter = 1;

        while (static::where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        return $slug;
    }

    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function hearings() {
        return $this->hasMany(Hearing::class);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_region');
    }

    public function councillors() {
        return $this->hasMany(Councillor::class);
    }
}
