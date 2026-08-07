@extends('layouts.public')

@section('title', 'Reset Password')

@section('content')
    <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-14">
        <div class="bg-white p-6 md:p-8 rounded-lg border border-gray-200 shadow-sm">
            <h1 class="text-2xl font-bold mb-2">Reset your password</h1>
            <p class="text-sm text-gn-text/70 mb-6">Choose a new password for your account.</p>

            <form method="POST" action="{{ route('password.store') }}" class="space-y-5" data-recaptcha="password_reset">
                @csrf

                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username"
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                    @error('email')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium mb-1">Password</label>
                    <input type="password" name="password" id="password" required autocomplete="new-password"
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                    @error('password')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required autocomplete="new-password"
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                    @error('password_confirmation')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                @error('g-recaptcha-response')<p class="text-gn-danger text-sm">{{ $message }}</p>@enderror

                <div class="flex justify-end pt-2">
                    <button type="submit" class="inline-flex items-center justify-center px-8 py-2.5 bg-gn-orange text-white text-[15px] font-medium rounded-full hover:opacity-90 transition">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
