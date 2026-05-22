@props(['user'])
<div class="flex gap-2">

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
            <span  title="This is your account"
                  class="p-1.5 w-12 rounded-lg bg-blue-50 inline-flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
             stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 text-blue-400">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/>
        </svg>
    </span>
    @endif

    <!-- REMOVE (fake/spam user delete) -->
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
