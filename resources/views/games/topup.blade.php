@extends('layouts.main')

@section('title', 'Top Up - ' . $game->name)

@section('content')
<div class="max-w-7xl mx-auto px-4">
    <!-- Game Header -->
    <x-card class="mb-8 flex flex-col md:flex-row items-center gap-8 md:text-left text-center">
        @if($game->logo)
            <img src="{{ asset($game->logo) }}" alt="{{ $game->name }}" class="w-36 h-36 md:w-40 md:h-40 rounded-2xl object-cover bg-gradient-to-br from-dark-border to-dark-surface flex-shrink-0">
        @else
            <div class="w-36 h-36 md:w-40 md:h-40 rounded-2xl bg-gradient-to-br from-dark-border to-dark-surface flex-shrink-0"></div>
        @endif
        <div class="flex-1">
            <h1 class="text-3xl font-bold text-white mb-2">{{ $game->name }}</h1>
            <p class="text-text-secondary">{{ $game->description ?? 'Moonton' }}</p>
        </div>
    </x-card>

    @if($errors->any())
        <x-alert type="error" class="mb-5">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </x-alert>
    @endif
    
    <form id="topupForm" method="POST" action="{{ route('topup.store') }}">
        @csrf

        <!-- Account Details -->
        <x-card class="mb-5">
            <h2 class="text-lg font-semibold text-white mb-5">Enter account details</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <x-input
                    label="User ID"
                    name="account_id"
                    id="userId"
                    type="text"
                    placeholder="Enter your User ID"
                    value="{{ old('account_id') }}"
                    required
                />
                <x-input
                    label="Zone ID"
                    name="zone_id"
                    id="zoneId"
                    type="text"
                    placeholder="Enter your Zone ID"
                    value="{{ old('zone_id') }}"
                />
            </div>
        </x-card>
        
        <!-- Flash Sale Items -->
        @php
            $flashSaleItems = $game->topupOptions->take(1);
            $regularItems = $game->topupOptions->skip(1);
        @endphp

        @if($flashSaleItems->count() > 0)
        <x-card class="mb-5">
            <div class="flex items-center gap-3 mb-5">
                <h2 class="text-lg font-semibold text-white">Flash Sale</h2>
                <x-badge variant="primary" class="animate-pulse">Limited Time</x-badge>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($flashSaleItems as $option)
                    <label class="payment-method relative cursor-pointer block" data-price="{{ $option->price }}" data-name="{{ $option->amount ?? $option->coins . ' Coins' }}">
                        <input type="radio"
                               name="topup_option_id"
                               value="{{ $option->id }}"
                               class="absolute opacity-0 pointer-events-none"
                               {{ old('topup_option_id') == $option->id ? 'checked' : '' }}>
                        <div class="text-sm font-semibold text-white mb-2">{{ $option->amount ?? $option->coins . ' Diamonds' }}</div>
                        <div class="text-base font-bold text-primary">Rp {{ number_format($option->price, 0, ',', '.') }}</div>
                    </label>
                @endforeach
            </div>
        </x-card>
        @endif
        
        <!-- Choose Item (Diamonds) -->
        <x-card class="mb-5">
            <h2 class="text-lg font-semibold text-white mb-5">Select Amount</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                @foreach($regularItems as $option)
                    <label class="payment-method relative cursor-pointer block" data-price="{{ $option->price }}" data-name="{{ $option->amount ?? $option->coins . ' Coins' }}">
                        <input type="radio"
                               name="topup_option_id"
                               value="{{ $option->id }}"
                               class="absolute opacity-0 pointer-events-none"
                               {{ old('topup_option_id') == $option->id ? 'checked' : '' }}
                               required>
                        <div class="text-sm font-semibold text-white mb-2">{{ $option->amount ?? $option->coins . ' Diamonds' }}</div>
                        <div class="text-base font-bold text-primary">Rp {{ number_format($option->price, 0, ',', '.') }}</div>
                    </label>
                @endforeach
            </div>
        </x-card>
        
        <!-- Bottom Section: Summary + Purchase Controls -->
        <x-card>
            <!-- Selected Summary -->
            <div class="flex justify-between items-center mb-6 pb-6 border-b border-dark-border">
                <div>
                    <h3 id="selectedItemName" class="text-xl font-bold text-white mb-1">Rp 0</h3>
                    <p id="selectedItemDesc" class="text-text-secondary text-sm">Please select an item</p>
                </div>
                <div id="totalPrice" class="text-2xl font-bold text-white">Rp 0</div>
            </div>

            <!-- Purchase Controls -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                <div class="flex flex-col">
                    <label class="block text-sm font-medium text-text-secondary mb-2">Purchase Quantity</label>
                    <div class="flex items-center gap-3">
                        <button type="button"
                                class="w-12 h-12 bg-primary hover:bg-primary-hover disabled:bg-dark-border disabled:text-text-tertiary disabled:cursor-not-allowed rounded-lg text-white text-xl font-bold transition-all flex items-center justify-center hover:scale-110 active:scale-95"
                                id="decreaseQty">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"/>
                            </svg>
                        </button>
                        <input type="number"
                               name="quantity"
                               id="quantity"
                               class="qty-input w-20 text-center px-3 py-3 bg-dark-elevated border-2 border-dark-border rounded-lg text-white text-lg font-bold focus:outline-none focus:border-primary transition-all [appearance:textfield] [&::-webkit-outer-spin-button]:appearance-none [&::-webkit-inner-spin-button]:appearance-none"
                               value="1"
                               min="1"
                               max="99"
                               readonly>
                        <button type="button"
                                class="w-12 h-12 bg-primary hover:bg-primary-hover disabled:bg-dark-border disabled:text-text-tertiary disabled:cursor-not-allowed rounded-lg text-white text-xl font-bold transition-all flex items-center justify-center hover:scale-110 active:scale-95"
                                id="increaseQty">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-text-tertiary mt-2">Max 99 items per purchase</p>
                </div>
            </div>

            <!-- Buy Now Button -->
            <button type="submit"
                    class="w-full px-6 py-4 bg-primary hover:bg-primary-hover disabled:bg-dark-border disabled:text-text-tertiary disabled:cursor-not-allowed disabled:transform-none rounded-lg text-white text-lg font-bold transition-all hover:transform hover:-translate-y-0.5 hover:shadow-[0_4px_12px_rgba(255,140,0,0.4)] flex items-center justify-center gap-2 group"
                    id="buyNowBtn"
                    disabled>
                <span id="buyNowText">Select an item to continue</span>
                <svg class="w-5 h-5 hidden group-enabled:inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </button>
        </x-card>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('topupForm');
    const itemCards = document.querySelectorAll('.payment-method');
    const quantityInput = document.getElementById('quantity');
    const decreaseBtn = document.getElementById('decreaseQty');
    const increaseBtn = document.getElementById('increaseQty');
    const buyNowBtn = document.getElementById('buyNowBtn');
    const buyNowText = document.getElementById('buyNowText');
    const selectedItemName = document.getElementById('selectedItemName');
    const selectedItemDesc = document.getElementById('selectedItemDesc');
    const totalPrice = document.getElementById('totalPrice');

    let selectedPrice = 0;
    let selectedName = '';

    // Handle item selection
    itemCards.forEach(card => {
        card.addEventListener('click', function() {
            // Get price and name
            selectedPrice = parseFloat(this.dataset.price);
            selectedName = this.dataset.name;

            // Update display
            updateSummary();
            buyNowBtn.disabled = false;
            buyNowText.textContent = 'Buy Now';
        });

        // Check if already selected (on page load)
        const radio = card.querySelector('input[type="radio"]');
        if (radio && radio.checked) {
            selectedPrice = parseFloat(card.dataset.price);
            selectedName = card.dataset.name;
            updateSummary();
            buyNowBtn.disabled = false;
            buyNowText.textContent = 'Buy Now';
        }
    });

    // Quantity controls
    decreaseBtn.addEventListener('click', function() {
        let qty = parseInt(quantityInput.value);
        if (qty > 1) {
            quantityInput.value = qty - 1;
            updateSummary();
        }
    });

    increaseBtn.addEventListener('click', function() {
        let qty = parseInt(quantityInput.value);
        if (qty < 99) {
            quantityInput.value = qty + 1;
            updateSummary();
        }
    });

    function updateSummary() {
        const qty = parseInt(quantityInput.value);
        const total = selectedPrice * qty;

        selectedItemName.textContent = 'Rp ' + formatNumber(total);
        selectedItemDesc.textContent = selectedName + (qty > 1 ? ' x ' + qty : '');
        totalPrice.textContent = 'Rp ' + formatNumber(total);
    }

    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
});
</script>
@endsection