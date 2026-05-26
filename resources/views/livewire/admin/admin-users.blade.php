<div>
  {{-- Header --}}
  <div class="flex items-center justify-between mb-4">
    <p class="text-sm text-slate-500">{{ $users->count() }} {{ $users->count() === 1 ? 'usuário' : 'usuários' }} cadastrado(s)</p>
    <button wire:click="openCreate"
            class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition-colors duration-150 ease-out">
      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
      </svg>
      Novo Usuário
    </button>
  </div>

  {{-- Tabela --}}
  <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nome</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">E-mail</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Perfis</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Notificações</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Desde</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $usr)
          <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors duration-150 ease-out">
            <td class="px-4 py-3 font-medium">
              {{ $usr->name }}
              @if($usr->id === auth()->id())
                <span class="ml-1.5 text-xs text-slate-400">(você)</span>
              @endif
            </td>
            <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $usr->email }}</td>
            <td class="px-4 py-3">
              <div class="flex flex-wrap gap-1">
                @foreach($usr->roles ?? [] as $role)
                  @php
                    $colors = [
                      'admin'        => 'blue',
                      'collaborator' => 'gray',
                      'hr'           => 'purple',
                      'financial'    => 'emerald',
                    ];
                    $labels = [
                      'admin'        => 'Admin',
                      'collaborator' => 'Colaborador',
                      'hr'           => 'RH',
                      'financial'    => 'Financeiro',
                    ];
                  @endphp
                  <x-status-badge :color="$colors[$role] ?? 'gray'">{{ $labels[$role] ?? $role }}</x-status-badge>
                @endforeach
              </div>
            </td>
            <td class="px-4 py-3">
              <span class="{{ $usr->notify_email ? 'text-emerald-600' : 'text-slate-400' }} text-xs">
                {{ $usr->notify_email ? '✓ Ativo' : '✗ Desativado' }}
              </span>
            </td>
            <td class="px-4 py-3 text-xs text-slate-400">{{ $usr->created_at->format('d/m/Y') }}</td>
            <td class="px-4 py-3 text-right">
              <div class="flex items-center justify-end gap-2">
                <button wire:click="openEdit({{ $usr->id }})"
                        class="text-xs border border-slate-200 hover:border-blue-300 text-slate-600 hover:text-blue-600 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                  Editar
                </button>
                @if($usr->id !== auth()->id())
                  <button wire:click="delete({{ $usr->id }})"
                          wire:confirm="Remover o usuário {{ $usr->name }}? Esta ação não pode ser desfeita."
                          class="text-xs border border-red-200 hover:border-red-400 text-red-500 hover:text-red-700 rounded-lg px-3 py-1.5 transition-colors duration-150 ease-out">
                    Remover
                  </button>
                @endif
              </div>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>

  {{-- Modal criar/editar usuário --}}
  @if($showModal)
    <div class="fixed inset-0 bg-slate-900/40 z-50 flex items-center justify-center p-6"
         wire:click.self="closeModal">
      <div class="bg-white rounded-2xl w-full max-w-md shadow-2xl">
        <div class="px-6 pt-6 pb-4 border-b border-slate-100">
          <h3 class="text-lg font-semibold">{{ $editingId ? 'Editar Usuário' : 'Novo Usuário' }}</h3>
        </div>
        <div class="p-6 space-y-4">

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">Nome *</label>
            <input type="text" wire:model="name" placeholder="Nome completo"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('name') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">E-mail *</label>
            <input type="email" wire:model="email" placeholder="email@empresa.com"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('email') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-1.5">
              Senha {{ $editingId ? '(deixe em branco para não alterar)' : '*' }}
            </label>
            <input type="password" wire:model="password" placeholder="Mínimo 8 caracteres"
                   class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            @error('password') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

          @if(!$editingId || $password)
            <div>
              <label class="block text-sm font-medium text-slate-600 mb-1.5">Confirmar senha *</label>
              <input type="password" wire:model="password_confirmation" placeholder="Repita a senha"
                     class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500">
            </div>
          @endif

          <div>
            <label class="block text-sm font-medium text-slate-600 mb-2">Perfis *</label>
            <div class="space-y-2 border border-slate-200 rounded-lg p-3">
              @foreach($availableRoles as $roleKey => $roleLabel)
                <label class="flex items-center gap-3 cursor-pointer">
                  <input type="checkbox"
                         wire:model="selectedRoles"
                         value="{{ $roleKey }}"
                         class="h-4 w-4 rounded border-slate-300 text-blue-600 focus:ring-blue-500">
                  <div>
                    <span class="text-sm font-medium text-slate-700">{{ $roleLabel }}</span>
                    @php
                      $descriptions = [
                        'collaborator' => 'Cadastra despesas e gera relatórios próprios',
                        'admin'        => 'Acesso total: pagamentos, usuários e categorias',
                        'hr'           => 'Acesso ao módulo de recrutamento',
                        'financial'    => 'Acesso ao painel de relatórios pendentes de pagamento',
                      ];
                    @endphp
                    <p class="text-xs text-slate-400">{{ $descriptions[$roleKey] ?? '' }}</p>
                  </div>
                </label>
              @endforeach
            </div>
            @error('selectedRoles') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
          </div>

        </div>
        <div class="px-6 pb-6 flex gap-3 justify-end">
          <button wire:click="closeModal"
                  class="px-4 py-2 text-sm font-medium text-slate-600 bg-white border border-slate-200 rounded-lg hover:bg-slate-50 transition-colors duration-150 ease-out">
            Cancelar
          </button>
          <button wire:click="save" wire:loading.attr="disabled" wire:target="save"
                  class="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-700 transition-colors disabled:opacity-70">
            <span wire:loading.remove wire:target="save">{{ $editingId ? 'Salvar' : 'Criar Usuário' }}</span>
            <span wire:loading wire:target="save">Salvando...</span>
          </button>
        </div>
      </div>
    </div>
  @endif
</div>
