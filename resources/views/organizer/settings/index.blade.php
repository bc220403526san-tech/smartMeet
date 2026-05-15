<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    @vite('resources/css/app.css')
</head>

<body class="bg-[#f4f5f7]">

<!-- Hidden checkbox for sidebar toggle -->
<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen overflow-hidden">

    <!-- OVERLAY (mobile) -->
    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    <!-- SIDEBAR -->
    <x-sidebar.organizer-menu />

    <!-- ══════════════════════════════
         MAIN
    ══════════════════════════════ -->
    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0">

       <x-header.page-title
           title="Organizer Dashboard"
       />

        <!-- CONTENT -->
        <div class="p-4 sm:p-6 bg-gray-50 rounded-xl mt-3 overflow-y-auto flex-1">

            <!-- HEADER -->
            <div class="mb-6">
                <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Manage your account preferences and security settings.
                </p>
            </div>

            <!-- GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- LEFT PANEL -->
                <div class="lg:col-span-2 space-y-6">

                    <!-- PROFILE CARD -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                        <div class="flex items-center gap-2 mb-6">
                            <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                            <h2 class="text-base font-bold text-gray-900">Profile Settings</h2>
                        </div>

                        <!-- UPLOAD -->
                        <label class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 hover:border-blue-500 hover:bg-blue-50 rounded-2xl p-8 cursor-pointer transition">

                            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01.88-7.903A5 5 0 1115.9 6H16a4 4 0 010 8H7z"/>
                                </svg>
                            </div>

                            <p class="text-sm font-semibold text-gray-700">Upload Profile Photo</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG up to 2MB</p>

                            <span class="mt-4 px-5 py-2 bg-blue-600 text-white text-xs rounded-xl font-semibold">
                        Choose File
                    </span>

                            <input type="file" class="hidden" accept="image/*">
                        </label>

                        <!-- INPUTS -->
                        <div class="mt-6 space-y-4">

                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Full Name</label>
                                <input type="text" value="Marcus Sterling"
                                       class="mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-blue-500 outline-none">
                            </div>

                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Email</label>
                                <input type="email" value="marcus@smartmeet.com"
                                       class="mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:border-blue-500 outline-none">
                            </div>

                            <div>
                                <label class="text-xs font-bold text-gray-400 uppercase tracking-wider">Bio</label>
                                <textarea rows="3"
                                          class="mt-2 w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm resize-none focus:bg-white focus:border-blue-500 outline-none"
                                          placeholder="Tell something about yourself..."></textarea>
                            </div>

                            <div class="flex justify-end">
                                <button class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition">
                                    Save Changes
                                </button>
                            </div>

                        </div>
                    </div>

                </div>

                <!-- RIGHT PANEL -->
                <div class="space-y-6">

                    <!-- SECURITY -->
                    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">

                        <div class="flex items-center gap-2 mb-5">
                            <div class="w-1 h-6 bg-blue-600 rounded-full"></div>
                            <h2 class="text-base font-bold text-gray-900">Security</h2>
                        </div>

                        <!-- 2FA -->
                        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-5">
                            <p class="text-sm font-bold text-blue-800">Change your password</p>
                            <p class="text-xs text-blue-600 mt-1">
                                Secure your account with extra protection.
                            </p>
                        </div>

                        <!-- PASSWORD -->
                        <div class="space-y-3 mb-12">
                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-1">Current Password</label>
                            <input type="password" placeholder="Current Password"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-blue-500 outline-none">

                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-3">New Password</label>
                            <input type="password" placeholder="New Password"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-blue-500 outline-none">

                            <label class="text-xs font-bold text-gray-400 uppercase tracking-wider mt-3">Confirm Password</label>
                            <input type="password" placeholder="Confirm Password"
                                   class="w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:border-blue-500 outline-none">

                            <button class="w-full bg-gray-900 hover:bg-blue-600 text-white py-3 rounded-xl text-sm font-semibold transition">
                                Update Password
                            </button>
                        </div>
                        <!-- DANGER ZONE -->
                        <div class="mt-8 p-4 border border-red-200 rounded-xl">
                            <h3 class="text-red-500 font-bold text-base mb-1">Danger Zone</h3>
                            <p class="text-gray-500 text-sm mb-4">Once you delete your account, there is no going back. Please be certain.</p>
                            <button class="w-full border border-red-400 text-red-500 hover:bg-red-500 hover:text-white py-3 rounded-xl text-sm font-semibold transition">
                                Delete Account
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </div>
</div>

</body>
</html>
