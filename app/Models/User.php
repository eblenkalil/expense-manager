<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'roles', 'notify_email',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'notify_email' => 'boolean',
            'roles' => 'array',
        ];
    }

    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles ?? []);
    }

    public function hasAnyRole(array $roles): bool
    {
        return count(array_intersect($roles, $this->roles ?? [])) > 0;
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function isHr(): bool
    {
        return $this->hasRole('hr');
    }

    public function isFinancial(): bool
    {
        return $this->hasRole('financial');
    }

    public function getRoleLabelAttribute(): string
    {
        $labels = [
            'admin' => 'Administrador',
            'collaborator' => 'Colaborador',
            'hr' => 'RH',
            'financial' => 'Financeiro',
        ];

        $names = collect($this->roles ?? [])
            ->map(fn ($r) => $labels[$r] ?? $r)
            ->implode(', ');

        return $names ?: 'Sem perfil';
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }
}
