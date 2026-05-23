<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProfileSettings extends Component
{
    public string $name         = '';
    public string $email        = '';
    public bool   $notify_email = true;

    // Troca de senha
    public string $current_password  = '';
    public string $new_password       = '';
    public string $confirm_password   = '';

    public function mount(): void
    {
        $user               = auth()->user();
        $this->name         = $user->name;
        $this->email        = $user->email;
        $this->notify_email = $user->notify_email;
    }

    public function saveProfile(): void
    {
        $this->validate([
            'name'  => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
        ]);

        auth()->user()->update([
            'name'         => $this->name,
            'email'        => $this->email,
            'notify_email' => $this->notify_email,
        ]);

        session()->flash('profile_success', 'Perfil atualizado!');
    }

    public function savePassword(): void
    {
        $this->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|different:current_password',
            'confirm_password' => 'required|same:new_password',
        ], [
            'new_password.different'  => 'A nova senha deve ser diferente da atual.',
            'confirm_password.same'   => 'As senhas não coincidem.',
        ]);

        if (! Hash::check($this->current_password, auth()->user()->password)) {
            $this->addError('current_password', 'Senha atual incorreta.');
            return;
        }

        auth()->user()->update(['password' => Hash::make($this->new_password)]);
        $this->reset(['current_password', 'new_password', 'confirm_password']);
        session()->flash('password_success', 'Senha alterada com sucesso!');
    }

    public function render()
    {
        return view('livewire.profile.profile-settings')
            ->layout('layouts.app', ['title' => 'Meu Perfil']);
    }
}
