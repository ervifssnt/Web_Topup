@extends('admin.layout')

@section('title', 'Transactions')

@section('content')
<div class="page-header">
    <h1>All Transactions</h1>
    <p>View all customer transactions</p>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Game</th>
                    <th>Package</th>
                    <th>Account ID</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($transactions as $transaction)
                    <tr>
                        <td>#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $transaction->user->username }}</td>
                        <td>{{ $transaction->topupOption->game->name }}</td>
                        <td>{{ $transaction->topupOption->amount }}</td>
                        <td>{{ $transaction->account_id }}</td>
                        <td>Rp {{ number_format($transaction->price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $transaction->status }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="padding: 20px;">
        {{ $transactions->links() }}
    </div>
</div>
@endsection