<x-layouts.app>
    <x-slot name="header">
        <x-header.search-bar placeholder="Search reports, meetings, users..." />
    </x-slot>

    <div class="m-2 mt-0 rounded-3xl bg-slate-50 p-3 sm:p-5 overflow-y-auto space-y-5">
        <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
            <div class="min-w-0">
                <div class="inline-flex items-center gap-2 rounded-full bg-blue-50 px-3 py-1 text-[11px] font-bold uppercase tracking-[0.14em] text-blue-600">
                    <i class="fa-solid fa-chart-line text-[10px]"></i>
                    Analytics
                </div>
                <h1 class="mt-2 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">Reports & Analytics</h1>
                <p class="mt-1 text-sm text-slate-500">View meeting activity and user participation for any selected period.</p>
            </div>

            <a href="{{ route('admin.reports.export', request()->query()) }}"
               class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                <i class="fa-solid fa-file-arrow-down text-xs"></i>
                Export PDF
            </a>
        </div>

        <section class="rounded-3xl bg-white p-4 sm:p-5 shadow-sm ring-1 ring-slate-100">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 mb-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <i class="fa-regular fa-calendar text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-semibold text-slate-900">Date Range</h2>
                        <p class="text-xs text-slate-400">Select the period to generate the report.</p>
                    </div>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}">
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                    <div>
                        <label for="from_date" class="mb-1.5 block text-xs font-semibold text-slate-600">From Date</label>
                        <input id="from_date" type="date" name="from_date" value="{{ $filters['from_date'] }}" max="{{ $filters['to_date'] }}"
                               class="w-full rounded-xl border-0 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 ring-1 ring-slate-200 transition focus:bg-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="to_date" class="mb-1.5 block text-xs font-semibold text-slate-600">To Date</label>
                        <input id="to_date" type="date" name="to_date" value="{{ $filters['to_date'] }}" min="{{ $filters['from_date'] }}"
                               class="w-full rounded-xl border-0 bg-slate-50 px-3.5 py-2.5 text-sm text-slate-700 ring-1 ring-slate-200 transition focus:bg-white focus:ring-2 focus:ring-blue-500">
                    </div>

                    @if(request('status'))
                        <input type="hidden" name="status" value="{{ request('status') }}">
                    @endif
                    @if(request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if(request()->boolean('flagged'))
                        <input type="hidden" name="flagged" value="1">
                    @endif

                    <div class="flex items-center gap-2">
                        <button type="submit" class="inline-flex h-[42px] flex-1 sm:flex-none items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
                            <i class="fa-solid fa-check text-[10px]"></i>
                            Apply
                        </button>
                        <a href="{{ route('admin.reports.index') }}" class="inline-flex h-[42px] flex-1 sm:flex-none items-center justify-center gap-2 rounded-xl bg-slate-100 px-4 text-sm font-semibold text-slate-600 transition hover:bg-slate-200">
                            <i class="fa-solid fa-rotate-left text-[10px]"></i>
                            Reset
                        </a>
                    </div>
                </div>

                <div class="mt-4 flex flex-wrap items-center gap-2">
                    <span class="mr-1 text-xs font-semibold text-slate-400">Quick range</span>
                    @php
                        $today = now()->toDateString();
                        $quickRanges = [
                            ['Today', $today, $today],
                            ['Last 7 Days', now()->subDays(6)->toDateString(), $today],
                            ['Last 30 Days', now()->subDays(29)->toDateString(), $today],
                            ['This Month', now()->startOfMonth()->toDateString(), $today],
                        ];
                    @endphp
                    @foreach($quickRanges as [$label, $from, $to])
                        <a href="{{ route('admin.reports.index', array_merge(request()->except(['page', 'from_date', 'to_date']), ['from_date' => $from, 'to_date' => $to])) }}"
                           class="rounded-lg bg-slate-50 px-3 py-1.5 text-xs font-medium text-slate-600 ring-1 ring-slate-200 transition hover:bg-blue-50 hover:text-blue-600 hover:ring-blue-100">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </form>
        </section>

        @php
            $summaryCards = [
                ['Meetings', $stats['total_meetings'], 'fa-video', 'bg-blue-50 text-blue-600'],
                ['Users In Meetings', $stats['unique_users'], 'fa-user-group', 'bg-violet-50 text-violet-600'],
                ['Completed', $stats['completed'], 'fa-circle-check', 'bg-emerald-50 text-emerald-600'],
                ['Cancelled', $stats['cancelled'], 'fa-ban', 'bg-rose-50 text-rose-500'],
            ];
        @endphp

        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
            @foreach($summaryCards as [$label, $value, $icon, $iconClass])
                <div class="rounded-3xl bg-white p-4 sm:p-5 shadow-sm ring-1 ring-slate-100">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-2xl {{ $iconClass }}">
                            <i class="fa-solid {{ $icon }} text-sm"></i>
                        </div>
                        <span class="text-[10px] font-medium text-slate-400 whitespace-nowrap">{{ $fromDate->format('M d') }} – {{ $toDate->format('M d') }}</span>
                    </div>
                    <p class="mt-4 text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">{{ number_format($value) }}</p>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        <section class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-4 sm:px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                        <i class="fa-solid fa-chart-column text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-sm sm:text-base font-semibold text-slate-900">Daily Activity</h2>
                        <p class="text-xs text-slate-400">Meetings and unique users for each day.</p>
                    </div>
                </div>
                <span class="inline-flex w-fit items-center gap-1.5 rounded-lg bg-slate-50 px-2.5 py-1.5 text-xs font-medium text-slate-500">
                    <i class="fa-regular fa-calendar text-[10px]"></i>
                    {{ $dailyBreakdown->count() }} active days
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-sm">
                    <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-center">Meetings</th>
                        <th class="px-5 py-3 text-center">Unique Users</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($dailyBreakdown as $day)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-slate-800">{{ $day['date']->format('M d, Y') }}</p>
                                <p class="mt-0.5 text-xs text-slate-400">{{ $day['date']->format('l') }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center"><span class="inline-flex min-w-10 justify-center rounded-lg bg-blue-50 px-2.5 py-1 text-xs font-semibold text-blue-600">{{ $day['meetings'] }}</span></td>
                            <td class="px-5 py-3.5 text-center"><span class="inline-flex min-w-10 justify-center rounded-lg bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-600">{{ $day['users'] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="px-5 py-10 text-center text-sm text-slate-400">No activity found for this date range.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="rounded-3xl bg-white p-3 sm:p-4 shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                <span class="mr-1 flex shrink-0 items-center gap-1.5 text-xs font-semibold text-slate-500"><i class="fa-solid fa-sliders text-blue-500"></i>Status</span>
                @foreach(['All Status', 'Active', 'Upcoming', 'Completed', 'Cancelled'] as $opt)
                    @php
                        $isActive = request('status', 'All Status') === $opt;
                        $target = array_merge(request()->except('page'), ['status' => $opt]);
                    @endphp
                    <a href="{{ route('admin.reports.index', $target) }}"
                       class="shrink-0 rounded-lg px-3 py-1.5 text-xs font-semibold transition {{ $isActive ? 'bg-blue-600 text-white shadow-sm' : 'bg-slate-50 text-slate-600 hover:bg-slate-100' }}">
                        {{ $opt }}
                    </a>
                @endforeach
            </div>
        </section>

        @php
            $statusColors = [
                'completed' => 'bg-indigo-50 text-indigo-600',
                'active' => 'bg-emerald-50 text-emerald-700',
                'cancelled' => 'bg-rose-50 text-rose-600',
                'upcoming' => 'bg-amber-50 text-amber-700',
                'ended' => 'bg-slate-100 text-slate-600',
                'flagged' => 'bg-orange-50 text-orange-600',
            ];
            $dotColors = [
                'completed' => 'bg-indigo-500',
                'active' => 'bg-emerald-500 animate-pulse',
                'cancelled' => 'bg-rose-500',
                'upcoming' => 'bg-amber-500',
                'ended' => 'bg-slate-400',
                'flagged' => 'bg-orange-500',
            ];
        @endphp

        <section id="reports-table" class="overflow-hidden rounded-3xl bg-white shadow-sm ring-1 ring-slate-100">
            <div class="flex items-center justify-between gap-3 px-4 sm:px-5 py-4">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-slate-600"><i class="fa-solid fa-table-list text-sm"></i></div>
                    <div>
                        <h2 class="text-sm sm:text-base font-semibold text-slate-900">Meetings Report</h2>
                        <p class="text-xs text-slate-400">{{ $fromDate->format('M d, Y') }} to {{ $toDate->format('M d, Y') }}</p>
                    </div>
                </div>
                <span class="text-xs text-slate-400 whitespace-nowrap">{{ number_format($meetings->total()) }} records</span>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                    <tr class="bg-slate-50 text-[11px] font-semibold uppercase tracking-wider text-slate-500">
                        <th class="px-5 py-3 text-left">Meeting</th>
                        <th class="px-5 py-3 text-left">Organizer</th>
                        <th class="px-5 py-3 text-left">Schedule</th>
                        <th class="px-5 py-3 text-center">Participants</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-center">View</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                    @forelse($meetings as $meeting)
                        <tr class="transition hover:bg-slate-50/70">
                            <td class="max-w-[230px] px-5 py-3.5"><p class="truncate font-semibold text-slate-800">{{ $meeting->title }}</p><p class="mt-0.5 text-xs text-slate-400">{{ $meeting->duration }} min</p></td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    @if($meeting->organizer)
                                        <x-user-avatar :user="$meeting->organizer" size="sm" />
                                    @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-slate-100 text-xs text-slate-400">NA</div>
                                    @endif
                                    <span class="truncate text-sm text-slate-700">{{ $meeting->organizer?->name ?? 'Unassigned' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap"><p class="text-sm text-slate-700">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</p><p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</p></td>
                            <td class="px-5 py-3.5 text-center"><span class="inline-flex items-center gap-1.5 rounded-lg bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-600"><i class="fa-solid fa-users text-[10px]"></i>{{ $meeting->participants->count() }}</span></td>
                            <td class="px-5 py-3.5"><span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $statusColors[$meeting->status] ?? 'bg-slate-100 text-slate-600' }}"><span class="h-1.5 w-1.5 rounded-full {{ $dotColors[$meeting->status] ?? 'bg-slate-400' }}"></span>{{ ucfirst($meeting->status) }}</span></td>
                            <td class="px-5 py-3.5 text-center"><a href="{{ route('admin.meetings.show', $meeting->id) }}" class="inline-flex h-8 w-8 items-center justify-center rounded-lg bg-slate-50 text-slate-400 transition hover:bg-blue-50 hover:text-blue-600"><i class="fa-regular fa-eye text-xs"></i></a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">No meetings found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-slate-100">
                @forelse($meetings as $meeting)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0"><p class="truncate font-semibold text-slate-800">{{ $meeting->title }}</p><p class="mt-1 text-xs text-slate-400">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }} · {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</p></div>
                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold {{ $statusColors[$meeting->status] ?? 'bg-slate-100 text-slate-600' }}"><span class="h-1.5 w-1.5 rounded-full {{ $dotColors[$meeting->status] ?? 'bg-slate-400' }}"></span>{{ ucfirst($meeting->status) }}</span>
                        </div>
                        <div class="mt-3 flex items-center justify-between gap-3">
                            <div class="min-w-0"><p class="truncate text-xs text-slate-700">{{ $meeting->organizer?->name ?? 'Unassigned' }}</p><p class="text-[11px] text-slate-400">{{ $meeting->participants->count() }} participants · {{ $meeting->duration }} min</p></div>
                            <a href="{{ route('admin.meetings.show', $meeting->id) }}" class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-slate-50 text-slate-500 transition hover:bg-blue-50 hover:text-blue-600"><i class="fa-regular fa-eye text-xs"></i></a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-sm text-slate-400">No meetings found.</div>
                @endforelse
            </div>

            @if($meetings->hasPages())
                <div class="bg-slate-50/60 px-4 py-3">{{ $meetings->links() }}</div>
            @endif
        </section>
    </div>
</x-layouts.app>
