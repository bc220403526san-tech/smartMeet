<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Organizer Dashboard" />
    </x-slot>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
    </style>
    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">
        {{-- Global success / error banner --}}
        @if (session('success'))
            <div id="flash-success" class="rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm px-4 py-3 transition-all duration-500">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div id="flash-error" class="rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3 transition-all duration-500">
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="mb-5 sm:mb-6">
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Settings</h1>
            <p class="text-gray-400 text-xs sm:text-sm mt-0.5">
                Manage your profile, security, and account preferences.
            </p>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">
            {{-- ========== LEFT COLUMN: PROFILE CARD ========== --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden sticky top-4">
                    <div class="bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400 h-16 sm:h-20 relative overflow-hidden">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-white/10 rounded-full"></div>
                        <div class="absolute -bottom-6 -left-6 w-16 h-16 bg-white/10 rounded-full"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-white/5 rounded-full"></div>
                    </div>
                    <div class="flex flex-col items-center -mt-10 sm:-mt-12 px-4 pb-4">
                        @php
                            $initials = collect(explode(' ', trim($user->name)))
                                ->map(fn($part) => mb_substr($part, 0, 1))
                                ->take(2)
                                ->implode('');
                            $initials = mb_strtoupper($initials ?: 'U');
                        @endphp
                        <div class="relative group mb-2 w-16 h-16 sm:w-20 sm:h-20">
                            <div id="avatar-initials"
                                 class="app-avatar-initials w-16 h-16 sm:w-20 sm:h-20 rounded-2xl border-4 border-white shadow-lg bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 flex items-center justify-center text-white font-bold text-lg sm:text-xl select-none"
                                 style="{{ $user->avatar ? 'display:none' : '' }}">
                                {{ $initials }}
                            </div>
                            <img id="avatar-preview"
                                 src="{{ $user->avatar ? Storage::url($user->avatar) : '' }}"
                                 class="app-avatar-img w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-4 border-white shadow-lg"
                                 style="{{ $user->avatar ? '' : 'display:none' }}">
                            <button type="button" id="avatar-trigger"
                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center rounded-2xl cursor-pointer transition-opacity duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                </svg>
                            </button>
                            <input type="file" id="avatar-input" name="avatar" accept="image/*" class="hidden">
                        </div>
                        <h2 class="text-sm font-bold text-gray-800 text-center">{{ $user->name }}</h2>
                        <p class="text-xs text-gray-400 text-center mb-1.5">{{ $user->email }}</p>
                        <span class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-[10px] font-semibold px-3 py-1 rounded-full tracking-wide shadow-sm">
                            ⚡ {{ strtoupper($user->role) }}
                        </span>
                        <div class="w-full bg-gradient-to-br mt-2 from-blue-50 to-blue-100 rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-center border border-blue-100">
                            <p class="text-base sm:text-xl font-bold text-blue-600">{{ $totalMeetings }}</p>
                            <p class="text-[10px] sm:text-xs text-blue-400 font-medium tracking-wide uppercase">Total Meetings</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- ========== RIGHT COLUMN ========== --}}
            <div class="lg:col-span-8">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
                    {{-- Edit Profile --}}
                    <div class="md:col-span-8 bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
                            <div class="w-8 h-8 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm">Edit Profile</h3>
                                <p class="text-xs text-gray-400">Update your personal details</p>
                            </div>
                        </div>
                        <form action="{{ route('organizers.settings.profile.update') }}" method="POST" class="p-4">
                            @csrf
                            @method('PATCH')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Role</label>
                                    <input type="text" value="{{ ucfirst($user->role) }}" disabled class="w-full px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Phone</label>
                                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200">
                                </div>
                            </div>
                            <button type="submit" class="w-full mt-3 flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Save Changes
                            </button>
                        </form>
                    </div>
                    {{-- Notifications --}}
                    <div class="md:col-span-4 bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300">
                        <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
                            <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-semibold text-gray-800 text-sm">Notifications</h3>
                                <p class="text-xs text-gray-400">Manage alerts</p>
                            </div>
                        </div>
                        <div class="p-4 space-y-2.5">
                            @php
                                $toggles = [
                                    ['key' => 'email_alerts', 'label' => 'Email Alerts', 'desc' => 'Get notified via email'],
                                    ['key' => 'reminders_enabled', 'label' => 'Reminders', 'desc' => 'Meeting & task reminders'],
                                    ['key' => 'system_alerts', 'label' => 'System Alerts', 'desc' => 'System updates'],
                                ];
                            @endphp
                            @foreach($toggles as $toggle)
                                <div class="flex items-center justify-between p-2 bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 rounded-lg transition-all duration-200 group">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">{{ $toggle['label'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $toggle['desc'] }}</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer ml-2">
                                        <input type="checkbox" class="notification-toggle sr-only peer"
                                               data-key="{{ $toggle['key'] }}"
                                            {{ $user->{$toggle['key']} ? 'checked' : '' }}>
                                        <div class="w-10 h-5 bg-gray-300 peer-focus:ring-2 peer-focus:ring-blue-200 rounded-full peer peer-checked:bg-blue-600 transition-all duration-300"></div>
                                        <div class="absolute left-0.5 top-0.5 w-4 h-4 bg-white rounded-full shadow-sm transition-all duration-300 peer-checked:translate-x-5"></div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ====== ROLE CHANGE REQUEST (Account & Access) ====== --}}
        <div class="w-full bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100">
                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 text-sm sm:text-base">Account &amp; Access</h2>
                    <p class="text-xs text-gray-400">Request a role change to unlock more features</p>
                </div>
            </div>
            <div class="p-4 sm:p-6">
                @if($pendingRequest)
                    <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4">
                        <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                                <circle cx="12" cy="12" r="9" stroke-linecap="round" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center gap-2">
                                <p class="text-sm font-semibold text-amber-800">Request under review</p>
                                <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-[10px] font-medium px-2 py-0.5 rounded-full">
                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                    Pending
                                </span>
                            </div>
                            <p class="text-xs text-amber-700 mt-1">
                                Submitted on {{ $pendingRequest->created_at->format('M d, Y \a\t g:i A') }} for
                                <span class="font-medium">{{ ucfirst($pendingRequest->requested_role) }}</span> access.
                                An admin will review it shortly.
                            </p>
                        </div>
                    </div>
                @else
                    @if($lastRequest && $lastRequest->status === 'approved')
                        <div class="flex items-start gap-3 bg-emerald-50 border border-emerald-200 rounded-xl p-4 mb-4">
                            <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-emerald-800">Request approved 🎉</p>
                                <p class="text-xs text-emerald-700 mt-1">
                                    Your request for <span class="font-medium">{{ ucfirst($lastRequest->requested_role) }}</span> access was approved.
                                </p>
                            </div>
                        </div>
                    @elseif($lastRequest && $lastRequest->status === 'rejected')
                        <div id="role-request-rejected-alert"
                             data-role-request-id="{{ $lastRequest->id }}"
                             class="flex items-start gap-3 bg-rose-50 border border-rose-200 rounded-xl p-4 mb-4 transition-all duration-300">

                            {{-- Existing cross icon is now the ONLY dismiss button --}}
                            <button type="button"
                                    id="dismiss-role-request-rejection"
                                    aria-label="Dismiss rejected role request message"
                                    title="Dismiss"
                                    class="w-9 h-9 rounded-full bg-rose-100 hover:bg-rose-200 flex items-center justify-center shrink-0 transition-colors focus:outline-none focus:ring-2 focus:ring-rose-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-rose-600 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-rose-800">Request declined</p>
                                <p class="text-xs text-rose-700 mt-1">
                                    Your last request wasn't approved. You're welcome to submit a new one below.
                                </p>
                            </div>
                        </div>
                    @endif
                    <button type="button"
                            onclick="document.getElementById('role-request-modal').classList.remove('hidden')"
                            class="flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        {{ $lastRequest ? 'Request Role Change Again' : 'Request Role Change' }}
                    </button>
                @endif

                <div id="role-request-modal" class="hidden fixed inset-0 z-50 flex items-start justify-center pt-16 sm:pt-24 px-4">
                    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm"
                         onclick="document.getElementById('role-request-modal').classList.add('hidden')"></div>
                    <div class="relative w-full max-w-lg bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden animate-[fadeIn_.15s_ease-out]">
                        <div class="flex items-center justify-between px-5 py-4 bg-gradient-to-r from-indigo-600 to-indigo-700">
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 bg-white/15 rounded-lg flex items-center justify-center shrink-0">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                                    </svg>
                                </div>
                                <div>
                                    <h3 class="text-sm font-semibold text-white">New Role Request</h3>
                                    <p class="text-[11px] text-indigo-100">To: Admin Team</p>
                                </div>
                            </div>
                            <button type="button"
                                    onclick="document.getElementById('role-request-modal').classList.add('hidden')"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-white/80 hover:text-white hover:bg-white/10 transition-colors">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <form id="role-request-form" method="POST" action="{{ route('organizers.settings.role-request') }}" class="p-5" onsubmit="return submitRoleRequest(event)">
                            @csrf
                            <div class="grid grid-cols-2 gap-3 mb-3 pb-3 border-b border-gray-100">
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-400 mb-1 uppercase tracking-wide">From</label>
                                    <p class="text-sm text-gray-700 font-medium">Organizer</p>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-400 mb-1 uppercase tracking-wide">Requesting</label>
                                    <span class="inline-flex items-center gap-1 bg-indigo-50 text-indigo-700 text-xs font-medium px-2.5 py-1 rounded-full">
                                        Participant
                                    </span>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Subject</label>
                                <input type="text" name="subject" required placeholder="e.g. Request for Participant Access"
                                       class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 focus:bg-white transition-all duration-200 text-sm">
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Message</label>
                                <textarea name="message" required rows="4" placeholder="Explain why you'd like to become a Participant..."
                                          class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-300 focus:border-indigo-400 focus:bg-white transition-all duration-200 text-sm"></textarea>
                            </div>
                            <div class="flex items-center justify-end gap-2">
                                <button type="button"
                                        onclick="document.getElementById('role-request-modal').classList.add('hidden')"
                                        class="px-4 py-2.5 text-gray-500 hover:text-gray-700 text-sm font-medium transition-colors">
                                    Cancel
                                </button>
                                <button type="submit" id="role-request-submit-btn" class="flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" />
                                    </svg>
                                    Send Request
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ---- CHANGE PASSWORD ---- --}}
        <div class="w-full bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100">
                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-800 text-sm sm:text-base">Change Password</h2>
                    <p class="text-xs text-gray-400">Keep your account secure with a strong password</p>
                </div>
            </div>
            <form action="{{ route('organizers.settings.password.update') }}" method="POST" class="p-4 sm:p-6">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Current Password</label>
                        <input type="password" name="current_password" placeholder="Enter current password" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">New Password</label>
                        <input type="password" name="password" placeholder="Enter new password" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Confirm Password</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm new password" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200 text-sm">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mt-4 gap-3">
                    <div class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-xs text-gray-500 leading-relaxed">Use at least <span class="font-medium text-gray-700">8 characters</span> with a mix of uppercase, lowercase, numbers &amp; symbols.</p>
                    </div>
                    <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        Update Password
                    </button>
                </div>
            </form>
        </div>

        {{-- ---- DEACTIVATE ACCOUNT ---- --}}
        <div class="w-full bg-gradient-to-r from-red-50 to-red-50/50 rounded-2xl border border-red-200 hover:border-red-300 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <form action="{{ route('organizers.settings.deactivate') }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to permanently deactivate this account? This cannot be undone.');"
                  class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-6">
                @csrf
                @method('DELETE')
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-800 text-sm sm:text-base">Deactivate Account</h2>
                        <p class="text-xs text-gray-500">Permanently delete your account and all associated data</p>
                        <div class="flex items-center gap-2 mt-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-medium rounded-full">⚠️ Irreversible</span>
                            <span class="text-[10px] text-gray-400">•</span>
                            <span class="text-[10px] text-gray-400">All data will be lost</span>
                        </div>
                        <div class="mt-2">
                            <input type="password" name="password" placeholder="Confirm password to deactivate" required
                                   class="w-full sm:w-64 px-3 py-2 bg-white border border-red-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    Deactivate Account
                </button>
            </form>
        </div>
    </div>
    <script>
        (function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

            function autoHideFlashMessages() {
                const flashSuccess = document.getElementById('flash-success');
                const flashError = document.getElementById('flash-error');
                if (flashSuccess) {
                    setTimeout(() => {
                        flashSuccess.style.opacity = '0';
                        flashSuccess.style.transform = 'translateY(-10px)';
                        setTimeout(() => { flashSuccess.style.display = 'none'; }, 500);
                    }, 1500);
                }
                if (flashError) {
                    setTimeout(() => {
                        flashError.style.opacity = '0';
                        flashError.style.transform = 'translateY(-10px)';
                        setTimeout(() => { flashError.style.display = 'none'; }, 500);
                    }, 3000);
                }
            }
            autoHideFlashMessages();

            function showFlashMessage(message, type = 'success') {
                fetch('{{ route("organizers.settings.flash") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ message: message, type: type }),
                })
                    .then(() => window.location.reload())
                    .catch(() => window.location.reload());
            }

            // ---- Role Request submit (AJAX, no page reload) ----
            window.submitRoleRequest = function (e) {
                e.preventDefault();
                const form = document.getElementById('role-request-form');
                const btn = document.getElementById('role-request-submit-btn');
                const formData = new FormData(form);

                if (!csrfToken) {
                    showFlashMessage('Missing CSRF meta tag.', 'error');
                    return false;
                }

                btn.disabled = true;
                btn.textContent = 'Sending...';

                fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                    .then(async (res) => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) throw new Error(data.message || 'Request failed.');
                        document.getElementById('role-request-modal').classList.add('hidden');
                        form.reset();
                        updateRoleRequestSectionToPending();
                        showFlashMessage(data.message || 'Request submitted successfully.', 'success');
                    })
                    .catch((err) => {
                        showFlashMessage(err.message || 'Something went wrong.', 'error');
                    })
                    .finally(() => {
                        btn.disabled = false;
                        btn.innerHTML = `
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 12 3.269 3.126A59.768 59.768 0 0 1 21.485 12 59.77 59.77 0 0 1 3.27 20.876L5.999 12Zm0 0h7.5" />
                </svg>
                Send Request`;
                    });

                return false;
            };

            function updateRoleRequestSectionToPending() {
                const modal = document.getElementById('role-request-modal');
                const container = modal ? modal.parentElement.querySelector('.p-4.sm\\:p-6') : null;
                if (!container) return;

                const existingButton = container.querySelector('button[onclick*="role-request-modal"]');
                if (existingButton) {
                    existingButton.outerHTML = `
            <div class="flex items-start gap-3 bg-amber-50 border border-amber-200 rounded-xl p-4">
                <div class="w-9 h-9 rounded-full bg-amber-100 flex items-center justify-center shrink-0">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2" />
                        <circle cx="12" cy="12" r="9" stroke-linecap="round" />
                    </svg>
                </div>
                <div class="flex-1">
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-semibold text-amber-800">Request under review</p>
                        <span class="inline-flex items-center gap-1 bg-amber-100 text-amber-700 text-[10px] font-medium px-2 py-0.5 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                            Pending
                        </span>
                    </div>
                    <p class="text-xs text-amber-700 mt-1">
                        Your request was just submitted for Participant access. An admin will review it shortly.
                    </p>
                </div>
            </div>`;
                }
            }

            // ---- Avatar upload ----
            const avatarTrigger = document.getElementById('avatar-trigger');
            const avatarInput = document.getElementById('avatar-input');
            function setAllAvatars(url) {
                document.querySelectorAll('.app-avatar-img').forEach((img) => {
                    img.src = url;
                    img.style.display = '';
                });
                document.querySelectorAll('.app-avatar-initials').forEach((el) => {
                    el.style.display = 'none';
                });
            }
            function revertAllAvatarsToInitials() {
                document.querySelectorAll('.app-avatar-img').forEach((img) => {
                    img.style.display = 'none';
                });
                document.querySelectorAll('.app-avatar-initials').forEach((el) => {
                    el.style.display = '';
                });
            }
            avatarTrigger.addEventListener('click', () => avatarInput.click());
            avatarInput.addEventListener('change', () => {
                const file = avatarInput.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = (e) => setAllAvatars(e.target.result);
                reader.readAsDataURL(file);
                if (!csrfToken) {
                    showFlashMessage('Missing CSRF meta tag — add <meta name="csrf-token"> to your layout head.', 'error');
                    return;
                }
                const formData = new FormData();
                formData.append('avatar', file);
                fetch('{{ route("organizers.settings.avatar.update") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                })
                    .then(async (res) => {
                        const data = await res.json().catch(() => ({}));
                        if (!res.ok) throw new Error(data.message || ('Upload failed (' + res.status + ')'));
                        if (data.url) setAllAvatars(data.url);
                        showFlashMessage('Profile photo updated.', 'success');
                    })
                    .catch((err) => {
                        console.error('Avatar upload error:', err);
                        revertAllAvatarsToInitials();
                        showFlashMessage(err.message || 'Could not upload photo.', 'error');
                    });
            });

            // ---- Notification toggles ----
            document.querySelectorAll('.notification-toggle').forEach((toggle) => {
                toggle.addEventListener('change', function () {
                    const key = this.dataset.key;
                    const value = this.checked;
                    if (!csrfToken) {
                        this.checked = !value;
                        showFlashMessage('Missing CSRF meta tag — add <meta name="csrf-token"> to your layout head.', 'error');
                        return;
                    }
                    fetch('{{ route("organizers.settings.notifications.update") }}', {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({ [key]: value }),
                    })
                        .then(async (res) => {
                            const data = await res.json().catch(() => ({}));
                            if (!res.ok) throw new Error(data.message || ('Update failed (' + res.status + ')'));
                            showFlashMessage('Notification preferences saved.', 'success');
                        })
                        .catch((err) => {
                            console.error('Notification toggle error:', err);
                            this.checked = !value;
                            showFlashMessage(err.message || 'Could not save preference.', 'error');
                        });
                });
            });
        })();
    </script>

    <script>
        (function () {
            const alert = document.getElementById('role-request-rejected-alert');
            const dismissButton = document.getElementById('dismiss-role-request-rejection');

            if (!alert || !dismissButton) return;

            const requestId = String(alert.dataset.roleRequestId || '');
            const storageKey = 'smartmeet_role_request_rejection_dismissed';

            try {
                if (requestId && localStorage.getItem(storageKey) === requestId) {
                    alert.remove();
                    return;
                }
            } catch (error) {
                // Ignore storage restrictions; current-page dismiss still works.
            }

            dismissButton.addEventListener('click', function () {
                if (requestId) {
                    try {
                        localStorage.setItem(storageKey, requestId);
                    } catch (error) {
                        // Ignore storage restrictions.
                    }
                }

                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-4px)';

                window.setTimeout(function () {
                    alert.remove();
                }, 250);
            });
        })();
    </script>

</x-layouts.app>

