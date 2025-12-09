@extends('layouts.main')

@section('title', 'Homepage - UP STORE')

@section('content')
<!-- Hero Banner -->
<div class="relative overflow-hidden rounded-2xl mb-12 bg-gradient-to-br from-primary via-primary-hover to-orange-600 p-12 md:p-16 shadow-2xl">
    <!-- Animated Background Pattern -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-32 h-32 bg-white rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute bottom-10 right-10 w-40 h-40 bg-yellow-300 rounded-full blur-3xl animate-pulse" style="animation-delay: 1s"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    <!-- Content -->
    <div class="relative z-10 text-center">
        <div class="inline-block mb-4">
            <span class="bg-white/20 backdrop-blur-sm text-white px-4 py-2 rounded-full text-sm font-semibold border border-white/30">
                🎮 Trusted by 10,000+ Gamers
            </span>
        </div>
        <h1 class="text-4xl md:text-6xl font-black text-white mb-4 tracking-tight">
            Level Up Your Game
        </h1>
        <p class="text-lg md:text-xl text-white/90 mb-8 max-w-2xl mx-auto font-medium">
            Instant top-ups for your favorite games. Fast, secure, and reliable.
        </p>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <x-button variant="primary" size="lg" href="{{ route('home') }}" class="bg-white text-primary hover:bg-gray-100 shadow-2xl font-bold">
                <x-icon name="gamepad" size="md" />
                Browse Games
            </x-button>
            <x-button variant="secondary" size="lg" href="#features" class="bg-white/10 backdrop-blur-sm text-white border-white/30 hover:bg-white/20 font-semibold">
                Learn More
            </x-button>
        </div>

        <!-- Trust Indicators -->
        <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-white/80 text-sm">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Instant Delivery</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Secure Payment</span>
            </div>
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>24/7 Support</span>
            </div>
        </div>
    </div>
</div>

<!-- Search Bar with Modern Design -->
<div class="max-w-2xl mx-auto mb-12">
    <div class="relative" x-data="{
        searchQuery: '{{ request('search') ?? '' }}',
        searchResults: [],
        isSearching: false,
        showResults: false
    }">
        <div class="relative">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
                <x-icon name="search" size="md" class="text-text-tertiary" />
            </div>

            <input
                type="text"
                id="searchInput"
                x-model="searchQuery"
                @input.debounce.300ms="
                    if (searchQuery.length >= 2) {
                        isSearching = true;
                        fetch(`/api/search-games?q=${encodeURIComponent(searchQuery)}`)
                            .then(res => res.json())
                            .then(data => {
                                searchResults = data;
                                showResults = true;
                                isSearching = false;
                            });
                    } else {
                        showResults = false;
                        searchResults = [];
                    }
                "
                @click.away="showResults = false"
                placeholder="Search games (e.g., Mobile Legends, Free Fire)..."
                class="w-full pl-12 pr-12 py-4 bg-dark-surface border-2 border-dark-border rounded-xl text-white placeholder-text-tertiary focus:border-primary focus:outline-none focus:ring-4 focus:ring-primary/20 transition-all text-lg"
            >

            <!-- Loading Spinner -->
            <div x-show="isSearching" id="searchLoading" class="absolute inset-y-0 right-4 flex items-center" style="display: none;">
                <x-icon name="loading" size="md" class="text-primary" />
            </div>
        </div>

        <!-- Search Results Dropdown -->
        <div x-show="showResults && searchResults.length > 0"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             id="searchResultsDropdown"
             class="absolute z-50 w-full mt-2 bg-dark-surface border border-dark-border rounded-xl shadow-2xl overflow-hidden"
             style="display: none;">
            <template x-for="game in searchResults" :key="game.id">
                <a :href="`/topup/${game.id}`"
                   class="flex items-center gap-4 p-4 hover:bg-dark-hover transition-colors border-b border-dark-border last:border-0">
                    <img :src="`/${game.logo}`" :alt="game.name" class="w-12 h-12 rounded-lg object-cover" x-show="game.logo">
                    <div class="flex-1">
                        <div class="font-semibold text-white" x-text="game.name"></div>
                        <div class="text-sm text-text-tertiary">Instant Delivery</div>
                    </div>
                    <x-icon name="arrow-right" size="sm" class="text-text-tertiary" />
                </a>
            </template>
        </div>

        <!-- No Results -->
        <div x-show="showResults && searchResults.length === 0 && searchQuery.length >= 2"
             class="absolute z-50 w-full mt-2 bg-dark-surface border border-dark-border rounded-xl shadow-2xl p-6 text-center"
             style="display: none;">
            <div class="text-text-secondary">No games found for "<span x-text="searchQuery"></span>"</div>
        </div>
    </div>

    @if(request('search'))
        <p class="text-center mt-4 text-text-secondary text-sm">
            Found <strong class="text-primary">{{ $games->count() }}</strong> result(s) for "<strong class="text-white">{{ request('search') }}</strong>"
        </p>
    @endif
