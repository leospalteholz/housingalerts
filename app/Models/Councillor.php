<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Councillor extends Model
{
    protected $fillable = [
        'region_id',
        'name',
        'elected_start',
        'elected_end',
    ];

    protected $casts = [
        'elected_start' => 'date',
        'elected_end' => 'date',
    ];

    // Relationship to region
    public function region()
    {
        return $this->belongsTo(Region::class);
    }

    // Relationship to individual votes
    public function councillorVotes()
    {
        return $this->hasMany(CouncillorVote::class);
    }

    // Helper method to check if councillor is currently serving
    public function isCurrentlyServing()
    {
        $today = now()->startOfDay();
        return $today->gte($this->elected_start) && 
               ($this->elected_end === null || $today->lte($this->elected_end));
    }

    // Helper method to get voting statistics
    public function getVotingStats()
    {
        $votes = $this->councillorVotes;
        
        return [
            'total' => $votes->count(),
            'for' => $votes->where('vote', 'for')->count(),
            'against' => $votes->where('vote', 'against')->count(),
            'abstain' => $votes->where('vote', 'abstain')->count(),
            'absent' => $votes->where('vote', 'absent')->count(),
        ];
    }
}
