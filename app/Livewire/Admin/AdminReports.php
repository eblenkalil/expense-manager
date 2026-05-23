<?php

namespace App\Livewire\Admin;

use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;

class AdminReports extends Component
{
    use WithPagination;

    public string $statusFilter = 'submitted';
    public string $search       = '';

    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingSearch(): void       { $this->resetPage(); }

    public function getReportsProperty()
    {
        return Report::with('user')
            ->withCount('expenses')
            ->when($this->statusFilter !== 'all', fn($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('title', 'like', "%{$this->search}%")
                       ->orWhere('protocol_number', 'like', "%{$this->search}%")
                       ->orWhereHas('user', fn($u) =>
                           $u->where('name', 'like', "%{$this->search}%")
                       )
                )
            )
            ->latest('submitted_at')
            ->paginate(20);
    }

    public function render()
    {
        return view('livewire.admin.admin-reports', [
            'reports' => $this->reports,
        ]);
    }
}
