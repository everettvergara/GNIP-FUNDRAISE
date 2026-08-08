@php
    /** @var \App\Models\Campaign $campaign */
    $events = $campaign->events;
@endphp

<div class="bg-white rounded-lg border border-gray-200 p-5">
    <h2 class="text-lg font-semibold mb-4">Campaign History</h2>

    @if ($events->isEmpty())
        <p class="text-sm text-gn-text/60">No activity yet. Submit your campaign for review to start the history.</p>
    @else
        <ol class="space-y-4">
            @foreach ($events as $event)
                <li class="relative pl-6 pb-4 border-l-2 border-gray-200 last:pb-0 last:border-l-0">
                    <span class="absolute -left-[9px] top-1 w-4 h-4 rounded-full border-2 border-white
                        @if ($event->type === \App\Models\CampaignEvent::TYPE_SUBMITTED) bg-amber-400
                        @elseif ($event->type === \App\Models\CampaignEvent::TYPE_APPROVED) bg-green-500
                        @elseif ($event->type === \App\Models\CampaignEvent::TYPE_REJECTED) bg-red-500
                        @elseif ($event->type === \App\Models\CampaignEvent::TYPE_REVOKED) bg-orange-500
                        @elseif ($event->type === \App\Models\CampaignEvent::TYPE_WITHDRAWN) bg-gray-400
                        @elseif ($event->type === \App\Models\CampaignEvent::TYPE_IMPACT_REPORT) bg-blue-500
                        @else bg-gray-300
                        @endif">
                    </span>

                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span class="text-sm font-medium">{{ $event->typeLabel() }}</span>
                        <span class="text-xs text-gn-text/50">{{ $event->created_at->format('M d, Y g:i A') }}</span>
                    </div>

                    <p class="text-xs text-gn-text/60 mt-0.5">by {{ $event->actorName() }}</p>

                    @if ($event->comment)
                        <p class="text-sm text-gn-text/80 mt-2 whitespace-pre-line">{{ $event->comment }}</p>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</div>
