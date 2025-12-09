@extends('admin.layout')

@section('title', 'Add New Topup Package')

@section('content')
<div class="page-header">
    <h1>Add New Topup Package</h1>
    <p>Create a new topup option for a game</p>
</div>

<div class="content-card">
    <div style="padding: 24px;">
        <form action="{{ route('admin.topup-options.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Game *</label>
                <select name="game_id" class="form-control" required>
                    <option value="">Select a game</option>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}" {{ old('game_id') == $game->id ? 'selected' : '' }}>
                            {{ $game->name }}
                        </option>
                    @endforeach
                </select>
                @error('game_id')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Coins *</label>
                <input type="number" name="coins" class="form-control" value="{{ old('coins') }}" placeholder="e.g., 100" required>
                @error('coins')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Amount Description *</label>
                <input type="text" name="amount" class="form-control" value="{{ old('amount') }}" placeholder="e.g., 100 Diamonds" required>
                <small style="color: #666; font-size: 13px;">This is what users will see (e.g., "100 Diamonds", "500 VP", "60 Genesis Crystals")</small>
                @error('amount')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Price (Rp) *</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price') }}" placeholder="e.g., 15000" required>
                @error('price')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Create Package</button>
                <a href="{{ route('admin.topup-options') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection