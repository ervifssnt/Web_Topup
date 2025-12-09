<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'UP STORE')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;900&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @yield('styles')
</head>
<body class="min-h-screen bg-dark-elevated text-text-primary">
    <!-- Navigation Bar -->
    <nav class="sticky top-0 z-50 bg-dark-surface/95 backdrop-blur-sm border-b border-dark-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo & Navigation -->
                <div class="flex items-center gap-8 lg:gap-12">
                    <!-- Logo -->
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <span class="text-3xl font-black italic text-primary group-hover:scale-110 transition-transform">UP</span>
                        <span class="text-xl font-bold tracking-wider">STORE</span>
                    </a>

                    <!-- Desktop Navigation -->
                    <ul class="hidden md:flex items-center gap-6 lg:gap-8">
                        <li>
                            <a href="{{ route('home') }}"
                               class="flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-primary' : 'text-text-secondary hover:text-primary' }}">
                                <x-icon name="home" size="sm" />
                                <span>Home</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('home') }}"
                               class="flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('home') ? 'text-primary' : 'text-text-secondary hover:text-primary' }}">
                                <x-icon name="gamepad" size="sm" />
                                <span>Games</span>
                            </a>
                        </li>
                        @auth
                            <li>
                                <a href="{{ route('profile.dashboard') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('profile.dashboard') ? 'text-primary' : 'text-text-secondary hover:text-primary' }}">
                                    <x-icon name="dashboard" size="sm" />
                                    <span>Dashboard</span>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('profile.history') }}"
                                   class="flex items-center gap-2 px-3 py-2 text-sm font-medium transition-colors {{ request()->routeIs('profile.history') ? 'text-primary' : 'text-text-secondary hover:text-primary' }}">
                                    <x-icon name="history" size="sm" />
                                    <span>History</span>
                                </a>
                            </li>
                        @endauth
                    </ul>
                </div>

                <!-- Right Side Actions -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- Balance Display -->
                        <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-dark-elevated rounded-lg border border-primary/20">
                            <x-icon name="wallet" size="sm" class="text-primary" />
                            <div class="text-xs">
                                <div class="text-text-tertiary">Balance</div>
                                <div class="font-bold text-primary">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</div>
                            </div>
                        </div>

                        <!-- Top-Up Button -->
                        <x-button variant="success" size="sm" href="{{ route('topup.form') }}" class="hidden sm:inline-flex">
                            <x-icon name="plus" size="sm" />
                            Top-Up
                        </x-button>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" id="profileDropdown">
                            <button @click="open = !open"
                                    @click.away="open = false"
                                    onclick="toggleProfileDropdown()"
                                    id="profileDropdownButton"
                                    class="flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-dark-hover transition-colors">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center text-sm font-bold text-white">
                                    {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                                </div>
                                <svg class="w-4 h-4 text-text-tertiary transition-transform" :class="{ 'rotate-180': open }" id="profileDropdownChevron" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-200"
                                 x-transition:enter-start="opacity-0 scale-95"
                                 x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-150"
                                 x-transition:leave-start="opacity-100 scale-100"
                                 x-transition:leave-end="opacity-0 scale-95"
                                 id="profileDropdownMenu"
                                 class="absolute right-0 mt-2 w-56 bg-dark-surface border border-dark-border rounded-lg shadow-xl py-2 z-50"
                                 style="display: none;">

                                <!-- User Info -->
                                <div class="px-4 py-3 border-b border-dark-border">
                                    <div class="text-sm font-medium text-white">{{ Auth::user()->username }}</div>
                                    <div class="text-xs text-text-tertiary truncate">{{ Auth::user()->email }}</div>
                                </div>

                                <!-- Menu Items -->
                                <a href="{{ route('profile.dashboard') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-text-secondary hover:bg-dark-hover hover:text-white transition-colors">
                                    <x-icon name="dashboard" size="sm" />
                                    Dashboard
                                </a>
                                <a href="{{ route('profile.history') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-text-secondary hover:bg-dark-hover hover:text-white transition-colors">
                                    <x-icon name="history" size="sm" />
                                    Transaction History
                                </a>
                                <a href="{{ route('topup.form') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-text-secondary hover:bg-dark-hover hover:text-white transition-colors sm:hidden">
                                    <x-icon name="plus" size="sm" />
                                    Top-Up Balance
                                </a>
                                <a href="{{ route('2fa.show') }}" class="flex items-center gap-3 px-4 py-2 text-sm text-text-secondary hover:bg-dark-hover hover:text-white transition-colors">
                                    <x-icon name="lock" size="sm" />
                                    2FA Settings
                                </a>

                                <div class="border-t border-dark-border mt-2 pt-2">
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full flex items-center gap-3 px-4 py-2 text-sm text-status-error hover:bg-dark-hover transition-colors">
                                            <x-icon name="logout" size="sm" />
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <x-button variant="primary" size="sm" href="{{ route('login') }}">
                            Sign In
                        </x-button>
                    @endauth

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="md:hidden p-2 rounded-lg hover:bg-dark-hover transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-2"
             class="md:hidden border-t border-dark-border bg-dark-surface"
             style="display: none;">
            <div class="px-4 py-4 space-y-2">
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('home') ? 'bg-primary/10 text-primary' : 'text-text-secondary hover:bg-dark-hover' }}">
                    <x-icon name="home" size="sm" />
                    Home
                </a>
                <a href="{{ route('home') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('home') ? 'bg-primary/10 text-primary' : 'text-text-secondary hover:bg-dark-hover' }}">
                    <x-icon name="gamepad" size="sm" />
                    Games
                </a>
                @auth
                    <a href="{{ route('profile.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('profile.dashboard') ? 'bg-primary/10 text-primary' : 'text-text-secondary hover:bg-dark-hover' }}">
                        <x-icon name="dashboard" size="sm" />
                        Dashboard
                    </a>
                    <a href="{{ route('profile.history') }}" class="flex items-center gap-3 px-4 py-3 rounded-lg {{ request()->routeIs('profile.history') ? 'bg-primary/10 text-primary' : 'text-text-secondary hover:bg-dark-hover' }}">
                        <x-icon name="history" size="sm" />
                        History
                    </a>

                    <!-- Mobile Balance -->
                    <div class="sm:hidden mt-4 p-4 bg-dark-elevated rounded-lg border border-primary/20">
                        <div class="flex items-center justify-between">
                            <div>
                                <div class="text-xs text-text-tertiary">Balance</div>
                                <div class="text-lg font-bold text-primary">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</div>
                            </div>
                            <a href="{{ route('topup.form') }}" class="btn btn-success btn-sm">
                                <x-icon name="plus" size="sm" />
                                Top-Up
                            </a>
                        </div>
                    </div>
                @endauth
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Alert Messages -->
        @if(session('success'))
            <x-alert type="success" dismissible class="mb-6 animate-fade-in">
                {{ session('success') }}
            </x-alert>
        @endif

        @if(session('error'))
            <x-alert type="error" dismissible class="mb-6 animate-fade-in">
                {{ session('error') }}
            </x-alert>
        @endif

        @if($errors->any())
            <x-alert type="error" dismissible class="mb-6 animate-fade-in">
                <ul class="list-disc list-inside space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <!-- Page Content -->
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-20 bg-dark-base border-t border-dark-border">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
                <!-- Brand Column -->
                <div class="lg:col-span-2">
                    <div class="flex items-center gap-2 mb-4">
                        <span class="text-3xl font-black italic text-primary">UP</span>
                        <span class="text-xl font-bold tracking-wider">STORE</span>
                    </div>
                    <p class="text-text-secondary text-sm leading-relaxed mb-6">
                        Your trusted platform for game top-up and digital products. Fast, secure, and reliable service for gamers worldwide.
                    </p>
                    <!-- Social Links -->
                    <div class="flex items-center gap-3">
                        <a href="#" class="w-10 h-10 rounded-lg bg-dark-surface border border-dark-border flex items-center justify-center text-text-tertiary hover:text-primary hover:border-primary transition-all hover:-translate-y-1" aria-label="Facebook">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-dark-surface border border-dark-border flex items-center justify-center text-text-tertiary hover:text-primary hover:border-primary transition-all hover:-translate-y-1" aria-label="Instagram">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-lg bg-dark-surface border border-dark-border flex items-center justify-center text-text-tertiary hover:text-primary hover:border-primary transition-all hover:-translate-y-1" aria-label="Twitter">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                        </a>
                    </div>
                </div>

                <!-- Site Map -->
                <div>
                    <h3 class="font-bold text-white mb-4">Site Map</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ route('home') }}" class="text-sm text-text-secondary hover:text-primary transition-colors">Home</a></li>
                        <li><a href="{{ route('home') }}" class="text-sm text-text-secondary hover:text-primary transition-colors">Games</a></li>
                        @auth
                            <li><a href="{{ route('profile.dashboard') }}" class="text-sm text-text-secondary hover:text-primary transition-colors">Dashboard</a></li>
                            <li><a href="{{ route('profile.history') }}" class="text-sm text-text-secondary hover:text-primary transition-colors">History</a></li>
                        @endauth
                    </ul>
                </div>

                <!-- Legal -->
                <div>
                    <h3 class="font-bold text-white mb-4">Legal</h3>
                    <ul class="space-y-3">
                        <li><a href="#" class="text-sm text-text-secondary hover:text-primary transition-colors">Terms & Conditions</a></li>
                        <li><a href="#" class="text-sm text-text-secondary hover:text-primary transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-sm text-text-secondary hover:text-primary transition-colors">Refund Policy</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="mt-12 pt-8 border-t border-dark-border text-center">
                <p class="text-sm text-text-tertiary">
                    &copy; {{ date('Y') }} UP STORE. All rights reserved.
                </p>
            </div>
        </div>
    </footer>

    <!-- Alpine.js for dropdown functionality -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('global', () => ({
                mobileMenuOpen: false
            }))
        })

        // Vanilla JS fallback for profile dropdown (in case Alpine.js fails to load)
        function toggleProfileDropdown() {
            const menu = document.getElementById('profileDropdownMenu');
            const chevron = document.getElementById('profileDropdownChevron');

            if (menu.style.display === 'none' || menu.style.display === '') {
                menu.style.display = 'block';
                chevron.classList.add('rotate-180');
            } else {
                menu.style.display = 'none';
                chevron.classList.remove('rotate-180');
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            const dropdown = document.getElementById('profileDropdown');
            const menu = document.getElementById('profileDropdownMenu');

            if (dropdown && menu && !dropdown.contains(event.target)) {
                menu.style.display = 'none';
                document.getElementById('profileDropdownChevron')?.classList.remove('rotate-180');
            }
        });
    </script>

    @yield('scripts')
</body>
</html>
