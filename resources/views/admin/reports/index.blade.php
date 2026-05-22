<x-layouts.app>

    <x-slot name="header">
        <x-header.search-bar placeholder="Search reports, meetings, users..." />
    </x-slot>

    {{-- Page Content --}}
    <div class="p-3 sm:p-4 space-y-4 p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">

        {{-- ═══════════════════════════════════════════
             PAGE TITLE
        ════════════════════════════════════════════ --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-gray-800">Reports & Analytics</h1>
                <p class="text-xs sm:text-sm text-gray-400 mt-0.5">Platform-wide insights, metrics, and export tools.</p>
            </div>

            {{-- Export Dropdown --}}
            <div x-data="{ open: false }" class="relative self-start sm:self-auto">
                <button @click="open = !open"
                        class="flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm px-4 py-2.5 rounded-xl transition shadow-sm whitespace-nowrap">
                    <i class="fa-solid fa-download text-xs"></i>
                    Export Report
                    <i class="fa-solid fa-chevron-down text-xs"></i>
                </button>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             STATS CARDS
        ════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-6 gap-2 sm:gap-3">

            @php
                $stats = [
                    ['label' => 'Total Meetings',    'value' => '1,284', 'change' => '+12%', 'up' => true,  'icon' => 'fa-calendar',       'color' => 'blue'],
                    ['label' => 'Active Now',         'value' => '42',    'change' => '+3',   'up' => true,  'icon' => 'fa-circle-dot',     'color' => 'green'],
                    ['label' => 'Completed',          'value' => '986',   'change' => '+8%',  'up' => true,  'icon' => 'fa-circle-check',   'color' => 'indigo'],
                    ['label' => 'Cancelled',          'value' => '58',    'change' => '-4%',  'up' => false, 'icon' => 'fa-ban',            'color' => 'red'],
                    ['label' => 'Upcoming',           'value' => '214',   'change' => '+18%', 'up' => true,  'icon' => 'fa-clock',          'color' => 'yellow'],
                    ['label' => 'Total Users',        'value' => '8,432', 'change' => '+5%',  'up' => true,  'icon' => 'fa-users',          'color' => 'purple'],
                    ['label' => 'Active Users',       'value' => '6,210', 'change' => '+2%',  'up' => true,  'icon' => 'fa-user-check',     'color' => 'teal'],
                    ['label' => 'Inactive Users',     'value' => '2,222', 'change' => '+1%',  'up' => false, 'icon' => 'fa-user-slash',     'color' => 'orange'],
                    ['label' => 'Organizers',         'value' => '312',   'change' => '+6',   'up' => true,  'icon' => 'fa-user-tie',       'color' => 'blue'],
                    ['label' => 'Participants',       'value' => '8,120', 'change' => '+4%',  'up' => true,  'icon' => 'fa-people-group',   'color' => 'green'],
                    ['label' => 'Created Today',      'value' => '24',    'change' => '+8',   'up' => true,  'icon' => 'fa-calendar-plus',  'color' => 'indigo'],
                    ['label' => 'Completed Today',    'value' => '17',    'change' => '+3',   'up' => true,  'icon' => 'fa-calendar-check', 'color' => 'purple'],
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

            @foreach($stats as $stat)
                @php $c = $colorMap[$stat['color']]; @endphp
                <div class="bg-white border border-gray-200 rounded-2xl p-3 sm:p-4 shadow-sm hover:shadow-md transition group">
                    <div class="flex items-center justify-between mb-2 sm:mb-3">
                        <div class="{{ $c['bg'] }} p-1.5 sm:p-2 rounded-xl">
                            <i class="fa-solid {{ $stat['icon'] }} {{ $c['icon'] }} text-xs sm:text-sm"></i>
                        </div>
                        <span class="text-xs font-medium px-1.5 sm:px-2 py-0.5 rounded-full
                            {{ $stat['up'] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }}">
                            {{ $stat['change'] }}
                        </span>
                    </div>
                    <p class="text-lg sm:text-2xl font-bold text-gray-800">{{ $stat['value'] }}</p>
                    <p class="text-xs text-gray-400 mt-1 leading-tight">{{ $stat['label'] }}</p>
                </div>
            @endforeach

        </div>

        {{-- ═══════════════════════════════════════════
             FILTERS
        ════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 rounded-2xl px-4 py-3 shadow-sm">
            <div class="flex flex-wrap items-end gap-3">
                <div class="flex items-center gap-1.5 text-sm font-semibold text-gray-600 shrink-0 pb-1">
                    <i class="fa-solid fa-sliders text-blue-500 text-xs"></i> Filters
                </div>

                <div class="flex flex-col gap-1 flex-1 min-w-[130px]">
                    <label class="text-xs text-gray-400">Date Range</label>
                    <input type="date" class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-full">
                </div>

                <div class="flex flex-col gap-1 flex-1 min-w-[120px]">
                    <label class="text-xs text-gray-400">Status</label>
                    <select class="text-xs border border-gray-200 rounded-lg px-2.5 py-1.5 focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400 w-full">
                        <option>All Status</option>
                        <option>Active</option>
                        <option>Upcoming</option>
                        <option>Completed</option>
                        <option>Cancelled</option>
                    </select>
                </div>

                <div class="flex items-end gap-2 shrink-0">
                    <button class="bg-blue-600 hover:bg-blue-700 text-white text-xs px-4 py-1.5 rounded-lg transition shadow-sm">
                        Apply
                    </button>
                </div>
            </div>
        </div>

        {{-- ═══════════════════════════════════════════
             MEETINGS TABLE
        ════════════════════════════════════════════ --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">

            <div class="px-4 sm:px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h2 class="font-semibold text-gray-800">Meetings Report</h2>
                    <p class="text-xs text-gray-400 mt-0.5">All meetings with full details</p>
                </div>
                <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-2 w-full sm:w-auto">
                    <i class="fa-solid fa-magnifying-glass text-gray-400 text-xs"></i>
                    <input type="text" placeholder="Search meetings..." class="text-xs outline-none flex-1 sm:w-48">
                </div>
            </div>

            {{-- Desktop Table --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
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
                    @php
                        $rows = [
                            ['Q4 Product Vision Sync',  'Alex Rivera',  'Oct 24, 2023', '10:00 AM', 12, 'Completed', 'bg-indigo-50 text-indigo-600 border-indigo-100'],
                            ['Weekly Design Critique',  'Sarah Chen',   'Oct 23, 2023', '02:00 PM', 8,  'Active',    'bg-green-50 text-green-700 border-green-100'],
                            ['Stakeholder Feedback',    'Marcus T.',    'Oct 21, 2023', '09:00 AM', 20, 'Completed', 'bg-indigo-50 text-indigo-600 border-indigo-100'],
                            ['Budget Q4 Review',        'Ali Khan',     'Oct 24, 2023', '11:00 AM', 5,  'Cancelled', 'bg-red-50 text-red-600 border-red-100'],
                            ['All Hands — Oct',         'Thomson H.',   'Oct 21, 2023', '09:00 AM', 15, 'Upcoming',  'bg-yellow-50 text-yellow-700 border-yellow-100'],
                        ];
                    @endphp
                    @foreach($rows as $row)
                        <tr class="hover:bg-blue-50/30 transition duration-150">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800">{{ $row[0] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">60 min duration</p>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 text-xs font-bold flex items-center justify-center shrink-0">
                                        {{ strtoupper(substr($row[1], 0, 2)) }}
                                    </div>
                                    <span class="text-sm text-gray-700">{{ $row[1] }}</span>
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <p class="text-sm text-gray-700">{{ $row[2] }}</p>
                                <p class="text-xs text-gray-400">{{ $row[3] }}</p>
                            </td>
                            <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 bg-gray-100 text-gray-700 px-3 py-1 rounded-xl text-xs font-medium">
                                        <i class="fa-solid fa-user text-[10px]"></i> {{ $row[4] }}
                                    </span>
                            </td>
                            <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold border {{ $row[6] }}">
                                        <span class="w-1.5 h-1.5 rounded-full
                                            {{ $row[5] == 'Active' ? 'bg-green-500 animate-pulse' : '' }}
                                            {{ $row[5] == 'Completed' ? 'bg-indigo-500' : '' }}
                                            {{ $row[5] == 'Cancelled' ? 'bg-red-400' : '' }}
                                            {{ $row[5] == 'Upcoming' ? 'bg-yellow-400' : '' }}
                                        "></span>
                                        {{ $row[5] }}
                                    </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2 text-gray-400">
                                    <button class="p-1.5 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition" title="View">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </button>
                                    <button class="p-1.5 hover:bg-red-50 hover:text-red-500 rounded-lg transition" title="Cancel">
                                        <i class="fa-solid fa-ban text-xs"></i>
                                    </button>
                                    <button class="p-1.5 hover:bg-orange-50 hover:text-orange-500 rounded-lg transition" title="Flag">
                                        <i class="fa-solid fa-flag text-xs"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Mobile Cards --}}
            <div class="md:hidden divide-y divide-gray-100">
                @php
                    $rows = [
                        ['Q4 Product Vision Sync',  'Alex Rivera',  'Oct 24, 2023', '10:00 AM', 12, 'Completed', 'bg-indigo-50 text-indigo-600 border-indigo-100'],
                        ['Weekly Design Critique',  'Sarah Chen',   'Oct 23, 2023', '02:00 PM', 8,  'Active',    'bg-green-50 text-green-700 border-green-100'],
                        ['Stakeholder Feedback',    'Marcus T.',    'Oct 21, 2023', '09:00 AM', 20, 'Completed', 'bg-indigo-50 text-indigo-600 border-indigo-100'],
                        ['Budget Q4 Review',        'Ali Khan',     'Oct 24, 2023', '11:00 AM', 5,  'Cancelled', 'bg-red-50 text-red-600 border-red-100'],
                        ['All Hands — Oct',         'Thomson H.',   'Oct 21, 2023', '09:00 AM', 15, 'Upcoming',  'bg-yellow-50 text-yellow-700 border-yellow-100'],
                    ];
                @endphp
                @foreach($rows as $row)
                    <div class="p-4 hover:bg-blue-50/20 transition">
                        {{-- Top Row: Title + Status --}}
                        <div class="flex items-start justify-between gap-2 mb-3">
                            <div>
                                <p class="font-semibold text-gray-800 text-sm">{{ $row[0] }}</p>
                                <p class="text-xs text-gray-400 mt-0.5">60 min duration</p>
                            </div>
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold border shrink-0 {{ $row[6] }}">
                                <span class="w-1.5 h-1.5 rounded-full
                                    {{ $row[5] == 'Active' ? 'bg-green-500 animate-pulse' : '' }}
                                    {{ $row[5] == 'Completed' ? 'bg-indigo-500' : '' }}
                                    {{ $row[5] == 'Cancelled' ? 'bg-red-400' : '' }}
                                    {{ $row[5] == 'Upcoming' ? 'bg-yellow-400' : '' }}
                                "></span>
                                {{ $row[5] }}
                            </span>
                        </div>

                        {{-- Meta Row: Organizer + Date + Participants --}}
                        <div class="flex flex-wrap items-center gap-3 text-xs text-gray-500 mb-3">
                            <div class="flex items-center gap-1.5">
                                <div class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 text-[10px] font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($row[1], 0, 2)) }}
                                </div>
                                <span>{{ $row[1] }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-calendar text-gray-300 text-[10px]"></i>
                                <span>{{ $row[2] }}, {{ $row[3] }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <i class="fa-solid fa-user text-gray-300 text-[10px]"></i>
                                <span>{{ $row[4] }} participants</span>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex items-center gap-2 text-gray-400">
                            <button class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-gray-50 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition border border-gray-100">
                                <i class="fa-solid fa-eye text-[10px]"></i> View
                            </button>
                            <button class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-gray-50 hover:bg-red-50 hover:text-red-500 rounded-lg transition border border-gray-100">
                                <i class="fa-solid fa-ban text-[10px]"></i> Cancel
                            </button>
                            <button class="flex items-center gap-1.5 text-xs px-3 py-1.5 bg-gray-50 hover:bg-orange-50 hover:text-orange-500 rounded-lg transition border border-gray-100">
                                <i class="fa-solid fa-flag text-[10px]"></i> Flag
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Pagination --}}
            <div class="px-4 sm:px-5 py-4 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs text-gray-400">Showing 1–5 of 1,284 meetings</p>
                <div class="flex items-center gap-1">
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition text-xs">
                        <i class="fa-solid fa-chevron-left"></i>
                    </button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-600 text-white text-xs font-medium">1</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition text-xs">2</button>
                    <button class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 text-gray-500 hover:bg-gray-50 transition text-xs">
                        <i class="fa-solid fa-chevron-right"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
