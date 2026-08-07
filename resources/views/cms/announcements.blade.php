@extends('layouts.public')

@section('title', 'Announcements')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold mb-8">Announcement and News</h1>

        <div class="space-y-6">
            @forelse ($announcements as $announcement)
                <article class="bg-white border border-gray-200 rounded-lg p-6">
                    <h2 class="font-semibold text-lg">{{ $announcement->title }}</h2>
                    <p class="text-sm text-gn-text/60 mt-1">{{ $announcement->published_at?->format('M d, Y') }}</p>
                    <p class="text-gn-text/80 mt-3">{{ $announcement->excerpt ?? Str::limit($announcement->body, 300) }}</p>
                </article>
            @empty
                <p class="text-gn-text/70">No announcements published yet.</p>
            @endforelse
        </div>

        <div class="mt-8">{{ $announcements->links() }}</div>
    </div>
@endsection