</div>

<!-- Popular Games Section -->
<section id="popular-games" class="mb-16">
    <div class="flex items-center justify-between mb-8">
        <div>
            <h2 class="text-2xl md:text-3xl font-bold text-white mb-2">Popular Games</h2>
            <p class="text-text-secondary">Most loved by our community</p>
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 md:gap-6">
        @if($games->count() >= 3)
            @foreach($games->take(5) as $game)
                <a href="{{ route('topup', $game->id) }}" class="group block">
                    <div class="bg-dark-surface rounded-xl border border-dark-border overflow-hidden h-full transition-all duration-300 hover:border-primary hover:shadow-lg hover:shadow-primary/20 hover:-translate-y-1">
                        <!-- Game Image -->
                        <div class="relative aspect-[3/4] overflow-hidden bg-dark-elevated">
                            @if($game->logo)
                                <img src="{{ asset($game->logo) }}"
                                     alt="{{ $game->name }}"
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-dark-border to-dark-surface flex items-center justify-center">
                                    <x-icon name="gamepad" size="xl" class="text-text-tertiary" />
                                </div>
                            @endif

                            <!-- Overlay on Hover -->
                            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                <div class="text-white text-center">
                                    <svg class="w-8 h-8 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                                    </svg>
                                    <span class="text-sm font-medium">Top Up Now</span>
                                </div>
                            </div>
                        </div>

                        <!-- Game Info -->
                        <div class="p-4">
                            <h3 class="font-semibold text-white mb-1 line-clamp-1 group-hover:text-primary transition-colors">{{ $game->name }}</h3>
                            <p class="text-xs text-text-tertiary">Instant Delivery</p>
                        </div>
                    </div>
                </a>
            @endforeach
        @else
            @for($i = 0; $i < 5; $i++)
                <div class="animate-pulse">
                    <x-card :padding="false">
                        <div class="aspect-[3/4] bg-dark-border"></div>
                        <div class="p-4 space-y-2">
                            <div class="h-4 bg-dark-border rounded w-3/4"></div>
                            <div class="h-3 bg-dark-border rounded w-1/2"></div>
                        </div>
                    </x-card>
                </div>
            @endfor
        @endif
    </div>
</section>

<!-- Trending Section -->
<section>
    <div class="flex items-center gap-3 mb-6">
        <x-icon name="fire" size="lg" class="text-primary" />
        <h2 class="text-2xl md:text-3xl font-bold text-white">TRENDING GAMES</h2>
    </div>

    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
        @if($games->count() > 0)
            @foreach($games as $game)
                <a href="{{ route('topup', $game->id) }}" class="group block">
                    <x-card :hover="true" :padding="false" class="overflow-hidden h-full">
                        <!-- Game Image -->
                        <div class="relative aspect-[3/4] overflow-hidden">
                            @if($game->logo)
                                <img src="{{ asset($game->logo) }}"
                                     alt="{{ $game->name }}"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                     loading="lazy">
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-dark-border to-dark-surface flex items-center justify-center">
                                    <x-icon name="gamepad" size="lg" class="text-text-tertiary" />
                                </div>
                            @endif

                            <!-- Overlay on Hover -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-3">
                                <div class="w-full">
                                    <x-button variant="primary" size="sm" class="w-full">
                                        <x-icon name="plus" size="sm" />
                                        Top Up Now
                                    </x-button>
                                </div>
                            </div>
                        </div>

                        <!-- Game Info -->
                        <div class="p-3">
                            <h3 class="font-semibold text-white text-sm mb-1 line-clamp-1">{{ $game->name }}</h3>
                            <div class="flex items-center gap-1.5 text-xs text-text-tertiary">
                                <div class="flex items-center gap-0.5">
                                    @for($i = 0; $i < 5; $i++)
                                        <x-icon name="star" size="sm" class="text-yellow-500" />
                                    @endfor
                                </div>
                                <span>4.8</span>
                            </div>
                        </div>
                    </x-card>
                </a>
            @endforeach

            <!-- Fill empty slots -->
            @for($i = $games->count(); $i < 12; $i++)
                <div class="opacity-50">
                    <x-card :padding="false">
                        <div class="aspect-[3/4] bg-gradient-to-br from-dark-border to-dark-surface flex items-center justify-center">
                            <div class="text-center p-4">
                                <x-icon name="gamepad" size="lg" class="text-text-tertiary mx-auto mb-2" />
                                <div class="text-xs text-text-tertiary font-medium">Coming Soon</div>
                            </div>
                        </div>
                        <div class="p-3">
                            <div class="h-4 bg-dark-border rounded mb-2"></div>
                            <div class="h-3 bg-dark-border rounded w-2/3"></div>
                        </div>
                    </x-card>
                </div>
            @endfor
        @else
            <div class="col-span-full text-center py-12">
                <x-icon name="gamepad" size="xl" class="text-text-tertiary mx-auto mb-4" />
                <p class="text-text-secondary">No games available yet.</p>
            </div>
        @endif
    </div>
