<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Organizer Dashboard" />
    </x-slot>

    <x-success />
    <x-error />

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        @if(session('show_welcome_banner'))
            <x-banner
                title="Welcome, {{ Auth::user()->name }}"
                desc="Create, schedule, and monitor all your meetings in one place. Keep your team aligned and your workflow organized effortlessly."
                action-route="organizer.meetings.index"
                action-button="Manage Meeting"
                color="black"
            />
        @endif

        <div>
            <h1 class="text-2xl font-semibold">Organizer Overview</h1>
            <p class="text-gray-500 text-sm mt-1">
                You have
                <span class="text-blue-600 font-semibold">{{ $todayMeetings }} meetings</span>
                scheduled for today.
            </p>
        </div>

        <!-- STATS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5">

            <div class="bg-white/80 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="flex items-start justify-between">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-blue-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                        </svg>
                    </div>
                    <p class="text-xs text-blue-500 font-semibold tracking-widest">TOTAL</p>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-3 sm:mt-4">
                    {{ str_pad($totalMeetings, 2, '0', STR_PAD_LEFT) }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">My Meetings</p>
            </div>

            <div class="bg-white/80 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="flex items-start justify-between">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-green-100">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <p class="text-xs text-green-500 font-semibold tracking-widest">LIVE</p>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-3 sm:mt-4">
                    {{ str_pad($activeMeetings, 2, '0', STR_PAD_LEFT) }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Active Meetings</p>
            </div>

            <div class="bg-white/80 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="flex items-start justify-between">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-indigo-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v11a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-xs text-indigo-500 font-semibold tracking-widest">TODAY</p>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-3 sm:mt-4">
                    {{ str_pad($todayMeetings, 2, '0', STR_PAD_LEFT) }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Today's Meetings</p>
            </div>

            <div class="bg-white/80 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-lg hover:shadow-xl transition duration-300">
                <div class="flex items-start justify-between">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-orange-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-xs text-orange-500 font-semibold tracking-widest">UPCOMING</p>
                </div>
                <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-3 sm:mt-4">
                    {{ str_pad($upcomingMeetings, 2, '0', STR_PAD_LEFT) }}
                </h2>
                <p class="text-xs sm:text-sm text-gray-500 mt-1">Upcoming Meetings</p>
            </div>

        </div>

        <!-- AGENDA CARD -->
        <div class="bg-white rounded-2xl shadow-md border border-blue-100 p-4 sm:p-6">

            <div class="flex justify-between items-center mb-5">
                <div>
                    <h2 class="text-base sm:text-lg font-semibold text-gray-800">Today's Meetings</h2>
                    <p class="text-xs text-gray-500">Your schedule for {{ \Carbon\Carbon::today()->format('M d, Y') }}</p>
                </div>
                <a href="{{ route('organizers.meetings.index') }}"
                   class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium hover:bg-blue-700 transition">
                    View All
                </a>
            </div>

            @forelse($agenda as $meeting)

                @php
                    $isActive    = $meeting->status === 'active';
                    $isCompleted = $meeting->status === 'completed';
                    $isCancelled = $meeting->status === 'cancelled';

                    $borderColor = match($meeting->status) {
                        'active'    => 'border-l-blue-600 bg-blue-50',
                        'upcoming'  => 'border-l-blue-200 bg-white',
                        'completed' => 'border-l-gray-300 bg-gray-50',
                        'cancelled' => 'border-l-red-300 bg-red-50',
                        default     => 'border-l-gray-200 bg-white',
                    };

                    $badgeClass = match($meeting->status) {
                        'active'    => 'bg-orange-100 text-orange-600',
                        'upcoming'  => 'bg-blue-100 text-blue-600',
                        'completed' => 'bg-gray-100 text-gray-600',
                        'cancelled' => 'bg-red-100 text-red-500',
                        default     => 'bg-gray-100 text-gray-600',
                    };

                    $badgeLabel = match($meeting->status) {
                        'active'    => 'LIVE NOW',
                        'upcoming'  => 'SCHEDULED',
                        'completed' => 'COMPLETED',
                        'cancelled' => 'CANCELLED',
                        default     => strtoupper($meeting->status),
                    };

                    $agendaItems = json_decode($meeting->agenda ?? '[]', true) ?? [];
                @endphp

                <div class="group relative flex flex-col sm:flex-row justify-between sm:items-center p-4 sm:p-5
                    bg-white border border-gray-100 border-l-4 {{ $borderColor }} rounded-2xl mb-3
                    shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all duration-300 gap-4 overflow-hidden">

                    <div class="absolute -right-10 -top-10 w-32 h-32 bg-blue-50 rounded-full blur-2xl
                        opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none"></div>

                    <div class="flex gap-4 items-start relative z-10">
                        <div class="w-16 sm:w-[68px] text-center shrink-0 bg-gray-50 group-hover:bg-blue-50
                            rounded-xl py-2.5 border border-gray-100 group-hover:border-blue-100 transition-colors duration-300">
                            <p class="text-base font-bold text-gray-800 leading-none">
                                {{ \Carbon\Carbon::parse($meeting->time)->format('h:i') }}
                            </p>
                            <p class="text-[10px] font-semibold text-gray-400 mt-1 tracking-wider">
                                {{ \Carbon\Carbon::parse($meeting->time)->format('A') }}
                            </p>
                        </div>

                        <div class="min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full {{ $badgeClass }} tracking-wide">
                                    {{ $badgeLabel }}
                                </span>
                                @if($isActive)
                                    <span class="inline-flex items-center gap-1 text-[11px] font-medium text-green-600">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                        Live now
                                    </span>
                                @endif
                            </div>

                            <h3 class="font-semibold text-gray-800 mt-1.5 text-sm sm:text-base leading-snug">
                                {{ $meeting->title }}
                            </h3>

                            <div class="flex items-center gap-1.5 text-xs text-gray-400 mt-1 flex-wrap">
                                <i class="fa-solid fa-earth-americas text-[11px]"></i>
                                <span>{{ $meeting->timezone }}</span>
                                <span class="text-gray-300">•</span>
                                <i class="fa-regular fa-clock text-[11px]"></i>
                                <span>{{ $meeting->duration }} min</span>
                            </div>

                            @if(count($agendaItems) > 0)
                                <ul class="mt-2.5 space-y-1">
                                    @foreach($agendaItems as $item)
                                        <li class="text-xs text-gray-500 flex items-center gap-2">
                                            <span class="w-1.5 h-1.5 bg-blue-400 rounded-full shrink-0"></span>
                                            <span class="truncate">{{ $item['title'] ?? $item }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    <div class="flex gap-2 self-start sm:self-center shrink-0 relative z-10">
                        @if($isActive)
                            <a href="{{ route('organizers.meetings.attend', $meeting) }}"
                               class="inline-flex items-center gap-2 bg-gradient-to-r from-blue-600 to-indigo-600
                                   text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-md
                                   hover:shadow-lg hover:from-blue-700 hover:to-indigo-700 transition-all duration-200">
                                <i class="fa-solid fa-video text-xs"></i>
                                Join
                            </a>
                        @elseif(!$isCompleted && !$isCancelled)
                            <a href="{{ route('organizers.meetings.show', $meeting) }}"
                               class="inline-flex items-center gap-2 text-blue-600 bg-blue-50 border border-blue-100
                                   px-4 py-2.5 rounded-xl text-sm font-medium
                                   hover:bg-blue-600 hover:text-white hover:border-blue-600 transition-all duration-200">
                                <i class="fa-solid fa-gear text-xs"></i>
                                Manage
                            </a>
                        @else
                            <a href="{{ route('organizers.meetings.show', $meeting) }}"
                               class="inline-flex items-center gap-2 text-gray-500 bg-gray-50 border border-gray-200
                                   px-4 py-2.5 rounded-xl text-sm font-medium hover:bg-gray-100 transition-all duration-200">
                                <i class="fa-solid fa-eye text-xs"></i>
                                View
                            </a>
                        @endif
                    </div>
                </div>

            @empty
                <div class="text-center py-10 text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v11a2 2 0 002 2z" />
                    </svg>
                    <p class="text-sm font-medium">No meetings scheduled for today.</p>
                    <a href="{{ route('organizers.meetings.create') }}"
                       class="mt-3 inline-block text-blue-600 text-sm hover:underline">
                        Schedule a meeting →
                    </a>
                </div>
            @endforelse

        </div>

    </div>

</x-layouts.app>
