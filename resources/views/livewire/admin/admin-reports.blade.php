{{-- resources/views/livewire/admin/admin-reports.blade.php --}}
<div>
  @if(session('success'))
    <div class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3">
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3">
      {{ session('error') }}
    </div>
  @endif

  <div class="bg-white border border-slate-200 rounded-xl p-4 mb-4 flex flex-wrap gap-3">
    <input wire:model.live.debounce.300ms="search" type="text"
           placeholder="Buscar por colaborador, título ou protocolo..."
           class="flex-1 min-w-56 text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
    @foreach(['submitted' => 'Pendentes', 'paid' => 'Pagos', 'rejected' => 'Reprovados', 'all' => 'Todos'] as $val => $label)
      <button wire:click="$set('statusFilter', '{{ $val }}')"
              class="text-sm px-3 py-2 rounded-lg border transition-colors duration-150 ease-out
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
            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors duration-150 ease-out">
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
                <div class="flex items-center justify-end gap-2">
                  @if($r->status === 'submitted')
                    <button wire:click="openPayModal({{ $r->id }})"
                            class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                      Pagar
                    </button>
                    <button wire:click="openRejectModal({{ $r->id }})"
                            class="text-xs border border-red-200 hover:border-red-400 text-red-600 hover:text-red-700 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                      Reprovar
                    </button>
                  @endif
                  <a href="{{ route('reports.show', $r) }}"
                     class="text-xs border border-slate-200 hover:border-blue-300 text-slate-600 hover:text-blue-600 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                    Ver
                  </a>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="px-4 py-3 border-t border-slate-100">{{ $reports->links() }}</div>
    @endif
  </div>

  {{-- Modal: Pagar --}}
  @if($showPayModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closePayModal">
      <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
          <h3 class="text-lg font-semibold">Confirmar Pagamento</h3>
          @php $r = $payingReportId ? \App\Models\Report::with('user')->find($payingReportId) : null; @endphp
          @if($r)
            <p class="text-sm text-slate-400 mt-1">{{ $r->user->name }} · {{ $r->protocol_number }} · R$ {{ number_format($r->total_value, 2, ',', '.') }}</p>
          @endif
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Comprovante de pagamento (opcional)</label>
            <input type="file" wire:model="paymentReceipt"
                   accept="image/jpeg,image/png,image/webp,application/pdf"
                   class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 border border-slate-200 rounded-lg p-1">
            <div wire:loading wire:target="paymentReceipt" class="text-xs text-blue-600 mt-1">Carregando...</div>
            @error('paymentReceipt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button wire:click="closePayModal"
                  class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors duration-150 ease-out">
            Cancelar
          </button>
          <button wire:click="confirmPay" wire:loading.attr="disabled" wire:target="confirmPay"
                  class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 rounded-lg hover:bg-emerald-700 transition-colors disabled:opacity-70">
            <span wire:loading.remove wire:target="confirmPay">✓ Confirmar Pagamento</span>
            <span wire:loading wire:target="confirmPay">Processando...</span>
          </button>
        </div>
      </div>
    </div>
  @endif

  {{-- Modal: Reprovar --}}
  @if($showRejectModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closeRejectModal">
      <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
          <h3 class="text-lg font-semibold">Reprovar Relatório</h3>
          @php $r = $rejectingReportId ? \App\Models\Report::with('user')->find($rejectingReportId) : null; @endphp
          @if($r)
            <p class="text-sm text-slate-400 mt-1">{{ $r->user->name }} · {{ $r->protocol_number }} · R$ {{ number_format($r->total_value, 2, ',', '.') }}</p>
          @endif
        </div>
        <div class="p-6">
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Motivo da reprovação *</label>
          <textarea wire:model="rejectionReason" rows="4"
                    placeholder="Descreva o motivo da reprovação para o colaborador..."
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500/20 focus:border-red-500 resize-none"></textarea>
          @error('rejectionReason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button wire:click="closeRejectModal"
                  class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors duration-150 ease-out">
            Cancelar
          </button>
          <button wire:click="confirmReject" wire:loading.attr="disabled" wire:target="confirmReject"
                  class="px-4 py-2 text-sm font-medium text-white bg-red-600 rounded-lg hover:bg-red-700 transition-colors disabled:opacity-70">
            <span wire:loading.remove wire:target="confirmReject">Reprovar Relatório</span>
            <span wire:loading wire:target="confirmReject">Processando...</span>
          </button>
        </div>
      </div>
    </div>
  @endif
</div>
