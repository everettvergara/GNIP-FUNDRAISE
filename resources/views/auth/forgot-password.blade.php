@extends('layouts.public')

@section('title', 'Forgot Password')

@section('content')
    <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-14">
        <div class="bg-white p-6 md:p-8 rounded-lg border border-gray-200 shadow-sm">
            <h1 class="text-2xl font-bold mb-2">Forgot your password?</h1>
            <p class="text-sm text-gn-text/70 mb-6">
                No problem. Enter your email address and we will send you a password reset link.
            </p>

            @if (session('status'))
                <div class="mb-6 p-4 rounded-md bg-gn-accent/10 border border-gn-accent/20 text-sm text-gn-text">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="space-y-5" data-recaptcha="password_reset_request">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                    @error('email')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                @error('g-recaptcha-response')<p class="text-gn-danger text-sm">{{ $message }}</p>@enderror

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                    <a href="{{ route('login') }}" class="text-sm text-gn-accent hover:underline">
                        Back to login
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-8 py-2.5 bg-gn-orange text-white text-[15px] font-medium rounded-full hover:opacity-90 transition">
                        Email Password Reset Link
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
