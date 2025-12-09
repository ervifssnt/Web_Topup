@extends('admin.layout')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h1>Dashboard</h1>
    <p>Overview of your game topup system</p>
</div>

<!-- Stats Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon">👥</div>
        <div class="stat-details">
            <div class="stat-value">{{ number_format($stats['total_users']) }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">📦</div>
        <div class="stat-details">
            <div class="stat-value">{{ number_format($stats['total_transactions']) }}</div>
            <div class="stat-label">Total Transactions</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">💰</div>
        <div class="stat-details">
            <div class="stat-value">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
            <div class="stat-label">Total Revenue</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon">⏳</div>
        <div class="stat-details">
            <div class="stat-value">{{ number_format($stats['pending_transactions']) }}</div>
            <div class="stat-label">Pending Orders</div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="content-card">
    <div class="card-header">
        <h2>Recent Transactions</h2>
        <a href="{{ route('admin.transactions') }}" class="btn-link">View All</a>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Game</th>
                    <th>Amount</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recent_transactions as $transaction)
                    <tr>
                        <td>#{{ str_pad($transaction->id, 6, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ $transaction->user->username }}</td>
                        <td>{{ $transaction->topupOption->game->name }}</td>
                        <td>{{ $transaction->topupOption->amount }}</td>
                        <td>Rp {{ number_format($transaction->price, 0, ',', '.') }}</td>
                        <td>
                            <span class="badge badge-{{ $transaction->status }}">
                                {{ ucfirst($transaction->status) }}
                            </span>
                        </td>
                        <td>{{ $transaction->created_at->format('d M Y, H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            No transactions yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection