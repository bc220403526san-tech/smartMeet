<x-layouts.app>

    <x-slot name="header">
        <x-header.page-title title="My Meeting Invitations" />
    </x-slot>

    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4">

        <x-success />
        <x-error />

        {{-- Page Header --}}
        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">

            <div>
                <a
                    href="{{ route('admin.meetings.index') }}"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 transition"
                >
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to Manage Meetings
                </a>

                <h1 class="mt-3 text-2xl sm:text-3xl font-bold text-gray-900">
                    My Meeting Invitations
                </h1>

                <p class="mt-1 text-sm text-gray-500">
                    Meetings where you have been invited as a participant.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">

                <span
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl
                           text-sm font-semibold bg-green-50 text-green-700
                           border border-green-100"
                >
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    {{ $activeMeetings->count() }} Active
                </span>

                <span
                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl
                           text-sm font-semibold bg-blue-50 text-blue-700
                           border border-blue-100"
                >
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    {{ $upcomingMeetings->count() }} Upcoming
                </span>

            </div>

        </div>


        {{-- Active Meetings --}}
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">

                <div class="flex items-center gap-3">

                    <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center">
                        <i class="fa-solid fa-video text-sm"></i>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900">
                            Active Meetings
                        </h3>

                        <p class="text-xs text-gray-400 mt-0.5">
                            Meetings currently in progress
                        </p>
                    </div>

                </div>

                <span
                    class="inline-flex items-center justify-center min-w-8 h-8 px-2
                           rounded-xl bg-green-50 text-green-700 text-xs font-semibold"
                >
                    {{ $activeMeetings->count() }}
                </span>

            </div>


            <div class="p-5 sm:p-6">

                @if($activeMeetings->count() > 0)

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">

                        @foreach($activeMeetings as $meeting)

                            <div
                                class="rounded-2xl border border-gray-200
                                       bg-gray-50 p-4 flex flex-col
                                       sm:flex-row sm:items-center
                                       sm:justify-between gap-4"
                            >

                                <div class="flex items-center gap-3 min-w-0">

                                    <div
                                        class="w-11 h-11 rounded-xl bg-green-100
                                               text-green-600 flex items-center
                                               justify-center shrink-0"
                                    >
                                        <i class="fa-solid fa-video"></i>
                                    </div>

                                    <div class="min-w-0">

                                        <p class="font-semibold text-gray-900 truncate">
                                            {{ $meeting->title }}
                                        </p>

                                        @if($meeting->organizer)
                                            <p class="text-sm text-gray-500 truncate">
                                                Organizer:
                                                {{ $meeting->organizer->name }}
                                            </p>
                                        @endif

                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">

                                            <span class="text-xs text-gray-400">
                                                <i class="fa-regular fa-calendar mr-1"></i>
                                                {{ $meeting->date }}
                                            </span>

                                            <span class="text-xs text-gray-400">
                                                <i class="fa-regular fa-clock mr-1"></i>
                                                {{ $meeting->time }}
                                            </span>

                                            @if($meeting->duration)
                                                <span class="text-xs text-gray-400">
                                                    <i class="fa-solid fa-hourglass-half mr-1"></i>
                                                    {{ $meeting->duration }} min
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                </div>


                                <div class="flex items-center gap-2 shrink-0">

                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-2
                                               rounded-xl bg-green-50 border
                                               border-green-200 text-xs
                                               font-semibold text-green-700"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                        Active
                                    </span>

                                    <a
                                        href="{{ route('admin.meetings.show', $meeting) }}"
                                        class="inline-flex items-center gap-2 px-3 py-2
                                               rounded-xl bg-green-600 text-white
                                               text-xs font-semibold
                                               hover:bg-green-700 transition"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                        View
                                    </a>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div
                        class="p-10 text-center bg-gray-50
                               border border-dashed border-gray-200 rounded-2xl"
                    >
                        <i class="fa-solid fa-video-slash text-3xl text-gray-300"></i>

                        <p class="mt-3 text-sm font-medium text-gray-600">
                            No active meetings
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            You currently have no active invited meetings.
                        </p>
                    </div>

                @endif

            </div>

        </section>


        {{-- Upcoming Meetings --}}
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">

                <div class="flex items-center gap-3">

                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fa-regular fa-calendar-days text-sm"></i>
                    </div>

                    <div>
                        <h3 class="font-semibold text-gray-900">
                            Upcoming Meetings
                        </h3>

                        <p class="text-xs text-gray-400 mt-0.5">
                            Meetings scheduled for a future time
                        </p>
                    </div>

                </div>

                <span
                    class="inline-flex items-center justify-center min-w-8 h-8 px-2
                           rounded-xl bg-blue-50 text-blue-700 text-xs font-semibold"
                >
                    {{ $upcomingMeetings->count() }}
                </span>

            </div>


            <div class="p-5 sm:p-6">

                @if($upcomingMeetings->count() > 0)

                    <div class="grid grid-cols-1 xl:grid-cols-2 gap-3">

                        @foreach($upcomingMeetings as $meeting)

                            <div
                                class="rounded-2xl border border-gray-200
                                       bg-gray-50 p-4 flex flex-col
                                       sm:flex-row sm:items-center
                                       sm:justify-between gap-4"
                            >

                                <div class="flex items-center gap-3 min-w-0">

                                    <div
                                        class="w-11 h-11 rounded-xl bg-blue-100
                                               text-blue-600 flex items-center
                                               justify-center shrink-0"
                                    >
                                        <i class="fa-regular fa-calendar-days"></i>
                                    </div>

                                    <div class="min-w-0">

                                        <p class="font-semibold text-gray-900 truncate">
                                            {{ $meeting->title }}
                                        </p>

                                        @if($meeting->organizer)
                                            <p class="text-sm text-gray-500 truncate">
                                                Organizer:
                                                {{ $meeting->organizer->name }}
                                            </p>
                                        @endif

                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1">

                                            <span class="text-xs text-gray-400">
                                                <i class="fa-regular fa-calendar mr-1"></i>
                                                {{ $meeting->date }}
                                            </span>

                                            <span class="text-xs text-gray-400">
                                                <i class="fa-regular fa-clock mr-1"></i>
                                                {{ $meeting->time }}
                                            </span>

                                            @if($meeting->duration)
                                                <span class="text-xs text-gray-400">
                                                    <i class="fa-solid fa-hourglass-half mr-1"></i>
                                                    {{ $meeting->duration }} min
                                                </span>
                                            @endif

                                        </div>

                                    </div>

                                </div>


                                <div class="flex items-center gap-2 shrink-0">

                                    <span
                                        class="inline-flex items-center gap-1.5 px-3 py-2
                                               rounded-xl bg-blue-50 border
                                               border-blue-200 text-xs
                                               font-semibold text-blue-700"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                                        Upcoming
                                    </span>

                                    <a
                                        href="{{ route('admin.meetings.show', $meeting) }}"
                                        class="inline-flex items-center gap-2 px-3 py-2
                                               rounded-xl bg-gray-900 text-white
                                               text-xs font-semibold
                                               hover:bg-gray-800 transition"
                                    >
                                        <i class="fa-regular fa-eye"></i>
                                        View
                                    </a>

                                </div>

                            </div>

                        @endforeach

                    </div>

                @else

                    <div
                        class="p-10 text-center bg-gray-50
                               border border-dashed border-gray-200 rounded-2xl"
                    >
                        <i class="fa-regular fa-calendar-xmark text-3xl text-gray-300"></i>

                        <p class="mt-3 text-sm font-medium text-gray-600">
                            No upcoming meetings
                        </p>

                        <p class="mt-1 text-xs text-gray-400">
                            You currently have no upcoming invited meetings.
                        </p>
                    </div>

                @endif

            </div>

        </section>


        {{-- Information --}}
        <section
            class="bg-white border border-gray-200 rounded-2xl
                   shadow-sm overflow-hidden"
        >

            <div class="p-5 sm:p-6 flex items-start gap-3">

                <div
                    class="w-9 h-9 rounded-xl bg-indigo-50
                           text-indigo-600 flex items-center
                           justify-center shrink-0"
                >
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </div>

                <div>

                    <h3 class="font-semibold text-gray-900">
                        Meeting Invitations
                    </h3>

                    <p class="mt-1 text-sm text-gray-500 leading-relaxed">
                        Meetings appear here when your account is added as a
                        participant through an invitation or meeting join link.
                        Upcoming meetings automatically move to Active when
                        their scheduled start time is reached.
                    </p>

                </div>

            </div>

        </section>

    </div>

</x-layouts.app>
