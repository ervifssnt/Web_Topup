@extends('admin.layout')

@section('title', 'Add New Game')

@section('content')
<div class="page-header">
    <h1>Add New Game</h1>
    <p>Create a new game for your store</p>
</div>

<div class="content-card">
    <div style="padding: 24px;">
        <form action="{{ route('admin.games.store') }}" method="POST">
            @csrf
            
            <div class="form-group">
                <label>Game Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g., Mobile Legends" required>
                @error('name')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="e.g., Popular MOBA game">
                @error('description')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group">
                <label>Logo Path</label>
                <input type="text" name="logo" class="form-control" value="{{ old('logo') }}" placeholder="e.g., images/mobile_legends.png">
                <small style="color: #666; font-size: 13px;">Upload the image to public/images/ first, then enter the path here</small>
                @error('logo')
                    <span style="color: #dc3545; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Create Game</button>
                <a href="{{ route('admin.games') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection