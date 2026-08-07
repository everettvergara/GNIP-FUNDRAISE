@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-center">
        <div class="flex flex-wrap items-center justify-center gap-1 font-sans text-sm text-gn-text">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-1.5 opacity-40 cursor-not-allowed" aria-disabled="true">
                    &lsaquo;
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-1.5 transition hover:border-gn-accent" aria-label="{{ __('pagination.previous') }}">
                    &lsaquo;
                </a>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <span class="inline-flex items-center px-2 py-1.5">{{ $element }}</span>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span aria-current="page" class="inline-flex items-center rounded-md border border-gn-accent bg-gn-accent px-3 py-1.5 text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-1.5 transition hover:border-gn-accent" aria-label="{{ __('Go to page :page', ['page' => $page]) }}">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-1.5 transition hover:border-gn-accent" aria-label="{{ __('pagination.next') }}">
                    &rsaquo;
                </a>
            @else
                <span class="inline-flex items-center rounded-md border border-gray-200 bg-white px-3 py-1.5 opacity-40 cursor-not-allowed" aria-disabled="true">
                    &rsaquo;
                </span>
            @endif
        </div>
    </nav>
@endif
