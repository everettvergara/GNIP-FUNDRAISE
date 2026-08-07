@extends('layouts.public')

@section('title', $page->meta_title ?? $page->title)
@section('meta_description', $page->meta_description)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold mb-8">{{ $page->title }}</h1>

        <div class="prose prose-gn max-w-none text-gn-text">
            @if (isset($page->body['content']))
                <p>{{ $page->body['content'] }}</p>
            @endif

            @if (isset($page->body['sections']))
                @foreach ($page->body['sections'] as $section)
                    <div class="mb-8">
                        @if (isset($section['question']))
                            <h2 class="text-xl font-semibold text-gn-accent">{{ $section['question'] }}</h2>
                            <p class="mt-2">{{ $section['answer'] }}</p>
                        @elseif (isset($section['heading']))
                            <h2 class="text-xl font-semibold text-gn-accent">{{ $section['heading'] }}</h2>
                            <p class="mt-2">{{ $section['content'] }}</p>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
@endsection
