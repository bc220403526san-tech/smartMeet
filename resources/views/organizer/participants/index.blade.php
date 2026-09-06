<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Organizer Dashboard" />
    </x-slot>
    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-6 overflow-y-auto min-h-screen">
        <!-- PAGE TITLE -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                    <i class="fa-solid fa-users text-sm"></i>
                </div>
                <div>
                    <h1 class="text-xl font-bold text-gray-900 sm:text-2xl">Participants</h1>
                    <p class="mt-0.5 text-sm text-gray-500">Manage and monitor attendee status across all sessions.</p>
                </div>
            </div>

            <div class="relative w-full sm:w-80">
                <i class="fa-solid fa-magnifying-glass pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 text-xs text-gray-400"></i>
                <input type="text"
                       id="participant-search-input"
                       value="{{ request('search', '') }}"
                       placeholder="Search by name or email..."
                       class="h-10 w-full rounded-xl border border-gray-200 bg-white pl-10 pr-4 text-sm text-gray-700 shadow-sm outline-none transition placeholder:text-gray-400 hover:border-blue-300 focus:border-blue-400 focus:ring-4 focus:ring-blue-50">
            </div>
        </div>
        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-blue-50"></div>
                <div class="relative flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-400">Total Invited</p>
                        <div class="mt-2 flex items-end gap-2">
                            <h2 id="stat-total" class="text-3xl font-bold leading-none text-gray-900">{{ $stats['total'] }}</h2>
                            <span class="pb-0.5 text-xs font-medium text-gray-400">participants</span>
                        </div>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600 ring-1 ring-blue-100">
                        <i class="fa-solid fa-user-group text-base"></i>
                    </div>
                </div>
                <div class="relative mt-5 h-1.5 overflow-hidden rounded-full bg-gray-100">
                    <div class="h-full w-4/5 rounded-full bg-blue-500"></div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-emerald-50"></div>
                <div class="relative flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-400">Active Now</p>
                        <div class="mt-2 flex items-center gap-2">
                            <h2 class="text-3xl font-bold leading-none text-gray-900">
                                <span id="stat-active">{{ $stats['activeNow'] }}</span>
                            </h2>
                            @if($stats['activeNow'] > 0)
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-emerald-400 opacity-60"></span>
                                    <span class="relative inline-flex h-2.5 w-2.5 rounded-full bg-emerald-500"></span>
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600 ring-1 ring-emerald-100">
                        <i class="fa-solid fa-video text-base"></i>
                    </div>
                </div>
                <div class="relative mt-5 flex items-center gap-2 text-xs text-gray-400">
                    <span class="inline-flex h-2 w-2 rounded-full bg-emerald-500"></span>
                    Live meeting activity
                </div>
            </div>

            <div class="relative overflow-hidden rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-100 transition duration-200 hover:-translate-y-0.5 hover:shadow-md">
                <div class="absolute -right-8 -top-8 h-28 w-28 rounded-full bg-violet-50"></div>
                <div class="relative flex items-center justify-between gap-4">
                    <div>
                        <p class="text-[11px] font-semibold uppercase tracking-[0.14em] text-gray-400">Pending Invites</p>
                        <div class="mt-2 flex items-end gap-2">
                            <h2 id="stat-pending" class="text-3xl font-bold leading-none text-gray-900">{{ $stats['pending'] }}</h2>
                            <span class="pb-0.5 text-xs font-medium text-gray-400">waiting</span>
                        </div>
                    </div>
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-violet-50 text-violet-600 ring-1 ring-violet-100">
                        <i class="fa-solid fa-envelope-open-text text-base"></i>
                    </div>
                </div>
                <div class="relative mt-5 flex items-center gap-2 text-xs text-gray-400">
                    <i class="fa-regular fa-clock text-[11px]"></i>
                    Awaiting response
                </div>
            </div>
        </div>
        <!-- ══ TABLE WRAPPER ══ -->
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-5 border-b border-gray-100 bg-white">
                <div class="flex items-center justify-between flex-wrap gap-3">
                    <div>
                        <h2 class="font-bold text-gray-900 text-lg">Participants Overview</h2>
                        <p class="text-xs text-gray-500 mt-1">Manage and monitor all participant activities.</p>
                    </div>

                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[900px] bg-white">
                    <thead>
                    <tr class="bg-gray-50/80 border-b border-gray-100">
                        <th class="px-5 py-4 text-[11px] font-semibold text-gray-500 uppercase tracking-[0.12em] text-left">NAME</th>
                        <th class="px-5 py-4 text-[11px] font-semibold text-gray-500 uppercase tracking-[0.12em] text-left">EMAIL ADDRESS</th>
                        <th class="px-5 py-4 text-[11px] font-semibold text-gray-500 uppercase tracking-[0.12em] text-left">STATUS</th>
                        <th class="px-5 py-4 text-[11px] font-semibold text-gray-500 uppercase tracking-[0.12em] text-left">LAST ACTIVE</th>
                        <th class="px-5 py-4 text-[11px] font-semibold text-gray-500 uppercase tracking-[0.12em] text-left">ACTIONS</th>
                    </tr>
                    </thead>
                    <tbody id="participants-tbody" class="divide-y divide-gray-100 [&>tr]:transition-colors [&>tr:hover]:bg-blue-50/30">
                    <x-participant-table-rows :participants="$participants" />
                    </tbody>
                </table>
            </div>
            <!-- PAGINATION -->
            <div class="px-5 sm:px-6 py-4 border-t border-gray-100 bg-white flex flex-col sm:flex-row justify-between items-center gap-3">
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
