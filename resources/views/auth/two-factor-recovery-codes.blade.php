@extends('layouts.main')

@section('title', '2FA Setup Complete - UP STORE')

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
        text-align: center;
    }

    .recovery-card {
        background: #2a2a2a;
        border-radius: 12px;
        padding: 40px;
        border: 1px solid #3a3a3a;
    }

    .success-banner {
        background: #1e4620;
        border: 2px solid #2e7d32;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 30px;
        text-align: center;
    }

    .success-icon {
        font-size: 48px;
        margin-bottom: 12px;
    }

    .success-title {
        font-size: 20px;
        font-weight: 700;
        color: #66bb6a;
        margin-bottom: 8px;
    }

    .success-text {
        color: #a5d6a7;
        font-size: 14px;
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
        margin-bottom: 24px;
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

    .continue-section {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #3a3a3a;
    }

    .continue-text {
        color: #999;
        margin-bottom: 16px;
        font-size: 14px;
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

        .page-title, .actions, .warning-box, .continue-section {
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
    <h1 class="page-title">2FA Setup Complete!</h1>

    <div class="recovery-card">
        <div class="success-banner">
            <div class="success-icon">✅</div>
            <div class="success-title">Two-Factor Authentication Enabled</div>
            <div class="success-text">Your account is now protected with an extra layer of security</div>
        </div>

        <div class="warning-box">
            <div class="warning-title">
                <span>🔑</span>
                <span>Save Your Recovery Codes</span>
            </div>
            <div class="warning-text">
                • Each recovery code can only be used <strong>once</strong><br>
                • Store these codes in a safe place (password manager, safe, etc.)<br>
                • You can use these codes if you lose access to your authenticator app<br>
                • <strong>This is the only time these codes will be displayed</strong>
            </div>
        </div>

        <div class="codes-grid">
            @foreach($recoveryCodes as $index => $code)
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
        </div>

        <div class="continue-section">
            <p class="continue-text">Make sure you've saved these codes before continuing</p>
            <a href="{{ route('home') }}" class="btn btn-primary">Continue to Dashboard</a>
        </div>
    </div>
</div>

<script>
function printCodes() {
    window.print();
}

function copyCodes() {
    const codes = @json($recoveryCodes);
    const text = codes.join('\n');
    navigator.clipboard.writeText(text).then(() => {
        alert('Recovery codes copied to clipboard!');
    });
}

function downloadCodes() {
    const codes = @json($recoveryCodes);
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
</script>
@endsection
