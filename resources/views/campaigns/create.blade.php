@extends('layouts.campaign-user')

@section('title', 'Create Campaign')

@section('content')
    <h1 class="text-2xl font-bold mb-6">Create a Fundraise</h1>

    <form method="POST" action="{{ route('campaigns.store') }}" enctype="multipart/form-data" data-turbo="false" class="max-w-2xl space-y-6 bg-white p-6 rounded-lg border border-gray-200">
        @csrf

        <div>
            <label for="title" class="block text-sm font-medium mb-1">Campaign Title</label>
            <input type="text" name="title" id="title" value="{{ old('title') }}" required
                   class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
            @error('title')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="category_id" class="block text-sm font-medium mb-1">Category</label>
            <select name="category_id" id="category_id" class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
                <option value="">Select a category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" @selected(old('category_id') == $category->id)>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('category_id')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="description" class="block text-sm font-medium mb-1">Description</label>
            <textarea name="description" id="description" rows="8" required
                      class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">{{ old('description') }}</textarea>
            @error('description')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="goal_amount" class="block text-sm font-medium mb-1">Goal Amount (₱)</label>
            <input type="number" name="goal_amount" id="goal_amount" value="{{ old('goal_amount') }}" min="1000" step="0.01" required
                   class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
            @error('goal_amount')<p class="text-gn-danger text-sm mt-1">{{ $message }}</p>@enderror
        </div>

        <x-image-upload name="cover_image" label="Cover Image" />

        <x-gallery-upload label="Gallery Images" />

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label for="starts_at" class="block text-sm font-medium mb-1">Start Date</label>
                <input type="date" name="starts_at" id="starts_at" value="{{ old('starts_at') }}"
                       class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
            </div>
            <div>
                <label for="ends_at" class="block text-sm font-medium mb-1">End Date</label>
                <input type="date" name="ends_at" id="ends_at" value="{{ old('ends_at') }}"
                       class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">
            </div>
        </div>

        <div>
            <label for="thank_you_message" class="block text-sm font-medium mb-1">Thank You Message</label>
            <textarea name="thank_you_message" id="thank_you_message" rows="3"
                      class="w-full rounded-md border-gray-300 focus:border-gn-accent focus:ring-gn-accent">{{ old('thank_you_message') }}</textarea>
        </div>

        <button type="submit" class="px-6 py-2 bg-gn-orange text-white font-semibold rounded-md hover:opacity-90">
            Create Campaign
        </button>
    </form>
@endsection
