@props([
    'label' => null,
    'error' => null,
    'hint' => null,
    'type' => 'text',
    'name' => null,
    'id' => null,
    'required' => false,
])

@php
$inputId = $id ?? $name ?? uniqid('input_');
$inputClasses = 'input';

if ($error) {
    $inputClasses .= ' border-status-error focus:border-status-error focus:ring-status-error/20';
}
@endphp

<div class="w-full">
    @if($label)
        <label for="{{ $inputId }}" class="block text-sm font-medium text-text-secondary mb-2">
            {{ $label }}
            @if($required)
                <span class="text-status-error">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $inputId }}"
        name="{{ $name }}"
        {{ $attributes->merge(['class' => $inputClasses]) }}
        @if($required) required @endif
    >

    @if($hint && !$error)
        <p class="mt-1.5 text-xs text-text-tertiary">{{ $hint }}</p>
    @endif

    @if($error)
        <p class="mt-1.5 text-xs text-status-error-text">{{ $error }}</p>
    @endif
</div>
