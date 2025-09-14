<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;
    
    protected $fillable = ['name', 'slug', 'contact-email', 'user_visible'];

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
