@php
    $loading = $lazy ? 'lazy' : 'eager';
    $decoding = $lazy ? 'async' : 'auto';
@endphp

@if ($webpSrc)
    <picture>
        <source srcset="{{ $webpSrc }}" type="image/webp">
        <img
            src="{{ asset($src) }}"
            alt="{{ $alt }}"
            @if ($class) class="{{ $class }}" @endif
            @if ($width) width="{{ $width }}" @endif
            @if ($height) height="{{ $height }}" @endif
            loading="{{ $loading }}"
            decoding="{{ $decoding }}"
        >
    </picture>
@else
    <img
        src="{{ asset($src) }}"
        alt="{{ $alt }}"
        @if ($class) class="{{ $class }}" @endif
        @if ($width) width="{{ $width }}" @endif
        @if ($height) height="{{ $height }}" @endif
        loading="{{ $loading }}"
        decoding="{{ $decoding }}"
    >
@endif
