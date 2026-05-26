<div class="min-h-screen bg-slate-50 py-12 px-4">
  <div class="max-w-xl mx-auto">

    {{-- Logo / Header --}}
    <div class="text-center mb-8">
      <p class="text-xs font-mono text-slate-400 uppercase tracking-widest mb-1">Formulário de Candidatura</p>
      <h1 class="text-2xl font-semibold text-slate-900">{{ $job->title }}</h1>
      <p class="text-slate-500 mt-1">{{ $job->position }}</p>
    </div>

    @if($job->status === 'closed')
      <div class="bg-amber-50 border border-amber-200 rounded-xl p-6 text-center">
        <svg class="w-10 h-10 text-amber-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <h2 class="text-lg font-semibold text-amber-800 mb-1">Vaga Encerrada</h2>
        <p class="text-sm text-amber-600">Esta vaga não está aceitando novas candidaturas no momento.</p>
      </div>

    @elseif($submitted)
      <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-8 text-center">
        <svg class="w-12 h-12 text-emerald-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h2 class="text-xl font-semibold text-emerald-800 mb-2">Candidatura enviada!</h2>
        <p class="text-sm text-emerald-600">Agradecemos seu interesse. Nossa equipe irá analisar seu currículo e entrar em contato.</p>
      </div>

    @else
      @if($job->description)
        <div class="bg-white border border-slate-200 rounded-xl p-5 mb-6">
          <h2 class="text-sm font-semibold text-slate-700 mb-2">Sobre a Vaga</h2>
          <p class="text-sm text-slate-600 whitespace-pre-line">{{ $job->description }}</p>
        </div>
      @endif

      <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
        <h2 class="text-base font-semibold text-slate-900 mb-2">Seus dados</h2>

        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Nome completo *</label>
          <input type="text" wire:model="name" placeholder="Seu nome completo"
                 class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">E-mail *</label>
          <input type="email" wire:model="email" placeholder="seu@email.com"
                 class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Telefone *</label>
          <input type="tel" wire:model="phone" placeholder="(11) 99999-9999"
                 class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">LinkedIn (opcional)</label>
          <input type="url" wire:model="linkedin" placeholder="https://linkedin.com/in/voce"
                 class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          @error('linkedin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Pretensão salarial (R$) (opcional)</label>
          <input type="number" step="100" wire:model="salary_expectation" placeholder="0,00"
                 class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
          @error('salary_expectation') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Currículo (PDF) *</label>
          <input type="file" wire:model="cv" accept="application/pdf"
                 class="w-full text-sm text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 border border-slate-200 rounded-lg p-1">
          <div wire:loading wire:target="cv" class="text-xs text-blue-600 mt-1">Carregando arquivo...</div>
          @error('cv') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
          <label class="block text-sm font-medium text-slate-600 mb-1.5">Comentário (opcional)</label>
          <textarea wire:model="notes" rows="3"
                    placeholder="Algo que queira nos dizer sobre você ou sua candidatura..."
                    class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 resize-none"></textarea>
        </div>

        <button wire:click="submit" wire:loading.attr="disabled" wire:target="submit"
                class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-3 rounded-lg transition-colors disabled:opacity-70">
          <span wire:loading.remove wire:target="submit">Enviar Candidatura</span>
          <span wire:loading wire:target="submit">Enviando...</span>
        </button>
      </div>
    @endif

  </div>
</div>
