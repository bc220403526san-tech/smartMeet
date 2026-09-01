<x-layouts.app>

    <x-slot name="header">
        <x-header.page-title title="Participant Dashboard" />
    </x-slot>

    <x-success />
    <x-error />

    <div class="participant-meetings-responsive p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        {{-- PAGE HEADER --}}
        <div class="dashboard-header flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
            <div>
                <h1 class="page-title-main text-2xl font-bold text-gray-800">My Meetings</h1>
                <p class="page-description text-sm text-gray-500 mt-1">
                    Manage your upcoming collaborative sessions and review previous meeting history.
                </p>
            </div>
        </div>

        <!-- STATS -->
        <div class="stats-grid grid grid-cols-1 sm:grid-cols-3 gap-4">

            <div class="stat-card bg-white rounded-2xl p-5 border border-blue-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02]">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-widest">Upcoming Today</p>
                        <h2 class="text-3xl font-bold text-blue-600 mt-2" id="stat-upcoming-today">
                            {{ str_pad($upcomingToday, 2, '0', STR_PAD_LEFT) }}
                        </h2>
                    </div>
                    <div class="stat-icon-wrap w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-blue-200/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs text-blue-600 bg-blue-100 px-2 py-0.5 rounded-full font-medium">Today</span>
                    <span class="text-xs text-gray-400">scheduled meetings</span>
                </div>
            </div>

            <div class="stat-card bg-white rounded-2xl p-5 border border-amber-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02]">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-600 uppercase tracking-widest">Total Meetings</p>
                        <h2 class="text-3xl font-bold text-amber-600 mt-2" id="stat-total">
                            {{ str_pad($totalMeetings, 2, '0', STR_PAD_LEFT) }}
                        </h2>
                    </div>
                    <div class="stat-icon-wrap w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-amber-200/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-amber-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs text-amber-600 bg-amber-100 px-2 py-0.5 rounded-full font-medium">All</span>
                    <span class="text-xs text-gray-400">meetings history</span>
                </div>
            </div>

            <div class="stat-card bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02]">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest">Completed Meetings</p>
                        <h2 class="text-3xl font-bold text-emerald-600 mt-2" id="stat-completed">
                            {{ str_pad($completedMeetings, 2, '0', STR_PAD_LEFT) }}
                        </h2>
                    </div>
                    <div class="stat-icon-wrap w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-emerald-200/50">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-emerald-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs text-emerald-600 bg-emerald-100 px-2 py-0.5 rounded-full font-medium">✓ Done</span>
                    <span class="text-xs text-gray-400">completed sessions</span>
                </div>
            </div>

        </div>

        <!-- MEETINGS TABLE -->
        <div class="meetings-shell bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">

            <!-- TOP BAR -->
            <div class="meetings-topbar px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="meetings-topbar-inner flex items-center justify-between">
                    <div>
                        <h2 class="meetings-topbar-title font-semibold text-gray-800 text-lg">Meetings Overview</h2>
                        <p class="meetings-topbar-subtitle text-xs text-gray-400 mt-0.5">Track upcoming, active and completed meetings</p>
                    </div>
                    <div class="meeting-status-filters flex items-center gap-2 flex-wrap justify-end" aria-label="Filter meetings by status">
                        <button type="button" data-status-filter="all"
                                class="meeting-filter-btn is-active px-3 py-2 rounded-xl text-xs font-semibold border transition">
                            All
                        </button>
                        <button type="button" data-status-filter="upcoming"
                                class="meeting-filter-btn px-3 py-2 rounded-xl text-xs font-semibold border transition">
                            Upcoming
                        </button>
                        <button type="button" data-status-filter="active"
                                class="meeting-filter-btn px-3 py-2 rounded-xl text-xs font-semibold border transition">
                            Active
                        </button>
                        <button type="button" data-status-filter="completed"
                                class="meeting-filter-btn px-3 py-2 rounded-xl text-xs font-semibold border transition">
                            Completed
                        </button>
                        <button type="button" data-status-filter="cancelled"
                                class="meeting-filter-btn px-3 py-2 rounded-xl text-xs font-semibold border transition">
                            Cancelled
                        </button>
                    </div>
                </div>
            </div>

            <!-- TABLE HEAD -->
            <div class="meeting-head grid grid-cols-6 px-5 py-3 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                    Meeting
                </div>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <rect x="3" y="4" width="18" height="18" rx="2" />
                        <line x1="16" y1="2" x2="16" y2="6" />
                        <line x1="8" y1="2" x2="8" y2="6" />
                        <line x1="3" y1="10" x2="21" y2="10" />
                    </svg>
                    Date & Time
                </div>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Duration
                </div>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106M12 12.75a4.125 4.125 0 1 0 0-8.25 4.125 4.125 0 0 0 0 8.25Zm0 0v1.5m0-1.5h-7.5a1.5 1.5 0 0 0-1.5 1.5v.5m0 0v.57m0 0a3.75 3.75 0 0 0 2.25 3.442m-2.25-3.442a3.75 3.75 0 0 0 2.25 3.442m4.5-1.5v1.5m0 0h-1.5m1.5 0h1.5" />
                    </svg>
                    Organizer
                </div>
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3.5 h-3.5">
                        <circle cx="12" cy="12" r="10" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                    </svg>
                    Status
                </div>
                <div class="text-right">Actions</div>
            </div>

            <!-- ROWS -->
            @forelse($meetings as $meeting)
                @php
                    $organizer = $meeting->organizer;
                    $orgInitials = strtoupper(substr($organizer->name, 0, 1) . substr(strrchr($organizer->name, ' ') ?: ' ', 1, 1));
                @endphp
                <div class="meeting-row grid grid-cols-6 items-center px-5 py-4 border-b border-gray-100 hover:bg-blue-50/30 transition duration-200 group"
                     data-meeting-id="{{ $meeting->id }}"
                     data-current-status="{{ $meeting->status }}">
                    <!-- TITLE -->
                    <div class="meeting-cell" data-label="Meeting">
                        <h3 class="meeting-title-text text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition">
                            {{ $meeting->title }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">ID: SM-{{ $meeting->id }}</p>
                    </div>
                    <!-- DATE -->
                    <div class="meeting-cell" data-label="Date & Time">
                        <p class="text-sm font-medium text-gray-700">
                            {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                        </p>
                    </div>
                    <!-- DURATION -->
                    <div class="meeting-cell" data-label="Duration">
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold border border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 mr-1 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ $meeting->duration }} mins
                        </span>
                    </div>
                    <!-- ORGANIZER -->
                    <div class="meeting-cell meeting-organizer flex items-center gap-3" data-label="Organizer">
                        <div class="meeting-organizer-avatar w-9 h-9 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-xs font-bold text-blue-700 ring-2 ring-gray-100">
                            {{ $orgInitials }}
                        </div>
                        <div>
                            <p class="organizer-name-text text-sm font-medium text-gray-700">{{ $organizer->name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst($organizer->role) }}</p>
                        </div>
                    </div>
                    <!-- STATUS -->
                    <div class="meeting-cell" data-label="Status" id="status-badge-{{ $meeting->id }}">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border
                            {{ $meeting->status == 'upcoming'   ? 'bg-blue-50 text-blue-700 border-blue-200'     : '' }}
                            {{ $meeting->status == 'active'     ? 'bg-orange-50 text-orange-600 border-orange-200' : '' }}
                            {{ $meeting->status == 'completed'  ? 'bg-emerald-50 text-emerald-600 border-emerald-200'   : '' }}
                            {{ $meeting->status == 'cancelled'  ? 'bg-red-50 text-red-500 border-red-200'       : '' }}
                            {{ $meeting->status == 'flagged'    ? 'bg-yellow-50 text-yellow-600 border-yellow-200' : '' }}">
                            <span class="w-1.5 h-1.5 rounded-full
                                {{ $meeting->status == 'upcoming'   ? 'bg-blue-500'     : '' }}
                                {{ $meeting->status == 'active'     ? 'bg-orange-500 animate-pulse' : '' }}
                                {{ $meeting->status == 'completed'  ? 'bg-emerald-500'   : '' }}
                                {{ $meeting->status == 'cancelled'  ? 'bg-red-500'       : '' }}
                                {{ $meeting->status == 'flagged'    ? 'bg-yellow-500' : '' }}">
                            </span>
                            {{ strtoupper($meeting->status) }}
                        </span>
                    </div>
                    <!-- ACTIONS -->
                    <div class="meeting-cell meeting-actions flex items-center justify-end gap-2" data-label="Actions">
                        <div id="attend-col-{{ $meeting->id }}">
                            @if($meeting->status === 'active')
                                <a href="{{ route('participant.meetings.attend', $meeting) }}"
                                   class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition shadow-sm hover:shadow">
                                    <i class="fa-solid fa-video text-[11px]"></i>
                                    Attend
                                </a>
                            @elseif($meeting->status === 'upcoming')
                                <span title="Meeting hasn't started yet"
                                      class="flex items-center gap-2 bg-gray-100 text-gray-400 px-4 py-2 rounded-xl text-xs font-semibold cursor-not-allowed border border-gray-200">
                                    <i class="fa-solid fa-clock text-[11px]"></i>
                                    Upcoming
                                </span>
                            @else
                                <span class="flex items-center gap-2 bg-gray-100 text-gray-400 px-4 py-2 rounded-xl text-xs font-semibold cursor-not-allowed border border-gray-200">
                                    <i class="fa-solid fa-video text-[11px]"></i>
                                    Attend
                                </span>
                            @endif
                        </div>
                        <a href="{{ route('participant.meetings.show', $meeting) }}"
                           class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-100 text-gray-500 hover:text-blue-600 transition flex items-center justify-center shadow-sm group-hover:shadow">
                            <i class="fa-regular fa-eye text-sm"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 text-gray-400">
                    <i class="fa fa-calendar-xmark text-4xl mb-3 block"></i>
                    <p class="text-sm">No meetings found.</p>
                </div>
            @endforelse

            <!-- PAGINATION -->
            <div class="pagination-wrap flex flex-col sm:flex-row justify-between items-center gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-500">
                    Showing {{ $meetings->firstItem() ?? 0 }}–{{ $meetings->lastItem() ?? 0 }}
                    of {{ $meetings->total() }} meetings
                </p>
                {{ $meetings->links() }}
            </div>

        </div>

    </div>

