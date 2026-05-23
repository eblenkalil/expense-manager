<?php

namespace App\Livewire\Admin;

use App\Models\Category;
use Livewire\Attributes\Validate;
use Livewire\Component;

class AdminCategories extends Component
{
    public bool   $showModal  = false;
    public ?int   $editingId  = null;

    #[Validate('required|string|max:100')]
    public string $name = '';

    public function openCreate(): void
    {
        $this->editingId = null;
        $this->name      = '';
        $this->resetValidation();
        $this->showModal = true;
    }

    public function openEdit(int $id): void
    {
        $category        = Category::findOrFail($id);
        $this->editingId = $id;
        $this->name      = $category->name;
        $this->resetValidation();
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editingId) {
            Category::findOrFail($this->editingId)->update(['name' => $this->name]);
            $msg = 'Categoria atualizada!';
        } else {
            Category::create(['name' => $this->name]);
            $msg = 'Categoria criada!';
        }

        $this->showModal = false;
        session()->flash('success', $msg);
    }

    public function toggleActive(int $id): void
    {
        $cat = Category::findOrFail($id);
        $cat->update(['active' => ! $cat->active]);
    }

    public function render()
    {
        return view('livewire.admin.admin-categories', [
            'categories' => Category::orderBy('name')->get(),
        ]);
    }
}
