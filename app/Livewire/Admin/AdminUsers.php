<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Livewire\Component;

class AdminUsers extends Component
{
    public function toggleRole(int $userId): void
    {
        if ($userId === auth()->id()) return;

        $user    = User::findOrFail($userId);
        $newRole = $user->role === 'admin' ? 'collaborator' : 'admin';
        $user->update(['role' => $newRole]);

        session()->flash('success', "Perfil de {$user->name} alterado para " .
            ($newRole === 'admin' ? 'Administrador' : 'Colaborador') . '.');
    }

    public function render()
    {
        return view('livewire.admin.admin-users', [
            'users' => User::orderBy('name')->get(),
        ]);
    }
}