</section>

<!-- Features Section -->
<section id="features" class="mt-20">
    <div class="text-center mb-12">
        <h2 class="text-3xl md:text-4xl font-bold text-white mb-3">Why Choose Us</h2>
        <p class="text-text-secondary max-w-2xl mx-auto">We provide the best gaming top-up experience with unmatched service quality</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-dark-surface border border-dark-border rounded-xl p-8 text-center hover:border-primary transition-all duration-300 group">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-5 group-hover:bg-primary/20 transition-colors">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                </svg>
            </div>
            <h3 class="font-bold text-lg text-white mb-3">Lightning Fast</h3>
            <p class="text-text-secondary text-sm leading-relaxed">Instant delivery within seconds. No waiting, just gaming.</p>
        </div>

        <div class="bg-dark-surface border border-dark-border rounded-xl p-8 text-center hover:border-primary transition-all duration-300 group">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-5 group-hover:bg-primary/20 transition-colors">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h3 class="font-bold text-lg text-white mb-3">100% Secure</h3>
            <p class="text-text-secondary text-sm leading-relaxed">Bank-level encryption protects all your transactions.</p>
        </div>

        <div class="bg-dark-surface border border-dark-border rounded-xl p-8 text-center hover:border-primary transition-all duration-300 group">
            <div class="w-14 h-14 rounded-2xl bg-primary/10 flex items-center justify-center mx-auto mb-5 group-hover:bg-primary/20 transition-colors">
                <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h3 class="font-bold text-lg text-white mb-3">Best Value</h3>
            <p class="text-text-secondary text-sm leading-relaxed">Competitive prices with no hidden fees. What you see is what you pay.</p>
        </div>
    </div>
</section>
@endsection

@section('scripts')
<script>
// Vanilla JS fallback for search functionality (works even if Alpine.js fails)
(function() {
    let searchTimeout;
    const searchInput = document.getElementById('searchInput');
    const searchResultsDropdown = document.getElementById('searchResultsDropdown');
    const searchLoading = document.getElementById('searchLoading');

    if (!searchInput) return; // Exit if search not on page

    // Search functionality
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        const query = e.target.value.trim();

        if (query.length >= 2) {
            // Show loading spinner
            if (searchLoading) searchLoading.style.display = 'flex';

            // Debounce: wait 300ms before searching
            searchTimeout = setTimeout(() => {
                fetch(`/api/search-games?q=${encodeURIComponent(query)}`)
                    .then(res => {
                        if (!res.ok) throw new Error('Search failed');
                        return res.json();
                    })
                    .then(data => {
                        displaySearchResults(data, query);
                        if (searchLoading) searchLoading.style.display = 'none';
                    })
                    .catch(err => {
                        console.error('Search error:', err);
                        searchResultsDropdown.innerHTML = '<div class="p-6 text-center text-status-error">Search failed. Please try again.</div>';
                        searchResultsDropdown.style.display = 'block';
                        if (searchLoading) searchLoading.style.display = 'none';
                    });
            }, 300);
        } else {
            // Query too short, hide results
            if (searchResultsDropdown) searchResultsDropdown.style.display = 'none';
            if (searchLoading) searchLoading.style.display = 'none';
        }
    });

    // Display search results
    function displaySearchResults(games, query) {
        if (!searchResultsDropdown) return;

        if (games.length === 0) {
            searchResultsDropdown.innerHTML = `
                <div class="p-6 text-center text-text-secondary">
                    No games found for "<span class="text-white">${escapeHtml(query)}</span>"
                </div>
            `;
            searchResultsDropdown.style.display = 'block';
            return;
        }

        const html = games.map(game => `
            <a href="/topup/${game.id}" class="flex items-center gap-4 p-4 hover:bg-dark-hover transition-colors border-b border-dark-border last:border-0">
                ${game.logo ? `<img src="/${escapeHtml(game.logo)}" alt="${escapeHtml(game.name)}" class="w-12 h-12 rounded-lg object-cover">` : ''}
                <div class="flex-1">
                    <div class="font-semibold text-white">${escapeHtml(game.name)}</div>
                    <div class="text-sm text-text-tertiary">Instant Delivery</div>
                </div>
                <svg class="w-4 h-4 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                </svg>
            </a>
        `).join('');

        searchResultsDropdown.innerHTML = html;
        searchResultsDropdown.style.display = 'block';
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Close search results when clicking outside
    document.addEventListener('click', function(event) {
        if (searchInput && searchResultsDropdown &&
            !searchInput.contains(event.target) &&
            !searchResultsDropdown.contains(event.target)) {
            searchResultsDropdown.style.display = 'none';
        }
    });

    // Handle escape key to close results
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape' && searchResultsDropdown) {
            searchResultsDropdown.style.display = 'none';
            searchInput.blur();
        }
    });
})();
</script>
@endsection
