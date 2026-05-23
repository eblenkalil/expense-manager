<div>
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-2xl font-semibold text-slate-900">Meus Relatórios</h2>
      <p class="text-slate-400 mt-1 text-sm">Histórico de entregas de despesas</p>
    </div>
    <a href="{{ route('reports.create') }}"
       class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Novo Relatório
    </a>
  </div>

  {{-- Filters --}}
  <div class="flex flex-wrap gap-3 mb-4">
    <input wire:model.live.debounce.300ms="search" type="text"
           placeholder="Buscar por título ou protocolo..."
           class="flex-1 min-w-48 text-sm border border-slate-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
    @foreach(['all' => 'Todos', 'draft' => 'Rascunhos', 'submitted' => 'Pendentes', 'paid' => 'Pagos'] as $val => $label)
      <button wire:click="$set('statusFilter', '{{ $val }}')"
              class="text-sm px-3 py-2 rounded-lg border transition-colors
                     {{ $statusFilter === $val
                        ? 'bg-blue-600 text-white border-blue-600'
                        : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
        {{ $label }}
      </button>
    @endforeach
  </div>

  <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
    @if($reports->isEmpty())
      <div class="text-center py-16 text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9.414V19a2 2 0 01-2 2z"/>
        </svg>
        <p class="text-sm">Nenhum relatório neste filtro.</p>
      </div>
    @else
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Protocolo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Título</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Despesas</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wide">Total</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Data</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($reports as $r)
            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $r->protocol_number }}</td>
              <td class="px-4 py-3 font-medium">{{ $r->title }}</td>
              <td class="px-4 py-3 font-mono text-slate-500">{{ $r->expenses_count }}</td>
              <td class="px-4 py-3 font-mono font-semibold text-right">R$ {{ number_format($r->total_value, 2, ',', '.') }}</td>
              <td class="px-4 py-3 text-xs text-slate-500">{{ $r->created_at->format('d/m/Y') }}</td>
              <td class="px-4 py-3">
                <x-status-badge :color="$r->status_color">{{ $r->status_label }}</x-status-badge>
              </td>
              <td class="px-4 py-3 text-right">
                <a href="{{ route('reports.show', $r) }}"
                   class="text-xs text-slate-600 hover:text-blue-600 border border-slate-200 hover:border-blue-300 rounded-lg px-3 py-1.5 transition-colors">
                  Ver
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="px-4 py-3 border-t border-slate-100">
        {{ $reports->links() }}
      </div>
    @endif
  </div>
</div>
