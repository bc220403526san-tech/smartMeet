<x-layouts.app>
    <x-slot name="header">
        <x-header.search-bar placeholder="Search Participants..." />
    </x-slot>
    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">
        <!-- PAGE TITLE -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Participants</h1>
                <p class="text-sm text-gray-400 mt-0.5">Manage and monitor attendee status across all sessions.</p>
            </div>
        </div>
        <!-- STAT CARDS (dynamic) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400 tracking-widest">TOTAL INVITED</p>
                        <h2 id="stat-total" class="text-3xl font-semibold text-gray-800 mt-1.5">{{ $stats['total'] }}</h2>
                    </div>
                    <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg style="width:18px;height:18px" class="text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400 tracking-widest">ACTIVE NOW</p>
                        <h2 class="text-3xl font-semibold text-gray-800 mt-1.5 flex items-center gap-2">
                            <span id="stat-active">{{ $stats['activeNow'] }}</span>
                            @if($stats['activeNow'] > 0)
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                                </span>
                            @endif
                        </h2>
                    </div>
                    <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg style="width:18px;height:18px" class="text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                </div>
            </div>
            <!-- REPLACED: Avg. Engagement -> Pending Invites -->
            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-xs font-medium text-gray-400 tracking-widest">PENDING INVITES</p>
                        <h2 id="stat-pending" class="text-3xl font-semibold text-gray-800 mt-1.5">{{ $stats['pending'] }}</h2>
                    </div>
                    <div class="w-10 h-10 bg-purple-50 rounded-xl flex items-center justify-center shrink-0">
                        <svg style="width:18px;height:18px" class="text-purple-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9 3.75h.008v.008H12v-.008Z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <!-- ══ TABLE WRAPPER ══ -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-lg">Participants Overview</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Manage and monitor all participant activities.</p>
                    </div>
                    <!-- SEARCH -->
                    <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-64">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400 shrink-0" fill="none"
                             viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                        </svg>
                        <input type="text" id="participant-search-input"
                               placeholder="Search by name or email..."
                               class="w-full bg-transparent text-sm focus:outline-none placeholder:text-gray-400">
                    </div>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[900px]">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">NAME</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">EMAIL ADDRESS</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">STATUS</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">LAST ACTIVE</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">ACTIONS</th>
                    </tr>
                    </thead>
                    <tbody id="participants-tbody" class="divide-y divide-gray-100">
                    <x-participant-table-rows :participants="$participants" />
                    </tbody>
                </table>
            </div>
            <!-- PAGINATION -->
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p id="showing-text" class="text-xs text-gray-500">
                    @if($participants->total() > 0)
                        Showing {{ $participants->firstItem() }}–{{ $participants->lastItem() }} of {{ $participants->total() }} participants
                    @else
                        No participants found
                    @endif
                </p>
                <div id="pagination-wrapper">
                    @if($participants->hasPages())
                        {{ $participants->links() }}
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- TOAST (simple feedback for delete) -->
    <div id="participant-toast" class="hidden fixed bottom-5 right-5 z-50 px-4 py-3 rounded-xl shadow-lg text-sm font-medium"></div>
</x-layouts.app>
<script>
    (function () {
        const searchInput       = document.getElementById('participant-search-input');
        const tbody             = document.getElementById('participants-tbody');
        const paginationWrapper = document.getElementById('pagination-wrapper');
        const showingText       = document.getElementById('showing-text');
        const indexUrl          = "{{ route('organizer.participants.index') }}";
        const csrfToken         = "{{ csrf_token() }}";
        const toast          = document.getElementById('participant-toast');
        let state = { search: "{{ request('search', '') }}", page: {{ (int) request('page', 1) }} };

        function buildUrl(params) {
            const url = new URL(indexUrl, window.location.origin);
            if (params.search) url.searchParams.set('search', params.search);
            if (params.page && params.page > 1) url.searchParams.set('page', params.page);
            return url;
        }

        function updateStats(stats) {
            document.getElementById('stat-total').textContent   = stats.total;
            document.getElementById('stat-active').textContent  = stats.activeNow;
            document.getElementById('stat-pending').textContent = stats.pending;
        }

        function showToast(message, isError = false) {
            toast.textContent = message;
            toast.className = 'fixed bottom-5 right-5 z-50 px-4 py-3 rounded-xl shadow-lg text-sm font-medium ' +
                (isError ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100');
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 2500);
        }

        async function loadParticipants(params, { pushState = true } = {}) {
            const url = buildUrl(params);
            try {
                const res = await fetch(url.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!res.ok) return;
                const data = await res.json();
                tbody.innerHTML             = data.rows;
                paginationWrapper.innerHTML = data.pagination || '';
                showingText.textContent     = data.showing;
                updateStats(data.stats);
                state = params;
                if (pushState) window.history.pushState({ participantFilter: params }, '', url.toString());
            } catch (e) {
                console.error('Failed to load participants:', e);
            }
        }

        let searchTimer;
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                clearTimeout(searchTimer);
                searchTimer = setTimeout(() => {
                    loadParticipants({ search: searchInput.value.trim(), page: 1 });
                }, 400);
            });
        }

        document.addEventListener('click', (e) => {
            const link = e.target.closest('#pagination-wrapper a');
            if (!link) return;
            e.preventDefault();
            const href = link.getAttribute('href');
            if (!href) return;
            const url  = new URL(href, window.location.origin);
            const page = parseInt(url.searchParams.get('page') || '1', 10);
            loadParticipants({ search: state.search, page });
        });

        window.addEventListener('popstate', (e) => {
            const params = (e.state && e.state.participantFilter) ? e.state.participantFilter : { search: '', page: 1 };
            loadParticipants(params, { pushState: false });
        });

        // ── DELETE PARTICIPANT ──
        async function handleDelete(id, name, row) {
            const confirmed = window.confirm(`Remove "${name}" from your meetings? This cannot be undone.`);
            if (!confirmed) return;
            try {
                const res = await fetch(`/organizer/participants/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                    }
                });
                const data = await res.json();
                if (!res.ok) {
                    showToast(data.message || 'Could not remove participant.', true);
                    return;
                }
                row.remove();
                updateStats(data.stats);
                showToast(data.message || 'Participant removed.');
                if (!tbody.querySelector('tr')) {
                    loadParticipants(state, { pushState: false });
                }
            } catch (e) {
                showToast('Something went wrong.', true);
            }
        }

        // View button ab ek normal <a> link hai (show page ki taraf),
        // isliye ab yahan sirf delete handle karna hai.
        document.addEventListener('click', (e) => {
            const deleteBtn = e.target.closest('.delete-participant-btn');
            if (deleteBtn) {
                const row = deleteBtn.closest('tr');
                handleDelete(deleteBtn.dataset.id, deleteBtn.dataset.name, row);
            }
        });

        // ── REAL-TIME (Laravel Reverb) ──
        const organizerId = {{ auth()->id() }};
        if (window.Echo) {
            window.Echo.private(`organizer.${organizerId}`)
                .listen('.participant.updated', () => {
                    loadParticipants(state, { pushState: false });
                });
        } else {
            console.warn('Echo not initialized — real-time updates disabled, falling back to polling.');
        }

        // Safety-net fallback (har 30s)
        setInterval(() => {
            loadParticipants(state, { pushState: false });
        }, 30000);
    })();
</script>
