@props(['active' => false, 'href' => '#'])

@php
$classes = ($active ?? false)
            ? 'flex items-center gap-3 px-3 py-2 rounded-md bg-indigo-700 text-white text-sm font-medium'
            : 'flex items-center gap-3 px-3 py-2 rounded-md text-indigo-200 hover:bg-indigo-800 hover:text-white text-sm font-medium transition-colors';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
