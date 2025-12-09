@extends('admin.layout')

@section('title', 'Edit Game')

@section('content')
<div class="page-header">
    <h1>Edit Game</h1>
    <p>Update game information</p>
</div>

<div class="content-card">
    <div style="padding: 24px;">
        <form action="{{ route('admin.games.update', $game->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Game Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $game->name) }}" required>
                @error('name')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" class="form-control" value="{{ old('description', $game->description) }}">
                @error('description')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Logo Path</label>
                <input type="text" name="logo" class="form-control" value="{{ old('logo', $game->logo) }}">
                <small style="color: #666; font-size: 13px;">Current: {{ $game->logo ?? 'None' }}</small>
                @error('logo')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Update Game</button>
                <a href="{{ route('admin.games') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection