<x-layouts.app>

    <x-header.page-title title="Admin Dashboard" />
    <div class="bg-gray-50 p-6 rounded mt-3 overflow-y-auto flex-1 ">
        <!-- Title -->
        <div class="mb-6">
            <h1 class="text-2xl font-semibold">Reports</h1>
        </div>

        <!-- FILTER SECTION -->
        <div class="bg-white p-5 rounded-sm  shadow flex flex-col md:flex-row md:items-end
        md:justify-between gap-4 shadow-[inset_0_4px_10px_rgba(0,0,0,0.2)]">

            <!-- LEFT FILTERS -->
            <div class="flex flex-col md:flex-row gap-4 w-full">

                <!-- DATE RANGE -->
                <div class="w-full md:w-56">
                    <p class="text-xs text-gray-500 mb-1">Date Range</p>
                    <select class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200">
                        <option>Last 7 Days</option>
                        <option>Last 30 Days</option>
                        <option>This Month</option>
                        <option>Last Month</option>
                        <option>This Year</option>
                    </select>
                </div>

                <!-- MEETING TYPE -->
                <div class="w-full md:w-56">
                    <p class="text-xs text-gray-500 mb-1">Meeting Type</p>
                    <select class="w-full p-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-200">
                        <option>All Types</option>
                        <option>Active Meetings</option>
                        <option>Completed</option>
                        <option>Upcoming</option>
                        <option>Cancelled</option>
                    </select>
                </div>


            </div>

            <!-- RIGHT ACTION BUTTONS -->
            <div class="flex gap-3">

                <!-- EXPORT -->
                <button class="px-4 py-2 rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 flex items-center gap-2">
                    <i class="fa-solid fa-file-export"></i>
                    Export
                </button>

                <!-- APPLY -->
                <button class="px-5 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700 shadow-md flex items-center gap-2">
                    <i class="fa-solid fa-filter"></i>
                    Filter
                </button>

            </div>

        </div>

        <!-- STATS -->
        <div class="grid grid-cols-3 gap-4 mt-4">

            <div class="bg-white p-4 rounded-xl shadow-lg border-green-400 border-r-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                     class="size-8 p-2 bg-green-100 rounded-sm mt-2 mb-4 text-green-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.242 5.992h12m-12 6.003H20.24m-12 5.999h12M4.117 7.495v-3.75H2.99m1.125 3.75H2.99m1.125 0H5.24m-1.92 2.577a1.125 1.125 0 1 1 1.591 1.59l-1.83 1.83h2.16M2.99 15.745h1.125a1.125 1.125 0 0 1 0 2.25H3.74m0-.002h.375a1.125 1.125 0 0 1 0 2.25H2.99" />
                </svg>


                <p class="text-gray-500 text-sm">Total Meetings</p>
                <h2 class="text-2xl font-bold">1,284</h2>
                <p class="text-green-500 text-xs">+12%</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-lg border-orange-400 border-r-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                     class="size-8 p-2 bg-orange-100 rounded-sm mt-2 mb-4 text-orange-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.652a3.75 3.75 0 0 1 0-5.304m5.304 0a3.75 3.75 0 0 1 0 5.304m-7.425 2.121a6.75 6.75 0 0 1 0-9.546m9.546 0a6.75 6.75 0 0 1 0 9.546M5.106 18.894c-3.808-3.807-3.808-9.98 0-13.788m13.788 0c3.808 3.807 3.808 9.98 0 13.788M12 12h.008v.008H12V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                </svg>

                <p class="text-gray-500 text-sm">Active Meetings</p>
                <h2 class="text-2xl font-bold">42</h2>
                <p class="text-yellow-500 text-xs">Live</p>
            </div>

            <div class="bg-white p-4 rounded-xl shadow-lg border-green-400 border-r-4">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                     class="size-8 p-2 bg-green-100 rounded-sm mt-2 mb-4 text-green-600">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                </svg>

                <p class="text-gray-500 text-sm">New Users</p>
                <h2 class="text-2xl font-bold">856</h2>
                <p class="text-green-500 text-xs">+8.4%</p>
            </div>

        </div>

        <!-- CHART + STATUS -->
        <div class="bg-white mt-4 p-6 rounded shadow shadow-[inset_0_4px_10px_rgba(0,0,0,0.2)]">

            <h2 class="font-semibold mb-4">Meeting Status</h2>

            <div class="flex justify-center">

                <!-- Fake Donut -->
                <div class="w-40 h-40 rounded-full border-[14px] border-blue-600 border-t-yellow-500 border-l-gray-300 flex items-center justify-center">
                    <div class="text-center">
                        <p class="font-bold">1.2k</p>
                        <p class="text-xs text-gray-500">Total</p>
                    </div>
                </div>

            </div>

            <!-- STATUS -->
            <div class="mt-6 space-y-2">

                <div class="flex justify-between text-sm">
                    <span>Completed</span>
                    <span>65%</span>
                </div>
                <div class="w-full bg-gray-200 h-2 rounded">
                    <div class="bg-blue-600 h-2 rounded w-[65%]"></div>
                </div>

                <div class="flex justify-between text-sm mt-3">
                    <span>Active</span>
                    <span>15%</span>
                </div>
                <div class="w-full bg-gray-200 h-2 rounded">
                    <div class="bg-yellow-500 h-2 rounded w-[15%]"></div>
                </div>

                <div class="flex justify-between text-sm mt-3">
                    <span>Upcoming</span>
                    <span>20%</span>
                </div>
                <div class="w-full bg-gray-200 h-2 rounded">
                    <div class="bg-gray-500 h-2 rounded w-[20%]"></div>
                </div>

            </div>

        </div>

    </div>
</x-layouts.app>
