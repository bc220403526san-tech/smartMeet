<x-layouts.app>

    <x-slot name="header">
        <x-header.search-bar placeholder="Search Resources..." />
    </x-slot>

    <x-success />

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        @if(session('show_welcome_banner'))
            <x-banner
                title="Welcome, {{ Auth::user()->name }}"
                desc="Manage your meetings effortlessly, collaborate with your team, and stay on top of your schedule with a modern dashboard experience."
                action-route="admin.meetings.index"
                action-button="Manage Meeting"
            />
        @endif

        <div>
            <h1 class="text-2xl font-semibold">Admin Overview</h1>
            <p class="text-gray-500 text-sm mt-1">Welcome, Here's what's happening.</p>
        </div>

        {{-- Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4">

            <x-card title="TOTAL MEETINGS" value="{{ number_format($totalMeetings) }}" color="blue" icon="fa-calendar">
                {{ $growthPercent >= 0 ? '↑' : '↓' }} {{ abs($growthPercent) }}% from last month
            </x-card>

            <x-card title="ACTIVE MEETINGS" value="{{ $activeMeetings }}" color="green" icon="fa-video">
                ● Live now
            </x-card>

            <x-card title="TOTAL USERS" value="{{ number_format($totalUsers) }}" color="purple" icon="fa-users">
                ↑ {{ $newUsersThisWeek }} new this week
            </x-card>

            <x-card title="TODAY'S MEETINGS" value="{{ $todayMeetings }}" color="yellow" icon="fa-chart-line">
                Progress
            </x-card>

            <x-card title="UPCOMING" value="{{ $upcomingMeetings }}" color="orange" icon="fa-clock">
                Scheduled for next 48h
            </x-card>

        </div>

        {{-- Activity --}}
        <x-activity :activities="$activities" />

    </div>

</x-layouts.app>
