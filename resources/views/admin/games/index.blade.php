@extends('admin.layout')

@section('title', 'Games Management')

@section('content')
<div class="page-header">
    <h1>Games Management</h1>
    <p>Manage all games in your store</p>
</div>

<div style="margin-bottom: 24px;">
    <a href="{{ route('admin.games.create') }}" class="btn btn-primary">+ Add New Game</a>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Logo</th>
                    <th>Topup Options</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($games as $game)
                    <tr>
                        <td>{{ $game->id }}</td>
                        <td>{{ $game->name }}</td>
                        <td>{{ $game->description ?? '-' }}</td>
                        <td>
                            @if($game->logo)
                                <img src="{{ asset($game->logo) }}" alt="{{ $game->name }}" style="width: 40px; height: 40px; border-radius: 6px; object-fit: cover;">
                            @else
                                -
                            @endif
                        </td>
                        <td>{{ $game->topup_options_count }} packages</td>
                        <td>{{ $game->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.games.edit', $game->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('admin.games.delete', $game->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this game and all its topup options?')">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            No games yet. Add your first game!
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection