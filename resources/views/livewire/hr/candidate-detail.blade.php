<div>
  {{-- Breadcrumb --}}
  <div class="flex items-center gap-2 text-sm text-slate-400 mb-6">
    <a href="{{ route('hr.jobs.index') }}" class="hover:text-slate-600 transition-colors">Vagas</a>
    <span>/</span>
    <a href="{{ route('hr.candidates.index', $candidate->job) }}" class="hover:text-slate-600 transition-colors">
      {{ $candidate->job->title }}
    </a>
    <span>/</span>
    <span class="text-slate-700 font-medium">{{ $candidate->name }}</span>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Coluna esquerda: dados + ações --}}
    <div class="space-y-4">

      {{-- Dados do candidato --}}
      <div class="bg-white border border-slate-200 rounded-xl p-5">
        <div class="flex items-start justify-between mb-4">
          <div>
            <h2 class="text-lg font-semibold text-slate-900">{{ $candidate->name }}</h2>
            <x-status-badge :color="$candidate->status_color" class="mt-1">{{ $candidate->status_label }}</x-status-badge>
          </div>
          @if($candidate->cv_path)
            <a href="{{ $candidate->cv_url }}" target="_blank"
               class="inline-flex items-center gap-1 text-xs text-blue-600 hover:text-blue-700 border border-blue-200 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
              </svg>
              CV
            </a>
          @endif
        </div>
        <div class="space-y-2 text-sm">
          <div class="flex gap-2">
            <span class="text-slate-400 w-24 flex-shrink-0">E-mail</span>
            <span class="text-slate-700 font-mono text-xs break-all">{{ $candidate->email }}</span>
          </div>
          @if($candidate->phone)
            <div class="flex gap-2">
              <span class="text-slate-400 w-24 flex-shrink-0">Telefone</span>
              <span class="text-slate-700">{{ $candidate->phone }}</span>
            </div>
          @endif
          @if($candidate->linkedin)
            <div class="flex gap-2">
              <span class="text-slate-400 w-24 flex-shrink-0">LinkedIn</span>
              <a href="{{ $candidate->linkedin }}" target="_blank" class="text-blue-600 hover:underline text-xs truncate">
                Ver perfil
              </a>
            </div>
          @endif
          @if($candidate->salary_expectation)
            <div class="flex gap-2">
              <span class="text-slate-400 w-24 flex-shrink-0">Pretensão</span>
              <span class="text-slate-700 font-mono">R$ {{ number_format($candidate->salary_expectation, 2, ',', '.') }}</span>
            </div>
          @endif
          <div class="flex gap-2">
            <span class="text-slate-400 w-24 flex-shrink-0">Origem</span>
            <span class="text-slate-600">{{ $candidate->source_label }}</span>
          </div>
          <div class="flex gap-2">
            <span class="text-slate-400 w-24 flex-shrink-0">Inscrição</span>
            <span class="text-slate-600">{{ $candidate->created_at->format('d/m/Y H:i') }}</span>
          </div>
        </div>
      </div>

      {{-- Ações de status --}}
      <div class="bg-white border border-slate-200 rounded-xl p-5">
        <h3 class="font-semibold text-slate-900 mb-3 text-sm">Mover para</h3>
        <div class="space-y-2">
          @foreach([
            'pending'   => ['Aguardando',        'border-amber-200 text-amber-700 hover:border-amber-400 hover:bg-amber-50',   'amber'],
            'interview' => ['Mover p/ Entrevista','border-blue-200 text-blue-700 hover:border-blue-400 hover:bg-blue-50',     'blue'],
            'hired'     => ['Contratar',          'border-emerald-200 text-emerald-700 hover:border-emerald-400 hover:bg-emerald-50', 'emerald'],
            'discarded' => ['Descartar',          'border-slate-200 text-slate-500 hover:border-slate-400 hover:bg-slate-50',  'slate'],
          ] as $status => [$label, $classes, $color])
            <button wire:click="openStatusModal('{{ $status }}')"
                    @disabled($candidate->status === $status)
                    class="w-full text-left text-sm border rounded-lg px-4 py-2.5 transition-colors duration-150 ease-out
                           {{ $candidate->status === $status ? 'bg-slate-50 border-slate-200 text-slate-400 cursor-not-allowed' : $classes }}">
              @if($candidate->status === $status)
                ✓ {{ $label }} (atual)
              @else
                {{ $label }}
              @endif
            </button>
          @endforeach
        </div>
      </div>

      {{-- Candidaturas anteriores --}}
      @if($previousCandidacies->isNotEmpty())
        <div class="bg-amber-50 border border-amber-200 rounded-xl p-5">
          <h3 class="font-semibold text-amber-800 mb-2 text-sm">Candidaturas anteriores</h3>
          <div class="space-y-1">
            @foreach($previousCandidacies as $prev)
              <a href="{{ route('hr.candidates.show', $prev) }}"
                 class="block text-sm text-amber-700 hover:text-amber-900 hover:underline">
                {{ $prev->job->title }} — {{ $prev->status_label }}
              </a>
            @endforeach
          </div>
        </div>
      @endif

    </div>

    {{-- Coluna direita: comentários + timeline --}}
    <div class="lg:col-span-2 space-y-4">

      {{-- Adicionar comentário --}}
      <div class="bg-white border border-slate-200 rounded-xl p-5">
        <h3 class="font-semibold text-slate-900 mb-3 text-sm">Adicionar comentário</h3>
        <textarea wire:model="newComment" rows="3"
                  placeholder="Escreva um comentário sobre este candidato..."
                  class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none"></textarea>
        @error('newComment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        <div class="flex justify-end mt-3">
          <button wire:click="addComment" wire:loading.attr="disabled" wire:target="addComment"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-70">
            Comentar
          </button>
        </div>
      </div>

      {{-- Linha do tempo --}}
      <div class="bg-white border border-slate-200 rounded-xl p-5">
        <h3 class="font-semibold text-slate-900 mb-4 text-sm">Histórico</h3>
        @if($candidate->events->isEmpty())
          <p class="text-sm text-slate-400">Nenhum evento registrado.</p>
        @else
          <div class="space-y-4">
            @foreach($candidate->events as $event)
              <div class="flex gap-3">
                {{-- Ícone do evento --}}
                <div class="flex-shrink-0 mt-0.5">
                  @if($event->type === 'status_change')
                    <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center">
                      <svg class="w-3.5 h-3.5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                      </svg>
                    </div>
                  @elseif($event->type === 'rating')
                    <div class="w-7 h-7 rounded-full bg-amber-100 flex items-center justify-center">
                      <svg class="w-3.5 h-3.5 text-amber-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                      </svg>
                    </div>
                  @elseif($event->type === 'comment')
                    <div class="w-7 h-7 rounded-full bg-slate-100 flex items-center justify-center">
                      <svg class="w-3.5 h-3.5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                      </svg>
                    </div>
                  @else
                    <div class="w-7 h-7 rounded-full bg-emerald-100 flex items-center justify-center">
                      <svg class="w-3.5 h-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                      </svg>
                    </div>
                  @endif
                </div>

                {{-- Conteúdo do evento --}}
                <div class="flex-1 min-w-0">
                  @if($event->type === 'comment' && $editingCommentId === $event->id)
                    <div>
                      <textarea wire:model="editingCommentContent" rows="3"
                                class="w-full border border-blue-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 resize-none"></textarea>
                      <div class="flex gap-2 mt-2">
                        <button wire:click="saveEditComment"
                                class="text-xs px-3 py-1.5 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                          Salvar
                        </button>
                        <button wire:click="cancelEditComment"
                                class="text-xs px-3 py-1.5 text-slate-600 border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors">
                          Cancelar
                        </button>
                      </div>
                    </div>
                  @else
                    <p class="text-sm text-slate-700">{{ $event->content }}</p>
                    @if($event->type === 'rating' && $event->rating)
                      <div class="flex items-center gap-1 mt-1">
                        @for($i = 1; $i <= 5; $i++)
                          <svg class="w-4 h-4 {{ $i <= $event->rating ? 'text-amber-400' : 'text-slate-200' }}"
                               fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                          </svg>
                        @endfor
                      </div>
                    @endif
                    <div class="flex items-center gap-2 mt-1">
                      <span class="text-xs text-slate-400">
                        {{ $event->user?->name ?? 'Sistema' }} · {{ $event->created_at->format('d/m/Y H:i') }}
                      </span>
                      @if($event->type === 'comment' && $event->user_id === auth()->id())
                        <button wire:click="openEditComment({{ $event->id }})"
                                class="text-xs text-blue-500 hover:text-blue-700 transition-colors">
                          Editar
                        </button>
                      @endif
                    </div>
                  @endif
                </div>
              </div>
            @endforeach
          </div>
        @endif
      </div>

    </div>
  </div>

  {{-- Modal de mudança de status --}}
  @if($showStatusModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closeStatusModal">
      <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
          <h3 class="text-lg font-semibold">
            @if($newStatus === 'interview') Mover para Entrevista
            @elseif($newStatus === 'hired') Contratar Candidato
            @elseif($newStatus === 'discarded') Descartar Candidato
            @else Alterar Status
            @endif
          </h3>
          <p class="text-sm text-slate-400 mt-1">{{ $candidate->name }}</p>
        </div>
        <div class="p-6 space-y-4">
          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">
              Motivo {{ in_array($newStatus, ['hired', 'discarded']) ? '*' : '(opcional)' }}
            </label>
            <textarea wire:model="statusReason" rows="3"
                      placeholder="Descreva o motivo..."
                      class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none"></textarea>
            @error('statusReason') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          @if($newStatus === 'interview')
            <div>
              <label class="block text-sm font-medium text-slate-600 mb-2">Avaliação (opcional)</label>
              <div class="flex items-center gap-2 mb-3">
                @for($i = 1; $i <= 5; $i++)
                  <button type="button" wire:click="$set('ratingValue', {{ $i }})"
                          class="w-8 h-8 rounded-full transition-colors {{ $i <= $ratingValue ? 'text-amber-400' : 'text-slate-200 hover:text-amber-300' }}">
                    <svg fill="currentColor" viewBox="0 0 24 24">
                      <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                  </button>
                @endfor
                @if($ratingValue > 0)
                  <button type="button" wire:click="$set('ratingValue', 0)"
                          class="text-xs text-slate-400 hover:text-slate-600 ml-1">Limpar</button>
                @endif
              </div>
              <textarea wire:model="ratingComment" rows="2"
                        placeholder="Texto de avaliação (opcional)..."
                        class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 resize-none"></textarea>
            </div>
          @endif
        </div>
        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button wire:click="closeStatusModal"
                  class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors duration-150 ease-out">
            Cancelar
          </button>
          <button wire:click="confirmStatusChange" wire:loading.attr="disabled" wire:target="confirmStatusChange"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-70">
            <span wire:loading.remove wire:target="confirmStatusChange">Confirmar</span>
            <span wire:loading wire:target="confirmStatusChange">Salvando...</span>
          </button>
        </div>
      </div>
    </div>
  @endif
</div>
