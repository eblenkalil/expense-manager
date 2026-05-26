<div>
  {{-- Breadcrumb --}}
  <div class="mb-6">
    <div class="flex items-center gap-2 text-sm text-slate-400 mb-3">
      <a href="{{ route('hr.jobs.index') }}" class="hover:text-slate-600 transition-colors">Vagas</a>
      <span>/</span>
      <span class="text-slate-700 font-medium">{{ $job->title }}</span>
    </div>
    <div class="flex items-center justify-between">
      <div>
        <h2 class="text-2xl font-semibold text-slate-900">Candidatos</h2>
        <p class="text-slate-400 mt-1 text-sm">{{ $job->title }} — {{ $job->position }}</p>
      </div>
      <div class="flex items-center gap-2">
        <button wire:click="exportCsv"
                class="inline-flex items-center gap-2 text-sm font-medium text-slate-600 border border-slate-200 hover:border-slate-300 px-4 py-2 rounded-lg transition-colors duration-150 ease-out">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
          </svg>
          Exportar
        </button>
        <button wire:click="openModal"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150 ease-out">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          Adicionar
        </button>
      </div>
    </div>
  </div>

  {{-- Contadores por status --}}
  <div class="grid grid-cols-4 gap-4 mb-6">
    @foreach([
      'pending'   => ['Aguardando',    'amber',   $counts['pending'] ?? 0],
      'interview' => ['Em Entrevista', 'blue',    $counts['interview'] ?? 0],
      'hired'     => ['Contratados',   'emerald', $counts['hired'] ?? 0],
      'discarded' => ['Descartados',   'slate',   $counts['discarded'] ?? 0],
    ] as $status => [$label, $color, $count])
      @php
        $colorMap = [
          'amber'   => 'bg-amber-50 border-amber-200 text-amber-700',
          'blue'    => 'bg-blue-50 border-blue-200 text-blue-700',
          'emerald' => 'bg-emerald-50 border-emerald-200 text-emerald-700',
          'slate'   => 'bg-slate-100 border-slate-200 text-slate-600',
        ];
      @endphp
      <div class="bg-white border border-slate-200 rounded-xl p-4 text-center cursor-pointer hover:border-blue-300 transition-colors duration-150 ease-out"
           wire:click="$set('statusFilter', '{{ $statusFilter === $status ? '' : $status }}')">
        <p class="text-2xl font-bold {{ $colorMap[$color] }} rounded-lg py-1">{{ $count }}</p>
        <p class="text-xs text-slate-500 mt-1">{{ $label }}</p>
      </div>
    @endforeach
  </div>

  {{-- Filtros --}}
  <div class="bg-white border border-slate-200 rounded-xl p-4 mb-4 flex flex-wrap gap-3">
    <select wire:model.live="statusFilter"
            class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
      <option value="">Todos os status</option>
      <option value="pending">Aguardando</option>
      <option value="interview">Em Entrevista</option>
      <option value="hired">Contratado</option>
      <option value="discarded">Descartado</option>
    </select>
    <select wire:model.live="sourceFilter"
            class="text-sm border border-slate-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
      <option value="">Todas as origens</option>
      <option value="manual">Cadastro Manual</option>
      <option value="public_form">Formulário Público</option>
    </select>
    @if($statusFilter || $sourceFilter)
      <button wire:click="$set('statusFilter', ''); $set('sourceFilter', '')"
              class="text-sm text-slate-500 hover:text-slate-700 border border-slate-200 rounded-lg px-3 py-2 transition-colors duration-150 ease-out">
        Limpar filtros
      </button>
    @endif
  </div>

  {{-- Tabela --}}
  <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
    @if($candidates->isEmpty())
      <div class="text-center py-12 text-slate-400 text-sm">
        <p>Nenhum candidato encontrado.</p>
      </div>
    @else
      <table class="w-full text-sm">
        <thead class="bg-slate-50 border-b border-slate-200">
          <tr>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nome</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">E-mail</th>
            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase">Pretensão</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Status</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Origem</th>
            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Inscrição</th>
            <th class="px-4 py-3"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($candidates as $c)
            <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors duration-150 ease-out">
              <td class="px-4 py-3 font-medium">
                <a href="{{ route('hr.candidates.show', $c) }}" class="hover:text-blue-600 transition-colors">
                  {{ $c->name }}
                </a>
              </td>
              <td class="px-4 py-3 text-xs text-slate-500 font-mono">{{ $c->email }}</td>
              <td class="px-4 py-3 text-right font-mono text-slate-600 text-xs">
                {{ $c->salary_expectation ? 'R$ ' . number_format($c->salary_expectation, 2, ',', '.') : '—' }}
              </td>
              <td class="px-4 py-3">
                <x-status-badge :color="$c->status_color">{{ $c->status_label }}</x-status-badge>
              </td>
              <td class="px-4 py-3 text-xs text-slate-500">{{ $c->source_label }}</td>
              <td class="px-4 py-3 text-xs text-slate-400">{{ $c->created_at->format('d/m/Y') }}</td>
              <td class="px-4 py-3 text-right">
                <a href="{{ route('hr.candidates.show', $c) }}"
                   class="text-xs border border-slate-200 hover:border-blue-300 text-slate-600 hover:text-blue-600 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                  Ver
                </a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <div class="px-4 py-3 border-t border-slate-100">{{ $candidates->links() }}</div>
    @endif
  </div>

  {{-- Modal adicionar candidato manual --}}
  @if($showModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-lg shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100 sticky top-0 bg-white">
          <h3 class="text-lg font-semibold">Adicionar Candidato</h3>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Nome *</label>
            <input type="text" wire:model="name" placeholder="Nome completo"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">E-mail *</label>
            <input type="email" wire:model="email" placeholder="email@exemplo.com"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Telefone</label>
            <input type="tel" wire:model="phone" placeholder="(11) 99999-9999"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">LinkedIn</label>
            <input type="url" wire:model="linkedin" placeholder="https://linkedin.com/in/..."
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('linkedin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Pretensão Salarial (R$)</label>
            <input type="number" step="100" wire:model="salary_expectation" placeholder="0,00"
                   class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('salary_expectation') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Currículo (PDF) *</label>
            <input type="file" wire:model="cv" accept="application/pdf"
                   class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-lg p-1">
            <div wire:loading wire:target="cv" class="text-xs text-blue-600 mt-1">Carregando...</div>
            @error('cv') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Comentário</label>
            <textarea wire:model="notes" rows="3" placeholder="Observações sobre o candidato..."
                      class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none"></textarea>
          </div>
        </div>
        <div class="px-6 pb-6 flex gap-3 justify-end sticky bottom-0 bg-white border-t border-slate-100 pt-4">
          <button wire:click="closeModal"
                  class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors duration-150 ease-out">
            Cancelar
          </button>
          <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-70">
            <span wire:loading.remove wire:target="save">Cadastrar</span>
            <span wire:loading wire:target="save">Salvando...</span>
          </button>
        </div>
      </div>
    </div>
  @endif
</div>
