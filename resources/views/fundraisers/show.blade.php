@extends('layouts.public')

@section('title', $user->full_name)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="flex flex-col sm:flex-row items-start gap-6 mb-10">
            @if ($user->avatar_url)
                <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-24 h-24 rounded-full object-cover border border-gray-200 shrink-0">
            @else
                <div class="w-24 h-24 rounded-full bg-gn-accent/20 flex items-center justify-center text-gn-accent font-bold text-2xl shrink-0">
                    {{ $user->initials }}
                </div>
            @endif

            <div>
                <h1 class="text-3xl font-bold">{{ $user->full_name }}</h1>
                @if ($user->organization || $user->position)
                    <p class="text-gn-text/70 mt-2">
                        @if ($user->position && $user->organization)
                            {{ $user->position }} at {{ $user->organization }}
                        @elseif ($user->position)
                            {{ $user->position }}
                        @else
                            {{ $user->organization }}
                        @endif
                    </p>
                @endif
                @if ($user->about_me)
                    <div class="mt-4 text-gn-text prose max-w-none">
                        {!! nl2br(e($user->about_me)) !!}
                    </div>
                @endif
            </div>
        </div>

        <h2 class="text-xl font-bold mb-6">Active Campaigns</h2>

        @if ($campaigns->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @foreach ($campaigns as $campaign)
                    <a href="{{ route('campaigns.show', $campaign->slug) }}" class="group block bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition">
                        @if ($campaign->cover_image)
                            <img src="{{ asset('storage/'.$campaign->cover_image) }}" alt="{{ $campaign->title }}" class="w-full h-40 object-cover">
                        @else
                            <div class="w-full h-40 bg-gn-accent/20 flex items-center justify-center text-gn-accent font-semibold">Campaign</div>
                        @endif
                        <div class="p-4">
                            @if ($campaign->category)
                                <span class="text-xs text-gn-accent font-medium">{{ $campaign->category->name }}</span>
                            @endif
                            <h3 class="font-semibold text-lg group-hover:text-gn-accent mt-1">{{ $campaign->title }}</h3>
                            <p class="text-sm mt-3">
                                ₱{{ number_format($campaign->raised_amount, 0) }} of ₱{{ number_format($campaign->goal_amount, 0) }}
                            </p>
                            @php $progress = $campaign->goal_amount > 0 ? min(100, ($campaign->raised_amount / $campaign->goal_amount) * 100) : 0; @endphp
                            <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                                <div class="h-full bg-gn-accent rounded-full" style="width: {{ $progress }}%"></div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <p class="text-gn-text/70">No active campaigns at the moment.</p>
        @endif
    </div>
@endsection
