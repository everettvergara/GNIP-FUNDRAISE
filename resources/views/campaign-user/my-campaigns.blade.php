@extends('layouts.campaign-user')

@section('title', 'My Campaigns')

@section('content')
    <div class="flex items-center justify-between mb-8">
        <h1 class="text-2xl font-bold">My Fundraising Pages</h1>
        <a href="{{ route('campaigns.create') }}" class="px-4 py-2 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90 text-sm">
            Create Campaign
        </a>
    </div>

    <div class="space-y-4">
        @forelse ($campaigns as $campaign)
            <div class="bg-white p-6 rounded-lg border border-gray-200 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h2 class="font-semibold text-lg">{{ $campaign->title }}</h2>
                    <p class="text-sm text-gn-text/60 mt-1 flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                            @if ($campaign->status === \App\Models\Campaign::STATUS_DRAFT) bg-gray-100 text-gray-800
                            @elseif ($campaign->status === \App\Models\Campaign::STATUS_PENDING) bg-amber-100 text-amber-800
                            @elseif ($campaign->status === \App\Models\Campaign::STATUS_REJECTED) bg-red-100 text-red-800
                            @elseif ($campaign->status === \App\Models\Campaign::STATUS_ACTIVE) bg-green-100 text-green-800
                            @else bg-gray-100 text-gray-800
                            @endif">
                            {{ $campaign->statusLabel() }}
                        </span>
                        @if ($campaign->category)
                            <span>{{ $campaign->category->name }}</span>
                        @endif
                    </p>
                    <p class="text-sm mt-2">
                        ₱{{ number_format($campaign->raised_amount, 0) }} of ₱{{ number_format($campaign->goal_amount, 0) }}
                    </p>
                    @if ($campaign->status === \App\Models\Campaign::STATUS_PENDING)
                        <p class="text-xs text-amber-700 mt-2">Awaiting admin approval — editing is locked.</p>
                    @endif
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('campaigns.show', $campaign->slug) }}" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-50">View</a>
                    @if ($campaign->canBeEditedByOwner() || $campaign->status === \App\Models\Campaign::STATUS_PENDING)
                        <a href="{{ route('campaigns.edit', $campaign->slug) }}" class="px-3 py-1.5 text-sm border border-gn-accent text-gn-accent rounded-md hover:bg-gn-accent hover:text-white transition">
                            {{ $campaign->canBeEditedByOwner() ? 'Edit' : 'Review' }}
                        </a>
                    @endif
                    @if ($campaign->status === \App\Models\Campaign::STATUS_ACTIVE)
                        <a href="{{ route('campaigns.share', $campaign->slug) }}" class="px-3 py-1.5 text-sm border border-gray-300 rounded-md hover:bg-gray-50">Share</a>
                    @endif
                    @if ($campaign->donations_count === 0 && $campaign->canBeEditedByOwner())
                        <form method="POST" action="{{ route('campaigns.destroy', $campaign->slug) }}" class="inline" onsubmit="return confirm('Delete this campaign permanently? This cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-sm border border-red-300 text-red-600 rounded-md hover:bg-red-50">
                                Delete
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white p-8 rounded-lg border border-gray-200 text-center">
                <p class="text-gn-text/70 mb-4">You haven't created any campaigns yet.</p>
                <a href="{{ route('campaigns.create') }}" class="text-gn-accent font-semibold hover:underline">Create your first campaign</a>
            </div>
        @endforelse
    </div>

    <div class="mt-8">{{ $campaigns->links() }}</div>
@endsection
