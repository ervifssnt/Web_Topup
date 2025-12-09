@extends('layouts.main')

@section('title', 'Transaction History - UP STORE')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-bold text-white mb-2">Riwayat Transaksi</h1>
            <p class="text-text-secondary">View your purchase history and transaction details</p>
        </div>
        @if($transactions->count() > 0)
            <x-button variant="secondary" onclick="exportTransactions()" class="whitespace-nowrap">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
                Export CSV
            </x-button>
        @endif
    </div>

    <!-- Search and Filter Bar -->
    @if($transactions->count() > 0)
        <x-card class="mb-6">
            <form method="GET" action="{{ route('profile.history') }}" class="flex flex-col md:flex-row gap-4">
                <!-- Search -->
                <div class="flex-1">
                    <label class="block text-xs font-medium text-text-tertiary mb-2">Search Game</label>
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search by game name..."
                           class="w-full px-4 py-2.5 bg-dark-elevated border border-dark-border rounded-lg text-white placeholder-text-tertiary focus:outline-none focus:border-primary transition-all">
                </div>

                <!-- Status Filter -->
                <div class="md:w-48">
                    <label class="block text-xs font-medium text-text-tertiary mb-2">Status</label>
                    <select name="status" class="w-full px-4 py-2.5 bg-dark-elevated border border-dark-border rounded-lg text-white focus:outline-none focus:border-primary transition-all">
                        <option value="">All Status</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Completed</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div class="md:w-48">
                    <label class="block text-xs font-medium text-text-tertiary mb-2">Month</label>
                    <input type="month"
                           name="month"
                           value="{{ request('month') }}"
                           class="w-full px-4 py-2.5 bg-dark-elevated border border-dark-border rounded-lg text-white focus:outline-none focus:border-primary transition-all">
                </div>

                <!-- Buttons -->
                <div class="flex gap-2 md:self-end">
                    <button type="submit" class="px-6 py-2.5 bg-primary hover:bg-primary-hover rounded-lg text-white font-medium transition-all">
                        Filter
                    </button>
                    @if(request('search') || request('status') || request('month'))
                        <a href="{{ route('profile.history') }}" class="px-6 py-2.5 bg-dark-elevated hover:bg-dark-hover border border-dark-border rounded-lg text-white font-medium transition-all">
                            Clear
                        </a>
                    @endif
                </div>
            </form>
        </x-card>
    @endif

    <x-card :padding="false">
        @if($transactions->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Game</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Transaction ID</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $transaction)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-lg bg-dark-elevated flex items-center justify-center flex-shrink-0">
                                            <x-icon name="gamepad" class="text-primary" />
                                        </div>
                                        <div>
                                            <h4 class="text-white font-medium mb-1">{{ $transaction->topupOption->game->name }}</h4>
                                            <p class="text-sm text-text-secondary">{{ $transaction->topupOption->amount ?? $transaction->coins . ' Coins' }}</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="text-text-primary">
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </td>

                                <td class="text-white font-semibold">
                                    Rp {{ number_format($transaction->price, 0, ',', '.') }}
                                </td>

                                <td class="text-text-secondary font-mono text-sm">
                                    <button type="button"
                                            onclick="copyToClipboard('#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}')"
                                            class="inline-flex items-center gap-2 hover:text-primary transition-colors group"
                                            title="Click to copy">
                                        #{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}
                                        <svg class="w-4 h-4 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                        </svg>
                                    </button>
                                </td>

                                <td>
                                    <x-badge variant="{{ $transaction->status === 'paid' ? 'success' : ($transaction->status === 'failed' ? 'error' : ($transaction->status === 'cancelled' ? 'default' : 'warning')) }}">
                                        {{ ucfirst($transaction->status) }}
                                    </x-badge>
                                </td>

                                <td>
                                    <div class="flex gap-2 justify-end">
                                        @if($transaction->status === 'pending')
                                            <x-button variant="primary" size="sm" href="{{ route('transaction.show', $transaction->id) }}">
                                                Pay Now
                                            </x-button>
                                        @elseif($transaction->status === 'paid')
                                            <x-button variant="success" size="sm" href="{{ route('transaction.show', $transaction->id) }}">
                                                View Receipt
                                            </x-button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($transactions->hasPages())
                <div class="p-6 flex justify-center border-t border-dark-border">
                    {{ $transactions->links() }}
                </div>
            @endif
        @else
            <div class="py-20 text-center">
                <div class="text-6xl mb-5 opacity-30">📋</div>
                <h3 class="text-xl font-semibold text-white mb-2">No Transactions Yet</h3>
                <p class="text-text-secondary mb-6">You haven't made any purchases yet. Start shopping for your favorite games!</p>
                <x-button variant="primary" href="{{ route('home') }}">
                    Browse Games
                </x-button>
            </div>
        @endif
    </x-card>
</div>
@endsection

@section('scripts')
<script>
// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        // Show success feedback
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

// Export transactions to CSV
function exportTransactions() {
    const table = document.querySelector('.data-table');
    let csv = [];

    // Get headers
    const headers = Array.from(table.querySelectorAll('thead th'))
        .slice(0, -1) // Exclude "Action" column
        .map(th => th.textContent.trim());
    csv.push(headers.join(','));

    // Get rows
    const rows = table.querySelectorAll('tbody tr');
    rows.forEach(row => {
        const cols = Array.from(row.querySelectorAll('td'));
        const rowData = [
            cols[0].querySelector('h4').textContent.trim(), // Game
            cols[1].textContent.trim(), // Date
            cols[2].textContent.trim(), // Amount
            cols[3].textContent.trim(), // Transaction ID
            cols[4].textContent.trim()  // Status
        ];
        csv.push(rowData.map(field => `"${field}"`).join(','));
    });

    // Create download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);

    link.setAttribute('href', url);
    link.setAttribute('download', `transactions_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
@endsection