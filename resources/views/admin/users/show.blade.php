<x-layouts.app>
    <x-header.page-title title="Admin Dashboard" />

    <!-- Only this content area scrolls; the app header remains fixed -->
    <div class="h-[calc(100vh-5rem)] overflow-y-auto">
        <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4">

            <x-success />
            <x-error />

            <!-- PAGE HEADER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex items-center gap-1 mb-1 text-sm font-medium text-blue-600
                              hover:gap-2 transition-all">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Back to User Directory
                    </a>

                    <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">
                        User Details
                    </h1>

                    <p class="mt-1 text-sm sm:text-base text-gray-400">
                        View account information and manage access.
                    </p>
                </div>

                @if(auth()->id() !== $user->id)
                    <form action="{{ route('admin.users.toggle-status', $user) }}"
                          method="POST"
                          class="w-fit">
                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                class="px-4 py-2.5 rounded-xl text-sm font-medium shadow-sm transition
                                       {{ $user->is_active
                                           ? 'bg-red-50 text-red-500 border border-red-200 hover:bg-red-100'
                                           : 'bg-green-50 text-green-600 border border-green-200 hover:bg-green-100' }}">
                            {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                        </button>
                    </form>
                @endif
            </div>

            <!-- USER CARD -->
            <div class="max-w-5xl mx-auto overflow-hidden bg-white border border-gray-200
                        rounded-3xl shadow-sm">

                <!-- HORIZONTAL PROFILE -->
                <div class="px-5 sm:px-8 py-6 sm:py-7 bg-gradient-to-r from-blue-50 to-indigo-50">
                    <div class="flex flex-col sm:flex-row sm:items-center gap-5 sm:gap-6">

                        <div class="relative shrink-0 self-center sm:self-auto">
                            <img src="{{ $user->image_url }}"
                                 alt="{{ $user->name }}"
                                 class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover
                                        border-4 border-white shadow-md">

                            <span class="absolute -bottom-1 -right-1 flex items-center justify-center
                                         w-5 h-5 rounded-full border-2 border-white
                                         {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}">
                                <span class="w-2 h-2 rounded-full bg-white
                                             {{ $user->is_active ? 'animate-pulse' : '' }}"></span>
                            </span>
                        </div>

                        <div class="min-w-0 flex-1 text-center sm:text-left">
                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">
                                <h2 class="text-xl sm:text-2xl font-bold text-gray-800 break-words">
                                    {{ $user->name }}
                                </h2>

                                <span class="self-center sm:self-auto inline-flex items-center
                                             px-3 py-1 rounded-full text-xs font-semibold
                                             {{ $user->role === 'admin'
                                                 ? 'bg-blue-100 text-blue-600'
                                                 : ($user->role === 'organizer'
                                                     ? 'bg-gray-200 text-gray-600'
                                                     : 'bg-green-100 text-green-600') }}">
                                    {{ strtoupper($user->role) }}
                                </span>
                            </div>

                            <div class="mt-3 flex flex-col lg:flex-row lg:items-center gap-2 lg:gap-5
                                        text-sm text-gray-500">
                                <span class="inline-flex items-center justify-center sm:justify-start gap-2 min-w-0">
                                    <i class="fa-solid fa-envelope text-gray-400 shrink-0"></i>
                                    <span class="break-all">{{ $user->email }}</span>
                                </span>

                                @if($user->email_verified_at)
                                    <span class="inline-flex items-center justify-center sm:justify-start
                                                 gap-1.5 text-xs font-medium text-blue-600">
                                        <i class="fa-solid fa-circle-check"></i>
                                        Verified
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center sm:justify-start
                                                 gap-1.5 text-xs font-medium text-red-400">
                                        <i class="fa-solid fa-circle-xmark"></i>
                                        Not Verified
                                    </span>
                                @endif

                                @if($user->provider)
                                    <span class="inline-flex items-center justify-center sm:justify-start
                                                 gap-1.5 text-xs text-gray-500">
                                        <i class="fa-brands fa-{{ $user->provider }}"></i>
                                        {{ ucfirst($user->provider) }} Account
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="shrink-0 self-center sm:self-auto">
                            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl
                                         text-sm font-semibold bg-white border border-gray-200 shadow-sm">
                                <span class="w-2.5 h-2.5 rounded-full
                                             {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                <span class="{{ $user->is_active ? 'text-green-600' : 'text-red-500' }}">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- ACCOUNT INFORMATION -->
                <div class="px-5 sm:px-8 py-6">
                    <h3 class="mb-3 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                        Account Information
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl">
                            <div class="flex items-center justify-center w-9 h-9 rounded-xl
                                        bg-blue-100 text-blue-600 shrink-0">
                                <i class="fa-solid fa-user text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Account ID</p>
                                <p class="mt-0.5 font-semibold text-gray-800">#{{ $user->id }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl">
                            <div class="flex items-center justify-center w-9 h-9 rounded-xl
                                        bg-blue-100 text-blue-600 shrink-0">
                                <i class="fa-solid fa-calendar-days text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Joined Date</p>
                                <p class="mt-0.5 font-semibold text-gray-800">
                                    {{ $user->created_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl">
                            <div class="flex items-center justify-center w-9 h-9 rounded-xl shrink-0
                                        {{ $user->is_active
                                            ? 'bg-green-100 text-green-600'
                                            : 'bg-red-100 text-red-500' }}">
                                <i class="fa-solid fa-circle text-[8px]"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Status</p>
                                <p class="mt-0.5 font-semibold text-gray-800">
                                    {{ $user->is_active ? 'Active' : 'Inactive' }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 bg-gray-50 border border-gray-100 rounded-2xl">
                            <div class="flex items-center justify-center w-9 h-9 rounded-xl
                                        bg-blue-100 text-blue-600 shrink-0">
                                <i class="fa-solid fa-rotate text-xs"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400">Last Updated</p>
                                <p class="mt-0.5 font-semibold text-gray-800">
                                    {{ $user->updated_at->format('M d, Y') }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- MEETING ACTIVITY -->
                @if(in_array($user->role, ['organizer', 'participant'], true))
                    <div class="px-5 sm:px-8 py-6 border-t border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4
                                    p-4 sm:p-5 bg-gradient-to-r from-blue-50 to-indigo-50
                                    border border-blue-100 rounded-2xl">
                            <div class="flex items-center gap-3">
                                <div class="flex items-center justify-center w-11 h-11 rounded-xl
                                            bg-white text-blue-600 shadow-sm shrink-0">
                                    <i class="fa-solid fa-video text-sm"></i>
                                </div>

                                <div>
                                    <p class="text-xs font-semibold text-blue-500 uppercase tracking-wider">
                                        Meeting Activity
                                    </p>
                                    <div class="flex items-baseline gap-2 mt-0.5">
                                        <span class="text-2xl font-bold text-gray-800">
                                            {{ $meetingCount }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ $user->role === 'organizer'
                                                ? 'meetings organized'
                                                : 'meetings attended' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <a href="{{ route('admin.users.meetings', $user) }}"
                               class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                                      rounded-xl bg-blue-600 text-white text-sm font-semibold
                                      shadow-sm hover:bg-blue-700 transition">
                                View Meeting History
                                <i class="fa-solid fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </div>
                @endif

                <!-- REMOVE USER -->
                <div class="px-5 sm:px-8 py-5 border-t border-gray-100 bg-gray-50/50">
                    <form action="{{ route('admin.users.destroy', $user) }}"
                          method="POST"
                          onsubmit="return confirm('Are you sure you want to permanently remove this user?')">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="inline-flex items-center justify-center gap-2 w-full sm:w-auto
                                       px-4 py-2.5 text-sm font-medium text-red-500
                                       border border-red-200 rounded-xl hover:bg-red-50 transition">
                            <i class="fa-solid fa-trash text-xs"></i>
                            Remove User
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
