@props([
    'name' => 'gallery_images',
    'id' => null,
    'label' => 'Gallery Images',
    'existingImages' => collect(),
    'accept' => 'image/*',
    'hint' => 'JPG, PNG, or WebP. You can select multiple files.',
    'readonly' => false,
])

@php
    $inputId = $id ?? $name;
@endphp

<div>
    <p class="block text-sm font-medium mb-1">{{ $label }}</p>

    @if ($existingImages->isNotEmpty())
        <div class="grid grid-cols-3 gap-3 mb-4">
            @foreach ($existingImages as $image)
                <div>
                    <img src="{{ asset('storage/'.$image->path) }}" alt="" class="h-24 w-full rounded border border-gray-200 object-cover">
                    @unless ($readonly)
                        <label class="mt-1 flex items-center gap-2 text-xs text-gn-text/70">
                            <input type="checkbox" name="remove_gallery[]" value="{{ $image->id }}" class="rounded border-gray-300 text-gn-accent focus:ring-gn-accent">
                            Remove
                        </label>
                    @endunless
                </div>
            @endforeach
        </div>
    @elseif ($readonly)
        <p class="mb-4 text-sm text-gn-text/60">No gallery images uploaded.</p>
    @endif

    @unless ($readonly)
        <label class="gn-image-upload" data-image-upload>
        <div class="gn-image-upload-content">
            <svg class="gn-image-upload-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M3.75 21h16.5A2.25 2.25 0 0 0 22.5 18.75V5.25A2.25 2.25 0 0 0 20.25 3H3.75A2.25 2.25 0 0 0 1.5 5.25v13.5A2.25 2.25 0 0 0 3.75 21Z" />
            </svg>
            <span class="gn-image-upload-text">
                <span class="font-semibold text-gn-accent">Choose files</span>
                <span class="text-gn-text/70"> or drag and drop</span>
            </span>
            <span class="gn-image-upload-hint">{{ $hint }}</span>
            <span class="gn-image-upload-selected hidden" data-image-upload-selected></span>
        </div>
        <input
            type="file"
            name="{{ $name }}[]"
            id="{{ $inputId }}"
            accept="{{ $accept }}"
            multiple
            class="gn-image-upload-input"
        >
    </label>
    @endunless

    @error($name)
        <p class="mt-1 text-sm text-gn-danger">{{ $message }}</p>
    @enderror
    @error('gallery_images.*')
        <p class="mt-1 text-sm text-gn-danger">{{ $message }}</p>
    @enderror
</div>
