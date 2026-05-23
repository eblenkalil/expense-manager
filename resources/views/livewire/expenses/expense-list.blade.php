<div>
  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-2xl font-semibold text-slate-900">Minhas Despesas</h2>
      <p class="text-slate-400 mt-1 text-sm">Gerencie seus comprovantes</p>
    </div>
    <button wire:click="openModal"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Nova Despesa
    </button>
  </div>

  {{-- Filters --}}
  <div class="bg-white border border-slate-200 rounded-xl p-4 mb-4 flex flex-wrap gap-3">
    <input wire:model.live.debounce.300ms="search" type="text"
           placeholder="Buscar descrição..."
           class="flex-1 min-w-40 text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    <select wire:model.live="categoryFilter"
            class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
      <option value="">Todas as categorias</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
      @endforeach
    </select>
    <input wire:model.live="dateFrom" type="date"
           class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
    <input wire:model.live="dateTo" type="date"
           class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
  </div>

  {{-- Table --}}
  <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
    @if($expenses->isEmpty())
      <div class="text-center py-16 text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
            d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/>
        </svg>
        <p class="text-sm">Nenhuma despesa encontrada.
          <button wire:click="openModal" class="text-blue-600 underline">Cadastre a primeira!</button>
        </p>
      </div>
    @else
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Data</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Descrição</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Categoria</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Recibo</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wide">Valor</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($expenses as $e)
            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
              <td class="px-4 py-3 font-mono text-xs text-slate-500">{{ $e->expense_date->format('d/m/Y') }}</td>
              <td class="px-4 py-3 font-medium max-w-xs truncate">{{ $e->description ?: '—' }}</td>
              <td class="px-4 py-3">
                <x-status-badge color="blue">{{ $e->category?->name ?? '—' }}</x-status-badge>
              </td>
              <td class="px-4 py-3">
                @if($e->receipt_path)
                  <button wire:click="preview({{ $e->id }})"
                          class="text-blue-600 hover:underline text-xs flex items-center gap-1">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    Ver
                  </button>
                @else
                  <span class="text-slate-400 text-xs">—</span>
                @endif
              </td>
              <td class="px-4 py-3 font-mono font-medium text-right">R$ {{ number_format($e->value, 2, ',', '.') }}</td>
              <td class="px-4 py-3">
                <x-status-badge :color="$e->status_color">{{ $e->status_label }}</x-status-badge>
              </td>
              <td class="px-4 py-3 text-right">
                @if($e->isAvailable())
                  <button wire:click="delete({{ $e->id }})"
                          wire:confirm="Excluir esta despesa?"
                          class="text-xs text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 rounded-lg px-2.5 py-1 transition-colors">
                    Excluir
                  </button>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="px-4 py-3 border-t border-slate-100">
        {{ $expenses->links() }}
      </div>
    @endif
  </div>

  {{-- Modal nova despesa --}}
  @if($showModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
          <h3 class="text-lg font-semibold">Nova Despesa</h3>
          <p class="text-sm text-slate-400 mt-1">Preencha os dados do comprovante</p>
        </div>
        <div class="p-6 space-y-4">

          {{-- Upload --}}
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Recibo (opcional)</label>
            <input type="file" wire:model="receipt"
                   accept="image/jpeg,image/png,image/webp,application/pdf"
                   class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-lg p-1">
            <div wire:loading wire:target="receipt" class="text-xs text-blue-600 mt-1">Carregando...</div>
            @error('receipt') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          {{-- Date + Value --}}
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-sm font-medium text-slate-600 mb-1.5">Data *</label>
              <input type="date" wire:model="expense_date"
                     class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
              @error('expense_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-600 mb-1.5">Valor (R$) *</label>
              <input type="number" step="0.01" min="0" wire:model="value" placeholder="0,00"
                     class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
              @error('value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
          </div>

          {{-- Category --}}
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Categoria *</label>
            <select wire:model="category_id"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
              <option value="">Selecione...</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
            @error('category_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          {{-- Description --}}
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Descrição</label>
            <input type="text" wire:model="description" placeholder="Ex: Almoço com cliente..."
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
          </div>
        </div>

        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button wire:click="closeModal"
                  class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
            Cancelar
          </button>
          <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-70">
            <span wire:loading.remove wire:target="save">Salvar Despesa</span>
            <span wire:loading wire:target="save">Salvando...</span>
          </button>
        </div>
      </div>
    </div>
  @endif

  {{-- Modal preview recibo --}}
  @if($showPreview)
    <div class="fixed inset-0 bg-slate-900/70 z-50 flex items-center justify-center p-6"
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
