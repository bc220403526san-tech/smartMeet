@props(['user'])

@php
    $isProtectedAdmin = $user->role === 'admin';
    $isOwnAccount = auth()->check() && auth()->user()->is($user);
@endphp

<div class="flex gap-2 flex-wrap items-center">

    <!-- VIEW -->
    <a href="{{ route('admin.users.show', $user) }}"
       class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow"
       title="View User">
        <svg xmlns="http://www.w3.org/2000/svg"
             fill="none"
             viewBox="0 0 24 24"
             stroke-width="1.5"
             stroke="currentColor"
             class="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition">
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
            <path stroke-linecap="round"
                  stroke-linejoin="round"
                  d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
        </svg>
    </a>

    <!-- CHANGE ROLE -->
    @if(!$isOwnAccount)
        <div class="dropdown-container">
            <button onclick="toggleDropdown(this)"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-indigo-100 transition group shadow-sm hover:shadow"
                    title="Change Role">
                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor"
                     class="w-4 h-4 text-gray-600 group-hover:text-indigo-600 transition">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="M15 9h3.75M15 12h3.75M15 15h3.75M4.5 19.5h15a2.25 2.25 0 0 0 2.25-2.25V6.75A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25v10.5A2.25 2.25 0 0 0 4.5 19.5Zm6-10.125a1.875 1.875 0 1 1-3.75 0 1.875 1.875 0 0 1 3.75 0Zm1.294 6.336a6.721 6.721 0 0 1-3.17.789 6.721 6.721 0 0 1-3.168-.789 3.376 3.376 0 0 1 6.338 0Z"/>
                </svg>
            </button>

            <div class="dropdown-menu hidden fixed w-44 bg-white rounded-xl shadow-lg border border-gray-200 py-1 z-[9999]">
                <form action="{{ route('admin.users.change-role', $user) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <button type="submit"
                            name="role"
                            value="admin"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition
                                   {{ $user->role === 'admin' ? 'bg-blue-50 text-blue-700' : '' }}">
                        Admin
                    </button>

                    <button type="submit"
                            name="role"
                            value="organizer"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition
                                   {{ $user->role === 'organizer' ? 'bg-gray-100' : '' }}">
                        Organizer
                    </button>

                    <button type="submit"
                            name="role"
                            value="participant"
                            class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-green-50 hover:text-green-700 transition
                                   {{ $user->role === 'participant' ? 'bg-green-50 text-green-700' : '' }}">
                        Participant
                    </button>
                </form>
            </div>
        </div>
    @else
        <span title="You cannot change your own role"
              class="p-2 rounded-lg bg-gray-50 inline-flex items-center justify-center opacity-40 cursor-not-allowed">
            <i class="fa-solid fa-id-card text-gray-400 text-sm"></i>
        </span>
    @endif

    <!-- ACTIVATE / DEACTIVATE -->
    @if(!$isProtectedAdmin && !$isOwnAccount)
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
        <span title="Admin account cannot be deactivated"
              class="p-1.5 w-12 rounded-lg bg-gray-50 inline-flex items-center justify-center opacity-35 cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 20" class="w-9 h-5">
                <rect x="0" y="0" width="36" height="20" rx="10" fill="#9ca3af"/>
                <circle cx="26" cy="10" r="7" fill="white"/>
            </svg>
        </span>
    @endif

    <!-- REMOVE -->
    @if(!$isProtectedAdmin && !$isOwnAccount)
        <form action="{{ route('admin.users.destroy', $user) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to permanently remove this user?')">
            @csrf
            @method('DELETE')

            <button type="submit"
                    title="Remove User"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow">
                <svg xmlns="http://www.w3.org/2000/svg"
                     fill="none"
                     viewBox="0 0 24 24"
                     stroke-width="1.5"
                     stroke="currentColor"
                     class="w-4 h-4 text-red-500 group-hover:text-red-700 transition">
                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                </svg>
            </button>
        </form>
    @else
        <span title="Admin account cannot be removed"
              class="p-2 rounded-lg bg-gray-50 inline-flex items-center justify-center opacity-30 cursor-not-allowed">
            <i class="fa-solid fa-trash text-gray-400 text-sm"></i>
        </span>
    @endif
</div>

<script>
    function toggleDropdown(button) {
        const dropdown = button.parentElement.querySelector('.dropdown-menu');

        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            if (menu !== dropdown) {
                menu.classList.add('hidden');
            }
        });

        if (!dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
            return;
        }

        const rect = button.getBoundingClientRect();

        dropdown.style.top = `${rect.bottom + 6}px`;
        dropdown.style.left = `${Math.max(8, rect.right - 176)}px`;

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
