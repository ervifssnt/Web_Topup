@extends('layouts.auth')

@section('title', 'Register - UP STORE')

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

    <!-- Right Side - Register Form -->
    <div class="flex-1 flex items-center justify-center bg-dark-surface p-6 sm:p-8 lg:p-12">
        <div class="w-full max-w-md">
            <!-- Mobile Logo -->
            <div class="lg:hidden text-center mb-8">
                <div class="flex items-center justify-center gap-2 mb-2">
                    <span class="text-3xl font-black italic text-primary">UP</span>
                    <span class="text-xl font-bold tracking-wider">STORE</span>
                </div>
            </div>

            <h1 class="text-3xl font-semibold text-center mb-10">Create your account</h1>

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

            <!-- Register Form -->
            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                <x-input
                    label="Username"
                    name="username"
                    type="text"
                    placeholder="Enter your username"
                    :value="old('username')"
                    required
                />

                <x-input
                    label="WhatsApp Number"
                    name="phone"
                    type="tel"
                    placeholder="Enter your WhatsApp number (e.g., 08123456789)"
                    :value="old('phone')"
                    required
                    autocomplete="tel"
                />

                <x-input
                    label="Email Address"
                    name="email"
                    type="email"
                    placeholder="Enter your email address"
                    :value="old('email')"
                    required
                />

                <!-- Password Fields in Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <x-input
                            label="Password"
                            name="password"
                            id="password"
                            type="password"
                            placeholder="Enter password"
                            required
                            autocomplete="new-password"
                        />
                        <!-- Password Strength Indicator -->
                        <div class="mt-2 hidden" id="password-strength">
                            <div class="flex gap-1 mb-1">
                                <div class="h-1 flex-1 bg-dark-border rounded transition-all" id="strength-1"></div>
                                <div class="h-1 flex-1 bg-dark-border rounded transition-all" id="strength-2"></div>
                                <div class="h-1 flex-1 bg-dark-border rounded transition-all" id="strength-3"></div>
                                <div class="h-1 flex-1 bg-dark-border rounded transition-all" id="strength-4"></div>
                            </div>
                            <p class="text-xs text-text-tertiary" id="strength-text">Password strength: <span id="strength-label">-</span></p>
                        </div>
                        <p class="text-xs text-text-tertiary mt-1.5">Min 8 chars, with uppercase, lowercase, number & special char</p>
                    </div>

                    <x-input
                        label="Confirm Password"
                        name="password_confirmation"
                        type="password"
                        placeholder="Confirm password"
                        required
                        autocomplete="new-password"
                    />
                </div>

                <!-- Terms Checkbox -->
                <div class="flex items-start gap-3">
                    <input
                        type="checkbox"
                        id="terms"
                        required
                        class="mt-1 w-4 h-4 rounded border-dark-border bg-dark-elevated text-primary focus:ring-2 focus:ring-primary/50 cursor-pointer"
                    >
                    <label for="terms" class="text-sm text-text-secondary cursor-pointer">
                        I agree to <a href="#" class="text-primary hover:text-primary-400 transition-colors">Terms & Privacy Policy</a>
                    </label>
                </div>

                <!-- Register Button -->
                <x-button type="submit" variant="primary" class="w-full">
                    Register
                </x-button>
            </form>

            <!-- Divider -->
            <div class="text-center my-6 text-sm text-text-secondary">
                Already have an account?
                <a href="{{ route('login') }}" class="text-white underline font-medium hover:text-primary transition-colors">Login</a>
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
                Sign up with Google
            </button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const passwordInput = document.getElementById('password');
    const strengthContainer = document.getElementById('password-strength');
    const strengthBars = [
        document.getElementById('strength-1'),
        document.getElementById('strength-2'),
        document.getElementById('strength-3'),
        document.getElementById('strength-4')
    ];
    const strengthLabel = document.getElementById('strength-label');

    passwordInput.addEventListener('input', function() {
        const password = this.value;

        if (password.length === 0) {
            strengthContainer.classList.add('hidden');
            return;
        }

        strengthContainer.classList.remove('hidden');

        let strength = 0;
        const checks = [
            password.length >= 8,
            /[a-z]/.test(password),
            /[A-Z]/.test(password),
            /[0-9]/.test(password),
            /[^a-zA-Z0-9]/.test(password)
        ];

        strength = checks.filter(Boolean).length;

        // Reset all bars
        strengthBars.forEach(bar => {
            bar.className = 'h-1 flex-1 bg-dark-border rounded transition-all';
        });

        // Update bars based on strength
        const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-green-500'];
        const labels = ['Weak', 'Fair', 'Good', 'Strong'];
        const colorTexts = ['text-red-500', 'text-orange-500', 'text-yellow-500', 'text-green-500'];

        if (strength > 0) {
            const level = Math.min(Math.ceil(strength / 1.25), 4) - 1;
            for (let i = 0; i <= level; i++) {
                strengthBars[i].className = `h-1 flex-1 ${colors[level]} rounded transition-all`;
            }
            strengthLabel.textContent = labels[level];
            strengthLabel.className = colorTexts[level];
        }
    });
});
</script>
@endsection
