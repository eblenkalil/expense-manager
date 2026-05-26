<div class="min-h-screen bg-slate-50">
    <div class="mx-auto w-full max-w-7xl px-4 py-8 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-8">
            <h1 class="text-2xl font-semibold text-slate-900">Olá, {{ auth()->user()->name }} 👋</h1>
            <p class="mt-1 text-sm text-slate-500">Aqui está o resumo das suas despesas.</p>
        </div>

        {{-- Stats --}}
        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <p class="text-sm font-medium text-slate-500">Despesas disponíveis</p>
                <p class="mt-2 text-2xl font-semibold font-mono text-slate-900">{{ $stats['available_expenses'] }}</p>
                <p class="mt-1 text-xs text-slate-400">prontas para agrupar</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <p class="text-sm font-medium text-slate-500">Total a reembolsar</p>
                <p class="mt-2 text-2xl font-semibold font-mono text-blue-600">
                    R$ {{ number_format($stats['available_total'], 2, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-400">despesas disponíveis</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <p class="text-sm font-medium text-slate-500">Relatórios pendentes</p>
                <p class="mt-2 text-2xl font-semibold font-mono text-amber-500">{{ $stats['pending_reports'] }}</p>
                <p class="mt-1 text-xs text-slate-400">aguardando pagamento</p>
            </div>

            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <p class="text-sm font-medium text-slate-500">Total recebido</p>
                <p class="mt-2 text-2xl font-semibold font-mono text-emerald-600">
                    R$ {{ number_format($stats['paid_total'], 2, ',', '.') }}
                </p>
                <p class="mt-1 text-xs text-slate-400">relatórios pagos</p>
            </div>

        </div>

        {{-- Listas recentes --}}
        <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">

            {{-- Últimas despesas --}}
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Últimas despesas</h3>
                    <a href="{{ route('expenses.index') }}"
                       class="text-sm font-medium text-blue-600 transition duration-150 ease-out hover:text-blue-700 hover:underline">
                        Ver todas
                    </a>
                </div>

                @forelse($recentExpenses as $e)
                    <div class="flex items-center justify-between border-b border-slate-100 py-3 last:border-0">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900">{{ $e->description }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">
                                {{ $e->category?->name }} · {{ $e->expense_date->format('d/m/Y') }}
                            </p>
                        </div>
                        <div class="ml-4 flex shrink-0 items-center gap-3">
                            <span class="font-mono text-sm font-medium text-slate-900">
                                R$ {{ number_format($e->value, 2, ',', '.') }}
                            </span>
                            <x-status-badge :color="$e->status_color">{{ $e->status_label }}</x-status-badge>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-10">
                        <p class="text-sm text-slate-400">Nenhuma despesa cadastrada.</p>
                    </div>
                @endforelse
            </div>

            {{-- Últimos relatórios --}}
            <div class="rounded-xl border border-slate-200 bg-white p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-slate-900">Últimos relatórios</h3>
                    <a href="{{ route('reports.index') }}"
                       class="text-sm font-medium text-blue-600 transition duration-150 ease-out hover:text-blue-700 hover:underline">
                        Ver todos
                    </a>
                </div>

                @forelse($recentReports as $r)
                    <a href="{{ route('reports.show', $r) }}"
                       class="-mx-2 flex items-center justify-between rounded-lg border-b border-slate-100 px-2 py-3 last:border-0 transition duration-150 ease-out hover:bg-slate-50">
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900">{{ $r->title }}</p>
                            <p class="mt-0.5 font-mono text-xs text-slate-400">{{ $r->protocol_number }}</p>
                        </div>
                        <div class="ml-4 flex shrink-0 items-center gap-3">
                            <span class="font-mono text-sm font-medium text-slate-900">
                                R$ {{ number_format($r->total_value, 2, ',', '.') }}
                            </span>
                            <x-status-badge :color="$r->status_color">{{ $r->status_label }}</x-status-badge>
                        </div>
                    </a>
                @empty
                    <div class="flex flex-col items-center justify-center py-10">
                        <p class="text-sm text-slate-400">Nenhum relatório criado.</p>
                    </div>
                @endforelse
            </div>

        </div>

        {{-- Gráfico --}}
        <div class="rounded-xl border border-slate-200 bg-white p-6">
            <h3 class="mb-4 text-lg font-semibold text-slate-900">Despesas reembolsadas por mês</h3>
            <canvas id="expenseChart" height="80"></canvas>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const data = @json($chartData);
    new Chart(document.getElementById('expenseChart'), {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Total (R$)',
                data: data.values,
                backgroundColor: '#dbeafe',
                borderColor: '#3b82f6',
                borderWidth: 2,
                borderRadius: 6,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { display: false } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        color: '#94a3b8',
                        callback: v => 'R$ ' + v.toLocaleString('pt-BR', { minimumFractionDigits: 0 })
                    },
                    grid: { color: '#f1f5f9' }
                },
                x: {
                    ticks: { color: '#94a3b8' },
                    grid: { display: false }
                }
            }
        }
    });
});
</script>
