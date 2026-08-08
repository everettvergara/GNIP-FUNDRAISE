@php
    /** @var \App\Models\Campaign $record */
    $record = $entry->getRecord();
    $documentTypes = \App\Models\CampaignDocumentType::activeOrdered()->get();
    $documentsByType = $record->documents->keyBy('document_type_id');
    $missingDocuments = $record->missingRequiredDocumentTypes();
@endphp

<div class="space-y-8">
    <div class="space-y-8">
        <div class="rounded-lg border border-gray-200 bg-gray-50 p-4 text-sm text-gn-text">
        <dl class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div>
                <dt class="text-gn-text/60">Status</dt>
                <dd class="mt-1 font-medium">{{ $record->statusLabel() }}</dd>
            </div>
            <div>
                <dt class="text-gn-text/60">Slug</dt>
                <dd class="mt-1 font-medium">{{ $record->slug }}</dd>
            </div>
            <div>
                <dt class="text-gn-text/60">Owner email</dt>
                <dd class="mt-1 font-medium">{{ $record->user?->email ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gn-text/60">Submitted</dt>
                <dd class="mt-1 font-medium">{{ $record->submitted_at?->format('M d, Y g:i A') ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gn-text/60">Reviewed by</dt>
                <dd class="mt-1 font-medium">{{ $record->reviewer?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-gn-text/60">Reviewed at</dt>
                <dd class="mt-1 font-medium">{{ $record->reviewed_at?->format('M d, Y g:i A') ?? '—' }}</dd>
            </div>
        </dl>

        @if ($record->status === \App\Models\Campaign::STATUS_REJECTED && $record->rejection_reason)
            <div class="mt-4 rounded-md border border-red-200 bg-red-50 p-3 text-red-800">
                <p class="font-semibold">Current rejection reason</p>
                <p class="mt-1 whitespace-pre-line">{{ $record->rejection_reason }}</p>
            </div>
        @endif

        @if ($record->isRevoked())
            <div class="mt-4 rounded-md border border-amber-200 bg-amber-50 p-3 text-amber-900">
                <p class="font-semibold">Campaign revoked</p>
                <p class="mt-1 whitespace-pre-line">{{ $record->revocation_reason }}</p>
            </div>
        @endif
        </div>

        @include('campaigns.partials.show-content', ['campaign' => $record, 'showDonateButton' => false])

        <div>
        <h2 class="text-xl font-bold mb-4">Submitted Documents</h2>

        @if ($missingDocuments->isNotEmpty())
            <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                <p class="font-semibold">Missing required documents</p>
                <p class="mt-1">{{ $missingDocuments->pluck('name')->join(', ') }}</p>
            </div>
        @endif

        <div class="space-y-4">
            @foreach ($documentTypes as $documentType)
                @php
                    $uploadedDocument = $documentsByType->get($documentType->id);
                @endphp
                <div class="rounded-lg border border-gray-200 bg-white p-4">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-semibold text-gn-text">{{ $documentType->name }}</h3>
                                @if ($documentType->is_required)
                                    <span class="text-xs font-medium text-red-700 bg-red-50 px-2 py-0.5 rounded">Required</span>
                                @endif
                            </div>
                            @if ($documentType->description)
                                <p class="text-sm text-gn-text/70 mt-1">{{ $documentType->description }}</p>
                            @endif
                        </div>
                        @if ($uploadedDocument)
                            <span class="text-xs text-green-700 bg-green-50 px-2 py-1 rounded">Uploaded</span>
                        @endif
                    </div>

                    @if ($uploadedDocument)
                        <div class="mt-4 flex flex-wrap items-start gap-4">
                            @if ($uploadedDocument->isImage())
                                <img
                                    src="{{ $uploadedDocument->publicUrl() }}"
                                    alt="{{ $uploadedDocument->original_name }}"
                                    class="h-24 w-24 rounded-md border border-gray-200 object-cover"
                                >
                            @endif

                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-medium text-gn-text truncate">{{ $uploadedDocument->original_name }}</p>
                                <p class="text-xs text-gn-text/60 mt-1">
                                    Uploaded {{ $uploadedDocument->created_at->format('M d, Y g:i A') }}
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    <a
                                        href="{{ $uploadedDocument->publicUrl() }}"
                                        download="{{ $uploadedDocument->original_name }}"
                                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gn-text hover:bg-gray-50"
                                    >
                                        Download
                                    </a>
                                    @if ($uploadedDocument->isPreviewable())
                                        <button
                                            type="button"
                                            x-data
                                            x-on:click="$dispatch('open-document-preview', {
                                                url: @js($uploadedDocument->publicUrl()),
                                                name: @js($uploadedDocument->original_name),
                                                type: @js($uploadedDocument->isPdf() ? 'pdf' : 'image'),
                                            })"
                                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gn-text hover:bg-gray-50"
                                        >
                                            Preview
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="mt-3 text-sm text-gn-text/60">No document uploaded.</p>
                    @endif
                </div>
            @endforeach
        </div>
        </div>
    </div>

    <div>
        @include('filament.resources.campaigns.campaign-history', ['campaign' => $record])
    </div>
</div>

<div
    x-data="{
        open: false,
        url: '',
        name: '',
        type: 'image',
    }"
    x-on:open-document-preview.window="
        open = true;
        url = $event.detail.url;
        name = $event.detail.name;
        type = $event.detail.type;
    "
    x-on:keydown.escape.window="open = false"
    x-show="open"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    <div class="absolute inset-0 bg-black/60" x-on:click="open = false"></div>
    <div class="relative z-10 w-full max-w-5xl rounded-lg bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-gray-200 px-4 py-3">
            <h3 class="text-sm font-semibold text-gn-text truncate" x-text="name"></h3>
            <button type="button" class="text-sm text-gn-text/70 hover:text-gn-text" x-on:click="open = false">Close</button>
        </div>
        <div class="max-h-[80vh] overflow-auto p-4">
            <template x-if="type === 'image'">
                <img :src="url" :alt="name" class="mx-auto max-h-[70vh] w-auto rounded-md">
            </template>
            <template x-if="type === 'pdf'">
                <iframe :src="url" class="h-[70vh] w-full rounded-md border border-gray-200" title="Document preview"></iframe>
            </template>
        </div>
    </div>
</div>
