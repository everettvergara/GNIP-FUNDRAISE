@extends('layouts.public')

@section('title', 'Our Partners')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold mb-8">Our Partners</h1>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            @foreach ($partners as $partner)
                <div class="text-center p-6 bg-white border border-gray-200 rounded-lg">
                    @if ($partner->logo)
                        <img src="{{ asset('storage/'.$partner->logo) }}" alt="{{ $partner->name }}" class="h-16 mx-auto object-contain">
                    @endif
                    <p class="mt-3 font-medium">{{ $partner->name }}</p>
                    @if ($partner->url)
                        <a href="{{ $partner->url }}" target="_blank" rel="noopener" class="text-sm text-gn-accent hover:underline">Visit</a>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
@endsection
