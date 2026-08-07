<nav class="bg-gray-50 border-b border-gray-200" aria-label="Account navigation">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center gap-4 text-sm font-medium overflow-x-auto py-3">
            <a href="{{ route('dashboard') }}" class="whitespace-nowrap hover:text-gn-accent {{ request()->routeIs('dashboard') ? 'text-gn-accent' : '' }}">Dashboard</a>
            <a href="{{ route('my-campaigns.index') }}" class="whitespace-nowrap hover:text-gn-accent {{ request()->routeIs('my-campaigns.*', 'campaigns.edit', 'campaigns.share') ? 'text-gn-accent' : '' }}">My Campaigns</a>
            <a href="{{ route('campaigns.create') }}" class="whitespace-nowrap hover:text-gn-accent {{ request()->routeIs('campaigns.create', 'campaigns.store') ? 'text-gn-accent' : '' }}">Create</a>
            <a href="{{ route('donations.index') }}" class="whitespace-nowrap hover:text-gn-accent {{ request()->routeIs('donations.*') ? 'text-gn-accent' : '' }}">Donations</a>
            <a href="{{ route('profile.edit') }}" class="whitespace-nowrap hover:text-gn-accent {{ request()->routeIs('profile.*', 'account.*') ? 'text-gn-accent' : '' }}">Profile</a>
            <form method="POST" action="{{ route('logout') }}" class="inline ml-auto">
                @csrf
                <button type="submit" class="whitespace-nowrap hover:text-gn-orange">Logout</button>
            </form>
        </div>
    </div>
</nav>
