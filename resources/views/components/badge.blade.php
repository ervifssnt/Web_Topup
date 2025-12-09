@props([
    'variant' => 'default',
    'size' => 'md',
])

@php
$variants = [
    'success' => 'badge-success',
    'warning' => 'badge-warning',
    'error' => 'badge-error',
    'info' => 'badge-info',
    'default' => 'bg-dark-border text-text-secondary',
    'primary' => 'bg-primary/20 text-primary border border-primary/30',
];

$sizes = [
    'sm' => 'px-2 py-0.5 text-xs',
    'md' => 'px-3 py-1 text-xs',
    'lg' => 'px-4 py-1.5 text-sm',
];

$classes = 'badge inline-flex items-center rounded-full font-semibold';
$classes .= ' ' . ($variants[$variant] ?? $variants['default']);
$classes .= ' ' . ($sizes[$size] ?? $sizes['md']);
@endphp

<span {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</span>
