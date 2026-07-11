<x-layouts.app>

    <x-slot name="header">
        <x-header.page-title title="Organizer Dashboard" />
    </x-slot>

    <!-- CONTENT -->
    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">

        <!-- Title -->
        <div class="mb-5 sm:mb-6">
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Settings</h1>
            <p class="text-gray-400 text-xs sm:text-sm mt-0.5">
                Manage your profile, security, and account preferences.
            </p>
        </div>

        <!-- ====== TOP GRID: PROFILE + EDIT PROFILE + NOTIFICATIONS ====== -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-4 sm:gap-6">

            <!-- ========== LEFT COLUMN: PROFILE CARD (spans 4 columns) ========== -->
            <div class="lg:col-span-4">
                <div class="bg-white rounded-2xl shadow-md border border-gray-100 hover:shadow-xl transition-all duration-300 overflow-hidden sticky top-4">

                    <!-- Gradient Banner - chota kiya -->
                    <div class="bg-gradient-to-br from-blue-600 via-blue-500 to-blue-400 h-16 sm:h-20 relative overflow-hidden">
                        <div class="absolute -top-4 -right-4 w-20 h-20 bg-white/10 rounded-full"></div>
                        <div class="absolute -bottom-6 -left-6 w-16 h-16 bg-white/10 rounded-full"></div>
                        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-32 h-32 bg-white/5 rounded-full"></div>
                    </div>

                    <!-- Avatar + Info - chota kiya -->
                    <div class="flex flex-col items-center -mt-10 sm:-mt-12 px-4 pb-4">
                        <div class="relative group mb-2">
                            <img src="{{ asset('images/profile.jpg') }}"
                                 class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl object-cover border-4 border-white shadow-lg">
                            <label class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center rounded-2xl cursor-pointer transition-opacity duration-200">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                                </svg>
                                <input type="file" class="hidden">
                            </label>
                        </div>

                        <h2 class="text-sm font-bold text-gray-800 text-center">Alex Thompson</h2>
                        <p class="text-xs text-gray-400 text-center mb-1.5">alex.thompson@smartmeet.ai</p>

                        <span class="bg-gradient-to-r from-blue-500 to-blue-600 text-white text-[10px] font-semibold px-3 py-1 rounded-full tracking-wide shadow-sm">
                            ⚡ Organizer
                        </span>

                        <!-- Stats -->
                        <div class="w-full bg-gradient-to-br mt-2 from-blue-50 to-blue-100 rounded-xl px-4 sm:px-6 py-2 sm:py-3 text-center border border-blue-100">
                            <p class="text-base sm:text-xl font-bold text-blue-600">128</p>
                            <p class="text-[10px] sm:text-xs text-blue-400 font-medium tracking-wide uppercase">Total Meetings</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========== RIGHT COLUMN (spans 8 columns) ========== -->
            <div class="lg:col-span-8">

                <!-- ---- EDIT PROFILE (LEFT - 8 cols) + NOTIFICATIONS (RIGHT - 4 cols) ---- -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4">

                    <!-- Edit Profile - 8 columns -->
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

                        <div class="p-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Full Name</label>
                                    <input type="text" value="Alex Thompson" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Role</label>
                                    <input type="text" value="System Administrator" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Email</label>
                                    <input type="email" value="alex.thompson@smartmeet.ai" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-medium text-gray-500 mb-1 uppercase tracking-wide">Phone</label>
                                    <input type="text" value="+1 (555) 234-8901" class="w-full px-3 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200">
                                </div>
                            </div>
                            <button class="w-full mt-3 flex items-center justify-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </div>

                    <!-- Notifications - 4 columns -->
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
                                    ['label' => 'Email Alerts', 'desc' => 'Get notified via email', 'checked' => true],
                                    ['label' => 'Reminders', 'desc' => 'Meeting & task reminders', 'checked' => true],
                                    ['label' => 'System Alerts', 'desc' => 'System updates', 'checked' => false],
                                ];
                            @endphp
                            @foreach($toggles as $toggle)
                                <div class="flex items-center justify-between p-2 bg-gray-50 hover:bg-blue-50 border border-gray-100 hover:border-blue-200 rounded-lg transition-all duration-200 group">
                                    <div>
                                        <p class="text-sm font-medium text-gray-700">{{ $toggle['label'] }}</p>
                                        <p class="text-xs text-gray-400">{{ $toggle['desc'] }}</p>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer ml-2">
                                        <input type="checkbox" {{ $toggle['checked'] ? 'checked' : '' }} class="sr-only peer">
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

        <!-- ====== FULL WIDTH SECTIONS (span the ENTIRE page, not just the right column) ====== -->

        <!-- ---- CHANGE PASSWORD (TRUE FULL WIDTH) ---- -->
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

            <div class="p-4 sm:p-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Current Password</label>
                        <input type="password" placeholder="Enter current password" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">New Password</label>
                        <input type="password" placeholder="Enter new password" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200 text-sm">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Confirm Password</label>
                        <input type="password" placeholder="Confirm new password" class="w-full px-3 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white transition-all duration-200 text-sm">
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mt-4 gap-3">
                    <div class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-xs text-gray-500 leading-relaxed">Use at least <span class="font-medium text-gray-700">8 characters</span> with a mix of uppercase, lowercase, numbers &amp; symbols.</p>
                    </div>
                    <button class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        Update Password
                    </button>
                </div>
            </div>
        </div>

        <!-- ---- DEACTIVATE ACCOUNT (TRUE FULL WIDTH) ---- -->
        <div class="w-full bg-gradient-to-r from-red-50 to-red-50/50 rounded-2xl border border-red-200 hover:border-red-300 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 p-4 sm:p-6">
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
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-red-100 text-red-700 text-[10px] font-medium rounded-full">
                                ⚠️ Irreversible
                            </span>
                            <span class="text-[10px] text-gray-400">•</span>
                            <span class="text-[10px] text-gray-400">All data will be lost</span>
                        </div>
                    </div>
                </div>
                <button class="w-full sm:w-auto flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg font-medium text-sm transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                    Deactivate Account
                </button>
            </div>
        </div>

    </div>

</x-layouts.app>