</x-layouts.app>


<style>
    /* ================================================================
       SMARTMEET PARTICIPANT MEETINGS INDEX — FULL RESPONSIVE UI
       Logic / polling / routes unchanged.
    ================================================================ */

    /* Filter visibility must win over responsive display:grid !important rules. */
    .participant-meetings-responsive .meeting-row.meeting-filter-hidden {
        display: none !important;
    }

    /* Prevent any horizontal page overflow */
    html, body {
        max-width: 100%;
        overflow-x: hidden;
    }

    /* Main dashboard wrapper */
    .participant-meetings-responsive {
        width: 100%;
        min-width: 0;
    }

    /* Stats cards */
    .participant-meetings-responsive .stats-grid {
        min-width: 0;
    }

    /* Meeting card/table shell */
    .participant-meetings-responsive .meetings-shell {
        min-width: 0;
    }

    /* Desktop table */
    .participant-meetings-responsive .meeting-head,
    .participant-meetings-responsive .meeting-row {
        grid-template-columns:
        minmax(180px, 1.35fr)
        minmax(150px, 1fr)
        minmax(115px, .75fr)
        minmax(170px, 1.1fr)
        minmax(120px, .8fr)
        minmax(145px, .9fr);
        column-gap: 14px;
    }

    /* Every table cell can shrink safely */
    .participant-meetings-responsive .meeting-head > *,
    .participant-meetings-responsive .meeting-row > * {
        min-width: 0;
    }

    /* Long text protection */
    .participant-meetings-responsive .meeting-title-text,
    .participant-meetings-responsive .organizer-name-text {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Keep buttons usable */
    .participant-meetings-responsive .meeting-actions {
        min-width: 0;
    }

    /* Pagination never breaks page */
    .participant-meetings-responsive nav[role="navigation"] {
        max-width: 100%;
    }

    .participant-meetings-responsive nav[role="navigation"] > div {
        max-width: 100%;
    }

    /* ================================================================
       LAPTOP / SMALL DESKTOP
    ================================================================ */
    @media (max-width: 1180px) {
        .participant-meetings-responsive {
            padding-left: 12px !important;
            padding-right: 12px !important;
        }

        .participant-meetings-responsive .meeting-head,
        .participant-meetings-responsive .meeting-row {
            grid-template-columns:
            minmax(155px, 1.25fr)
            minmax(130px, 1fr)
            minmax(100px, .7fr)
            minmax(145px, 1fr)
            minmax(105px, .75fr)
            minmax(125px, .85fr);
            column-gap: 10px;
        }

        .participant-meetings-responsive .meeting-row {
            padding-left: 14px !important;
            padding-right: 14px !important;
        }

        .participant-meetings-responsive .meeting-head {
            padding-left: 14px !important;
            padding-right: 14px !important;
        }
    }

    /* ================================================================
       TABLET
       Convert each desktop row into a responsive card.
    ================================================================ */
    @media (max-width: 900px) {
        .participant-meetings-responsive {
            margin: 0 !important;
            padding: 14px !important;
            border-radius: 16px !important;
        }

        .participant-meetings-responsive .page-title-main {
            font-size: 1.35rem !important;
        }

        .participant-meetings-responsive .page-description {
            max-width: 680px;
            line-height: 1.55;
        }

        .participant-meetings-responsive .stats-grid {
            grid-template-columns: repeat(3, minmax(0, 1fr)) !important;
            gap: 12px !important;
        }

        .participant-meetings-responsive .stat-card {
            padding: 16px !important;
        }

        .participant-meetings-responsive .stat-card h2 {
            font-size: 1.75rem !important;
        }

        .participant-meetings-responsive .meetings-shell {
            overflow: visible !important;
            background: transparent !important;
            border: 0 !important;
            box-shadow: none !important;
        }

        .participant-meetings-responsive .meetings-topbar {
            border: 1px solid rgb(229 231 235) !important;
            border-radius: 18px !important;
            margin-bottom: 12px;
            background: linear-gradient(to right, rgb(239 246 255), rgb(238 242 255)) !important;
        }

        /* Hide desktop labels */
        .participant-meetings-responsive .meeting-head {
            display: none !important;
        }

        /* Rows become cards */
        .participant-meetings-responsive .meeting-row {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 14px !important;
            padding: 16px !important;
            margin-bottom: 12px;
            background: #fff;
            border: 1px solid rgb(229 231 235) !important;
            border-radius: 18px !important;
            box-shadow: 0 4px 14px rgba(15, 23, 42, .05);
        }

        .participant-meetings-responsive .meeting-row:hover {
            background: #fff !important;
        }

        .participant-meetings-responsive .meeting-row > div {
            min-width: 0;
        }

        /* Add mobile/tablet field labels */
        .participant-meetings-responsive .meeting-cell {
            position: relative;
            padding-top: 20px;
        }

        .participant-meetings-responsive .meeting-cell::before {
            content: attr(data-label);
            position: absolute;
            top: 0;
            left: 0;
            color: rgb(156 163 175);
            font-size: 10px;
            line-height: 1;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
        }

        .participant-meetings-responsive .meeting-actions {
            grid-column: 1 / -1;
            justify-content: flex-start !important;
            padding-top: 22px;
            flex-wrap: wrap;
        }

        .participant-meetings-responsive .meeting-actions #attend-col-placeholder,
        .participant-meetings-responsive .meeting-actions > div {
            min-width: 0;
        }

        .participant-meetings-responsive .meeting-actions a,
        .participant-meetings-responsive .meeting-actions span {
            max-width: 100%;
        }

        .participant-meetings-responsive .pagination-wrap {
            border: 1px solid rgb(229 231 235);
            border-radius: 16px;
            background: white !important;
            padding: 14px !important;
        }
    }

    /* ================================================================
       MOBILE
    ================================================================ */
    @media (max-width: 640px) {
        .participant-meetings-responsive {
            padding: 10px !important;
            min-height: calc(100dvh - 20px) !important;
        }

        .participant-meetings-responsive .dashboard-header {
            margin-bottom: 16px !important;
        }

        .participant-meetings-responsive .page-title-main {
            font-size: 1.2rem !important;
        }

        .participant-meetings-responsive .page-description {
            font-size: .78rem !important;
        }

        /* Stats stack cleanly */
        .participant-meetings-responsive .stats-grid {
            grid-template-columns: 1fr !important;
            gap: 10px !important;
        }

        .participant-meetings-responsive .stat-card {
            padding: 14px !important;
            border-radius: 16px !important;
        }

        .participant-meetings-responsive .stat-card h2 {
            font-size: 1.55rem !important;
        }

        .participant-meetings-responsive .stat-card .stat-icon-wrap {
            width: 42px !important;
            height: 42px !important;
            border-radius: 14px !important;
        }

        .participant-meetings-responsive .meetings-topbar {
            padding: 14px !important;
            border-radius: 16px !important;
        }

        .participant-meetings-responsive .meetings-topbar-inner {
            gap: 10px;
            align-items: flex-start !important;
        }

        .participant-meetings-responsive .meetings-topbar-title {
            font-size: .95rem !important;
        }

        .participant-meetings-responsive .meetings-topbar-subtitle {
            font-size: .7rem !important;
            line-height: 1.4;
        }

        /* One column meeting card */
        .participant-meetings-responsive .meeting-row {
            grid-template-columns: 1fr !important;
            gap: 12px !important;
            padding: 14px !important;
            border-radius: 16px !important;
        }

        .participant-meetings-responsive .meeting-title-text,
        .participant-meetings-responsive .organizer-name-text {
            white-space: normal;
            overflow: visible;
            text-overflow: initial;
            overflow-wrap: anywhere;
        }

        .participant-meetings-responsive .meeting-organizer {
            gap: 10px !important;
        }

        .participant-meetings-responsive .meeting-organizer-avatar {
            width: 34px !important;
            height: 34px !important;
            min-width: 34px !important;
        }

        .participant-meetings-responsive .meeting-actions {
            grid-column: auto !important;
            display: grid !important;
            grid-template-columns: 1fr auto;
            gap: 8px !important;
            width: 100%;
        }

        .participant-meetings-responsive .meeting-actions > div,
        .participant-meetings-responsive .meeting-actions > div > a,
        .participant-meetings-responsive .meeting-actions > div > span {
            width: 100%;
        }

        .participant-meetings-responsive .meeting-actions > div > a,
        .participant-meetings-responsive .meeting-actions > div > span {
            justify-content: center;
        }

        .participant-meetings-responsive .meeting-actions > a {
            width: 42px !important;
            height: 42px !important;
            min-width: 42px !important;
        }

        .participant-meetings-responsive .pagination-wrap {
            align-items: stretch !important;
            text-align: center;
        }

        .participant-meetings-responsive .pagination-wrap nav {
            width: 100%;
            overflow-x: auto;
            padding-bottom: 3px;
        }
    }

    /* ================================================================
       VERY SMALL MOBILE
    ================================================================ */
    @media (max-width: 380px) {
        .participant-meetings-responsive {
            padding-left: 7px !important;
            padding-right: 7px !important;
        }

        .participant-meetings-responsive .meeting-row {
            padding: 12px !important;
        }

        .participant-meetings-responsive .meeting-actions {
            grid-template-columns: 1fr auto;
        }
    }

    /* STATUS FILTERS */
    .participant-meetings-responsive .meeting-status-filters {
        min-width: 0;
    }

    .participant-meetings-responsive .meeting-filter-btn {
        background: #fff;
        color: rgb(75 85 99);
        border-color: rgb(229 231 235);
        white-space: nowrap;
    }

    .participant-meetings-responsive .meeting-filter-btn:hover {
        background: rgb(249 250 251);
        color: rgb(37 99 235);
        border-color: rgb(191 219 254);
    }

    .participant-meetings-responsive .meeting-filter-btn.is-active {
        background: rgb(37 99 235);
        color: #fff;
        border-color: rgb(37 99 235);
        box-shadow: 0 4px 10px rgba(37, 99, 235, .18);
    }

    @media (max-width: 900px) {
        .participant-meetings-responsive .meetings-topbar-inner {
            flex-wrap: wrap;
        }

        .participant-meetings-responsive .meeting-status-filters {
            width: 100%;
            justify-content: flex-start !important;
        }
    }

    @media (max-width: 640px) {
        .participant-meetings-responsive .meeting-status-filters {
            display: grid !important;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 7px !important;
        }

        .participant-meetings-responsive .meeting-filter-btn {
            width: 100%;
            padding: 8px 7px !important;
            font-size: 11px !important;
        }

        .participant-meetings-responsive .meeting-filter-btn:first-child {
            grid-column: 1 / -1;
        }
    }

</style>


{{-- ================================================================
     LIVE STATUS POLLING (bina refresh ke Upcoming → Attend switch)
================================================================ --}}
<script>
    /* Client-side status filters: no page refresh required. */
    (function () {
        const buttons = Array.from(document.querySelectorAll('[data-status-filter]'));

        function applyMeetingFilter(status) {
            document.querySelectorAll('[data-meeting-id]').forEach(row => {
                const current = String(row.dataset.currentStatus || '').toLowerCase();
                const shouldHide = status !== 'all' && current !== status;
                row.classList.toggle('meeting-filter-hidden', shouldHide);
            });
        }

        buttons.forEach(button => {
            button.addEventListener('click', function () {
                buttons.forEach(btn => btn.classList.remove('is-active'));
                this.classList.add('is-active');
                applyMeetingFilter(this.dataset.statusFilter || 'all');
            });
        });

        /* Expose for live polling so current filter remains correct
           when Upcoming changes to Active without refresh. */
        window.smartMeetApplyMeetingFilter = function () {
            const active = document.querySelector('[data-status-filter].is-active');
            applyMeetingFilter(active?.dataset.statusFilter || 'all');
        };
    })();
</script>

<script>
    (function () {
        const rows = Array.from(document.querySelectorAll('[data-meeting-id]'));
        if (rows.length === 0) return;

        const meetingIds = [...new Set(rows.map(el => el.dataset.meetingId))];

        // Use server time, not the device clock, for exact meeting transitions.
        let serverClockOffsetMs =
            Number(@json($serverNowMs)) - Date.now();

        let nextTransitionMs =
            @json($nextTransitionMs);

        let exactTransitionTimer = null;
        let requestRunning = false;
        let pendingRefresh = false;

        function currentServerTimeMs() {
            return Date.now() + serverClockOffsetMs;
        }

        function updateServerClock(serverNowMs) {
            const timestamp = Number(serverNowMs);

            if (Number.isFinite(timestamp)) {
                serverClockOffsetMs = timestamp - Date.now();
            }
        }

        function statusBadgeHtml(status) {
            const map = {
                upcoming:  'bg-blue-50 text-blue-700 border-blue-200',
                active:    'bg-orange-50 text-orange-600 border-orange-200',
                completed: 'bg-emerald-50 text-emerald-600 border-emerald-200',
                cancelled: 'bg-red-50 text-red-500 border-red-200',
                flagged:   'bg-yellow-50 text-yellow-600 border-yellow-200',
            };
            const dotMap = {
                upcoming:  'bg-blue-500',
                active:    'bg-orange-500 animate-pulse',
                completed: 'bg-emerald-500',
                cancelled: 'bg-red-500',
                flagged:   'bg-yellow-500',
            };
            const cls = map[status] || 'bg-gray-100 text-gray-500 border-gray-200';
            const dot = dotMap[status] || 'bg-gray-400';

            return `<span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold border ${cls}">
                <span class="w-1.5 h-1.5 rounded-full ${dot}"></span>
                ${status.toUpperCase()}
            </span>`;
        }

        function attendHtml(status, id) {
            if (status === 'active') {
                return `<a href="/participant/meetings/${id}/attend" class="flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-4 py-2 rounded-xl text-xs font-semibold transition shadow-sm hover:shadow"><i class="fa-solid fa-video text-[11px]"></i> Attend</a>`;
            }

            if (status === 'upcoming') {
                return `<span title="Meeting hasn't started yet" class="flex items-center gap-2 bg-gray-100 text-gray-400 px-4 py-2 rounded-xl text-xs font-semibold cursor-not-allowed border border-gray-200"><i class="fa-solid fa-clock text-[11px]"></i> Upcoming</span>`;
            }

            return `<span class="flex items-center gap-2 bg-gray-100 text-gray-400 px-4 py-2 rounded-xl text-xs font-semibold cursor-not-allowed border border-gray-200"><i class="fa-solid fa-video text-[11px]"></i> Attend</span>`;
        }

        function scheduleExactStatusRefresh() {
            if (exactTransitionTimer) {
                clearTimeout(exactTransitionTimer);
                exactTransitionTimer = null;
            }

            const transitionTimestamp = Number(nextTransitionMs);

            if (
                !Number.isFinite(transitionTimestamp) ||
                transitionTimestamp <= 0
            ) {
                return;
            }

            // Ask Laravel just after the exact boundary so the new status is
            // committed and returned in the same displayed second.
            const delay = Math.max(
                0,
                transitionTimestamp - currentServerTimeMs() + 50
            );

            const maximumTimeout = 2_147_000_000;

            if (delay > maximumTimeout) {
                exactTransitionTimer = setTimeout(
                    scheduleExactStatusRefresh,
                    maximumTimeout
                );
                return;
            }

            exactTransitionTimer = setTimeout(() => {
                poll('exact-meeting-time');
            }, delay);
        }

        async function poll(reason = 'manual') {
            if (requestRunning) {
                pendingRefresh = true;
                return;
            }

            requestRunning = true;

            try {
                const syncUrl = new URL(@json(route('participant.meetings.index')), window.location.origin);
                syncUrl.searchParams.set('status_sync', '1');
                syncUrl.searchParams.set('ids', meetingIds.join(','));
                syncUrl.searchParams.set('_', Date.now().toString());

                const url = syncUrl.toString();

                const res = await fetch(url, {
                    method: 'GET',
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache'
                    }
                });

                if (!res.ok) {
                    throw new Error(
                        `Participant meeting status refresh failed with HTTP ${res.status}`
                    );
                }

                const data = await res.json();

                updateServerClock(data.server_now_ms);
                nextTransitionMs = data.next_transition_ms;

                const s = data.stats || {};
                const setStat = (id, val) => {
                    const el = document.getElementById(id);
                    if (el && val !== undefined) {
                        el.textContent = String(val).padStart(2, '0');
                    }
                };

                setStat('stat-upcoming-today', s.upcomingToday);
                setStat('stat-total', s.total);
                setStat('stat-completed', s.completed);

                Object.entries(data.meetings || {}).forEach(([id, status]) => {
                    const row = document.querySelector(
                        `[data-meeting-id="${id}"]`
                    );

                    if (!row || row.dataset.currentStatus === status) {
                        return;
                    }

                    row.dataset.currentStatus = status;

                    const badge = document.getElementById(
                        'status-badge-' + id
                    );

                    if (badge) {
                        badge.innerHTML = statusBadgeHtml(status);
                    }

                    const attend = document.getElementById(
                        'attend-col-' + id
                    );

                    if (attend) {
                        attend.innerHTML = attendHtml(status, id);
                    }
                });

                if (typeof window.smartMeetApplyMeetingFilter === 'function') {
                    window.smartMeetApplyMeetingFilter();
                }

                scheduleExactStatusRefresh();
            } catch (error) {
                console.error(
                    `Participant meeting status refresh failed (${reason}):`,
                    error
                );
            } finally {
                requestRunning = false;

                if (pendingRefresh) {
                    pendingRefresh = false;
                    poll('queued-refresh');
                }
            }
        }

        // Initial sync, then exact server-time transitions.
        poll('initial-load');

        // Low-frequency safety backup only; exact transition uses setTimeout above.
        setInterval(() => {
            poll('backup-check');
        }, 30_000);

        window.addEventListener('focus', () => {
            poll('window-focus');
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                poll('tab-visible');
            }
        });

        window.addEventListener('online', () => {
            poll('network-online');
        });

        scheduleExactStatusRefresh();
    })();
</script>
