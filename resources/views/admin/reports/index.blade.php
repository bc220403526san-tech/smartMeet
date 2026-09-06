<x-layouts.app>
    <x-slot name="header">
        <x-header.search-bar placeholder="Search reports, meetings, users..." />
    </x-slot>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=IBM+Plex+Mono:wght@400;500;600;700&display=swap');

        .rpt-scope { font-family: 'Inter', ui-sans-serif, system-ui, sans-serif; }
        .rpt-scope .num { font-family: 'IBM Plex Mono', ui-monospace, monospace; font-feature-settings: "tnum" 1; }
        .rpt-scope .bar-track { background: #ECEAE2; }
        .rpt-scope .tab-underline { transition: color .15s ease; }
        .rpt-scope .tab-underline::after {
            content: '';
            position: absolute;
            left: 0; right: 0; bottom: -1px;
            height: 2px;
            background: #1F3A66;
            transform: scaleX(0);
            transition: transform .18s ease;
        }
        .rpt-scope .tab-underline.is-active::after { transform: scaleX(1); }
    </style>

    @php
        $totalForBars = max((int) ($stats['total_meetings'] ?? 0), 1);
        $completedPct = min(100, round(($stats['completed'] ?? 0) / $totalForBars * 100));
        $cancelledPct = min(100, round(($stats['cancelled'] ?? 0) / $totalForBars * 100));
        $maxDailyMeetings = $dailyBreakdown->max('meetings') ?: 1;
        $maxDailyUsers = $dailyBreakdown->max('users') ?: 1;
    @endphp

    <div class="rpt-scope m-2 mt-0 space-y-5 overflow-y-auto rounded-2xl bg-[#F7F6F2] p-4 sm:p-6 lg:p-8">

        {{-- MASTHEAD --}}
        <div class="flex flex-col gap-4 border-b border-[#E4E1D8] pb-5 sm:flex-row sm:items-end sm:justify-between">
            <div class="min-w-0">
                <p class="text-xs font-medium text-[#8A8D97]">Admin / Analytics</p>
                <h1 class="mt-1 text-[26px] font-semibold leading-tight tracking-tight text-[#14161F] sm:text-[30px]">
                    Reports &amp; Analytics
                </h1>
                <p class="mt-1.5 max-w-xl text-sm leading-6 text-[#6B6F7A]">
                    Meeting activity, unique users and performance for
                    <span class="num text-[#14161F]">{{ $fromDate->format('M d, Y') }}</span>
                    through
                    <span class="num text-[#14161F]">{{ $toDate->format('M d, Y') }}</span>.
                </p>
            </div>

            <div class="flex shrink-0 gap-2">
                <a href="{{ route('admin.reports.index') }}"
                   class="inline-flex items-center gap-2 rounded-lg border border-[#DEDBD2] bg-white px-4 py-2.5
                          text-sm font-medium text-[#4B4F5B] transition hover:border-[#C7C3B7] hover:bg-[#FBFAF7]">
                    <i class="fa-solid fa-rotate-left text-[11px] text-[#8A8D97]"></i>
                    Reset
                </a>

                <a href="{{ route('admin.reports.export', request()->query()) }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-[#1F3A66] px-4 py-2.5
                          text-sm font-medium text-white transition hover:bg-[#17294D]">
                    <i class="fa-regular fa-file-pdf text-[11px]"></i>
                    Export PDF
                </a>
            </div>
        </div>

        {{-- DATE RANGE --}}
        <section class="rounded-xl border border-[#E4E1D8] bg-white p-5 sm:p-6">
            <div class="mb-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <h2 class="text-sm font-semibold text-[#14161F]">Date range</h2>
                <span class="num inline-flex w-fit items-center gap-2 rounded-md bg-[#F7F6F2] px-2.5 py-1.5 text-xs text-[#4B4F5B]">
                    {{ $fromDate->format('M d, Y') }} → {{ $toDate->format('M d, Y') }}
                </span>
            </div>

            <form method="GET" action="{{ route('admin.reports.index') }}">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="from_date" class="mb-1.5 block text-xs font-medium text-[#6B6F7A]">From date</label>
                        <input id="from_date"
                               type="date"
                               name="from_date"
                               value="{{ $filters['from_date'] }}"
                               max="{{ $filters['to_date'] }}"
                               class="num w-full rounded-lg border border-[#DEDBD2] bg-white px-3.5 py-2.5 text-sm text-[#14161F]
                                      outline-none transition focus:border-[#1F3A66] focus:ring-2 focus:ring-[#1F3A66]/15">
                    </div>

                    <div>
                        <label for="to_date" class="mb-1.5 block text-xs font-medium text-[#6B6F7A]">To date</label>
                        <input id="to_date"
                               type="date"
                               name="to_date"
                               value="{{ $filters['to_date'] }}"
                               min="{{ $filters['from_date'] }}"
                               class="num w-full rounded-lg border border-[#DEDBD2] bg-white px-3.5 py-2.5 text-sm text-[#14161F]
                                      outline-none transition focus:border-[#1F3A66] focus:ring-2 focus:ring-[#1F3A66]/15">
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

                <div class="mt-5 flex flex-col gap-4 border-t border-[#EFEDE6] pt-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex flex-wrap items-center gap-1.5">
                        <span class="mr-1 text-xs text-[#8A8D97]">Quick range</span>

                        @php
                            $today = now()->toDateString();

                            $quickRanges = [
                                ['Today', $today, $today],
                                ['Last 7 days', now()->subDays(6)->toDateString(), $today],
                                ['Last 30 days', now()->subDays(29)->toDateString(), $today],
                                ['This month', now()->startOfMonth()->toDateString(), $today],
                            ];
                        @endphp

                        @foreach($quickRanges as [$label, $from, $to])
                            <a href="{{ route('admin.reports.index', array_merge(
                                request()->except(['page', 'from_date', 'to_date']),
                                ['from_date' => $from, 'to_date' => $to]
                            )) }}"
                               class="rounded-md px-3 py-1.5 text-xs font-medium text-[#4B4F5B]
                                      transition hover:bg-[#F0EEE7] hover:text-[#14161F]">
                                {{ $label }}
                            </a>
                        @endforeach
                    </div>

                    <button type="submit"
                            class="inline-flex items-center justify-center gap-2 rounded-lg bg-[#14161F]
                                   px-5 py-2.5 text-sm font-medium text-white transition hover:bg-[#2B2E3A]">
                        Apply filters
                    </button>
                </div>
            </form>
        </section>

        {{-- SUMMARY LEDGER --}}
        <section class="rounded-xl border border-[#E4E1D8] bg-white p-5 sm:p-6">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-[1fr_auto_1.3fr] lg:items-center">

                {{-- hero metric --}}
                <div>
                    <p class="text-xs font-medium text-[#8A8D97]">Meetings recorded</p>
                    <p class="num mt-1.5 text-[44px] font-semibold leading-none text-[#14161F]">
                        {{ number_format($stats['total_meetings']) }}
                    </p>
                    <p class="mt-2 text-xs text-[#8A8D97]">
                        across {{ $dailyBreakdown->count() }} active {{ Str::plural('day', $dailyBreakdown->count()) }}
                    </p>
                </div>

                <div class="hidden h-20 w-px bg-[#E4E1D8] lg:block"></div>

                {{-- supporting metrics --}}
                <div class="space-y-3.5">
                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-[#4B4F5B]">
                                <i class="fa-solid fa-user-group text-[11px] text-[#5B5FA6]"></i>
                                Unique users
                            </span>
                            <span class="num font-semibold text-[#14161F]">{{ number_format($stats['unique_users']) }}</span>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-[#4B4F5B]">
                                <i class="fa-solid fa-circle-check text-[11px] text-[#1E8577]"></i>
                                Completed
                            </span>
                            <span class="num font-semibold text-[#14161F]">
                                {{ number_format($stats['completed']) }} <span class="text-[#8A8D97]">({{ $completedPct }}%)</span>
                            </span>
                        </div>
                        <div class="bar-track mt-1.5 h-1.5 w-full overflow-hidden rounded-full">
                            <div class="h-full rounded-full bg-[#1E8577]" style="width: {{ $completedPct }}%"></div>
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="flex items-center gap-2 text-[#4B4F5B]">
                                <i class="fa-solid fa-ban text-[11px] text-[#B14A3E]"></i>
                                Cancelled
                            </span>
                            <span class="num font-semibold text-[#14161F]">
                                {{ number_format($stats['cancelled']) }} <span class="text-[#8A8D97]">({{ $cancelledPct }}%)</span>
                            </span>
                        </div>
                        <div class="bar-track mt-1.5 h-1.5 w-full overflow-hidden rounded-full">
                            <div class="h-full rounded-full bg-[#B14A3E]" style="width: {{ $cancelledPct }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- DAILY ACTIVITY --}}
        <section class="overflow-hidden rounded-xl border border-[#E4E1D8] bg-white">
            <div class="flex items-center justify-between border-b border-[#EFEDE6] px-5 py-4 sm:px-6">
                <div>
                    <h2 class="text-sm font-semibold text-[#14161F]">Daily activity</h2>
                    <p class="mt-0.5 text-xs text-[#8A8D97]">Meetings and unique users per active day</p>
                </div>
                <span class="num text-xs text-[#8A8D97]">{{ $dailyBreakdown->count() }} days</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[560px] text-sm">
                    <thead>
                    <tr class="text-xs font-medium text-[#8A8D97]">
                        <th class="px-6 py-3 text-left">Date</th>
                        <th class="px-6 py-3 text-left">Meetings</th>
                        <th class="px-6 py-3 text-right">Unique users</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-[#EFEDE6]">
                    @forelse($dailyBreakdown as $day)
                        <tr class="transition hover:bg-[#FBFAF7]">
                            <td class="px-6 py-3.5 align-middle">
                                <p class="font-medium text-[#14161F]">{{ $day['date']->format('M d, Y') }}</p>
                                <p class="text-xs text-[#8A8D97]">{{ $day['date']->format('l') }}</p>
                            </td>

                            <td class="px-6 py-3.5 align-middle">
                                <div class="flex items-center gap-3">
                                    <span class="num w-6 shrink-0 text-[#14161F]">{{ $day['meetings'] }}</span>
                                    <div class="bar-track h-1.5 w-28 max-w-[40%] overflow-hidden rounded-full">
                                        <div class="h-full rounded-full bg-[#1F3A66]"
                                             style="width: {{ min(100, round($day['meetings'] / $maxDailyMeetings * 100)) }}%"></div>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-3.5 text-right align-middle">
                                <span class="num text-[#4B4F5B]">{{ $day['users'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-sm text-[#8A8D97]">
                                No activity found for this date range.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        @php
            $statusColors = [
                'completed' => 'text-[#4F46E5]',
                'active' => 'text-[#1E8577]',
                'cancelled' => 'text-[#B14A3E]',
                'upcoming' => 'text-[#B07C1F]',
                'ended' => 'text-[#6B6F7A]',
                'flagged' => 'text-[#C2661A]',
            ];

            $dotColors = [
                'completed' => 'bg-[#4F46E5]',
                'active' => 'bg-[#1E8577] animate-pulse',
                'cancelled' => 'bg-[#B14A3E]',
                'upcoming' => 'bg-[#B07C1F]',
                'ended' => 'bg-[#8A8D97]',
                'flagged' => 'bg-[#C2661A]',
            ];
        @endphp

        {{-- STATUS FILTER --}}
        <section class="rounded-xl border border-[#E4E1D8] bg-white px-5 pt-1">
            <div class="flex items-center gap-5 overflow-x-auto no-scrollbar">
                @foreach(['All Status', 'Active', 'Upcoming', 'Completed', 'Cancelled'] as $opt)
                    @php
                        $isActive = request('status', 'All Status') === $opt;
                        $target = array_merge(request()->except('page'), ['status' => $opt]);
                    @endphp

                    <a href="{{ route('admin.reports.index', $target) }}"
                       class="tab-underline relative shrink-0 whitespace-nowrap py-3.5 text-sm font-medium transition
                              {{ $isActive ? 'is-active text-[#14161F]' : 'text-[#8A8D97] hover:text-[#4B4F5B]' }}">
                        {{ $opt }}
                    </a>
                @endforeach
            </div>
        </section>

        {{-- MEETINGS REPORT --}}
        <section id="reports-table" class="overflow-hidden rounded-xl border border-[#E4E1D8] bg-white">
            <div class="flex items-center justify-between gap-3 border-b border-[#EFEDE6] px-5 py-4 sm:px-6">
                <div>
                    <h2 class="text-sm font-semibold text-[#14161F]">Meeting details</h2>
                    <p class="mt-0.5 text-xs text-[#8A8D97]">
                        {{ $fromDate->format('M d, Y') }} to {{ $toDate->format('M d, Y') }}
                    </p>
                </div>

                <span class="num whitespace-nowrap text-xs text-[#8A8D97]">
                    {{ number_format($meetings->total()) }} records
                </span>
            </div>

            <div class="hidden overflow-x-auto md:block">
                <table class="w-full min-w-[760px] text-sm">
                    <thead>
                    <tr class="text-xs font-medium text-[#8A8D97]">
                        <th class="px-6 py-3 text-left">Meeting</th>
                        <th class="px-6 py-3 text-left">Organizer</th>
                        <th class="px-6 py-3 text-left">Schedule</th>
                        <th class="px-6 py-3 text-center">Participants</th>
                        <th class="px-6 py-3 text-left">Status</th>
                        <th class="px-6 py-3 text-center">View</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-[#EFEDE6]">
                    @forelse($meetings as $meeting)
                        <tr class="transition hover:bg-[#FBFAF7]">
                            <td class="max-w-[230px] px-6 py-4">
                                <p class="truncate font-medium text-[#14161F]">{{ $meeting->title }}</p>
                                <p class="num mt-0.5 text-xs text-[#8A8D97]">{{ $meeting->duration }} min</p>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex min-w-0 items-center gap-2.5">
                                    @if($meeting->organizer)
                                        <x-user-avatar :user="$meeting->organizer" size="sm" />
                                    @else
                                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-[#F0EEE7] text-xs text-[#8A8D97]">NA</div>
                                    @endif

                                    <span class="truncate text-sm text-[#4B4F5B]">
                                        {{ $meeting->organizer?->name ?? 'Unassigned' }}
                                    </span>
                                </div>
                            </td>

                            <td class="num px-6 py-4 whitespace-nowrap">
                                <p class="text-sm text-[#14161F]">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</p>
                                <p class="text-xs text-[#8A8D97]">{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</p>
                            </td>

                            <td class="num px-6 py-4 text-center text-[#4B4F5B]">
                                {{ $meeting->participants->count() }}
                            </td>

                            <td class="px-6 py-4">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium {{ $statusColors[$meeting->status] ?? 'text-[#6B6F7A]' }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $dotColors[$meeting->status] ?? 'bg-[#8A8D97]' }}"></span>
                                    {{ ucfirst($meeting->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.meetings.show', $meeting->id) }}"
                                   class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-[#8A8D97]
                                          transition hover:bg-[#F0EEE7] hover:text-[#1F3A66]">
                                    <i class="fa-regular fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-sm text-[#8A8D97]">No meetings found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE --}}
            <div class="divide-y divide-[#EFEDE6] md:hidden">
                @forelse($meetings as $meeting)
                    <div class="p-5">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate font-medium text-[#14161F]">{{ $meeting->title }}</p>
                                <p class="num mt-1 text-xs text-[#8A8D97]">
                                    {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }} —
                                    {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                                </p>
                            </div>

                            <span class="inline-flex shrink-0 items-center gap-1.5 text-xs font-medium {{ $statusColors[$meeting->status] ?? 'text-[#6B6F7A]' }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $dotColors[$meeting->status] ?? 'bg-[#8A8D97]' }}"></span>
                                {{ ucfirst($meeting->status) }}
                            </span>
                        </div>

                        <div class="mt-4 flex items-center justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-xs text-[#4B4F5B]">{{ $meeting->organizer?->name ?? 'Unassigned' }}</p>
                                <p class="num text-[11px] text-[#8A8D97]">
                                    {{ $meeting->participants->count() }} participants — {{ $meeting->duration }} min
                                </p>
                            </div>

                            <a href="{{ route('admin.meetings.show', $meeting->id) }}"
                               class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg text-[#8A8D97]
                                      transition hover:bg-[#F0EEE7] hover:text-[#1F3A66]">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-sm text-[#8A8D97]">No meetings found.</div>
                @endforelse
            </div>

            @if($meetings->hasPages())
                <div class="border-t border-[#EFEDE6] bg-[#FBFAF7] px-5 py-4">
                    {{ $meetings->links() }}
                </div>
            @endif
        </section>
    </div>
</x-layouts.app>
