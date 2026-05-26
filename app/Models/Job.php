<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    protected $table = 'job_postings';

    protected $fillable = [
        'title', 'position_id', 'description', 'status', 'public_token', 'created_by',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $job) {
            if (empty($job->public_token)) {
                $job->public_token = (string) Str::uuid();
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function candidates()
    {
        return $this->hasMany(Candidate::class);
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->status === 'open' ? 'Aberta' : 'Fechada';
    }

    public function getStatusColorAttribute(): string
    {
        return $this->status === 'open' ? 'emerald' : 'gray';
    }
}
