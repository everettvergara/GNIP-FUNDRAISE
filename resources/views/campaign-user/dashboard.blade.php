@extends('layouts.campaign-user')

@section('title', 'Dashboard')

@section('content')
    @if (request('verified'))
        <div class="mb-6 p-4 rounded-md bg-gn-accent/10 border border-gn-accent/20 text-sm text-gn-text">
            Your email has been verified. You can now create and manage your campaigns.
        </div>
    @endif

    <h1 class="text-2xl font-bold mb-8">Dashboard</h1>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-10">
        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <p class="text-sm text-gn-text/60">Total Campaigns</p>
            <p class="text-3xl font-bold text-gn-accent mt-1">{{ $stats['campaigns_count'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <p class="text-sm text-gn-text/60">Active Campaigns</p>
            <p class="text-3xl font-bold text-gn-accent mt-1">{{ $stats['active_campaigns'] }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <p class="text-sm text-gn-text/60">Total Raised</p>
            <p class="text-3xl font-bold text-gn-accent mt-1">₱{{ number_format($stats['total_raised'], 0) }}</p>
        </div>
        <div class="bg-white p-6 rounded-lg border border-gray-200">
            <p class="text-sm text-gn-text/60">Donations Received</p>
            <p class="text-3xl font-bold text-gn-accent mt-1">{{ $stats['donations_count'] }}</p>
        </div>
    </div>

    <div class="flex gap-4 mb-8">
        <a href="{{ route('campaigns.create') }}" class="px-4 py-2 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90">Create Campaign</a>
        <a href="{{ route('my-campaigns.index') }}" class="px-4 py-2 border border-gn-accent text-gn-accent font-semibold rounded-md hover:bg-gn-accent hover:text-white transition">My Campaigns</a>
    </div>

    @if ($stats['recent_donations']->isNotEmpty())
        <h2 class="text-lg font-semibold mb-4">Recent Donations</h2>
        <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="text-left px-4 py-3 font-medium">Donor</th>
                        <th class="text-left px-4 py-3 font-medium">Campaign</th>
                        <th class="text-left px-4 py-3 font-medium">Amount</th>
                        <th class="text-left px-4 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($stats['recent_donations'] as $donation)
                        <tr class="border-t border-gray-100">
                            <td class="px-4 py-3">{{ $donation->donorDisplayName() }}</td>
                            <td class="px-4 py-3">{{ $donation->campaign->title }}</td>
                            <td class="px-4 py-3">₱{{ number_format($donation->amount, 2) }}</td>
                            <td class="px-4 py-3 capitalize">{{ $donation->status }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
@endsection
