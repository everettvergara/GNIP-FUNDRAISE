@extends('layouts.public')

@section('title', 'Create Your Account')

@section('content')
    <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-14">
        <div class="bg-white p-6 md:p-8 rounded-lg border border-gray-200 shadow-sm">
            <h1 class="text-2xl font-bold mb-2">Create your account</h1>
            <p class="text-sm text-gn-text/70 mb-6">Start fundraising for a cause you care about.</p>

            <form method="POST" action="{{ route('register') }}" class="space-y-5" data-recaptcha="register">
                @csrf

                <div>
                    <label for="first_name" class="block text-sm font-medium mb-1">First Name</label>
                    <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required autofocus autocomplete="given-name"
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                    @error('first_name')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium mb-1">Last Name</label>
                    <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required autocomplete="family-name"
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                    @error('last_name')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium mb-1">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required autocomplete="username"
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

                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pt-2">
                    <a href="{{ route('login') }}" class="text-sm text-gn-accent hover:underline">
                        Already registered? Log in
                    </a>
                    <button type="submit" class="inline-flex items-center justify-center px-8 py-2.5 bg-gn-orange text-white text-[15px] font-medium rounded-full hover:opacity-90 transition">
                        Register
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
