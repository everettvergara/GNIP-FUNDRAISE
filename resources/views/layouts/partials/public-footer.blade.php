@php
    $partnerLogos = [
        ['file' => 'ronald-mcdonald-house-charities.png', 'alt' => 'Ronald McDonald House Charities Philippines'],
        ['file' => 'ups-foundation.png', 'alt' => 'The UPS Foundation'],
        ['file' => 'unicef.png', 'alt' => 'UNICEF'],
        ['file' => 'unfpa-philippines.png', 'alt' => 'UNFPA Philippines'],
        ['file' => 'sm-store.png', 'alt' => 'SM Store'],
    ];
@endphp
<section class="w-full bg-[#faf6f4] py-12 md:py-16">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-[26px] md:text-[27px] font-bold text-center text-[#685b55] mb-8 md:mb-10">
            Our Partners
        </h2>
        <ul class="flex flex-wrap items-center justify-center gap-x-8 gap-y-8 md:gap-x-10 lg:gap-x-12">
            @foreach ($partnerLogos as $logo)
                <li class="flex items-center justify-center w-[42%] sm:w-[30%] md:w-auto md:max-w-[180px] lg:max-w-[200px]">
                    <img
                        src="{{ asset('images/partners/'.$logo['file']) }}"
                        alt="{{ $logo['alt'] }}"
                        class="max-h-14 md:max-h-16 w-auto max-w-full object-contain"
                        loading="lazy"
                        decoding="async"
                    >
                </li>
            @endforeach
        </ul>
    </div>
</section>

<section class="w-full overflow-hidden" aria-hidden="true">
    <x-optimized-image
        src="images/design/pre-footer-banner.png"
        alt=""
        class="block w-full h-auto min-h-[180px] md:min-h-[280px] max-h-[420px] object-cover object-center"
        :lazy="true"
    />
</section>

<footer class="bg-white border-t border-[#685b55]/10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 text-xs tracking-wide text-[#685b55]">
            <p class="text-center md:text-left">
                Good Neighbors Philippines &copy; {{ date('Y') }} All Rights Reserved
            </p>
            <nav class="flex flex-wrap items-center justify-center md:justify-end gap-x-6 gap-y-2" aria-label="Legal">
                <a href="{{ route('terms-of-use') }}" class="hover:underline">Terms of Use</a>
                <a href="{{ route('privacy-policy') }}" class="hover:underline">Data Privacy Policy</a>
                <a href="{{ route('donor-policy') }}" class="hover:underline">Donor Policy</a>
            </nav>
        </div>
    </div>
</footer>
