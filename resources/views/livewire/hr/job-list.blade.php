<div>
  <div class="flex items-center justify-between mb-6">
    <div>
      <h2 class="text-2xl font-semibold text-slate-900">Vagas</h2>
      <p class="text-slate-400 mt-1 text-sm">Gerencie as vagas abertas e candidatos</p>
    </div>
    <button wire:click="openCreate"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors duration-150 ease-out">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Nova Vaga
    </button>
  </div>

  {{-- Filtros --}}
  <div class="bg-white border border-slate-200 rounded-xl p-4 mb-4 flex flex-wrap gap-3 items-center">
    @if($positions->isNotEmpty())
      <select wire:model.live="positionFilter"
              class="h-10 text-sm border border-slate-200 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        <option value="">Todos os cargos</option>
        @foreach($positions as $pos)
          <option value="{{ $pos->id }}">{{ $pos->name }}</option>
        @endforeach
      </select>
    @endif
    <select wire:model.live="companyFilter"
            class="h-10 text-sm border border-slate-200 rounded-lg px-3 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
      <option value="">Todas as empresas</option>
      @foreach($companies as $key => $label)
        <option value="{{ $key }}">{{ $label }}</option>
      @endforeach
    </select>
    @if($positionFilter || $companyFilter)
      <button wire:click="$set('positionFilter', ''); $set('companyFilter', '')"
              class="text-sm text-slate-500 hover:text-slate-700 border border-slate-200 rounded-lg px-3 py-2 transition-colors duration-150 ease-out">
        Limpar filtros
      </button>
    @endif
  </div>

  <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
    @if($jobs->isEmpty())
      <div class="text-center py-16 text-slate-400">
        <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
        </svg>
        <p class="text-sm">Nenhuma vaga cadastrada.
          <button wire:click="openCreate" class="text-blue-600 underline">Crie a primeira!</button>
        </p>
      </div>
    @else
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Título / Cargo</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">Aguardando</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">Entrevista</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">Contratado</th>
            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wide">Descartado</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Aberta há</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wide">Status</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($jobs as $job)
            @php $daysOpen = (int) $job->created_at->diffInDays(now()); @endphp
            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors duration-150 ease-out">
              <td class="px-4 py-3">
                <a href="{{ route('hr.candidates.index', $job) }}"
                   class="font-semibold text-slate-900 hover:text-blue-600 transition-colors">
                  {{ $job->title }}
                </a>
                <p class="text-xs text-slate-400 mt-0.5">{{ $job->position?->name ?? '—' }}</p>
                @if($job->company)
                  <x-status-badge color="blue">{{ $companies[$job->company] ?? $job->company }}</x-status-badge>
                @endif
              </td>
              <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-50 text-amber-700 text-xs font-semibold">
                  {{ $job->pending_count ?? 0 }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold">
                  {{ $job->interview_count ?? 0 }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-50 text-emerald-700 text-xs font-semibold">
                  {{ $job->hired_count ?? 0 }}
                </span>
              </td>
              <td class="px-4 py-3 text-center">
                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-100 text-slate-500 text-xs font-semibold">
                  {{ $job->discarded_count ?? 0 }}
                </span>
              </td>
              <td class="px-4 py-3">
                <span class="text-xs text-slate-500">{{ $job->created_at->format('d/m/Y') }}</span>
                <p class="text-xs text-slate-400 mt-0.5">
                  {{ $daysOpen === 0 ? 'hoje' : $daysOpen . ' ' . ($daysOpen === 1 ? 'dia' : 'dias') }}
                </p>
              </td>
              <td class="px-4 py-3">
                <x-status-badge :color="$job->status_color">{{ $job->status_label }}</x-status-badge>
              </td>
              <td class="px-4 py-3 text-right">
                <div class="flex items-center justify-end gap-2">
                  <a href="{{ route('hr.candidates.index', $job) }}"
                     class="text-xs border border-slate-200 hover:border-blue-300 text-slate-600 hover:text-blue-600 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                    Candidatos
                  </a>
                  <button wire:click="openEdit({{ $job->id }})"
                          class="text-xs border border-slate-200 hover:border-blue-300 text-slate-600 hover:text-blue-600 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                    Editar
                  </button>
                  <button wire:click="toggleStatus({{ $job->id }})"
                          class="text-xs border {{ $job->status === 'open' ? 'border-amber-200 hover:border-amber-400 text-amber-600 hover:text-amber-700' : 'border-emerald-200 hover:border-emerald-400 text-emerald-600 hover:text-emerald-700' }} rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                    {{ $job->status === 'open' ? 'Fechar' : 'Reabrir' }}
                  </button>
                  <button wire:click="showLink({{ $job->id }})"
                          class="text-xs border border-slate-200 hover:border-blue-300 text-slate-500 hover:text-blue-600 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                    Link Público
                  </button>
                </div>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @endif
  </div>

  {{-- Modal criar/editar vaga --}}
  @if($showModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
          <h3 class="text-lg font-semibold">{{ $editingId ? 'Editar Vaga' : 'Nova Vaga' }}</h3>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Título *</label>
            <input type="text" wire:model="title" placeholder="Ex: Desenvolvedor Full Stack"
                   class="h-10 w-full border border-slate-200 rounded-lg px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('title') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Empresa *</label>
            <select wire:model="company"
                    class="h-10 w-full border border-slate-200 rounded-lg px-3 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
              <option value="">Selecione a empresa</option>
              @foreach($companies as $key => $label)
                <option value="{{ $key }}">{{ $label }}</option>
              @endforeach
            </select>
            @error('company') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Cargo *</label>
            <select wire:model="position_id"
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
              <option value="">Selecione um cargo</option>
              @foreach($positions as $pos)
                <option value="{{ $pos->id }}">{{ $pos->name }}</option>
              @endforeach
            </select>
            @error('position_id') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Descrição</label>
            <div wire:ignore x-data="tiptapEditor()" x-init="init()">
              {{-- Toolbar --}}
              <div class="flex flex-wrap gap-1 bg-slate-50 border border-slate-200 rounded-t-lg px-2 py-1.5">
                <button type="button" @mousedown.prevent="cmd(c => c.toggleBold().run())"
                        :class="active('bold') ? 'bg-blue-50 text-blue-600' : 'hover:bg-slate-100'"
                        class="px-2 py-1 rounded text-sm font-bold text-slate-700 transition-colors">B</button>
                <button type="button" @mousedown.prevent="cmd(c => c.toggleItalic().run())"
                        :class="active('italic') ? 'bg-blue-50 text-blue-600' : 'hover:bg-slate-100'"
                        class="px-2 py-1 rounded text-sm italic text-slate-700 transition-colors">I</button>
                <button type="button" @mousedown.prevent="cmd(c => c.toggleHeading({level:2}).run())"
                        :class="active('heading',{level:2}) ? 'bg-blue-50 text-blue-600' : 'hover:bg-slate-100'"
                        class="px-2 py-1 rounded text-xs font-semibold text-slate-700 transition-colors">H2</button>
                <button type="button" @mousedown.prevent="cmd(c => c.toggleHeading({level:3}).run())"
                        :class="active('heading',{level:3}) ? 'bg-blue-50 text-blue-600' : 'hover:bg-slate-100'"
                        class="px-2 py-1 rounded text-xs font-semibold text-slate-700 transition-colors">H3</button>
                <button type="button" @mousedown.prevent="cmd(c => c.toggleBulletList().run())"
                        :class="active('bulletList') ? 'bg-blue-50 text-blue-600' : 'hover:bg-slate-100'"
                        class="px-2 py-1 rounded text-xs text-slate-700 transition-colors">&#8226; Lista</button>
                <button type="button" @mousedown.prevent="cmd(c => c.toggleOrderedList().run())"
                        :class="active('orderedList') ? 'bg-blue-50 text-blue-600' : 'hover:bg-slate-100'"
                        class="px-2 py-1 rounded text-xs text-slate-700 transition-colors">1. Lista</button>
                <button type="button" @mousedown.prevent="cmd(c => c.setHorizontalRule().run())"
                        class="px-2 py-1 rounded text-xs text-slate-700 hover:bg-slate-100 transition-colors">&#8212;</button>
              </div>
              {{-- Editor area --}}
              <div x-ref="editorEl"
                   class="min-h-[150px] p-3 bg-white border border-t-0 border-slate-200 rounded-b-lg text-sm text-slate-900 [&_.ProseMirror]:outline-none [&_h2]:text-base [&_h2]:font-semibold [&_h3]:text-sm [&_h3]:font-semibold [&_ul]:list-disc [&_ul]:pl-4 [&_ol]:list-decimal [&_ol]:pl-4 [&_strong]:font-semibold [&_em]:italic [&_hr]:border-slate-200 [&_hr]:my-2">
              </div>
            </div>
            @error('description') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
        </div>
        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button wire:click="closeModal"
                  class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors duration-150 ease-out">
            Cancelar
          </button>
          <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-70">
            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Salvar' : 'Criar Vaga' }}</span>
            <span wire:loading wire:target="save">Salvando...</span>
          </button>
        </div>
      </div>
    </div>
  @endif

  {{-- Modal link público --}}
  @if($showLinkModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closeLinkModal">
      <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
          <h3 class="text-lg font-semibold">Link de Candidatura</h3>
          <p class="text-sm text-slate-400 mt-1">Compartilhe este link para receber candidaturas</p>
        </div>
        <div class="p-6">
          <div class="bg-slate-50 border border-slate-200 rounded-lg px-4 py-3 mb-4 break-all font-mono text-sm text-slate-700 select-all">
            {{ $linkUrl }}
          </div>
          <div class="flex gap-3"
               x-data="{ copied: false }">
            <button
              @click="
                try { navigator.clipboard.writeText('{{ $linkUrl }}'); } catch(e) {
                  let t = document.createElement('textarea');
                  t.value = '{{ $linkUrl }}'; document.body.appendChild(t); t.select();
                  document.execCommand('copy'); document.body.removeChild(t);
                }
                copied = true; setTimeout(() => copied = false, 2000);
              "
              :class="copied ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-blue-600 hover:bg-blue-700'"
              class="flex-1 px-4 py-2 text-sm font-medium text-white rounded-lg transition-colors duration-150 ease-out"
              x-text="copied ? '✓ Copiado!' : 'Copiar link'">
            </button>
            <a href="{{ $linkUrl }}" target="_blank"
               class="px-4 py-2 text-sm font-medium text-slate-600 border border-slate-200 hover:border-blue-300 hover:text-blue-600 rounded-lg transition-colors duration-150 ease-out">
              Abrir
            </a>
            <button wire:click="closeLinkModal"
                    class="px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-700 transition-colors">
              Fechar
            </button>
          </div>
        </div>
      </div>
    </div>
  @endif
</div>
