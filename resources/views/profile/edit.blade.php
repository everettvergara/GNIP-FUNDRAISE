@extends('layouts.campaign-user')

@section('title', 'Profile')

@section('content')
    <h1 class="text-2xl font-bold mb-8">Profile</h1>

    <div class="max-w-xl space-y-6">
        <div class="bg-white p-6 rounded-lg border border-gray-200">
            @include('profile.partials.update-profile-information-form')
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <h2 class="text-lg font-medium mb-2">Password</h2>
            <p class="text-sm text-gn-text/70 mb-4">Update your password on a separate page.</p>
            <a href="{{ route('account.change-password') }}" class="text-gn-accent font-medium hover:underline">
                Change Password &rarr;
            </a>
        </div>

        <div class="bg-white p-6 rounded-lg border border-gray-200">
            @include('profile.partials.delete-user-form')
        </div>
    </div>
@endsection
