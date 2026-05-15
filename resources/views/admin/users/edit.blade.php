<x-layouts.app>

    <x-header.page-title tilte="Admin Dashboard" />

    <div class="min-h-screen flex flex-col bg-gradient-to-br from-gray-50 to-blue-50 mt-2">

        <div class="flex-1 p-3 sm:p-6 overflow-y-auto">

            <p class="text-xs text-gray-400 mb-6 text-center">
                Manage Users <span class="text-gray-300">/</span>
                <span class="text-blue-600 font-medium">Edit {{ $user->name }}</span>
            </p>

            <form action="{{ route('admin.users.update', $user) }}"
                  method="POST"
                  enctype="multipart/form-data"
                  class="bg-white/90 backdrop-blur p-4 sm:p-7 rounded-2xl shadow-lg
                         w-full max-w-4xl mx-auto border border-gray-100">

                @csrf
                @method('PUT')

                <!-- AVATAR -->
                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 sm:gap-6 text-center sm:text-left">

                    <div class="relative flex-shrink-0">
                        <img src="{{ $user->image_url }}"
                             class="w-20 h-20 sm:w-24 sm:h-24 rounded-xl object-cover shadow-md">

                        <!-- ✅ SIRF EK FILE INPUT — camera icon se trigger -->
                        <label for="imageInput"
                               class="absolute bottom-0 right-0 bg-blue-600 p-2 rounded-full cursor-pointer shadow">
                            <i class="fa fa-camera text-white text-xs"></i>
                        </label>
                    </div>

                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Change Avatar</h3>
                        <p class="text-sm text-gray-400">JPG, PNG, WebP — Max 2MB</p>

                        <div class="mt-3 flex items-center gap-3 justify-center sm:justify-start">

                            <!-- Upload Button -->
                            <label for="imageInput"
                                   class="bg-blue-50 text-blue-600 px-4 py-1.5 rounded-lg
                                          text-sm cursor-pointer hover:bg-blue-100 transition">
                                Upload New
                            </label>

                            <!-- ✅ REMOVE — checkbox se -->
                            @if($user->image)
                                <label class="flex items-center gap-1.5 text-sm text-red-500 cursor-pointer">
                                    <input type="checkbox" name="remove_image" value="1"
                                           class="accent-red-500">
                                    Remove Photo
                                </label>
                            @endif

                        </div>
                    </div>

                </div>

                <input type="file"
                       id="imageInput"
                       name="image"
                       accept="image/*"
                       class="hidden">

                <hr class="my-5 sm:my-6 border-gray-200">

                <!-- FORM FIELDS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-5">

                    <div>
                        <label class="text-[11px] text-gray-500">FULL NAME</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}"
                               class="mt-1 w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-md
                               focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" required>
                    </div>

                    <div>
                        <label class="text-[11px] text-gray-500">EMAIL</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}"
                               class="mt-1 w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-md
                               focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none" required>
                    </div>

                    <div>
                        <label class="text-[11px] text-gray-500">ROLE</label>
                        <select name="role"
                                class="mt-1 w-full px-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-md
                                focus:ring-2 focus:ring-blue-200 focus:border-blue-400 outline-none">
                            <option value="admin"       {{ $user->role == 'admin'       ? 'selected' : '' }}>Admin</option>
                            <option value="organizer"   {{ $user->role == 'organizer'   ? 'selected' : '' }}>Organizer</option>
                            <option value="participant" {{ $user->role == 'participant' ? 'selected' : '' }}>Participant</option>
                        </select>
                    </div>

                </div>

                <!-- STATUS -->
                <div class="mt-5 sm:mt-6 bg-gradient-to-r from-gray-50 to-blue-50 p-4 rounded-xl
                            flex justify-between items-center gap-3 border border-gray-100">
                    <div>
                        <p class="text-xs text-gray-400">ACCOUNT STATUS</p>
                        <p class="text-sm font-medium text-gray-700">
                            {{ $user->is_active ? 'Currently Active' : 'Currently Inactive' }}
                        </p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer flex-shrink-0">
                        <input type="checkbox" name="is_active" value="1"
                               class="sr-only peer" {{ $user->is_active ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-300 rounded-full peer peer-checked:bg-blue-500 transition"></div>
                        <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full transition peer-checked:translate-x-5 shadow"></div>
                    </label>
                </div>

                <!-- BUTTONS -->
                <div class="mt-6 sm:mt-7 flex flex-col-reverse sm:flex-row justify-end gap-2 sm:gap-3">
                    <a href="{{ route('admin.users.show', $user) }}"
                       class="w-full sm:w-auto text-center px-4 py-2 text-sm text-gray-500
                              hover:text-gray-700 transition border border-gray-200 rounded-lg">
                        Cancel
                    </a>
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-2 text-sm bg-gradient-to-r from-blue-500 to-blue-600
                                   text-white rounded-lg shadow-md hover:shadow-lg
                                   hover:from-blue-600 hover:to-blue-700 transition">
                        Update User
                    </button>
                </div>

            </form>

        </div>

    </div>

</x-layouts.app>
