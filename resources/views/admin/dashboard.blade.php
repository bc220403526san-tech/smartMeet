<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Admin Dashboard"/>
    </x-slot>
    <x-success />

    @php
        $isNewUser = session('welcome_type') === 'register';
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
                    {{ $isNewUser ? "Welcome aboard — let's get your dashboard set up." : "Welcome back — here's what's happening today." }}
                </p>
            </div>

        {{-- Stat cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">
                <x-card title="Total Meetings" value="{{ number_format($totalMeetings) }}" icon="fa-calendar" color="blue" :primary="true">
        <span class="{{ $growthPercent >= 0 ? 'text-emerald-600' : 'text-red-500' }} font-medium">
            {{ $growthPercent >= 0 ? '↑' : '↓' }} {{ abs($growthPercent) }}%
        </span>
                    <span class="text-gray-400">from last month</span>
                </x-card>

                <x-card title="Active Meetings" value="{{ $activeMeetings }}" icon="fa-video" color="emerald" :live="true">
                    Currently in progress
                </x-card>

                <x-card title="Total Users" value="{{ number_format($totalUsers) }}" icon="fa-users" color="purple">
                    <span class="text-emerald-600 font-medium">↑ {{ $newUsersThisWeek }}</span>
                    <span class="text-gray-400">new this week</span>
                </x-card>

                <x-card title="Today's Meetings" value="{{ $todayMeetings }}" icon="fa-chart-line" color="amber">
                    <span class="text-gray-400">Scheduled for today</span>
                </x-card>

                <x-card title="Upcoming" value="{{ $upcomingMeetings }}" icon="fa-clock" color="red">
                    <span class="text-gray-400">Next 48 hours</span>
                </x-card>
            </div>

        {{-- Activity --}}
        <x-activity :activities="$activities" :limit="6" />
    </div>
</x-layouts.app>
