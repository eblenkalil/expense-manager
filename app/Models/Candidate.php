<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Candidate extends Model
{
    protected $fillable = [
        'job_id', 'name', 'cpf', 'email', 'phone', 'linkedin',
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
            'pending'          => 'Aguardando',
            'interview'        => '1ª Entrevista',
            'second_interview' => '2ª Entrevista',
            'hired'            => 'Contratado',
            'discarded'        => 'Descartado',
            default            => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'          => 'amber',
            'interview'        => 'blue',
            'second_interview' => 'purple',
            'hired'            => 'emerald',
            'discarded'        => 'slate',
            default            => 'gray',
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

    public static function formatCpf(string $cpf): string
    {
        $digits = preg_replace('/\D/', '', $cpf);

        return strlen($digits) === 11
            ? substr($digits, 0, 3).'.'.substr($digits, 3, 3).'.'.substr($digits, 6, 3).'-'.substr($digits, 9, 2)
            : $cpf;
    }

    public static function isValidCpf(string $cpf): bool
    {
        $d = preg_replace('/\D/', '', $cpf);

        if (strlen($d) !== 11 || preg_match('/^(\d)\1{10}$/', $d)) {
            return false;
        }

        for ($t = 9; $t < 11; $t++) {
            $sum = 0;
            for ($i = 0; $i < $t; $i++) {
                $sum += (int) $d[$i] * ($t + 1 - $i);
            }
            $rem = (10 * $sum) % 11;
            if ((int) $d[$t] !== ($rem === 10 ? 0 : $rem)) {
                return false;
            }
        }

        return true;
    }
}
