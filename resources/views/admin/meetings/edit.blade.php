<x-layouts.app>

    <x-header.page-title title="Admin Dashboard" />
    <!-- PAGE WRAPPER -->
    <div class="min-h-screen flex flex-col items-center bg-gray-50 mt-3">

        <!-- CONTENT -->
        <div class="flex flex-col items-center p-4 w-full bg-gray-50 min-h-screen">

            <!-- CARD -->
            <div class="w-full max-w-3xl bg-white rounded-2xl shadow-md hover:shadow-xl
                border border-gray-100 p-5 transition-all duration-300">

                <!-- HEADER -->
                <div class="mb-5 flex items-start justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-800">Edit Meeting</h2>
                        <p class="text-xs text-gray-400">
                            Update meeting details and manage participants
                        </p>
                    </div>
                    <div class="bg-blue-100 text-blue-600 px-3 py-2 rounded-lg text-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="h-4 w-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125" />
                        </svg>

                    </div>
                </div>

                <!-- GENERAL INFO -->
                <div class="mb-5 bg-gray-50 p-4 rounded-xl border border-gray-100">

                    <h3 class="text-blue-600 text-xs font-semibold mb-3 flex items-center gap-2">
                        • GENERAL INFORMATION
                    </h3>

                    <label class="text-xs text-gray-500">Meeting Title</label>
                    <input type="text" value="Q4 Product Vision Sync"
                           class="w-full mt-1 mb-3 px-3 py-2 text-sm bg-white border border-gray-200 rounded-md
                   focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">

                        <div>
                            <label class="text-xs text-gray-500">Date</label>
                            <input type="date" value="2023-10-24"
                                   class="w-full mt-1 px-3 py-2 text-sm bg-white border border-gray-200 rounded-md
                           focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">Start Time</label>
                            <input type="time" value="10:00"
                                   class="w-full mt-1 px-3 py-2 text-sm bg-white border border-gray-200 rounded-md
                           focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">End Time</label>
                            <input type="time" value="11:30"
                                   class="w-full mt-1 px-3 py-2 text-sm bg-white border border-gray-200 rounded-md
                           focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
                        </div>

                        <div>
                            <label class="text-xs text-gray-500">Organizer</label>
                            <select class="w-full mt-1 px-3 py-2 text-sm bg-white border border-gray-200 rounded-md
                        focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
                                <option>Alex Rivera</option>
                            </select>
                        </div>

                    </div>
                </div>

                <!-- PARTICIPANTS -->
                <div class="mb-5 bg-gray-50 p-4 rounded-xl border border-gray-100">

                    <h3 class="text-blue-600 text-xs font-semibold mb-3 flex items-center gap-2">
                        • PARTICIPANTS
                    </h3>

                    <div class="flex flex-wrap gap-2">

                <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs flex items-center gap-1">
                    Sarah Chen <span class="cursor-pointer hover:text-red-500">✕</span>
                </span>

                        <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs flex items-center gap-1">
                    Marcus Thorne <span class="cursor-pointer hover:text-red-500">✕</span>
                </span>

                        <span class="bg-blue-100 text-blue-600 px-2 py-1 rounded-full text-xs flex items-center gap-1">
                    Janet Doe <span class="cursor-pointer hover:text-red-500">✕</span>
                </span>

                        <button type="button" class="text-blue-500 text-xs hover:underline ml-1">
                            + Add More
                        </button>

                    </div>

                    <p class="text-[11px] text-gray-400 mt-2">
                        3 participants selected
                    </p>

                </div>

                <!-- AGENDA -->
                <div class="mb-5 bg-gray-50 p-4 rounded-xl border border-gray-100">

                    <h3 class="text-blue-600 text-xs font-semibold mb-3 flex items-center gap-2">
                        • AGENDA DETAILS
                    </h3>

                    <label class="text-xs text-gray-500">Meeting Description</label>

                    <textarea rows="3"
                              class="w-full mt-1 px-3 py-2 text-sm bg-white border border-gray-200 rounded-md
                      focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
Strategic roadmap and resource allocation for the upcoming quarter.
            </textarea>

                </div>

                <!-- BUTTONS -->
                <div class="flex justify-end gap-2 pt-2">

                    <a href="#"
                       class="text-gray-500 text-sm px-4 py-2 hover:text-gray-700">
                        Cancel
                    </a>

                    <button type="submit"
                            class="bg-blue-500 text-white text-sm px-5 py-2 rounded-md
                    hover:bg-blue-600 shadow-sm transition">
                        Update
                    </button>

                </div>

            </div>

        </div>

    </div>

</x-layouts.app>
