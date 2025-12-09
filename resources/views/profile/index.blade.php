@extends('layouts.main')

@section('title', 'Dashboard - UP STORE')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-white">My Profile</h1>
        <button onclick="toggleEditMode()"
                id="editBtn"
                class="px-4 py-2 bg-dark-surface border border-dark-border rounded-lg text-white hover:border-primary transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Profile
        </button>
    </div>

    <!-- Profile Card -->
    <x-card class="mb-8">
        <form id="profileForm" action="{{ route('profile.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8">
                <div class="flex items-center gap-5">
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-primary to-primary-hover flex items-center justify-center text-3xl font-bold text-white shadow-lg">
                        {{ strtoupper(substr($user->username, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-white mb-1">Hello, {{ $user->username }}!</h3>
                        <p class="text-text-secondary text-sm">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-primary/10 to-primary/5 border-2 border-primary/30 rounded-xl px-8 py-5 text-center lg:text-right w-full lg:w-auto">
                    <div class="text-sm text-text-secondary mb-2">Current Balance</div>
                    <div class="text-3xl font-bold text-primary">Rp {{ number_format($user->balance, 0, ',', '.') }}</div>
                    <a href="{{ route('topup.form') }}" class="text-xs text-primary hover:text-primary-hover mt-2 inline-flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Top-up Balance
                    </a>
                </div>
            </div>

            <!-- Editable Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">Username</label>
                    <input type="text"
                           name="username"
                           value="{{ $user->username }}"
                           disabled
                           id="usernameInput"
                           class="w-full px-4 py-3 bg-dark-elevated border border-dark-border rounded-lg text-white disabled:opacity-50 disabled:cursor-not-allowed focus:border-primary focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">Email Address</label>
                    <input type="email"
                           name="email"
                           value="{{ $user->email }}"
                           disabled
                           id="emailInput"
                           class="w-full px-4 py-3 bg-dark-elevated border border-dark-border rounded-lg text-white disabled:opacity-50 disabled:cursor-not-allowed focus:border-primary focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">Phone Number</label>
                    <input type="tel"
                           name="phone"
                           value="{{ $user->phone }}"
                           disabled
                           id="phoneInput"
                           class="w-full px-4 py-3 bg-dark-elevated border border-dark-border rounded-lg text-white disabled:opacity-50 disabled:cursor-not-allowed focus:border-primary focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-text-secondary mb-2">Member Since</label>
                    <input type="text"
                           value="{{ $user->created_at->format('d M Y') }}"
                           disabled
                           class="w-full px-4 py-3 bg-dark-elevated border border-dark-border rounded-lg text-white opacity-50 cursor-not-allowed">
                </div>
            </div>

            <!-- Save/Cancel Buttons (Hidden by default) -->
            <div id="actionButtons" class="hidden flex gap-3 mt-6">
                <button type="submit"
                        class="px-6 py-3 bg-primary hover:bg-primary-hover rounded-lg text-white font-semibold transition-all">
                    Save Changes
                </button>
                <button type="button"
                        onclick="toggleEditMode()"
                        class="px-6 py-3 bg-dark-surface border border-dark-border hover:border-primary rounded-lg text-white font-semibold transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </x-card>

    <!-- Transaction Overview -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-5">Transaction Overview</h2>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">
            <x-stat-card
                icon="shopping-bag"
                :value="$totalOrders"
                label="Total Orders"
                iconColor="text-primary" />

            <x-stat-card
                icon="clock"
                :value="$pendingOrders"
                label="Pending"
                iconColor="text-warning" />

            <x-stat-card
                icon="check-circle"
                :value="$paidOrders"
                label="Completed"
                iconColor="text-success" />

            <x-stat-card
                icon="x"
                :value="$failedOrders"
                label="Failed"
                iconColor="text-error" />
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="mb-8">
        <h2 class="text-xl font-bold text-white mb-5">Recent Transactions</h2>

        <x-card :padding="false">
            @if($recentTransactions->count() > 0)
                @foreach($recentTransactions as $transaction)
                    <div class="p-6 border-b border-dark-border last:border-b-0 hover:bg-dark-elevated transition-colors flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-lg bg-dark-elevated flex items-center justify-center text-xl flex-shrink-0">
                                <x-icon name="gamepad" class="text-primary" />
                            </div>
                            <div>
                                <h4 class="text-white font-medium mb-1">{{ $transaction->topupOption->game->name }}</h4>
                                <p class="text-sm text-text-secondary">{{ $transaction->topupOption->amount ?? $transaction->coins . ' Coins' }} • {{ $transaction->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="flex flex-col items-start md:items-end gap-2 w-full md:w-auto">
                            <div class="text-white font-semibold">Rp {{ number_format($transaction->price, 0, ',', '.') }}</div>
                            <x-badge variant="{{ $transaction->status === 'paid' ? 'success' : ($transaction->status === 'failed' ? 'error' : 'warning') }}">
                                {{ ucfirst($transaction->status) }}
                            </x-badge>
                        </div>
                    </div>
                @endforeach

                <a href="{{ route('profile.history') }}" class="block text-center p-5 text-primary hover:bg-dark-elevated transition-colors border-t border-dark-border font-semibold">
                    View All Transactions →
                </a>
            @else
                <div class="p-20 text-center text-text-tertiary">
                    <p>No transactions yet. Start shopping!</p>
                </div>
            @endif
        </x-card>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleEditMode() {
    const isEditing = document.getElementById('usernameInput').disabled;
    const editBtn = document.getElementById('editBtn');
    const actionButtons = document.getElementById('actionButtons');

    // Toggle inputs
    document.getElementById('usernameInput').disabled = !isEditing;
    document.getElementById('emailInput').disabled = !isEditing;
    document.getElementById('phoneInput').disabled = !isEditing;

    // Toggle UI
    if (isEditing) {
        editBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
            Cancel Edit
        `;
        actionButtons.classList.remove('hidden');
        actionButtons.classList.add('flex');
    } else {
        editBtn.innerHTML = `
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Edit Profile
        `;
        actionButtons.classList.add('hidden');
        actionButtons.classList.remove('flex');

        // Reset form values
        document.getElementById('profileForm').reset();
    }
}
</script>
@endsection