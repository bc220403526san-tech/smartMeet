
<x-slot name="header">
    <x-header.search-bar placeholder="Search meetings..." />
</x-slot>

<x-success />
<x-error />

<div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">
                My Meetings
            </h1>

            <p class="text-gray-400 mt-1 text-sm sm:text-base">
                Review and manage your scheduled sessions.
            </p>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            <div class="bg-white border border-blue-100 rounded-2xl px-4 py-3 shadow-sm">
                <p class="text-xs text-gray-400 mb-1">Total</p>
                <h3 id="stat-total" class="text-xl font-bold text-blue-600">
                    {{ $totalMeetings }}
                </h3>
            </div>

            <div class="bg-white border border-green-100 rounded-2xl px-4 py-3 shadow-sm">
                <p class="text-xs text-gray-400 mb-1">Active</p>
                <h3 id="stat-active" class="text-xl font-bold text-green-600">
                    {{ $activeMeetings }}
                </h3>
            </div>

            <div class="bg-white border border-yellow-100 rounded-2xl px-4 py-3 shadow-sm">
                <p class="text-xs text-gray-400 mb-1">Upcoming</p>
                <h3 id="stat-upcoming" class="text-xl font-bold text-yellow-500">
                    {{ $upcomingMeetings }}
                </h3>
            </div>

            <div class="bg-white border border-gray-100 rounded-2xl px-4 py-3 shadow-sm">
                <p class="text-xs text-gray-400 mb-1">Completed</p>
                <h3 id="stat-completed" class="text-xl font-bold text-gray-500">
                    {{ $completedMeetings }}
                </h3>
            </div>

            <div class="bg-white border border-red-100 rounded-2xl px-4 py-3 shadow-sm">
                <p class="text-xs text-gray-400 mb-1">Cancelled</p>
                <h3 id="stat-cancelled" class="text-xl font-bold text-red-600">
                    {{ $cancelledMeetings }}
                </h3>
            </div>
        </div>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="flex gap-2 items-center flex-wrap">
                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4 text-gray-400"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/>
                </svg>

                @php
                    $statuses = [
                        '' => [
                            'label' => 'All',
                            'active' => 'bg-blue-600 text-white',
                            'inactive' => 'border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600',
                        ],
                        'upcoming' => [
                            'label' => 'Upcoming',
                            'active' => 'bg-blue-600 text-white',
                            'inactive' => 'border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600',
                        ],
                        'active' => [
                            'label' => 'Active',
                            'active' => 'bg-green-600 text-white',
                            'inactive' => 'border border-gray-200 text-gray-500 hover:bg-green-50 hover:text-green-600',
                        ],
                        'completed' => [
                            'label' => 'Completed',
                            'active' => 'bg-gray-600 text-white',
                            'inactive' => 'border border-gray-200 text-gray-500 hover:bg-gray-100',
                        ],
                        'cancelled' => [
                            'label' => 'Cancelled',
                            'active' => 'bg-red-600 text-white',
                            'inactive' => 'border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-600',
                        ],
                    ];
                @endphp

                @foreach($statuses as $value => $config)
                    <a href="{{ route('organizer.meetings.index', ['status' => $value]) }}"
                       class="filter-link text-xs px-4 py-2 rounded-xl transition
                                  {{ request('status', '') == $value ? $config['active'] : $config['inactive'] }}"
                       data-status="{{ $value }}"
                       data-active-class="{{ $config['active'] }}"
                       data-inactive-class="{{ $config['inactive'] }}">
                        {{ $config['label'] }}
                    </a>
                @endforeach

                <a href="{{ route('organizer.meetings.create') }}"
                   class="text-xs px-4 py-2 rounded-xl bg-blue-600 text-white hover:bg-blue-700 transition shadow-sm">
                    + New Meeting
                </a>
            </div>

            <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 w-fit">
                <div class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></div>

                <p id="showing-text" class="text-xs text-gray-500">
                    @if($meetings->total() > 0)
                        Showing {{ $meetings->firstItem() }}–{{ $meetings->lastItem() }}
                        of {{ $meetings->total() }} meetings
                    @else
                        No meetings found
                    @endif
                </p>
            </div>
        </div>
    </div>

    <div id="pagetop"
         class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
            <div class="flex items-center justify-between flex-wrap gap-2">
                <div>
                    <h2 class="font-semibold text-gray-800 text-lg">
                        Meetings Overview
                    </h2>

                    <p class="text-xs text-gray-400 mt-0.5">
                        Track all your meetings and activities.
                    </p>
                </div>

                <div id="exact-time-indicator"
                     class="hidden items-center gap-2 rounded-xl border border-blue-100 bg-white px-3 py-2 text-xs text-blue-600 shadow-sm">
                    <span class="inline-block h-2 w-2 rounded-full bg-blue-500 animate-pulse"></span>
                    <span>Exact-time sync active</span>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm min-w-[1050px]">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-100">
                    <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                        Meeting
                    </th>

                    <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                        Date & Time
                    </th>

                    <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                        Participants
                    </th>

                    <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                        Status
                    </th>

                    <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                        Attend
                    </th>

                    <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">
                        Actions
                    </th>
                </tr>
                </thead>

                <tbody id="meetings-tbody" class="divide-y divide-gray-100">
                <x-meeting-table-rows :meetings="$meetings" />
                </tbody>
            </table>
        </div>

        <div id="pagination-wrapper"
             class="px-5 py-4 border-t border-gray-100">
            @if($meetings->hasPages())
                {{ $meetings->links() }}
            @endif
        </div>
    </div>
