@extends('layouts.public')

@section('title', $sector->name)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <a href="{{ route('sectors.index') }}" class="text-gn-accent text-sm hover:underline">&larr; All Sectors</a>
        <h1 class="text-3xl font-bold mt-4 mb-6">{{ $sector->name }}</h1>
        <p class="text-gn-text/80 leading-relaxed">{{ $sector->description }}</p>
    </div>
@endsection
