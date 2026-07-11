<x-layouts.app>

    <x-slot name="header">
        <x-header.page-title title="Participant Dashboard" />
    </x-slot>

    <x-success />
    <x-error />

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

            {{-- PAGE HEADER --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">My Meetings</h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Manage your upcoming collaborative sessions and review previous meeting history.
                    </p>
                </div>
            </div>

        <!-- STATS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

            <div class="bg-white rounded-2xl p-5 border border-blue-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02]">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-blue-600 uppercase tracking-widest">Upcoming Today</p>
                        <h2 class="text-3xl font-bold text-blue-600 mt-2" id="stat-upcoming-today">
                            {{ str_pad($upcomingToday, 2, '0', STR_PAD_LEFT) }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-blue-200/50">
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

            <div class="bg-white rounded-2xl p-5 border border-amber-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02]">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-amber-600 uppercase tracking-widest">Total Meetings</p>
                        <h2 class="text-3xl font-bold text-amber-600 mt-2" id="stat-total">
                            {{ str_pad($totalMeetings, 2, '0', STR_PAD_LEFT) }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 bg-amber-500/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-amber-200/50">
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

            <div class="bg-white rounded-2xl p-5 border border-emerald-100 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-[1.02]">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-600 uppercase tracking-widest">Completed Meetings</p>
                        <h2 class="text-3xl font-bold text-emerald-600 mt-2" id="stat-completed">
                            {{ str_pad($completedMeetings, 2, '0', STR_PAD_LEFT) }}
                        </h2>
                    </div>
                    <div class="w-12 h-12 bg-emerald-500/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-emerald-200/50">
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
        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">

            <!-- TOP BAR -->
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-lg">Meetings Overview</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Track upcoming, active and completed meetings</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button class="p-2 rounded-lg bg-white border border-gray-200 hover:bg-gray-50 transition shadow-sm" title="Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- TABLE HEAD -->
            <div class="grid grid-cols-6 px-5 py-3 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
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
                <div class="grid grid-cols-6 items-center px-5 py-4 border-b border-gray-100 hover:bg-blue-50/30 transition duration-200 group"
                     data-meeting-id="{{ $meeting->id }}"
                     data-current-status="{{ $meeting->status }}">
                    <!-- TITLE -->
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 group-hover:text-blue-600 transition">
                            {{ $meeting->title }}
                        </h3>
                        <p class="text-xs text-gray-400 mt-1">ID: SM-{{ $meeting->id }}</p>
                    </div>
                    <!-- DATE -->
                    <div>
                        <p class="text-sm font-medium text-gray-700">
                            {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}
                        </p>
                        <p class="text-xs text-gray-400 mt-1">
                            {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                        </p>
                    </div>
                    <!-- DURATION -->
                    <div>
                        <span class="inline-flex items-center px-3 py-1 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold border border-gray-200">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-3 h-3 mr-1 text-gray-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                            </svg>
                            {{ $meeting->duration }} mins
                        </span>
                    </div>
                    <!-- ORGANIZER -->
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-xs font-bold text-blue-700 ring-2 ring-gray-100">
                            {{ $orgInitials }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ $organizer->name }}</p>
                            <p class="text-xs text-gray-400">{{ ucfirst($organizer->role) }}</p>
                        </div>
                    </div>
                    <!-- STATUS -->
                    <div id="status-badge-{{ $meeting->id }}">
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
                    <div class="flex items-center justify-end gap-2">
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
                        <!-- View Details -->
                        <a
{{--                            href="{{ route('participant.meetings.show', $meeting) }}"--}}
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
            <div class="flex flex-col sm:flex-row justify-between items-center gap-3 px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                <p class="text-xs text-gray-500">
                    Showing {{ $meetings->firstItem() ?? 0 }}–{{ $meetings->lastItem() ?? 0 }}
                    of {{ $meetings->total() }} meetings
                </p>
                {{ $meetings->links() }}
            </div>

        </div>

    </div>

</x-layouts.app>

{{-- ================================================================
     LIVE STATUS POLLING (bina refresh ke Upcoming → Attend switch)
================================================================ --}}
<script>
    (function () {
        const rows = Array.from(document.querySelectorAll('[data-meeting-id]'));
        if (rows.length === 0) return;

        const meetingIds = [...new Set(rows.map(el => el.dataset.meetingId))];

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

        async function poll() {
            try {
                const url = `{{ route('participant.meetings.status-check') }}?ids=${meetingIds.join(',')}`;
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                if (!res.ok) return;
                const data = await res.json();

                const s = data.stats || {};
                const setStat = (id, val) => {
                    const el = document.getElementById(id);
                    if (el && val !== undefined) el.textContent = String(val).padStart(2, '0');
                };
                setStat('stat-upcoming-today', s.upcomingToday);
                setStat('stat-total', s.total);
                setStat('stat-completed', s.completed);

                Object.entries(data.meetings || {}).forEach(([id, status]) => {
                    const row = document.querySelector(`[data-meeting-id="${id}"]`);
                    if (!row || row.dataset.currentStatus === status) return;
                    row.dataset.currentStatus = status;

                    const badge = document.getElementById('status-badge-' + id);
                    if (badge) badge.innerHTML = statusBadgeHtml(status);

                    const attend = document.getElementById('attend-col-' + id);
                    if (attend) attend.innerHTML = attendHtml(status, id);
                });
            } catch (e) {
                console.error('Participant meeting poll failed:', e);
            }
        }

        setInterval(poll, 5000);
    })();
</script>
