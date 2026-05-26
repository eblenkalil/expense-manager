<?php

namespace App\Livewire;

use App\Models\Expense;
use App\Models\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class Dashboard extends Component
{
    public function getStatsProperty(): array
    {
        $userId = auth()->id();

        return [
            'available_expenses' => Expense::where('user_id', $userId)->where('status', 'available')->count(),
            'available_total' => Expense::where('user_id', $userId)->where('status', 'available')->sum('value'),
            'pending_reports' => Report::where('user_id', $userId)->where('status', 'submitted')->count(),
            'paid_total' => Report::where('user_id', $userId)->where('status', 'paid')->sum('total_value'),
        ];
    }

    public function getRecentExpensesProperty()
    {
        return Expense::with('category')
            ->where('user_id', auth()->id())
            ->whereIn('status', ['available', 'locked'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getRecentReportsProperty()
    {
        return Report::where('user_id', auth()->id())
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getChartDataProperty(): array
    {
        Carbon::setLocale('pt_BR');

        $months = collect(range(5, 0))->map(function ($m) {
            $date = now()->subMonths($m);

            return [
                'label' => $date->translatedFormat('M/y'),
                'year' => $date->year,
                'month' => $date->month,
            ];
        });

        $data = Expense::where('user_id', auth()->id())
            ->where('status', 'archived')
            ->select(
                DB::raw('YEAR(expense_date) as year'),
                DB::raw('MONTH(expense_date) as month'),
                DB::raw('SUM(value) as total')
            )
            ->groupBy('year', 'month')
            ->get()
            ->keyBy(fn ($r) => "{$r->year}-{$r->month}");

        return [
            'labels' => $months->pluck('label')->toArray(),
            'values' => $months->map(fn ($m) => (float) ($data["{$m['year']}-{$m['month']}"]->total ?? 0)
            )->toArray(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard', [
            'stats' => $this->stats,
            'recentExpenses' => $this->recentExpenses,
            'recentReports' => $this->recentReports,
            'chartData' => $this->chartData,
        ])->layout('layouts.app', ['title' => 'Dashboard']);
    }
}
