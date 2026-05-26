<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CandidateEvent extends Model
{
    protected $fillable = [
        'candidate_id', 'user_id', 'type', 'content',
        'previous_status', 'new_status', 'rating',
    ];

    public function candidate()
    {
        return $this->belongsTo(Candidate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
