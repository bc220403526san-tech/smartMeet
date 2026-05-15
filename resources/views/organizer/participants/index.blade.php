<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/css/app.css')
</head>

<body class="bg-[#f4f5f7]">

<!-- Hidden checkbox for sidebar toggle -->
<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen overflow-hidden">

    <!-- OVERLAY (mobile) -->
    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    <!-- SIDEBAR -->
    <x-sidebar.organizer-menu />

    <!-- ══════════════════════════════
         MAIN
    ══════════════════════════════ -->
    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0">

        <!-- HEADER -->
       <x-header.search-bar
       placeholder="Search Participants..."
       />

        <!-- ══ CONTENT ══ -->
        <div class="p-4 sm:p-6 bg-gray-50 rounded-xl mt-3 overflow-y-auto flex-1">

            <!-- PAGE TITLE + CTA -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Participants</h1>
                    <p class="text-sm text-gray-400 mt-0.5">Manage and monitor attendee status across all sessions.</p>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <button class="flex items-center gap-2 px-4 py-2.5 border border-gray-200 bg-white text-gray-600 rounded-xl text-sm hover:bg-gray-50 transition font-medium">
                        <svg style="width:14px;height:14px" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        Export
                    </button>
                    <a href="{{ route('organizer.participants.create') }}"
                       class="flex items-center gap-2 bg-blue-600 text-white px-4 py-2.5 rounded-xl text-sm hover:bg-blue-700 transition font-medium shadow-sm">

                        <svg style="width:14px;height:14px" xmlns="http://www.w3.org/2000/svg" fill="none"
                             viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                        </svg>

                        Add Participant
                    </a>
                </div>
            </div>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-1 mt-4 sm:grid-cols-3 gap-3">

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-400 tracking-widest">TOTAL INVITED</p>
                            <h2 class="text-3xl font-semibold text-gray-800 mt-1.5">1,248</h2>
                            <p class="text-xs text-blue-500 mt-1.5 font-medium">↑ 12% from last month</p>
                        </div>
                        <div class="w-10 h-10 bg-blue-50 rounded-xl flex items-center justify-center shrink-0">
                            <svg style="width:18px;height:18px" class="text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-400 tracking-widest">ACTIVE NOW</p>
                            <h2 class="text-3xl font-semibold text-gray-800 mt-1.5 flex items-center gap-2">
                                84
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-amber-500"></span>
                                </span>
                            </h2>
                            <p class="text-xs text-amber-500 mt-1.5 font-medium">Live across 3 sessions</p>
                        </div>
                        <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                            <svg style="width:18px;height:18px" class="text-amber-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs font-medium text-gray-400 tracking-widest">AVG. ENGAGEMENT</p>
                            <h2 class="text-3xl font-semibold text-gray-800 mt-1.5">92%</h2>
                            <p class="text-xs text-emerald-500 mt-1.5 font-medium">↑ 5% from last week</p>
                        </div>
                        <div class="w-10 h-10 bg-emerald-50 rounded-xl flex items-center justify-center shrink-0">
                            <svg style="width:18px;height:18px" class="text-emerald-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.7" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 0 1 3 19.875v-6.75ZM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V8.625ZM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 0 1-1.125-1.125V4.125Z" />
                            </svg>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ══ TABLE WRAPPER ══ -->
            <div class="bg-white border border-blue-500 border-l-[6px] border-r-[6px] rounded-md overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 ease-in-out p-4 mt-6">

                <!-- ── DESKTOP TABLE (lg+) ── -->
                <div class="hidden lg:block">

                    <!-- Header -->
                    <div class="grid grid-cols-5 text-xs text-blue-600 font-semibold mt-6 mb-6 mx-4
                                border border-gray-200 p-4 bg-blue-50 rounded-sm
                                shadow-[inset_0_4px_10px_rgba(0,0,0,0.1)]">
                        <div>NAME</div>
                        <div>EMAIL ADDRESS</div>
                        <div>STATUS</div>
                        <div>LAST ACTIVE</div>
                        <div>ACTIONS</div>
                    </div>

                    <!-- Rows -->
                    <!-- ROW 1 -->
                    <div class="grid grid-cols-5 px-6 py-4 items-center border-t hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/40?img=5" class="w-8 h-8 rounded-full shrink-0" alt="">
                            <h3 class="text-sm font-semibold text-gray-800">Sarah Chen</h3>
                        </div>
                        <div class="text-sm text-gray-600">sarah.chen@techflow.io</div>
                        <div><span class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-full">Joined</span></div>
                        <div><span class="px-3 py-1 text-xs border border-gray-200 rounded-full">● 2 mins ago</span></div>
                        <div class="flex items-center gap-3 text-gray-500">
{{--                            <x-meeting-icons />--}}
                        </div>
                    </div>

                    <!-- ROW 2 -->
                    <div class="grid grid-cols-5 px-6 py-4 items-center border-t hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/40?img=8" class="w-8 h-8 rounded-full shrink-0" alt="">
                            <h3 class="text-sm font-semibold text-gray-800">Marcus Wright</h3>
                        </div>
                        <div class="text-sm text-gray-600">m.wright@globalnexus.com</div>
                        <div><span class="px-3 py-1 text-xs bg-red-100 text-red-600 rounded-full">Not Joined</span></div>
                        <div><span class="px-3 py-1 text-xs border border-gray-200 rounded-full">● Yesterday</span></div>
                        <div class="flex items-center gap-3 text-gray-500">
{{--                            <x-meeting-icons />--}}
                        </div>
                    </div>

                    <!-- ROW 3 -->
                    <div class="grid grid-cols-5 px-6 py-4 items-center border-t hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/40?img=12" class="w-8 h-8 rounded-full shrink-0" alt="">
                            <h3 class="text-sm font-semibold text-gray-800">Elena Rodriguez</h3>
                        </div>
                        <div class="text-sm text-gray-600">elena.rod@creativepulse.org</div>
                        <div><span class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-full">Joined</span></div>
                        <div><span class="px-3 py-1 text-xs border border-gray-200 rounded-full">● 15 mins ago</span></div>
                        <div class="flex items-center gap-3 text-gray-500">
{{--                            <x-meeting-icons />--}}
                        </div>
                    </div>

                    <!-- ROW 4 -->
                    <div class="grid grid-cols-5 px-6 py-4 items-center border-t hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/40?img=15" class="w-8 h-8 rounded-full shrink-0" alt="">
                            <h3 class="text-sm font-semibold text-gray-800">David Kim</h3>
                        </div>
                        <div class="text-sm text-gray-600">dkim@strategicvision.co</div>
                        <div><span class="px-3 py-1 text-xs bg-red-100 text-red-600 rounded-full">Not Joined</span></div>
                        <div><span class="px-3 py-1 text-xs border border-gray-200 rounded-full">● Never</span></div>
                        <div class="flex items-center gap-3 text-gray-500">
{{--                            <x-meeting-icons />--}}
                        </div>
                    </div>

                    <!-- ROW 5 -->
                    <div class="grid grid-cols-5 px-6 py-4 items-center border-t hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <img src="https://i.pravatar.cc/40?img=20" class="w-8 h-8 rounded-full shrink-0" alt="">
                            <h3 class="text-sm font-semibold text-gray-800">Sophie Dubois</h3>
                        </div>
                        <div class="text-sm text-gray-600">s.dubois@luxdesign.fr</div>
                        <div><span class="px-3 py-1 text-xs bg-blue-100 text-blue-600 rounded-full">Joined</span></div>
                        <div><span class="px-3 py-1 text-xs border border-gray-200 rounded-full">● Active Now</span></div>
                        <div class="flex items-center gap-3 text-gray-500">
{{--                            <x-meeting-icons />--}}
                        </div>
                    </div>

                </div>

                <!-- ── MOBILE / TABLET CARD LIST (below lg) ── -->
                <div class="lg:hidden space-y-3 mt-2">

                    <!-- Mobile table label -->
                    <p class="text-xs font-semibold text-blue-600 tracking-widest px-1">PARTICIPANTS</p>

                    <!-- Card template — repeat for each participant -->
                    <!-- Card 1 -->
                    <div class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="https://i.pravatar.cc/40?img=5" class="w-9 h-9 rounded-full shrink-0" alt="">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">Sarah Chen</p>
                                <p class="text-xs text-gray-400 truncate">sarah.chen@techflow.io</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2.5 py-1 text-xs bg-blue-100 text-blue-600 rounded-full whitespace-nowrap">Joined</span>
                            <span class="text-xs text-gray-400 whitespace-nowrap hidden sm:inline">● 2 mins ago</span>
                            <!-- kebab / actions -->
                            <div class="flex items-center gap-2 text-gray-400 ml-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 17.25 4.875"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="https://i.pravatar.cc/40?img=8" class="w-9 h-9 rounded-full shrink-0" alt="">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">Marcus Wright</p>
                                <p class="text-xs text-gray-400 truncate">m.wright@globalnexus.com</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2.5 py-1 text-xs bg-red-100 text-red-600 rounded-full whitespace-nowrap">Not Joined</span>
                            <span class="text-xs text-gray-400 whitespace-nowrap hidden sm:inline">● Yesterday</span>
                            <div class="flex items-center gap-2 text-gray-400 ml-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 17.25 4.875"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="https://i.pravatar.cc/40?img=12" class="w-9 h-9 rounded-full shrink-0" alt="">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">Elena Rodriguez</p>
                                <p class="text-xs text-gray-400 truncate">elena.rod@creativepulse.org</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2.5 py-1 text-xs bg-blue-100 text-blue-600 rounded-full whitespace-nowrap">Joined</span>
                            <span class="text-xs text-gray-400 whitespace-nowrap hidden sm:inline">● 15 mins ago</span>
                            <div class="flex items-center gap-2 text-gray-400 ml-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 17.25 4.875"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="https://i.pravatar.cc/40?img=15" class="w-9 h-9 rounded-full shrink-0" alt="">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">David Kim</p>
                                <p class="text-xs text-gray-400 truncate">dkim@strategicvision.co</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2.5 py-1 text-xs bg-red-100 text-red-600 rounded-full whitespace-nowrap">Not Joined</span>
                            <span class="text-xs text-gray-400 whitespace-nowrap hidden sm:inline">● Never</span>
                            <div class="flex items-center gap-2 text-gray-400 ml-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 17.25 4.875"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </div>
                        </div>
                    </div>

                    <!-- Card 5 -->
                    <div class="flex items-center justify-between gap-3 border border-gray-100 rounded-xl px-4 py-3 hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3 min-w-0">
                            <img src="https://i.pravatar.cc/40?img=20" class="w-9 h-9 rounded-full shrink-0" alt="">
                            <div class="min-w-0">
                                <p class="text-sm font-semibold text-gray-800 truncate">Sophie Dubois</p>
                                <p class="text-xs text-gray-400 truncate">s.dubois@luxdesign.fr</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="px-2.5 py-1 text-xs bg-blue-100 text-blue-600 rounded-full whitespace-nowrap">Joined</span>
                            <span class="text-xs text-gray-400 whitespace-nowrap hidden sm:inline">● Active Now</span>
                            <div class="flex items-center gap-2 text-gray-400 ml-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-blue-600"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 7.125 17.25 4.875"/></svg>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 cursor-pointer hover:text-red-500"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/></svg>
                            </div>
                        </div>
                    </div>

                </div>
                <!-- end mobile cards -->

            </div>
            <!-- end table wrapper -->

        </div>
        <!-- end content -->

    </div>
    <!-- end main -->

</div>
<!-- end layouts-wrapper -->

</body>
</html>
