@php
    // ── Windowed page numbers (max visible numbers around current page) ──
    $current   = $paginator->currentPage();
    $last      = $paginator->lastPage();
    $onEachSide = 1; // numbers to show on each side of current page

    // Always include first, last, and a window around current page
    $pages = collect([1, $last])
        ->merge(range(max(1, $current - $onEachSide), min($last, $current + $onEachSide)))
        ->unique()
        ->sort()
        ->values();
@endphp

@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between" data-pagination>

        <!-- MOBILE -->
        <div class="flex justify-between flex-1 sm:hidden">
            @if ($paginator->onFirstPage())
                <span class="px-4 py-2 text-sm text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                    Previous
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                   class="px-4 py-2 text-sm text-blue-600 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 transition"
                   data-pagination-link>
                    Previous
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                   class="px-4 py-2 text-sm text-blue-600 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 transition"
                   data-pagination-link>
                    Next
                </a>
            @else
                <span class="px-4 py-2 text-sm text-gray-400 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>

        <!-- DESKTOP -->
        <div class="hidden sm:flex sm:items-center sm:justify-between w-full">

            <!-- INFO -->
            <p class="text-sm text-gray-500">
                Showing
                <span class="font-semibold text-gray-700">{{ $paginator->firstItem() }}</span>
                to
                <span class="font-semibold text-gray-700">{{ $paginator->lastItem() }}</span>
                of
                <span class="font-semibold text-gray-700">{{ $paginator->total() }}</span>
                results
            </p>

            <!-- BUTTONS -->
            <div class="flex items-center gap-1">

                {{-- Previous --}}
                @if ($paginator->onFirstPage())
                    <span class="px-3 py-2 text-sm text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                        ←
                    </span>
                @else
                    <a href="{{ $paginator->previousPageUrl() }}"
                       class="px-3 py-2 text-sm text-blue-600 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 transition"
                       data-pagination-link>
                        ←
                    </a>
                @endif

                {{-- Windowed Page Numbers --}}
                @php $previousPage = null; @endphp
                @foreach ($pages as $page)
                    @if ($previousPage !== null && $page - $previousPage > 1)
                        <span class="px-3 py-2 text-sm text-gray-400">…</span>
                    @endif

                    @if ($page == $current)
                        <span aria-current="page"
                              class="px-3 py-2 text-sm font-semibold text-white bg-blue-600 border border-blue-600 rounded-lg">
                            {{ $page }}
                        </span>
                    @else
                        <a href="{{ $paginator->url($page) }}"
                           class="px-3 py-2 text-sm text-gray-600 bg-white border border-gray-200 rounded-lg hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition"
                           data-pagination-link>
                            {{ $page }}
                        </a>
                    @endif

                    @php $previousPage = $page; @endphp
                @endforeach

                {{-- Next --}}
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}"
                       class="px-3 py-2 text-sm text-blue-600 bg-white border border-blue-200 rounded-lg hover:bg-blue-50 transition"
                       data-pagination-link>
                        →
                    </a>
                @else
                    <span class="px-3 py-2 text-sm text-gray-300 bg-white border border-gray-200 rounded-lg cursor-not-allowed">
                        →
                    </span>
                @endif

            </div>
        </div>

    </nav>
@endif
