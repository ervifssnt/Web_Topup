@extends('layouts.auth')

@section('title', '2FA Verification - UP STORE')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-10">
            <div class="flex items-center justify-center gap-2 mb-2">
                <span class="text-4xl font-black italic text-primary">UP</span>
                <span class="text-2xl font-bold tracking-wider">STORE</span>
            </div>
        </div>

        <!-- Verification Card -->
        <x-card>
            <!-- Lock Icon -->
            <div class="text-center text-6xl mb-6">
                <span class="inline-block">🔒</span>
            </div>

            <h1 class="text-3xl font-bold text-center mb-3">Two-Factor Authentication</h1>
            <p class="text-center text-text-secondary mb-8">
                Enter the 6-digit code from your authenticator app
            </p>

            <!-- Error Messages -->
            @if($errors->any())
                <x-alert type="error" class="mb-6">
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </x-alert>
            @endif

            <!-- Verification Form -->
            <form method="POST" action="{{ route('2fa.login') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="codeInput" id="codeLabel" class="block text-sm font-medium text-text-secondary mb-2">
                        Authentication Code
                    </label>
                    <input
                        type="text"
                        name="code"
                        id="codeInput"
                        maxlength="8"
                        inputmode="numeric"
                        placeholder="000000"
                        required
                        autofocus
                        class="input w-full text-center text-2xl font-bold tracking-[0.75rem] bg-dark-base"
                    >
                    <p id="helpText" class="mt-2 text-xs text-center text-text-tertiary">
                        Open your authenticator app and enter the current code
                    </p>
                </div>

                <x-button type="submit" variant="primary" class="w-full">
                    Verify
                </x-button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-dark-border"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-dark-surface text-text-tertiary">OR</span>
                </div>
            </div>

            <!-- Recovery Code Toggle -->
            <div class="text-center">
                <button
                    type="button"
                    onclick="toggleRecoveryMode()"
                    class="text-sm text-primary hover:text-primary-400 underline transition-colors"
                >
                    Use a recovery code instead
                </button>
            </div>

            <!-- Help Text -->
            <div class="mt-8 pt-6 border-t border-dark-border text-center">
                <p class="text-sm text-text-secondary">
                    <strong class="text-white">Lost your phone?</strong><br>
                    Use one of your 8-character recovery codes to log in
                </p>
            </div>
        </x-card>
    </div>
</div>

@section('scripts')
<script>
let isRecoveryMode = false;

function toggleRecoveryMode() {
    isRecoveryMode = !isRecoveryMode;
    const input = document.getElementById('codeInput');
    const label = document.getElementById('codeLabel');
    const helpText = document.getElementById('helpText');
    const toggleButton = event.target;

    if (isRecoveryMode) {
        input.placeholder = 'XXXXXXXX';
        input.maxLength = 8;
        input.style.letterSpacing = '0.375rem';
        label.textContent = 'Recovery Code';
        helpText.textContent = 'Enter one of your 8-character recovery codes';
        toggleButton.textContent = 'Use authenticator app instead';
    } else {
        input.placeholder = '000000';
        input.maxLength = 6;
        input.style.letterSpacing = '0.75rem';
        label.textContent = 'Authentication Code';
        helpText.textContent = 'Open your authenticator app and enter the current code';
        toggleButton.textContent = 'Use a recovery code instead';
    }

    input.value = '';
    input.focus();
}

// Auto-format input based on mode
document.getElementById('codeInput').addEventListener('input', function(e) {
    if (!isRecoveryMode) {
        // Numeric only for TOTP codes
        this.value = this.value.replace(/[^0-9]/g, '');
    } else {
        // Alphanumeric for recovery codes, uppercase
        this.value = this.value.replace(/[^0-9A-Za-z]/g, '').toUpperCase();
    }
});
</script>
@endsection
@endsection
