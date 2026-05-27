<div class="min-h-screen lg:grid lg:grid-cols-2">

  {{-- Coluna esquerda — painel da empresa --}}
  <div class="bg-gradient-to-br from-slate-900 to-blue-900 flex flex-col justify-center px-8 py-8 lg:p-12">
    <div>
      <img src="{{ asset('images/logo.png') }}" alt="Veloce Tech" class="h-12 w-auto brightness-0 invert mb-8">

      @if($job->status === 'closed')
        <p class="text-white text-2xl font-semibold text-center mt-16">Esta vaga não está aceitando candidaturas no momento.</p>
      @else
        {{-- Desktop: título + cargo + descrição completa --}}
        <h1 class="text-3xl font-semibold text-white mb-1 hidden lg:block">{{ $job->title }}</h1>
        <p class="text-blue-300 text-lg hidden lg:block">{{ $job->position?->name ?? '' }}</p>
        @if(!empty($job->company))
          <p class="text-blue-200 text-sm mt-1 hidden lg:block">{{ config('companies.' . $job->company, $job->company) }}</p>
        @endif

        @if($job->description)
          <div class="border-t border-white/10 my-6 hidden lg:block"></div>
          <div class="text-white/70 text-sm leading-relaxed hidden lg:block prose prose-sm max-w-none">
            {!! $job->description !!}
          </div>
        @endif

        {{-- Mobile: apenas título --}}
        <div class="lg:hidden">
          <h1 class="text-2xl font-semibold text-white">{{ $job->title }}</h1>
          <p class="text-blue-300 text-base mt-1">{{ $job->position?->name ?? '' }}</p>
        </div>
      @endif
    </div>

    <p class="text-white/40 text-xs mt-auto pt-8">Veloce Tech &copy; {{ date('Y') }}</p>
  </div>

  {{-- Coluna direita — formulário (apenas quando vaga aberta) --}}
  @if($job->status !== 'closed')
    <div class="bg-white flex flex-col justify-center px-6 py-8 lg:p-10">

      @if($submitted)
        <div class="text-center py-16">
          <svg class="w-16 h-16 text-emerald-500 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <h2 class="text-2xl font-semibold text-slate-900 mb-2">Candidatura enviada com sucesso!</h2>
          <p class="text-sm text-slate-500">Entraremos em contato em breve.</p>
        </div>

      @else
        <div class="mb-8">
          <h2 class="text-2xl font-semibold text-slate-900">Sua candidatura</h2>
          <p class="text-sm text-slate-500 mt-1">Preencha os dados abaixo para se candidatar</p>
        </div>

        <div class="space-y-4">

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Nome completo <span class="text-red-500">*</span></label>
            <input type="text" wire:model="name" placeholder="Seu nome completo"
                   class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">E-mail <span class="text-red-500">*</span></label>
            <input type="email" wire:model="email" placeholder="seu@email.com"
                   class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">CPF <span class="text-red-500">*</span></label>
            <input type="text" wire:model="cpf" placeholder="000.000.000-00" maxlength="14"
                   class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            @error('cpf') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Telefone <span class="text-red-500">*</span></label>
            <input type="tel" wire:model="phone" placeholder="(11) 99999-9999"
                   class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            @error('phone') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">LinkedIn (opcional)</label>
            <input type="url" wire:model="linkedin" placeholder="https://linkedin.com/in/seu-perfil"
                   class="h-10 w-full rounded-lg border border-slate-300 bg-white px-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            @error('linkedin') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Pretensão salarial (opcional)</label>
            <div class="relative">
              <span class="absolute inset-y-0 left-3 flex items-center text-sm text-slate-400 pointer-events-none">R$</span>
              <input type="number" step="100" wire:model="salary_expectation" placeholder="0,00"
                     class="h-10 w-full rounded-lg border border-slate-300 bg-white pl-9 pr-3 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20">
            </div>
            @error('salary_expectation') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Currículo em PDF <span class="text-red-500">*</span></label>
            <div class="border-dashed border-2 border-slate-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors">
              <svg class="w-8 h-8 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
              </svg>
              <p class="text-sm text-slate-500 mb-2">Arraste ou selecione seu currículo</p>
              <input type="file" wire:model="cv" accept="application/pdf"
                     class="text-sm text-slate-600 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>
            <div wire:loading wire:target="cv" class="text-xs text-blue-600 mt-1">Carregando arquivo...</div>
            @error('cv') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-700 mb-1.5">Mensagem (opcional)</label>
            <textarea wire:model="notes" rows="3"
                      placeholder="Conte um pouco sobre você ou por que tem interesse nesta vaga"
                      class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm text-slate-900 placeholder:text-slate-400 focus:border-blue-500 focus:outline-none focus:ring-2 focus:ring-blue-500/20 resize-none"></textarea>
          </div>

          <button wire:click="submit" wire:loading.attr="disabled" wire:target="submit"
                  class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition-colors disabled:opacity-70">
            <span wire:loading.remove wire:target="submit">Enviar candidatura</span>
            <span wire:loading wire:target="submit">Enviando...</span>
          </button>

        </div>
      @endif
    </div>
  @endif

</div>
