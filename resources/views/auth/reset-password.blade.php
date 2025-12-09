@extends('layouts.auth')

@section('title', 'Reset Password - UP STORE')

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
                <p class="text-sm text-text-secondary">Enter your new password below</p>
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

            <!-- Form -->
            <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <x-input
                    label="Email Address"
                    name="email"
                    type="email"
                    placeholder="Enter your email address"
                    :value="old('email')"
                    required
                    autocomplete="email"
                />

                <x-input
                    label="New Password"
                    name="password"
                    type="password"
                    placeholder="Enter new password"
                    required
                    autocomplete="new-password"
                    hint="Min 8 chars, with uppercase, lowercase, number & special char"
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
        </x-card>
    </div>
</div>
@endsection
