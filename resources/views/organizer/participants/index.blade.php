<x-layouts.app>

    <x-slot name="header">
        <x-header.search-bar placeholder="Search Participants..." />
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        <!-- PAGE TITLE + CTA -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Participants</h1>
                <p class="text-sm text-gray-400 mt-0.5">Manage and monitor attendee status across all sessions.</p>
            </div>
        </div>

        <!-- STAT CARDS -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
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

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
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

            <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-md transition">
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
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-lg">Participants Overview</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Manage and monitor all participant activities.</p>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[900px]">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">NAME</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">EMAIL ADDRESS</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">STATUS</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">LAST ACTIVE</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">ACTIONS</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <!-- ROW 1 -->
                    <tr class="hover:bg-blue-50/30 transition duration-200">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/40?img=5" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100" alt="">
                                <div>
                                    <p class="font-semibold text-gray-800">Sarah Chen</p>
                                    <p class="text-xs text-gray-400">ID: #P-001</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-600">sarah.chen@techflow.io</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border bg-blue-50 text-blue-700 border-blue-100">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Joined
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium border border-gray-200 bg-gray-50 text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                2 mins ago
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <!-- VIEW -->
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow" title="View Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <!-- DELETE -->
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow" title="Delete Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-red-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- ROW 2 -->
                    <tr class="hover:bg-blue-50/30 transition duration-200">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/40?img=8" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100" alt="">
                                <div>
                                    <p class="font-semibold text-gray-800">Marcus Wright</p>
                                    <p class="text-xs text-gray-400">ID: #P-002</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-600">m.wright@globalnexus.com</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border bg-red-50 text-red-600 border-red-100">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                Not Joined
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium border border-gray-200 bg-gray-50 text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                Yesterday
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow" title="View Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow" title="Delete Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-red-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- ROW 3 -->
                    <tr class="hover:bg-blue-50/30 transition duration-200">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/40?img=12" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100" alt="">
                                <div>
                                    <p class="font-semibold text-gray-800">Elena Rodriguez</p>
                                    <p class="text-xs text-gray-400">ID: #P-003</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-600">elena.rod@creativepulse.org</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border bg-blue-50 text-blue-700 border-blue-100">
                                <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                                Joined
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium border border-gray-200 bg-gray-50 text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                15 mins ago
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow" title="View Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow" title="Delete Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-red-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- ROW 4 -->
                    <tr class="hover:bg-blue-50/30 transition duration-200">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/40?img=15" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100" alt="">
                                <div>
                                    <p class="font-semibold text-gray-800">David Kim</p>
                                    <p class="text-xs text-gray-400">ID: #P-004</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-600">dkim@strategicvision.co</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border bg-red-50 text-red-600 border-red-100">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                Not Joined
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium border border-gray-200 bg-gray-50 text-gray-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                Never
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow" title="View Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow" title="Delete Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-red-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- ROW 5 -->
                    <tr class="hover:bg-blue-50/30 transition duration-200">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <img src="https://i.pravatar.cc/40?img=20" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100" alt="">
                                <div>
                                    <p class="font-semibold text-gray-800">Sophie Dubois</p>
                                    <p class="text-xs text-gray-400">ID: #P-005</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <p class="text-sm text-gray-600">s.dubois@luxdesign.fr</p>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border bg-green-50 text-green-700 border-green-100">
                                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                Active Now
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium border border-green-200 bg-green-50 text-green-600">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></span>
                                Active Now
                            </span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow" title="View Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                </a>
                                <a href="#" class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow" title="Delete Participant">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-red-600 transition">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </a>
                            </div>
                        </td>
                    </tr>

                    </tbody>
                </table>
            </div>

            <!-- PAGINATION -->
            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row justify-between items-center gap-3">
                <p class="text-xs text-gray-500">
                    Showing 1–5 of 1,248 participants
                </p>
                <div class="flex items-center gap-2">
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-500 text-xs hover:bg-gray-50 transition disabled:opacity-50" disabled>
                        Previous
                    </button>
                    <button class="px-3 py-1.5 rounded-lg bg-blue-600 text-white text-xs font-medium shadow-sm">
                        1
                    </button>
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-500 text-xs hover:bg-gray-50 transition">
                        2
                    </button>
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-500 text-xs hover:bg-gray-50 transition">
                        3
                    </button>
                    <button class="px-3 py-1.5 rounded-lg border border-gray-200 bg-white text-gray-500 text-xs hover:bg-gray-50 transition">
                        Next
                    </button>
                </div>
            </div>

        </div>
        <!-- end table wrapper -->

    </div>

</x-layouts.app>
