@props(['href', 'active' => false])

<a href="{{ $href }}"
   class="flex items-center gap-2.5 px-3 py-2 rounded-lg text-sm font-medium transition-colors
          {{ $active
             ? 'bg-blue-50 text-blue-700'
             : 'text-slate-500 hover:bg-slate-100 hover:text-slate-900' }}">
  <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    {{ $icon }}
  </svg>
  <span>{{ $slot }}</span>
</a>
