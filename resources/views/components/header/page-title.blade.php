@props(['title' => null])

<!-- HEADER -->
<div class="bg-white shadow-sm p-4 rounded-xl flex justify-between items-center">

    <div class="flex items-center gap-3">
        <!-- Hamburger icon (mobile only) - label toggles the checkbox -->
        <label for="sidebar-toggle" class="lg:hidden cursor-pointer p-1 rounded-lg hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-gray-700">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
            </svg>
        </label>
        <h2 class="font-semibold text-blue-600">{{ $title }}</h2>
    </div>

    <div class="flex items-center gap-3 sm:gap-5">

        <div class="flex items-center gap-2">
            <x-header.icons />
            <x-header.profile />
            </div>
        </div>
    </div>

