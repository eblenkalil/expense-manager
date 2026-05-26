<?php

namespace App\Livewire\Admin;

use App\Models\Position;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AdminPositions extends Component
{
    public bool $showModal = false;

    public ?int $editingId = null;

    #[Validate('required|string|max:100')]
    public string $name = '';

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $position = Position::findOrFail($id);
        $this->editingId = $id;
        $this->name = $position->name;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            Position::findOrFail($this->editingId)->update(['name' => $this->name]);
            $msg = 'Cargo atualizado!';
        } else {
            Position::create(['name' => $this->name]);
            $msg = 'Cargo criado!';
        }

        $this->showModal = false;
        session()->flash('success', $msg);
    }

    public function toggleActive(int $id): void
    {
        $pos = Position::findOrFail($id);
        $pos->update(['active' => ! $pos->active]);
    }

    public function render()
    {
        return view('livewire.admin.admin-positions', [
            'positions' => Position::orderBy('name')->get(),
        ]);
    }
}
