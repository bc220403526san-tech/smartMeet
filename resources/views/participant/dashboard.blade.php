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

    <!-- SIDEBAR (unchanged) -->
    <div class="sidebar w-64 bg-white shadow-lg p-5 flex flex-col justify-between rounded-xl m-3">
        <div>
            <!-- LOGO -->
           <x-logo />

            <!-- MENU -->
            <ul class="space-y-1 text-sm mt-2">

                <li class="bg-blue-100 text-blue-600 p-2 rounded-lg flex gap-2 items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 16.875h3.375m0 0h3.375m-3.375 0V13.5m0 3.375v3.375M6 10.5h2.25a2.25 2.25 0 0 0 2.25-2.25V6a2.25 2.25 0 0 0-2.25-2.25H6A2.25 2.25 0 0 0 3.75 6v2.25A2.25 2.25 0 0 0 6 10.5Zm0 9.75h2.25A2.25 2.25 0 0 0 10.5 18v-2.25a2.25 2.25 0 0 0-2.25-2.25H6a2.25 2.25 0 0 0-2.25 2.25V18A2.25 2.25 0 0 0 6 20.25Zm9.75-9.75H18a2.25 2.25 0 0 0 2.25-2.25V6A2.25 2.25 0 0 0 18 3.75h-2.25A2.25 2.25 0 0 0 13.5 6v2.25a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                    Dashboard
                </li>

                <li class="text-gray-600 p-2 rounded-lg hover:bg-gray-100 flex gap-2 items-center cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75" />
                    </svg>
                    My Meetings
                </li>

                <li class="text-gray-600 p-2 rounded-lg hover:bg-gray-100 flex gap-2 items-center cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H8.25m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0H12m4.125 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 0 1-2.555-.337A5.972 5.972 0 0 1 5.41 20.97a5.969 5.969 0 0 1-.474-.065 4.48 4.48 0 0 0 .978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25Z" />
                    </svg>
                    Join Meeting
                </li>

                <li class="text-gray-600 p-2 rounded-lg hover:bg-gray-100 flex gap-2 items-center cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-5 w-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    Schedule
                </li>

                <li class="text-gray-600 p-2 rounded-lg hover:bg-gray-100 flex gap-2 items-center cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                         class="size-5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                    Settings
                </li>

            </ul>
        </div>

        <!-- BOTTOM ACTIONS -->
        <div class="space-y-2 border-t border-gray-200 pt-4">
            <!-- Start Meeting button -->
            <button class="w-54 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition hidden sm:block">
                Start Meeting
            </button>
            <div class="flex items-center gap-2 text-gray-600 p-2 rounded-lg hover:bg-gray-100 cursor-pointer text-sm">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15m3 0 3-3m0 0-3-3m3 3H9" />
                </svg>
                Logout
            </div>
        </div>
    </div>

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
