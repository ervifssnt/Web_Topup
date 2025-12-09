@extends('admin.layout')

@section('title', 'Password Reset Activity')

@section('content')
<div class="page-header">
    <h1>Password Reset Activity</h1>
    <p>Monitor all password reset actions and security events</p>
</div>

<div class="content-card">
    <div class="card-header">
        <h2>Password Reset Events</h2>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>User/Email</th>
                    <th>IP Address</th>
                    <th>Action</th>
                    <th>Description</th>
                    <th>User Agent</th>
                </tr>
            </thead>
            <tbody>
                @forelse($resetLogs as $log)
                    <tr>
                        <td style="white-space: nowrap;">
                            <div>{{ $log->created_at->format('d M Y') }}</div>
                            <div style="font-size: 12px; color: #999;">{{ $log->created_at->format('H:i:s') }}</div>
                        </td>
                        <td>
                            @if($log->user)
                                <div style="font-weight: 600;">{{ $log->user->username }}</div>
                                <div style="font-size: 12px; color: #666;">{{ $log->user->email }}</div>
                            @else
                                @php
                                    // Extract email from description if available
                                    preg_match('/[\w\-\.]+@[\w\-\.]+\.\w+/', $log->description, $matches);
                                    $email = $matches[0] ?? null;
                                @endphp
                                @if($email)
                                    <div style="color: #666;">{{ $email }}</div>
                                @else
                                    <span style="color: #999;">System</span>
                                @endif
                            @endif
                        </td>
                        <td style="font-family: monospace; font-size: 13px; color: #555;">
                            {{ $log->ip_address ?? '-' }}
                        </td>
                        <td>
                            @php
                                $actionClass = 'pending';
                                if (str_contains($log->action, 'completed')) {
                                    $actionClass = 'paid';
                                } elseif (str_contains($log->action, 'rejected') || str_contains($log->action, 'failed')) {
                                    $actionClass = 'failed';
                                } elseif (str_contains($log->action, 'requested') || str_contains($log->action, 'link')) {
                                    $actionClass = 'pending';
                                }
                            @endphp
                            <span class="badge badge-{{ $actionClass }}">
                                {{ str_replace('_', ' ', ucwords(str_replace('_', ' ', $log->action))) }}
                            </span>
                        </td>
                        <td>{{ $log->description }}</td>
                        <td style="font-size: 12px; color: #666; max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->user_agent }}">
                            {{ $log->user_agent ?? '-' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: #999;">
                            <div style="font-size: 48px; margin-bottom: 16px;">🔑</div>
                            <div style="font-size: 16px; font-weight: 600; margin-bottom: 8px;">No password reset activity found</div>
                            <div style="font-size: 14px;">Password reset events will appear here when users reset their passwords</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($resetLogs->hasPages())
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
        {{ $resetLogs->links() }}
    </div>
    @endif
</div>

<style>
/* Additional responsive styling */
@media (max-width: 768px) {
    .table-responsive {
        overflow-x: auto;
    }

    .data-table td {
        white-space: normal;
    }

    .data-table td[style*="max-width"] {
        max-width: 150px !important;
    }
}
</style>
@endsection
