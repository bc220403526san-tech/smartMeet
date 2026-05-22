@props(['title' => null])

<!-- HEADER -->
<div class="bg-white shadow px-4 py-3 m-2 rounded-2xl flex items-center justify-between gap-3 shrink-0">

    {{-- LEFT: Hamburger + Title --}}
    <div class="flex items-center gap-3 flex-1 min-w-0">

        {{-- Hamburger — mobile only --}}
        <button id="hamburger"
                class="lg:hidden p-1.5 rounded-lg text-gray-600 hover:bg-gray-100 transition shrink-0">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none"
                 viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
            </svg>
        </button>

        {{-- Title --}}
        <h2 class="font-semibold text-blue-600 truncate">{{ $title }}</h2>

    </div>

    {{-- RIGHT: Icons + Profile --}}
    <div class="flex items-center gap-2 sm:gap-3 shrink-0">
        <x-header.icons />
        <x-header.profile />
    </div>

</div>
