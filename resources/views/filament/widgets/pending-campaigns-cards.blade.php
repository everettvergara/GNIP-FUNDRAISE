<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            Campaigns Pending Approval
        </x-slot>

        <x-slot name="description">
            Review submitted campaigns and approve or reject them for publication.
        </x-slot>

        <x-slot name="headerEnd">
            <a
                href="{{ $campaignsIndexUrl }}"
                class="inline-flex items-center gap-1 text-sm font-medium text-primary-600 hover:underline"
            >
                View all campaigns
                <x-filament::icon icon="heroicon-o-arrow-top-right-on-square" class="h-4 w-4" />
            </a>
        </x-slot>

        @if ($campaigns->isEmpty())
            <div class="fi-ta-empty-state px-6 py-12 text-center">
                <p class="text-sm font-medium text-gray-950 dark:text-white">No campaigns awaiting approval</p>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    When fundraisers submit campaigns for review, they will appear here.
                </p>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                @foreach ($campaigns as $campaign)
                    <article class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-900">
                        <div class="aspect-[16/9] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
                            @if ($campaign->cover_image)
                                <img
                                    src="{{ asset('storage/'.$campaign->cover_image) }}"
                                    alt="{{ $campaign->title }}"
                                    class="h-full w-full object-cover"
                                >
                            @else
                                <div class="flex h-full w-full items-center justify-center text-sm font-medium text-gray-400">
                                    No cover image
                                </div>
                            @endif
                        </div>

                        <div class="space-y-3 p-4">
                            <div>
                                <h3 class="text-base font-semibold text-gray-950 dark:text-white">
                                    <a
                                        href="{{ \App\Filament\Resources\Campaigns\CampaignResource::getUrl('view', ['record' => $campaign]) }}"
                                        class="hover:text-primary-600"
                                    >
                                        {{ $campaign->title }}
                                    </a>
                                </h3>
                                @if ($campaign->category)
                                    <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">{{ $campaign->category->name }}</p>
                                @endif
                            </div>

                            <dl class="space-y-1 text-sm text-gray-600 dark:text-gray-300">
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500 dark:text-gray-400">Owner</dt>
                                    <dd class="text-right font-medium text-gray-950 dark:text-white">{{ $campaign->user?->full_name ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500 dark:text-gray-400">Submitted</dt>
                                    <dd class="text-right">{{ $campaign->submitted_at?->format('M d, Y g:i A') ?? '—' }}</dd>
                                </div>
                                <div class="flex justify-between gap-3">
                                    <dt class="text-gray-500 dark:text-gray-400">Goal</dt>
                                    <dd class="text-right font-medium text-gray-950 dark:text-white">₱{{ number_format($campaign->goal_amount, 0) }}</dd>
                                </div>
                            </dl>

                            @php
                                $latestSubmission = $campaign->events->firstWhere('type', \App\Models\CampaignEvent::TYPE_SUBMITTED);
                            @endphp
                            @if ($latestSubmission?->comment)
                                <div class="rounded-lg border border-amber-200 bg-amber-50 p-3 text-sm dark:border-amber-500/30 dark:bg-amber-500/10">
                                    <p class="text-xs font-semibold uppercase tracking-wide text-amber-800 dark:text-amber-200">Fundraiser comment</p>
                                    <p class="mt-1 whitespace-pre-line text-amber-900 dark:text-amber-100">{{ $latestSubmission->comment }}</p>
                                </div>
                            @endif

                            <div>
                                <p class="mb-2 text-xs font-medium uppercase tracking-wide text-gray-500 dark:text-gray-400">Documents</p>
                                @if ($campaign->documents->isEmpty())
                                    <p class="text-xs text-gray-400">No documents uploaded</p>
                                @else
                                    <div class="flex flex-wrap gap-2">
                                        @foreach ($campaign->documents as $document)
                                            <a
                                                href="{{ asset('storage/'.$document->path) }}"
                                                target="_blank"
                                                rel="noopener noreferrer"
                                                class="inline-flex items-center gap-1.5 rounded-md border border-gray-200 px-2.5 py-1.5 text-xs font-medium text-gray-700 transition hover:border-primary-300 hover:bg-primary-50 hover:text-primary-700 dark:border-gray-600 dark:text-gray-200 dark:hover:border-primary-500 dark:hover:bg-primary-500/10"
                                                title="{{ $document->original_name }}"
                                            >
                                                <x-filament::icon icon="heroicon-o-arrow-down-tray" class="h-4 w-4" />
                                                <span class="max-w-[10rem] truncate">{{ $document->documentType?->name ?? 'Document' }}</span>
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center justify-end gap-1 border-t border-gray-100 pt-3 dark:border-gray-800">
                                <a
                                    href="{{ \App\Filament\Resources\Campaigns\CampaignResource::getUrl('view', ['record' => $campaign]) }}"
                                    class="fi-icon-btn fi-size-md fi-ac-icon-btn-action flex items-center justify-center rounded-lg p-2 text-gray-500 transition hover:bg-gray-100 hover:text-gray-950 dark:text-gray-400 dark:hover:bg-gray-800 dark:hover:text-white"
                                    title="View campaign"
                                >
                                    <x-filament::icon icon="heroicon-o-eye" class="h-5 w-5" />
                                </a>

                                {{ $this->campaignActionButton('approve', $campaign->id) }}
                                {{ $this->campaignActionButton('reject', $campaign->id) }}
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </x-filament::section>

    <x-filament-actions::modals />
</x-filament-widgets::widget>
