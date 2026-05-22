<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite('resources/css/app.css')
</head>

<body class="bg-[#f4f5f7]">

<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen">

    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    <!-- SIDEBAR -->
    <x-sidebar.participant-menu />

    <!-- MAIN -->
    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0 md:ml-0">

        <!-- HEADER -->
        <x-header.page-title
            title="Participant Dashboard"
        />

        <!-- CONTENT WRAPPER -->
        <div class="p-3 sm:p-6 bg-gray-50 overflow-y-auto rounded-lg mt-3 flex-1">

            <!-- Banner Image -->
                        <x-banner
                            title="Welcome, {{ Auth::user()->name }}"
                            desc="Create, schedule, and monitor all your meetings in one place. Keep your team aligned and your
                            workflow organized effortlessly."
                            action-route="organizer.meetings.index"
                            action-button="Manage Meetings"
                        />

            <!-- 3 Feature Cards (image jesa) -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">

                <!-- My Meetings Card -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-md transition cursor-pointer group">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-600 group-hover:text-blue-600 transition">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-base mb-1">My Meetings</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">View all historical sessions and your personal recordings archive.</p>
                </div>

                <!-- Today's Meetings Card -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-md transition cursor-pointer group">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-600 group-hover:text-blue-600 transition">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-base mb-1">Today's Meetings</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Track your daily schedule and get notified before meetings start.</p>
                </div>

                <!-- Upcoming Meetings Card -->
                <div class="bg-white border border-gray-200 rounded-2xl p-5 hover:shadow-md transition cursor-pointer group">
                    <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-50 transition">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-gray-600 group-hover:text-blue-600 transition">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                        </svg>
                    </div>
                    <h3 class="font-semibold text-gray-800 text-base mb-1">Upcoming Meetings</h3>
                    <p class="text-xs text-gray-500 leading-relaxed">Stay ahead with insights into next week's confirmed sessions.</p>
                </div>

            </div>

            <!-- Upcoming Schedule Table -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100">

                <!-- Table Header -->
                <div class="flex justify-between items-start p-5 sm:p-6 border-b border-gray-100">
                    <div>
                        <h2 class="text-base font-semibold text-gray-800">Upcoming Schedule</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Confirmed meetings for the next 48 hours</p>
                    </div>
                    <div class="flex items-center gap-3">
                        <!-- Filter icon -->
                        <button class="p-1.5 rounded-lg hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                            </svg>
                        </button>
                        <!-- Search icon -->
                        <button class="p-1.5 rounded-lg hover:bg-gray-100 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-500">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Column Headers -->
                <div class="grid grid-cols-4 px-5 sm:px-6 py-3 bg-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                    <div>Meeting Name</div>
                    <div>Host</div>
                    <div>Date &amp; Time</div>
                    <div class="text-right">Action</div>
                </div>

                <!-- Row 1 — Live / Joinable -->
                <div class="grid grid-cols-4 items-center px-5 sm:px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-blue-600 inline-block"></span>
                        <span class="text-sm font-medium text-gray-800">Team Sync: Sprint 4</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <img src="https://i.pravatar.cc/32?img=5" class="w-7 h-7 rounded-full" alt="">
                        <span class="text-sm text-gray-600 hidden sm:block">Sarah Jenkins</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 font-medium">Today</p>
                        <p class="text-xs text-gray-400">10:00 AM – 11:00 AM</p>
                    </div>
                    <div class="text-right">
                        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-xs font-semibold hover:bg-blue-700 transition">
                            Join Session
                        </button>
                    </div>
                </div>

                <!-- Row 2 -->
                <div class="grid grid-cols-4 items-center px-5 sm:px-6 py-4 border-b border-gray-50 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                        <span class="text-sm font-medium text-gray-800">Design Review: V2 Dashboard</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <img src="https://i.pravatar.cc/32?img=8" class="w-7 h-7 rounded-full" alt="">
                        <span class="text-sm text-gray-600 hidden sm:block">Marcus Chen</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 font-medium">Today</p>
                        <p class="text-xs text-gray-400">2:30 PM – 3:30 PM</p>
                    </div>
                    <div class="text-right">
                        <button class="bg-gray-100 text-gray-500 px-4 py-2 rounded-lg text-xs font-semibold hover:bg-gray-200 transition">
                            Join Later
                        </button>
                    </div>
                </div>

                <!-- Row 3 -->
                <div class="grid grid-cols-4 items-center px-5 sm:px-6 py-4 hover:bg-gray-50 transition">
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-gray-300 inline-block"></span>
                        <span class="text-sm font-medium text-gray-800">Weekly All-Hands</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <!-- Avatar with initials -->
                        <div class="w-7 h-7 rounded-full bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-600">SM</div>
                        <span class="text-sm text-gray-600 hidden sm:block">SmartMeet HR</span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-700 font-medium">Tomorrow</p>
                        <p class="text-xs text-gray-400">09:00 AM – 10:00 AM</p>
                    </div>
                    <div class="text-right">
                        <button class="bg-gray-100 text-gray-500 px-4 py-2 rounded-lg text-xs font-semibold hover:bg-gray-200 transition">
                            Join Later
                        </button>
                    </div>
                </div>

            </div>

        </div>
    </div>

</div>

</body>
</html>
