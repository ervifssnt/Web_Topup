@extends('admin.layout')

@section('title', 'Topup Options Management')

@section('content')
<div class="page-header">
    <h1>Topup Options Management</h1>
    <p>Manage all topup packages for your games</p>
</div>

<div style="margin-bottom: 24px;">
    <a href="{{ route('admin.topup-options.create') }}" class="btn btn-primary">+ Add New Package</a>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Game</th>
                    <th>Coins</th>
                    <th>Amount Description</th>
                    <th>Price</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($options as $option)
                    <tr>
                        <td>{{ $option->id }}</td>
                        <td>{{ $option->game->name }}</td>
                        <td>{{ $option->coins }}</td>
                        <td>{{ $option->amount }}</td>
                        <td>Rp {{ number_format($option->price, 0, ',', '.') }}</td>
                        <td>{{ $option->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.topup-options.edit', $option->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('admin.topup-options.delete', $option->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this topup option?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            No topup options yet. Add your first package!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection