@extends('layouts.main')

@section('title', '2FA Settings - UP STORE')

@section('styles')
<style>
    .settings-container {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 30px;
        color: white;
    }
    
    .settings-card {
        background: #2a2a2a;
        border-radius: 12px;
        padding: 30px;
        border: 1px solid #3a3a3a;
        margin-bottom: 20px;
    }
    
    .section-title {
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 16px;
        color: white;
    }
    
    .section-description {
        color: #999;
        margin-bottom: 24px;
        line-height: 1.6;
    }
    
    .status-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 20px;
    }
    
    .status-enabled {
        background: #d4edda;
        color: #155724;
    }
    
    .status-disabled {
        background: #f8d7da;
        color: #721c24;
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
        transform: translateY(-2px);
    }
    
    .btn-danger {
        background: #dc3545;
        color: white;
    }
    
    .btn-danger:hover {
        background: #c82333;
    }
    
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .recovery-codes-box {
        background: #1a1a1a;
        padding: 20px;
        border-radius: 8px;
        margin-top: 20px;
    }
    
    .recovery-code {
        font-family: monospace;
        font-size: 16px;
        color: #FF8C00;
        padding: 8px 0;
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
    
    .alert-warning {
        background: #4a3500;
        border: 1px solid #856404;
        color: #ffc107;
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
</style>
@endsection

@section('content')
<div class="settings-container">
    <h1 class="page-title">🔐 Two-Factor Authentication</h1>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    
    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif
    
    @if(session('recovery_codes'))
        <div class="settings-card">
            <h2 class="section-title">⚠️ Save Your Recovery Codes</h2>
            <p class="section-description">
                Store these codes in a safe place. Each code can only be used once if you lose access to your authenticator app.
            </p>
            
            <div class="recovery-codes-box">
                @foreach(session('recovery_codes') as $code)
                    <div class="recovery-code">{{ $code }}</div>
                @endforeach
            </div>
            
            <div style="margin-top: 20px;">
                <button onclick="printCodes()" class="btn btn-secondary">Print Codes</button>
                <button onclick="copyCodes()" class="btn btn-secondary">Copy to Clipboard</button>
            </div>
        </div>
    @endif
    
    <div class="settings-card">
        <h2 class="section-title">Security Status</h2>
        
        @if($user->has2FAEnabled())
            <span class="status-badge status-enabled">✓ 2FA Enabled</span>
            
            <p class="section-description">
                Your account is protected with Two-Factor Authentication. You'll need to enter a code from your authenticator app when logging in.
            </p>
            
            <div style="display: flex; gap: 12px; flex-wrap: wrap;">
                <a href="{{ route('2fa.recovery') }}" class="btn btn-secondary">View Recovery Codes</a>
                <button onclick="showDisableModal()" class="btn btn-danger">Disable 2FA</button>
            </div>
        @else
            <span class="status-badge status-disabled">✗ 2FA Disabled</span>
            
            <p class="section-description">
                Two-Factor Authentication adds an extra layer of security to your account. Even if someone knows your password, they won't be able to log in without your phone.
            </p>
            
            <a href="{{ route('2fa.enable') }}" class="btn btn-primary">Enable 2FA</a>
        @endif
    </div>
    
    <div class="settings-card">
        <h2 class="section-title">How It Works</h2>
        <p class="section-description">
            1. <strong>Download an authenticator app</strong> on your phone (Google Authenticator, Microsoft Authenticator, or Authy)<br>
            2. <strong>Scan the QR code</strong> we provide with your app<br>
            3. <strong>Enter the 6-digit code</strong> from the app to verify<br>
            4. <strong>Save recovery codes</strong> in case you lose your phone<br>
            5. <strong>Log in securely</strong> with your password + 6-digit code
        </p>
    </div>
</div>

<!-- Disable 2FA Modal -->
<div id="disableModal" class="modal">
    <div class="modal-content">
        <h3 class="modal-title">Disable Two-Factor Authentication</h3>
        
        <form method="POST" action="{{ route('2fa.disable') }}">
            @csrf
            
            <div class="form-group">
                <label>Enter your password to confirm:</label>
                <input type="password" name="password" class="form-control" required autofocus>
                @error('password')
                    <span style="color: #ef5350; font-size: 13px;">{{ $message }}</span>
                @enderror
            </div>
            
            <div style="display: flex; gap: 12px;">
                <button type="submit" class="btn btn-danger">Yes, Disable 2FA</button>
                <button type="button" onclick="closeModal()" class="btn btn-secondary">Cancel</button>
            </div>
        </form>
    </div>
</div>

<script>
function showDisableModal() {
    document.getElementById('disableModal').style.display = 'flex';
}

function closeModal() {
    document.getElementById('disableModal').style.display = 'none';
}

function printCodes() {
    window.print();
}

function copyCodes() {
    const codes = @json(session('recovery_codes', []));
    const text = codes.join('\n');
    navigator.clipboard.writeText(text).then(() => {
        alert('Recovery codes copied to clipboard!');
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('disableModal');
    if (event.target == modal) {
        closeModal();
    }
}
</script>
@endsection