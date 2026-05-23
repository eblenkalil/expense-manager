<?php

namespace App\Livewire\Admin;

use Livewire\Component;

class AdminIndex extends Component
{
    public string $tab = 'reports';

    public function setTab(string $tab): void
    {
        $this->tab = $tab;
    }

    public function render()
    {
        return view('livewire.admin.admin-index')
            ->layout('layouts.app', ['title' => 'Painel Admin']);
    }
}
