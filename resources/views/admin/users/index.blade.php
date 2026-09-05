<x-layouts.app>
    <x-slot name="header">
        <x-header.search-bar placeholder="Search for users, roles, or status..." />
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 min-h-full">
        <x-success />
        <x-error />

        <div>
            <h1 class="text-xl sm:text-2xl font-semibold">Manage Users</h1>
            <p class="text-sm text-gray-400 mt-1">View, manage, and control all registered users.</p>
        </div>

        <x-stats
            :totalUsers="$totalUsers"
            :activeUsers="$activeUsers"
            :inactiveUsers="$inactiveUsers"
        />

        <div id="pagetop" class="bg-white rounded-3xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-lg">User Directory</h2>
                        <p class="text-xs text-gray-400 mt-0.5">Manage and control all registered users.</p>
                    </div>

                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-xl px-3 py-1.5 shadow-sm">
                            <svg xmlns="http://www.w3.org/2000/svg"
                                 fill="none"
                                 viewBox="0 0 24 24"
                                 stroke-width="1.5"
                                 stroke="currentColor"
                                 class="w-4 h-4 text-gray-400 shrink-0">
                                <path stroke-linecap="round"
                                      stroke-linejoin="round"
                                      d="M10.5 6h9.75M10.5 6a1.5 1.5 0 1 1-3 0m3 0a1.5 1.5 0 1 0-3 0M3.75 6H7.5m3 12h9.75m-9.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-3.75 0H7.5m9-6h3.75m-3.75 0a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m-9.75 0h9.75"/>
                            </svg>

                            <form method="GET"
                                  action="{{ route('admin.users.index') }}"
                                  class="flex items-center gap-2">
                                @foreach(request()->except(['role', 'page']) as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach

                                <select name="role"
                                        onchange="this.form.submit()"
                                        class="text-sm text-gray-600 bg-transparent outline-none cursor-pointer">
                                    <option value="">All Roles</option>
                                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="organizer" {{ request('role') === 'organizer' ? 'selected' : '' }}>Organizer</option>
                                    <option value="participant" {{ request('role') === 'participant' ? 'selected' : '' }}>Participant</option>
                                </select>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden md:grid md:grid-cols-5 text-xs font-semibold text-gray-500 uppercase tracking-wider
                        bg-gray-50 border-b border-gray-100 px-5 py-3">
                <p>Name</p>
                <p>Email</p>
                <p>Role</p>
                <p>Status</p>
                <p>Actions</p>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($users as $user)
                    <div class="hidden md:grid md:grid-cols-5 items-center px-5 py-4 hover:bg-blue-50/30 transition duration-200">
                        <div class="flex items-center gap-3 min-w-0">
                            <x-user-avatar :user="$user" size="sm" />

                            <div class="min-w-0">
                                <span class="text-sm font-semibold text-gray-800 truncate block">{{ $user->name }}</span>
                                <p class="text-xs text-gray-400">ID: #U-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</p>
                            </div>
                        </div>

                        <p class="text-sm text-gray-600 truncate pr-4">{{ $user->email }}</p>

                        <span class="px-3 py-1.5 rounded-full text-xs font-semibold w-fit
                            {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700 border border-blue-200' : '' }}
                            {{ $user->role === 'organizer' ? 'bg-gray-100 text-gray-700 border border-gray-200' : '' }}
                            {{ $user->role === 'participant' ? 'bg-green-100 text-green-700 border border-green-200' : '' }}">
                            {{ ucfirst($user->role) }}
                        </span>

                        <div class="flex items-center gap-2">
                            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium border
                                {{ $user->is_active
                                    ? 'bg-green-50 text-green-700 border-green-200'
                                    : 'bg-red-50 text-red-600 border-red-200' }}">
                                <span class="w-2 h-2 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </div>

                        <div class="flex gap-2 text-gray-500">
                            <x-icons :user="$user" />
                        </div>
                    </div>

                    <div class="md:hidden px-4 py-3 hover:bg-blue-50/30 transition duration-200">
                        <div class="flex justify-between items-start mb-2 gap-3">
                            <div class="flex items-center gap-2 min-w-0">
                                <x-user-avatar :user="$user" size="sm" />

                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400 truncate max-w-[180px]">{{ $user->email }}</p>
                                </div>
                            </div>

                            <span class="px-2.5 py-1 rounded-full text-xs font-medium shrink-0
                                {{ $user->role === 'admin' ? 'bg-blue-100 text-blue-700 border border-blue-200' : '' }}
                                {{ $user->role === 'organizer' ? 'bg-gray-100 text-gray-700 border border-gray-200' : '' }}
                                {{ $user->role === 'participant' ? 'bg-green-100 text-green-700 border border-green-200' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium border
                                {{ $user->is_active
                                    ? 'bg-green-50 text-green-700 border-green-200'
                                    : 'bg-red-50 text-red-600 border-red-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-400' }}"></span>
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </div>

                            <div class="flex gap-2 text-gray-500">
                                <x-icons :user="$user" />
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-16 text-gray-400 text-sm">
                        <i class="fa-solid fa-user-slash text-4xl mb-3 block text-gray-300"></i>
                        <p>No users found.</p>
                    </div>
                @endforelse
            </div>

            <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/50">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const params = new URLSearchParams(window.location.search);
            const target = document.getElementById('pagetop');
            const isFilteredNav = ['page', 'role', 'search'].some(key => params.has(key));

            if (target && isFilteredNav) {
                target.scrollIntoView({ behavior: 'instant', block: 'start' });
            }
        });
    </script>
</x-layouts.app>
