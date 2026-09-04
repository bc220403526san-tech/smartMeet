<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Participant Dashboard" />
    </x-slot>

    <x-success />
    <x-error />

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        <!-- PAGE HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 text-xs text-gray-400 mb-1">
                    <span class="text-blue-600 font-medium">Today</span>
                    <span>•</span>
                    <span>{{ \Carbon\Carbon::now(config('app.timezone', 'Asia/Karachi'))->format('l, F d, Y') }}</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">Today's Meetings</h1>
                <p class="text-sm text-gray-500 mt-1">All your scheduled meetings in one clean dashboard view.</p>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 px-5 py-3.5 shadow-sm hover:shadow-md transition">
                <p class="text-xs text-gray-400 font-medium">Today</p>
                <div class="flex items-center gap-2">
                    <p class="text-2xl font-bold text-gray-800">{{ $todayMeetings->count() }}</p>
                    <p class="text-sm text-gray-500">Meetings</p>
                </div>
            </div>
        </div>

        <!-- MEETINGS LIST -->
        <div class="space-y-4">
            @forelse($todayMeetings as $meeting)
                @php
                    $organizer = $meeting->organizer;
                    $organizerName = $organizer?->name ?? 'SmartMeet';
                    $orgInitials = strtoupper(
                        substr($organizerName, 0, 1) .
                        substr(strrchr($organizerName, ' ') ?: ' ', 1, 1)
                    );

                    $timezone = config('app.timezone', 'Asia/Karachi');
                    $meetingStart = \Carbon\Carbon::parse($meeting->date . ' ' . $meeting->time, $timezone);
                    $meetingEnd = $meetingStart->copy()->addMinutes((int) $meeting->duration);

                    $startTime = $meetingStart->format('g:i A');
                    $endTime = $meetingEnd->format('g:i A');
                    $dateLabel = $meetingStart->format('M d, Y');

                    $isActive = $meeting->status === 'active';
                    $isUpcoming = $meeting->status === 'upcoming';
                    $isCompleted = $meeting->status === 'completed';
                @endphp

                <div
                    class="group bg-white rounded-2xl p-6 border shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 {{ $isActive ? 'border-l-4 border-l-red-500 border-gray-200' : 'border-l-4 border-l-blue-500 border-gray-200' }}"
                    data-today-meeting-id="{{ $meeting->id }}"
                    data-current-status="{{ $meeting->status }}"
                >
                    <div class="flex items-center justify-between mb-4 gap-3 flex-wrap">
                        <div id="today-status-{{ $meeting->id }}">
                            @if($isActive)
                                <span class="inline-flex items-center gap-2 bg-red-50 text-red-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-red-200">
                                    <span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>
                                    LIVE NOW
                                </span>
                            @elseif($isUpcoming)
                                <span class="inline-flex items-center gap-2 bg-amber-50 text-amber-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-amber-200">
                                    <span class="w-2 h-2 bg-amber-500 rounded-full"></span>
                                    UPCOMING
                                </span>
                            @elseif($isCompleted)
                                <span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-emerald-200">
                                    <span class="w-2 h-2 bg-emerald-500 rounded-full"></span>
                                    COMPLETED
                                </span>
                            @else
                                <span class="inline-flex items-center gap-2 bg-gray-100 text-gray-500 text-xs font-semibold px-3 py-1.5 rounded-full border border-gray-200">
                                    <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
                                    {{ strtoupper($meeting->status) }}
                                </span>
                            @endif
                        </div>

                        <div class="text-xs text-gray-400 font-medium flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                <circle cx="12" cy="12" r="10" />
                                <polyline points="12 6 12 12 16 14" />
                            </svg>
                            {{ $dateLabel }} • {{ $startTime }} - {{ $endTime }}
                        </div>
                    </div>

                    <h2 class="text-xl font-bold text-gray-800 group-hover:text-blue-600 transition">{{ $meeting->title }}</h2>

                    @if($meeting->description)
                        <p class="text-sm text-gray-500 mt-2 leading-relaxed">{{ $meeting->description }}</p>
                    @endif

                    <div class="flex items-center justify-between mt-5 pt-4 border-t border-gray-100 gap-4 flex-wrap">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-xs font-bold shadow-md">
                                {{ $orgInitials }}
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-gray-700">{{ $organizerName }}</p>
                                <p class="text-xs text-gray-400">{{ $organizer?->role ? ucfirst($organizer->role) : 'Organizer' }}</p>
                            </div>
                        </div>

                        <div id="today-action-{{ $meeting->id }}">
                            @if($isActive)
                                <a href="{{ route('participant.meetings.attend', $meeting) }}"
                                   class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition flex items-center gap-2">
                                    <i class="fa-solid fa-circle-play"></i>
                                    Join Live
                                </a>
                            @elseif($isUpcoming)
                                <a href="{{ route('participant.meetings.show', $meeting) }}"
                                   class="bg-gray-100 hover:bg-blue-50 text-gray-600 hover:text-blue-600 px-5 py-2.5 rounded-xl text-sm font-semibold transition border border-gray-200 hover:border-blue-200">
                                    <i class="fa-regular fa-clock mr-1.5"></i>
                                    View Details
                                </a>
                            @elseif($isCompleted)
                                <a href="{{ route('participant.meetings.show', $meeting) }}"
                                   class="bg-emerald-50 text-emerald-600 px-5 py-2.5 rounded-xl text-sm font-semibold transition border border-emerald-200">
                                    <i class="fa-solid fa-check mr-1.5"></i>
                                    View Meeting
                                </a>
                            @else
                                <button disabled class="bg-gray-100 text-gray-400 px-5 py-2.5 rounded-xl text-sm font-semibold cursor-not-allowed border border-gray-200">
                                    <i class="fa-solid fa-video-slash mr-1.5"></i>
                                    Closed
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm text-center py-20">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fa-regular fa-calendar-xmark text-3xl text-gray-400"></i>
                    </div>
                    <p class="text-lg font-semibold text-gray-600">No meetings scheduled for today</p>
                    <p class="text-sm text-gray-400 mt-1">Relax or check your full schedule later</p>
                </div>
            @endforelse
        </div>
    </div>
