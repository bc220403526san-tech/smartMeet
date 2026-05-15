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

<!-- Hidden checkbox for sidebar toggle -->
<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen">

    <!-- OVERLAY (mobile only) - acts as close button via label -->
    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    <!-- SIDEBAR -->
    <x-sidebar.organizer-menu />

    <!-- MAIN -->
    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0 md:ml-0">

        <!-- HEADER -->
       <x-header.page-title
       title="Organizer Dashboard"
       />

        <!-- CONTENT WRAPPER -->
        <div class="p-3 sm:p-6 bg-gray-50 overflow-y-auto rounded-lg mt-3 flex-1">

            <!-- Banner Image -->
{{--            <x-banner--}}
{{--                title="Welcome, {{ Auth::user()->name }}"--}}
{{--                desc="Create, schedule, and monitor all your meetings in one place. Keep your team aligned and your--}}
{{--                workflow organized effortlessly."--}}
{{--                action-route="organizer.meetings.index"--}}
{{--                action-button="Manage Meetings"--}}
{{--            />--}}
            <div class="w-full h-48 sm:h-64 rounded-2xl overflow-hidden shadow-xl relative mb-4">
                <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c"
                     class="absolute inset-0 w-full h-full object-cover scale-105" alt="Banner">
                <div class="absolute inset-0 bg-gradient-to-r from-gray-900/70 via-slate-800/60 to-zinc-700/50"></div>
                <div class="absolute top-[-60px] left-[-60px] w-96 h-96 bg-gray-400/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-[-60px] right-[-60px] w-96 h-96 bg-slate-300/10 rounded-full blur-3xl"></div>

                <div class="relative h-full flex items-center px-6 sm:px-10">
                    <div class="max-w-xl">
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-semibold text-white tracking-wide">
                            Welcome, {{ Auth::user()->name }}
                        </h1>
                        <p class="text-gray-200 mt-2 text-xs sm:text-sm md:text-base leading-relaxed hidden sm:block">
                            Create, schedule, and monitor all your meetings in one place. Keep your team aligned and your workflow organized effortlessly.
                        </p>
                        <a class="inline-block mt-4 sm:mt-6 bg-white text-gray-800 px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm font-semibold shadow-md hover:bg-gray-100 hover:scale-105 transition cursor-pointer">
                            Manage Meetings
                        </a>
                    </div>
                </div>
            </div>

            <!-- HERO / TITLE -->
            <div class="mb-5">
                <p class="text-sm text-gray-500">
                    You have <span class="text-blue-600 font-semibold">4 meetings</span> scheduled for today.
                </p>
            </div>

            <!-- STATS - 2 cols on small, 4 cols on large -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-5 mb-6">

                <!-- TOTAL -->
                <div class="bg-white/80 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="flex items-start justify-between">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-blue-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18" />
                            </svg>
                        </div>
                        <p class="text-xs text-blue-500 font-semibold tracking-widest text-right">TOTAL</p>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-3 sm:mt-4">24</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">My Meetings</p>
                </div>

                <!-- LIVE -->
                <div class="bg-white/80 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="flex items-start justify-between">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-green-100">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 text-green-600">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                        <p class="text-xs text-green-500 font-semibold tracking-widest text-right">LIVE</p>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-3 sm:mt-4">02</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Active Meetings</p>
                </div>

                <!-- CURRENT -->
                <div class="bg-white/80 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="flex items-start justify-between">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-indigo-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10m-11 9h12a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v11a2 2 0 002 2z" />
                            </svg>
                        </div>
                        <p class="text-xs text-indigo-500 font-semibold tracking-widest text-right">CURRENT</p>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-3 sm:mt-4">08</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Today's Meetings</p>
                </div>

                <!-- PENDING -->
                <div class="bg-white/80 backdrop-blur-md p-4 sm:p-5 rounded-2xl shadow-lg hover:shadow-xl transition duration-300">
                    <div class="flex items-start justify-between">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 flex items-center justify-center rounded-xl bg-orange-100">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <p class="text-xs text-orange-500 font-semibold tracking-widest text-right">PENDING</p>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-800 mt-3 sm:mt-4">12</h2>
                    <p class="text-xs sm:text-sm text-gray-500 mt-1">Upcoming Meetings</p>
                </div>

            </div>

            <!-- AGENDA CARD -->
            <div class="bg-white rounded-2xl shadow-md border border-blue-100 p-4 sm:p-6">

                <!-- HEADER -->
                <div class="flex justify-between items-center mb-5">
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-800">Priority Agenda</h2>
                        <p class="text-xs text-gray-500">Today's meetings schedule overview</p>
                    </div>
                    <a href="#" class="bg-blue-600 text-white px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-medium hover:bg-blue-700 transition">
                        View All
                    </a>
                </div>

                <!-- ITEM 1 (ACTIVE) -->
                <div class="flex flex-col sm:flex-row justify-between sm:items-center p-4 bg-blue-50 border border-l-4 border-blue-600 rounded-xl mb-3 hover:shadow-md transition gap-3">
                    <div class="flex gap-4 items-center">
                        <div class="w-14 sm:w-16 text-center shrink-0">
                            <p class="text-sm font-bold text-gray-800">10:30</p>
                            <p class="text-xs text-gray-500">AM</p>
                        </div>
                        <div>
                            <span class="text-xs px-2 py-1 bg-orange-100 text-orange-600 rounded-full">LIVE NOW</span>
                            <h3 class="font-semibold text-gray-800 mt-1 text-sm sm:text-base">Product Strategy Review Q3</h3>
                            <p class="text-xs sm:text-sm text-gray-500">Google Meet • Boardroom B</p>
                        </div>
                    </div>
                    <a href="#" class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm hover:bg-blue-700 transition self-start sm:self-auto">
                        Join
                    </a>
                </div>

                <!-- ITEM 2 -->
                <div class="flex flex-col sm:flex-row justify-between sm:items-center p-4 bg-white border border-l-4 border-blue-200 rounded-xl mb-3 hover:bg-blue-50 transition gap-3">
                    <div class="flex gap-4 items-center">
                        <div class="w-14 sm:w-16 text-center shrink-0">
                            <p class="text-sm font-bold text-gray-800">01:00</p>
                            <p class="text-xs text-gray-500">PM</p>
                        </div>
                        <div>
                            <span class="text-xs px-2 py-1 bg-blue-100 text-blue-600 rounded-full">SCHEDULED</span>
                            <h3 class="font-semibold text-gray-800 mt-1 text-sm sm:text-base">Design System Alignment</h3>
                            <p class="text-xs sm:text-sm text-gray-500">SmartMeet Video • Virtual Room</p>
                        </div>
                    </div>
                    <div class="flex gap-2 self-start sm:self-auto">
                        <a href="#" class="text-blue-600 border border-blue-200 px-3 py-2 rounded-lg text-sm hover:bg-blue-600 hover:text-white transition">Manage</a>
                        <a href="#" class="text-gray-600 bg-gray-100 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition">Join Early</a>
                    </div>
                </div>

                <!-- ITEM 3 -->
                <div class="flex flex-col sm:flex-row justify-between sm:items-center p-4 bg-white border border-l-4 border-gray-200 rounded-xl hover:bg-blue-50 transition gap-3">
                    <div class="flex gap-4 items-center">
                        <div class="w-14 sm:w-16 text-center shrink-0">
                            <p class="text-sm font-bold text-gray-800">03:30</p>
                            <p class="text-xs text-gray-500">PM</p>
                        </div>
                        <div>
                            <span class="text-xs px-2 py-1 bg-gray-100 text-gray-600 rounded-full">TECHNICAL</span>
                            <h3 class="font-semibold text-gray-800 mt-1 text-sm sm:text-base">API Integration Sync</h3>
                            <p class="text-xs sm:text-sm text-gray-500">Slack Huddle • No Physical Room</p>
                        </div>
                    </div>
                    <div class="flex gap-2 self-start sm:self-auto">
                        <a href="#" class="text-blue-600 border border-blue-200 px-3 py-2 rounded-lg text-sm hover:bg-blue-600 hover:text-white transition">Manage</a>
                        <a href="#" class="text-gray-600 bg-gray-100 px-3 py-2 rounded-lg text-sm hover:bg-gray-200 transition">Details</a>
                    </div>
                </div>

            </div>
        </div>

    </div>

</div>

</body>
</html>
