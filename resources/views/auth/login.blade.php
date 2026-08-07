@extends('layouts.public')

@section('title', 'Log In')

@section('content')
    <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-14">
        <div class="bg-white p-6 md:p-8 rounded-lg border border-gray-200 shadow-sm">
            <h1 class="text-2xl font-bold mb-2">Log in</h1>
            <p class="text-sm text-gn-text/70 mb-6">Access your fundraising dashboard.</p>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-md bg-gn-accent/10 border border-gn-accent/20 text-sm text-gn-text">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5" data-recaptcha="login">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                    @error('email')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="current-password"
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                    @error('password')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" name="remember" class="rounded border-gray-300 text-gn-accent focus:ring-gn-accent">
                        <span class="ms-2 text-sm text-gn-text/70">Remember me</span>
                    </label>
                </div>

                @error('g-recaptcha-response')<p class="text-gn-danger text-sm">{{ $message }}</p>@enderror

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                    <div class="flex flex-col gap-1 text-sm">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-gn-accent hover:underline">
                                Forgot your password?
                            </a>
                        @endif
                        <a href="{{ route('register') }}" class="text-gn-accent hover:underline">
                            I want to fundraise — create an account
                        </a>
                    </div>
                    <button type="submit" class="inline-flex items-center justify-center px-8 py-2.5 bg-gn-orange text-white text-[15px] font-medium rounded-full hover:opacity-90 transition">
                        Log in
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
