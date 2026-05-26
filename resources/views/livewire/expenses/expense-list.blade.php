<div>
  {{-- Header --}}
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-2xl font-semibold text-slate-900">Minhas Despesas</h2>
      <p class="text-slate-400 mt-1 text-sm">Gerencie seus comprovantes</p>
    </div>
    <button wire:click="openModal"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors duration-150 ease-out">
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
           class="flex-1 min-w-40 text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
    <select wire:model.live="categoryFilter"
            class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
      <option value="">Todas as categorias</option>
      @foreach($categories as $cat)
        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
      @endforeach
    </select>
    <input wire:model.live="dateFrom" type="date"
           class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
    <input wire:model.live="dateTo" type="date"
           class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
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
            <th class="px-4 py-3 w-8"></th>
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
            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors {{ in_array($e->id, $selectedIds) ? 'bg-blue-50' : '' }}">
              <td class="px-4 py-3">
                @if($e->isAvailable())
                  <input type="checkbox"
                         wire:click="toggleSelect({{ $e->id }})"
                         @checked(in_array($e->id, $selectedIds))
                         class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500 cursor-pointer">
                @endif
              </td>
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
                          class="text-xs text-red-500 hover:text-red-700 border border-red-200 hover:border-red-400 rounded-lg px-2.5 py-1 transition-colors duration-150 ease-out">
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

  {{-- Barra de ação flutuante (seleção) --}}
  @if(count($selectedIds) > 0)
    <div class="fixed bottom-6 left-1/2 -translate-x-1/2 z-40 flex items-center gap-4 bg-slate-900 text-white rounded-2xl px-5 py-3.5 shadow-2xl border border-slate-700 transition-all duration-150 ease-out">
      <span class="text-sm font-medium">
        {{ count($selectedIds) }} {{ count($selectedIds) === 1 ? 'despesa selecionada' : 'despesas selecionadas' }}
      </span>
      <div class="w-px h-5 bg-slate-600"></div>
      <button wire:click="openReportModal"
              class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-500 text-white text-sm font-medium px-4 py-1.5 rounded-lg transition-colors duration-150 ease-out">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        Gerar Relatório
      </button>
      <button wire:click="clearSelection"
              class="text-slate-400 hover:text-white transition-colors duration-150 ease-out p-1 rounded-lg hover:bg-slate-800">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>
    </div>
  @endif

  {{-- Modal criação de relatório --}}
  @if($showReportModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closeReportModal">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
          <h3 class="text-lg font-semibold">Novo Relatório</h3>
          <p class="text-sm text-slate-400 mt-1">
            {{ count($selectedIds) }} {{ count($selectedIds) === 1 ? 'despesa selecionada' : 'despesas selecionadas' }}
          </p>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Título do relatório *</label>
            <input type="text" wire:model="reportTitle"
                   placeholder="Ex: Viagem São Paulo — Mai/2025"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('reportTitle') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Chave PIX</label>
            <input type="text" wire:model="reportPixKey"
                   placeholder="CPF, e-mail, telefone ou chave aleatória"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          </div>

          {{-- Resumo das despesas --}}
          <div class="bg-slate-50 rounded-lg p-4 space-y-2 max-h-48 overflow-y-auto">
            @foreach($expenses as $e)
              @if(in_array($e->id, $selectedIds))
                <div class="flex items-center justify-between text-sm">
                  <span class="text-slate-700 truncate flex-1 mr-3">{{ $e->description ?: '—' }}</span>
                  <span class="font-mono font-medium text-slate-900 shrink-0">R$ {{ number_format($e->value, 2, ',', '.') }}</span>
                </div>
              @endif
            @endforeach
          </div>
        </div>
        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button wire:click="closeReportModal"
                  class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors duration-150 ease-out">
            Cancelar
          </button>
          <button wire:click="createReport" wire:loading.attr="disabled" wire:target="createReport"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-70">
            <span wire:loading.remove wire:target="createReport">Criar Relatório</span>
            <span wire:loading wire:target="createReport">Criando...</span>
          </button>
        </div>
      </div>
    </div>
  @endif

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
                     class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
              @error('expense_date') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
              <label class="block text-sm font-medium text-slate-600 mb-1.5">Valor (R$) *</label>
              <input type="number" step="0.01" min="0" wire:model="value" placeholder="0,00"
                     class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
              @error('value') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
            </div>
          </div>

          {{-- Category --}}
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Categoria *</label>
            <select wire:model="category_id"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
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
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          </div>
        </div>

        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button wire:click="closeModal"
                  class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors duration-150 ease-out">
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

  {{-- Slide-over preview recibo --}}
  @if($showPreview)
    <div class="fixed inset-0 z-50 overflow-hidden">
      <div class="absolute inset-0 bg-slate-900/40 transition-opacity duration-150 ease-out"
           wire:click="closePreview"></div>
      <div class="absolute inset-y-0 right-0 flex max-w-full pl-10">
        <div class="relative w-screen max-w-xl">
          <div class="flex h-full flex-col bg-white shadow-xl rounded-l-xl border-l border-slate-200 overflow-hidden">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100">
              <h3 class="text-sm font-semibold text-slate-900">Visualizar Recibo</h3>
              <button wire:click="closePreview"
                      class="rounded-lg p-1.5 text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition-colors duration-150 ease-out">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
              </button>
            </div>
            <div class="flex-1 overflow-auto p-4 bg-slate-50">
              @if($previewType === 'image')
                <img src="{{ $previewUrl }}" alt="Recibo"
                     class="w-full rounded-xl border border-slate-200 shadow-sm object-contain">
              @else
                <iframe src="{{ $previewUrl }}"
                        class="w-full h-full min-h-[calc(100vh-120px)] rounded-xl border border-slate-200"></iframe>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
