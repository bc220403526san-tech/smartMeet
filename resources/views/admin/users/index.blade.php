<x-layouts.app>

    <x-slot name="header">
        <x-header.search-bar placeholder="Search for users, roles, or status..." />
    </x-slot>

    <div class="p-3 sm:p-4 space-y-4 p-3 sm:p-4 space-y-4 p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">

        <x-success />
        <x-error />

        <!-- TITLE -->
        <div>
            <h1 class="text-xl sm:text-2xl font-semibold">Manage Users</h1>
            <p class="text-sm text-gray-400 mt-1">View, manage, and control all registered users.</p>
        </div>

        <!-- STATS -->
        <x-stats
            :totalUsers="$totalUsers"
            :activeUsers="$activeUsers"
            :inactiveUsers="$inactiveUsers"
        />

        <!-- USER DIRECTORY -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">

            <!-- DIRECTORY HEADER -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 p-4 sm:p-5 border-b border-gray-100">

                <h2 class="text-base font-semibold text-gray-700">User Directory</h2>

                <div class="flex items-center gap-2 w-full sm:w-auto">

                    <!-- FILTER FORM -->
                    <form method="GET" action="{{ route('admin.users.index') }}"
                          class="flex items-center gap-2 bg-gray-50 border border-gray-200 rounded-xl px-3 py-2 flex-1 sm:flex-none">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-400 shrink-0">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/>
                        </svg>
                        <select name="role" class="text-sm text-gray-600 bg-transparent outline-none cursor-pointer">
                            <option value="">All Roles</option>
                            <option value="admin"       {{ request('role') == 'admin'       ? 'selected' : '' }}>Admin</option>
                            <option value="organizer"   {{ request('role') == 'organizer'   ? 'selected' : '' }}>Organizer</option>
                            <option value="participant" {{ request('role') == 'participant' ? 'selected' : '' }}>Participant</option>
                        </select>
                        <button type="submit"
                                class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded-lg hover:bg-blue-700 transition shrink-0">
                            Filter
                        </button>
                    </form>

                    <!-- EXPORT -->
                    <button class="flex items-center gap-1.5 bg-gray-100 hover:bg-gray-200
                                   text-gray-600 text-xs px-3 py-2 rounded-xl border border-gray-200 transition shrink-0">
                        <i class="fa-solid fa-download text-[10px]"></i>
                        <span class="hidden sm:inline">Export</span>
                    </button>

                </div>
            </div>

            <!-- TABLE HEADER — desktop only -->
            <div class="hidden md:grid md:grid-cols-5 text-xs font-semibold text-blue-500 uppercase tracking-wider
                        bg-blue-50 border-b border-blue-100 px-5 py-3">
                <p>Name</p>
                <p>Email</p>
                <p>Role</p>
                <p>Status</p>
                <p>Actions</p>
            </div>

            <!-- ROWS -->
            <div class="divide-y divide-gray-100">
                @foreach($users as $user)

                    {{-- DESKTOP ROW --}}
                    <div class="hidden md:grid md:grid-cols-5 items-center px-5 py-4 hover:bg-gray-50 transition">

                        <div class="flex items-center gap-3">
                            <img src="{{ $user->image ? asset('storage/' . $user->image) : $user->image_url }}"
                                 class="w-9 h-9 rounded-full object-cover shrink-0">
                            <span class="text-sm font-medium text-gray-800">{{ $user->name }}</span>
                        </div>

                        <p class="text-sm text-gray-500 truncate pr-4">{{ $user->email }}</p>

                        <span class="px-2.5 py-1 rounded-lg text-xs font-medium w-fit
                            {{ $user->role == 'admin'       ? 'bg-blue-100 text-blue-600'   : '' }}
                            {{ $user->role == 'organizer'   ? 'bg-gray-200 text-gray-600'   : '' }}
                            {{ $user->role == 'participant' ? 'bg-green-100 text-green-600' : '' }}">
                            {{ ucfirst($user->role) }}
                        </span>

                        <div class="flex items-center gap-2">
                            <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                            <span class="text-sm text-gray-600">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                        </div>

                        <div class="flex gap-3 text-gray-500">
                            <x-icons :user="$user" />
                        </div>

                    </div>

                    {{-- MOBILE ROW --}}
                    <div class="md:hidden px-4 py-3 hover:bg-gray-50 transition">

                        <div class="flex justify-between items-start mb-2">
                            <div class="flex items-center gap-2">
                                <img src="{{ $user->image ? asset('storage/' . $user->image) : $user->image_url }}"
                                     class="w-9 h-9 rounded-full object-cover shrink-0">
                                <div>
                                    <p class="text-sm font-medium text-gray-800">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[180px]">{{ $user->email }}</p>
                                </div>
                            </div>

                            <span class="px-2.5 py-1 rounded-lg text-xs font-medium shrink-0
                                {{ $user->role == 'admin'       ? 'bg-blue-100 text-blue-600'   : '' }}
                                {{ $user->role == 'organizer'   ? 'bg-gray-200 text-gray-600'   : '' }}
                                {{ $user->role == 'participant' ? 'bg-green-100 text-green-600' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <div class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                <span class="text-xs text-gray-500">{{ $user->is_active ? 'Active' : 'Inactive' }}</span>
                            </div>
                            <div class="flex gap-3 text-gray-500">
                                <x-icons :user="$user" />
                            </div>
                        </div>

                    </div>

                @endforeach
            </div>

        </div>

        <!-- PAGINATION -->
        <div>
            {{ $users->links() }}
        </div>

    </div>

</x-layouts.app>
