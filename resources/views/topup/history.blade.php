@extends('layouts.main')

@section('title', 'Top-Up History')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white mb-2">Top-Up History</h1>
        <p class="text-text-secondary">View all your balance top-up requests and their status</p>
    </div>

    <x-card :padding="false">
        @if($requests->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Status</th>
                            <th>Admin Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td data-label="Date" class="text-text-primary">
                                    {{ $request->created_at->format('d M Y, H:i') }}
                                </td>
                                <td data-label="Amount">
                                    <strong class="text-white font-semibold">Rp {{ number_format($request->amount, 0, ',', '.') }}</strong>
                                </td>
                                <td data-label="Payment" class="text-text-primary">
                                    {{ $request->payment_method }}
                                </td>
                                <td data-label="Status">
                                    <x-badge variant="{{ $request->status === 'approved' ? 'success' : ($request->status === 'rejected' ? 'error' : 'warning') }}">
                                        {{ ucfirst($request->status) }}
                                    </x-badge>
                                </td>
                                <td data-label="Notes" class="text-text-primary">
                                    @if($request->admin_notes)
                                        {{ $request->admin_notes }}
                                    @else
                                        <span class="text-text-tertiary">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($requests->hasPages())
                <div class="p-6 flex justify-center border-t border-dark-border">
                    {{ $requests->links() }}
                </div>
            @endif
        @else
            <div class="py-20 text-center">
                <div class="text-6xl mb-5 opacity-30">📋</div>
                <h3 class="text-xl font-semibold text-white mb-2">No Top-Up History</h3>
                <p class="text-text-secondary mb-6">You haven't made any top-up requests yet</p>
                <x-button variant="primary" href="{{ route('topup.form') }}">
                    Make Your First Top-Up
                </x-button>
            </div>
        @endif
    </x-card>

    <div class="mt-6">
        <x-button variant="primary" href="{{ route('topup.form') }}">
            New Top-Up Request
        </x-button>
    </div>
</div>
@endsection