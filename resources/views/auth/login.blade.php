@extends('layouts.auth')

@section('title', 'Login - UP STORE')

@section('content')
<div class="flex min-h-screen">
    <!-- Left Side - Branding -->
    <div class="hidden lg:flex lg:flex-1 items-center justify-center bg-dark-base p-8">
        <div class="text-center">
            <h2 class="text-text-tertiary text-lg font-normal mb-6">Welcome to</h2>
            <div class="flex items-center justify-center gap-3">
                <span class="text-5xl font-black italic text-primary -tracking-wider">UP</span>
                <span class="text-3xl font-bold tracking-widest">STORE</span>
            </div>
        </div>
    </div>

    <!-- Right Side - Login Form -->
    <div class="flex-1 flex items-center justify-center bg-dark-surface p-6 sm:p-8 lg:p-12">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-8">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <span class="text-3xl font-black italic text-primary">UP</span>
                    <span class="text-xl font-bold tracking-wider">STORE</span>
                </div>
            </div>

            <h1 class="text-3xl font-semibold text-center mb-10">Log In Member</h1>

            <!-- Error Messages -->
            @if($errors->any())
                <x-alert type="error" class="mb-6">
                    @foreach($errors->all() as $error)
                        {{ $error }}
                    @endforeach
                </x-alert>
            @endif

            <!-- Login Form -->
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <x-input
                    label="Username or Email"
                    name="email"
                    type="text"
                    placeholder="Enter your username or email"
                    :value="old('email')"
                    required
                    autocomplete="username"
                />

                <x-input
                    label="Password"
                    name="password"
                    type="password"
                    placeholder="Enter your password"
                    required
                    autocomplete="current-password"
                />

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-dark-border bg-dark-elevated text-primary focus:ring-2 focus:ring-primary/50 cursor-pointer">
                        <span class="text-sm text-text-secondary">Remember me</span>
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm text-primary hover:text-primary-400 font-medium transition-colors">
                        Forgot Password?
                    </a>
                </div>

                <!-- Login Button -->
                <x-button type="submit" variant="primary" class="w-full">
                    Login
                </x-button>
            </form>

            <!-- Divider -->
            <div class="text-center my-6 text-sm text-text-secondary">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-white underline font-medium hover:text-primary transition-colors">Register</a>
            </div>

            <!-- Google Sign-in (Placeholder) -->
            <button
                type="button"
                onclick="alert('Google Sign-in coming soon!')"
                class="w-full flex items-center justify-center gap-2 px-6 py-3 bg-dark-elevated border border-dark-border rounded-lg text-white hover:bg-dark-hover hover:border-gray-500 transition-all"
            >
                <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                Log in with Google
            </button>
        </div>
    </div>
</div>
@endsection
