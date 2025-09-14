<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name', 'organization_id'];

    public function organization() {
        return $this->belongsTo(Organization::class);
    }

    public function hearings() {
        return $this->hasMany(Hearing::class);
    }

    public function users() {
        return $this->belongsToMany(User::class, 'user_region');
    }
}
