<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Good Neighbors Philippines'))</title>

    @include('layouts.partials.favicon')
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @if (config('services.recaptcha.site_key'))
        <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}"></script>
    @endif
</head>
<body class="font-sans antialiased text-gn-text bg-white">
    @include('layouts.partials.public-header')

    @include('layouts.partials.status-banner')

    <main>
        @yield('content')
    </main>

    @include('layouts.partials.public-footer')
</body>
</html>
