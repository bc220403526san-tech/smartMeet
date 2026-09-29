@props(['user'])

@php
    $isOwnAccount = auth()->check() && auth()->user()->is($user);
@endphp

<div class="flex items-center gap-1.5">

    {{-- View User --}}
    <a href="{{ route('admin.users.show', $user) }}"
       class="group inline-flex items-center justify-center w-7 h-7
              text-blue-500 hover:text-blue-700 hover:bg-blue-50 rounded-md
              transition-colors duration-150"
       title="View User"
       aria-label="View User">
        <i class="fa-regular fa-eye text-sm group-hover:scale-110 transition-transform"></i>
    </a>

    {{-- Change Role --}}
    @if(!$isOwnAccount)
        <div class="dropdown-container relative">
            <button type="button"
                    onclick="toggleDropdown(this)"
                    class="group inline-flex items-center justify-center w-7 h-7
                           text-indigo-500 hover:text-indigo-700 hover:bg-indigo-50 rounded-md
                           transition-colors duration-150"
                    title="Change Role"
                    aria-label="Change Role">
                <i class="fa-solid fa-user-gear text-sm group-hover:scale-110 transition-transform"></i>
            </button>

            <div class="dropdown-menu hidden fixed w-52 bg-white rounded-xl
                        shadow-xl border border-gray-200 py-1 z-[9999] overflow-hidden">

                <div class="px-3 py-2 border-b border-gray-100">
                    <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">
                        Change Role
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5 truncate">
                        {{ $user->name }}
                    </p>
                </div>

                <form action="{{ route('admin.users.change-role', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            name="role"
                            value="admin"
                            class="w-full flex items-center gap-2.5 text-left px-3 py-2.5 text-sm
                                   text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition
                                   {{ $user->role === 'admin' ? 'bg-blue-50 text-blue-700' : '' }}">
                        <i class="fa-solid fa-shield-halved w-4 text-blue-500"></i>
                        <span class="flex-1">Admin</span>
                        @if($user->role === 'admin')
                            <i class="fa-solid fa-check text-xs text-blue-600"></i>
                        @endif
                    </button>

                    <button type="submit"
                            name="role"
                            value="organizer"
                            class="w-full flex items-center gap-2.5 text-left px-3 py-2.5 text-sm
                                   text-gray-700 hover:bg-indigo-50 hover:text-indigo-700 transition
                                   {{ $user->role === 'organizer' ? 'bg-indigo-50 text-indigo-700' : '' }}">
                        <i class="fa-solid fa-user-tie w-4 text-indigo-500"></i>
                        <span class="flex-1">Organizer</span>
                        @if($user->role === 'organizer')
                            <i class="fa-solid fa-check text-xs text-indigo-600"></i>
                        @endif
                    </button>

                    <button type="submit"
                            name="role"
                            value="participant"
                            class="w-full flex items-center gap-2.5 text-left px-3 py-2.5 text-sm
                                   text-gray-700 hover:bg-green-50 hover:text-green-700 transition
                                   {{ $user->role === 'participant' ? 'bg-green-50 text-green-700' : '' }}">
                        <i class="fa-solid fa-user w-4 text-green-500"></i>
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
              class="inline-flex items-center justify-center w-7 h-7
                     text-gray-300 cursor-not-allowed">
            <i class="fa-solid fa-user-gear text-sm"></i>
        </span>
    @endif

    {{-- Account Status --}}
    @if(!$isOwnAccount)
        <form action="{{ route('admin.users.toggle-status', $user) }}"
              method="POST"
              class="inline-flex">
            @csrf
            @method('PATCH')

            <button type="submit"
                    title="{{ $user->is_active ? 'Deactivate User' : 'Activate User' }}"
                    aria-label="{{ $user->is_active ? 'Deactivate User' : 'Activate User' }}"
                    class="inline-flex items-center justify-center w-7 h-7
                           hover:bg-gray-50 rounded-md transition-colors duration-150">

                @if($user->is_active)
                    <span class="relative block w-7 h-4 rounded-full bg-emerald-500">
                        <span class="absolute top-0.5 right-0.5 w-3 h-3 rounded-full
                                     bg-white shadow-sm"></span>
                    </span>
                @else
                    <span class="relative block w-7 h-4 rounded-full bg-gray-300">
                        <span class="absolute top-0.5 left-0.5 w-3 h-3 rounded-full
                                     bg-white shadow-sm"></span>
                    </span>
                @endif
            </button>
        </form>
    @else
        <span title="You cannot deactivate your own account"
              class="inline-flex items-center justify-center w-7 h-7
                     text-gray-300 cursor-not-allowed">
            <span class="relative block w-7 h-4 rounded-full bg-gray-200">
                <span class="absolute top-0.5 right-0.5 w-3 h-3 rounded-full bg-white shadow-sm"></span>
            </span>
        </span>
    @endif

    {{-- Delete User --}}
    @if(!$isOwnAccount)
        <form action="{{ route('admin.users.destroy', $user) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to permanently remove this user?')"
              class="inline-flex">
            @csrf
            @method('DELETE')

            <button type="submit"
                    title="Remove User"
                    aria-label="Remove User"
                    class="group inline-flex items-center justify-center w-7 h-7
                           text-red-400 hover:text-red-600 hover:bg-red-50 rounded-md
                           transition-colors duration-150">
                <i class="fa-regular fa-trash-can text-sm group-hover:scale-110 transition-transform"></i>
            </button>
        </form>
    @else
        <span title="You cannot remove your own account"
              class="inline-flex items-center justify-center w-7 h-7 text-gray-300 cursor-not-allowed">
            <i class="fa-regular fa-trash-can text-sm"></i>
        </span>
    @endif

