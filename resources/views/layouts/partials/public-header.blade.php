<header
    x-data="{ open: false, scrolled: false }"
    x-init="scrolled = window.scrollY > 8; window.addEventListener('scroll', () => { scrolled = window.scrollY > 8 }, { passive: true })"
    :class="scrolled ? 'shadow-sm' : ''"
    class="sticky top-0 z-50 relative border-b border-gray-200 bg-white/95 backdrop-blur-sm transition-shadow"
>
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between gap-4 min-h-[100px] md:min-h-[120px] py-3">
            <div class="flex items-start gap-1 md:gap-2 min-w-0 self-stretch -my-3">
                <a href="{{ route('home') }}" class="flex items-center self-center shrink-0">
                    <img
                        src="{{ asset('images/design/logo-paper-plane.png') }}"
                        alt="Good Neighbors Philippines"
                        class="h-9 md:h-10 w-auto shrink-0"
                    >
                </a>
                <img
                    src="{{ asset('images/design/header-plane-trail.png') }}"
                    alt=""
                    aria-hidden="true"
                    class="pointer-events-none h-10 md:h-14 w-auto max-w-[120px] sm:max-w-[160px] md:max-w-[220px] object-contain object-left shrink-0"
                >
                <img
                    src="{{ asset('images/design/header-clouds.png') }}"
                    alt=""
                    aria-hidden="true"
                    class="pointer-events-none h-12 md:h-16 w-auto max-w-[min(40vw,280px)] md:max-w-[360px] object-contain opacity-70 shrink min-w-0"
                >
            </div>

            <nav class="hidden md:flex items-center gap-6 text-sm font-medium shrink-0">
                <a href="{{ route('home') }}" class="hover:text-gn-accent {{ request()->routeIs('home') ? 'text-gn-accent' : '' }}">Home</a>
                <a href="{{ route('campaigns.index') }}" class="hover:text-gn-accent {{ request()->routeIs('campaigns.*') && !request()->routeIs('campaigns.create', 'campaigns.edit', 'campaigns.share') ? 'text-gn-accent' : '' }}">Browse</a>
                <a href="{{ route('faq') }}" class="hover:text-gn-accent {{ request()->routeIs('faq') ? 'text-gn-accent' : '' }}">FAQ</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="hover:text-gn-accent {{ request()->routeIs('dashboard', 'my-campaigns.*', 'donations.*', 'profile.*', 'account.*') ? 'text-gn-accent' : '' }}">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-gn-accent">Login</a>
                @endauth
                <a href="{{ auth()->check() ? route('campaigns.create') : route('register') }}"
                   class="inline-flex items-center justify-center px-8 py-2.5 bg-gn-orange text-white text-[15px] font-normal rounded-full hover:opacity-90 transition">
                    I want to fundraise
                </a>
            </nav>

            <div class="flex md:hidden items-center gap-2">
                <a href="{{ auth()->check() ? route('campaigns.create') : route('register') }}"
                   class="inline-flex items-center justify-center px-5 py-2 bg-gn-orange text-white text-[13px] font-normal rounded-full hover:opacity-90 transition">
                    I want to fundraise
                </a>
                <button
                    type="button"
                    @click="open = !open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gn-text hover:bg-gray-100"
                    :aria-expanded="open"
                    aria-controls="mobile-nav"
                    aria-label="Toggle menu"
                >
                    <svg x-show="!open" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg x-show="open" x-cloak class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <nav
            id="mobile-nav"
            x-show="open"
            x-cloak
            @click.outside="open = false"
            class="md:hidden border-t border-gray-100 py-3 space-y-1 text-sm font-medium"
        >
            <a href="{{ route('home') }}" class="block px-2 py-2 rounded hover:bg-gray-50 {{ request()->routeIs('home') ? 'text-gn-accent' : '' }}">Home</a>
            <a href="{{ route('campaigns.index') }}" class="block px-2 py-2 rounded hover:bg-gray-50 {{ request()->routeIs('campaigns.*') && !request()->routeIs('campaigns.create', 'campaigns.edit', 'campaigns.share') ? 'text-gn-accent' : '' }}">Browse</a>
            <a href="{{ route('faq') }}" class="block px-2 py-2 rounded hover:bg-gray-50 {{ request()->routeIs('faq') ? 'text-gn-accent' : '' }}">FAQ</a>
            @auth
                <a href="{{ route('dashboard') }}" class="block px-2 py-2 rounded hover:bg-gray-50 {{ request()->routeIs('dashboard', 'my-campaigns.*', 'donations.*', 'profile.*', 'account.*') ? 'text-gn-accent' : '' }}">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="block px-2 py-2 rounded hover:bg-gray-50">Login</a>
            @endauth
        </nav>
    </div>
</header>
