@extends('layouts.main')

@section('title', 'Recovery Codes - UP STORE')

@section('styles')
<style>
    .recovery-container {
        max-width: 700px;
        margin: 0 auto;
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 30px;
        color: white;
    }
    
    .recovery-card {
        background: #2a2a2a;
        border-radius: 12px;
        padding: 40px;
        border: 1px solid #3a3a3a;
    }
    
    .warning-box {
        background: #4a3500;
        border: 2px solid #856404;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
    }
    
    .warning-title {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 18px;
        font-weight: 700;
        color: #ffc107;
        margin-bottom: 12px;
    }
    
    .warning-text {
        color: #ffc107;
        line-height: 1.6;
        font-size: 14px;
    }
    
    .codes-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
        margin-bottom: 30px;
    }
    
    .code-item {
        background: #1a1a1a;
        padding: 16px 20px;
        border-radius: 8px;
        border: 1px solid #3a3a3a;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .code-value {
        font-family: monospace;
        font-size: 18px;
        color: #FF8C00;
        font-weight: 700;
        letter-spacing: 2px;
    }
    
    .code-number {
        font-size: 12px;
        color: #666;
        font-weight: 600;
    }
    
    .actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .btn {
        padding: 12px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s;
        text-decoration: none;
        display: inline-block;
        border: none;
    }
    
    .btn-primary {
        background: #FF8C00;
        color: white;
    }
    
    .btn-primary:hover {
        background: #ff9d1f;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .btn-danger {
        background: #dc3545;
        color: white;
    }
    
    .btn-danger:hover {
        background: #c82333;
    }
    
    .alert {
        padding: 16px 20px;
        border-radius: 8px;
        margin-bottom: 24px;
        font-size: 14px;
    }
    
    .alert-success {
        background: #1e4620;
        border: 1px solid #2e7d32;
        color: #66bb6a;
    }
    
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0,0,0,0.8);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    }
    
    .modal-content {
        background: #2a2a2a;
        padding: 30px;
        border-radius: 12px;
        max-width: 500px;
        width: 90%;
    }
    
    .modal-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 20px;
        color: white;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        font-size: 14px;
        margin-bottom: 8px;
        color: #ccc;
    }
    
    .form-control {
        width: 100%;
        padding: 12px 16px;
        background: #1a1a1a;
        border: 1px solid #3a3a3a;
        border-radius: 8px;
        color: white;
        font-size: 15px;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #FF8C00;
    }
    
    @media (max-width: 768px) {
        .codes-grid {
            grid-template-columns: 1fr;
        }
    }
    
    @media print {
        body {
            background: white;
            color: black;
        }
        
        .page-title, .actions, .warning-box {
            display: none;
        }
        
        .recovery-card {
            background: white;
            border: 2px solid black;
        }
        
        .code-item {
            border: 1px solid black;
            background: white;
        }
        
        .code-value {
            color: black;
        }
    }
</style>
@endsection

@section('content')
<div class="recovery-container">
    <h1 class="page-title">🔑 Recovery Codes</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('recovery_codes'))
        <div class="warning-box">
            <div class="warning-title">
                <span>⚠️</span>
                <span>New Recovery Codes Generated</span>
            </div>
            <div class="warning-text">
                Your old recovery codes are no longer valid. Save these new codes in a secure location.
            </div>
        </div>
    @endif
    
    <div class="recovery-card">
        <div class="warning-box">
            <div class="warning-title">
                <span>💡</span>
                <span>Important Information</span>
            </div>
            <div class="warning-text">
                • Each recovery code can only be used <strong>once</strong><br>
                • Store these codes in a safe place (password manager, safe, etc.)<br>
                • You can use these codes if you lose access to your authenticator app<br>
                • Generate new codes if you've used most of them
            </div>
        </div>
        
        <div class="codes-grid">
            @foreach(session('recovery_codes', $recoveryCodes) as $index => $code)
                <div class="code-item">
                    <div class="code-value">{{ $code }}</div>
                    <div class="code-number">#{{ $index + 1 }}</div>
                </div>
            @endforeach
        </div>
        
        <div class="actions">
            <button onclick="printCodes()" class="btn btn-secondary">🖨️ Print Codes</button>
            <button onclick="copyCodes()" class="btn btn-secondary">📋 Copy All</button>
            <button onclick="downloadCodes()" class="btn btn-secondary">💾 Download</button>
            <button onclick="showRegenerateModal()" class="btn btn-danger">🔄 Regenerate Codes</button>
        </div>
    </div>
</div>

<!-- Regenerate Modal -->
<div id="regenerateModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">Regenerate Recovery Codes</h3>
        <p style="color: #999; margin-bottom: 20px;">
            This will invalidate all your current recovery codes and generate new ones. 
            Make sure you have access to your authenticator app.
        </p>
        
        <form method="POST" action="{{ route('2fa.recovery.regenerate') }}">
            @csrf
            
            <div class="form-group">
                <label>Enter your password to confirm:</label>
                <input type="password" name="password" class="form-control" required autofocus>
                @error('password')
                    <span style="color: #ef5350; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-danger">Regenerate Codes</button>
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showRegenerateModal() {
    document.getElementById('regenerateModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('regenerateModal').style.display = 'none';
}

function printCodes() {
    window.print();
}

function copyCodes() {
    const codes = @json(session('recovery_codes', $recoveryCodes));
    const text = codes.join('\n');
    navigator.clipboard.writeText(text).then(() => {
        alert('Recovery codes copied to clipboard!');
    });
}

function downloadCodes() {
    const codes = @json(session('recovery_codes', $recoveryCodes));
    const text = 'UP STORE - Two-Factor Authentication Recovery Codes\n' +
                 'Generated: ' + new Date().toLocaleString() + '\n' +
                 'Username: {{ auth()->user()->username }}\n\n' +
                 'IMPORTANT: Each code can only be used once.\n' +
                 'Store these codes in a safe place.\n\n' +
                 codes.map((code, i) => `${i + 1}. ${code}`).join('\n') +
                 '\n\n--------------------\n' +
                 'Keep these codes secure and confidential.';
    
    const blob = new Blob([text], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'upstore-recovery-codes.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('regenerateModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
@endsection