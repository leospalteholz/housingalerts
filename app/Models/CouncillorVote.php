<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouncillorVote extends Model
{
    protected $fillable = [
        'hearing_vote_id',
        'councillor_id',
        'vote',
    ];

    // Relationship to hearing vote
    public function hearingVote()
    {
        return $this->belongsTo(HearingVote::class);
    }

    // Relationship to councillor
    public function councillor()
    {
        return $this->belongsTo(Councillor::class);
    }

    // Helper method to get vote label
    public function getVoteLabelAttribute()
    {
        return match($this->vote) {
            'for' => 'For',
            'against' => 'Against',
            'abstain' => 'Abstain',
            'absent' => 'Absent',
            default => 'Unknown',
        };
    }

    // Helper method to get vote color (for UI)
    public function getVoteColorAttribute()
    {
        return match($this->vote) {
            'for' => 'green',
            'against' => 'red',
            'abstain' => 'yellow',
            'absent' => 'gray',
            default => 'gray',
        };
    }
}
