@extends('admin.layout')

@section('title', 'Top-Up Requests')

@section('content')
<div class="page-header">
    <h1>Wallet Top-Up Requests</h1>
    <p>Approve or reject user balance top-up requests</p>
</div>

<div class="content-card">
    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Amount</th>
                    <th>Payment Method</th>
                    <th>Proof</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requests as $request)
                    <tr>
                        <td>#{{ $request->id }}</td>
                        <td>{{ $request->user->username }}</td>
                        <td>Rp {{ number_format($request->amount, 0, ',', '.') }}</td>
                        <td>{{ $request->payment_method }}</td>
                        <td>
                            @if($request->proof_image)
                                <a href="{{ route('admin.topup-proof', $request->id) }}" target="_blank" class="btn btn-primary btn-sm">View</a>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-{{ $request->status === 'approved' ? 'paid' : ($request->status === 'rejected' ? 'failed' : 'pending') }}">
                                {{ ucfirst($request->status) }}
                            </span>
                        </td>
                        <td>{{ $request->created_at->format('d M Y, H:i') }}</td>
                        <td>
                            @if($request->status === 'pending')
                                <button onclick="showApproveModal({{ $request->id }})" class="btn btn-primary btn-sm">Approve</button>
                                <button onclick="showRejectModal({{ $request->id }})" class="btn btn-danger btn-sm">Reject</button>
                            @else
                                <small>{{ $request->processedBy->username ?? 'System' }}</small>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px;">
                            No top-up requests yet
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div style="padding: 20px;">
        {{ $requests->links() }}
    </div>
</div>

<!-- Approve Modal -->
<div id="approveModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 20px;">Approve Top-Up Request</h3>
        <form id="approveForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Admin Notes (Optional)</label>
                <textarea name="admin_notes" class="form-control" rows="3" placeholder="Any notes for the user..."></textarea>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button type="submit" class="btn btn-primary">Confirm Approval</button>
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- Reject Modal -->
<div id="rejectModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; padding: 30px; border-radius: 12px; max-width: 500px; width: 90%;">
        <h3 style="margin-bottom: 20px;">Reject Top-Up Request</h3>
        <form id="rejectForm" method="POST">
            @csrf
            <div class="form-group">
                <label>Reason for Rejection *</label>
                <textarea name="admin_notes" class="form-control" rows="3" placeholder="Explain why this request is rejected..." required></textarea>
            </div>
            <div style="display: flex; gap: 12px; margin-top: 20px;">
                <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showApproveModal(id) {
    document.getElementById('approveForm').action = '/admin/topup-requests/' + id + '/approve';
    document.getElementById('approveModal').style.display = 'flex';
}

function showRejectModal(id) {
    document.getElementById('rejectForm').action = '/admin/topup-requests/' + id + '/reject';
    document.getElementById('rejectModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('approveModal').style.display = 'none';
    document.getElementById('rejectModal').style.display = 'none';
}
</script>
@endsection