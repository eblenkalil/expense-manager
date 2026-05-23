<div>
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-2xl font-semibold text-slate-900">Novo Relatório</h2>
      <p class="text-slate-400 mt-1 text-sm">Selecione as despesas e defina o título</p>
    </div>
    <a href="{{ route('reports.index') }}"
       class="text-sm text-slate-500 hover:text-slate-900 border border-slate-200 rounded-lg px-4 py-2 transition-colors">
      ← Voltar
    </a>
  </div>

  @error('selectedIds')
    <div class="mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-lg px-4 py-3">{{ $message }}</div>
  @enderror

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Expense selection --}}
    <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl overflow-hidden">
      <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
        <h3 class="font-semibold text-slate-900">Despesas disponíveis</h3>
        <button wire:click="toggleAll"
                class="text-xs text-blue-600 hover:underline">
          {{ count($selectedIds) === count($availableExpenses) ? 'Desmarcar todas' : 'Selecionar todas' }}
        </button>
      </div>

      @if($availableExpenses->isEmpty())
        <div class="text-center py-12 text-slate-400 text-sm">
          <p>Nenhuma despesa disponível.
            <a href="{{ route('expenses.index') }}" class="text-blue-600 underline">Cadastre despesas primeiro.</a>
          </p>
        </div>
      @else
        <table class="w-full text-sm">
          <thead class="bg-slate-50 border-b border-slate-100">
            <tr>
              <th class="w-10 px-4 py-3"></th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Data</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Descrição</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Categoria</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase">Valor</th>
            </tr>
          </thead>
          <tbody>
            @foreach($availableExpenses as $e)
              <tr wire:click="toggleExpense({{ $e->id }})"
                  class="border-b border-slate-100 last:border-0 cursor-pointer transition-colors
                         {{ in_array($e->id, $selectedIds) ? 'bg-blue-50' : 'hover:bg-slate-50' }}">
                <td class="px-4 py-3">
                  <input type="checkbox" readonly
                         {{ in_array($e->id, $selectedIds) ? 'checked' : '' }}
                         class="rounded border-slate-300 text-blue-600 pointer-events-none">
                </td>
                <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $e->expense_date->format('d/m/Y') }}</td>
                <td class="px-4 py-3 font-medium">{{ $e->description ?: '—' }}</td>
                <td class="px-4 py-3">
                  <x-status-badge color="blue">{{ $e->category?->name ?? '—' }}</x-status-badge>
                </td>
                <td class="px-4 py-3 font-mono font-medium text-right">R$ {{ number_format($e->value, 2, ',', '.') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      @endif
    </div>

    {{-- Summary + form --}}
    <div class="space-y-4">

      <div class="bg-white border border-slate-200 rounded-xl p-5">
        <h3 class="font-semibold text-slate-900 mb-4">Resumo</h3>
        <div class="space-y-2 text-sm">
          <div class="flex justify-between">
            <span class="text-slate-500">Selecionadas</span>
            <span class="font-mono font-semibold">{{ count($selectedIds) }}</span>
          </div>
          <div class="flex justify-between pt-2 border-t border-slate-100">
            <span class="text-slate-500">Total</span>
            <span class="font-mono font-bold text-blue-600 text-lg">
              R$ {{ number_format($total, 2, ',', '.') }}
            </span>
          </div>
        </div>
      </div>

      <div class="bg-white border border-slate-200 rounded-xl p-5 space-y-4">
        <h3 class="font-semibold text-slate-900">Dados do relatório</h3>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Título *</label>
          <input type="text" wire:model="title" placeholder="Ex: Despesas Outubro 2025"
                 class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Observações</label>
          <textarea wire:model="notes" rows="3" placeholder="Observações opcionais..."
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"></textarea>
        </div>
        <button wire:click="save"
                wire:loading.attr="disabled"
                @if(empty($selectedIds) || !$title) disabled @endif
                class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
          <svg class="w-4 h-4" wire:loading.remove wire:target="save" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9.414V19a2 2 0 01-2 2z"/>
          </svg>
          <span wire:loading.remove wire:target="save">Criar Relatório</span>
          <span wire:loading wire:target="save">Criando...</span>
        </button>
      </div>

    </div>
  </div>
</div>
