@extends('admin.layout')

@section('title', 'Edit Topup Package')

@section('content')
<div class="page-header">
    <h1>Edit Topup Package</h1>
    <p>Update topup option details</p>
</div>

<div class="content-card">
    <div style="padding: 24px;">
        <form action="{{ route('admin.topup-options.update', $option->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Game *</label>
                <select name="game_id" class="form-control" required>
                    @foreach($games as $game)
                        <option value="{{ $game->id }}" {{ old('game_id', $option->game_id) == $game->id ? 'selected' : '' }}>
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
                <input type="number" name="coins" class="form-control" value="{{ old('coins', $option->coins) }}" required>
                @error('coins')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Amount Description *</label>
                <input type="text" name="amount" class="form-control" value="{{ old('amount', $option->amount) }}" required>
                @error('amount')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Price (Rp) *</label>
                <input type="number" step="0.01" name="price" class="form-control" value="{{ old('price', $option->price) }}" required>
                @error('price')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Update Package</button>
                <a href="{{ route('admin.topup-options') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection