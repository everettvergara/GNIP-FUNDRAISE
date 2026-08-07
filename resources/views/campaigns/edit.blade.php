@extends('layouts.campaign-user')

@section('title', 'Edit Campaign')

@section('content')
    <div class="flex flex-wrap items-center gap-3 mb-6">
        <h1 class="text-2xl font-bold">Edit Campaign</h1>
        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
            @if ($campaign->status === \App\Models\Campaign::STATUS_DRAFT) bg-gray-100 text-gray-800
            @elseif ($campaign->status === \App\Models\Campaign::STATUS_PENDING) bg-amber-100 text-amber-800
            @elseif ($campaign->status === \App\Models\Campaign::STATUS_REJECTED) bg-red-100 text-red-800
            @elseif ($campaign->status === \App\Models\Campaign::STATUS_ACTIVE) bg-green-100 text-green-800
            @else bg-gray-100 text-gray-800
            @endif">
            {{ $campaign->statusLabel() }}
        </span>
    </div>

    @if ($campaign->status === \App\Models\Campaign::STATUS_REJECTED && $campaign->rejection_reason)
        <div class="max-w-2xl mb-6 p-4 bg-red-50 border border-red-200 rounded-lg text-sm text-red-800">
            <p class="font-semibold mb-1">This campaign was rejected</p>
            <p>{{ $campaign->rejection_reason }}</p>
        </div>
    @endif

    @if ($campaign->status === \App\Models\Campaign::STATUS_PENDING)
        <div class="max-w-2xl mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-900">
            Your campaign is pending admin review. You cannot edit it until an admin approves or rejects it.
        </div>
    @endif

    @if (! $canEdit)
        <div class="max-w-2xl space-y-6 bg-white p-6 rounded-lg border border-gray-200">
            <div>
                <h2 class="text-lg font-semibold mb-2">{{ $campaign->title }}</h2>
                <p class="text-sm text-gn-text/70">{{ $campaign->description }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-medium">Goal:</span> ₱{{ number_format($campaign->goal_amount, 2) }}
                </div>
                <div>
                    <span class="font-medium">Raised:</span> ₱{{ number_format($campaign->raised_amount, 2) }}
                </div>
            </div>
            <a href="{{ route('campaigns.show', $campaign->slug) }}" class="inline-block px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                View Public Page
            </a>
        </div>
    @else
        <form method="POST" action="{{ route('campaigns.update', $campaign->slug) }}" enctype="multipart/form-data" class="max-w-2xl space-y-6 bg-white p-6 rounded-lg border border-gray-200">
            @csrf
            @method('PUT')

            <div>
                <label for="title" class="block text-sm font-medium mb-1">Campaign Title</label>
                <input type="text" name="title" id="title" value="{{ old('title', $campaign->title) }}" required
                       class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                @error('title')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium mb-1">Category</label>
                <select name="category_id" id="category_id" class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                    <option value="">Select a category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" @selected(old('category_id', $campaign->category_id) == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="description" class="block text-sm font-medium mb-1">Description</label>
                <textarea name="description" id="description" rows="8" required
                          class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">{{ old('description', $campaign->description) }}</textarea>
                @error('description')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="goal_amount" class="block text-sm font-medium mb-1">Goal Amount (₱)</label>
                <input type="number" name="goal_amount" id="goal_amount" value="{{ old('goal_amount', $campaign->goal_amount) }}" min="1000" step="0.01" required
                       class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                @error('goal_amount')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
            </div>

            <x-image-upload
                name="cover_image"
                label="Cover Image"
                :current-image="$campaign->cover_image ? asset('storage/'.$campaign->cover_image) : null"
            />

            <x-gallery-upload
                label="Gallery Images"
                :existing-images="$campaign->media"
            />

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="starts_at" class="block text-sm font-medium mb-1">Start Date</label>
                    <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at', $campaign->starts_at?->format('Y-m-d')) }}"
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                </div>
                <div>
                    <label for="ends_at" class="block text-sm font-medium mb-1">End Date</label>
                    <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at', $campaign->ends_at?->format('Y-m-d')) }}"
                           class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                </div>
            </div>

            <div>
                <label for="thank_you_message" class="block text-sm font-medium mb-1">Thank You Message</label>
                <textarea name="thank_you_message" id="thank_you_message" rows="3"
                          class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">{{ old('thank_you_message', $campaign->thank_you_message) }}</textarea>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="px-6 py-2 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90">
                    Save Changes
                </button>
                <a href="{{ route('campaigns.show', $campaign->slug) }}" class="px-6 py-2 border border-gray-300 rounded-md hover:bg-gray-50">
                    View Public Page
                </a>
            </div>
        </form>

        <div class="max-w-2xl mt-8 space-y-4 bg-white p-6 rounded-lg border border-gray-200">
            <div>
                <h2 class="text-lg font-semibold">Required Documents</h2>
                <p class="text-sm text-gn-text/70 mt-1">Upload all required documents before submitting your campaign for admin approval.</p>
            </div>

            @if ($missingRequiredDocuments->isNotEmpty())
                <div class="p-3 bg-amber-50 border border-amber-200 rounded-md text-sm text-amber-900">
                    <p class="font-medium mb-1">Missing required documents:</p>
                    <ul class="list-disc list-inside">
                        @foreach ($missingRequiredDocuments as $missingType)
                            <li>{{ $missingType->name }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @foreach ($documentTypes as $documentType)
                @php
                    $uploadedDocument = $campaign->documents->firstWhere('document_type_id', $documentType->id);
                @endphp
                <div class="border border-gray-200 rounded-lg p-4">
                    <div class="flex flex-wrap items-start justify-between gap-2 mb-2">
                        <div>
                            <h3 class="font-medium">
                                {{ $documentType->name }}
                                @if ($documentType->is_required)
                                    <span class="text-xs text-red-600">(Required)</span>
                                @endif
                            </h3>
                            @if ($documentType->description)
                                <p class="text-sm text-gn-text/70 mt-1">{{ $documentType->description }}</p>
                            @endif
                        </div>
                        @if ($uploadedDocument)
                            <span class="text-xs text-green-700 bg-green-50 px-2 py-1 rounded">Uploaded</span>
                        @endif
                    </div>

                    @if ($uploadedDocument)
                        <div class="flex flex-wrap items-center gap-3 mb-3 text-sm">
                            <a href="{{ asset('storage/'.$uploadedDocument->path) }}" target="_blank" class="text-gn-accent hover:underline">
                                {{ $uploadedDocument->original_name }}
                            </a>
                            <form method="POST" action="{{ route('campaigns.documents.destroy', [$campaign->slug, $documentType]) }}" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Remove</button>
                            </form>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('campaigns.documents.store', [$campaign->slug, $documentType]) }}" enctype="multipart/form-data" class="flex flex-wrap items-end gap-3">
                        @csrf
                        <div class="flex-1 min-w-[200px]">
                            <input type="file" name="document" accept=".pdf,.jpg,.jpeg,.png" required
                                   class="w-full text-sm text-gn-text/80 file:mr-3 file:py-2 file:px-4 file:rounded-md file:border-0 file:bg-gray-100 file:text-sm file:font-medium">
                            @error('document')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="px-4 py-2 bg-gray-800 text-white text-sm font-medium rounded-md hover:opacity-90">
                            {{ $uploadedDocument ? 'Replace' : 'Upload' }}
                        </button>
                    </form>
                </div>
            @endforeach

            @if ($campaign->canSubmitForApproval())
                <form method="POST" action="{{ route('campaigns.submit-for-approval', $campaign->slug) }}" class="pt-2">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90">
                        Submit for Approval
                    </button>
                </form>
            @elseif ($campaign->canBeEditedByOwner())
                <p class="text-sm text-gn-text/70">
                    Upload all required documents to submit this campaign for admin approval.
                </p>
            @endif
        </div>

        @if ($campaign->canBeDeleted())
            @include('campaigns.partials.delete-campaign-form')
        @else
            <div class="max-w-2xl mt-8 p-4 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gn-text/70">
                This campaign cannot be deleted because it has received donations.
            </div>
        @endif
    @endif
@endsection
