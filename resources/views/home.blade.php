@extends('layouts.public')

@section('title', $page?->meta_title ?? 'Good Neighbors Philippines Fundraising')

@section('content')
    @php
        $hero = $page?->body['hero'] ?? [];
        $heroAlt = $hero['heading'] ?? 'Good Neighbors Philippines fundraising';
    @endphp

    <section class="relative w-full bg-gn-text/5">
        <a href="{{ route('campaigns.index') }}" class="block w-full" aria-label="{{ $hero['cta_primary'] ?? 'Browse campaigns' }}">
            <x-optimized-image
                src="images/design/hero-banner.png"
                :alt="$heroAlt"
                class="block w-full h-auto"
            />
        </a>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-section-heading>
                Featured Campaigns
            </x-section-heading>
            @if ($featuredCampaigns->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($featuredCampaigns as $campaign)
                        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                            <a href="{{ route('campaigns.show', $campaign->slug) }}" class="group block">
                                @if ($campaign->cover_image)
                                    <img src="{{ asset('storage/'.$campaign->cover_image) }}" alt="{{ $campaign->title }}" class="w-full h-48 object-cover" loading="lazy" decoding="async">
                                @else
                                    <div class="w-full h-48 bg-gn-accent/20 flex items-center justify-center text-gn-accent font-semibold">Campaign</div>
                                @endif
                            </a>
                            <div class="p-4">
                                <a href="{{ route('campaigns.show', $campaign->slug) }}" class="group block">
                                    <h3 class="font-semibold text-lg group-hover:text-gn-accent">{{ $campaign->title }}</h3>
                                </a>
                                <div class="mt-2">
                                    @include('campaigns.partials.organizer', ['campaign' => $campaign, 'label' => 'by'])
                                </div>
                                <p class="text-sm text-gn-text/70 mt-2">
                                    ₱{{ number_format($campaign->raised_amount, 0) }} raised of ₱{{ number_format($campaign->goal_amount, 0) }}
                                </p>
                                @php $progress = $campaign->goal_amount > 0 ? min(100, ($campaign->raised_amount / $campaign->goal_amount) * 100) : 0; @endphp
                                <div class="mt-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                                    <div class="h-full bg-gn-accent rounded-full" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-center mt-8">
                    <a href="{{ route('campaigns.index') }}" class="text-gn-accent font-semibold hover:underline">View all campaigns &rarr;</a>
                </div>
            @else
                <p class="text-gn-text/70 text-center py-8">
                    No featured campaigns yet.
                    <a href="{{ route('campaigns.index') }}" class="text-gn-accent font-semibold hover:underline">Browse all campaigns</a>
                </p>
            @endif
        </div>
    </section>

    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <x-section-heading>
                Our Sectors
            </x-section-heading>

            @if ($sectorCategories->isNotEmpty())
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-x-12 gap-y-10">
                    @foreach ($sectorCategories as $category)
                        <div>
                            <h3 class="text-lg text-gn-text mb-6 leading-snug">{{ $category['label'] }}</h3>
                            <div class="space-y-8">
                                @foreach ($category['sectors'] as $sector)
                                    <a
                                        href="{{ route('campaigns.index', ['category' => $sector->slug]) }}"
                                        class="flex flex-col lg:flex-row gap-4 group hover:opacity-90 transition"
                                    >
                                        @if ($sector->image)
                                            <x-optimized-image
                                                :src="ltrim($sector->image, '/')"
                                                :alt="$sector->name"
                                                class="w-full lg:w-[218px] aspect-square object-cover flex-shrink-0"
                                                :lazy="true"
                                            />
                                        @else
                                            <div class="w-full lg:w-[218px] aspect-square bg-gn-accent/20 flex-shrink-0"></div>
                                        @endif
                                        <div class="min-w-0 lg:pt-1">
                                            <h4 class="text-base font-bold text-[#94a240] group-hover:underline">
                                                {{ $sector->name }}
                                            </h4>
                                            <p class="text-xs text-gn-text mt-2 leading-relaxed">
                                                {{ $sector->description }}
                                            </p>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gn-text/70 text-center py-8">
                    Sector information is being updated.
                    <a href="{{ route('sectors.index') }}" class="text-gn-accent font-semibold hover:underline">View our sectors</a>
                </p>
            @endif
        </div>
    </section>
@endsection
