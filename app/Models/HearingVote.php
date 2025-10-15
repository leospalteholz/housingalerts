<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HearingVote extends Model
{
    protected $fillable = [
        'hearing_id',
        'vote_date',
        'passed',
        'notes',
    ];

    protected $casts = [
        'vote_date' => 'date',
        'passed' => 'boolean',
    ];

    // Relationship to hearing
    public function hearing()
    {
        return $this->belongsTo(Hearing::class);
    }

    // Relationship to individual councillor votes
    public function councillorVotes()
    {
        return $this->hasMany(CouncillorVote::class);
    }

    // Helper method to get vote tallies
    public function getTallies()
    {
        $votes = $this->councillorVotes;
        
        return [
            'for' => $votes->where('vote', 'for')->count(),
            'against' => $votes->where('vote', 'against')->count(),
            'abstain' => $votes->where('vote', 'abstain')->count(),
            'absent' => $votes->where('vote', 'absent')->count(),
        ];
    }

    // Helper method to get formatted vote result
    public function getVoteResultAttribute()
    {
        if ($this->passed === null) {
            return 'Pending';
        }
        return $this->passed ? 'Passed' : 'Failed';
    }
}
