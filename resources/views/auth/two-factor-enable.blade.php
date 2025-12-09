@extends('layouts.main')

@section('title', 'Enable 2FA - UP STORE')

@section('styles')
<style>
    .enable-container {
        max-width: 600px;
        margin: 0 auto;
    }
    
    .page-title {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 30px;
        color: white;
        text-align: center;
    }
    
    .setup-card {
        background: #2a2a2a;
        border-radius: 12px;
        padding: 40px;
        border: 1px solid #3a3a3a;
        text-align: center;
    }
    
    .step-indicator {
        display: flex;
        justify-content: center;
        gap: 8px;
        margin-bottom: 30px;
    }
    
    .step {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #3a3a3a;
    }
    
    .step.active {
        background: #FF8C00;
    }
    
    .qr-code-container {
        background: white;
        padding: 20px;
        border-radius: 12px;
        display: inline-block;
        margin-bottom: 24px;
    }
    
    .qr-code-container svg {
        display: block;
    }
    
    .secret-key {
        background: #1a1a1a;
        padding: 16px;
        border-radius: 8px;
        margin-bottom: 24px;
    }
    
    .secret-key-label {
        font-size: 13px;
        color: #999;
        margin-bottom: 8px;
    }
    
    .secret-key-value {
        font-family: monospace;
        font-size: 18px;
        color: #FF8C00;
        font-weight: 600;
        letter-spacing: 2px;
    }
    
    .instructions {
        text-align: left;
        margin-bottom: 30px;
    }
    
    .instruction-step {
        display: flex;
        gap: 16px;
        margin-bottom: 20px;
        align-items: start;
    }
    
    .step-number {
        width: 32px;
        height: 32px;
        background: #FF8C00;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        flex-shrink: 0;
    }
    
    .step-text {
        flex: 1;
        padding-top: 4px;
    }
    
    .step-title {
        font-weight: 600;
        color: white;
        margin-bottom: 4px;
    }
    
    .step-description {
        font-size: 14px;
        color: #999;
        line-height: 1.5;
    }
    
    .verification-form {
        margin-top: 30px;
    }
    
    .form-group {
        margin-bottom: 20px;
        text-align: left;
    }
    
    .form-group label {
        display: block;
        font-size: 14px;
        margin-bottom: 8px;
        color: #ccc;
        font-weight: 600;
    }
    
    .form-control {
        width: 100%;
        padding: 14px 16px;
        background: #1a1a1a;
        border: 1px solid #3a3a3a;
        border-radius: 8px;
        color: white;
        font-size: 20px;
        text-align: center;
        letter-spacing: 8px;
        font-weight: 600;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #FF8C00;
    }
    
    .btn {
        padding: 14px 28px;
        border-radius: 8px;
        font-size: 16px;
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
    
    .btn-secondary {
        background: #6c757d;
        color: white;
        margin-left: 12px;
    }
    
    .btn-secondary:hover {
        background: #5a6268;
    }
    
    .alert-error {
        background: #4a1a1a;
        border: 1px solid #d32f2f;
        color: #ef5350;
        padding: 12px 16px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 14px;
    }
</style>
@endsection

@section('content')
<div class="enable-container">
    <h1 class="page-title">🔐 Enable Two-Factor Authentication</h1>
    
    <div class="setup-card">
        <div class="step-indicator">
            <div class="step active"></div>
            <div class="step active"></div>
            <div class="step"></div>
        </div>
        
        <div class="instructions">
            <div class="instruction-step">
                <div class="step-number">1</div>
                <div class="step-text">
                    <div class="step-title">Download an Authenticator App</div>
                    <div class="step-description">
                        Install Google Authenticator, Microsoft Authenticator, or Authy on your phone
                    </div>
                </div>
            </div>
            
            <div class="instruction-step">
                <div class="step-number">2</div>
                <div class="step-text">
                    <div class="step-title">Scan the QR Code</div>
                    <div class="step-description">
                        Open your authenticator app and scan this QR code
                    </div>
                </div>
            </div>
        </div>
        
        <div class="qr-code-container">
            {!! $qrCodeSvg !!}
        </div>
        
        <div class="secret-key">
            <div class="secret-key-label">Or enter this code manually:</div>
            <div class="secret-key-value">{{ $secret }}</div>
        </div>
        
        @if($errors->any())
            <div class="alert-error">
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif
        
        <div class="verification-form">
            <form method="POST" action="{{ route('2fa.verify.post') }}">
                @csrf
                
                <div class="form-group">
                    <label>Step 3: Enter the 6-digit code from your app</label>
                    <input type="text" 
                           name="code" 
                           class="form-control" 
                           placeholder="000000" 
                           maxlength="6" 
                           pattern="[0-9]{6}"
                           inputmode="numeric"
                           required 
                           autofocus>
                </div>
                
                <button type="submit" class="btn btn-primary">Verify & Enable 2FA</button>
                <a href="{{ route('2fa.show') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
// Auto-format code input
document.querySelector('input[name="code"]').addEventListener('input', function(e) {
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endsection