@props(['placeholder' => 'Search...', 'actionButton' => null, 'actionRoute' => null])

<div class="bg-white shadow px-4 py-3 m-2 rounded-2xl flex items-center justify-between gap-3 shrink-0">
    <div class="flex items-center gap-3 flex-1 min-w-0">
        <button id="hamburger"
                class="lg:hidden p-1.5 rounded-lg text-gray-600 hover:bg-gray-100 transition shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>

        <form method="GET" action="{{ url()->current() }}" id="live-search-form" class="hidden sm:flex flex-1 max-w-sm">
            @foreach(request()->except(['search', 'page']) as $key => $value)
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endforeach

            <div class="relative w-full">
                <span id="search-icon" class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </span>

                <span id="search-spinner" class="hidden absolute inset-y-0 left-3 items-center">
                    <svg class="w-4 h-4 animate-spin text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </span>

                <input type="text"
                       id="live-search-input"
                       name="search"
                       autocomplete="off"
                       value="{{ request('search') }}"
                       placeholder="{{ $placeholder }}"
                       class="w-full pl-9 pr-9 py-2 text-sm border border-gray-200 rounded-lg
                           bg-gray-50 placeholder-gray-400
                           focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-100">

                @if(request('search'))
                    <a href="{{ url()->current() }}?{{ http_build_query(request()->except(['search', 'page'])) }}"
                       class="absolute inset-y-0 right-3 flex items-center text-gray-400 hover:text-red-500">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                             viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <x-notification-bell />
        @if($actionButton && $actionRoute)
            <a href="{{ route($actionRoute) }}"
               class="hidden sm:inline-flex items-center gap-2
                      bg-blue-600 text-white px-4 py-2 rounded-lg text-sm
                      hover:bg-blue-700 transition whitespace-nowrap">
                {{ $actionButton }}
            </a>
            <a href="{{ route($actionRoute) }}" title="{{ $actionButton }}"
               class="sm:hidden inline-flex items-center justify-center
                      bg-blue-600 text-white w-8 h-8 rounded-lg hover:bg-blue-700 transition">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                     viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                </svg>
            </a>
        @endif
        <x-header.profile />
    </div>
</div>

<script>
    (function () {
        const form    = document.getElementById('live-search-form');
        const input   = document.getElementById('live-search-input');
        const icon    = document.getElementById('search-icon');
        const spinner = document.getElementById('search-spinner');
        if (!form || !input) return;

        let debounceTimer = null;
        const DEBOUNCE_MS = 450;

        function showSpinner() {
            icon.classList.add('hidden');
            spinner.classList.remove('hidden');
            spinner.classList.add('flex');
        }

        input.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            showSpinner();
            debounceTimer = setTimeout(function () {
                form.submit();
            }, DEBOUNCE_MS);
        });

        if (input.value) {
            const val = input.value;
            input.focus();
            input.value = '';
            input.value = val;
        }
    })();
</script>
