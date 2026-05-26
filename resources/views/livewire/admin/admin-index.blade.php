<div>
  {{-- Header --}}
  <div class="mb-8">
    <h2 class="text-2xl font-semibold text-slate-900">Painel Administrativo</h2>
    <p class="text-slate-400 mt-1 text-sm">Gerencie pagamentos, usuários e categorias</p>
  </div>

  {{-- Tabs --}}
  <div class="border-b border-slate-200 mb-6">
    <nav class="flex gap-0 -mb-px">
      @php
        $tabs = [
          'reports' => ['Relatórios', 'M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9.414V19a2 2 0 01-2 2z'],
        ];
        if (auth()->user()->isAdmin()) {
          $tabs['users']      = ['Usuários',   'M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z'];
          $tabs['categories'] = ['Categorias', 'M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z'];
          $tabs['positions']  = ['Cargos',     'M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z'];
        }
      @endphp
      @foreach($tabs as $key => [$label, $icon])
        <button wire:click="setTab('{{ $key }}')"
                class="flex items-center gap-2 px-5 py-3 text-sm font-medium border-b-2 transition-colors
                       {{ $tab === $key
                          ? 'border-blue-600 text-blue-600'
                          : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300' }}">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/>
          </svg>
          {{ $label }}
        </button>
      @endforeach
    </nav>
  </div>

  {{-- Tab content --}}
  @if($tab === 'reports')
    @livewire('admin.admin-reports', key('admin-reports'))
  @elseif($tab === 'users' && auth()->user()->isAdmin())
    @livewire('admin.admin-users', key('admin-users'))
  @elseif($tab === 'categories' && auth()->user()->isAdmin())
    @livewire('admin.admin-categories', key('admin-categories'))
  @elseif($tab === 'positions' && auth()->user()->isAdmin())
    @livewire('admin.admin-positions', key('admin-positions'))
  @endif
</div>
