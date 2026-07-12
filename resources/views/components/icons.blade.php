@props(['user'])

<div class="flex gap-2 flex-wrap items-center">

    <!-- VIEW -->
    <a href="{{ route('admin.users.show', $user) }}"
       class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow"
       title="View User">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor"
             class="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
        </svg>
    </a>

    <!-- ====== CHANGE ROLE DROPDOWN — admin apna khud ka role change nahi kar sakta ====== -->
    @if(auth()->id() !== $user->id)
        <div class="dropdown-container">
            <button onclick="toggleDropdown(this)"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-indigo-100 transition group shadow-sm hover:shadow"
                    title="Change Role">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor"
                     class="w-4 h-4 text-gray-600 group-hover:text-indigo-600 transition">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/>
                </svg>
            </button>

            <!-- Dropdown Menu (position: fixed via JS — parent overflow-hidden isko clip nahi karega) -->
            <div class="dropdown-menu hidden fixed w-44 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-[9999]">
                <form action="{{ route('admin.users.change-role', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" name="role" value="admin"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition flex items-center gap-2 {{ $user->role == 'admin' ? 'bg-blue-50 text-blue-700' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                        </svg>
                        Admin
                        @if($user->role == 'admin')
                            <span class="ml-auto text-blue-500">✓</span>
                        @endif
                    </button>
                    <button type="submit" name="role" value="organizer"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-700 transition flex items-center gap-2 {{ $user->role == 'organizer' ? 'bg-gray-100' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z"/>
                        </svg>
                        Organizer
                        @if($user->role == 'organizer')
                            <span class="ml-auto text-blue-500">✓</span>
                        @endif
                    </button>
                    <button type="submit" name="role" value="participant"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition flex items-center gap-2 {{ $user->role == 'participant' ? 'bg-green-50 text-green-700' : '' }}">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                        </svg>
                        Participant
                        @if($user->role == 'participant')
                            <span class="ml-auto text-blue-500">✓</span>
                        @endif
                    </button>
                </form>
            </div>
        </div>
    @else
        <span title="You cannot change your own role"
              class="p-2 rounded-lg bg-gray-50 inline-flex items-center justify-center opacity-40 cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-4 h-4 text-gray-400">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/>
            </svg>
        </span>
    @endif

    {{-- ACTIVATE / DEACTIVATE TOGGLE --}}
    @if(auth()->id() !== $user->id)
        <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
            @csrf
            @method('PATCH')
            <button type="submit"
                    title="{{ $user->is_active ? 'Deactivate User' : 'Activate User' }}"
                    class="p-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 transition shadow-sm hover:shadow">
                @if($user->is_active)
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 20" class="w-9 h-5">
                        <rect x="0" y="0" width="36" height="20" rx="10" fill="#3b82f6"/>
                        <circle cx="26" cy="10" r="7" fill="white"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 20" class="w-9 h-5">
                        <rect x="0" y="0" width="36" height="20" rx="10" fill="#ef4444"/>
                        <circle cx="10" cy="10" r="7" fill="white"/>
                    </svg>
                @endif
            </button>
        </form>
    @else
        <span title="This is your account"
              class="p-1.5 w-12 rounded-lg bg-blue-50 inline-flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-5 h-5 text-blue-400">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
            </svg>
        </span>
    @endif

    <!-- REMOVE -->
    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
          onsubmit="return confirm('Are you sure you want to permanently remove this user?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                title="Remove User"
                class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-4 h-4 text-red-500 group-hover:text-red-700 transition">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
            </svg>
        </button>
    </form>
</div>

<!-- ====== JAVASCRIPT FOR DROPDOWN (fixed positioning, escapes overflow-hidden) ====== -->
<script>
    function toggleDropdown(button) {
        var dropdown = button.parentElement.querySelector('.dropdown-menu');
        var allDropdowns = document.querySelectorAll('.dropdown-menu');

        // Close all other dropdowns
        allDropdowns.forEach(function (menu) {
            if (menu !== dropdown) {
                menu.classList.add('hidden');
            }
        });

        var willOpen = dropdown.classList.contains('hidden');
        dropdown.classList.toggle('hidden');

        if (willOpen) {
            positionDropdown(button, dropdown);
        }
    }

    function positionDropdown(button, dropdown) {
        var rect = button.getBoundingClientRect();
        var menuWidth = 176; // w-44 = 11rem = 176px
        var menuHeight = dropdown.offsetHeight || 150;

        var top = rect.bottom + 6;
        var left = rect.right - menuWidth;

        // Agar neeche jagah kam ho, to upar khol do
        if (top + menuHeight > window.innerHeight - 10) {
            top = rect.top - menuHeight - 6;
        }

        // Agar left screen se bahar jaye to adjust karo
        if (left < 8) left = 8;
        if (left + menuWidth > window.innerWidth - 8) {
            left = window.innerWidth - menuWidth - 8;
        }

        dropdown.style.top = top + 'px';
        dropdown.style.left = left + 'px';
    }

    // Close dropdown when clicking outside
    document.addEventListener('click', function (event) {
        var isDropdownButton = event.target.closest('[onclick="toggleDropdown(this)"]');
        if (!isDropdownButton) {
            document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
                menu.classList.add('hidden');
            });
        }
    });

    // Scroll ya resize ho to dropdown band kar do (position stale na ho)
    window.addEventListener('scroll', function () {
        document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
            menu.classList.add('hidden');
        });
    }, true);

    window.addEventListener('resize', function () {
        document.querySelectorAll('.dropdown-menu').forEach(function (menu) {
            menu.classList.add('hidden');
        });
    });
</script>
