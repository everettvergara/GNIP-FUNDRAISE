@php
    $showDonateButton = $showDonateButton ?? true;
@endphp

@if ($campaign->cover_image)
    <img src="{{ asset('storage/'.$campaign->cover_image) }}" alt="{{ $campaign->title }}" class="w-full h-64 md:h-80 object-cover rounded-lg mb-8">
@endif

<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
    <div>
        @if ($campaign->category)
            <span class="text-sm text-gn-accent font-medium">{{ $campaign->category->name }}</span>
        @endif
        <h1 class="text-3xl font-bold mt-1">{{ $campaign->title }}</h1>
        <div class="mt-3">
            @include('campaigns.partials.organizer', ['campaign' => $campaign, 'size' => 'lg', 'showRole' => true])
        </div>
    </div>
    @if ($showDonateButton)
        <a href="{{ route('donations.create', $campaign->slug) }}"
           class="inline-flex items-center justify-center px-6 py-3 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90 whitespace-nowrap">
            Donate Now
        </a>
    @endif
</div>

<div class="bg-gray-50 rounded-lg p-6 mb-8">
    <div class="flex justify-between text-sm mb-2">
        <span class="font-semibold">₱{{ number_format($campaign->raised_amount, 2) }} raised</span>
        <span class="text-gn-text/60">Goal: ₱{{ number_format($campaign->goal_amount, 2) }}</span>
    </div>
    @php $progress = $campaign->goal_amount > 0 ? min(100, ($campaign->raised_amount / $campaign->goal_amount) * 100) : 0; @endphp
    <div class="h-3 bg-gray-200 rounded-full overflow-hidden">
        <div class="h-full bg-gn-accent rounded-full" style="width: {{ $progress }}%"></div>
    </div>
</div>

<div class="prose max-w-none text-gn-text mb-12">
    {!! nl2br(e($campaign->description)) !!}
</div>

@if ($campaign->media->isNotEmpty())
    <div class="mb-12">
        <h2 class="text-xl font-bold mb-4">Gallery</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
            @foreach ($campaign->media as $image)
                <img src="{{ asset('storage/'.$image->path) }}" alt="" class="w-full h-40 object-cover rounded-lg" loading="lazy" decoding="async">
            @endforeach
        </div>
    </div>
@endif
