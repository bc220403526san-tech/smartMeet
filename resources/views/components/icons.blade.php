@props(['user'])

@php
    $isOwnAccount = auth()->check() && auth()->user()->is($user);
@endphp

<div class="flex gap-2 flex-wrap items-center">
    <a href="{{ route('admin.users.show', $user) }}"
       class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow"
       title="View User">
        <i class="fa-regular fa-eye text-gray-600 group-hover:text-blue-600"></i>
    </a>

    @if(!$isOwnAccount)
        <div class="dropdown-container">
            <button onclick="toggleDropdown(this)"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-indigo-100 transition group shadow-sm hover:shadow"
                    title="Change Role">
                <i class="fa-regular fa-id-card text-gray-600 group-hover:text-indigo-600"></i>
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
              class="p-2 rounded-lg bg-gray-50 inline-flex items-center justify-center opacity-35 cursor-not-allowed">
            <i class="fa-regular fa-id-card text-gray-400"></i>
        </span>
    @endif

    @if(!$isOwnAccount)
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
        <span title="You cannot deactivate your own account"
              class="p-1.5 w-12 rounded-lg bg-gray-50 inline-flex items-center justify-center opacity-35 cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 36 20" class="w-9 h-5">
                <rect x="0" y="0" width="36" height="20" rx="10" fill="#9ca3af"/>
                <circle cx="26" cy="10" r="7" fill="white"/>
            </svg>
        </span>
    @endif

    @if(!$isOwnAccount)
        <form action="{{ route('admin.users.destroy', $user) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to permanently remove this user?')">
            @csrf
            @method('DELETE')

            <button type="submit"
                    title="Remove User"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow">
                <i class="fa-regular fa-trash-can text-red-500 group-hover:text-red-700"></i>
            </button>
        </form>
    @else
        <span title="You cannot remove your own account"
              class="p-2 rounded-lg bg-gray-50 inline-flex items-center justify-center opacity-25 cursor-not-allowed">
            <i class="fa-regular fa-trash-can text-gray-400"></i>
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
