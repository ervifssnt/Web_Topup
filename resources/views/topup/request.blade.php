@extends('layouts.main')

@section('title', 'Top-Up Wallet')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Balance Header -->
    <div class="bg-gradient-to-br from-primary to-primary-hover rounded-xl p-8 mb-8 text-center text-white">
        <div class="text-base opacity-90 mb-2">Current Balance</div>
        <div class="text-4xl font-bold">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</div>
    </div>

    @if(session('success'))
        <x-alert type="success" class="mb-6">
            {{ session('success') }}
        </x-alert>
    @endif

    <!-- Pending Requests -->
    @if($pendingRequests->count() > 0)
        <x-card class="mb-6">
            <div class="flex items-center gap-2 text-lg font-semibold text-white mb-4">
                <x-icon name="clock" class="text-warning" />
                Pending Requests
            </div>
            @foreach($pendingRequests as $req)
                <div class="bg-warning/10 border border-warning/30 rounded-lg p-4 mb-3 last:mb-0 flex justify-between items-center flex-col sm:flex-row gap-3">
                    <div>
                        <div class="text-xl font-bold text-warning">Rp {{ number_format($req->amount, 0, ',', '.') }}</div>
                        <div class="text-sm text-warning/80">Waiting for admin approval</div>
                    </div>
                    <div class="text-sm text-warning/70 text-right">
                        {{ $req->created_at->format('d M Y, H:i') }}
                    </div>
                </div>
            @endforeach
        </x-card>
    @endif

    <!-- Top-Up Form -->
    <x-card>
        <h2 class="text-2xl font-bold text-white mb-6">New Top-Up Request</h2>

        <form action="{{ route('topup.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <x-input
                type="number"
                name="amount"
                label="Amount (Rp)"
                placeholder="Enter amount (min. Rp 10,000)"
                :value="old('amount')"
                min="10000"
                step="1000"
                hint="Minimum top-up amount is Rp 10,000"
                :error="$errors->first('amount')"
                required />

            <div class="mb-5">
                <label class="block text-sm font-medium text-text-primary mb-2">Payment Method *</label>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @php
                    $methods = [
                        'BCA' => ['icon' => '🏦', 'desc' => 'Bank Transfer'],
                        'Mandiri' => ['icon' => '🏦', 'desc' => 'Bank Transfer'],
                        'BNI' => ['icon' => '🏦', 'desc' => 'Bank Transfer'],
                        'GoPay' => ['icon' => '💳', 'desc' => 'E-Wallet'],
                        'OVO' => ['icon' => '💳', 'desc' => 'E-Wallet'],
                        'DANA' => ['icon' => '💳', 'desc' => 'E-Wallet'],
                    ];
                    @endphp
                    @foreach($methods as $method => $details)
                        <div>
                            <input type="radio" name="payment_method" value="{{ $method }}" id="{{ strtolower($method) }}" class="peer hidden" {{ $loop->first ? 'required' : '' }}>
                            <label for="{{ strtolower($method) }}" class="block p-4 bg-dark-elevated border-2 border-dark-border rounded-lg text-center cursor-pointer transition-all hover:bg-dark-surface hover:border-primary/50 peer-checked:border-primary peer-checked:bg-dark-surface peer-checked:shadow-lg peer-checked:scale-105">
                                <div class="text-2xl mb-2">{{ $details['icon'] }}</div>
                                <div class="text-white font-semibold mb-1">{{ $method }}</div>
                                <div class="text-xs text-text-tertiary">{{ $details['desc'] }}</div>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>

            <x-input
                type="file"
                name="proof_image"
                label="Payment Proof (Optional)"
                accept="image/jpeg,image/png"
                hint="Upload payment proof (Max 2MB, JPG/PNG only)"
                :error="$errors->first('proof_image')" />

            <div class="mb-6">
                <div class="flex justify-between items-center mb-2">
                    <label for="notes" class="block text-sm font-medium text-text-primary">Notes (Optional)</label>
                    <span class="text-xs text-text-tertiary"><span id="charCount">0</span>/500</span>
                </div>
                <textarea
                    name="notes"
                    id="notes"
                    rows="4"
                    maxlength="500"
                    class="w-full px-4 py-3 bg-dark-elevated border border-dark-border rounded-lg text-white placeholder-text-tertiary focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all resize-vertical"
                    placeholder="Any additional information (e.g., account details, special requests)..."
                    oninput="document.getElementById('charCount').textContent = this.value.length">{{ old('notes') }}</textarea>
                <p class="text-xs text-text-tertiary mt-1.5">Add any relevant details that might help us process your request faster</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <x-button type="submit" variant="primary" class="flex-1">
                    Submit Request
                </x-button>
                <x-button variant="secondary" href="{{ route('topup.history') }}">
                    View History
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection