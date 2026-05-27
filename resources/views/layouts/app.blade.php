<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $title ?? 'Dashboard' }} — Gestão de Despesas</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600&family=DM+Mono:wght@400;500&display=swap" rel="stylesheet">
@vite(['resources/css/app.css', 'resources/js/app.js'])
@livewireStyles
</head>
<body class="bg-slate-50 font-sans text-slate-900 antialiased">

<div class="flex min-h-screen">

  {{-- Sidebar --}}
  <aside class="w-60 bg-white border-r border-slate-200 flex flex-col fixed inset-y-0 left-0 z-30">

    <div class="px-6 py-4 border-b border-slate-100">
      <a href="{{ route('dashboard') }}">
        <img src="{{ asset('images/logo.png') }}" alt="Veloce Tech" class="h-24 w-auto">
      </a>
    </div>

    <nav class="flex-1 px-3 py-4 space-y-0.5 overflow-y-auto">
      <x-nav-item href="{{ route('dashboard') }}" :active="request()->routeIs('dashboard')">
        <x-slot:icon><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></x-slot:icon>
        Dashboard
      </x-nav-item>

      <x-nav-item href="{{ route('expenses.index') }}" :active="request()->routeIs('expenses.*')">
        <x-slot:icon><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z"/></x-slot:icon>
        Minhas Despesas
      </x-nav-item>

      <x-nav-item href="{{ route('reports.index') }}" :active="request()->routeIs('reports.*')">
        <x-slot:icon><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414A1 1 0 0120 9.414V19a2 2 0 01-2 2z"/></x-slot:icon>
        Relatórios
      </x-nav-item>

      @if(auth()->user()->isAdmin() || auth()->user()->isFinancial())
        <div class="pt-3 pb-1 px-3">
          <p class="text-xs font-mono text-slate-400 uppercase tracking-widest">Gestão</p>
        </div>
        <x-nav-item href="{{ route('admin.index') }}" :active="request()->routeIs('admin.*')">
          <x-slot:icon><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></x-slot:icon>
          {{ auth()->user()->isAdmin() ? 'Painel Admin' : 'Pagamentos' }}
        </x-nav-item>
      @endif

      @if(auth()->user()->isAdmin() || auth()->user()->isHr())
        @if(!auth()->user()->isAdmin() && !auth()->user()->isFinancial())
          <div class="pt-3 pb-1 px-3">
            <p class="text-xs font-mono text-slate-400 uppercase tracking-widest">Gestão</p>
          </div>
        @endif
        <x-nav-item href="{{ route('hr.jobs.index') }}" :active="request()->routeIs('hr.*')">
          <x-slot:icon><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></x-slot:icon>
          Recrutamento
        </x-nav-item>
      @endif
    </nav>

    {{-- User info --}}
    <div class="px-4 py-4 border-t border-slate-100">
      <div class="flex items-center gap-3">
        <a href="{{ route('profile') }}"
           class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs font-semibold flex-shrink-0 hover:bg-blue-700 transition-colors duration-150 ease-out"
           title="Meu Perfil">
          {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
        </a>
        <div class="min-w-0 flex-1">
          <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
          <p class="text-xs text-slate-400 truncate">{{ auth()->user()->role_label }}</p>
        </div>
        <form method="POST" action="{{ route('logout') }}" class="flex-shrink-0">
          @csrf
          <button type="submit" title="Sair" class="text-slate-400 hover:text-red-500 transition-colors duration-150 ease-out">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
            </svg>
          </button>
        </form>
      </div>
    </div>

  </aside>

  {{-- Page content --}}
  <main class="flex-1 ml-60 min-h-screen">
    <div class="max-w-6xl mx-auto px-8 py-8">

      {{-- Flash messages --}}
      @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
             class="mb-6 flex items-center gap-3 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl px-4 py-3 text-sm">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
             class="mb-6 flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-xl px-4 py-3 text-sm">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ session('error') }}
        </div>
      @endif

      @if(session('warning'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 6000)"
             class="mb-6 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-sm">
          <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
          </svg>
          {{ session('warning') }}
        </div>
      @endif

      {{ $slot }}

    </div>
  </main>

</div>

@livewireScripts
<script src="https://cdn.jsdelivr.net/npm/chart.js@4"></script>
<script src="https://cdn.jsdelivr.net/npm/@tiptap/core@2/dist/tiptap-core.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@tiptap/starter-kit@2/dist/tiptap-starter-kit.umd.min.js"></script>
<script>
function tiptapEditor() {
    return {
        editor: null,
        init() {
            const TiptapCore = window.TiptapCore || window.tiptapCore;
            const TiptapStarterKit = window.TiptapStarterKit || window.tiptapStarterKit;
            if (!TiptapCore || !TiptapStarterKit) return;
            const self = this;
            this.editor = new TiptapCore.Editor({
                element: this.$refs.editorEl,
                extensions: [TiptapStarterKit.StarterKit],
                content: this.$wire.description || '',
                editorProps: {
                    attributes: { class: 'min-h-[130px] focus:outline-none' }
                },
                onUpdate({ editor }) {
                    self.$wire.set('description', editor.getHTML());
                },
            });
            this.$cleanup(() => { this.editor?.destroy(); });
        },
        cmd(fn) { fn(this.editor?.chain().focus()); },
        active(type, opts) { return this.editor?.isActive(type, opts) ?? false; },
    };
}
</script>
</body>
</html>
