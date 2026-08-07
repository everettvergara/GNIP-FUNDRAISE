@props(['align' => 'left'])

<div
    {{ $attributes->class([
        'mb-10',
        'text-center' => $align === 'center',
    ]) }}
>
    <h2 class="inline-flex items-center gap-2 font-bold text-gn-text text-[26.667px] leading-[1.4]">
        {{ $slot }}
        <img
            src="{{ asset('images/design/heart.png') }}"
            alt=""
            class="h-[37px] w-[37px] shrink-0"
            aria-hidden="true"
        >
    </h2>
</div>
