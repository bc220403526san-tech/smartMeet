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

            {{-- Success/Error --}}
            <x-success />
            <x-error />

            {{-- PAGE HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">My Meetings</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage your upcoming collaborative sessions and review previous meeting history.
                    </p>
                </div>
            </div>

            {{-- STATS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

                <div class="bg-white rounded-2xl p-5 border border-blue-100 shadow-sm border-l-4 border-blue-600">
                    <p class="text-xs text-gray-400 uppercase">Upcoming Today</p>
                    <h2 class="text-3xl font-bold text-blue-600 mt-2">
                        {{ str_pad($upcomingToday, 2, '0', STR_PAD_LEFT) }}
                    </h2>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm border-l-4 border-yellow-500">
                    <p class="text-xs text-gray-400 uppercase">Total Meetings</p>
                    <h2 class="text-3xl font-bold text-yellow-600 mt-2">
                        {{ str_pad($totalMeetings, 2, '0', STR_PAD_LEFT) }}
                    </h2>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm border-l-4 border-green-500">
                    <p class="text-xs text-gray-400 uppercase">Completed Meetings</p>
                    <h2 class="text-3xl font-bold text-green-600 mt-2">
                        {{ str_pad($completedMeetings, 2, '0', STR_PAD_LEFT) }}
                    </h2>
                </div>

            </div>

            {{-- MEETINGS TABLE --}}
            <div class="bg-white rounded-3xl shadow-[0_10px_40px_rgba(0,0,0,0.08)] overflow-hidden border border-gray-100">

                {{-- TOP BAR --}}
                <div class="flex items-center justify-between px-6 py-5 border-b border-gray-100 bg-white">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-800">Meetings Overview</h2>
                        <p class="text-sm text-gray-400 mt-1">Track upcoming, active and completed meetings</p>
                    </div>
                </div>

                {{-- TABLE HEAD --}}
                <div class="grid grid-cols-6 px-6 py-4 bg-gradient-to-r from-blue-50 to-gray-50
                            text-xs font-semibold uppercase tracking-wider text-gray-500 border-b border-gray-100">
                    <div>Meeting</div>
                    <div>Date & Time</div>
                    <div>Duration</div>
                    <div>Organizer</div>
                    <div>Status</div>
                    <div class="text-center">Actions</div>
                </div>

                {{-- ROWS --}}
                @forelse($meetings as $meeting)
                    @php
                        $organizer = $meeting->organizer;
                        $orgInitials = strtoupper(substr($organizer->name, 0, 1) . substr(strrchr($organizer->name, ' ') ?: ' ', 1, 1));
                    @endphp

                    <div class="grid grid-cols-6 items-center px-6 py-5 border-b border-gray-100
                                hover:bg-blue-50/40 transition duration-200">

                        {{-- TITLE --}}
                        <div class="flex items-start gap-3 ml-4">
                            <div>
                                <h3 class="text-sm font-semibold text-gray-800">
                                    {{ $meeting->title }}
                                </h3>
                                <p class="text-xs text-gray-400 mt-1">
                                    ID: SM-{{ $meeting->id }}
                                </p>
                            </div>
                        </div>

                        {{-- DATE --}}
                        <div>
                            <p class="text-sm font-medium text-gray-700">
                                {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}
                            </p>
                            <p class="text-xs text-gray-400 mt-1">
                                {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                            </p>
                        </div>

                        {{-- DURATION --}}
                        <div>
                            <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">
                                {{ $meeting->duration }} mins
                            </span>
                        </div>

                        {{-- ORGANIZER --}}
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center
                                        justify-content-center text-xs font-bold text-blue-600
                                        flex items-center justify-center">
                                {{ $orgInitials }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-700">{{ $organizer->name }}</p>
                                <p class="text-xs text-gray-400">{{ ucfirst($organizer->role) }}</p>
                            </div>
                        </div>

                        {{-- STATUS --}}
                        <div>
                            <span class="px-3 py-1 rounded-full text-xs font-semibold
                                {{ $meeting->status == 'upcoming'   ? 'bg-blue-100 text-blue-600'     : '' }}
                                {{ $meeting->status == 'active'     ? 'bg-orange-100 text-orange-600' : '' }}
                                {{ $meeting->status == 'completed'  ? 'bg-green-100 text-green-600'   : '' }}
                                {{ $meeting->status == 'cancelled'  ? 'bg-red-100 text-red-500'       : '' }}
                                {{ $meeting->status == 'flagged'    ? 'bg-yellow-100 text-yellow-600' : '' }}">
                                {{ strtoupper($meeting->status) }}
                            </span>
                        </div>

                        {{-- ACTIONS --}}
                        <div class="flex items-center justify-end gap-2">

                            {{-- Attend — sirf active ya upcoming --}}
                            @if(in_array($meeting->status, ['active', 'upcoming']))
                                <a href="{{ route('participant.meetings.attend', $meeting) }}"
                                   class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700
                                          text-white px-4 py-2 rounded-xl text-xs font-semibold
                                          transition shadow-sm">
                                    <i class="fa-solid fa-video text-[11px]"></i>
                                    Attend
                                </a>
                            @else
                                <span class="flex items-center gap-2 bg-gray-100 text-gray-400
                                             px-4 py-2 rounded-xl text-xs font-semibold cursor-not-allowed">
                                    <i class="fa-solid fa-video text-[11px]"></i>
                                    Attend
                                </span>
                            @endif

                            {{-- View Details --}}
                            <a
{{--                                href="{{ route('participant.meetings.show', $meeting) }}"--}}
                               class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-100
                                      text-gray-600 hover:text-blue-600 transition
                                      flex items-center justify-center shadow-sm">
                                <i class="fa-regular fa-eye text-sm"></i>
                            </a>

                        </div>

                    </div>
                @empty
                    <div class="text-center py-16 text-gray-400">
                        <i class="fa fa-calendar-xmark text-4xl mb-3"></i>
                        <p class="text-sm">No meetings found.</p>
                    </div>
                @endforelse

                {{-- PAGINATION --}}
                <div class="flex items-center justify-between px-6 py-4 bg-gray-50">
                    <p class="text-sm text-gray-400">
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
