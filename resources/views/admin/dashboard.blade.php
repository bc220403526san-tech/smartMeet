<x-layouts.app>

    <x-slot name="header">
        <x-header.search-bar
            placeholder="Search Resources..."
        />
    </x-slot>

    <x-success />

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">

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
            <x-card title="TOTAL MEETINGS" value="1,284" color="blue" icon="fa-calendar">
                ↑ 12% from last month
            </x-card>
            <x-card title="ACTIVE MEETINGS" value="42" color="green" icon="fa-video">
                ● Live now
            </x-card>
            <x-card title="TOTAL USERS" value="8,432" color="purple" icon="fa-users">
                ↑ 5.2k new this week
            </x-card>
            <x-card title="TODAY'S MEETINGS" value="156" color="yellow" icon="fa-chart-line">
                Progress
            </x-card>
            <x-card title="UPCOMING" value="214" color="orange" icon="fa-clock">
                Scheduled for next 48h
            </x-card>
        </div>

        {{-- Activity --}}
        <x-activity :activities="[
            ['image' => 'images/image1.avif', 'name' => 'James Wilson', 'message' => 'Started meeting Q4 Budget Review',     'time' => '2 mins ago'],
            ['image' => 'images/image4.avif', 'name' => 'Sarah Chen',   'message' => 'Invited 12 participants to All-Hands', 'time' => '15 mins ago'],
            ['image' => 'images/image2.avif', 'name' => 'System',       'message' => 'Security patch deployed successfully', 'time' => '1 hour ago'],
            ['image' => 'images/image3.webp', 'name' => 'Marcus T.',    'message' => 'Reported a connectivity issue',        'time' => '3 hours ago'],
        ]" />

    </div>

</x-layouts.app>
