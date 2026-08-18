<x-layouts.app>
    <x-slot name="header">
        {{-- This component already submits (live, debounced) to url()->current(),
             so on this page it naturally searches meetings/reports. No extra props needed. --}}
        <x-header.search-bar placeholder="Search reports, meetings, users..." />
    </x-slot>

    {{-- PAGE CONTENT --}}
    <div class="p-3 sm:p-4 space-y-4 bg-gray-50 rounded-2xl m-2 mt-0 overflow-y-auto">

        {{-- PAGE TITLE --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-lg sm:text-xl md:text-2xl font-bold text-gray-800 truncate">Reports & Analytics</h1>
                <p class="text-xs sm:text-sm text-gray-400 mt-0.5">Platform-wide insights, metrics, and export tools.</p>
            </div>
            {{-- Export (PDF only, simple direct download link) --}}
            <a href="{{ route('admin.reports.export', request()->query()) }}"
               class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2.5 rounded-xl transition shadow-sm whitespace-nowrap w-full sm:w-auto self-stretch sm:self-auto">
                <i class="fa-solid fa-file-pdf text-xs"></i>
                Export as PDF
            </a>
        </div>

        {{-- STATS CARDS (dynamic from controller) --}}
        @php
            $statCards = [
                ['label' => 'Total Meetings',   'value' => number_format($stats['total_meetings']),   'change' => $changes['total_meetings']  ?? null, 'icon' => 'fa-calendar',       'color' => 'blue'],
                ['label' => 'Active Now',       'value' => number_format($stats['active_now']),       'change' => null,                                'icon' => 'fa-circle-dot',     'color' => 'green'],
                ['label' => 'Completed',        'value' => number_format($stats['completed']),        'change' => $changes['completed']        ?? null, 'icon' => 'fa-circle-check',   'color' => 'indigo'],
                ['label' => 'Cancelled',        'value' => number_format($stats['cancelled']),        'change' => $changes['cancelled']        ?? null, 'icon' => 'fa-ban',            'color' => 'red'],
                ['label' => 'Upcoming',         'value' => number_format($stats['upcoming']),         'change' => $changes['upcoming']         ?? null, 'icon' => 'fa-clock',          'color' => 'yellow'],
                ['label' => 'Total Users',      'value' => number_format($stats['total_users']),      'change' => $changes['total_users']      ?? null, 'icon' => 'fa-users',          'color' => 'purple'],
                ['label' => 'Active Users',     'value' => number_format($stats['active_users']),     'change' => $changes['active_users']     ?? null, 'icon' => 'fa-user-check',     'color' => 'teal'],
                ['label' => 'Inactive Users',   'value' => number_format($stats['inactive_users']),   'change' => $changes['inactive_users']   ?? null, 'icon' => 'fa-user-slash',     'color' => 'orange'],
                ['label' => 'Organizers',       'value' => number_format($stats['organizers']),       'change' => $changes['organizers']       ?? null, 'icon' => 'fa-user-tie',       'color' => 'blue'],
                ['label' => 'Participants',     'value' => number_format($stats['participants']),     'change' => $changes['participants']     ?? null, 'icon' => 'fa-people-group',   'color' => 'green'],
                ['label' => 'Created Today',    'value' => number_format($stats['created_today']),    'change' => null,                                'icon' => 'fa-calendar-plus',  'color' => 'indigo'],
                ['label' => 'Completed Today',  'value' => number_format($stats['completed_today']),  'change' => null,                                'icon' => 'fa-calendar-check', 'color' => 'purple'],
            ];
            $colorMap = [
                'blue'   => ['bg' => 'bg-blue-50',   'icon' => 'text-blue-500',   'badge' => 'bg-blue-100 text-blue-600'],
                'green'  => ['bg' => 'bg-green-50',  'icon' => 'text-green-500',  'badge' => 'bg-green-100 text-green-600'],
                'indigo' => ['bg' => 'bg-indigo-50', 'icon' => 'text-indigo-500', 'badge' => 'bg-indigo-100 text-indigo-600'],
                'red'    => ['bg' => 'bg-red-50',    'icon' => 'text-red-500',    'badge' => 'bg-red-100 text-red-600'],
                'yellow' => ['bg' => 'bg-yellow-50', 'icon' => 'text-yellow-500', 'badge' => 'bg-yellow-100 text-yellow-600'],
                'purple' => ['bg' => 'bg-purple-50', 'icon' => 'text-purple-500', 'badge' => 'bg-purple-100 text-purple-600'],
                'teal'   => ['bg' => 'bg-teal-50',   'icon' => 'text-teal-500',   'badge' => 'bg-teal-100 text-teal-600'],
                'orange' => ['bg' => 'bg-orange-50', 'icon' => 'text-orange-500', 'badge' => 'bg-orange-100 text-orange-600'],
            ];
        @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-2 sm:gap-3">
            @foreach($statCards as $stat)
                @php
                    $c  = $colorMap[$stat['color']];
                    $up = $stat['change'] ? !str_starts_with($stat['change'], '-') : true;
                @endphp
                <div class="bg-white border border-gray-200 rounded-2xl p-3 sm:p-4 shadow-sm hover:shadow-md transition group min-w-0">
                    <div class="flex items-center justify-between mb-2 sm:mb-3 gap-1">
                        <div class="{{ $c['bg'] }} p-1.5 sm:p-2 rounded-xl shrink-0">
                            <i class="fa-solid {{ $stat['icon'] }} {{ $c['icon'] }} text-xs sm:text-sm"></i>
                        </div>
                        @if($stat['change'])
                            <span class="text-[10px] sm:text-xs font-medium px-1.5 sm:px-2 py-0.5 rounded-full whitespace-nowrap
                                {{ $up ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                                {{ $stat['change'] }}
                            </span>
                        @endif
                    </div>
                    <p class="text-base sm:text-lg md:text-2xl font-bold text-gray-800 truncate">{{ $stat['value'] }}</p>
                    <p class="text-[11px] sm:text-xs text-gray-400 mt-1 leading-tight">{{ $stat['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- STATUS FILTER (pill buttons, horizontal scroll on small screens) --}}
        <div class="bg-white border border-gray-200 rounded-2xl px-3 sm:px-4 py-3 shadow-sm">
            <div class="flex items-center gap-2 overflow-x-auto no-scrollbar pb-1 sm:pb-0 sm:flex-wrap">
                <div class="flex items-center gap-1.5 text-sm font-semibold text-gray-600 shrink-0 pr-1">
                    <i class="fa-solid fa-sliders text-blue-500 text-xs"></i> Status
                </div>
                @foreach(['All Status', 'Active', 'Upcoming', 'Completed', 'Cancelled'] as $opt)
                    @php
                        $isActive = request('status', 'All Status') == $opt;
                        $target = array_merge(request()->except(['page']), ['status' => $opt]);
                    @endphp
                    <a href="{{ route('admin.reports.index', $target) }}"
                       class="filter-link shrink-0 px-3 py-1.5 rounded-full text-xs font-semibold border transition whitespace-nowrap
                           {{ $isActive
                               ? 'bg-blue-600 text-white border-blue-600 shadow-sm'
                               : 'bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:border-gray-300' }}">
                        {{ $opt }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- MEETINGS TABLE --}}
        <div id="reports-table" class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-4 sm:px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h2 class="font-semibold text-gray-800 text-sm sm:text-base">Meetings Report</h2>
                <p class="text-xs text-gray-400 mt-0.5">All meetings with full details</p>
            </div>
            @php
                $statusColors = [
                    'completed' => 'bg-indigo-50 text-indigo-600 border-indigo-100',
                    'active'    => 'bg-green-50 text-green-700 border-green-100',
                    'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                    'upcoming'  => 'bg-yellow-50 text-yellow-700 border-yellow-100',
                ];
                $dotColors = [
                    'active'    => 'bg-green-500 animate-pulse',
                    'completed' => 'bg-indigo-500',
                    'cancelled' => 'bg-red-400',
                    'upcoming'  => 'bg-yellow-400',
                ];
            @endphp

            {{-- Desktop / Tablet Table (scrolls horizontally instead of breaking layout) --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm min-w-[720px]">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                        <th class="px-5 py-3 text-left">Meeting</th>
                        <th class="px-5 py-3 text-left">Organizer</th>
                        <th class="px-5 py-3 text-left">Date & Time</th>
                        <th class="px-5 py-3 text-left">Participants</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                    @forelse($meetings as $meeting)
                        <tr class="hover:bg-blue-50/30 transition duration-150">
                            <td class="px-5 py-4 max-w-[220px]">
                                <div class="flex items-center gap-2 min-w-0">
                                    <p class="font-semibold text-gray-800 truncate">{{ $meeting->title }}</p>
                                    @if($meeting->is_flagged)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 text-red-500 shrink-0" title="Flagged">
                                            <path d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054a8.25 8.25 0 0 0 5.58.652l3.109-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $meeting->duration }} min duration</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2 min-w-0">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 text-xs font-bold flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($meeting->organizer)
                                            <img src="{{ $meeting->organizer->image_url }}" alt="{{ $meeting->organizer->name }}" class="w-full h-full object-cover">
                                        @else
                                            NA
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-700 truncate">{{ $meeting->organizer->name ?? 'Unassigned' }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4 whitespace-nowrap">
                                <p class="text-sm text-gray-700">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</p>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 px-3 py-1 rounded-xl text-xs font-medium whitespace-nowrap">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 shrink-0">
                                        <path fill-rule="evenodd" d="M8.25 6.75a3.75 3.75 0 1 1 7.5 0 3.75 3.75 0 0 1-7.5 0ZM15.75 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM2.25 9.75a3 3 0 1 1 6 0 3 3 0 0 1-6 0ZM6.31 15.117A6.745 6.745 0 0 1 12 12a6.745 6.745 0 0 1 6.709 7.498.75.75 0 0 1-.372.568A12.696 12.696 0 0 1 12 21.75c-2.305 0-4.47-.612-6.337-1.684a.75.75 0 0 1-.372-.568 6.787 6.787 0 0 1 1.019-4.38Z" clip-rule="evenodd" />
                                    </svg>
                                    {{ $meeting->participants->count() }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border whitespace-nowrap {{ $statusColors[$meeting->status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                    <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $dotColors[$meeting->status] ?? 'bg-gray-400' }}"></span>
                                    {{ ucfirst($meeting->status) }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-1.5 text-gray-400">
                                    {{-- VIEW (eye icon) --}}
                                    <a href="{{ route('admin.meetings.show', $meeting->id) }}"
                                       class="p-1.5 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition" title="View details">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                        </svg>
                                    </a>
                                    {{-- CANCEL (only if upcoming/active) --}}
                                    @if(in_array($meeting->status, ['upcoming', 'active']))
                                        <form action="{{ route('admin.meetings.cancel', $meeting->id) }}" method="POST" onsubmit="return confirm('Cancel this meeting?')">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="p-1.5 hover:bg-red-50 hover:text-red-500 rounded-lg transition" title="Cancel meeting">
                                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                                </svg>
                                            </button>
                                        </form>
                                    @endif
                                    {{-- FLAG (only available for upcoming meetings) --}}
                                    @if($meeting->status === 'upcoming')
                                        <form action="{{ route('admin.meetings.flag', $meeting->id) }}" method="POST"
                                              onsubmit="return confirm('{{ $meeting->is_flagged ? 'Remove flag from this meeting?' : 'Flag this meeting for review?' }}')">
                                            @csrf
                                            <button type="submit"
                                                    class="p-1.5 rounded-lg transition {{ $meeting->is_flagged ? 'text-red-500 hover:bg-red-50' : 'hover:bg-orange-50 hover:text-orange-500' }}"
                                                    title="{{ $meeting->is_flagged ? 'Remove flag' : 'Flag for review' }}">
                                                @if($meeting->is_flagged)
                                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-4 h-4">
                                                        <path d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054a8.25 8.25 0 0 0 5.58.652l3.109-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" />
                                                    </svg>
                                                @else
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21V6.75m0 0A2.25 2.25 0 0 1 5.25 4.5h6.879a1.5 1.5 0 0 1 1.06.44l1.622 1.62a1.5 1.5 0 0 0 1.06.44h4.129a2.25 2.25 0 0 1 2.25 2.25v6.5a2.25 2.25 0 0 1-2.25 2.25H16.5a1.5 1.5 0 0 0-1.06.44l-1.63 1.63a1.5 1.5 0 0 1-1.062.44H5.25a2.25 2.25 0 0 1-2.25-2.25V6.75Z" />
                                                    </svg>
                                                @endif
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-400 text-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 mx-auto mb-2 text-gray-300">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008Z" />
                                </svg>
                                No meeting here...
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @forelse($meetings as $meeting)
                    <div class="p-3 sm:p-4 hover:bg-blue-50/20 transition">
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2 min-w-0">
                                    <p class="font-semibold text-gray-800 text-sm truncate">{{ $meeting->title }}</p>
                                    @if($meeting->is_flagged)
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3 h-3 text-red-500 shrink-0">
                                            <path d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054a8.25 8.25 0 0 0 5.58.652l3.109-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" />
                                        </svg>
                                    @endif
                                </div>
                                <p class="text-xs text-gray-400 mt-0.5">{{ $meeting->duration }} min duration</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border shrink-0 whitespace-nowrap {{ $statusColors[$meeting->status] ?? 'bg-gray-50 text-gray-600 border-gray-100' }}">
                                <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $dotColors[$meeting->status] ?? 'bg-gray-400' }}"></span>
                                {{ ucfirst($meeting->status) }}
                            </span>
                        </div>
                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-gray-500 mb-3">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-[10px] font-bold flex items-center justify-center shrink-0 overflow-hidden">
                                    @if($meeting->organizer)
                                        <img src="{{ $meeting->organizer->image_url }}" alt="{{ $meeting->organizer->name }}" class="w-full h-full object-cover">
                                    @else
                                        NA
                                    @endif
                                </div>
                                <span class="truncate">{{ $meeting->organizer->name ?? 'Unassigned' }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5 text-gray-300 shrink-0">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75"/>
                                </svg>
                                <span class="whitespace-nowrap">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}, {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5 text-gray-300 shrink-0">
                                    <path fill-rule="evenodd" d="M7.5 6a4.5 4.5 0 1 1 9 0 4.5 4.5 0 0 1-9 0ZM3.751 20.105a8.25 8.25 0 0 1 16.498 0 .75.75 0 0 1-.437.695A18.683 18.683 0 0 1 12 22.5c-2.786 0-5.433-.608-7.812-1.7a.75.75 0 0 1-.437-.695Z" clip-rule="evenodd" />
                                </svg>
                                <span class="whitespace-nowrap">{{ $meeting->participants->count() }} participants</span>
                            </div>
                        </div>
                        <div class="flex flex-wrap items-center gap-2 text-gray-400">
                            <a href="{{ route('admin.meetings.show', $meeting->id) }}" class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-gray-50 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition border border-gray-100">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                </svg>
                                View
                            </a>
                            @if(in_array($meeting->status, ['upcoming', 'active']))
                                <form action="{{ route('admin.meetings.cancel', $meeting->id) }}" method="POST" onsubmit="return confirm('Cancel this meeting?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-gray-50 hover:bg-red-50 hover:text-red-500 rounded-lg transition border border-gray-100">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 0 0 5.636 5.636m12.728 12.728A9 9 0 0 1 5.636 5.636m12.728 12.728L5.636 5.636" />
                                        </svg>
                                        Cancel
                                    </button>
                                </form>
                            @endif
                            @if($meeting->status === 'upcoming')
                                <form action="{{ route('admin.meetings.flag', $meeting->id) }}" method="POST"
                                      onsubmit="return confirm('{{ $meeting->is_flagged ? 'Remove flag from this meeting?' : 'Flag this meeting for review?' }}')">
                                    @csrf
                                    <button type="submit" class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-gray-50 rounded-lg transition border border-gray-100 {{ $meeting->is_flagged ? 'text-red-500 hover:bg-red-50' : 'hover:bg-orange-50 hover:text-orange-500' }}">
                                        @if($meeting->is_flagged)
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-3.5 h-3.5">
                                                <path d="M3 2.25a.75.75 0 0 1 .75.75v.54l1.838-.46a9.75 9.75 0 0 1 6.725.738l.108.054a8.25 8.25 0 0 0 5.58.652l3.109-.732a.75.75 0 0 1 .917.81 47.784 47.784 0 0 0 .005 10.337.75.75 0 0 1-.574.812l-3.114.733a9.75 9.75 0 0 1-6.594-.77l-.108-.054a8.25 8.25 0 0 0-5.69-.625l-2.202.55V21a.75.75 0 0 1-1.5 0V3A.75.75 0 0 1 3 2.25Z" />
                                            </svg>
                                        @else
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3.5 h-3.5">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 3v1.5M3 21V6.75m0 0A2.25 2.25 0 0 1 5.25 4.5h6.879a1.5 1.5 0 0 1 1.06.44l1.622 1.62a1.5 1.5 0 0 0 1.06.44h4.129a2.25 2.25 0 0 1 2.25 2.25v6.5a2.25 2.25 0 0 1-2.25 2.25H16.5a1.5 1.5 0 0 0-1.06.44l-1.63 1.63a1.5 1.5 0 0 1-1.062.44H5.25a2.25 2.25 0 0 1-2.25-2.25V6.75Z" />
                                            </svg>
                                        @endif
                                        {{ $meeting->is_flagged ? 'Unflag' : 'Flag' }}
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10 text-gray-400 text-sm">
                        No meeting here...
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div id="pagination-wrapper" class="px-3 sm:px-5 py-4 border-t border-gray-100 overflow-x-auto">
                <div class="flex justify-center min-w-max">
                    {{ $meetings->links() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Hide scrollbar for the status-filter horizontal scroll on small screens --}}
    <style>
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    {{-- SCROLL FIX --
         - Searching (header search bar) -> page scrolls to the very top.
         - Paginating (next/prev/page number) -> page scrolls down to the meetings table.
    --}}
    <script>
        (function () {
            const STORAGE_KEY = 'reportsScrollTarget';
            document.addEventListener('click', function (e) {
                const link = e.target.closest('#pagination-wrapper a');
                if (link) sessionStorage.setItem(STORAGE_KEY, 'table');
            });
            document.addEventListener('click', function (e) {
                const link = e.target.closest('a.filter-link');
                if (link) sessionStorage.setItem(STORAGE_KEY, 'table');
            });
            document.addEventListener('submit', function (e) {
                if (e.target.id === 'live-search-form') {
                    sessionStorage.setItem(STORAGE_KEY, 'table');
                }
            });
            document.addEventListener('DOMContentLoaded', function () {
                const target = sessionStorage.getItem(STORAGE_KEY);
                sessionStorage.removeItem(STORAGE_KEY);
                if (target === 'table') {
                    const table = document.getElementById('reports-table');
                    if (table) table.scrollIntoView({ behavior: 'auto', block: 'start' });
                }
            });
        })();
    </script>
</x-layouts.app>
