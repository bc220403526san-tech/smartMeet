<x-layouts.app>

    <x-header.page-title title="Admin Dashboard" />

    <!-- CONTENT WRAPPER -->
    <div class="flex flex-col min-h-full">

        <!-- CONTENT -->
        <div class="p-6 bg-gray-50 rounded-lg mt-3 flex-1">

            <!-- BACK -->
            <p class="text-sm text-blue-600 mb-3 cursor-pointer">
                ← Back to Meetings
            </p>

            <!-- TITLE + BUTTONS -->
            <div class="flex justify-between items-start mb-5">

                <div>
                    <h2 class="text-2xl font-bold">Meeting Details</h2>
                    <p class="text-gray-500 text-sm">
                        Review meeting logistics, participants and agenda.
                    </p>
                </div>

                <div class="flex gap-3">

                    <button class="px-4 py-2 bg-gray-200 rounded-lg text-sm">
                        Edit Details
                    </button>

                    <button class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm">
                        Launch Meeting
                    </button>

                </div>

            </div>

            <!-- MAIN GRID -->
            <div class="grid grid-cols-3 gap-6">

                <!-- LEFT MAIN CARD -->
                <div class="col-span-2 bg-white p-6 rounded-xl shadow border border-blue-600 border-l-6">

                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold">Q4 Product Vision Sync</h3>

                        <span class="bg-blue-100 text-blue-600 text-xs px-3 py-1 rounded-full">
                            UPCOMING
                        </span>
                    </div>

                    <div class="flex gap-4 text-sm text-gray-500 mt-2">
                        <span><i class="fa-regular fa-calendar"></i> Oct 24, 2023</span>
                        <span><i class="fa-regular fa-clock"></i> 10:00 AM - 11:30 AM</span>
                    </div>

                    <div class="mt-5">
                        <h4 class="text-sm font-semibold mb-2">DESCRIPTION</h4>
                        <p class="text-sm text-gray-600 leading-6">
                            Strategic roadmap and resource allocation for upcoming quarter.
                            We will discuss product ecosystem, growth pillars and planning for 2024.
                        </p>
                    </div>

                </div>

                <!-- RIGHT ORGANIZER -->
                <div class="bg-white p-6 rounded-xl shadow border border-gray-600 border-l-6">

                    <h4 class="text-sm font-semibold text-gray-500 mb-3">ORGANIZER</h4>

                    <div class="text-center">

                        <img src="{{ asset('images/profile.jpg') }}"
                             class="w-20 h-20 rounded-full mx-auto">

                        <h3 class="font-bold mt-3">Alex Rivera</h3>
                        <p class="text-sm text-gray-500">Lead Product Manager</p>

                        <button class="mt-4 w-full bg-blue-600 text-white py-2 rounded-lg text-sm">
                            Contact Organizer
                        </button>

                        <p class="text-xs text-gray-500 mt-3 cursor-pointer">
                            View Full Profile
                        </p>

                    </div>

                </div>

            </div>

            <!-- PARTICIPANTS -->
            <div class="bg-white mt-6 p-6 rounded-xl shadow border border-green-600 border-l-6">

                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold">PARTICIPANTS (3)</h3>
                    <button class="text-blue-600 text-sm">Manage All</button>
                </div>

                <div class="grid grid-cols-2 gap-4">

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold">Sarah Chen</p>
                            <p class="text-xs text-gray-500">Lead Designer</p>
                        </div>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">ADMIN</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold">Marcus Thorne</p>
                            <p class="text-xs text-gray-500">CTO</p>
                        </div>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">SPEAKER</span>
                    </div>

                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold">Janet Doe</p>
                            <p class="text-xs text-gray-500">Marketing Lead</p>
                        </div>
                        <span class="text-xs bg-gray-200 px-2 py-1 rounded">VIEWER</span>
                    </div>

                </div>

            </div>

        </div>

    </div>

</x-layouts.app>