</x-layouts.app>

<script>
    (function () {
        const rows = Array.from(document.querySelectorAll('[data-today-meeting-id]'));
        if (!rows.length) return;

        const ids = [...new Set(rows.map(row => row.dataset.todayMeetingId))];

        // Same exact server-clock strategy used by Organizer / Participant index.
        let serverClockOffsetMs = Number(@json($serverNowMs)) - Date.now();
        let nextTransitionMs = @json($nextTransitionMs);
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

        function statusHtml(status) {
            if (status === 'active') {
                return `<span class="inline-flex items-center gap-2 bg-red-50 text-red-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-red-200"><span class="w-2 h-2 bg-red-500 rounded-full animate-pulse"></span>LIVE NOW</span>`;
            }
            if (status === 'upcoming') {
                return `<span class="inline-flex items-center gap-2 bg-amber-50 text-amber-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-amber-200"><span class="w-2 h-2 bg-amber-500 rounded-full"></span>UPCOMING</span>`;
            }
            if (status === 'completed') {
                return `<span class="inline-flex items-center gap-2 bg-emerald-50 text-emerald-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-emerald-200"><span class="w-2 h-2 bg-emerald-500 rounded-full"></span>COMPLETED</span>`;
            }
            if (status === 'ended') {
                return `<span class="inline-flex items-center gap-2 bg-slate-100 text-slate-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-slate-200"><span class="w-2 h-2 bg-slate-500 rounded-full"></span>ENDED BY ORGANIZER</span>`;
            }
            if (status === 'cancelled') {
                return `<span class="inline-flex items-center gap-2 bg-red-50 text-red-600 text-xs font-semibold px-3 py-1.5 rounded-full border border-red-200"><span class="w-2 h-2 bg-red-500 rounded-full"></span>CANCELLED BY ORGANIZER</span>`;
            }
            return `<span class="inline-flex items-center gap-2 bg-gray-100 text-gray-500 text-xs font-semibold px-3 py-1.5 rounded-full border border-gray-200"><span class="w-2 h-2 bg-gray-400 rounded-full"></span>${String(status).toUpperCase()}</span>`;
        }

        function actionHtml(status, id) {
            if (status === 'active') {
                return `<a href="/participant/meetings/${id}/attend" class="bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white px-5 py-2.5 rounded-xl text-sm font-semibold shadow-md hover:shadow-lg transition flex items-center gap-2"><i class="fa-solid fa-circle-play"></i>Join Live</a>`;
            }
            if (status === 'upcoming') {
                return `<a href="/participant/meetings/${id}" class="bg-gray-100 hover:bg-blue-50 text-gray-600 hover:text-blue-600 px-5 py-2.5 rounded-xl text-sm font-semibold transition border border-gray-200 hover:border-blue-200"><i class="fa-regular fa-clock mr-1.5"></i>View Details</a>`;
            }
            if (status === 'completed') {
                return `<a href="/participant/meetings/${id}" class="bg-emerald-50 text-emerald-600 px-5 py-2.5 rounded-xl text-sm font-semibold transition border border-emerald-200"><i class="fa-solid fa-check mr-1.5"></i>View Meeting</a>`;
            }
            return `<button disabled class="bg-gray-100 text-gray-400 px-5 py-2.5 rounded-xl text-sm font-semibold cursor-not-allowed border border-gray-200"><i class="fa-solid fa-video-slash mr-1.5"></i>Closed</button>`;
        }

        function scheduleExactStatusRefresh() {
            if (exactTransitionTimer) {
                clearTimeout(exactTransitionTimer);
                exactTransitionTimer = null;
            }

            const transitionTimestamp = Number(nextTransitionMs);
            if (!Number.isFinite(transitionTimestamp) || transitionTimestamp <= 0) return;

            const delay = Math.max(0, transitionTimestamp - currentServerTimeMs() + 50);
            const maximumTimeout = 2_147_000_000;

            if (delay > maximumTimeout) {
                exactTransitionTimer = setTimeout(scheduleExactStatusRefresh, maximumTimeout);
                return;
            }

            exactTransitionTimer = setTimeout(() => poll('exact-meeting-time'), delay);
        }

        async function poll(reason = 'manual') {
            if (requestRunning) {
                pendingRefresh = true;
                return;
            }

            requestRunning = true;

            try {
                const url = new URL(@json(route('participant.meetings.status-check')), window.location.origin);
                url.searchParams.set('ids', ids.join(','));
                url.searchParams.set('_', Date.now().toString());

                const response = await fetch(url.toString(), {
                    method: 'GET',
                    cache: 'no-store',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Cache-Control': 'no-cache'
                    }
                });

                if (!response.ok) {
                    throw new Error(`Today status sync failed with HTTP ${response.status}`);
                }

                const data = await response.json();
                updateServerClock(data.server_now_ms);
                nextTransitionMs = data.next_transition_ms;

                Object.entries(data.meetings || {}).forEach(([id, status]) => {
                    const row = document.querySelector(`[data-today-meeting-id="${id}"]`);
                    if (!row || row.dataset.currentStatus === status) return;

                    // Change the UI only when the DATABASE status actually changed.
                    row.dataset.currentStatus = status;

                    const badge = document.getElementById('today-status-' + id);
                    const action = document.getElementById('today-action-' + id);
                    if (badge) badge.innerHTML = statusHtml(status);
                    if (action) action.innerHTML = actionHtml(status, id);
                });

                scheduleExactStatusRefresh();
            } catch (error) {
                console.error(`Today meetings status sync failed (${reason}):`, error);
            } finally {
                requestRunning = false;
                if (pendingRefresh) {
                    pendingRefresh = false;
                    poll('queued-refresh');
                }
            }
        }

        poll('initial-load');
        scheduleExactStatusRefresh();

        // Safety check only. Exact Upcoming -> Active uses the scheduled timeout above.
        setInterval(() => poll('backup-check'), 30_000);
        window.addEventListener('focus', () => poll('window-focus'));
        window.addEventListener('online', () => poll('network-online'));
        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) poll('tab-visible');
        });
    })();
</script>

