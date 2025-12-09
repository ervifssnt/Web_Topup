@props([
    'id' => 'modal',
    'title' => '',
    'size' => 'md',
])

@php
$sizes = [
    'sm' => 'max-w-md',
    'md' => 'max-w-lg',
    'lg' => 'max-w-2xl',
    'xl' => 'max-w-4xl',
];
@endphp

<div
    x-data="{ show: false }"
    @open-modal-{{ $id }}.window="show = true"
    @close-modal-{{ $id }}.window="show = false"
    @keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <!-- Backdrop -->
    <div
        @click="show = false"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 modal-backdrop"
    ></div>

    <!-- Modal Content -->
    <div
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        {{ $attributes->merge(['class' => 'modal relative z-10 ' . ($sizes[$size] ?? $sizes['md'])]) }}
    >
        @if($title)
        <div class="border-b border-dark-border p-6 flex items-center justify-between">
            <h3 class="text-xl font-semibold text-white">{{ $title }}</h3>
            <button
                @click="show = false"
                type="button"
                class="text-text-tertiary hover:text-white transition-colors"
            >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        @endif

        <div class="p-6">
            {{ $slot }}
        </div>
    </div>
</div>
