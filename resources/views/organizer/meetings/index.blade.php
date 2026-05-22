<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite('resources/css/app.css')
</head>

<body class="bg-[#f4f5f7]">

<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen overflow-hidden">

    <label for="sidebar-toggle" class="sidebar-overlay"></label>
    <x-sidebar.organizer-menu />

    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0">

        <x-header.search-bar placeholder="Search Meetings..." />

        <div class="p-3 sm:p-5 bg-gray-50 rounded-lg mt-3 overflow-y-auto flex-1">

            <x-success />
            <x-error />

            {{-- ================================================================
                 TITLE + STATS
            ================================================================ --}}
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-5">

                <div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">My Meetings</h1>
                    <p class="text-gray-400 mt-1 text-sm">Review and manage your scheduled sessions.</p>
                </div>

                {{-- STATS CARDS --}}
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">

                    <div class="bg-blue-50 border border-blue-100 rounded-2xl px-4 py-3 shadow-sm">
                        <p class="text-xs text-gray-400 mb-1">Total</p>
                        <h3 class="text-xl font-bold text-blue-600">{{ $totalMeetings }}</h3>
                    </div>

                    <div class="bg-green-50 border border-green-100 rounded-2xl px-4 py-3 shadow-sm">
                        <p class="text-xs text-gray-400 mb-1">Active</p>
                        <h3 class="text-xl font-bold text-green-600">{{ $activeMeetings }}</h3>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-100 rounded-2xl px-4 py-3 shadow-sm">
                        <p class="text-xs text-gray-400 mb-1">Upcoming</p>
                        <h3 class="text-xl font-bold text-yellow-500">{{ $upcomingMeetings }}</h3>
                    </div>

                    <div class="bg-gray-50 border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
                        <p class="text-xs text-gray-400 mb-1">Completed</p>
                        <h3 class="text-xl font-bold text-gray-500">{{ $completedMeetings }}</h3>
                    </div>

                    <div class="bg-red-50 border border-red-100 rounded-2xl px-4 py-3 shadow-sm">
                        <p class="text-xs text-gray-400 mb-1">Cancelled</p>
                        <h3 class="text-xl font-bold text-red-600">{{ $cancelledMeetings }}</h3>
                    </div>

                </div>

            </div>

            {{-- ================================================================
                 FILTER + NEW MEETING
            ================================================================ --}}
            <form method="GET" action="{{ route('organizer.meetings.index') }}"
                  class="flex flex-wrap gap-2 justify-end sm:gap-3 mb-4">

                <div class="relative">
                    <select name="status" onchange="this.form.submit()"
                            class="appearance-none pl-4 pr-10 py-2 border border-gray-200 rounded-lg
                                   text-sm text-gray-600 bg-white hover:bg-gray-50
                                   focus:outline-none focus:ring-2 focus:ring-blue-100 transition cursor-pointer">
                        <option value="">All Meetings</option>
                        <option value="active"    {{ request('status') == 'active'    ? 'selected' : '' }}>Active</option>
                        <option value="upcoming"  {{ request('status') == 'upcoming'  ? 'selected' : '' }}>Upcoming</option>
                        <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
                        </svg>
                    </div>
                </div>

                <a href="{{ route('organizer.meetings.create') }}"
                   class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg text-sm
                          hover:bg-blue-700 transition whitespace-nowrap">
                    + New Meeting
                </a>

            </form>

            {{-- ================================================================
                 TABLE WRAPPER
            ================================================================ --}}
            <div class="bg-white border border-blue-500 border-l-6 border-r-6 rounded-md overflow-hidden
                        shadow-sm hover:shadow-lg transition-all duration-300 ease-in-out p-4 mt-2">

                @if($meetings->isEmpty())

                    <div class="text-center py-16 text-gray-400">
                        <i class="fa fa-calendar-xmark text-4xl mb-3"></i>
                        <p class="text-sm">No meetings found.</p>
                        <a href="{{ route('organizer.meetings.create') }}"
                           class="mt-3 inline-block text-blue-600 text-sm hover:underline">
                            + Create your first meeting
                        </a>
                    </div>

                @else

                    {{-- ============================================================
                         DESKTOP TABLE  (lg and above)
                    ============================================================ --}}
                    <div class="hidden lg:block">

                        {{-- Header --}}
                        <div class="grid grid-cols-6 text-xs text-blue-600 font-semibold mt-6 mb-6 mx-4
                                    border border-gray-200 p-4 bg-blue-50 rounded-sm
                                    shadow-[inset_0_4px_10px_rgba(0,0,0,0.2)]">
                            <div>MEETING TITLE</div>
                            <div>DATE & TIME</div>
                            <div>PARTICIPANTS</div>
                            <div>STATUS</div>
                            <div>ATTEND</div>
                            <div>ACTIONS</div>
                        </div>

                        {{-- Rows --}}
                        @foreach($meetings as $meeting)
                            <div class="grid grid-cols-6 px-6 py-4 items-center border-t hover:bg-gray-50 transition">

                                {{-- Title --}}
                                <div>
                                    <h3 class="font-semibold text-gray-800 text-sm">{{ $meeting->title }}</h3>
                                    <p class="text-xs text-gray-400">IM # M-{{ $meeting->id }}</p>
                                </div>

                                {{-- Date & Time --}}
                                <div class="text-sm text-gray-600">
                                    {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}
                                    <p class="text-xs text-gray-400">
                                        {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                                    </p>
                                </div>

                                {{-- Participants --}}
                                <div class="text-sm text-gray-600">
                                    {{ $meeting->participants->count() }} Participants
                                </div>

                                {{-- Status Badge --}}
                                <div>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs rounded-full font-medium
                                        {{ $meeting->status === 'upcoming'  ? 'bg-blue-100 text-blue-700'     : '' }}
                                        {{ $meeting->status === 'active'    ? 'bg-green-100 text-green-700'   : '' }}
                                        {{ $meeting->status === 'completed' ? 'bg-gray-100 text-gray-600'     : '' }}
                                        {{ $meeting->status === 'cancelled' ? 'bg-red-100 text-red-600'       : '' }}
                                        {{ $meeting->status === 'flagged'   ? 'bg-yellow-100 text-yellow-700' : '' }}">

                                        {{-- Dot --}}
                                        <span class="w-1.5 h-1.5 rounded-full
                                            {{ $meeting->status === 'upcoming'  ? 'bg-blue-500'   : '' }}
                                            {{ $meeting->status === 'active'    ? 'bg-green-500'  : '' }}
                                            {{ $meeting->status === 'completed' ? 'bg-gray-400'   : '' }}
                                            {{ $meeting->status === 'cancelled' ? 'bg-red-500'    : '' }}
                                            {{ $meeting->status === 'flagged'   ? 'bg-yellow-500' : '' }}">
                                        </span>

                                        {{ ucfirst($meeting->status) }}
                                    </span>
                                </div>

                                {{-- ── ATTEND COLUMN ── --}}
                                <div>
                                    @if($meeting->status === 'active')

                                        {{-- Active → green clickable button --}}
                                        <a href="{{ route('organizer.meetings.attend', $meeting->id) }}"
                                           class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                  bg-green-600 hover:bg-green-700 active:scale-95
                                                  text-white text-xs font-semibold rounded-lg
                                                  transition-all duration-150 shadow-sm">
                                            <i class="fa fa-video text-[11px]"></i>
                                            Attend
                                        </a>

                                    @elseif($meeting->status === 'upcoming')

                                        {{-- Upcoming → disabled with clock --}}
                                        <span title="Meeting hasn't started yet"
                                              class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                     bg-gray-100 text-gray-400 border border-gray-200
                                                     text-xs font-medium rounded-lg cursor-not-allowed
                                                     select-none">
                                            <i class="fa fa-clock text-[11px]"></i>
                                            Upcoming
                                        </span>

                                    @elseif($meeting->status === 'completed')

                                        {{-- Completed → disabled with check --}}
                                        <span title="This meeting has ended"
                                              class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                     bg-gray-100 text-gray-400 border border-gray-200
                                                     text-xs font-medium rounded-lg cursor-not-allowed
                                                     select-none">
                                            <i class="fa fa-circle-check text-[11px]"></i>
                                            Ended
                                        </span>

                                    @elseif($meeting->status === 'cancelled')

                                        {{-- Cancelled → disabled red-tinted --}}
                                        <span title="This meeting was cancelled"
                                              class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                     bg-red-50 text-red-300 border border-red-100
                                                     text-xs font-medium rounded-lg cursor-not-allowed
                                                     select-none">
                                            <i class="fa fa-xmark text-[11px]"></i>
                                            Cancelled
                                        </span>

                                    @else

                                        {{-- Flagged or any other status --}}
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5
                                                     bg-yellow-50 text-yellow-400 border border-yellow-100
                                                     text-xs font-medium rounded-lg cursor-not-allowed
                                                     select-none">
                                            <i class="fa fa-flag text-[11px]"></i>
                                            {{ ucfirst($meeting->status) }}
                                        </span>

                                    @endif
                                </div>

                                {{-- Actions --}}
                                <div class="flex gap-3">
                                    <x-meeting-icons :meeting="$meeting" />
                                </div>

                            </div>
                        @endforeach

                    </div>

                    {{-- ============================================================
                         MOBILE CARDS  (below lg)
                    ============================================================ --}}
                    <div class="lg:hidden divide-y divide-gray-100">

                        @foreach($meetings as $meeting)
                            <div class="p-4 hover:bg-gray-50 transition">

                                {{-- Top row: title + status badge --}}
                                <div class="flex justify-between items-start gap-2 mb-2">
                                    <div>
                                        <h3 class="font-semibold text-gray-800 text-sm">{{ $meeting->title }}</h3>
                                        <p class="text-xs text-gray-400 mt-0.5">
                                            IM # M-{{ $meeting->id }} &nbsp;·&nbsp;
                                            {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }},
                                            {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                                        </p>
                                    </div>

                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 text-xs rounded-full shrink-0 font-medium
                                        {{ $meeting->status === 'upcoming'  ? 'bg-blue-100 text-blue-700'     : '' }}
                                        {{ $meeting->status === 'active'    ? 'bg-green-100 text-green-700'   : '' }}
                                        {{ $meeting->status === 'completed' ? 'bg-gray-100 text-gray-600'     : '' }}
                                        {{ $meeting->status === 'cancelled' ? 'bg-red-100 text-red-600'       : '' }}
                                        {{ $meeting->status === 'flagged'   ? 'bg-yellow-100 text-yellow-700' : '' }}">

                                        <span class="w-1.5 h-1.5 rounded-full
                                            {{ $meeting->status === 'upcoming'  ? 'bg-blue-500'   : '' }}
                                            {{ $meeting->status === 'active'    ? 'bg-green-500'  : '' }}
                                            {{ $meeting->status === 'completed' ? 'bg-gray-400'   : '' }}
                                            {{ $meeting->status === 'cancelled' ? 'bg-red-500'    : '' }}
                                            {{ $meeting->status === 'flagged'   ? 'bg-yellow-500' : '' }}">
                                        </span>

                                        {{ ucfirst($meeting->status) }}
                                    </span>
                                </div>

                                {{-- Bottom row: participants + attend + actions --}}
                                <div class="flex justify-between items-center mt-3">

                                    <span class="text-xs text-gray-500">
                                        <i class="fa fa-users mr-1 text-gray-400"></i>
                                        {{ $meeting->participants->count() }} Participants
                                    </span>

                                    <div class="flex items-center gap-2">

                                        {{-- ── ATTEND BUTTON (mobile) ── --}}
                                        @if($meeting->status === 'active')
                                            <a href="{{ route('organizer.meetings.attend', $meeting->id) }}"
                                               class="inline-flex items-center gap-1 px-2.5 py-1.5
                                                      bg-green-600 hover:bg-green-700 active:scale-95
                                                      text-white text-xs font-semibold rounded-lg
                                                      transition-all duration-150">
                                                <i class="fa fa-video text-[10px]"></i>
                                                Attend
                                            </a>

                                        @elseif($meeting->status === 'upcoming')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1.5
                                                         bg-gray-100 text-gray-400 border border-gray-200
                                                         text-xs rounded-lg cursor-not-allowed select-none">
                                                <i class="fa fa-clock text-[10px]"></i>
                                                Upcoming
                                            </span>

                                        @elseif($meeting->status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1.5
                                                         bg-gray-100 text-gray-400 border border-gray-200
                                                         text-xs rounded-lg cursor-not-allowed select-none">
                                                <i class="fa fa-circle-check text-[10px]"></i>
                                                Ended
                                            </span>

                                        @elseif($meeting->status === 'cancelled')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-1.5
                                                         bg-red-50 text-red-300 border border-red-100
                                                         text-xs rounded-lg cursor-not-allowed select-none">
                                                <i class="fa fa-xmark text-[10px]"></i>
                                                Cancelled
                                            </span>
                                        @endif

                                        {{-- Existing action icons --}}
                                        <div class="flex gap-2">
                                            <x-meeting-icons :meeting="$meeting" />
                                        </div>

                                    </div>
                                </div>
                            </div>
                        @endforeach

                    </div>

                @endif

                {{-- ================================================================
                     PAGINATION
                ================================================================ --}}
                <div class="flex flex-col sm:flex-row justify-between items-center gap-3
                            px-4 sm:px-6 py-4 text-sm text-gray-500 border-t mt-2">
                    <p class="text-xs sm:text-sm">
                        Showing {{ $meetings->firstItem() ?? 0 }}–{{ $meetings->lastItem() ?? 0 }}
                        of {{ $meetings->total() }} meetings
                    </p>
                    {{ $meetings->links() }}
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
