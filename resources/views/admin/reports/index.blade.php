<x-layouts.app>
    <x-slot name="header">
        <x-header.search-bar placeholder="Search reports, meetings, users..." />
    </x-slot>

    <div class="p-3 sm:p-4 bg-slate-50 rounded-2xl m-2 mt-0 overflow-y-auto space-y-4">

        {{-- Page Header --}}
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <p class="text-[11px] font-bold uppercase tracking-[0.18em] text-blue-600">Admin Reports</p>
                <h1 class="mt-1 text-2xl sm:text-3xl font-bold text-slate-900">Reports & Analytics</h1>
                <p class="mt-1 text-sm text-slate-500">
                    Review meeting activity and user participation across any date range.
                </p>
            </div>

            <a href="{{ route('admin.reports.export', request()->query()) }}"
               class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl
                      bg-white border border-slate-200 text-slate-700 text-sm font-semibold
                      hover:border-blue-200 hover:text-blue-600 hover:bg-blue-50 transition shadow-sm
                      w-full sm:w-auto">
                <span class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center">
                    <i class="fa-solid fa-file-pdf text-xs"></i>
                </span>
                Export PDF
            </a>
        </div>

        {{-- Date Range Filter --}}
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-calendar-days text-sm"></i>
                </div>
                <div>
                    <h2 class="text-sm sm:text-base font-semibold text-slate-900">Date Range</h2>
                    <p class="text-xs text-slate-400 mt-0.5">Choose the period you want to analyze.</p>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}" class="p-4 sm:p-5">
                <div class="grid grid-cols-1 xl:grid-cols-[1fr_1fr_auto] gap-3 items-end">
                    <div>
                        <label for="from_date" class="block text-xs font-semibold text-slate-600 mb-1.5">From Date</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-calendar text-xs"></i>
                            </span>
                            <input id="from_date"
                                   type="date"
                                   name="from_date"
                                   value="{{ $filters['from_date'] }}"
                                   max="{{ $filters['to_date'] }}"
                                   class="w-full pl-9 pr-3 py-2.5 rounded-xl border-slate-200 text-sm text-slate-700
                                          bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-blue-500">
                        </div>
                    </div>

                    <div>
                        <label for="to_date" class="block text-xs font-semibold text-slate-600 mb-1.5">To Date</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                                <i class="fa-regular fa-calendar text-xs"></i>
                            </span>
                            <input id="to_date"
                                   type="date"
                                   name="to_date"
                                   value="{{ $filters['to_date'] }}"
                                   min="{{ $filters['from_date'] }}"
                                   class="w-full pl-9 pr-3 py-2.5 rounded-xl border-slate-200 text-sm text-slate-700
                                          bg-slate-50 focus:bg-white focus:border-blue-500 focus:ring-blue-500">
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

                    <div class="flex items-center gap-2">
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 h-[42px] px-4 rounded-xl
                                       bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold transition shadow-sm">
                            <i class="fa-solid fa-check text-xs"></i>
                            Apply
                        </button>

                        <a href="{{ route('admin.reports.index') }}"
                           class="inline-flex items-center justify-center gap-2 h-[42px] px-4 rounded-xl
                                  bg-slate-100 hover:bg-slate-200 text-slate-600 text-sm font-semibold transition">
                            <i class="fa-solid fa-rotate-left text-xs"></i>
                            Reset
                        </a>
                    </div>
                </div>

                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-wrap items-center gap-2">
                    <span class="text-xs font-semibold text-slate-400 mr-1">Quick range</span>

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
                           class="px-3 py-1.5 rounded-lg border border-slate-200 bg-white
                                  text-xs font-medium text-slate-600 hover:border-blue-200
                                  hover:bg-blue-50 hover:text-blue-600 transition">
                            {{ $label }}
                        </a>
                    @endforeach
                </div>
            </form>
        </section>

        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 xl:grid-cols-4 gap-3">
            @foreach([
                ['Meetings', $stats['total_meetings'], 'fa-video', 'blue'],
                ['Users In Meetings', $stats['unique_users'], 'fa-user-group', 'purple'],
                ['Completed', $stats['completed'], 'fa-circle-check', 'green'],
                ['Cancelled', $stats['cancelled'], 'fa-circle-xmark', 'red'],
            ] as [$label, $value, $icon, $tone])

                @php
                    $toneMap = [
                        'blue' => ['icon' => 'bg-blue-50 text-blue-600', 'bar' => 'bg-blue-500'],
                        'purple' => ['icon' => 'bg-purple-50 text-purple-600', 'bar' => 'bg-purple-500'],
                        'green' => ['icon' => 'bg-green-50 text-green-600', 'bar' => 'bg-green-500'],
                        'red' => ['icon' => 'bg-red-50 text-red-500', 'bar' => 'bg-red-500'],
                    ];
                    $toneClasses = $toneMap[$tone];
                @endphp

                <div class="relative bg-white border border-slate-200 rounded-2xl p-4 shadow-sm overflow-hidden">
                    <span class="absolute top-0 left-0 right-0 h-1 {{ $toneClasses['bar'] }}"></span>

                    <div class="flex items-start justify-between gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $toneClasses['icon'] }} flex items-center justify-center">
                            <i class="fa-solid {{ $icon }} text-sm"></i>
                        </div>
                        <span class="text-[10px] text-slate-400 whitespace-nowrap">
                            {{ $fromDate->format('M d') }} – {{ $toDate->format('M d') }}
                        </span>
                    </div>

                    <p class="mt-4 text-2xl font-bold text-slate-900">{{ number_format($value) }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- Daily Activity --}}
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <i class="fa-solid fa-chart-column text-sm"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-900 text-sm sm:text-base">Daily Activity</h2>
                        <p class="text-xs text-slate-400 mt-0.5">Meetings and unique users by day</p>
                    </div>
                </div>

                <span class="inline-flex items-center gap-1.5 w-fit px-2.5 py-1 rounded-lg bg-slate-100 text-slate-500 text-xs font-medium">
                    <i class="fa-regular fa-calendar text-[10px]"></i>
                    {{ $dailyBreakdown->count() }} active days
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[560px]">
                    <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-center">Meetings</th>
                        <th class="px-5 py-3 text-center">Unique Users</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                    @forelse($dailyBreakdown as $day)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5">
                                <p class="font-semibold text-slate-800">{{ $day['date']->format('M d, Y') }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $day['date']->format('l') }}</p>
                            </td>

                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center min-w-10 px-2.5 py-1 rounded-lg
                                             bg-blue-50 text-blue-600 text-xs font-semibold">
                                    {{ $day['meetings'] }}
                                </span>
                            </td>

                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center justify-center min-w-10 px-2.5 py-1 rounded-lg
                                             bg-purple-50 text-purple-600 text-xs font-semibold">
                                    {{ $day['users'] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center">
                                <i class="fa-regular fa-folder-open text-2xl text-slate-300"></i>
                                <p class="mt-2 text-sm text-slate-400">No activity found for this date range.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        {{-- Status Filter --}}
        <section class="bg-white border border-slate-200 rounded-2xl shadow-sm px-3 sm:px-4 py-3">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                <span class="flex items-center gap-1.5 text-xs font-semibold text-slate-500 shrink-0 mr-1">
                    <i class="fa-solid fa-sliders text-blue-500"></i>
                    Status
                </span>

                @foreach(['All Status', 'Active', 'Upcoming', 'Completed', 'Cancelled'] as $opt)
                    @php
                        $isActive = request('status', 'All Status') === $opt;
                        $target = array_merge(request()->except('page'), ['status' => $opt]);
                    @endphp

                    <a href="{{ route('admin.reports.index', $target) }}"
                       class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-semibold border transition
                              {{ $isActive
                                  ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                                  : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50 hover:border-slate-300' }}">
                        {{ $opt }}
                    </a>
                @endforeach
            </div>
        </section>

        @php
            $statusColors = [
                'completed' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                'active' => 'bg-green-50 text-green-700 border-green-100',
                'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                'upcoming' => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                'ended' => 'bg-gray-50 text-gray-600 border-gray-200',
                'flagged' => 'bg-orange-50 text-orange-600 border-orange-100',
            ];
            $dotColors = [
                'completed' => 'bg-indigo-500',
                'active' => 'bg-green-500 animate-pulse',
                'cancelled' => 'bg-red-500',
                'upcoming' => 'bg-yellow-500',
                'ended' => 'bg-gray-400',
                'flagged' => 'bg-orange-500',
            ];
        @endphp

        {{-- Meetings Report --}}
        <section id="reports-table" class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-slate-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center">
                        <i class="fa-solid fa-table-list text-sm"></i>
                    </div>
                    <div>
                        <h2 class="font-semibold text-slate-900 text-sm sm:text-base">Meetings Report</h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $fromDate->format('M d, Y') }} to {{ $toDate->format('M d, Y') }}
                        </p>
                    </div>
                </div>

                <span class="text-xs text-slate-400 whitespace-nowrap">{{ number_format($meetings->total()) }} records</span>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm min-w-[760px]">
                    <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-semibold text-slate-500 uppercase tracking-wider">
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
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-5 py-3.5 max-w-[230px]">
                                <p class="font-semibold text-slate-800 truncate">{{ $meeting->title }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ $meeting->duration }} min</p>
                            </td>

                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    @if($meeting->organizer)
                                        <x-user-avatar :user="$meeting->organizer" size="sm" />
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xs">NA</div>
                                    @endif
                                    <span class="text-sm text-slate-700 truncate">{{ $meeting->organizer?->name ?? 'Unassigned' }}</span>
                                </div>
                            </td>

                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <p class="text-sm text-slate-700">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</p>
                                <p class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</p>
                            </td>

                            <td class="px-5 py-3.5 text-center">
                                <span class="inline-flex items-center gap-1.5 bg-slate-100 text-slate-600 px-2.5 py-1 rounded-lg text-xs font-medium">
                                    <i class="fa-solid fa-users text-[10px]"></i>
                                    {{ $meeting->participants->count() }}
                                </span>
                            </td>

                            <td class="px-5 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border whitespace-nowrap {{ $statusColors[$meeting->status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$meeting->status] ?? 'bg-gray-400' }}"></span>
                                    {{ ucfirst($meeting->status) }}
                                </span>
                            </td>

                            <td class="px-5 py-3.5 text-center">
                                <a href="{{ route('admin.meetings.show', $meeting->id) }}"
                                   class="inline-flex w-8 h-8 items-center justify-center rounded-lg
                                          text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition">
                                    <i class="fa-regular fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-slate-400">
                                No meetings found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="md:hidden divide-y divide-slate-100">
                @forelse($meetings as $meeting)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-slate-800 truncate">{{ $meeting->title }}</p>
                                <p class="text-xs text-slate-400 mt-1">
                                    {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }} ·
                                    {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                                </p>
                            </div>

                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold border shrink-0 {{ $statusColors[$meeting->status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotColors[$meeting->status] ?? 'bg-gray-400' }}"></span>
                                {{ ucfirst($meeting->status) }}
                            </span>
                        </div>

                        <div class="flex items-center justify-between gap-3 mt-3">
                            <div class="min-w-0">
                                <p class="text-xs text-slate-700 truncate">{{ $meeting->organizer?->name ?? 'Unassigned' }}</p>
                                <p class="text-[11px] text-slate-400">{{ $meeting->participants->count() }} participants · {{ $meeting->duration }} min</p>
                            </div>

                            <a href="{{ route('admin.meetings.show', $meeting->id) }}"
                               class="w-8 h-8 shrink-0 flex items-center justify-center rounded-lg
                                      bg-slate-50 text-slate-500 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-sm text-slate-400">No meetings found.</div>
                @endforelse
            </div>

            @if($meetings->hasPages())
                <div class="px-4 py-3 border-t border-slate-100 bg-slate-50/50">
                    {{ $meetings->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
