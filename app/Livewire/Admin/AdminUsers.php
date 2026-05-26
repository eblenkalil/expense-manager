<?php

namespace App\Livewire\Admin;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AdminUsers extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|min:8|confirmed')]
    public ?string $password = null;

    public ?string $password_confirmation = null;

    public array $selectedRoles = ['collaborator'];

    public static array $availableRoles = [
        'collaborator' => 'Colaborador',
        'admin' => 'Administrador',
        'hr' => 'RH',
        'financial' => 'Financeiro',
    ];

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->email = '';
        $this->password = null;
        $this->password_confirmation = null;
        $this->selectedRoles = ['collaborator'];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $userId): void
    {
        $user = User::findOrFail($userId);
        $this->editingId = $userId;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = null;
        $this->password_confirmation = null;
        $this->selectedRoles = $user->roles ?? ['collaborator'];
        $this->resetValidation();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
    }

    public function save(): void
    {
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email'.($this->editingId ? ",{$this->editingId}" : ''),
            'selectedRoles' => 'required|array|min:1',
            'selectedRoles.*' => 'in:admin,collaborator,hr,financial',
        ];

        if (! $this->editingId) {
            $rules['password'] = 'required|string|min:8|confirmed';
        } elseif ($this->password) {
            $rules['password'] = 'nullable|string|min:8|confirmed';
        }

        $this->validate($rules);

        if ($this->editingId) {
            $user = User::findOrFail($this->editingId);
            $data = ['name' => $this->name, 'email' => $this->email, 'roles' => $this->selectedRoles];
            if ($this->password) {
                $data['password'] = Hash::make($this->password);
            }
            $user->update($data);
            $message = "Usuário {$user->name} atualizado.";
        } else {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'roles' => $this->selectedRoles,
            ]);
            $message = "Usuário {$user->name} criado com sucesso.";
        }

        $this->closeModal();
        session()->flash('success', $message);
    }

    public function delete(int $userId): void
    {
        if ($userId === auth()->id()) {
            return;
        }

        $user = User::findOrFail($userId);
        $user->delete();
        session()->flash('success', "Usuário {$user->name} removido.");
    }

    public function render()
    {
        return view('livewire.admin.admin-users', [
            'users' => User::orderBy('name')->get(),
            'availableRoles' => self::$availableRoles,
        ]);
    }
}
