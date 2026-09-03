<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Organizer Profile" />
    </x-slot>

    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        <x-success />
        <x-error />

        <div>
            <a href="{{ url()->previous() }}"
               class="text-blue-600 text-sm mb-1 inline-flex items-center gap-1 hover:gap-2 transition-all font-medium">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                </svg>
                Back
            </a>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">Organizer Profile</h1>
            <p class="text-gray-400 mt-1 text-sm sm:text-base">Contact details for the organizer of your meeting.</p>
        </div>

        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden max-w-3xl mx-auto">

            <div class="relative bg-gradient-to-r from-blue-50 to-indigo-50 px-5 sm:px-8 pt-8 pb-6">
                <div class="flex flex-col items-center text-center">
                    <img id="preview-image"
                         src="{{ $organizer->image_url }}"
                         class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover shadow-md border-4 border-white">

                    <h3 class="mt-4 text-xl font-bold text-gray-800">{{ $organizer->name }}</h3>

                    <span class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full bg-gray-200 text-gray-600">
                        {{ strtoupper($organizer->role) }}
                    </span>

                    <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4 mt-3 text-sm text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <i class="fa fa-envelope text-gray-400"></i>
                            {{ $organizer->email }}
                        </span>
                        @if($organizer->email_verified_at)
                            <span class="flex items-center gap-1 text-blue-600 text-xs font-medium">
                                <i class="fa fa-check-circle"></i> Verified
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="px-5 sm:px-8 py-6">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Organizer Information</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 p-4 rounded-2xl">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-id-badge"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Account ID</p>
                            <p class="font-semibold text-gray-800 mt-0.5">#{{ $organizer->id }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 p-4 rounded-2xl">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-calendar"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Organizer Since</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $organizer->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
