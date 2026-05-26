<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'job_id', 'name', 'email', 'phone', 'linkedin',
        'salary_expectation', 'cv_path', 'notes', 'status', 'source', 'created_by',
    ];

    protected $casts = [
        'salary_expectation' => 'decimal:2',
    ];

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function events()
    {
        return $this->hasMany(CandidateEvent::class)->orderByDesc('created_at');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Aguardando',
            'interview' => 'Em Entrevista',
            'hired' => 'Contratado',
            'discarded' => 'Descartado',
            default => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'interview' => 'blue',
            'hired' => 'emerald',
            'discarded' => 'slate',
            default => 'gray',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return $this->source === 'public_form' ? 'Formulário Público' : 'Cadastro Manual';
    }

    public function getCvUrlAttribute(): ?string
    {
        return $this->cv_path ? asset('storage/'.$this->cv_path) : null;
    }
}
