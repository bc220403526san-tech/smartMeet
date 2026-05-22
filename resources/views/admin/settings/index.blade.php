<x-layouts.app>

    <x-slot name="header">
        <x-header.page-title title="Admin Dashboard" />
    </x-slot>

    <!-- CONTENT -->
    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">

        <!-- Title -->
        <div class="mb-5 sm:mb-6">
            <h1 class="text-xl sm:text-2xl font-semibold text-gray-800">Settings</h1>
            <p class="text-gray-400 text-xs sm:text-sm mt-0.5">
                Manage your account preferences and configurations.
            </p>
        </div>

        <!-- GRID -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">

            <!-- PASSWORD — full width -->
            <div class="md:col-span-2 bg-white p-5 sm:p-8 rounded-xl
                        border border-gray-200 border-l-2 sm:border-l-4 border-l-blue-500
                        shadow-sm hover:shadow-xl transition-all duration-300 ease-in-out">

                <!-- Header -->
                <div class="flex items-center gap-3 mb-5 sm:mb-6">
                    <div class="w-9 h-9 sm:w-10 sm:h-10 bg-blue-100 rounded-xl flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-4 h-4 sm:w-5 sm:h-5 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-800">Change Password</h2>
                        <p class="text-xs sm:text-sm text-gray-400">Keep your account secure with a strong password</p>
                    </div>
                </div>

                <!-- 3-column input layout -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">

                    <!-- Current Password -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Current Password</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 0 1 3 3m3 0a6 6 0 0 1-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 0 1 21.75 8.25Z" />
                                </svg>
                            </span>
                            <input type="password" placeholder="Enter current password"
                                   class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg
                                          focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white
                                          transition-all duration-200 text-sm">
                        </div>
                    </div>

                    <!-- New Password -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">New Password</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            </span>
                            <input type="password" placeholder="Enter new password"
                                   class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg
                                          focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white
                                          transition-all duration-200 text-sm">
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="sm:col-span-2 md:col-span-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Confirm Password</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z" />
                                </svg>
                            </span>
                            <input type="password" placeholder="Confirm new password"
                                   class="w-full pl-10 pr-4 py-2.5 sm:py-3 bg-gray-50 border border-gray-200 rounded-lg
                                          focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400 focus:bg-white
                                          transition-all duration-200 text-sm">
                        </div>
                    </div>

                </div>

                <!-- Hint + Button -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mt-5 sm:mt-6 gap-4">
                    <div class="flex items-start gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                        </svg>
                        <p class="text-xs text-gray-500 leading-relaxed">
                            Use at least <span class="font-medium text-gray-700">8 characters</span> with a mix of uppercase, lowercase, numbers &amp; symbols.
                        </p>
                    </div>
                    <button class="w-full sm:w-auto shrink-0 flex items-center justify-center gap-2 px-6 py-2.5 sm:py-3
                                   bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white rounded-lg font-medium text-sm
                                   transition-all duration-200 shadow-sm hover:shadow-md active:shadow-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 1 0-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                        </svg>
                        Update Password
                    </button>
                </div>

            </div>

            <!-- PREFERENCES -->
            <div class="bg-white p-5 sm:p-6 rounded-xl
                        border border-gray-200 border-l-4 sm:border-l-4 border-l-blue-500
                        shadow-sm hover:shadow-xl transition-all duration-300 ease-in-out">

                <!-- Header -->
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 0 1 1.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.559.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 0 1-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 0 1-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.526-.738a1.125 1.125 0 0 1 .12-1.45l.773-.773a1.125 1.125 0 0 1 1.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894Z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                        </svg>
                    </div>
                    <h2 class="font-semibold text-gray-800">Preferences</h2>
                </div>

                <div class="space-y-4">
                    <!-- Meeting Duration -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Meeting Duration</label>
                        <select class="w-full p-2.5 sm:p-3 bg-gray-50 border border-gray-200 rounded-lg
                                       focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                       transition-all duration-200 text-sm">
                            <option>15 min</option>
                            <option selected>30 min</option>
                            <option>60 min</option>
                        </select>
                    </div>

                    <!-- Time Zone -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Time Zone</label>
                        <select class="w-full p-2.5 sm:p-3 bg-gray-50 border border-gray-200 rounded-lg
                                       focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                       transition-all duration-200 text-sm">
                            <option value="" disabled selected>Select Time Zone</option>
                            <optgroup label="Pakistan">
                                <option value="Asia/Karachi">PKT — Pakistan Standard Time (UTC+5)</option>
                            </optgroup>
                            <optgroup label="UTC Offsets">
                                <option value="Etc/GMT+12">UTC−12:00 — Baker Island</option>
                                <option value="Etc/GMT+11">UTC−11:00 — American Samoa</option>
                                <option value="Etc/GMT+10">UTC−10:00 — Hawaii (HST)</option>
                                <option value="Etc/GMT+9">UTC−09:00 — Alaska (AKST)</option>
                                <option value="Etc/GMT+8">UTC−08:00 — Pacific Time (PST)</option>
                                <option value="Etc/GMT+7">UTC−07:00 — Mountain Time (MST)</option>
                                <option value="Etc/GMT+6">UTC−06:00 — Central Time (CST)</option>
                                <option value="Etc/GMT+5">UTC−05:00 — Eastern Time (EST)</option>
                                <option value="Etc/GMT+4">UTC−04:00 — Atlantic Time (AST)</option>
                                <option value="Etc/GMT+3">UTC−03:00 — Buenos Aires</option>
                                <option value="UTC">UTC±00:00 — London (GMT)</option>
                                <option value="Etc/GMT-1">UTC+01:00 — Berlin, Paris (CET)</option>
                                <option value="Etc/GMT-2">UTC+02:00 — Cairo (EET)</option>
                                <option value="Etc/GMT-3">UTC+03:00 — Moscow (EAT)</option>
                                <option value="Etc/GMT-4">UTC+04:00 — Dubai</option>
                                <option value="Asia/Karachi">UTC+05:00 — Karachi</option>
                                <option value="Asia/Kolkata">UTC+05:30 — India (IST)</option>
                                <option value="Etc/GMT-8">UTC+08:00 — Singapore, Beijing</option>
                                <option value="Etc/GMT-9">UTC+09:00 — Tokyo (JST)</option>
                                <option value="Etc/GMT-10">UTC+10:00 — Sydney (AEST)</option>
                                <option value="Etc/GMT-12">UTC+12:00 — Auckland (NZST)</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Language -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5 uppercase tracking-wide">Language</label>
                        <select class="w-full p-2.5 sm:p-3 bg-gray-50 border border-gray-200 rounded-lg
                                       focus:outline-none focus:ring-2 focus:ring-blue-300 focus:border-blue-400
                                       transition-all duration-200 text-sm">
                            <option value="" disabled selected>Select Language</option>
                            <option>English</option>
                            <option>Urdu</option>
                        </select>
                    </div>
                </div>

            </div>

            <!-- NOTIFICATIONS -->
            <div class="bg-white p-5 sm:p-6 rounded-xl
                        border border-gray-200 border-l-4 sm:border-l-4 border-l-blue-500
                        shadow-sm hover:shadow-xl transition-all duration-300 ease-in-out">

                <!-- Header -->
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                             stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
                        </svg>
                    </div>
                    <h2 class="font-semibold text-gray-800">Notifications</h2>
                </div>

                <!-- Toggles -->
                <div class="space-y-3">

                    @php
                        $toggles = [
                            [
                                'label'   => 'Email Alerts',
                                'desc'    => 'Get notified via email',
                                'checked' => true,
                                'icon'    => 'M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75',
                            ],
                            [
                                'label'   => 'Reminders',
                                'desc'    => 'Meeting & task reminders',
                                'checked' => true,
                                'icon'    => 'M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                            ],
                            [
                                'label'   => 'System Alerts',
                                'desc'    => 'System updates & warnings',
                                'checked' => false,
                                'icon'    => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94H9.75c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z',
                            ],
                        ];
                    @endphp

                    @foreach($toggles as $toggle)
                        <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 border border-gray-100 rounded-lg
                                    hover:bg-blue-50 hover:border-blue-100 hover:shadow-sm
                                    transition-all duration-200 ease-in-out group">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-blue-100 group-hover:bg-blue-200 rounded-lg flex items-center justify-center shrink-0 transition-colors duration-200">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $toggle['icon'] }}" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-700">{{ $toggle['label'] }}</p>
                                    <p class="text-xs text-gray-400">{{ $toggle['desc'] }}</p>
                                </div>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                                <input type="checkbox" {{ $toggle['checked'] ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-11 h-6 bg-gray-300 peer-focus:ring-2 peer-focus:ring-blue-200 rounded-full
                                            peer peer-checked:bg-blue-600 transition-all duration-300"></div>
                                <div class="absolute left-1 top-1 w-4 h-4 bg-white rounded-full shadow-sm
                                            transition-all duration-300 peer-checked:translate-x-5"></div>
                            </label>
                        </div>
                    @endforeach

                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
