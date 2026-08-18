<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Participant Dashboard" />
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">
        @if(session('show_welcome_banner'))
            <x-banner
                title="Welcome, {{ Auth::user()->name }}"
                desc="Create, schedule, and monitor all your meetings in one place. Keep your team aligned and your workflow organized effortlessly."
                action-route="participant.meetings.index"
                action-button="Manage Meetings"
            />
        @endif

        <div>
            <h1 class="text-2xl font-semibold">Overview</h1>
            <p class="text-gray-500 text-sm mt-1">Your meetings and activities at a glance.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <a href="{{ route('participant.meetings.index') }}" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group block">
                <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gradient-to-br group-hover:from-blue-500 group-hover:to-indigo-600 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-600 group-hover:text-white transition-all duration-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 text-base mb-1">My Meetings</h3>
                <p class="text-xs text-gray-500 leading-relaxed">View all historical sessions and your personal recordings archive.</p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs text-blue-600 font-medium">{{ $totalMeetings }} {{ Str::plural('meeting', $totalMeetings) }}</span>
                    <span class="text-xs text-gray-300">•</span>
                    <span class="text-xs text-gray-400">View all →</span>
                </div>
            </a>

            <a href="{{ route('participant.meetings.index', ['filter' => 'today']) }}" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group block">
                <div class="w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gradient-to-br group-hover:from-emerald-500 group-hover:to-teal-600 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-emerald-600 group-hover:text-white transition-all duration-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 text-base mb-1">Today's Meetings</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Track your daily schedule and get notified before meetings start.</p>
                <div class="mt-3 flex items-center gap-2">
                    @if($liveMeetings > 0)
                        <span class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                            </span>
                            {{ $liveMeetings }} live now
                        </span>
                    @else
                        <span class="text-xs text-gray-500 font-medium">{{ $todayMeetings }} today</span>
                    @endif
                    <span class="text-xs text-gray-300">•</span>
                    <span class="text-xs text-gray-400">View schedule →</span>
                </div>
            </a>

            <a href="{{ route('participant.meetings.index', ['filter' => 'upcoming']) }}" class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group block">
                <div class="w-12 h-12 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-gradient-to-br group-hover:from-purple-500 group-hover:to-violet-600 transition-all duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-purple-600 group-hover:text-white transition-all duration-300">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-800 text-base mb-1">Upcoming Meetings</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Stay ahead with insights into next week's confirmed sessions.</p>
                <div class="mt-3 flex items-center gap-2">
                    <span class="text-xs text-purple-600 font-medium">{{ $upcomingMeetings }} upcoming</span>
                    <span class="text-xs text-gray-300">•</span>
                    <span class="text-xs text-gray-400">See all →</span>
                </div>
            </a>
        </div>

        <div class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div>
                    <h2 class="font-semibold text-gray-800 text-lg">Upcoming Schedule</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Confirmed meetings for the next 48 hours</p>
                </div>
            </div>

            <div class="grid grid-cols-4 px-5 py-3 bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                <div>Meeting Name</div>
                <div>Host</div>
                <div>Date &amp; Time</div>
                <div class="text-right">Action</div>
            </div>

            @forelse($schedule as $meeting)
                @php
                    $meetingDate = \Carbon\Carbon::parse($meeting->date);
                    $isLive = $meeting->status === 'active';
                    $dayLabel = $meetingDate->isToday()
                        ? 'Today'
                        : ($meetingDate->isTomorrow() ? 'Tomorrow' : $meetingDate->format('M d, Y'));
                    $startTime = $meeting->time ? \Carbon\Carbon::parse($meeting->time)->format('h:i A') : null;
                    $endTime = $meeting->end_time ? \Carbon\Carbon::parse($meeting->end_time)->format('h:i A') : null;
                    $host = $meeting->organizer;
                @endphp

                <div class="grid grid-cols-4 items-center px-5 py-4 border-b border-gray-100 last:border-b-0 hover:bg-blue-50/30 transition duration-200 group">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="relative flex h-2.5 w-2.5 flex-shrink-0">
                            @if($isLive)
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-400 opacity-75"></span>
                            @endif
                            <span class="relative inline-flex rounded-full h-2.5 w-2.5 {{ $isLive ? 'bg-blue-600' : 'bg-gray-300' }}"></span>
                        </span>
                        <span class="text-sm font-medium text-gray-800 group-hover:text-blue-600 transition truncate" title="{{ $meeting->title }}">
                            {{ $meeting->title }}
                        </span>
                    </div>

                    <div class="flex items-center gap-2 min-w-0">
                        @if($host && $host->avatar)
                            <img src="{{ $host->avatar }}" class="w-8 h-8 rounded-full ring-2 ring-gray-100 flex-shrink-0" alt="{{ $host->name }}">
                        @else
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-xs font-bold text-blue-700 ring-2 ring-gray-100 flex-shrink-0">
                                {{ $host ? Str::of($host->name)->explode(' ')->map(fn($word) => Str::substr($word, 0, 1))->take(2)->implode('') : 'SM' }}
                            </div>
                        @endif
                        <span class="text-sm text-gray-600 hidden sm:block truncate">{{ $host->name ?? 'SmartMeet' }}</span>
                    </div>

                    <div>
                        <p class="text-sm text-gray-700 font-medium">{{ $dayLabel }}</p>
                        <p class="text-xs text-gray-400">{{ $startTime }}@if($endTime) – {{ $endTime }}@endif</p>
                    </div>

                    <div class="text-right">
                        @if($isLive)
                            <a href="{{ route('participant.meetings.attend', $meeting) }}"
                               class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-xs font-semibold transition bg-gradient-to-r from-blue-600 to-indigo-600 text-white hover:from-blue-700 hover:to-indigo-700 shadow-sm hover:shadow">
                                <i class="fa-solid fa-video text-[11px]"></i>
                                Join Session
                            </a>
                        @else
                            <span title="You can join after the organizer starts this meeting."
                                  class="inline-flex items-center gap-2 px-5 py-2 rounded-lg text-xs font-semibold bg-gray-100 text-gray-400 border border-gray-200 cursor-not-allowed select-none">
                                <i class="fa-solid fa-clock text-[11px]"></i>
                                Upcoming
                            </span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-gray-500">No upcoming meetings in the next 48 hours.</p>
                </div>
            @endforelse

            <div class="px-5 py-3 border-t border-gray-100 bg-gray-50/50 text-center">
                <a href="{{ route('participant.meetings.index') }}" class="text-xs text-blue-600 font-medium hover:text-blue-700 hover:underline transition">
                    View all meetings →
                </a>
            </div>
        </div>
    </div>
</x-layouts.app>
