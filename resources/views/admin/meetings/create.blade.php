<x-layouts.app>

    <x-header.page-title title="Admin Dashboard" />

    <!-- CONTENT WRAPPER -->
    <div class="p-3 bg-gray-50 min-h-full flex flex-col mt-2">

        <!-- CENTER CONTENT -->
        <div class="flex-1 flex items-center justify-center p-3">

            <div class="w-full max-w-4xl">

                <!-- CARD -->
                <div class="bg-white p-6 w-full rounded-2xl shadow-md hover:shadow-xl
                        border border-gray-100 transition-all duration-300">

                    <!-- Header -->
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-800">Create Meeting</h2>
                            <p class="text-gray-400 text-xs">
                                Schedule and manage your session easily
                            </p>
                        </div>
                        <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                            <i class="fa-solid fa-calendar-plus"></i>
                        </div>
                    </div>

                    <!-- FORM -->
                    <form action="{{ route('admin.meetings.create') }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- TITLE -->
                        <div>
                            <label class="text-xs text-gray-500">Title</label>
                            <input type="text" name="title"
                                   placeholder="Meeting title"
                                   class="w-full mt-1 px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md
                               focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
                        </div>

                        <!-- DATE + TIME -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">

                            <div>
                                <label class="text-xs text-gray-500">Date</label>
                                <input type="date" name="date"
                                       class="w-full mt-1 px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md
                                   focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
                            </div>

                            <div>
                                <label class="text-xs text-gray-500">Start</label>
                                <input type="time" name="start_time"
                                       class="w-full mt-1 px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md
                                   focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
                            </div>

                            <div>
                                <label class="text-xs text-gray-500">End</label>
                                <input type="time" name="end_time"
                                       class="w-full mt-1 px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md
                                   focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
                            </div>

                        </div>

                        <!-- ORGANIZER -->
                        <div>
                            <label class="text-xs text-gray-500">Organizer</label>
                            <input type="text" name="organizer"
                                   placeholder="Organizer name"
                                   class="w-full mt-1 px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md
                               focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none">
                        </div>

                        <!-- PARTICIPANTS -->
                        <div>
                            <label class="text-xs text-gray-500">Participants</label>

                            <div class="mt-1 flex items-center gap-2 flex-wrap bg-gray-100 p-2 rounded-md">

                                <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs flex items-center gap-2">
                                    Jordan Smith
                                    <i class="fa-solid fa-xmark text-xs"></i> </span>
                                <span class="bg-blue-100 text-blue-600 px-3 py-1 rounded-full text-xs flex items-center gap-2">
                                    Maria Lopez
                                    <i class="fa-solid fa-xmark text-xs"></i> </span>

                                <button type="button" class="text-blue-500 text-xs hover:underline">
                                    + Add Member
                                </button>

                            </div>
                        </div>

                        <!-- DESCRIPTION -->
                        <div>
                            <label class="text-xs text-gray-500">Description</label>
                            <textarea name="description" rows="3"
                                      placeholder="Meeting details..."
                                      class="w-full mt-1 px-3 py-2 text-sm bg-gray-50 border border-gray-200 rounded-md
                                  focus:ring-1 focus:ring-blue-400 focus:border-blue-400 outline-none"></textarea>
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
                                Create
                            </button>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>
</x-layouts.app>
