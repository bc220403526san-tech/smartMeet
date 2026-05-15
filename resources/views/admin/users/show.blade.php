<x-layouts.app>
    <x-header.page-title title="Admin Dashboard" />

    <div class="min-h-screen flex flex-col bg-gray-50 mt-3">
        <div class="flex-1 p-3 sm:p-6 m-1 sm:m-2 overflow-y-auto">

            <x-success />
            <x-error />
            <!-- TITLE -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 mb-6">
                <div>
                    <a href="{{ route('admin.users.index') }}"
                       class="text-blue-600 text-sm mb-1 inline-block hover:underline">
                        ← Back to User Directory
                    </a>
                    <h2 class="text-2xl font-semibold">User Details</h2>
                </div>

                <div class="flex gap-3">
                    {{-- TOGGLE STATUS --}}
                    @if(auth()->id() !== $user->id)
                        <form action="{{ route('admin.users.toggleStatus', $user) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="px-4 py-2 rounded-lg text-sm font-medium transition
                       {{ $user->is_active
                           ? 'bg-red-50 text-red-500 hover:bg-red-100 border border-red-200'
                           : 'bg-green-50 text-green-600 hover:bg-green-100 border border-green-200' }}">
                                {{ $user->is_active ? 'Deactivate User' : 'Activate User' }}
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- CARD -->
            <div class="bg-white p-4 sm:p-8 rounded-2xl shadow-lg w-full max-w-3xl mx-auto">

                <!-- IMAGE SECTION -->
                <div class="flex flex-col items-center text-center">

                    <div class="relative">
                        <img id="preview-image"
                             src="{{ $user->image_url }}"
                             class="w-24 h-24 sm:w-28 sm:h-28 rounded-2xl object-cover shadow-md border-4 border-white">
                        <span class="absolute bottom-2 right-2 w-4 h-4
                                     {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }}
                                     border-2 border-white rounded-full"></span>
                    </div>

                    <h3 class="mt-4 text-xl font-semibold text-gray-800">{{ $user->name }}</h3>

                    <span class="mt-2 text-xs px-3 py-1 rounded-full
                        {{ $user->role == 'admin'       ? 'bg-blue-100 text-blue-600'   : '' }}
                        {{ $user->role == 'organizer'   ? 'bg-gray-200 text-gray-600'   : '' }}
                        {{ $user->role == 'participant' ? 'bg-green-100 text-green-600' : '' }}">
                        {{ strtoupper($user->role) }}
                    </span>

                    <div class="flex flex-col sm:flex-row items-center gap-2 sm:gap-4 mt-3 text-sm text-gray-500">
                        <span class="flex items-center gap-1">
                            <i class="fa fa-envelope text-gray-400"></i>
                            {{ $user->email }}
                        </span>
                        @if($user->email_verified_at)
                            <span class="flex items-center gap-1 text-blue-600 text-xs">
                                <i class="fa fa-check-circle"></i> Verified
                            </span>
                        @else
                            <span class="flex items-center gap-1 text-red-400 text-xs">
                                <i class="fa fa-times-circle"></i> Not Verified
                            </span>
                        @endif
                    </div>

                    @if($user->provider)
                        <span class="mt-2 flex items-center gap-1 text-xs bg-gray-100 text-gray-500 px-3 py-1 rounded-full">
                            <i class="fa-brands fa-{{ $user->provider }} text-xs"></i>
                            {{ ucfirst($user->provider) }} Account
                        </span>
                    @endif

                </div>

                <div class="my-6 border-t"></div>

                <!-- DETAILS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="text-xs text-gray-400">ACCOUNT ID</p>
                        <p class="font-semibold mt-1">#{{ $user->id }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="text-xs text-gray-400">JOINED DATE</p>
                        <p class="font-semibold mt-1">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="text-xs text-gray-400">STATUS</p>
                        <p class="flex items-center gap-2 font-semibold mt-1">
                            <span class="w-2 h-2 {{ $user->is_active ? 'bg-green-500' : 'bg-red-500' }} rounded-full"></span>
                            {{ $user->is_active ? 'Active' : 'Inactive' }}
                        </p>
                    </div>
                    <div class="bg-gray-50 p-4 rounded-xl">
                        <p class="text-xs text-gray-400">LAST UPDATED</p>
                        <p class="font-semibold mt-1">{{ $user->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>

                <!-- REMOVE USER -->
                <div class="mt-6 border-t pt-6">
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to permanently remove this user?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full sm:w-auto px-4 py-2 text-sm text-red-500
                                       border border-red-200 rounded-lg hover:bg-red-50 transition">
                            <i class="fa-solid fa-trash text-xs mr-1"></i>
                            Remove User
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

</x-layouts.app>
