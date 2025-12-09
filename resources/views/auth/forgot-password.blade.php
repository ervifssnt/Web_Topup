@extends('layouts.auth')

@section('title', 'Forgot Password - UP STORE')

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
                <h1 class="text-3xl font-bold mb-2">Reset Password</h1>
                <p class="text-sm text-text-secondary">Verify your identity to reset your password</p>
            </div>

            <!-- Security Notice about 2FA Requirement -->
            <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg mb-6">
                <div class="flex gap-3">
                    <svg class="w-5 h-5 text-blue-600 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                    <div class="text-sm text-blue-800">
                        <strong>Enhanced Security</strong>
                        <p class="mt-1 text-blue-700">This method requires <strong>Two-Factor Authentication (2FA)</strong> to be enabled on your account. If you don't have 2FA enabled, please contact support for assistance.</p>
                    </div>
                </div>
            </div>

            <!-- Success Message -->
            @if(session('success'))
                <x-alert type="success" class="mb-6">
                    {{ session('success') }}
                </x-alert>
            @endif

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

            <!-- Form -->
            <form method="POST" action="{{ route('password.reset.simple') }}" class="space-y-5">
                @csrf

                <x-input
                    label="Email Address"
                    name="email"
                    type="email"
                    placeholder="Enter your registered email"
                    :value="old('email')"
                    required
                    autocomplete="email"
                />

                <x-input
                    label="Username"
                    name="username"
                    type="text"
                    placeholder="Enter your username"
                    :value="old('username')"
                    required
                    autocomplete="username"
                />

                <x-input
                    label="Phone Number"
                    name="phone"
                    type="text"
                    placeholder="Enter your registered phone number"
                    :value="old('phone')"
                    required
                    autocomplete="tel"
                />

                <x-input
                    label="New Password"
                    name="password"
                    type="password"
                    placeholder="Enter new password"
                    required
                    autocomplete="new-password"
                    hint="Min 8 chars with uppercase, lowercase, number & special char"
                />

                <x-input
                    label="Confirm Password"
                    name="password_confirmation"
                    type="password"
                    placeholder="Confirm new password"
                    required
                    autocomplete="new-password"
                />

                <x-button type="submit" variant="primary" class="w-full mt-6">
                    Reset Password
                </x-button>
            </form>

            <!-- Back to Login Link -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-sm text-primary hover:text-primary-400 font-medium transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Back to Login
                </a>
            </div>
        </x-card>
    </div>
</div>
@endsection
