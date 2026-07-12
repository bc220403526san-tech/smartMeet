<x-layouts.app>
    <x-header.page-title title="Admin Dashboard" />

    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">

        <x-success />
        <x-error />

        <!-- TITLE -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
            <div>
                <a href="{{ route('admin.users.index') }}"
                   class="text-blue-600 text-sm mb-1 inline-flex items-center gap-1 hover:gap-2 transition-all font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Back to User Directory
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">User Details</h1>
                <p class="text-gray-400 mt-1 text-sm sm:text-base">View account information and manage access.</p>
            </div>

            @if(auth()->id() !== $user->id)
                <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="w-fit">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm
                       {{ $user->is_active
                           ? 'bg-red-50 text-red-500 hover:bg-red-100 border border-red-200'
                           : 'bg-green-50 text-green-600 hover:bg-green-100 border border-green-200' }}">
                        {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                    </button>
                </form>
            @endif
        </div>

        <!-- CARD -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden max-w-3xl mx-auto">

            <!-- PROFILE HEADER -->
            <div class="relative bg-gradient-to-r from-blue-50 to-indigo-50 px-5 sm:px-8 pt-8 pb-6">
                <div class="flex flex-col items-center text-center">
                    <div class="relative">
                        <img id="preview-image"
                             src="{{ $user->image_url }}"
                             class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover shadow-md border-4 border-white">
                        <span class="absolute -bottom-1 -right-1 w-5 h-5 flex items-center justify-center
                                     {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}
                                     border-2 border-white rounded-full">
                            <span class="w-2 h-2 rounded-full bg-white {{ $user->is_active ? 'animate-pulse' : '' }}"></span>
                        </span>
                    </div>

                    <h3 class="mt-4 text-xl font-bold text-gray-800">{{ $user->name }}</h3>

                    <span class="mt-2 inline-flex items-center gap-1.5 text-xs font-semibold px-3 py-1 rounded-full
                        {{ $user->role == 'admin'       ? 'bg-blue-100 text-blue-600'   : '' }}
                        {{ $user->role == 'organizer'   ? 'bg-gray-200 text-gray-600'   : '' }}
                        {{ $user->role == 'participant' ? 'bg-green-100 text-green-600' : '' }}">
                        {{ strtoupper($user->role) }}
                    </span>

                    <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4 mt-3 text-sm text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <i class="fa fa-envelope text-gray-400"></i>
                            {{ $user->email }}
                        </span>
                        @if($user->email_verified_at)
                            <span class="flex items-center gap-1 text-blue-600 text-xs font-medium">
                                <i class="fa fa-check-circle"></i> Verified
                            </span>
                        @else
                            <span class="flex items-center gap-1 text-red-400 text-xs font-medium">
                                <i class="fa fa-times-circle"></i> Not Verified
                            </span>
                        @endif
                    </div>

                    @if($user->provider)
                        <span class="mt-3 inline-flex items-center gap-1.5 text-xs bg-white text-gray-500 border border-gray-200 px-3 py-1 rounded-full shadow-sm">
                            <i class="fa-brands fa-{{ $user->provider }} text-xs"></i>
                            {{ ucfirst($user->provider) }} Account
                        </span>
                    @endif
                </div>
            </div>

            <!-- DETAILS -->
            <div class="px-5 sm:px-8 py-6">
                <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-3">Account Information</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 p-4 rounded-2xl">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Account ID</p>
                            <p class="font-semibold text-gray-800 mt-0.5">#{{ $user->id }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 p-4 rounded-2xl">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Joined Date</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $user->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 p-4 rounded-2xl">
                        <div class="w-9 h-9 rounded-xl {{ $user->is_active ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-500' }} flex items-center justify-center shrink-0">
                            <span class="w-2.5 h-2.5 rounded-full {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Status</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $user->is_active ? 'Active' : 'Inactive' }}</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 bg-gray-50 border border-gray-100 p-4 rounded-2xl">
                        <div class="w-9 h-9 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400">Last Updated</p>
                            <p class="font-semibold text-gray-800 mt-0.5">{{ $user->updated_at->format('M d, Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- REMOVE USER -->
            <div class="px-5 sm:px-8 py-5 border-t border-gray-100 bg-gray-50/50">
                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Are you sure you want to permanently remove this user?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="w-full sm:w-auto px-4 py-2.5 text-sm font-medium text-red-500
                                   border border-red-200 rounded-xl hover:bg-red-50 transition inline-flex items-center justify-center gap-2">
                        <i class="fa-solid fa-trash text-xs"></i>
                        Remove User
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-layouts.app>
