<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Admin Dashboard"/>
    </x-slot>

    <x-success />

    @php
        $isNewUser = session('welcome_type') === 'register';

        $typeLabels = [
            'registration' => 'Registration',
            'meeting' => 'Meeting Created',
            'meeting_join' => 'Meeting Joined',
            'meeting_leave' => 'Meeting Left',
            'login_session' => 'Login / Session',
            'invite' => 'Invite',
            'role_request' => 'Role Request',
            'notification' => 'Notification',
        ];

        $typeClasses = [
            'registration' => 'bg-purple-50 text-purple-700 border-purple-100',
            'meeting' => 'bg-blue-50 text-blue-700 border-blue-100',
            'meeting_join' => 'bg-emerald-50 text-emerald-700 border-emerald-100',
            'meeting_leave' => 'bg-red-50 text-red-700 border-red-100',
            'login_session' => 'bg-slate-100 text-slate-700 border-slate-200',
            'invite' => 'bg-cyan-50 text-cyan-700 border-cyan-100',
            'role_request' => 'bg-amber-50 text-amber-700 border-amber-100',
            'notification' => 'bg-indigo-50 text-indigo-700 border-indigo-100',
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

        {{-- Original overview cards --}}
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
                <span class="text-gray-400">All upcoming meetings</span>
            </x-card>
        </div>

        {{-- Database activity counters --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 xl:grid-cols-8 gap-2">
            <div class="bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-500">All Activities</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($activityCounts['all']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-500">Registrations</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($activityCounts['registrations']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-500">Meetings</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($activityCounts['meetings']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-500">Join / Leave</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($activityCounts['meeting_sessions']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-500">Login Sessions</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($activityCounts['login_sessions']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-500">Invites</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($activityCounts['invites']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-500">Role Requests</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($activityCounts['role_requests']) }}</p>
            </div>
            <div class="bg-white rounded-xl border border-gray-100 p-3">
                <p class="text-xs text-gray-500">Notifications</p>
                <p class="text-xl font-bold text-gray-900">{{ number_format($activityCounts['notifications']) }}</p>
            </div>
        </div>

        {{-- Complete database timeline --}}
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="px-4 sm:px-6 py-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Complete Database Activity</h2>
                    <p class="text-sm text-gray-500 mt-1">
                        All available historical records currently stored in SmartMeet database.
                    </p>
                </div>

                <div class="text-sm text-gray-500">
                    Showing
                    <span class="font-semibold text-gray-800">{{ $activities->firstItem() ?? 0 }}</span>
                    -
                    <span class="font-semibold text-gray-800">{{ $activities->lastItem() ?? 0 }}</span>
                    of
                    <span class="font-semibold text-gray-800">{{ number_format($activities->total()) }}</span>
                </div>
            </div>

            @forelse($activities as $activity)
                <div class="px-4 sm:px-6 py-4 border-b border-gray-100 last:border-b-0 hover:bg-gray-50 transition">
                    <div class="flex items-start gap-3 sm:gap-4">

                        <img
                            src="{{ $activity['image'] }}"
                            alt="{{ $activity['name'] }}"
                            class="w-10 h-10 rounded-full object-cover border border-gray-200 shrink-0"
                            onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.png') }}';"
                        >

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-2">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span class="font-semibold text-gray-900">
                                            {{ $activity['name'] }}
                                        </span>

                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full border text-[11px] font-semibold
                                            {{ $typeClasses[$activity['type']] ?? 'bg-gray-50 text-gray-600 border-gray-200' }}">
                                            {{ $typeLabels[$activity['type']] ?? ucfirst($activity['type']) }}
                                        </span>
                                    </div>

                                    @if(!empty($activity['email']))
                                        <p class="text-xs text-gray-400 mt-0.5 break-all">
                                            {{ $activity['email'] }}
                                        </p>
                                    @endif

                                    <p class="text-sm text-gray-700 mt-1">
                                        {{ $activity['message'] }}
                                    </p>

                                    @if(!empty($activity['meeting']))
                                        <p class="text-xs text-gray-500 mt-1">
                                            <span class="font-medium">Meeting:</span>
                                            {{ $activity['meeting'] }}
                                        </p>
                                    @endif

                                    @if(!empty($activity['details']))
                                        <p class="text-xs text-gray-400 mt-1 break-words">
                                            {{ $activity['details'] }}
                                        </p>
                                    @endif
                                </div>

                                <div class="shrink-0 lg:text-right">
                                    <p class="text-xs font-medium text-gray-600">
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
                <div class="p-10 text-center">
                    <p class="font-medium text-gray-700">No database activity found.</p>
                </div>
            @endforelse

            @if($activities->hasPages())
                <div class="px-4 sm:px-6 py-4 border-t border-gray-100 bg-gray-50/70">
                    {{ $activities->onEachSide(1)->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.app>
