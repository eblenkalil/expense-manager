<?php

namespace App\Livewire\Reports;

use App\Mail\ReportSubmittedMail;
use App\Models\Expense;
use App\Models\Report;
use App\Models\User;
use App\Services\ProtocolService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Livewire\Attributes\Validate;
use Livewire\Component;

class CreateReport extends Component
{
    public array  $selectedIds = [];

    #[Validate('required|string|max:255')]
    public string $title = '';

    #[Validate('nullable|string|max:1000')]
    public string $notes = '';

    public function getAvailableExpensesProperty()
    {
        return Expense::with('category')
            ->where('user_id', auth()->id())
            ->where('status', 'available')
            ->orderBy('expense_date', 'desc')
            ->get();
    }

    public function getTotalProperty(): float
    {
        return $this->availableExpenses
            ->whereIn('id', $this->selectedIds)
            ->sum('value');
    }

    public function toggleExpense(int $id): void
    {
        if (in_array($id, $this->selectedIds)) {
            $this->selectedIds = array_values(array_filter(
                $this->selectedIds, fn($v) => $v !== $id
            ));
        } else {
            $this->selectedIds[] = $id;
        }
    }

    public function toggleAll(): void
    {
        $allIds = $this->availableExpenses->pluck('id')->toArray();
        $this->selectedIds = count($this->selectedIds) === count($allIds) ? [] : $allIds;
    }

    public function save(): void
    {
        $this->validate();

        if (empty($this->selectedIds)) {
            $this->addError('selectedIds', 'Selecione ao menos uma despesa.');
            return;
        }

        $expenses = Expense::whereIn('id', $this->selectedIds)
            ->where('user_id', auth()->id())
            ->where('status', 'available')
            ->get();

        if ($expenses->count() !== count($this->selectedIds)) {
            $this->addError('selectedIds', 'Algumas despesas são inválidas.');
            return;
        }

        DB::transaction(function () use ($expenses) {
            $protocol = ProtocolService::generate();

            $report = Report::create([
                'user_id'         => auth()->id(),
                'protocol_number' => $protocol,
                'title'           => $this->title,
                'notes'           => $this->notes,
                'total_value'     => $expenses->sum('value'),
            ]);

            $report->expenses()->attach($expenses->pluck('id'));

            Expense::whereIn('id', $expenses->pluck('id'))
                ->update(['status' => 'locked']);
        });

        $report = Report::where('user_id', auth()->id())->latest()->first();
        session()->flash('success', "Relatório {$report->protocol_number} criado!");
        $this->redirect(route('reports.show', $report));
    }

    public function render()
    {
        return view('livewire.reports.create-report', [
            'availableExpenses' => $this->availableExpenses,
            'total'             => $this->total,
        ])->layout('layouts.app', ['title' => 'Novo Relatório']);
    }
}
