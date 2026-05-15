@props(['placeholder' => 'Search...', 'actionButton' => null, 'actionRoute' => null, 'title' => null ])

{{-- HEADER --}}
<div class="bg-white shadow px-4 py-3 flex items-center justify-between rounded-md gap-3">

    {{-- LEFT: Hamburger + Search --}}
    <div class="flex items-center gap-3 flex-1 min-w-0">

        <!-- Hamburger -->
        <label for="sidebar-toggle" class="lg:hidden cursor-pointer p-1.5 rounded-lg hover:bg-gray-100 shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5 text-gray-700">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </label>

        {{-- Desktop Search Input - sm+ pe visible --}}
        <div class="hidden sm:flex flex-1 max-w-xs lg:max-w-sm">
            <div class="relative w-full">
                <span class="absolute inset-y-0 left-3 flex items-center text-gray-400 pointer-events-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                         viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </span>
                <input
                    type="text"
                    placeholder="{{ $placeholder }}"
                    class="w-full pl-9 pr-4 py-2 text-sm border border-gray-300 rounded-lg
                           focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200
                           placeholder-gray-400">
            </div>
        </div>

        {{-- Mobile: Search Icon only --}}
        <button class="sm:hidden p-2 rounded-lg text-gray-500 hover:bg-gray-100 transition">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                 viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
            </svg>
        </button>

    </div>

    {{-- RIGHT: Icons + Button + Profile --}}
    <div class="flex items-center gap-2 sm:gap-4 shrink-0">

        <x-header.icons />

        @if($actionButton && $actionRoute)
            {{-- Desktop: full button --}}
            <a href="{{ route($actionRoute) }}"
               class="hidden sm:inline-flex items-center gap-2
                      bg-blue-600 text-white px-4 py-2 rounded-lg text-sm
                      hover:bg-blue-700 transition whitespace-nowrap">
                {{ $actionButton }}
            </a>

            {{-- Mobile: icon only --}}
            <a href="{{ route($actionRoute) }}"
               title="{{ $actionButton }}"
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
