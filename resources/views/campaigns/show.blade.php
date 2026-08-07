@extends('layouts.public')

@section('title', $campaign->title)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
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
            <a href="{{ route('donations.create', $campaign->slug) }}"
               class="inline-flex items-center justify-center px-6 py-3 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90 whitespace-nowrap">
                Donate Now
            </a>
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
                        <img src="{{ asset('storage/'.$image->path) }}" alt="" class="w-full h-40 object-cover rounded-lg">
                    @endforeach
                </div>
            </div>
        @endif

        @auth
            @if (auth()->id() === $campaign->user_id)
                <div class="flex gap-4 mb-8">
                    <a href="{{ route('campaigns.edit', $campaign->slug) }}" class="text-gn-accent font-medium hover:underline">Edit Campaign</a>
                    <a href="{{ route('campaigns.share', $campaign->slug) }}" class="text-gn-accent font-medium hover:underline">Share Tools</a>
                </div>
            @endif
        @endauth

        <div class="mb-12">
            <h2 class="text-xl font-bold mb-4">Donations</h2>
            @if ($donations->isNotEmpty())
                <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="text-left px-4 py-3 font-medium">Donor</th>
                                <th class="text-left px-4 py-3 font-medium">Amount</th>
                                @if ($isOwner)
                                    <th class="text-left px-4 py-3 font-medium">Status</th>
                                @endif
                                <th class="text-left px-4 py-3 font-medium">Date</th>
                                <th class="text-left px-4 py-3 font-medium">Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($donations as $donation)
                                <tr class="border-t border-gray-100">
                                    <td class="px-4 py-3">{{ $donation->donorDisplayName() }}</td>
                                    <td class="px-4 py-3 font-medium">₱{{ number_format($donation->amount, 2) }}</td>
                                    @if ($isOwner)
                                        <td class="px-4 py-3 capitalize">{{ $donation->status }}</td>
                                    @endif
                                    <td class="px-4 py-3">{{ ($donation->paid_at ?? $donation->created_at)->format('M d, Y g:i A') }}</td>
                                    <td class="px-4 py-3 text-gn-text/80">
                                        @if ($donation->message)
                                            {{ $donation->message }}
                                        @else
                                            <span class="text-gn-text/40">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gn-text/60 text-sm">No donations yet.</p>
            @endif

            @if ($donations->hasPages())
                <div class="mt-6">{{ $donations->links() }}</div>
            @endif
        </div>

        <div class="mb-12">
            <h2 class="text-xl font-bold mb-4">Impact Reports</h2>

            @if ($isOwner && $campaign->status === \App\Models\Campaign::STATUS_ACTIVE)
                <form method="POST"
                      action="{{ route('campaigns.impact-reports.store', $campaign->slug) }}"
                      enctype="multipart/form-data"
                      class="mb-8 p-6 bg-gray-50 rounded-lg border border-gray-200 space-y-4">
                    @csrf

                    <div>
                        <label for="impact_message" class="block text-sm font-medium mb-1">Message</label>
                        <textarea name="message"
                                  id="impact_message"
                                  rows="4"
                                  required
                                  class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-1 text-sm text-gn-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <x-gallery-upload
                        name="photos"
                        id="impact_photos"
                        label="Event Photos"
                        hint="JPG, PNG, or WebP. Upload 1–10 photos from your event."
                    />
                    @error('photos')
                        <p class="mt-1 text-sm text-gn-danger">{{ $message }}</p>
                    @enderror
                    @error('photos.*')
                        <p class="mt-1 text-sm text-gn-danger">{{ $message }}</p>
                    @enderror

                    <button type="submit" class="px-6 py-2 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90">
                        Post Impact Report
                    </button>
                </form>
            @endif

            @if ($campaign->impactReports->isNotEmpty())
                <div class="space-y-6">
                    @foreach ($campaign->impactReports as $report)
                        <article class="border border-gray-200 rounded-lg p-6">
                            <div class="flex flex-wrap items-start justify-between gap-4 mb-4">
                                <p class="text-sm text-gn-text/60">{{ $report->created_at->format('M d, Y g:i A') }}</p>
                                @if ($isOwner)
                                    <form method="POST"
                                          action="{{ route('campaigns.impact-reports.destroy', [$campaign->slug, $report]) }}"
                                          onsubmit="return confirm('Delete this impact report?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-sm text-gn-danger hover:underline">Delete</button>
                                    </form>
                                @endif
                            </div>

                            <p class="text-gn-text whitespace-pre-line mb-4">{{ $report->message }}</p>

                            @if ($report->photos->isNotEmpty())
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                                    @foreach ($report->photos as $photo)
                                        <img src="{{ asset('storage/'.$photo->path) }}" alt="" class="w-full h-40 object-cover rounded-lg">
                                    @endforeach
                                </div>
                            @endif
                        </article>
                    @endforeach
                </div>
            @else
                <p class="text-gn-text/60 text-sm">No impact reports yet.</p>
            @endif
        </div>
    </div>
@endsection
