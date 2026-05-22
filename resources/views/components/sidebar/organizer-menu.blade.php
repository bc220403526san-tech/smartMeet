<aside class="w-64 bg-white shadow-md flex flex-col h-[calc(100vh-1rem)] m-2 rounded-md">

    <!-- TOP -->
    <div class="p-5 flex-1 overflow-y-auto">

        <!-- Logo -->
        <x-logo />

        <!-- Navigation -->
        <ul class="mt-10 space-y-1">

            {{-- Dashboard --}}
            <x-sidebar.sidebar-link
                route="organizer.dashboard"
                label="Dashboard"
                :active="request()->routeIs('organizer.dashboard')">

                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z"/>
                </x-slot>

            </x-sidebar.sidebar-link>

            {{-- My Meetings --}}
            <x-sidebar.sidebar-link
                route="organizer.meetings.index"
                label="My Meetings"
                :active="request()->routeIs('organizer.meetings.index','organizer.meetings.show','organizer.meetings.edit')">

                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75"/>
                </x-slot>

            </x-sidebar.sidebar-link>

            {{-- Create Meeting --}}
            <x-sidebar.sidebar-link
                route="organizer.meetings.create"
                label="Create Meeting"
                :active="request()->routeIs('organizer.meetings.create')">

                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 4.5v15m7.5-7.5h-15"/>
                </x-slot>

            </x-sidebar.sidebar-link>

            {{-- Participants --}}
            <x-sidebar.sidebar-link
                route="organizer.participants.index"
                label="Participants"
                :active="request()->routeIs('organizer.participants.*')">

                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                </x-slot>

            </x-sidebar.sidebar-link>

            {{-- Settings --}}
            <x-sidebar.sidebar-link
                route="organizer.settings.index"
                label="Settings"
                :active="request()->routeIs('organizer.settings.index')">

                <x-slot name="icon">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                </x-slot>

            </x-sidebar.sidebar-link>

        </ul>

    </div>

    <!-- BOTTOM -->
    <div class="p-5 border-t border-gray-200 space-y-3">

        <a href="#"
           class="w-full flex items-center justify-center gap-2 text-sm font-medium bg-blue-600 text-white py-2.5 rounded-lg hover:bg-blue-700 transition">

            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z"/>
            </svg>

            Start Meeting
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-2 text-sm text-gray-600
                       hover:bg-red-50 hover:text-red-600 p-2.5 rounded-xl transition group">

                <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                     viewBox="0 0 24 24" stroke-width="1.5"
                     stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9"/>
                </svg>

                Logout

                {{-- Gray dot animation on hover --}}
                <span class="ml-auto flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                <span class="w-1 h-1 rounded-full bg-red-400 animate-bounce" style="animation-delay: 0ms"></span>
                <span class="w-1 h-1 rounded-full bg-red-400 animate-bounce" style="animation-delay: 150ms"></span>
                <span class="w-1 h-1 rounded-full bg-red-400 animate-bounce" style="animation-delay: 300ms"></span>
            </span>

            </button>
        </form>

    </div>

</aside>
