<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Admin Dashboard"/>
    </x-slot>

    <x-success />

    @php
        $isNewUser = session('welcome_type') === 'register';

        $typeLabels = [
            'registration' => 'Registered',
            'login' => 'Login',
            'meeting' => 'Meeting Created',
        ];

        $typeClasses = [
            'registration' => 'bg-purple-50 text-purple-700 border-purple-100',
            'login' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'meeting' => 'bg-blue-50 text-blue-700 border-blue-100',
        ];
    @endphp

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-6 overflow-y-auto min-h-screen">

        @if(session('show_welcome_banner'))
            <x-banner
                title="{{ session('welcome_title', 'Welcome, ' . Auth::user()->name) }}"
                desc="Manage your meetings effortlessly, collaborate with your team, and stay on top of your schedule with a modern dashboard experience."
                action-route="admin.meetings.index"
                action-button="Manage Meeting"
            />
        @endif

        <div>
            <h1 class="text-xl font-semibold text-gray-900">Overview</h1>
            <p class="text-gray-500 text-sm mt-1">
                {{ $isNewUser
                    ? "Welcome aboard — let's get your dashboard set up."
                    : "Welcome back — here's what's happening today."
                }}
            </p>
        </div>

        {{-- Original dashboard stat cards only --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
            <x-card
                title="Total Meetings"
                value="{{ number_format($totalMeetings) }}"
                icon="fa-calendar"
                color="blue"
                :primary="true"
            >
                <span class="{{ $growthPercent >= 0 ? 'text-emerald-600' : 'text-red-500' }} font-medium">
                    {{ $growthPercent >= 0 ? '↑' : '↓' }} {{ abs($growthPercent) }}%
                </span>
                <span class="text-gray-400">from last month</span>
            </x-card>

            <x-card
                title="Active Meetings"
                value="{{ $activeMeetings }}"
                icon="fa-video"
                color="emerald"
                :live="true"
            >
                Currently in progress
            </x-card>

            <x-card
                title="Total Users"
                value="{{ number_format($totalUsers) }}"
                icon="fa-users"
                color="purple"
            >
                <span class="text-emerald-600 font-medium">↑ {{ $newUsersThisWeek }}</span>
                <span class="text-gray-400">new this week</span>
            </x-card>

            <x-card
                title="Today's Meetings"
                value="{{ $todayMeetings }}"
                icon="fa-chart-line"
                color="amber"
            >
                <span class="text-gray-400">Scheduled for today</span>
            </x-card>

            <x-card
                title="Upcoming"
                value="{{ $upcomingMeetings }}"
                icon="fa-clock"
                color="red"
            >
                <span class="text-gray-400">Upcoming meetings</span>
            </x-card>
        </div>

        {{-- Activity History --}}
        <div id="activity-history" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

            <div class="px-4 sm:px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Recent Activity</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Registrations, logins and created meetings.
                    </p>
                </div>

                <div class="text-sm text-gray-500">
                    {{ number_format($activities->total()) }} activities
                </div>
            </div>

            @forelse($activities as $activity)
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-3">

                        <img
                            src="{{ $activity['image'] }}"
                            alt="{{ $activity['name'] }}"
                            class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0"
                            onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';"
                        >

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-1 sm:gap-4">

                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-sm text-gray-900">
                                            {{ $activity['name'] }}
                                        </span>

                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full border text-[11px] font-semibold
                                            {{ $typeClasses[$activity['type']] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                            {{ $typeLabels[$activity['type']] ?? ucfirst($activity['type']) }}
                                        </span>
                                    </div>

                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $activity['message'] }}
                                    </p>
                                </div>

                                <div class="shrink-0 sm:text-right">
                                    <p class="text-xs text-gray-500">
                                        {{ $activity['date_time'] }}
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $activity['time'] }}
                                    </p>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <p class="text-gray-700 font-medium">No activity available</p>
                </div>
            @endforelse

            @if($activities->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                    {{ $activities->onEachSide(1)->fragment('activity-history')->links() }}
                </div>
            @endif

        </div>

    </div>
</x-layouts.app>
