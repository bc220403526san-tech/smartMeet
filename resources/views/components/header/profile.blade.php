@props(['showName' => true])
@php
    $u = Auth::user();
    $initials = '';
    if ($u) {
        $initials = collect(explode(' ', trim($u->name)))
            ->map(fn($part) => mb_substr($part, 0, 1))
            ->take(2)
            ->implode('');
        $initials = mb_strtoupper($initials ?: 'U');
    }

    // Role ke hisaab se dynamic settings route
    $settingsRoute = match($u?->role) {
        'admin'       => 'admin.settings.index',
        'organizer'   => 'organizer.settings.index',
        'participant' => 'participant.settings.index',
        default       => 'settings',
    };

    // Unique id taake ek page pe multiple instance ho to bhi conflict na ho
    $uid = 'profile-dropdown-' . uniqid();
@endphp

<div class="relative" id="{{ $uid }}">
    {{-- TRIGGER --}}
    <div class="profile-trigger flex items-center gap-2 cursor-pointer select-none">
        <div class="w-8 h-8 rounded-full overflow-hidden shrink-0 flex items-center justify-center text-white text-xs font-bold bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
            @auth
                <img src="{{ $u->avatar ? Storage::url($u->avatar) : '' }}"
                     class="app-avatar-img w-full h-full object-cover"
                     style="{{ $u->avatar ? '' : 'display:none' }}"
                     alt="{{ $u->name }}">
                <span class="app-avatar-initials" style="{{ $u->avatar ? 'display:none' : '' }}">{{ $initials }}</span>
            @endauth
        </div>
        @if($showName)
            <div class="hidden sm:block">
                <p class="text-sm font-semibold">{{ $u?->name }}</p>
                <p class="text-xs text-gray-500 capitalize">{{ $u?->role }}</p>
            </div>
        @endif
        <svg xmlns="http://www.w3.org/2000/svg" class="profile-chevron hidden sm:block w-4 h-4 text-gray-400 transition-transform"
             fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5"/>
        </svg>
    </div>

    {{-- DROPDOWN --}}
    <div class="profile-menu hidden absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-lg border border-gray-100 z-50 overflow-hidden">

        {{-- Profile Info --}}
        <div class="flex items-center gap-3 px-4 py-4 border-b border-gray-100">
            <div class="w-12 h-12 rounded-full overflow-hidden shrink-0 flex items-center justify-center text-white text-sm font-bold bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500">
                @auth
                    <img src="{{ $u->avatar ? Storage::url($u->avatar) : '' }}"
                         class="app-avatar-img w-full h-full object-cover"
                         style="{{ $u->avatar ? '' : 'display:none' }}"
                         alt="{{ $u->name }}">
                    <span class="app-avatar-initials" style="{{ $u->avatar ? 'display:none' : '' }}">{{ $initials }}</span>
                @endauth
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-gray-800 truncate">{{ $u?->name }}</p>
                <p class="text-xs text-gray-500 capitalize">{{ $u?->role }}</p>
                <p class="text-xs text-gray-400 truncate">{{ $u?->email }}</p>
            </div>
        </div>

        {{-- Options --}}
        <div class="py-2">
            @if(Route::has($settingsRoute))
                <a href="{{ route($settingsRoute) }}"
                   class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-500" fill="none"
                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M16.862 4.487 18.549 2.8a2.121 2.121 0 0 1 3 3l-1.687 1.687M16.862 4.487 5.408 15.94a4 4 0 0 0-1.052 1.848l-.892 3.293 3.293-.892a4 4 0 0 0 1.848-1.052L19.95 7.65a2.121 2.121 0 0 0 0-3l-.001-.001a2.121 2.121 0 0 0-3-.001Z"/>
                    </svg>
                    Edit Profile
                </a>
            @endif

            <div class="border-t border-gray-100 my-1"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none"
                         viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6a2.25 2.25 0 0 0-2.25 2.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12.75"/>
                    </svg>
                    Logout
                </button>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('click', function (e) {
            // Trigger click -> uske parent dropdown wrapper ka menu toggle karo
            const trigger = e.target.closest('.profile-trigger');

            if (trigger) {
                const wrapper = trigger.closest('[id^="profile-dropdown-"]');
                const menu = wrapper.querySelector('.profile-menu');
                const chevron = wrapper.querySelector('.profile-chevron');

                // Baaki sab open dropdowns band kar do
                document.querySelectorAll('.profile-menu').forEach(m => {
                    if (m !== menu) m.classList.add('hidden');
                });
                document.querySelectorAll('.profile-chevron').forEach(c => {
                    if (c !== chevron) c.classList.remove('rotate-180');
                });

                // Current dropdown toggle karo
                menu.classList.toggle('hidden');
                if (chevron) chevron.classList.toggle('rotate-180');

                return; // outside click logic ko trigger na hone do
            }

            // Bahar click -> sab dropdown band kar do
            document.querySelectorAll('.profile-menu').forEach(m => m.classList.add('hidden'));
            document.querySelectorAll('.profile-chevron').forEach(c => c.classList.remove('rotate-180'));
        });
    </script>
</div>
