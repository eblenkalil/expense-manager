<div>
  {{-- Header --}}
  <div class="flex items-start justify-between mb-6">
    <div>
      <div class="flex items-center gap-3 mb-1">
        <a href="{{ auth()->user()->isAdmin() ? route('admin.index') : route('reports.index') }}"
           class="text-slate-400 hover:text-slate-700 text-sm transition-colors duration-150 ease-out">← Voltar</a>
        <x-status-badge :color="$report->status_color">{{ $report->status_label }}</x-status-badge>
      </div>
      <h2 class="text-2xl font-semibold text-slate-900">{{ $report->title }}</h2>
      <p class="text-slate-400 text-sm mt-1 font-mono">{{ $report->protocol_number }}</p>
      @if(auth()->user()->isAdmin())
        <p class="text-slate-500 text-sm mt-0.5">{{ $report->user->name }} · {{ $report->user->email }}</p>
      @endif
    </div>
    <div class="flex items-center gap-2">
      @if($report->expenses->filter(fn($e) => $e->receipt_path)->isNotEmpty())
        <a href="{{ route('reports.attachments', $report) }}"
           class="inline-flex items-center gap-2 text-sm text-slate-600 border border-slate-200 rounded-lg px-4 py-2 hover:bg-slate-50 transition-colors duration-150 ease-out">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
          </svg>
          Baixar Anexos
        </a>
      @endif
      <a href="{{ route('reports.pdf', $report) }}" target="_blank"
         class="inline-flex items-center gap-2 text-sm text-slate-600 border border-slate-200 rounded-lg px-4 py-2 hover:bg-slate-50 transition-colors duration-150 ease-out">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9.414V19a2 2 0 01-2 2z"/>
        </svg>
        Baixar PDF
      </a>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Expenses table --}}
    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100">
        <h3 class="font-semibold text-slate-900">Despesas incluídas</h3>
      </div>
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-100">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Data</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Descrição</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Categoria</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Recibo</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase">Valor</th>
          </tr>
        </thead>
        <tbody>
          @foreach($report->expenses as $e)
            <tr class="border-b border-slate-100 last:border-0">
              <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $e->expense_date->format('d/m/Y') }}</td>
              <td class="px-4 py-3 font-medium">{{ $e->description ?: '—' }}</td>
              <td class="px-4 py-3">
                <x-status-badge color="blue">{{ $e->category?->name ?? '—' }}</x-status-badge>
              </td>
              <td class="px-4 py-3">
                @if($e->receipt_path)
                  <button wire:click="previewReceipt({{ $e->id }})"
                          class="text-blue-600 hover:underline text-xs flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver
                  </button>
                @else
                  <span class="text-xs text-slate-400">—</span>
                @endif
              </td>
              <td class="px-4 py-3 font-mono font-medium text-right">R$ {{ number_format($e->value, 2, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot class="border-t-2 border-slate-200 bg-slate-50">
          <tr>
            <td colspan="4" class="px-4 py-3 font-semibold text-slate-700">Total</td>
            <td class="px-4 py-3 font-mono font-bold text-xl text-blue-600 text-right">
              R$ {{ number_format($report->total_value, 2, ',', '.') }}
            </td>
          </tr>
        </tfoot>
      </table>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-4">

      {{-- Info card --}}
      <div class="bg-white border border-slate-200 rounded-xl p-5">
        <h3 class="font-semibold text-slate-900 mb-4">Informações</h3>
        <div class="space-y-3 text-sm">
          <div class="flex justify-between">
            <span class="text-slate-500">Protocolo</span>
            <span class="font-mono font-medium text-xs">{{ $report->protocol_number }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Total</span>
            <span class="font-mono font-semibold">R$ {{ number_format($report->total_value, 2, ',', '.') }}</span>
          </div>
          <div class="flex justify-between">
            <span class="text-slate-500">Criado em</span>
            <span class="text-xs">{{ $report->created_at->format('d/m/Y') }}</span>
          </div>
          @if($report->submitted_at)
            <div class="flex justify-between">
              <span class="text-slate-500">Entregue em</span>
              <span class="text-xs">{{ $report->submitted_at->format('d/m/Y') }}</span>
            </div>
          @endif
          @if($report->paid_at)
            <div class="flex justify-between">
              <span class="text-slate-500">Pago em</span>
              <span class="text-xs text-emerald-600 font-medium">{{ $report->paid_at->format('d/m/Y') }}</span>
            </div>
          @endif
        </div>
        @if($report->notes)
          <div class="mt-4 pt-4 border-t border-slate-100">
            <p class="text-xs text-slate-400 mb-1">Observações</p>
            <p class="text-sm text-slate-600">{{ $report->notes }}</p>
          </div>
        @endif
      </div>

      {{-- Submit button (collaborator + draft) --}}
      @if($report->status === 'draft' && $report->user_id === auth()->id())
        <button wire:click="submit"
                wire:confirm="Entregar este relatório para pagamento? Após a entrega, ele não poderá ser editado."
                wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-3 rounded-xl transition-colors duration-150 ease-out">
          <svg class="w-4 h-4" wire:loading.remove wire:target="submit" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
          </svg>
          <span wire:loading.remove wire:target="submit">Entregar Relatório</span>
          <span wire:loading wire:target="submit">Entregando...</span>
        </button>
      @endif

      {{-- Rejection reason --}}
      @if($report->status === 'rejected' && $report->rejection_reason)
        <div class="bg-red-50 border border-red-200 rounded-xl p-4">
          <p class="text-sm font-semibold text-red-800 mb-2">Relatório reprovado</p>
          <p class="text-sm text-red-700">{{ $report->rejection_reason }}</p>
          @if($report->rejected_at)
            <p class="text-xs text-red-400 mt-2">{{ $report->rejected_at->format('d/m/Y H:i') }}</p>
          @endif
        </div>
      @endif

      {{-- Payment receipt --}}
      @if($report->payment_receipt_path)
        <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4">
          <p class="text-sm font-semibold text-emerald-800 mb-2">✓ Comprovante de pagamento</p>
          <a href="{{ $report->payment_receipt_url }}" target="_blank"
             class="text-sm text-emerald-700 hover:underline truncate block">
            {{ $report->payment_receipt_name ?? 'Ver comprovante' }}
          </a>
        </div>
      @endif

      {{-- Admin: mark as paid --}}
      @if(auth()->user()->isAdmin() && $report->status === 'submitted')
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
          <p class="text-sm font-semibold text-amber-900 mb-3">Confirmar Pagamento</p>
          <div class="space-y-3">
            <div>
              <label class="block text-xs font-medium text-amber-800 mb-1.5">Comprovante (opcional)</label>
              <input type="file" wire:model="paymentReceipt"
                     accept="image/jpeg,image/png,image/webp,application/pdf"
                     class="w-full text-xs text-amber-700 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-medium file:bg-amber-100 file:text-amber-800">
              <div wire:loading wire:target="paymentReceipt" class="text-xs text-amber-700 mt-1">Carregando...</div>
            </div>
            <button wire:click="markAsPaid"
                    wire:confirm="Confirmar o pagamento deste relatório?"
                    wire:loading.attr="disabled"
                    class="w-full flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors duration-150 ease-out">
              <span wire:loading.remove wire:target="markAsPaid">✓ Marcar como Pago</span>
              <span wire:loading wire:target="markAsPaid">Processando...</span>
            </button>
          </div>
        </div>
      @endif

    </div>
  </div>

  {{-- Preview modal --}}
  @if($showPreview)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closePreview">
      <div class="bg-white rounded-2xl overflow-hidden shadow-2xl max-w-3xl w-full max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
          <h3 class="font-semibold">Recibo</h3>
          <button wire:click="closePreview" class="text-slate-400 hover:text-slate-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>
        <div class="flex-1 overflow-auto p-4">
          @if($previewType === 'image')
            <img src="{{ $previewUrl }}" alt="Recibo" class="max-w-full mx-auto rounded-lg">
          @else
            <iframe src="{{ $previewUrl }}" class="w-full h-[70vh] rounded-lg border border-slate-200"></iframe>
          @endif
        </div>
      </div>
    </div>
  @endif
</div>
