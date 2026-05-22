<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite('resources/css/app.css')
</head>

<body class="bg-[#f4f5f7]">

<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen">

    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    {{-- SIDEBAR --}}
    <x-sidebar.participant-menu />

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0 md:ml-0">

        <x-header.page-title title="Participant Dashboard" />

        <div class="p-3 sm:p-6 bg-gray-50 overflow-y-auto rounded-lg mt-3 flex-1">

            <x-success />
            <x-error />

            {{-- PAGE HEADER --}}
            <div class="mb-6">

                <div class="flex items-start justify-between flex-wrap gap-3">

                    <div>
                        <h1 class="text-3xl font-bold text-gray-800 tracking-tight">
                            Today's Meetings
                        </h1>
                        <p class="text-sm text-gray-500 mt-1">
                            All your scheduled meetings in one clean dashboard view.
                        </p>

                        <p class="text-sm text-red-400 mt-2 font-medium">
                            <i class="fa-regular fa-calendar mr-1"></i>
                            {{ \Carbon\Carbon::today()->format('l, F d, Y') }}
                        </p>
                    </div>

                    {{-- QUICK INFO CARD --}}
                    <div class="bg-white border border-gray-100 rounded-xl px-4 py-3 shadow-sm">
                        <p class="text-xs text-gray-400">Today</p>
                        <p class="text-lg font-bold text-gray-800">
                            {{ count($todayMeetings) }} Meetings
                        </p>
                    </div>

                </div>
            </div>

            {{-- MEETINGS LIST --}}
            <div class="space-y-5">

                @forelse($todayMeetings as $meeting)
                    @php
                        $organizer   = $meeting->organizer;
                        $orgInitials = strtoupper(
                            substr($organizer->name, 0, 1) .
                            substr(strrchr($organizer->name, ' ') ?: ' ', 1, 1)
                        );

                        $startTime = \Carbon\Carbon::parse($meeting->time)->format('g:i A');
                        $endTime   = \Carbon\Carbon::parse($meeting->time)
                                        ->addMinutes($meeting->duration)
                                        ->format('g:i A');

                        $dateLabel = \Carbon\Carbon::parse($meeting->date)->format('M d, Y');

                        $isActive   = $meeting->status === 'active';
                        $isUpcoming = $meeting->status === 'upcoming';
                    @endphp

                    {{-- CARD --}}
                    <div class="relative group bg-white/80 backdrop-blur-md border
                    rounded-2xl p-6 shadow-sm hover:shadow-2xl
                    transition-all duration-300 hover:-translate-y-1 overflow-hidden
                    {{ $isActive ? 'border border-l-4 border-red-200' : 'border border-l-4 border-blue-200' }}">

                        {{-- Glow Effect --}}
                        <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition duration-500">
                            <div class="absolute -top-10 -right-10 w-40 h-40 bg-blue-200 blur-3xl rounded-full"></div>
                            <div class="absolute -bottom-10 -left-10 w-40 h-40 bg-purple-200 blur-3xl rounded-full"></div>
                        </div>

                        {{-- TOP ROW --}}
                        <div class="relative flex items-center justify-between mb-4">

                            {{-- STATUS --}}
                            @if($isActive)
                                <span class="flex items-center gap-2 bg-red-100 text-red-600
                                 text-xs font-semibold px-3 py-1 rounded-full">
                        <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                        LIVE NOW
                    </span>
                            @elseif($isUpcoming)
                                <span class="flex items-center gap-2 bg-yellow-100 text-yellow-700
                                 text-xs font-semibold px-3 py-1 rounded-full">
                        <span class="w-2 h-2 bg-yellow-500 rounded-full"></span>
                        UPCOMING
                    </span>
                            @else
                                <span class="bg-gray-100 text-gray-500 text-xs px-3 py-1 rounded-full">
                        {{ strtoupper($meeting->status) }}
                    </span>
                            @endif

                            {{-- TIME --}}
                            <div class="text-xs text-gray-400 font-medium">
                                <i class="fa-regular fa-clock mr-1"></i>
                                {{ $dateLabel }} • {{ $startTime }} - {{ $endTime }}
                            </div>
                        </div>

                        {{-- TITLE --}}
                        <h2 class="relative text-xl font-bold text-gray-800 group-hover:text-blue-700 transition">
                            {{ $meeting->title }}
                        </h2>

                        {{-- DESCRIPTION --}}
                        @if($meeting->description)
                            <p class="relative text-sm text-gray-500 mt-2 leading-relaxed">
                                {{ $meeting->description }}
                            </p>
                        @endif

                        {{-- BOTTOM ROW --}}
                        <div class="relative flex items-center justify-between mt-6">

                            {{-- ORGANIZER --}}
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-500
                                flex items-center justify-center text-white text-xs font-bold shadow">
                                    {{ $orgInitials }}
                                </div>

                                <div>
                                    <p class="text-sm font-semibold text-gray-700">
                                        {{ $organizer->name }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ ucfirst($organizer->role) }}
                                    </p>
                                </div>
                            </div>

                            {{-- ACTION --}}
                            @if($isActive)
                                <a href="{{ route('participant.meetings.attend', $meeting) }}"
                                   class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700
                              hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm
                              font-semibold shadow-md transition flex items-center gap-2">
                                    <i class="fa-solid fa-circle-play"></i>
                                    Join Live
                                </a>

                            @elseif($isUpcoming)
                                <a href="{{ route('participant.meetings.attend', $meeting) }}"
                                   class="bg-gray-100 hover:bg-blue-50 text-gray-600 hover:text-blue-600
                              px-5 py-2.5 rounded-xl text-sm font-semibold transition">
                                    <i class="fa-regular fa-clock"></i>
                                    View Details
                                </a>

                            @else
                                <button disabled
                                        class="bg-gray-100 text-gray-400 px-5 py-2.5 rounded-xl text-sm font-semibold cursor-not-allowed">
                                    <i class="fa-solid fa-video-slash"></i>
                                    Closed
                                </button>
                            @endif

                        </div>
                    </div>

                @empty

                    {{-- EMPTY STATE --}}
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm
                    text-center py-24 text-gray-400">

                        <i class="fa-regular fa-calendar-xmark text-5xl mb-4 block"></i>

                        <p class="text-base font-semibold text-gray-500">
                            No meetings scheduled for today
                        </p>

                        <p class="text-sm text-gray-400 mt-1">
                            Relax or check your full schedule later
                        </p>
                    </div>

                @endforelse

            </div>

        </div>
    </div>

</div>

</body>
</html>
