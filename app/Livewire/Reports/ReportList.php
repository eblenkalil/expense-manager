<?php

namespace App\Livewire\Reports;

use App\Models\Report;
use Livewire\Component;
use Livewire\WithPagination;

class ReportList extends Component
{
    use WithPagination;

    public string $statusFilter = 'all';
    public string $search       = '';

    public function updatingStatusFilter(): void { $this->resetPage(); }
    public function updatingSearch(): void       { $this->resetPage(); }

    public function getReportsProperty()
    {
        return Report::withCount('expenses')
            ->where('user_id', auth()->id())
            ->when($this->statusFilter !== 'all', fn($q) =>
                $q->where('status', $this->statusFilter)
            )
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('title', 'like', "%{$this->search}%")
                       ->orWhere('protocol_number', 'like', "%{$this->search}%")
                )
            )
            ->latest()
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.reports.report-list', [
            'reports' => $this->reports,
        ])->layout('layouts.app', ['title' => 'Meus Relatórios']);
    }
}
