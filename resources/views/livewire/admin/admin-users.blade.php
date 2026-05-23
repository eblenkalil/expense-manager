{{-- resources/views/livewire/admin/admin-users.blade.php --}}
<div>
  <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-slate-50 border-b border-slate-200">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Nome</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">E-mail</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Notificações</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Perfil</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Desde</th>
          <th class="px-4 py-3"></th>
        </tr>
      </thead>
      <tbody>
        @foreach($users as $usr)
          <tr class="border-b border-slate-100 last:border-0 hover:bg-slate-50 transition-colors">
            <td class="px-4 py-3 font-medium">{{ $usr->name }}</td>
            <td class="px-4 py-3 text-slate-500 font-mono text-xs">{{ $usr->email }}</td>
            <td class="px-4 py-3">
              <span class="{{ $usr->notify_email ? 'text-emerald-600' : 'text-slate-400' }} text-xs">
                {{ $usr->notify_email ? '✓ Ativo' : '✗ Desativado' }}
              </span>
            </td>
            <td class="px-4 py-3">
              <x-status-badge :color="$usr->role === 'admin' ? 'blue' : 'gray'">
                {{ $usr->role === 'admin' ? 'Admin' : 'Colaborador' }}
              </x-status-badge>
            </td>
            <td class="px-4 py-3 text-xs text-slate-400">{{ $usr->created_at->format('d/m/Y') }}</td>
            <td class="px-4 py-3 text-right">
              @if($usr->id !== auth()->id())
                <button wire:click="toggleRole({{ $usr->id }})"
                        wire:confirm="Alterar o perfil de {{ $usr->name }}?"
                        class="text-xs border border-slate-200 hover:border-blue-300 text-slate-600 hover:text-blue-600 rounded-lg px-3 py-1.5 transition-colors">
                  {{ $usr->role === 'admin' ? '→ Colaborador' : '→ Admin' }}
                </button>
              @else
                <span class="text-xs text-slate-400">Você</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
