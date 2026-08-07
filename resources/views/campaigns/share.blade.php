@extends('layouts.campaign-user')

@section('title', 'Share Campaign')

@section('content')
    <h1 class="text-2xl font-bold mb-2">Promotional Tools</h1>
    <p class="text-gn-text/70 mb-8">Share your campaign and reach more supporters.</p>

    <div class="max-w-2xl space-y-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <h2 class="font-semibold mb-2">{{ $campaign->title }}</h2>
            <p class="text-sm text-gn-text/60 mb-4">Campaign URL</p>
            <div class="flex gap-2">
                <input type="text" readonly value="{{ route('campaigns.show', $campaign->slug) }}"
                       class="flex-1 rounded-md border-gray-300 bg-gray-50 text-sm" id="campaign-url">
                <button type="button" onclick="navigator.clipboard.writeText(document.getElementById('campaign-url').value)"
                        class="px-4 py-2 bg-gn-accent text-white text-sm font-medium rounded-md hover:opacity-90">
                    Copy
                </button>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <h3 class="font-semibold mb-3">Share on Social Media</h3>
            <div class="flex gap-3">
                <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('campaigns.show', $campaign->slug)) }}"
                   target="_blank" rel="noopener"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Facebook</a>
                <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('campaigns.show', $campaign->slug)) }}&text={{ urlencode($campaign->title) }}"
                   target="_blank" rel="noopener"
                   class="px-4 py-2 border border-gray-300 rounded-md text-sm hover:bg-gray-50">Twitter / X</a>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <h3 class="font-semibold mb-3">Email Template</h3>
            <textarea readonly rows="6" class="w-full rounded-md border-gray-300 bg-gray-50 text-sm">Hi,

I'm fundraising for a cause I care about: {{ $campaign->title }}

Please consider donating here: {{ route('campaigns.show', $campaign->slug) }}

Thank you!</textarea>
        </div>

        <a href="{{ route('campaigns.edit', $campaign->slug) }}" class="text-gn-accent hover:underline">&larr; Back to Edit</a>
    </div>
@endsection
