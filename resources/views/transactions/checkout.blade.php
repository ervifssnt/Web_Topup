@extends('layouts.main')

@section('title', 'Checkout - UP STORE')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-3xl font-bold text-white mb-8">Checkout</h1>

    @if(session('success'))
        <x-alert type="success" class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    @if(session('error'))
        <x-alert type="error" class="mb-6">
            {{ session('error') }}
        </x-alert>
    @endif

    @if($transaction->status === 'paid')
        <!-- Transaction Complete State -->
        <x-card>
            <div class="text-center py-10">
                <div class="w-20 h-20 rounded-full bg-success/20 mx-auto mb-6 flex items-center justify-center animate-fade-in">
                    <svg class="w-12 h-12 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-white mb-3">Payment Successful!</h2>
                <p class="text-text-secondary mb-8">Your top-up has been processed successfully. The items will be added to your game account shortly.</p>

                <!-- What happens next -->
                <div class="bg-dark-elevated rounded-lg p-6 mb-8 max-w-md mx-auto text-left">
                    <h3 class="font-semibold text-white mb-4 text-center">What happens next?</h3>
                    <ol class="space-y-3">
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-xs text-primary font-bold">1</span>
                            <span class="text-sm text-text-secondary">Items will be added to your account within 5 minutes</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-xs text-primary font-bold">2</span>
                            <span class="text-sm text-text-secondary">You'll receive a confirmation email</span>
                        </li>
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary/20 flex items-center justify-center text-xs text-primary font-bold">3</span>
                            <span class="text-sm text-text-secondary">Check your transaction history for receipt</span>
                        </li>
                    </ol>
                </div>

                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <x-button variant="secondary" href="{{ route('profile.history') }}">
                        View Receipt
                    </x-button>
                    <x-button variant="primary" href="{{ route('home') }}">
                        Back to Home
                    </x-button>
                </div>
            </div>
        </x-card>
    @else
        <!-- Order Summary -->
        <x-card :padding="false" class="mb-5">
            <div class="bg-dark-elevated p-6 border-b border-dark-border">
                <h2 class="text-lg font-semibold text-white">Order Summary</h2>
            </div>

            <div class="p-8">
                <!-- Game Info -->
                <div class="flex items-center gap-5 pb-6 border-b border-dark-border mb-6">
                    @if($transaction->topupOption->game->logo)
                        <img src="{{ asset($transaction->topupOption->game->logo) }}"
                             alt="{{ $transaction->topupOption->game->name }}"
                             class="w-20 h-20 rounded-xl object-cover">
                    @else
                        <div class="w-20 h-20 rounded-xl bg-gradient-to-br from-dark-elevated to-dark-surface"></div>
                    @endif

                    <div>
                        <h3 class="text-lg font-semibold text-white mb-1">{{ $transaction->topupOption->game->name }}</h3>
                        <p class="text-sm text-text-secondary">{{ $transaction->topupOption->amount ?? $transaction->coins . ' Coins' }}</p>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="space-y-3 mb-6">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-text-secondary">Account ID</span>
                        <span class="text-white font-medium">{{ $transaction->account_id }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-text-secondary">Package</span>
                        <span class="text-white font-medium">{{ $transaction->topupOption->amount ?? $transaction->coins . ' Coins' }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-text-secondary">Transaction ID</span>
                        <span class="text-white font-medium">#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="flex justify-between items-center">
                        <span class="text-sm text-text-secondary">Status</span>
                        <x-badge variant="{{ $transaction->status === 'paid' ? 'success' : ($transaction->status === 'failed' ? 'error' : 'warning') }}">
                            {{ ucfirst($transaction->status) }}
                        </x-badge>
                    </div>
                </div>

                <!-- Price Summary -->
                <div class="bg-dark-elevated rounded-lg p-5">
                    <div class="flex justify-between py-2.5">
                        <span class="text-sm text-text-secondary">Item Price</span>
                        <span class="text-white font-medium">Rp {{ number_format($transaction->price, 0, ',', '.') }}</span>
                    </div>

                    <div class="flex justify-between py-2.5">
                        <span class="text-sm text-text-secondary">Admin Fee</span>
                        <span class="text-white font-medium">Rp 0</span>
                    </div>

                    <div class="flex justify-between pt-4 mt-3 border-t-2 border-dark-border">
                        <span class="text-base font-semibold text-white">Total Payment</span>
                        <span class="text-xl font-bold text-primary">Rp {{ number_format($transaction->price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </x-card>

        <!-- Balance Card -->
        <x-card class="mb-5">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-5">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-hover rounded-xl flex items-center justify-center text-2xl">
                        <x-icon name="wallet" class="text-white" />
                    </div>
                    <div>
                        <h3 class="text-sm text-text-secondary mb-1">Your Balance</h3>
                        <div class="text-xl font-bold text-white">Rp {{ number_format($user->balance, 0, ',', '.') }}</div>
                    </div>
                </div>

                @if($user->balance >= $transaction->price)
                    <span class="text-success font-semibold flex items-center gap-2">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        Sufficient Balance
                    </span>
                @else
                    <div class="flex flex-col gap-3 w-full">
                        <span class="text-error font-semibold flex items-center gap-2">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Insufficient Balance
                        </span>
                        <div class="bg-error/10 border border-error/30 rounded-lg p-3">
                            <p class="text-sm text-error mb-2">
                                You need <strong>Rp {{ number_format($transaction->price - $user->balance, 0, ',', '.') }}</strong> more
                            </p>
                            <x-button variant="primary" href="{{ route('topup.form') }}" size="sm">
                                <x-icon name="plus" size="sm" />
                                Top-up Balance
                            </x-button>
                        </div>
                    </div>
                @endif
            </div>
        </x-card>

        <!-- Promo Code & Payment Form -->
        <form method="POST" action="{{ route('checkout.process') }}">
            @csrf
            <input type="hidden" name="transaction_id" value="{{ $transaction->id }}">

            <!-- Promo Code Section -->
            <x-card class="mb-5">
                <label class="block text-sm font-medium text-text-primary mb-2">Have a Promo Code?</label>
                <input
                    type="text"
                    name="promo_code"
                    class="w-full px-4 py-3 bg-dark-elevated border border-dark-border rounded-lg text-white placeholder-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all"
                    placeholder="Enter promo code"
                    value="{{ old('promo_code') }}">
            </x-card>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4">
                <x-button variant="secondary" href="{{ route('home') }}" class="order-2 sm:order-1">
                    Cancel
                </x-button>
                <x-button type="submit" variant="primary" class="flex-1 order-1 sm:order-2">
                    Confirm Payment
                </x-button>
            </div>
        </form>
    @endif
</div>
@endsection
