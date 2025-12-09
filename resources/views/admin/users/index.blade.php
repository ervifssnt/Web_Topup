@extends('admin.layout')

@section('title', 'Users Management')

@section('content')
<div class="page-header">
    <h1>Users Management</h1>
    <p>Manage all registered users</p>
</div>

<div class="content-card">
    <div class="card-header">
        <h2>All Users</h2>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Balance</th>
                    <th>Admin</th>
                    <th>Status</th> 
                    <th>Transactions</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->username }}</td>
                        <td>{{ $user->email ?? '-' }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>Rp {{ number_format($user->balance, 0, ',', '.') }}</td>
                        <td>
                            @if($user->is_admin)
                                <span class="badge badge-paid">Admin</span>
                            @else
                                <span class="badge badge-pending">User</span>
                            @endif
                        </td> 

                        <td>
                            @if($user->is_locked)
                                <span class="badge badge-failed">Locked</span>
                            @else
                                <span class="badge badge-paid">Active</span>
                            @endif
                        </td>

                        <td>{{ $user->transactions_count }}</td>
                        <td>{{ $user->created_at->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            
                            
                            @if($user->is_locked)
                                <form action="{{ route('admin.users.unlock', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">Unlock</button>
                                </form>
                            @endif
                            
                            
                            @if(!$user->is_admin)
                                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')">Delete</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div style="padding: 20px;">
        {{ $users->links() }}
    </div>
</div>
@endsection