</div>

<x-email-invite-modal />
</x-layouts.app>

<script>
    (function () {
        const filterLinks = document.querySelectorAll('.filter-link');
        const searchInput = document.getElementById('meeting-search-input');
        const tbody = document.getElementById('meetings-tbody');
        const paginationWrapper = document.getElementById('pagination-wrapper');
        const showingText = document.getElementById('showing-text');
        const exactTimeIndicator = document.getElementById('exact-time-indicator');
        const indexUrl = @json(route('organizer.meetings.index'));

        let state = {
            status: @json(request('status', '')),
            search: @json(request('search', '')),
            page: {{ (int) request('page', 1) }}
        };

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

        function buildUrl(params) {
            const url = new URL(indexUrl, window.location.origin);

            if (params.status) {
                url.searchParams.set('status', params.status);
            }

            if (params.search) {
                url.searchParams.set('search', params.search);
            }

            if (params.page && params.page > 1) {
                url.searchParams.set('page', params.page);
            }

            return url;
        }

        function updateActiveFilterUI(status) {
            filterLinks.forEach(link => {
                const linkStatus = link.dataset.status || '';

                const activeClasses = (link.dataset.activeClass || '')
                    .split(' ')
                    .filter(Boolean);

                const inactiveClasses = (link.dataset.inactiveClass || '')
                    .split(' ')
                    .filter(Boolean);

                if (linkStatus === (status || '')) {
                    link.classList.remove(...inactiveClasses);
                    link.classList.add(...activeClasses);
                } else {
                    link.classList.remove(...activeClasses);
                    link.classList.add(...inactiveClasses);
                }
            });
        }

        function updateStats(stats) {
            document.getElementById('stat-total').textContent =
                stats.total;

            document.getElementById('stat-active').textContent =
                stats.active;

            document.getElementById('stat-upcoming').textContent =
                stats.upcoming;

            document.getElementById('stat-completed').textContent =
                stats.completed;

            document.getElementById('stat-cancelled').textContent =
                stats.cancelled;
        }

        function updateServerClock(serverNowMs) {
            const timestamp = Number(serverNowMs);

            if (Number.isFinite(timestamp)) {
                serverClockOffsetMs = timestamp - Date.now();
            }
        }

        function updateExactTimeIndicator() {
            const transition = Number(nextTransitionMs);
            const hasTransition =
                Number.isFinite(transition) && transition > 0;

            exactTimeIndicator?.classList.toggle(
                'hidden',
                !hasTransition
            );

            exactTimeIndicator?.classList.toggle(
                'flex',
                hasTransition
            );
        }

        function scheduleExactMeetingRefresh() {
            if (exactTransitionTimer) {
                clearTimeout(exactTransitionTimer);
                exactTransitionTimer = null;
            }

            updateExactTimeIndicator();

            const transitionTimestamp = Number(nextTransitionMs);

            if (
                !Number.isFinite(transitionTimestamp) ||
                transitionTimestamp <= 0
            ) {
                return;
            }

            /*
             * 50ms allowance ensures the AJAX request reaches Laravel just
             * after the scheduled boundary, while remaining in the same
             * displayed second.
             */
            const delay = Math.max(
                0,
                transitionTimestamp - currentServerTimeMs() + 50
            );

            const maximumTimeout = 2_147_000_000;

            if (delay > maximumTimeout) {
                exactTransitionTimer = setTimeout(
                    scheduleExactMeetingRefresh,
                    maximumTimeout
                );

                return;
            }

            exactTransitionTimer = setTimeout(() => {
                loadMeetings(
                    state,
                    {
                        pushState: false,
                        reason: 'exact-meeting-time'
                    }
                );
            }, delay);
        }

        async function loadMeetings(
            params,
            {
                pushState = true,
                reason = 'manual'
            } = {}
        ) {
            if (requestRunning) {
                pendingRefresh = true;
                return;
            }

            requestRunning = true;
            const url = buildUrl(params);

            try {
                const response = await fetch(url.toString(), {
                    cache: 'no-store',

                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (!response.ok) {
                    throw new Error(
                        `Meeting refresh failed with HTTP ${response.status}`
                    );
                }

                const data = await response.json();

                tbody.innerHTML = data.rows;
                paginationWrapper.innerHTML = data.pagination || '';
                showingText.textContent = data.showing;

                updateStats(data.stats);
                updateServerClock(data.server_now_ms);

                nextTransitionMs = data.next_transition_ms;
                scheduleExactMeetingRefresh();

                state = {
                    status: params.status || '',
                    search: params.search || '',
                    page: Number(params.page || 1)
                };

                updateActiveFilterUI(state.status);

                if (pushState) {
                    window.history.pushState(
                        { meetingFilter: state },
                        '',
                        url.toString()
                    );
                }
            } catch (error) {
                console.error(
                    `Failed to load meetings (${reason}):`,
                    error
                );
            } finally {
                requestRunning = false;

                if (pendingRefresh) {
                    pendingRefresh = false;

                    loadMeetings(
                        state,
                        {
                            pushState: false,
                            reason: 'queued-refresh'
                        }
                    );
                }
            }
        }

        filterLinks.forEach(link => {
            link.addEventListener('click', event => {
                event.preventDefault();

                loadMeetings({
                    status: link.dataset.status || '',
                    search: state.search,
                    page: 1
                });
            });
        });

        let searchTimer = null;

        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);

                searchTimer = setTimeout(() => {
                    loadMeetings({
                        status: state.status,
                        search: searchInput.value.trim(),
                        page: 1
                    });
                }, 350);
            });
        }

        document.addEventListener('click', event => {
            const link = event.target.closest(
                '#pagination-wrapper a'
            );

            if (!link) {
                return;
            }

            event.preventDefault();

            const href = link.getAttribute('href');

            if (!href) {
                return;
            }

            const url = new URL(
                href,
                window.location.origin
            );

            const page = Number(
                url.searchParams.get('page') || 1
            );

            loadMeetings({
                status: state.status,
                search: state.search,
                page
            });
        });

        window.addEventListener('popstate', event => {
            const params =
                event.state?.meetingFilter ||
                {
                    status: '',
                    search: '',
                    page: 1
                };

            loadMeetings(
                params,
                {
                    pushState: false,
                    reason: 'browser-history'
                }
            );
        });

        document.addEventListener('visibilitychange', () => {
            if (!document.hidden) {
                loadMeetings(
                    state,
                    {
                        pushState: false,
                        reason: 'page-visible'
                    }
                );
            }
        });

        window.addEventListener('focus', () => {
            loadMeetings(
                state,
                {
                    pushState: false,
                    reason: 'window-focus'
                }
            );
        });

        window.addEventListener('online', () => {
            loadMeetings(
                state,
                {
                    pushState: false,
                    reason: 'network-online'
                }
            );
        });

        /*
         * Backup only. Exact meeting activation is controlled by the
         * scheduled timeout above.
         */
        setInterval(() => {
            loadMeetings(
                state,
                {
                    pushState: false,
                    reason: 'backup-check'
                }
            );
        }, 30_000);

        scheduleExactMeetingRefresh();
    })();
</script>
