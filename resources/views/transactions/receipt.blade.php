@extends('layouts.main')

@section('title', 'Transaction Receipt')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Print/Download Actions -->
    <div class="flex justify-end gap-2 mb-4 no-print">
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2 bg-dark-surface hover:bg-dark-hover border border-dark-border rounded-lg text-white text-sm font-medium transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Print Receipt
        </button>
    </div>

    <x-card :padding="false" id="receipt">
        <!-- Receipt Header -->
        <div class="bg-gradient-to-br from-success to-success/80 p-10 text-center text-white">
            <div class="text-6xl mb-4">✓</div>
            <h1 class="text-2xl font-bold mb-2">Payment Successful</h1>
            <p class="text-sm opacity-90">{{ $transaction->updated_at->format('d M Y, H:i') }}</p>
        </div>

        <div class="p-8">
            <!-- Transaction ID -->
            <div class="text-center p-5 bg-dark-elevated rounded-lg mb-6">
                <div class="text-xs text-text-secondary mb-2">Transaction ID</div>
                <button type="button"
                        onclick="copyReceiptID('#{{ str_pad($transaction->id, 8, '0', STR_PAD_LEFT) }}')"
                        class="text-xl font-bold text-primary tracking-wide hover:text-primary-hover transition-colors inline-flex items-center gap-2 group"
                        title="Click to copy">
                    #{{ str_pad($transaction->id, 8, '0', STR_PAD_LEFT) }}
                    <svg class="w-5 h-5 opacity-0 group-hover:opacity-100 transition-opacity no-print" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </button>
            </div>

            <!-- Game Information -->
            <div class="pb-6 mb-6 border-b border-dark-border">
                <div class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-4">Game Details</div>
                <div class="flex items-center gap-4">
                    @if($transaction->topupOption->game->logo)
                        <img src="{{ asset($transaction->topupOption->game->logo) }}"
                             alt="{{ $transaction->topupOption->game->name }}"
                             class="w-15 h-15 rounded-lg object-cover">
                    @else
                        <div class="w-15 h-15 rounded-lg bg-dark-elevated"></div>
                    @endif

                    <div>
                        <div class="text-lg font-semibold text-white mb-1">{{ $transaction->topupOption->game->name }}</div>
                        <div class="text-sm text-text-secondary">{{ $transaction->topupOption->amount ?? $transaction->coins . ' Coins' }}</div>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            <div class="pb-6 mb-6 border-b border-dark-border">
                <div class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-4">Account Information</div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-text-secondary">Account ID</span>
                        <span class="text-white font-medium text-right">{{ $transaction->account_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-text-secondary">Player</span>
                        <span class="text-white font-medium text-right">{{ $user->username }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="pb-6 mb-6 border-b border-dark-border">
                <div class="text-xs font-semibold text-text-secondary uppercase tracking-wider mb-4">Payment Summary</div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-sm text-text-secondary">Item Price</span>
                        <span class="text-white font-medium">Rp {{ number_format($transaction->price, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-sm text-text-secondary">Admin Fee</span>
                        <span class="text-white font-medium">Rp 0</span>
                    </div>
                    <div class="flex justify-between pt-4 mt-3 border-t-2 border-dark-border">
                        <span class="text-base font-semibold text-white">Total Paid</span>
                        <span class="text-xl font-bold text-success">Rp {{ number_format($transaction->price, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </x-card>

    <!-- Action Buttons -->
    <div class="flex flex-col sm:flex-row gap-3 mt-5 no-print">
        <x-button variant="secondary" href="{{ route('profile.history') }}">
            View History
        </x-button>
        <x-button variant="primary" href="{{ route('home') }}" class="flex-1">
            Back to Home
        </x-button>
    </div>

    <!-- Support Section -->
    <x-card class="mt-5 no-print">
        <div class="flex items-start gap-4">
            <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-white font-semibold mb-1">Need Help?</h3>
                <p class="text-sm text-text-secondary mb-3">
                    If you didn't receive your items within 5 minutes or have questions about this transaction, please contact our support team.
                </p>
                <a href="mailto:support@upstore.com" class="inline-flex items-center gap-2 text-sm text-primary hover:text-primary-hover font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    Contact Support
                </a>
            </div>
        </div>
    </x-card>
</div>
@endsection

@section('scripts')
<script>
function copyReceiptID(text) {
    navigator.clipboard.writeText(text).then(() => {
        const notification = document.createElement('div');
        notification.className = 'fixed bottom-4 right-4 bg-success text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
        notification.textContent = `Copied: ${text}`;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy transaction ID');
    });
}
</script>

<style media="print">
    .no-print { display: none !important; }
    body { background: white; }
    nav, footer { display: none; }
    .bg-dark-elevated { background: #f5f5f5 !important; }
    .text-white { color: #000 !important; }
    .text-text-secondary { color: #666 !important; }
    .border-dark-border { border-color: #ddd !important; }
</style>
@endsection
