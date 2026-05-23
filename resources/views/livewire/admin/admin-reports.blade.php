{{-- resources/views/livewire/admin/admin-reports.blade.php --}}
<div>
  <div class="flex flex-wrap gap-3 mb-4">
    <input wire:model.live.debounce.300ms="search" type="text"
           placeholder="Buscar por colaborador, título ou protocolo..."
           class="flex-1 min-w-56 text-sm border border-slate-200 rounded-lg px-3 py-2 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500">
    @foreach(['submitted' => 'Pendentes', 'paid' => 'Pagos', 'all' => 'Todos'] as $val => $label)
      <button wire:click="$set('statusFilter', '{{ $val }}')"
              class="text-sm px-3 py-2 rounded-lg border transition-colors
                     {{ $statusFilter === $val ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:border-slate-300' }}">
        {{ $label }}
      </button>
    @endforeach
  </div>

  <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
    @if($reports->isEmpty())
      <p class="text-center py-12 text-slate-400 text-sm">Nenhum relatório encontrado.</p>
    @else
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Protocolo</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Colaborador</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Título</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Itens</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase">Total</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Entregue</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Status</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($reports as $r)
            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $r->protocol_number }}</td>
              <td class="px-4 py-3">
                <p class="font-medium">{{ $r->user->name }}</p>
                <p class="text-xs text-slate-400">{{ $r->user->email }}</p>
              </td>
              <td class="px-4 py-3 font-medium">{{ $r->title }}</td>
              <td class="px-4 py-3 font-mono text-slate-500">{{ $r->expenses_count }}</td>
              <td class="px-4 py-3 font-mono font-semibold text-right">R$ {{ number_format($r->total_value, 2, ',', '.') }}</td>
              <td class="px-4 py-3 text-xs text-slate-500">{{ $r->submitted_at?->format('d/m/Y') ?? '—' }}</td>
              <td class="px-4 py-3"><x-status-badge :color="$r->status_color">{{ $r->status_label }}</x-status-badge></td>
              <td class="px-4 py-3 text-right">
                <a href="{{ route('reports.show', $r) }}"
                   class="text-xs border border-slate-200 hover:border-blue-300 text-slate-600 hover:text-blue-600 rounded-lg px-3 py-1.5 transition-colors">
                  Ver
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="px-4 py-3 border-t border-slate-100">{{ $reports->links() }}</div>
    @endif
  </div>
</div>
