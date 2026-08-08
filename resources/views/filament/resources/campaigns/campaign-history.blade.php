@php
    /** @var \App\Models\Campaign $campaign */
    $events = $campaign->events ?? collect();
    $latestSubmission = $events->firstWhere('type', \App\Models\CampaignEvent::TYPE_SUBMITTED);
@endphp

<div class="lg:sticky lg:top-6 space-y-4">
    @if ($latestSubmission?->comment)
        <div class="rounded-lg border border-amber-200 bg-amber-50 p-4 text-sm dark:border-amber-500/30 dark:bg-amber-500/10">
            <p class="font-semibold text-amber-900 dark:text-amber-200">Latest fundraiser comment</p>
            <p class="mt-1 whitespace-pre-line text-amber-800 dark:text-amber-100">{{ $latestSubmission->comment }}</p>
            <p class="mt-2 text-xs text-amber-700/80 dark:text-amber-300/70">
                Submitted {{ $latestSubmission->created_at->format('M d, Y g:i A') }}
                @if ($latestSubmission->user)
                    · {{ $latestSubmission->user->full_name ?? $latestSubmission->user->name }}
                @endif
            </p>
        </div>
    @endif

    <div>
        <h3 class="text-base font-semibold text-gray-950 dark:text-white">Campaign history</h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Submissions, reviewer feedback, approvals, and impact stories.
        </p>
    </div>

    @if ($events->isEmpty())
        <p class="text-sm text-gray-500 dark:text-gray-400">No history recorded yet.</p>
    @else
        <ol class="space-y-3">
            @foreach ($events as $event)
                <li class="rounded-lg border border-gray-200 bg-white p-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                    <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                        <span class="inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium
                            @if ($event->type === \App\Models\CampaignEvent::TYPE_SUBMITTED) bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200
                            @elseif ($event->type === \App\Models\CampaignEvent::TYPE_APPROVED) bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-200
            @elseif ($event->type === \App\Models\CampaignEvent::TYPE_REJECTED) bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-200
                            @elseif ($event->type === \App\Models\CampaignEvent::TYPE_REVOKED) bg-orange-100 text-orange-800 dark:bg-orange-500/20 dark:text-orange-200
                            @elseif ($event->type === \App\Models\CampaignEvent::TYPE_WITHDRAWN) bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300
                            @elseif ($event->type === \App\Models\CampaignEvent::TYPE_IMPACT_REPORT) bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-200
                            @else bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-300
                            @endif">
                            {{ $event->typeLabel() }}
                        </span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $event->created_at->format('M d, Y g:i A') }}</span>
                    </div>

                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">by {{ $event->actorName() }}</p>

                    @if ($event->comment)
                        <div class="mt-3 rounded-md border border-gray-100 bg-gray-50 p-3 dark:border-gray-800 dark:bg-gray-950">
                            @if ($event->commentLabel())
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $event->commentLabel() }}</p>
                            @endif
                            <p class="mt-1 whitespace-pre-line text-gray-800 dark:text-gray-200">{{ $event->comment }}</p>
                        </div>
                    @endif
                </li>
            @endforeach
        </ol>
    @endif
</div>
