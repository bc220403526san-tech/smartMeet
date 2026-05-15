<x-layouts.app>
    <x-header.search-bar
        placeholder="Search for users, roles, or status..."
        actionRoute="admin.users.create" />

    <div class="p-3 sm:p-6 bg-gray-50 rounded-lg overflow-y-auto mt-3">

        <x-success />
        <x-error />

        <!-- TITLE -->
        <h1 class="text-xl sm:text-2xl font-semibold">Manage Users</h1>
        <p class="text-sm text-gray-400 mb-4 sm:mb-5">
            View, manage, and control all registered users.
        </p>


        <!-- STATS -->
        <x-stats
            :totalUsers="$totalUsers"
            :activeUsers="$activeUsers"
            :inactiveUsers="$inactiveUsers"
        />

        <!-- USER DIRECTORY -->
        <div class="bg-white p-3 sm:p-5 rounded shadow-sm hover:shadow-lg border-blue-100 border-4">

            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">

                <!-- LEFT — TITLE -->
                <h2 class="text-base font-semibold text-gray-700">User Directory</h2>

                <!-- RIGHT — FILTER + EXPORT -->
                <div class="flex items-center gap-2">

                    <!-- FILTER FORM -->
                    <form method="GET" action="{{ route('admin.users.index') }}"
                          class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-2 shadow-sm">

                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                             class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75" />
                        </svg>


                        <select name="role"
                                class="text-sm text-gray-600 bg-transparent outline-none cursor-pointer pr-2">
                            <option value="">All Roles</option>
                            <option value="admin"       {{ request('role') == 'admin'       ? 'selected' : '' }}>Admin</option>
                            <option value="organizer"   {{ request('role') == 'organizer'   ? 'selected' : '' }}>Organizer</option>
                            <option value="participant" {{ request('role') == 'participant' ? 'selected' : '' }}>Participant</option>
                        </select>

                        <button type="submit"
                                class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-blue-700 transition">
                            Filter
                        </button>

                    </form>

                    <!-- EXPORT BUTTON -->
                    <button class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200
                       text-gray-600 text-xs px-3 py-2 rounded-xl border border-gray-200 transition">
                        <i class="fa-solid fa-download text-[10px]"></i>
                        Export
                    </button>

                </div>

            </div>


            <!-- TABLE HEADER -->
            <div class="hidden md:grid md:grid-cols-5 text-sm text-blue-500
                 border border-gray-200 p-4 bg-blue-50 rounded-sm shadow-[inset_0_4px_10px_rgba(0,0,0,0.2)]">
                <p>Name</p>
                <p>Email</p>
                <p>Role</p>
                <p>Status</p>
                <p>Actions</p>
            </div>

            @foreach($users as $user)

                {{-- DESKTOP --}}
                <div class="hidden md:grid md:grid-cols-5 items-center py-4 border-b">
                    <div class="flex items-center gap-2">
                        <img src="{{ $user->image_url }}"
                             class="w-8 h-8 rounded-full object-cover">
                        <span class="text-sm">{{ $user->name }}</span>
                    </div>

                    <p class="text-sm">{{ $user->email }}</p>

                    <span class="px-2 py-1 rounded text-xs w-fit
            {{ $user->role == 'admin' ? 'bg-blue-100 text-blue-600' : '' }}
            {{ $user->role == 'organizer' ? 'bg-gray-200 text-gray-600' : '' }}
            {{ $user->role == 'participant' ? 'bg-green-100 text-green-600' : '' }}">
            {{ ucfirst($user->role) }}
        </span>

                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                        <span class="text-sm">
                {{ $user->is_active ? 'Active' : 'Inactive' }}
            </span>
                    </div>

                    <div class="flex gap-3 text-gray-500">
                        <div class="flex gap-3 text-gray-500">
                            <x-icons :user="$user" />
                        </div>

                    </div>
                </div>

                {{-- MOBILE --}}
                <div class="md:hidden p-3 border-b">
                    <div class="flex justify-between items-start mb-2">
                        <div class="flex items-center gap-2">
                            <img
                                src="{{ $user->image ? asset('storage/' . $user->image) : $user->image_url }}"
                                class="w-9 h-9 rounded-full"
                            >
                            <div>
                                <p class="font-medium text-sm">{{ $user->name }}</p>
                                <p class="text-xs text-gray-400">{{ $user->email }}</p>
                            </div>
                        </div>

                        <span class="px-2 py-1 rounded text-xs
                {{ $user->role == 'admin' ? 'bg-blue-100 text-blue-600' : '' }}
                {{ $user->role == 'organizer' ? 'bg-gray-200 text-gray-600' : '' }}
                {{ $user->role == 'participant' ? 'bg-green-100 text-green-600' : '' }}">
                {{ ucfirst($user->role) }}
            </span>
                    </div>

                    <div class="flex justify-between items-center">
                        <div class="flex items-center gap-1">
                            <span class="w-2 h-2 {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                            <span class="text-xs text-gray-600">
                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
                        </div>

                        <div class="flex gap-3 text-gray-500">
                            <div class="flex gap-3 text-gray-500">
                                <x-icons :user="$user" />
                            </div>

                        </div>
                    </div>
                </div>

            @endforeach

        </div>
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>



</x-layouts.app>
