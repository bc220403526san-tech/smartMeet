@props(['user'])

@php
    $isOwnAccount = auth()->check() && auth()->user()->is($user);
@endphp

<div class="flex gap-2 flex-wrap items-center">

    {{-- View User --}}
    <a href="{{ route('admin.users.show', $user) }}"
       class="group w-9 h-9 inline-flex items-center justify-center rounded-xl
              bg-blue-50 border border-blue-100 text-blue-600
              hover:bg-blue-600 hover:text-white hover:border-blue-600
              transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5"
       title="View User" aria-label="View User">
        <i class="fa-regular fa-eye text-xs transition-transform duration-200 group-hover:scale-110"></i>
    </a>

    {{-- Change Role --}}
    @if(!$isOwnAccount)
        <div class="dropdown-container relative">
            <button type="button"
                    onclick="toggleDropdown(this)"
                    class="group w-9 h-9 inline-flex items-center justify-center rounded-xl
                           bg-indigo-50 border border-indigo-100 text-indigo-600
                           hover:bg-indigo-600 hover:text-white hover:border-indigo-600
                           transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5"
                    title="Change Role" aria-label="Change Role">
                <i class="fa-solid fa-user-gear text-xs"></i>
            </button>

            <div class="dropdown-menu hidden fixed w-48 bg-white rounded-2xl
                        shadow-xl shadow-gray-200/60 border border-gray-200
                        py-1.5 z-[9999] overflow-hidden">

                <div class="px-3.5 py-2 border-b border-gray-100">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Change Role</p>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">{{ $user->name }}</p>
                </div>

                <form action="{{ route('admin.users.change-role', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit" name="role" value="admin"
                            class="w-full flex items-center gap-3 text-left px-3.5 py-2.5 text-sm
                                   text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition
                                   {{ $user->role === 'admin' ? 'bg-blue-50 text-blue-700' : '' }}">
                        <span class="w-7 h-7 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-shield-halved text-[10px]"></i>
                        </span>
                        <span class="flex-1">Admin</span>
                        @if($user->role === 'admin')
                            <i class="fa-solid fa-check text-xs text-blue-600"></i>
                        @endif
                    </button>

                    <button type="submit" name="role" value="organizer"
                            class="w-full flex items-center gap-3 text-left px-3.5 py-2.5 text-sm
                                   text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition
                                   {{ $user->role === 'organizer' ? 'bg-indigo-50 text-indigo-700' : '' }}">
                        <span class="w-7 h-7 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-user-tie text-[10px]"></i>
                        </span>
                        <span class="flex-1">Organizer</span>
                        @if($user->role === 'organizer')
                            <i class="fa-solid fa-check text-xs text-indigo-600"></i>
                        @endif
                    </button>

                    <button type="submit" name="role" value="participant"
                            class="w-full flex items-center gap-3 text-left px-3.5 py-2.5 text-sm
                                   text-gray-700 hover:bg-green-50 hover:text-green-700 transition
                                   {{ $user->role === 'participant' ? 'bg-green-50 text-green-700' : '' }}">
                        <span class="w-7 h-7 rounded-lg bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                            <i class="fa-solid fa-user text-[10px]"></i>
                        </span>
                        <span class="flex-1">Participant</span>
                        @if($user->role === 'participant')
                            <i class="fa-solid fa-check text-xs text-green-600"></i>
                        @endif
                    </button>
                </form>
            </div>
        </div>
    @else
        <span title="You cannot change your own role"
              class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100
                     inline-flex items-center justify-center opacity-40 cursor-not-allowed">
            <i class="fa-solid fa-user-gear text-gray-400 text-xs"></i>
        </span>
    @endif

    {{-- Account Status --}}
    @if(!$isOwnAccount)
        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit"
                    title="{{ $user->is_active ? 'Deactivate User' : 'Activate User' }}"
                    aria-label="{{ $user->is_active ? 'Deactivate User' : 'Activate User' }}"
                    class="w-9 h-9 inline-flex items-center justify-center rounded-xl
                           bg-amber-50 border border-amber-100 hover:bg-amber-100 hover:border-amber-200
                           transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                @if($user->is_active)
                    <span class="relative w-7 h-4 rounded-full bg-emerald-500 shadow-inner">
                        <span class="absolute top-0.5 right-0.5 w-3 h-3 rounded-full bg-white shadow-sm"></span>
                    </span>
                @else
                    <span class="relative w-7 h-4 rounded-full bg-gray-300 shadow-inner">
                        <span class="absolute top-0.5 left-0.5 w-3 h-3 rounded-full bg-white shadow-sm"></span>
                    </span>
                @endif
            </button>
        </form>
    @else
        <span title="You cannot deactivate your own account"
              class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100
                     inline-flex items-center justify-center opacity-40 cursor-not-allowed">
            <span class="relative w-7 h-4 rounded-full bg-gray-300">
                <span class="absolute top-0.5 right-0.5 w-3 h-3 rounded-full bg-white shadow-sm"></span>
            </span>
        </span>
    @endif

    {{-- Delete User --}}
    @if(!$isOwnAccount)
        <form action="{{ route('admin.users.destroy', $user) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to permanently remove this user?')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    title="Remove User" aria-label="Remove User"
                    class="group w-9 h-9 inline-flex items-center justify-center rounded-xl
                           bg-red-50 border border-red-100 text-red-500
                           hover:bg-red-600 hover:text-white hover:border-red-600
                           transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5">
                <i class="fa-regular fa-trash-can text-xs transition-transform duration-200 group-hover:scale-110"></i>
            </button>
        </form>
    @else
        <span title="You cannot remove your own account"
              class="w-9 h-9 rounded-xl bg-gray-50 border border-gray-100
                     inline-flex items-center justify-center opacity-35 cursor-not-allowed">
            <i class="fa-regular fa-trash-can text-gray-400 text-xs"></i>
        </span>
    @endif

</div>

<script>
    function toggleDropdown(button) {
        const dropdown = button.parentElement.querySelector('.dropdown-menu');

        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== dropdown) menu.classList.add('hidden');
        });

        if (!dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
            return;
        }

        const rect = button.getBoundingClientRect();
        dropdown.style.top = `${rect.bottom + 6}px`;
        dropdown.style.left = `${Math.max(8, rect.right - 192)}px`;
        dropdown.classList.remove('hidden');
    }

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.dropdown-container')) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
            });
        }
    });
</script>
