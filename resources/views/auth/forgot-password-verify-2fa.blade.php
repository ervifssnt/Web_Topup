@extends('layouts.auth')

@section('title', 'Verify 2FA - UP STORE')

@section('content')
<div class="min-h-screen flex items-center justify-center p-6">
    <div class="w-full max-w-lg">
        <!-- Logo -->
        <div class="text-center mb-12">
            <div class="flex items-center justify-center gap-2 mb-2">
                <span class="text-4xl font-black italic text-primary">UP</span>
                <span class="text-2xl font-bold tracking-wider">STORE</span>
            </div>
        </div>

        <!-- Form Card -->
        <x-card class="max-w-md mx-auto">
            <div class="text-center mb-8">
                <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold mb-2">Verify 2FA Code</h1>
                <p class="text-sm text-text-secondary">
                    Enter your authenticator app code to complete password reset
                </p>
            </div>

            <!-- Error Messages -->
            @if($errors->any())
                <x-alert type="error" class="mb-6">
                    <ul class="space-y-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-alert>
            @endif

            <!-- 2FA Verification Form -->
            <form action="{{ route('password.reset.verify2fa.post') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Security Notice -->
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <div class="flex gap-3">
                        <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="text-sm text-blue-800">
                            <strong>Security Verification</strong>
                            <p class="mt-1 text-blue-700">For your security, we require two-factor authentication to reset your password. This session will expire in 10 minutes.</p>
                        </div>
                    </div>
                </div>

                <!-- 2FA Code Input -->
                <div>
                    <label for="2fa_code" class="block text-sm font-medium mb-2">
                        Authenticator Code <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="2fa_code"
                        name="2fa_code"
                        class="input w-full text-center text-2xl tracking-widest font-mono"
                        placeholder="000000"
                        maxlength="6"
                        pattern="[0-9]{6}"
                        inputmode="numeric"
                        autocomplete="one-time-code"
                        required
                        autofocus
                    />
                    <p class="mt-2 text-sm text-text-secondary">
                        Enter the 6-digit code from your authenticator app
                    </p>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full text-white font-bold py-4 px-6 rounded-xl shadow-lg hover:shadow-2xl transform hover:-translate-y-1 hover:scale-[1.02] transition-all duration-200 flex items-center justify-center gap-3 group" style="background-color: #FF8C00 !important;">
                    <svg class="w-6 h-6 group-hover:scale-110 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-lg">Verify and Reset Password</span>
                </button>

                <!-- Back Link -->
                <div class="text-center text-sm">
                    <a href="{{ route('password.request') }}" class="text-primary hover:text-primary-hover font-medium">
                        ← Start Over
                    </a>
                </div>
            </form>

            <!-- Help Text -->
            <div class="mt-6 pt-6 border-t border-border">
                <p class="text-sm text-text-secondary text-center">
                    Don't have your authenticator app?<br>
                    <span class="text-text-secondary">Please contact support for assistance.</span>
                </p>
            </div>
        </x-card>
    </div>
</div>

<!-- Auto-format code input -->
<script>
document.getElementById('2fa_code').addEventListener('input', function(e) {
    // Only allow numbers
    this.value = this.value.replace(/[^0-9]/g, '');
});
</script>
@endsection
