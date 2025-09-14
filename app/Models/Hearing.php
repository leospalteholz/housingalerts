<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hearing extends Model
{
    protected $fillable = [
        'organization_id',
        'region_id',
        'type',
        'title',
        'street_address',
        'postal_code',
        'rental',
        'units',
        'description',
        'remote_instructions',
        'inperson_instructions',
        'comments_email',
        'image_url',
        'start_date',
        'start_time',
        'end_time',
        'more_info_url',
    ];

    protected $casts = [
        'rental' => 'boolean',
        'start_date' => 'date',
    ];

    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function region() {
        return $this->belongsTo(Region::class);
    }

    // Helper method to get display title
    public function getDisplayTitleAttribute()
    {
        if ($this->type === 'policy') {
            return $this->title;
        } else {
            return $this->title ?: "Hearing for {$this->street_address}";
        }
    }

    // Helper method to check if this is a development hearing
    public function isDevelopment()
    {
        return $this->type === 'development';
    }

    // Helper method to check if this is a policy hearing
    public function isPolicy()
    {
        return $this->type === 'policy';
    }
}
