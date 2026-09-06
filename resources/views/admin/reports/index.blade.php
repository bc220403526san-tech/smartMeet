<x-layouts.app>
    <x-slot name="header">
        <x-header.search-bar placeholder="Search reports, meetings, users..." />
    </x-slot>

    <div class="m-2 mt-0 space-y-7 overflow-y-auto rounded-[32px] bg-[#f6f9ff] p-4 sm:p-6 lg:p-8">

        {{-- HERO / PAGE HEADER --}}
        <section class="relative overflow-hidden rounded-[30px] bg-gradient-to-br from-blue-600 via-blue-600 to-blue-700
                        px-6 py-7 text-white shadow-[0_22px_55px_rgba(37,99,235,0.22)]
                        sm:px-8 sm:py-8">
            <div class="pointer-events-none absolute -right-20 -top-24 h-72 w-72 rounded-full bg-white/10 blur-2xl"></div>
            <div class="pointer-events-none absolute bottom-0 left-1/3 h-40 w-40 rounded-full bg-sky-300/10 blur-2xl"></div>

            <div class="relative flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
                <div class="min-w-0">
                    <div class="inline-flex items-center gap-2 rounded-full bg-white/12 px-3 py-1.5
                                text-[11px] font-bold uppercase tracking-[0.14em] text-blue-50">
                        <i class="fa-solid fa-chart-line text-[10px]"></i>
                        Analytics
                    </div>

                    <h1 class="mt-4 text-2xl font-bold tracking-tight sm:text-3xl">
                        Reports & Analytics
                    </h1>

                    <p class="mt-2 max-w-2xl text-sm leading-6 text-blue-100">
                        Analyze meeting activity, unique users, daily trends and meeting performance for any selected period.
                    </p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <a href="{{ route('admin.reports.index') }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white/12
                              px-4 py-2.5 text-sm font-semibold text-white backdrop-blur
                              transition hover:-translate-y-0.5 hover:bg-white/18 sm:w-auto">
                        <i class="fa-solid fa-rotate-left text-[10px]"></i>
                        Reset
                    </a>

                    <a href="{{ route('admin.reports.export', request()->query()) }}"
                       class="inline-flex w-full items-center justify-center gap-2 rounded-xl bg-white
                              px-4 py-2.5 text-sm font-semibold text-blue-700
                              shadow-[0_10px_28px_rgba(15,23,42,0.14)]
                              transition hover:-translate-y-0.5 hover:shadow-[0_16px_34px_rgba(15,23,42,0.18)] sm:w-auto">
                        <i class="fa-solid fa-file-pdf text-xs"></i>
                        Export PDF
                    </a>
                </div>
            </div>
        </section>

        {{-- DATE RANGE --}}
        <section class="rounded-[28px] bg-white p-5 shadow-[0_14px_38px_rgba(15,23,42,0.06)] sm:p-7">
            <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-blue-50 text-blue-600
                                shadow-[0_8px_20px_rgba(37,99,235,0.10)]">
                        <i class="fa-regular fa-calendar text-sm"></i>
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Date Range</h2>
                        <p class="mt-1 text-xs text-slate-400">
                            Select the reporting period you want to analyze.
                        </p>
                    </div>
                </div>

                <span class="inline-flex w-fit items-center gap-2 rounded-xl bg-blue-50 px-3.5 py-2
                             text-xs font-semibold text-blue-600">
                    <i class="fa-regular fa-calendar-days text-[10px]"></i>
                    {{ $fromDate->format('M d, Y') }} — {{ $toDate->format('M d, Y') }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}">
                <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <label for="from_date" class="mb-2 block text-xs font-semibold text-slate-600">
                            From Date
                        </label>

                        <input id="from_date"
                               type="date"
                               name="from_date"
                               value="{{ $filters['from_date'] }}"
                               max="{{ $filters['to_date'] }}"
                               class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm text-slate-700
                                      outline-none ring-1 ring-slate-100 transition
                                      focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">
                        <label for="to_date" class="mb-2 block text-xs font-semibold text-slate-600">
                            To Date
                        </label>

                        <input id="to_date"
                               type="date"
                               name="to_date"
                               value="{{ $filters['to_date'] }}"
                               min="{{ $filters['from_date'] }}"
                               class="w-full rounded-xl border-0 bg-white px-4 py-3 text-sm text-slate-700
                                      outline-none ring-1 ring-slate-100 transition
                                      focus:ring-2 focus:ring-blue-500">
                    </div>
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

                <div class="mt-5 flex flex-col gap-4 xl:flex-row xl:items-center xl:justify-between">
                    <div class="flex flex-wrap items-center gap-2">
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
                            <a href="{{ route('admin.reports.index', array_merge(
                                request()->except(['page', 'from_date', 'to_date']),
                                ['from_date' => $from, 'to_date' => $to]
                            )) }}"
                               class="rounded-xl bg-blue-50 px-3.5 py-2 text-xs font-semibold text-blue-600
                                      transition hover:-translate-y-0.5 hover:bg-blue-100">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600
                                   px-5 py-2.5 text-sm font-semibold text-white
                                   shadow-[0_10px_24px_rgba(37,99,235,0.20)]
                                   transition hover:-translate-y-0.5 hover:bg-blue-700
                                   hover:shadow-[0_14px_30px_rgba(37,99,235,0.28)]">
                        <i class="fa-solid fa-check text-[10px]"></i>
                        Apply Filters
                    </button>
                </div>
            </form>
        </section>

        {{-- SUMMARY CARDS --}}
        @php
            $summaryCards = [
                ['label' => 'Meetings', 'value' => $stats['total_meetings'], 'icon' => 'fa-video', 'iconClass' => 'bg-blue-50 text-blue-600'],
                ['label' => 'Unique Users', 'value' => $stats['unique_users'], 'icon' => 'fa-user-group', 'iconClass' => 'bg-indigo-50 text-indigo-600'],
                ['label' => 'Completed', 'value' => $stats['completed'], 'icon' => 'fa-circle-check', 'iconClass' => 'bg-sky-50 text-sky-600'],
                ['label' => 'Cancelled', 'value' => $stats['cancelled'], 'icon' => 'fa-ban', 'iconClass' => 'bg-slate-100 text-slate-500'],
            ];
        @endphp

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">
            @foreach($summaryCards as $card)
                <div class="rounded-[26px] bg-white p-5 shadow-[0_14px_36px_rgba(15,23,42,0.06)]
                            transition duration-300 hover:-translate-y-1 hover:shadow-[0_20px_48px_rgba(15,23,42,0.09)] sm:p-6">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex h-11 w-11 items-center justify-center rounded-2xl {{ $card['iconClass'] }}">
                            <i class="fa-solid {{ $card['icon'] }} text-sm"></i>
                        </div>

                        <span class="rounded-xl bg-slate-50 px-2.5 py-1 text-[10px] font-medium text-slate-400">
                            {{ $fromDate->format('M d') }} – {{ $toDate->format('M d') }}
                        </span>
                    </div>

                    <p class="mt-5 text-3xl font-bold tracking-tight text-slate-900">
                        {{ number_format($card['value']) }}
                    </p>

                    <p class="mt-1 text-sm font-medium text-slate-500">
                        {{ $card['label'] }}
                    </p>
                </div>
            @endforeach
        </div>

        {{-- DAILY ACTIVITY --}}
        <section class="overflow-hidden rounded-[28px] bg-white shadow-[0_14px_38px_rgba(15,23,42,0.06)]">
            <div class="flex flex-col gap-3 px-5 py-5 sm:flex-row sm:items-center sm:justify-between sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-chart-column text-sm"></i>
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Daily Activity</h2>
                        <p class="mt-0.5 text-xs text-slate-400">
                            Meetings and unique users for each day.
                        </p>
                    </div>
                </div>

                <span class="inline-flex w-fit items-center gap-1.5 rounded-xl bg-blue-50 px-3 py-2
                             text-xs font-medium text-blue-600">
                    <i class="fa-regular fa-calendar text-[10px]"></i>
                    {{ $dailyBreakdown->count() }} active days
                </span>
            </div>

            <div class="overflow-x-auto px-3 pb-3 sm:px-5 sm:pb-5">
                <div class="overflow-hidden rounded-2xl bg-slate-50">
                    <table class="w-full min-w-[560px] text-sm">
                        <thead>
                        <tr class="bg-blue-50 text-[11px] font-semibold uppercase tracking-wider text-blue-700">
                            <th class="px-5 py-3.5 text-left">Date</th>
                            <th class="px-5 py-3.5 text-center">Meetings</th>
                            <th class="px-5 py-3.5 text-center">Unique Users</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-white bg-slate-50">
                        @forelse($dailyBreakdown as $day)
                            <tr class="transition hover:bg-white">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-slate-800">
                                        {{ $day['date']->format('M d, Y') }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-400">
                                        {{ $day['date']->format('l') }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex min-w-10 justify-center rounded-xl bg-white px-3 py-1.5
                                                 text-xs font-semibold text-blue-600 shadow-sm">
                                        {{ $day['meetings'] }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex min-w-10 justify-center rounded-xl bg-white px-3 py-1.5
                                                 text-xs font-semibold text-indigo-600 shadow-sm">
                                        {{ $day['users'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <i class="fa-regular fa-folder-open text-2xl text-slate-300"></i>
                                    <p class="mt-2 text-sm text-slate-400">
                                        No activity found for this date range.
                                    </p>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- STATUS FILTER --}}
        <section class="rounded-[26px] bg-white p-4 shadow-[0_14px_36px_rgba(15,23,42,0.06)]">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                <span class="mr-1 flex shrink-0 items-center gap-1.5 text-xs font-semibold text-slate-500">
                    <i class="fa-solid fa-sliders text-blue-500"></i>
                    Status
                </span>

                @foreach(['All Status', 'Active', 'Upcoming', 'Completed', 'Cancelled'] as $opt)
                    @php
                        $isActive = request('status', 'All Status') === $opt;
                        $target = array_merge(request()->except('page'), ['status' => $opt]);
                    @endphp

                    <a href="{{ route('admin.reports.index', $target) }}"
                       class="shrink-0 rounded-xl px-3.5 py-2 text-xs font-semibold transition
                              {{ $isActive
                                  ? 'bg-blue-600 text-white shadow-[0_8px_20px_rgba(37,99,235,0.20)]'
                                  : 'bg-blue-50 text-blue-600 hover:bg-blue-100' }}">
                        {{ $opt }}
                    </a>
                @endforeach
            </div>
        </section>

        @php
            $statusColors = [
                'completed' => 'bg-blue-50 text-blue-700',
                'active' => 'bg-emerald-50 text-emerald-700',
                'cancelled' => 'bg-slate-100 text-slate-600',
                'upcoming' => 'bg-sky-50 text-sky-700',
                'ended' => 'bg-slate-100 text-slate-600',
                'flagged' => 'bg-amber-50 text-amber-700',
            ];

            $dotColors = [
                'completed' => 'bg-blue-500',
                'active' => 'bg-emerald-500 animate-pulse',
                'cancelled' => 'bg-slate-400',
                'upcoming' => 'bg-sky-500',
                'ended' => 'bg-slate-400',
                'flagged' => 'bg-amber-500',
            ];
        @endphp

        {{-- MEETINGS REPORT --}}
        <section id="reports-table" class="overflow-hidden rounded-[28px] bg-white shadow-[0_14px_38px_rgba(15,23,42,0.06)]">
            <div class="flex items-center justify-between gap-3 px-5 py-5 sm:px-6">
                <div class="flex items-center gap-3">
                    <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                        <i class="fa-solid fa-table-list text-sm"></i>
                    </div>

                    <div>
                        <h2 class="text-base font-semibold text-slate-900">Meetings Report</h2>
                        <p class="mt-0.5 text-xs text-slate-400">
                            {{ $fromDate->format('M d, Y') }} to {{ $toDate->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                <span class="rounded-xl bg-blue-50 px-3 py-2 text-xs font-medium text-blue-600 whitespace-nowrap">
                    {{ number_format($meetings->total()) }} records
                </span>
            </div>

            <div class="hidden overflow-x-auto px-3 pb-3 md:block sm:px-5 sm:pb-5">
                <div class="overflow-hidden rounded-2xl bg-slate-50">
                    <table class="w-full min-w-[760px] text-sm">
                        <thead>
                        <tr class="bg-blue-50 text-[11px] font-semibold uppercase tracking-wider text-blue-700">
                            <th class="px-5 py-3.5 text-left">Meeting</th>
                            <th class="px-5 py-3.5 text-left">Organizer</th>
                            <th class="px-5 py-3.5 text-left">Schedule</th>
                            <th class="px-5 py-3.5 text-center">Participants</th>
                            <th class="px-5 py-3.5 text-left">Status</th>
                            <th class="px-5 py-3.5 text-center">View</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-white bg-slate-50">
                        @forelse($meetings as $meeting)
                            <tr class="transition hover:bg-white">
                                <td class="max-w-[230px] px-5 py-4">
                                    <p class="truncate font-semibold text-slate-800">
                                        {{ $meeting->title }}
                                    </p>
                                    <p class="mt-0.5 text-xs text-slate-400">
                                        {{ $meeting->duration }} min
                                    </p>
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex min-w-0 items-center gap-2.5">
                                        @if($meeting->organizer)
                                            <x-user-avatar :user="$meeting->organizer" size="sm" />
                                        @else
                                            <div class="flex h-8 w-8 items-center justify-center rounded-full
                                                        bg-white text-xs text-slate-400 shadow-sm">
                                                NA
                                            </div>
                                        @endif

                                        <span class="truncate text-sm text-slate-700">
                                            {{ $meeting->organizer?->name ?? 'Unassigned' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="px-5 py-4 whitespace-nowrap">
                                    <p class="text-sm text-slate-700">
                                        {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}
                                    </p>
                                    <p class="text-xs text-slate-400">
                                        {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                                    </p>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <span class="inline-flex items-center gap-1.5 rounded-xl bg-white px-3 py-1.5
                                                 text-xs font-medium text-slate-600 shadow-sm">
                                        <i class="fa-solid fa-users text-[10px] text-blue-500"></i>
                                        {{ $meeting->participants->count() }}
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5
                                                 text-xs font-semibold
                                                 {{ $statusColors[$meeting->status] ?? 'bg-slate-100 text-slate-600' }}">
                                        <span class="h-1.5 w-1.5 rounded-full
                                                     {{ $dotColors[$meeting->status] ?? 'bg-slate-400' }}"></span>
                                        {{ ucfirst($meeting->status) }}
                                    </span>
                                </td>

                                <td class="px-5 py-4 text-center">
                                    <a href="{{ route('admin.meetings.show', $meeting->id) }}"
                                       class="inline-flex h-9 w-9 items-center justify-center rounded-xl bg-white
                                              text-slate-400 shadow-sm transition
                                              hover:-translate-y-0.5 hover:text-blue-600">
                                        <i class="fa-regular fa-eye text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-sm text-slate-400">
                                    No meetings found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- MOBILE --}}
            <div class="space-y-3 px-3 pb-4 md:hidden">
                @forelse($meetings as $meeting)
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-semibold text-slate-800">
                                    {{ $meeting->title }}
                                </p>

                                <p class="mt-1 text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}
                                    ·
                                    {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                                </p>
                            </div>

                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-3 py-1.5
                                         text-[11px] font-semibold
                                         {{ $statusColors[$meeting->status] ?? 'bg-slate-100 text-slate-600' }}">
                                <span class="h-1.5 w-1.5 rounded-full
                                             {{ $dotColors[$meeting->status] ?? 'bg-slate-400' }}"></span>
                                {{ ucfirst($meeting->status) }}
                            </span>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-xs text-slate-700">
                                    {{ $meeting->organizer?->name ?? 'Unassigned' }}
                                </p>

                                <p class="text-[11px] text-slate-400">
                                    {{ $meeting->participants->count() }} participants
                                    ·
                                    {{ $meeting->duration }} min
                                </p>
                            </div>

                            <a href="{{ route('admin.meetings.show', $meeting->id) }}"
                               class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl
                                      bg-white text-slate-500 shadow-sm transition hover:text-blue-600">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-slate-50 p-10 text-center text-sm text-slate-400">
                        No meetings found.
                    </div>
                @endforelse
            </div>

            @if($meetings->hasPages())
                <div class="px-5 pb-5">
                    <div class="rounded-2xl bg-slate-50 px-4 py-3">
                        {{ $meetings->links() }}
                    </div>
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
