<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>

    @include('layouts.partials.favicon')

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased text-gn-text bg-gray-50">
    @include('layouts.partials.public-header')

    @include('layouts.partials.campaign-user-subnav')

    @include('layouts.partials.status-banner')

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    @include('layouts.partials.public-footer')
</body>
</html>
