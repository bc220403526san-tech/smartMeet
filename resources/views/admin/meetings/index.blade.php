<x-layouts.app>

    <x-slot name="header">
        <x-header.search-bar
            placeholder="Search meetings..."
        />
    </x-slot>

    <div class="p-3 sm:p-4 space-y-4 p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">

        <!-- TOP HEADER -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">Manage Meetings</h1>
                <p class="text-gray-400 mt-1 text-sm sm:text-base">Monitor, moderate, and oversee all meetings across the platform.</p>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                <div class="bg-white border border-blue-100 rounded-2xl px-4 py-3 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Total</p>
                    <h3 class="text-xl font-bold text-blue-600">124</h3>
                </div>
                <div class="bg-white border border-green-100 rounded-2xl px-4 py-3 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Active</p>
                    <h3 class="text-xl font-bold text-green-600">18</h3>
                </div>
                <div class="bg-white border border-yellow-100 rounded-2xl px-4 py-3 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Upcoming</p>
                    <h3 class="text-xl font-bold text-yellow-500">42</h3>
                </div>
                <div class="bg-white border border-red-100 rounded-2xl px-4 py-3 shadow-sm">
                    <p class="text-xs text-gray-400 mb-1">Issues</p>
                    <h3 class="text-xl font-bold text-red-500">6</h3>
                </div>
            </div>
        </div>

        <!-- FILTERS -->
        <div class="bg-white border border-gray-200 rounded-2xl p-4 shadow-sm">
            <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                <div class="flex gap-2 items-center flex-wrap">
                    <button class="flex items-center gap-1.5 text-xs px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-600 hover:bg-white transition">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>
                        Filters
                    </button>
                    <button class="text-xs px-4 py-2 rounded-xl bg-blue-600 text-white font-medium shadow-sm">All</button>
                    <button class="text-xs px-4 py-2 rounded-xl border border-gray-200 text-gray-500 hover:bg-blue-50 hover:text-blue-600 transition">Upcoming</button>
                    <button class="text-xs px-4 py-2 rounded-xl border border-gray-200 text-gray-500 hover:bg-green-50 hover:text-green-600 transition">Active</button>
                    <button class="text-xs px-4 py-2 rounded-xl border border-gray-200 text-gray-500 hover:bg-yellow-50 hover:text-yellow-600 transition">In Progress</button>
                    <button class="text-xs px-4 py-2 rounded-xl border border-gray-200 text-gray-500 hover:bg-gray-100 transition">Completed</button>
                </div>
                <div class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 w-fit">
                    <div class="w-2.5 h-2.5 rounded-full bg-green-500 animate-pulse"></div>
                    <p class="text-xs text-gray-500">Showing 1–5 of 124 meetings</p>
                </div>
            </div>
        </div>

        <!-- TABLE -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">

            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-lg">Meetings Overview</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Track all meetings and moderation activities.</p>
                    </div>
                    <button class="px-4 py-2 rounded-xl bg-blue-600 text-white text-sm font-medium shadow hover:bg-blue-700 transition">Export Data</button>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm min-w-[1050px]">
                    <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Meeting</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Organizer</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Date & Time</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Participants</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Status</th>
                        <th class="px-5 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-left">Actions</th>
                    </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">

                    <tr class="hover:bg-blue-50/40 transition duration-200">
                        <td class="px-5 py-4"><p class="font-semibold text-gray-800">Q4 Product Vision Sync</p><p class="text-xs text-gray-400 mt-1">Strategy roadmap discussion</p></td>
                        <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-semibold">AR</div><div><p class="font-medium text-gray-700">Alex Rivera</p><p class="text-xs text-gray-400">Product Manager</p></div></div></td>
                        <td class="px-5 py-4"><p class="font-medium text-gray-700">Oct 24, 2023</p><p class="text-xs text-gray-400 mt-1">10:00 AM</p></td>
                        <td class="px-5 py-4"><div class="inline-flex items-center bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl"><span class="font-medium text-gray-700">12 Participants</span></div></td>
                        <td class="px-5 py-4"><span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-xs font-semibold border border-blue-100"><span class="w-2 h-2 rounded-full bg-blue-500"></span>Upcoming</span></td>
                        <td class="px-5 py-4"><div class="flex items-center gap-2"><x-action-buttons /></div></td>
                    </tr>

                    <tr class="hover:bg-green-50/40 transition duration-200">
                        <td class="px-5 py-4"><p class="font-semibold text-gray-800">Weekly Design Critique</p><p class="text-xs text-gray-400 mt-1">UI improvements & feedback</p></td>
                        <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center text-xs font-semibold">SC</div><div><p class="font-medium text-gray-700">Sarah Chen</p><p class="text-xs text-gray-400">Lead Designer</p></div></div></td>
                        <td class="px-5 py-4"><p class="font-medium text-gray-700">Oct 23, 2023</p><p class="text-xs text-gray-400 mt-1">02:00 PM</p></td>
                        <td class="px-5 py-4"><div class="inline-flex bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl"><span class="font-medium text-gray-700">8 Participants</span></div></td>
                        <td class="px-5 py-4"><span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-yellow-50 text-yellow-700 text-xs font-semibold border border-yellow-100"><span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>In Progress</span></td>
                        <td class="px-5 py-4"><div class="flex gap-2"><x-action-buttons /></div></td>
                    </tr>

                    <tr class="hover:bg-gray-50 transition duration-200">
                        <td class="px-5 py-4"><p class="font-semibold text-gray-800">Stakeholder Feedback</p><p class="text-xs text-gray-400 mt-1">User insights review</p></td>
                        <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center text-xs font-semibold">MT</div><div><p class="font-medium text-gray-700">Marcus T.</p><p class="text-xs text-gray-400">Research Analyst</p></div></div></td>
                        <td class="px-5 py-4"><p class="font-medium text-gray-700">Oct 21, 2023</p><p class="text-xs text-gray-400 mt-1">09:00 AM</p></td>
                        <td class="px-5 py-4"><div class="inline-flex bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl"><span class="font-medium text-gray-700">20 Participants</span></div></td>
                        <td class="px-5 py-4"><span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold border border-gray-200"><span class="w-2 h-2 rounded-full bg-gray-400"></span>Completed</span></td>
                        <td class="px-5 py-4"><div class="flex gap-2"><x-action-buttons /></div></td>
                    </tr>

                    <tr class="hover:bg-red-50/30 transition duration-200">
                        <td class="px-5 py-4"><p class="font-semibold text-gray-800">Budget Q4 Review</p><p class="text-xs text-gray-400 mt-1">Finance discussion</p></td>
                        <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-pink-100 text-pink-700 flex items-center justify-center text-xs font-semibold">AK</div><div><p class="font-medium text-gray-700">Ali Khan</p><p class="text-xs text-gray-400">Finance Head</p></div></div></td>
                        <td class="px-5 py-4"><p class="font-medium text-gray-700">Oct 24, 2023</p><p class="text-xs text-gray-400 mt-1">11:00 AM</p></td>
                        <td class="px-5 py-4"><div class="inline-flex bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl"><span class="font-medium text-gray-700">5 Participants</span></div></td>
                        <td class="px-5 py-4"><span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-red-50 text-red-600 text-xs font-semibold border border-red-100"><span class="w-2 h-2 rounded-full bg-red-400"></span>Incomplete</span></td>
                        <td class="px-5 py-4"><div class="flex gap-2"><x-action-buttons /></div></td>
                    </tr>

                    <tr class="hover:bg-green-50/40 transition duration-200">
                        <td class="px-5 py-4"><p class="font-semibold text-gray-800">All Hands — Oct</p><p class="text-xs text-gray-400 mt-1">Company updates & announcements</p></td>
                        <td class="px-5 py-4"><div class="flex items-center gap-3"><div class="w-10 h-10 rounded-full bg-green-100 text-green-700 flex items-center justify-center text-xs font-semibold">TH</div><div><p class="font-medium text-gray-700">Thomson H.</p><p class="text-xs text-gray-400">Operations Manager</p></div></div></td>
                        <td class="px-5 py-4"><p class="font-medium text-gray-700">Oct 21, 2023</p><p class="text-xs text-gray-400 mt-1">09:00 AM</p></td>
                        <td class="px-5 py-4"><div class="inline-flex bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl"><span class="font-medium text-gray-700">15 Participants</span></div></td>
                        <td class="px-5 py-4"><span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-green-50 text-green-700 text-xs font-semibold border border-green-100"><span class="w-2 h-2 rounded-full bg-green-500"></span>Active</span></td>
                        <td class="px-5 py-4"><div class="flex gap-2"><x-action-buttons /></div></td>
                    </tr>

                    </tbody>
                </table>
            </div>

        </div>

    </div>

</x-layouts.app>
