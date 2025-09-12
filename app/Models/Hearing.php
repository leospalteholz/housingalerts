<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hearing extends Model
{
    protected $fillable = [
        'organization_id',
        'region_id',
        'title',
        'body',
        'image_url',
        'start_date',
        'start_time',
        'more_info_url',
    ];

    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function region() {
        return $this->belongsTo(Region::class);
    }
}
