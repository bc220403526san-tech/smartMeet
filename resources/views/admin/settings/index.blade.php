<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Admin Dashboard" />
    </x-slot>

    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">

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

        <div class="mb-4 sm:mb-6">
            <h1 class="text-lg sm:text-xl md:text-2xl font-semibold text-gray-800">Settings</h1>
            <p class="text-gray-400 text-xs sm:text-sm mt-0.5">
                Manage your profile, security, and account preferences.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">

            {{-- ========== LEFT COLUMN: PROFILE CARD ========== --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden lg:sticky lg:top-4">
                    <div class="bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400 h-16 sm:h-20 relative overflow-hidden">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-white/10 rounded-full"></div>
                        <div class="absolute -bottom-6 -left-6 w-16 h-16 bg-white/10 rounded-full"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-white/5 rounded-full"></div>
                    </div>

                    {{-- FIX: fixed h-60 hata kar min-height + natural flow diya,
                         taake lamba naam/email content ko crop na kare aur
                         chhota content khali jagah na chhode --}}
                    <div class="flex flex-col items-center -mt-10 sm:-mt-12 px-4 pb-5 min-h-[15rem]">
                        @php
                            $initials = collect(explode(' ', trim($user->name)))
                                ->map(fn($part) => mb_substr($part, 0, 1))
                                ->take(2)
                                ->implode('');
                            $initials = mb_strtoupper($initials ?: 'U');
                        @endphp

                        <div class="relative group mb-2 w-16 h-16 sm:w-20 sm:h-20 shrink-0">
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

                        <h2 class="text-sm font-bold text-gray-800 text-center break-words max-w-full px-2">{{ $user->name }}</h2>
                        <p class="text-xs text-gray-400 text-center mb-1.5 break-all max-w-full px-2">{{ $user->email }}</p>
                        <span class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-[10px] font-semibold px-3 py-1 rounded-full tracking-wide shadow-sm whitespace-nowrap">
                            &#9889; {{ strtoupper($user->role) }}
                        </span>

                        {{-- ADDED: Member Since + Account Status quick-info block --}}
                        <div class="w-full mt-4 pt-4 border-t border-gray-100 grid grid-cols-2 gap-2">
                            <div class="text-center px-1">
                                <p class="text-[10px] uppercase tracking-wide text-gray-400 font-medium">Member Since</p>
                                <p class="text-xs sm:text-sm font-semibold text-gray-700 mt-0.5">
                                    {{ $user->created_at?->format('M Y') ?? '-' }}
                                </p>
                            </div>
                            <div class="text-center px-1 border-l border-gray-100">
                                <p class="text-[10px] uppercase tracking-wide text-gray-400 font-medium">Status</p>
                                @php $isActive = $user->is_active ?? true; @endphp
                                <p class="text-xs sm:text-sm font-semibold mt-0.5 {{ $isActive ? 'text-green-600' : 'text-red-500' }}">
                                    <span class="inline-block w-1.5 h-1.5 rounded-full {{ $isActive ? 'bg-green-500' : 'bg-red-400' }} mr-1 align-middle"></span>
                                    {{ $isActive ? 'Active' : 'Inactive' }}
                                </p>
                            </div>
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
                            <div class="min-w-0">
                                <h3 class="font-semibold text-gray-800 text-sm">Edit Profile</h3>
                                <p class="text-xs text-gray-400">Update your personal details</p>
                            </div>
                        </div>
                        <form action="{{ route('admin.settings.profile.update') }}" method="POST" class="p-4">
                            @csrf
                            @method('PATCH')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Role</label>
                                    <input type="text" name="role" value="{{ old('role', $user->role) }}" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200">
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
                            <div class="min-w-0">
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
                                <div class="flex items-center justify-between gap-2 p-2 bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 rounded-lg transition-all duration-200 group">
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-700 truncate">{{ $toggle['label'] }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $toggle['desc'] }}</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer shrink-0">
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

        {{-- ---- CHANGE PASSWORD ---- --}}
        <div class="w-full bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="flex items-center gap-3 px-4 sm:px-6 py-3 sm:py-4 border-b border-gray-100">
                <div class="w-8 h-8 sm:w-9 sm:h-9 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                    </svg>
                </div>
                <div class="min-w-0">
                    <h2 class="font-semibold text-gray-800 text-sm sm:text-base">Change Password</h2>
                    <p class="text-xs text-gray-400">Keep your account secure with a strong password</p>
                </div>
            </div>
            <form action="{{ route('admin.settings.password.update') }}" method="POST" class="p-4 sm:p-6">
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
                    <div class="flex items-start gap-2 min-w-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-xs text-gray-500 leading-relaxed">Use at least <span class="font-medium text-gray-700">8 characters</span> with a mix of uppercase, lowercase, numbers &amp; symbols.</p>
                    </div>
                    <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md shrink-0">
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
            <form action="{{ route('admin.settings.deactivate') }}" method="POST"
                  onsubmit="return confirm('Are you sure you want to permanently deactivate this account? This cannot be undone.');"
                  class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-6">
                @csrf
                @method('DELETE')
                <div class="flex items-start gap-3 min-w-0">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shrink-0 shadow-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h2 class="font-semibold text-gray-800 text-sm sm:text-base">Deactivate Account</h2>
                        <p class="text-xs text-gray-500">Permanently delete your account and all associated data</p>
                        <div class="flex flex-wrap items-center gap-2 mt-1.5">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-medium rounded-full">&#9888;&#65039; Irreversible</span>
                            <span class="text-[10px] text-gray-400">&bull;</span>
                            <span class="text-[10px] text-gray-400">All data will be lost</span>
                        </div>
                        <div class="mt-2">
                            <input type="password" name="password" placeholder="Confirm password to deactivate" required
                                   class="w-full sm:w-64 px-3 py-2 bg-white border border-red-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-red-300">
                        </div>
                    </div>
                </div>
                <button type="submit" class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md shrink-0">
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
                fetch('{{ route("admin.settings.flash") }}', {
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
                    showFlashMessage('Missing CSRF meta tag - add <meta name="csrf-token"> to your layout head.', 'error');
                    return;
                }

                const formData = new FormData();
                formData.append('avatar', file);

                fetch('{{ route("admin.settings.avatar.update") }}', {
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
                        showFlashMessage('Missing CSRF meta tag - add <meta name="csrf-token"> to your layout head.', 'error');
                        return;
                    }

                    fetch('{{ route("admin.settings.notifications.update") }}', {
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
</x-layouts.app>
