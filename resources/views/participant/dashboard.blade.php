<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Participant Dashboard" />
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        <!-- Banner -->
        @if(session('show_welcome_banner'))
            <x-banner
                title="Welcome, {{ Auth::user()->name }}"
                desc="Create, schedule, and monitor all your meetings in one place. Keep your team aligned and your workflow organized effortlessly."
                action-route="participant.meetings.index"
                action-button="Manage Meetings"
            />
        @endif

        <div>
            <h1 class="text-2xl font-semibold">Overview</h1>
            <p class="text-gray-500 text-sm mt-1">
                Your meetings and activities at a glance.
            </p>
        </div>

        <!-- Feature Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <!-- My Meetings Card -->
            <a
                href="{{ route('participant.meetings.index') }}"
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group block"
            >
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gradient-to-br group-hover:from-blue-500 group-hover:to-indigo-600 transition-all duration-300">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6 text-blue-600 group-hover:text-white transition-all duration-300"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
                        />
                    </svg>
                </div>

                <h3 class="font-semibold text-gray-800 text-base mb-1">
                    My Meetings
                </h3>

                <p class="text-xs text-gray-500 leading-relaxed">
                    View all historical sessions and your personal recordings archive.
                </p>

                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs text-blue-600 font-medium">
                        {{ $totalMeetings }} {{ Str::plural('meeting', $totalMeetings) }}
                    </span>

                    <span class="text-xs text-gray-300">•</span>

                    <span class="text-xs text-gray-400">
                        View all →
                    </span>
                </div>
            </a>


            <!-- Today's Meetings Card -->
            <a
                href="{{ route('participant.meetings.index', ['filter' => 'today']) }}"
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group block"
            >
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gradient-to-br group-hover:from-emerald-500 group-hover:to-teal-600 transition-all duration-300">
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6 text-emerald-600 group-hover:text-white transition-all duration-300"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"
                        />
                    </svg>
                </div>

                <h3 class="font-semibold text-gray-800 text-base mb-1">
                    Today's Meetings
                </h3>

                <p class="text-xs text-gray-500 leading-relaxed">
                    Track your daily schedule and get notified before meetings start.
                </p>

                <div class="mt-3 flex items-center gap-2">

                    @if($liveMeetings > 0)

                        <span class="text-xs text-emerald-600 font-medium flex items-center gap-1">

                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>

                            {{ $liveMeetings }} live now
                        </span>

                    @else

                        <span class="text-xs text-gray-500 font-medium">
                            {{ $todayMeetings }} today
                        </span>

                    @endif

                    <span class="text-xs text-gray-300">•</span>

                    <span class="text-xs text-gray-400">
                        View schedule →
                    </span>

                </div>
            </a>


            <!-- Upcoming Meetings Card -->
            <a
                href="{{ route('participant.meetings.index', ['filter' => 'upcoming']) }}"
                class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group block"
            >
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gradient-to-br group-hover:from-purple-500 group-hover:to-violet-600 transition-all duration-300">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.5"
                        stroke="currentColor"
                        class="w-6 h-6 text-purple-600 group-hover:text-white transition-all duration-300"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"
                        />
                    </svg>

                </div>

                <h3 class="font-semibold text-gray-800 text-base mb-1">
                    Upcoming Meetings
                </h3>

                <p class="text-xs text-gray-500 leading-relaxed">
                    Stay ahead with insights into next week's confirmed sessions.
                </p>

                <div class="mt-3 flex items-center gap-2">

                    <span class="text-xs text-purple-600 font-medium">
                        {{ $upcomingMeetings }} upcoming
                    </span>

                    <span class="text-xs text-gray-300">•</span>

                    <span class="text-xs text-gray-400">
                        See all →
                    </span>

                </div>
            </a>

        </div>


        <!-- Upcoming Schedule Table -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">

            <!-- Table Header -->
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">

                <div>
                    <h2 class="font-semibold text-gray-800 text-lg">
                        Upcoming Schedule
                    </h2>

                    <p class="text-xs text-gray-400 mt-0.5">
                        Confirmed meetings for the next 48 hours
                    </p>
                </div>

            </div>


            <!-- Column Headers -->
            <div class="grid grid-cols-4 px-5 py-3 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">

                <!-- Meeting -->
                <div class="flex items-center gap-2">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-3.5 h-3.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z"
                        />
                    </svg>

                    Meeting Name

                </div>


                <!-- Host -->
                <div class="flex items-center gap-2">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-3.5 h-3.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106M12 12.75a4.125 4.125 0 1 0 0-8.25 4.125 4.125 0 0 0 0 8.25Zm0 0v1.5m0-1.5h-7.5a1.5 1.5 0 0 0-1.5 1.5v.5m0 0v.57m0 0a3.75 3.75 0 0 0 2.25 3.442m-2.25-3.442a3.75 3.75 0 0 0 2.25 3.442m4.5-1.5v1.5m0 0h-1.5m1.5 0h1.5"
                        />
                    </svg>

                    Host

                </div>


                <!-- Date -->
                <div class="flex items-center gap-2">

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="2"
                        stroke="currentColor"
                        class="w-3.5 h-3.5"
                    >
                        <rect
                            x="3"
                            y="4"
                            width="18"
                            height="18"
                            rx="2"
                        />

                        <line
                            x1="16"
                            y1="2"
                            x2="16"
                            y2="6"
                        />

                        <line
                            x1="8"
                            y1="2"
                            x2="8"
                            y2="6"
                        />

                        <line
                            x1="3"
                            y1="10"
                            x2="21"
                            y2="10"
                        />
                    </svg>

                    Date &amp; Time

                </div>


                <div class="text-right">
                    Action
                </div>

            </div>


            @forelse($schedule as $meeting)

                @php

                    $meetingDate = \Carbon\Carbon::parse($meeting->date);

                    $isLive = $meeting->status === 'active';

                    $dayLabel = $meetingDate->isToday()
                        ? 'Today'
                        : (
                            $meetingDate->isTomorrow()
                                ? 'Tomorrow'
                                : $meetingDate->format('M d, Y')
                        );

                    $startTime = $meeting->time
                        ? \Carbon\Carbon::parse($meeting->time)->format('h:i A')
                        : null;

                    $endTime = $meeting->end_time
                        ? \Carbon\Carbon::parse($meeting->end_time)->format('h:i A')
                        : null;

                    $host = $meeting->organizer;

                    /*
                     * Organizer image
                     *
                     * User model's image_url accessor handles:
                     *
                     * - avatar column
                     * - image column
                     * - Google/external URLs
                     * - Laravel storage
                     * - fallback avatar
                     */
                    $hostImage = $host
                        ? $host->image_url
                        : null;

                    /*
                     * Initials fallback
                     */
                    $hostInitials = $host
                        ? Str::of($host->name)
                            ->explode(' ')
                            ->filter()
                            ->map(fn ($word) => Str::upper(Str::substr($word, 0, 1)))
                            ->take(2)
                            ->implode('')
                        : 'SM';

                @endphp


                <div class="grid grid-cols-4 items-center px-5 py-4 border-b border-gray-100 last:border-b-0 hover:bg-blue-50/30 transition duration-200 group">

                    <!-- Meeting Name -->
                    <div class="flex items-center gap-2 min-w-0">

    <span class="relative flex h-2.5 w-2.5 flex-shrink-0">

        @if($isLive)
            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
        @endif

        <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $isLive ? 'bg-blue-600' : 'bg-gray-300' }}"></span>

    </span>

                        <span class="text-sm font-medium text-gray-800 group-hover:text-blue-600 transition truncate">
        {{ $meeting->title }}
    </span>

                    </div>


                    <!-- HOST -->
                    <div class="flex items-center gap-2 min-w-0">

                        @if($host)

                            <!-- Organizer Profile Image -->
                            <img
                                src="{{ $hostImage }}"
                                alt="{{ $host->name }}"
                                class="w-8 h-8 rounded-full object-cover ring-2 ring-gray-100 flex-shrink-0"
                                loading="lazy"
                                referrerpolicy="no-referrer"
                                onerror="
                                    this.style.display='none';
                                    const fallback = this.nextElementSibling;
                                    if (fallback) {
                                        fallback.style.display='flex';
                                    }
                                "
                            >

                            <!-- Fallback Initials -->
                            <div
                                style="display:none;"
                                class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 items-center justify-center text-xs font-bold text-blue-700 ring-2 ring-gray-100 flex-shrink-0"
                            >
                                {{ $hostInitials }}
                            </div>

                            <!-- Organizer Name -->
                            <span class="text-sm text-gray-600 hidden sm:block truncate">
                                {{ $host->name }}
                            </span>

                        @else

                            <!-- No Organizer Fallback -->
                            <div
                                class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-xs font-bold text-blue-700 ring-2 ring-gray-100 flex-shrink-0"
                            >
                                SM
                            </div>

                            <span class="text-sm text-gray-600 hidden sm:block truncate">
                                SmartMeet
                            </span>

                        @endif

                    </div>


                    <!-- Date & Time -->
                    <div>

                        <p class="text-sm text-gray-700 font-medium">
                            {{ $dayLabel }}
                        </p>

                        <p class="text-xs text-gray-400">

                            {{ $startTime }}

                            @if($endTime)
                                – {{ $endTime }}
                            @endif

                        </p>

                    </div>


                    <!-- Action -->
                    <div class="text-right">

                        <a
                            href="{{ route('participant.meetings.attend', $meeting->id) }}"
                            class="inline-block px-5 py-2 rounded-lg text-xs font-semibold transition {{ $isLive
                                ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 shadow-sm hover:shadow'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
                            }}"
                        >
                            {{ $isLive ? 'Join Session' : 'Attend Meeting' }}
                        </a>

                    </div>

                </div>

            @empty

                <div class="px-5 py-10 text-center">

                    <p class="text-sm text-gray-500">
                        No upcoming meetings in the next 48 hours.
                    </p>

                </div>

            @endforelse


            <!-- View All Link -->
            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 text-center">

                <a
                    href="{{ route('participant.meetings.index') }}"
                    class="text-xs text-blue-600 font-medium hover:text-blue-700 hover:underline transition"
                >
                    View all meetings →
                </a>

            </div>

        </div>

    </div>

</x-layouts.app>
