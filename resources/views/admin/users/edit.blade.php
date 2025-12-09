@extends('admin.layout')

@section('title', 'Edit User')

@section('content')
<div class="page-header">
    <h1>Edit User</h1>
    <p>Update user information</p>
</div>

<div class="content-card">
    <div class="card-header">
        <h2>User Details</h2>
    </div>
    
    <div style="padding: 24px;">
        <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" value="{{ $user->username }}" required>
            </div>
            
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" value="{{ $user->email }}">
            </div>
            
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" value="{{ $user->phone }}" required>
            </div>
            
            <div class="form-group">
                <label>Balance</label>
                <input type="number" step="0.01" name="balance" class="form-control" value="{{ $user->balance }}" required>
            </div>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_admin" value="1" {{ $user->is_admin ? 'checked' : '' }}>
                    Make this user an admin
                </label>
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-primary">Update User</button>
                <a href="{{ route('admin.users') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>

        <hr style="margin: 30px 0; border: 1px solid #ddd;">

<h3 style="margin-bottom: 20px;">Reset Password</h3>

<form action="{{ route('admin.users.reset-password', $user->id) }}" method="POST">
    @csrf
    
    <div class="form-group">
        <label>New Password</label>
        <input type="password" name="new_password" class="form-control" required>
        <small style="color: #666; font-size: 13px;">Must be at least 8 characters with uppercase, lowercase, number, and special character</small>
    </div>
    
    <div class="form-group">
        <label>Confirm New Password</label>
        <input type="password" name="new_password_confirmation" class="form-control" required>
    </div>
    
            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reset this user\'s password?')">
                Reset Password
            </button>
        </form>
    </div>
</div>
@endsection