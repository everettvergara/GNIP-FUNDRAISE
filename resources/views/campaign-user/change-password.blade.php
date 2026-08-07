@extends('layouts.campaign-user')

@section('title', 'Change Password')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Change Password</h1>

    <form method="POST" action="{{ route('account.change-password.update') }}" class="max-w-md space-y-6 bg-white p-6 rounded-lg border border-gray-200">
        @csrf
        @method('PUT')

        <div>
            <label for="current_password" class="block text-sm font-medium mb-1">Current Password</label>
            <input type="password" name="current_password" id="current_password" required
                   class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent" autocomplete="current-password">
            @error('current_password')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password" class="block text-sm font-medium mb-1">New Password</label>
            <input type="password" name="password" id="password" required
                   class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent" autocomplete="new-password">
            @error('password')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium mb-1">Confirm New Password</label>
            <input type="password" name="password_confirmation" id="password_confirmation" required
                   class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent" autocomplete="new-password">
        </div>

        <div class="flex gap-4">
            <button type="submit" class="px-6 py-2 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90">
                Update Password
            </button>
            <a href="{{ route('profile.edit') }}" class="px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                Back to Profile
            </a>
        </div>
    </form>
@endsection
