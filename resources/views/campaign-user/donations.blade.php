@extends('layouts.campaign-user')

@section('title', 'Donations')

@section('content')
    <h1 class="text-2xl font-bold mb-8">Donations Received</h1>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="text-left px-4 py-3 font-medium">Donor</th>
                    <th class="text-left px-4 py-3 font-medium">Campaign</th>
                    <th class="text-left px-4 py-3 font-medium">Amount</th>
                    <th class="text-left px-4 py-3 font-medium">Type</th>
                    <th class="text-left px-4 py-3 font-medium">Status</th>
                    <th class="text-left px-4 py-3 font-medium">Date</th>
                    <th class="text-left px-4 py-3 font-medium">Message</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($donations as $donation)
                    <tr class="border-t border-gray-100">
                        <td class="px-4 py-3">{{ $donation->donorDisplayName() }}</td>
                        <td class="px-4 py-3">{{ $donation->campaign->title }}</td>
                        <td class="px-4 py-3">₱{{ number_format($donation->amount, 2) }}</td>
                        <td class="px-4 py-3">{{ $donation->typeLabel() }}</td>
                        <td class="px-4 py-3 capitalize">{{ $donation->status }}</td>
                        <td class="px-4 py-3">{{ $donation->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 text-gn-text/80">
                            @if ($donation->message)
                                {{ $donation->message }}
                            @else
                                <span class="text-gn-text/40">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gn-text/60">No donations received yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-8">{{ $donations->links() }}</div>
@endsection
