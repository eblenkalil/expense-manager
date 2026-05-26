{{-- resources/views/livewire/admin/admin-positions.blade.php --}}
<div>
  <div class="flex justify-end mb-4">
    <button wire:click="openCreate"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150 ease-out">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Novo Cargo
    </button>
  </div>

  <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nome</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Status</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody>
        @forelse($positions as $pos)
          <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors duration-150 ease-out">
            <td class="px-4 py-3 font-medium {{ $pos->active ? '' : 'text-slate-400 line-through' }}">
              {{ $pos->name }}
            </td>
            <td class="px-4 py-3">
              <x-status-badge :color="$pos->active ? 'green' : 'gray'">
                {{ $pos->active ? 'Ativo' : 'Inativo' }}
              </x-status-badge>
            </td>
            <td class="px-4 py-3 text-right flex items-center justify-end gap-2">
              <button wire:click="openEdit({{ $pos->id }})"
                      class="text-xs border border-slate-200 hover:border-blue-300 text-slate-600 hover:text-blue-600 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                Editar
              </button>
              <button wire:click="toggleActive({{ $pos->id }})"
                      class="text-xs border rounded-lg px-3 py-1.5 transition-colors
                             {{ $pos->active ? 'border-red-200 text-red-500 hover:border-red-400' : 'border-emerald-200 text-emerald-600 hover:border-emerald-400' }}">
                {{ $pos->active ? 'Desativar' : 'Ativar' }}
              </button>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="3" class="px-4 py-10 text-center text-slate-400 text-sm">
              Nenhum cargo cadastrado.
              <button wire:click="openCreate" class="text-blue-600 underline ml-1">Crie o primeiro!</button>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Modal --}}
  @if($showModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="$set('showModal', false)">
      <div class="bg-white rounded-2xl w-full max-w-sm shadow-2xl">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
          <h3 class="font-semibold">{{ $editingId ? 'Editar' : 'Novo' }} Cargo</h3>
        </div>
        <div class="p-6">
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Nome *</label>
          <input type="text" wire:model="name" placeholder="Ex: Desenvolvedor, Analista, Estagiário"
                 class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button wire:click="$set('showModal', false)"
                  class="px-4 py-2 text-sm font-medium text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors duration-150 ease-out">
            Cancelar
          </button>
          <button wire:click="save"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors duration-150 ease-out">
            Salvar
          </button>
        </div>
      </div>
    </div>
  @endif
</div>
