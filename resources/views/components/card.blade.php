@props([
    'hover' => false,
    'padding' => true,
])

@php
$classes = 'bg-dark-surface rounded-xl border border-dark-border transition-all duration-300';

if ($hover) {
    $classes .= ' card-hover cursor-pointer';
}

if ($padding) {
    $classes .= ' p-6';
}
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</div>
