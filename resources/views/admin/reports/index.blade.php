<x-layouts.app>
    <x-slot name="header">
        <x-header.search-bar placeholder="Search reports, meetings, users..." />
    </x-slot>

    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 overflow-y-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Reports & Analytics</h1>
                <p class="text-xs sm:text-sm text-gray-400 mt-0.5">Meeting performance and platform activity at a glance.</p>
            </div>
            <a href="{{ route('admin.reports.export', request()->query()) }}"
               class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2.5 rounded-xl transition shadow-sm w-full sm:w-auto">
                <i class="fa-solid fa-file-pdf text-xs"></i> Export PDF
            </a>
        </div>

        {{-- Only the four metrics that are most useful on a report overview --}}
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-4">
            @foreach([
                ['Total Meetings', $stats['total_meetings'], 'fa-calendar-days', 'bg-blue-50 text-blue-600'],
                ['Completed', $stats['completed'], 'fa-circle-check', 'bg-green-50 text-green-600'],
                ['Cancelled', $stats['cancelled'], 'fa-ban', 'bg-red-50 text-red-500'],
                ['Total Users', $stats['total_users'], 'fa-users', 'bg-purple-50 text-purple-600'],
            ] as [$label, $value, $icon, $color])
                <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
                    <div class="w-9 h-9 rounded-xl {{ $color }} flex items-center justify-center">
                        <i class="fa-solid {{ $icon }} text-sm"></i>
                    </div>
                    <p class="text-2xl font-bold text-gray-800 mt-3">{{ number_format($value) }}</p>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $label }}</p>
                </div>
            @endforeach
        </div>

        {{-- Compact status filter --}}
        <div class="bg-white border border-gray-200 rounded-2xl px-3 sm:px-4 py-3 shadow-sm mb-4">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar">
                <span class="flex items-center gap-1.5 text-xs font-semibold text-gray-500 shrink-0 mr-1">
                    <i class="fa-solid fa-filter text-blue-500"></i> Filter
                </span>
                @foreach(['All Status', 'Active', 'Upcoming', 'Completed', 'Cancelled'] as $opt)
                    @php
                        $isActive = request('status', 'All Status') === $opt;
                        $target = array_merge(request()->except('page'), ['status' => $opt]);
                    @endphp
                    <a href="{{ route('admin.reports.index', $target) }}"
                       class="shrink-0 px-3 py-1.5 rounded-lg text-xs font-medium border transition {{ $isActive ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50' }}">
                        {{ $opt }}
                    </a>
                @endforeach
            </div>
        </div>

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

        <div id="reports-table" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-3.5 border-b border-gray-100 flex items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-gray-800 text-sm sm:text-base">Meetings Report</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Meeting records and current status</p>
                </div>
                <span class="text-xs text-gray-400 whitespace-nowrap">{{ number_format($meetings->total()) }} records</span>
            </div>

            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm min-w-[760px]">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-[11px] font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left">Meeting</th>
                        <th class="px-5 py-3 text-left">Organizer</th>
                        <th class="px-5 py-3 text-left">Schedule</th>
                        <th class="px-5 py-3 text-center">Participants</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-center">View</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($meetings as $meeting)
                        <tr class="hover:bg-gray-50/70 transition">
                            <td class="px-5 py-3.5 max-w-[230px]">
                                <p class="font-semibold text-gray-800 truncate">{{ $meeting->title }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $meeting->duration }} min</p>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    @if($meeting->organizer)
                                        <x-user-avatar :user="$meeting->organizer" size="sm" />
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center text-xs">NA</div>
                                    @endif
                                    <span class="text-sm text-gray-700 truncate">{{ $meeting->organizer?->name ?? 'Unassigned' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-3.5 whitespace-nowrap">
                                <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</p>
                            </td>
                            <td class="px-5 py-3.5 text-center">
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-600 px-2.5 py-1 rounded-lg text-xs font-medium">
                                        <i class="fa-solid fa-users text-[10px]"></i> {{ $meeting->participants->count() }}
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
                                   class="inline-flex w-8 h-8 items-center justify-center rounded-lg text-gray-400 hover:text-blue-600 hover:bg-blue-50 transition"
                                   title="View meeting">
                                    <i class="fa-regular fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-sm text-gray-400">No meetings found.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Existing responsive behavior retained as compact cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($meetings as $meeting)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="font-semibold text-gray-800 truncate">{{ $meeting->title }}</p>
                                <p class="text-xs text-gray-400 mt-1">
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
                                <p class="text-xs text-gray-700 truncate">{{ $meeting->organizer?->name ?? 'Unassigned' }}</p>
                                <p class="text-[11px] text-gray-400">{{ $meeting->participants->count() }} participants · {{ $meeting->duration }} min</p>
                            </div>
                            <a href="{{ route('admin.meetings.show', $meeting->id) }}"
                               class="w-8 h-8 shrink-0 flex items-center justify-center rounded-lg bg-gray-50 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition">
                                <i class="fa-regular fa-eye text-xs"></i>
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="p-10 text-center text-sm text-gray-400">No meetings found.</div>
                @endforelse
            </div>

            @if($meetings->hasPages())
                <div class="px-4 py-3 border-t border-gray-100 bg-gray-50/50">
                    {{ $meetings->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
