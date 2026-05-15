<x-layouts.app>

    <x-header.search-bar placeholder="Search Resources..." />

    <x-success />

    <div class="p-4 bg-gray-50 rounded-lg overflow-y-auto mt-3">
   @if(session('show_welcome_banner'))
    <x-banner
    title="Welcome, {{ Auth::user()->name }}"
    desc="Manage your meetings effortlessly, collaborate with your team, and stay on top of your schedule with
     a modern dashboard experience."
    action-route="admin.meetings.index"
    action-button="Manage Meeting"
    />
    @endif

    <h1 class="text-2xl font-semibold mb-2">Admin Overview</h1>
    <p class="text-gray-500 mb-6 text-sm">
        Welcome, Here's what's happening.
    </p>

    {{-- Card Component--}}
       <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-5 gap-3 sm:gap-4 mb-6">

        <x-card
            title="TOTAL MEETINGS"
            value="1,284"
            color="blue"
            icon="fa-calendar"
        >
            ↑ 12% from last month
        </x-card>

        <x-card
            title="ACTIVE MEETINGS"
            value="42"
            color="green"
            icon="fa-video"
        >
            ● Live now
        </x-card>

        <x-card
            title="TOTAL USERS"
            value="8,432"
            color="purple"
            icon="fa-users"
        >
            ↑ 5.2k new this week
        </x-card>

        <x-card
            title="TODAY'S MEETINGS"
            value="156"
            color="yellow"
            icon="fa-chart-line"
        >
            Progress
        </x-card>

        <x-card
            title="UPCOMING"
            value="214"
            color="orange"
            icon="fa-clock"
        >
            Scheduled for next 48h
        </x-card>

    </div>

    {{-- Activity Component --}}
    <x-activity :activities="[
        [
            'image' => 'images/image1.avif',
            'name' => 'James Wilson',
            'message' => 'Started meeting Q4 Budget Review',
            'time' => '2 mins ago'
        ],
        [
            'image' => 'images/image4.avif',
            'name' => 'Sarah Chen',
            'message' => 'Invited 12 participants to All-Hands',
            'time' => '15 mins ago'
        ],
        [
            'image' => 'images/image2.avif',
            'name' => 'System',
            'message' => 'Security patch deployed successfully',
            'time' => '1 hour ago'
        ],
        [
            'image' => 'images/image3.webp',
            'name' => 'Marcus T.',
            'message' => 'Reported a connectivity issue',
            'time' => '3 hours ago'
        ]
    ]" />
    </div>
</x-layouts.app>
