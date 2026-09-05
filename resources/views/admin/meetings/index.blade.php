<x-layouts.app>
    <x-slot name="header">
        <x-header.search-bar placeholder="Search meetings..." />
    </x-slot>

    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">Manage Meetings</h1>
                <p class="text-gray-400 mt-1 text-sm sm:text-base">Monitor, moderate, and oversee all meetings.</p>
            </div>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white border border-blue-100 rounded-2xl px-4 py-3 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Total</p>
                    <h3 class="text-xl font-bold text-blue-600">{{ $totalMeetings }}</h3>
                </div>
                <div class="bg-white border border-green-100 rounded-2xl px-4 py-3 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Active</p>
                    <h3 class="text-xl font-bold text-green-600">{{ $activeMeetings }}</h3>
                </div>
                <div class="bg-white border border-yellow-100 rounded-2xl px-4 py-3 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Upcoming</p>
                    <h3 class="text-xl font-bold text-yellow-500">{{ $upcomingMeetings }}</h3>
                </div>
                <div class="bg-white border border-red-100 rounded-2xl px-4 py-3 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Issues</p>
                    <h3 class="text-xl font-bold text-red-500">{{ $issueMeetings }}</h3>
                </div>
            </div>
        </div>

        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                <div class="flex gap-2 items-center flex-wrap">
                    @php
                        $statuses = [
                            '' => ['label' => 'All', 'active' => 'bg-blue-600 text-white', 'inactive' => 'border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600'],
                            'upcoming' => ['label' => 'Upcoming', 'active' => 'bg-blue-600 text-white', 'inactive' => 'border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600'],
                            'active' => ['label' => 'Active', 'active' => 'bg-green-600 text-white', 'inactive' => 'border border-gray-200 text-gray-500 hover:bg-green-50 hover:text-green-600'],
                            'completed' => ['label' => 'Completed', 'active' => 'bg-gray-600 text-white', 'inactive' => 'border border-gray-200 text-gray-500 hover:bg-gray-100'],
                            'ended' => ['label' => 'Ended', 'active' => 'bg-purple-600 text-white', 'inactive' => 'border border-gray-200 text-gray-500 hover:bg-purple-50 hover:text-purple-600'],
                            'cancelled' => ['label' => 'Cancelled', 'active' => 'bg-red-600 text-white', 'inactive' => 'border border-gray-200 text-gray-500 hover:bg-red-50 hover:text-red-600'],
                        ];
                    @endphp

                    @foreach($statuses as $value => $config)
                        <a href="{{ route('admin.meetings.index', ['status' => $value]) }}"
                           class="text-xs px-4 py-2 rounded-xl transition
                                  {{ request('status') == $value ? $config['active'] : $config['inactive'] }}">
                            {{ $config['label'] }}
                        </a>
                    @endforeach
                </div>

                <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 w-fit">
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></div>
                    <p class="text-xs text-gray-500">
                        Showing {{ $meetings->firstItem() }}–{{ $meetings->lastItem() }}
                        of {{ $meetings->total() }} meetings
                    </p>
                </div>
            </div>
        </div>

        <div id="pagetop" class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h2 class="font-semibold text-gray-800 text-lg">Meetings Overview</h2>
                <p class="text-xs text-gray-400 mt-0.5">Track all meetings and moderation activities.</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[1050px]">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Meeting</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Organizer</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Date & Time</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Participants</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Status</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Actions</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                    @forelse($meetings as $meeting)
                        @php
                            $statusConfig = [
                                'upcoming' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-100', 'dot' => 'bg-blue-500', 'label' => 'Upcoming'],
                                'active' => ['bg' => 'bg-green-50 text-green-700 border-green-100', 'dot' => 'bg-green-500 animate-pulse', 'label' => 'Active'],
                                'completed' => ['bg' => 'bg-gray-100 text-gray-600 border-gray-200', 'dot' => 'bg-gray-400', 'label' => 'Completed'],
                                'ended' => ['bg' => 'bg-purple-50 text-purple-700 border-purple-100', 'dot' => 'bg-purple-500', 'label' => 'Ended'],
                                'cancelled' => ['bg' => 'bg-red-50 text-red-600 border-red-100', 'dot' => 'bg-red-400', 'label' => 'Cancelled'],
                                'flagged' => ['bg' => 'bg-orange-50 text-orange-600 border-orange-100', 'dot' => 'bg-orange-400', 'label' => 'Flagged'],
                            ];

                            $s = $statusConfig[$meeting->status]
                                ?? ['bg' => 'bg-gray-100 text-gray-500 border-gray-200', 'dot' => 'bg-gray-400', 'label' => ucfirst($meeting->status)];

                            $organizer = $meeting->organizer;
                            $name = optional($organizer)->name ?? 'Unknown';
                        @endphp

                        <tr class="hover:bg-blue-50/30 transition duration-200">
                            <td class="px-5 py-4">
                                <p class="font-semibold text-gray-800">{{ $meeting->title }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ Str::limit($meeting->description, 40) }}</p>
                            </td>

                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    @if($organizer)
                                        <x-user-avatar :user="$organizer" size="sm" />
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-user"></i>
                                        </div>
                                    @endif

                                    <div>
                                        <p class="font-medium text-gray-700">{{ $name }}</p>
                                        <p class="text-xs text-gray-400">{{ ucfirst(optional($organizer)->role ?? '') }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                <p class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</p>
                                <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</p>
                            </td>

                            <td class="px-5 py-4">
                                <div class="inline-flex items-center bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl">
                                    <span class="font-medium text-gray-700">{{ $meeting->participants->count() }} Participants</span>
                                </div>
                            </td>

                            <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $s['bg'] }}">
                                        <span class="w-2 h-2 rounded-full {{ $s['dot'] }}"></span>
                                        {{ $s['label'] }}
                                    </span>
                            </td>

                            <td class="px-5 py-4">
                                <x-meeting-actions :meeting="$meeting" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
                                No meetings found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($meetings->hasPages())
                <div class="px-5 py-4 border-t border-gray-100">
                    {{ $meetings->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
