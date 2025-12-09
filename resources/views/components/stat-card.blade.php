@props([
    'icon' => 'star',
    'label' => '',
    'value' => '0',
    'iconColor' => 'text-primary',
])

<x-card>
    <div class="flex items-center gap-4">
        <div class="p-4 rounded-xl bg-dark-elevated">
            <x-icon :name="$icon" size="lg" class="{{ $iconColor }}" />
        </div>
        <div class="flex-1">
            <div class="text-2xl font-bold text-white mb-1">{{ $value }}</div>
            <div class="text-sm text-text-tertiary">{{ $label }}</div>
        </div>
    </div>
</x-card>
