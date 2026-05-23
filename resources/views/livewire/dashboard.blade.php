<div>
  {{-- Header --}}
  <div class="flex items-center justify-between mb-8">
    <div>
      <h2 class="text-2xl font-semibold text-slate-900">
        Olá, {{ explode(' ', auth()->user()->name)[0] }} 👋
      </h2>
      <p class="text-slate-400 mt-1 text-sm">Aqui está o resumo das suas despesas</p>
    </div>
    <a href="{{ route('expenses.index') }}?new=1"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Nova Despesa
    </a>
  </div>

  {{-- Stats --}}
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white border border-slate-200 rounded-xl p-5">
      <p class="text-2xl font-semibold font-mono text-slate-900">{{ $stats['available_expenses'] }}</p>
      <p class="text-sm text-slate-400 mt-1">Despesas disponíveis</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-5">
      <p class="text-2xl font-semibold font-mono text-blue-600">
        R$ {{ number_format($stats['available_total'], 2, ',', '.') }}
      </p>
      <p class="text-sm text-slate-400 mt-1">Total a reembolsar</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-5">
      <p class="text-2xl font-semibold font-mono text-amber-600">{{ $stats['pending_reports'] }}</p>
      <p class="text-sm text-slate-400 mt-1">Relatórios pendentes</p>
    </div>
    <div class="bg-white border border-slate-200 rounded-xl p-5">
      <p class="text-2xl font-semibold font-mono text-emerald-600">
        R$ {{ number_format($stats['paid_total'], 2, ',', '.') }}
      </p>
      <p class="text-sm text-slate-400 mt-1">Total recebido</p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

    {{-- Recent expenses --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-900">Últimas despesas</h3>
        <a href="{{ route('expenses.index') }}" class="text-sm text-blue-600 hover:underline">Ver todas</a>
      </div>
      @forelse($recentExpenses as $e)
        <div class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0">
          <div class="min-w-0 flex-1">
            <p class="text-sm font-medium truncate">{{ $e->description ?: $e->category?->name }}</p>
            <p class="text-xs text-slate-400">{{ $e->expense_date->format('d/m/Y') }} · {{ $e->category?->name }}</p>
          </div>
          <div class="flex items-center gap-3 ml-3 flex-shrink-0">
            <span class="text-sm font-mono font-medium">R$ {{ number_format($e->value, 2, ',', '.') }}</span>
            <x-status-badge :color="$e->status_color">{{ $e->status_label }}</x-status-badge>
          </div>
        </div>
      @empty
        <p class="text-sm text-slate-400 py-6 text-center">Nenhuma despesa cadastrada.</p>
      @endforelse
    </div>

    {{-- Recent reports --}}
    <div class="bg-white border border-slate-200 rounded-xl p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="font-semibold text-slate-900">Últimos relatórios</h3>
        <a href="{{ route('reports.index') }}" class="text-sm text-blue-600 hover:underline">Ver todos</a>
      </div>
      @forelse($recentReports as $r)
        <a href="{{ route('reports.show', $r) }}"
           class="flex items-center justify-between py-2.5 border-b border-slate-50 last:border-0 -mx-2 px-2 rounded hover:bg-slate-50 transition-colors">
          <div class="min-w-0 flex-1">
            <p class="text-sm font-medium truncate">{{ $r->title }}</p>
            <p class="text-xs font-mono text-slate-400">{{ $r->protocol_number }}</p>
          </div>
          <div class="flex items-center gap-3 ml-3 flex-shrink-0">
            <span class="text-sm font-mono font-medium">R$ {{ number_format($r->total_value, 2, ',', '.') }}</span>
            <x-status-badge :color="$r->status_color">{{ $r->status_label }}</x-status-badge>
          </div>
        </a>
      @empty
        <p class="text-sm text-slate-400 py-6 text-center">Nenhum relatório criado.</p>
      @endforelse
    </div>

  </div>

  {{-- Chart --}}
  <div class="bg-white border border-slate-200 rounded-xl p-6">
    <h3 class="font-semibold text-slate-900 mb-4">Despesas reembolsadas por mês</h3>
    <canvas id="expenseChart" height="90"></canvas>
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
            backgroundColor: '#eff6ff',
            borderColor: '#1a56db',
            borderWidth: 2,
            borderRadius: 6,
          }]
        },
        options: {
          responsive: true,
          plugins: { legend: { display: false } },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                callback: v => 'R$ ' + v.toLocaleString('pt-BR', { minimumFractionDigits: 0 })
              },
              grid: { color: '#f1f5f9' }
            },
            x: { grid: { display: false } }
          }
        }
      });
    });
  </script>
</div>
