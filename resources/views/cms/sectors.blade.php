@extends('layouts.public')

@section('title', 'Our Sectors')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold mb-8">Our Sectors</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach ($sectors as $sector)
                <a href="{{ route('sectors.show', $sector->slug) }}" class="block p-6 bg-white border border-gray-200 rounded-lg hover:border-gn-accent transition">
                    <h2 class="font-semibold text-xl text-gn-accent">{{ $sector->name }}</h2>
                    <p class="text-gn-text/80 mt-2">{{ Str::limit($sector->description, 200) }}</p>
                </a>
            @endforeach
        </div>
    </div>
@endsection
