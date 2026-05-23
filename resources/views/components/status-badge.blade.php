@props(['color' => 'gray'])

@php
$classes = match($color) {
    'green' => 'bg-emerald-50 text-emerald-700',
    'amber' => 'bg-amber-50 text-amber-700',
    'blue'  => 'bg-blue-50 text-blue-700',
    default => 'bg-slate-100 text-slate-600',
};
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium font-mono $classes"]) }}>
    {{ $slot }}
</span>
