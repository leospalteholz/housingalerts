<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = [
        'name',
        'organization_id',
        'comments_email',
        'remote_instructions',
        'inperson_instructions',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function hearings()
    {
        return $this->hasMany(Hearing::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_region');
    }
}
