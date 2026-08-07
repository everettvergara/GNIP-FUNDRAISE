@extends('layouts.public')

@section('title', 'Browse Campaigns')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold mb-8">Browse Campaigns</h1>

        <div class="mb-8">
            <p class="text-sm font-semibold text-gn-text mb-2">Category</p>
            <div class="flex flex-wrap gap-2">
                <a
                    href="{{ route('campaigns.index') }}"
                    class="px-3 py-1.5 text-sm border transition {{ $selectedCategory === '' ? 'bg-gn-accent text-white border-gn-accent' : 'bg-white text-gn-text border-gray-200 hover:border-gn-accent' }}"
                >
                    All
                </a>
                @foreach ($categories as $category)
                    <a
                        href="{{ route('campaigns.index', ['category' => $category->slug]) }}"
                        class="px-3 py-1.5 text-sm border transition {{ $selectedCategory === $category->slug ? 'bg-gn-accent text-white border-gn-accent' : 'bg-white text-gn-text border-gray-200 hover:border-gn-accent' }}"
                    >
                        {{ $category->name }}
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse ($campaigns as $campaign)
                <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                    <a href="{{ route('campaigns.show', $campaign->slug) }}" class="group block">
                        @if ($campaign->cover_image)
                            <img src="{{ asset('storage/'.$campaign->cover_image) }}" alt="{{ $campaign->title }}" class="w-full h-48 object-cover">
                        @else
                            <div class="w-full h-48 bg-gn-accent/20 flex items-center justify-center text-gn-accent font-semibold">Campaign</div>
                        @endif
                    </a>
                    <div class="p-4">
                        @if ($campaign->category)
                            <span class="text-xs text-gn-accent font-medium">{{ $campaign->category->name }}</span>
                        @endif
                        <a href="{{ route('campaigns.show', $campaign->slug) }}" class="group block">
                            <h2 class="font-semibold text-lg group-hover:text-gn-accent mt-1">{{ $campaign->title }}</h2>
                        </a>
                        <div class="mt-2">
                            @include('campaigns.partials.organizer', ['campaign' => $campaign, 'label' => 'by'])
                        </div>
                        <p class="text-sm mt-3">
                            ₱{{ number_format($campaign->raised_amount, 0) }} of ₱{{ number_format($campaign->goal_amount, 0) }}
                        </p>
                        @php $progress = $campaign->goal_amount > 0 ? min(100, ($campaign->raised_amount / $campaign->goal_amount) * 100) : 0; @endphp
                        <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-gn-accent rounded-full" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gn-text/70 col-span-full">No active campaigns match these filters.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $campaigns->links() }}</div>
    </div>
@endsection
