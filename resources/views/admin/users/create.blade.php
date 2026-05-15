<x-layouts.app>

    <x-header.page-title title="Admin Dashboard" />

    <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-50 to-blue-50 mt-2">

        <!-- CONTENT -->
        <div class="flex flex-col items-center py-8 flex-1">

            <!-- CARD -->
            <div class="bg-white/90 backdrop-blur w-full max-w-2xl rounded-2xl shadow-lg hover:shadow-xl
                        border border-l-8 hover:border-blue-600 border-gray-400 transition-all duration-300 p-6">

                <!-- TITLE -->
                <div class="text-center mb-6">
                    <h1 class="text-2xl font-semibold text-gray-800">Add New User</h1>
                    <p class="text-sm text-gray-400 mt-1">
                        Create profile & assign role access
                    </p>
                </div>

                @if ($errors->any())
                    <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-600 rounded-lg">
                        <ul class="text-sm list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- FORM -->
                <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">

                    @csrf

                    <!-- PROFILE -->
                    <div class="flex flex-col items-center mb-6">

                        <label for="profileImage" class="cursor-pointer relative group">

                            <div class="w-24 h-24 border-2 border-dashed border-gray-300 rounded-xl
                            flex items-center justify-center bg-gray-50 overflow-hidden
                            group-hover:border-blue-400 transition">

                                <!-- placeholder -->
                                <div class="flex flex-col items-center text-gray-300">
                                    <i class="fa-solid fa-image text-2xl"></i>
                                    <span class="text-[10px] mt-1">Upload</span>
                                </div>

                            </div>

                            <div class="absolute bottom-0 right-0 bg-blue-600 text-white p-1.5 rounded-full shadow">
                                <i class="fa-solid fa-pen text-[10px]"></i>
                            </div>

                        </label>

                        <input type="file" id="profileImage" name="image" accept="image/*" class="hidden">

                        <p class="text-[11px] text-gray-400 mt-2">UPLOAD PHOTO</p>

                    </div>

                    <!-- FORM FIELDS -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label class="text-[11px] font-medium text-gray-500">FULL NAME</label>
                            <input type="text" name="name"
                                   class="w-full mt-1 px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-md
                                   focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" required>
                        </div>

                        <div>
                            <label class="text-[11px] font-medium text-gray-500">EMAIL</label>
                            <input type="email" name="email"
                                   class="w-full mt-1 px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-md
                                   focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" required>
                        </div>

                        <!-- ROLE -->
                        <div>
                            <label class="text-[11px] font-medium text-gray-500">ROLE</label>

                            <div class="relative mt-1">

                                <select name="role"
                                        class="w-full appearance-none px-3 py-2.5 text-sm
                                        border border-gray-200 rounded-md
                                        bg-gray-50 text-gray-700
                                        focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                                        hover:border-blue-300 transition shadow-sm">

                                    <option selected disabled>Select a role</option>
                                    <option value="admin">Admin</option>
                                    <option value="organizer">Organizer</option>
                                    <option value="participant">Participant</option>

                                </select>

                                <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </div>

                            </div>
                        </div>

                    </div>

                    <!-- STATUS -->
                    <div class="mt-6 bg-gradient-to-r from-gray-50 to-blue-50 p-4 rounded-xl flex justify-between items-center border border-gray-100">

                        <div>
                            <p class="text-sm font-medium text-gray-700">Account Status</p>
                            <p class="text-xs text-gray-400">
                                Active users can access dashboard instantly
                            </p>
                        </div>

                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" checked>

                            <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-500 transition"></div>

                            <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition
                                        peer-checked:translate-x-5 shadow"></div>
                        </label>

                    </div>

                    <!-- BUTTONS -->
                    <div class="flex justify-end gap-3 mt-6">

                        <button type="button"
                                class="px-4 py-2 text-sm text-gray-500 hover:text-gray-700 transition">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-6 py-2 text-sm bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-lg
                                       shadow-md hover:shadow-lg hover:from-blue-600 hover:to-blue-700 transition">
                            Add User
                        </button>

                    </div>

                </form>

            </div>

        </div>

    </div>

</x-layouts.app>
