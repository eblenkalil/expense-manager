<div class="max-w-2xl">
  <div class="mb-8">
    <h2 class="text-2xl font-semibold text-slate-900">Meu Perfil</h2>
    <p class="text-slate-400 mt-1 text-sm">Gerencie suas informações e preferências</p>
  </div>

  {{-- Profile info --}}
  <div class="bg-white border border-slate-200 rounded-xl p-6 mb-5">
    <h3 class="font-semibold text-slate-900 mb-5">Dados pessoais</h3>

    @if(session('profile_success'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
           class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3">
        {{ session('profile_success') }}
      </div>
    @endif

    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">Nome *</label>
        <input type="text" wire:model="name"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">E-mail *</label>
        <input type="email" wire:model="email"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>

      {{-- Notification toggle --}}
      <div class="flex items-center justify-between py-3 border-t border-slate-100 mt-2">
        <div>
          <p class="text-sm font-medium text-slate-700">Notificações por e-mail</p>
          <p class="text-xs text-slate-400 mt-0.5">
            Receba avisos quando um relatório for entregue ou pago
          </p>
        </div>
        <button wire:click="$toggle('notify_email')" type="button"
                class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 focus:ring-offset-2
                       {{ $notify_email ? 'bg-blue-600' : 'bg-slate-200' }}">
          <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out
                       {{ $notify_email ? 'translate-x-5' : 'translate-x-0' }}"></span>
        </button>
      </div>

      <div class="pt-2">
        <button wire:click="saveProfile" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors disabled:opacity-70">
          <span wire:loading.remove wire:target="saveProfile">Salvar alterações</span>
          <span wire:loading wire:target="saveProfile">Salvando...</span>
        </button>
      </div>
    </div>
  </div>

  {{-- Change password --}}
  <div class="bg-white border border-slate-200 rounded-xl p-6">
    <h3 class="font-semibold text-slate-900 mb-5">Alterar senha</h3>

    @if(session('password_success'))
      <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
           class="mb-4 text-sm text-emerald-700 bg-emerald-50 border border-emerald-200 rounded-lg px-4 py-3">
        {{ session('password_success') }}
      </div>
    @endif

    <div class="space-y-4">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">Senha atual *</label>
        <input type="password" wire:model="current_password"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        @error('current_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">Nova senha *</label>
        <input type="password" wire:model="new_password"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        @error('new_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1.5">Confirmar nova senha *</label>
        <input type="password" wire:model="confirm_password"
               class="w-full border border-slate-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
        @error('confirm_password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
      </div>
      <div class="pt-2">
        <button wire:click="savePassword" wire:loading.attr="disabled"
                class="inline-flex items-center gap-2 bg-slate-800 hover:bg-slate-900 text-white text-sm font-medium px-5 py-2.5 rounded-lg transition-colors disabled:opacity-70">
          <span wire:loading.remove wire:target="savePassword">Alterar senha</span>
          <span wire:loading wire:target="savePassword">Alterando...</span>
        </button>
      </div>
    </div>
  </div>
</div>
