@extends('admin.layout')

@section('title', 'Audit Logs')

@section('content')
<div class="page-header">
    <h1>Audit Logs</h1>
    <p>Security monitoring and activity tracking</p>
</div>

<div class="content-card">
    <div class="card-header">
        <h2>Recent Activities</h2>
    </div>
    
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>IP Address</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td style="white-space: nowrap;">
                            {{ $log->created_at->format('d M Y H:i:s') }}
                        </td>
                        <td>
                            @if($log->user)
                                {{ $log->user->username }}
                            @else
                                <span style="color: #999;">System</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $log->action === 'login' ? 'paid' : ($log->action === 'delete_game' || $log->action === 'delete_user' ? 'failed' : 'pending') }}">
                                {{ str_replace('_', ' ', ucfirst($log->action)) }}
                            </span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td style="font-family: monospace; font-size: 13px;">
                            {{ $log->ip_address }}
                        </td>
                        <td>
                            @if($log->old_values || $log->new_values)
                                <button onclick="toggleDetails('log-{{ $log->id }}')" class="btn btn-primary btn-sm">
                                    View
                                </button>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    @if($log->old_values || $log->new_values)
                        <tr id="log-{{ $log->id }}" style="display: none;">
                            <td colspan="6" style="background: #f8f8f8; padding: 20px;">
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                    @if($log->old_values)
                                        <div>
                                            <strong>Before:</strong>
                                            <pre style="background: white; padding: 12px; border-radius: 6px; margin-top: 8px; font-size: 12px; overflow-x: auto;">{{ json_encode($log->old_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    @endif
                                    @if($log->new_values)
                                        <div>
                                            <strong>After:</strong>
                                            <pre style="background: white; padding: 12px; border-radius: 6px; margin-top: 8px; font-size: 12px; overflow-x: auto;">{{ json_encode($log->new_values, JSON_PRETTY_PRINT) }}</pre>
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            No audit logs yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
<div style="padding: 20px;">
    <style>
    /* Pagination styling */
    nav[role="navigation"] {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    
    nav[role="navigation"] > div {
        display: flex;
        gap: 8px;
        align-items: center;
    }
    
    nav[role="navigation"] a,
    nav[role="navigation"] span {
        min-width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0 12px;
        border: 1px solid #ddd;
        border-radius: 6px;
        text-decoration: none;
        color: #333;
        font-size: 14px;
        background: white;
    }
    
    nav[role="navigation"] a:hover {
        background: #FF8C00;
        color: white;
        border-color: #FF8C00;
    }
    
    nav[role="navigation"] span[aria-current="page"] {
        background: #FF8C00;
        color: white;
        border-color: #FF8C00;
    }
    
    nav[role="navigation"] span[aria-disabled="true"] {
        opacity: 0.5;
        cursor: not-allowed;
    }
    
    /* Hide SVG arrows */
    nav[role="navigation"] svg {
        display: none !important;
    }
    
    /* Text arrows */
    nav[role="navigation"] a[rel="prev"]::before {
        content: "← ";
    }
    
    nav[role="navigation"] a[rel="next"]::after {
        content: " →";
    }
    
    /* Spacing for page info text */
    nav[role="navigation"] p {
        margin: 0 16px;
        font-size: 14px;
        color: #666;
    }
    </style>
    {{ $logs->links() }}
</div>

<script>
function toggleDetails(id) {
    const row = document.getElementById(id);
    if (row.style.display === 'none') {
        row.style.display = 'table-row';
    } else {
        row.style.display = 'none';
    }
}
</script>
@endsection