</div>

<script>
    if (!window.__smartMeetAdminUserDropdownReady) {
        window.__smartMeetAdminUserDropdownReady = true;

        window.toggleDropdown = function (button) {
            const dropdown = button.parentElement.querySelector('.dropdown-menu');

            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu !== dropdown) {
                    menu.classList.add('hidden');
                    menu.style.visibility = '';
                }
            });

            if (!dropdown.classList.contains('hidden')) {
                dropdown.classList.add('hidden');
                dropdown.style.visibility = '';
                return;
            }

            const rect = button.getBoundingClientRect();
            const gap = 6;
            const screenPadding = 8;

            // Show temporarily so we can measure the REAL menu height.
            dropdown.style.visibility = 'hidden';
            dropdown.classList.remove('hidden');

            const menuRect = dropdown.getBoundingClientRect();
            const menuWidth = menuRect.width;
            const menuHeight = menuRect.height;

            let left = rect.right - menuWidth;
            let top = rect.bottom + gap;

            if (left < screenPadding) {
                left = screenPadding;
            }

            if (left + menuWidth > window.innerWidth - screenPadding) {
                left = window.innerWidth - menuWidth - screenPadding;
            }

            // If there isn't enough room below, open above.
            if (top + menuHeight > window.innerHeight - screenPadding) {
                top = rect.top - menuHeight - gap;
            }

            // Final safety clamp.
            if (top < screenPadding) {
                top = screenPadding;
            }

            dropdown.style.left = `${left}px`;
            dropdown.style.top = `${top}px`;
            dropdown.style.visibility = 'visible';
        };

        document.addEventListener('click', function (event) {
            if (!event.target.closest('.dropdown-container')) {
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    menu.classList.add('hidden');
                    menu.style.visibility = '';
                });
            }
        });

        window.addEventListener('scroll', function () {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (!menu.classList.contains('hidden')) {
                    menu.classList.add('hidden');
                    menu.style.visibility = '';
                }
            });
        }, true);

        window.addEventListener('resize', function () {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.add('hidden');
                menu.style.visibility = '';
            });
        });
    }
</script>
