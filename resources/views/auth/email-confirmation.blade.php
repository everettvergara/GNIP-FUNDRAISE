@extends('layouts.public')

@section('title', 'Email Confirmation')

@section('content')
    <div class="max-w-lg mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-14">
        <div class="bg-white p-6 md:p-8 rounded-lg border border-gray-200 shadow-sm">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-bold text-gn-text mb-2">Thank you for signing up!</h1>
                <p class="text-gn-text/70 text-sm leading-relaxed">
                    A verification email has been sent to
                    <strong class="text-gn-text">{{ auth()->user()->email }}</strong>.
                    Please check your inbox and click the link to verify your account before creating a campaign.
                </p>
            </div>

            @if (session('status') === 'verification-link-sent')
                <div class="mb-6 p-4 rounded-md bg-gn-accent/10 border border-gn-accent/20 text-sm text-gn-text">
                    A new verification link has been sent to your email address.
                </div>
            @endif

            <p class="text-sm text-gn-text/70 mb-6 leading-relaxed">
                Please note that in order to have your campaign able to accept donations, the organization you requested to support will need to confirm your campaign after you are done creating it.
            </p>

            @error('g-recaptcha-response')<p class="text-gn-danger text-sm mb-4">{{ $message }}</p>@enderror

            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-2">
                <form method="POST" action="{{ route('verification.send') }}" data-recaptcha="verification_resend">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center px-8 py-2.5 bg-gn-accent text-white text-[15px] font-medium rounded-full hover:opacity-90 transition">
                        Resend Verification Email
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="text-sm text-gn-text/70 hover:text-gn-text underline">
                        Log Out
                    </button>
                </form>
            </div>

            <p class="text-center text-sm text-gn-text/60 mt-8">Happy fundraising!</p>
        </div>
    </div>
@endsection
