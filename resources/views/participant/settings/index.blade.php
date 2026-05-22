<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite('resources/css/app.css')
</head>

<body class="bg-[#f4f5f7]">

<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen">

    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    {{-- SIDEBAR --}}
    <x-sidebar.participant-menu />

    {{-- MAIN --}}
    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0">

        <x-header.page-title title="Settings" />

        <div class="p-4 sm:p-6 bg-gray-50 overflow-y-auto rounded-lg mt-3 flex-1">

            <x-success />
            <x-error />

            {{-- PAGE HEADER --}}
            <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">Settings</h1>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Manage your personal preferences, security, and notification triggers.
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('participant.dashboard') }}"
                       class="px-4 py-2 rounded-xl border border-gray-200 bg-white text-sm
                              font-medium text-gray-600 hover:bg-gray-50 transition shadow-sm">
                        Cancel
                    </a>
                    <button form="settings-form" type="submit"
                            class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white
                                   text-sm font-semibold shadow-sm hover:shadow-md transition-all duration-200
                                   flex items-center gap-2">
                        <i class="fa-regular fa-floppy-disk text-xs"></i>
                        Save Changes
                    </button>
                </div>
            </div>

            <form id="settings-form"
                  action="{{ route('participant.settings.update') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- EQUAL HEIGHT 3-col grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

                    {{-- ══════════════════════════════
                         COL 1 — PROFILE
                    ══════════════════════════════ --}}
                    <div class="bg-white rounded-2xl border border-blue-400 border-l-4
                                shadow-lg hover:shadow-2xl transition-all duration-300 p-6">

                        <h2 class="text-sm font-semibold text-gray-700 mb-5 flex items-center gap-2">
                            <span class="w-1 h-4 bg-yellow-400 rounded-full"></span>
                            Profile Information
                        </h2>

                        {{-- AVATAR --}}
                        <div class="flex flex-col items-center mb-6">
                            <div class="relative group w-20 h-20">
                                <img id="avatar-preview"
                                     src="{{ auth()->user()->image_url
                                             ? asset('storage/' . auth()->user()->image_url)
                                             : asset('images/default-avatar.png') }}"
                                     alt="Avatar"
                                     class="w-20 h-20 rounded-2xl object-cover border-2 border-gray-100 shadow">

                                <label for="avatar-upload"
                                       class="absolute inset-0 rounded-2xl bg-black/40 flex items-center
                                              justify-center opacity-0 group-hover:opacity-100
                                              transition-opacity cursor-pointer">
                                    <i class="fa-solid fa-camera text-white text-base"></i>
                                </label>
                                <input type="file" id="avatar-upload" name="avatar"
                                       accept="image/*" class="hidden"
                                       onchange="previewAvatar(event)">
                            </div>
                            <p class="text-xs text-gray-400 mt-2">Click to update photo</p>
                        </div>

                        {{-- FIELDS --}}
                        <div class="space-y-4">

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">
                                    Full Name
                                </label>
                                <input type="text" name="name"
                                       value="{{ old('name', auth()->user()->name) }}"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                              text-gray-700 bg-gray-50 focus:bg-white focus:outline-none
                                              focus:ring-2 focus:ring-blue-300 focus:border-transparent
                                              transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">
                                    Phone Number
                                </label>
                                <input type="text" name="phone"
                                       value="{{ old('phone', auth()->user()->phone ?? '') }}"
                                       placeholder="+1 (555) 000-0000"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                              text-gray-700 bg-gray-50 focus:bg-white focus:outline-none
                                              focus:ring-2 focus:ring-blue-300 focus:border-transparent
                                              transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">
                                    Bio / Role Detail
                                </label>
                                <textarea name="bio" rows="4"
                                          placeholder="Brief description of your role..."
                                          class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                                 text-gray-700 bg-gray-50 focus:bg-white focus:outline-none
                                                 focus:ring-2 focus:ring-blue-300 focus:border-transparent
                                                 transition-all duration-200 resize-none">{{ old('bio', auth()->user()->bio ?? '') }}</textarea>
                            </div>

                        </div>
                    </div>

                    {{-- ══════════════════════════════
                         COL 2 — SECURITY
                    ══════════════════════════════ --}}
                    <div class="bg-white rounded-2xl border border-blue-400 border-l-4
                                shadow-lg hover:shadow-2xl transition-all duration-300
                                 p-6 h-full">

                        <h2 class="text-sm font-semibold text-gray-700 mb-5 flex items-center gap-2">
                            <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                            Security & Password
                        </h2>

                        <div class="space-y-4">

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">
                                    Current Password
                                </label>
                                <input type="password" name="current_password"
                                       placeholder="••••••••"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                              text-gray-700 bg-gray-50 focus:bg-white focus:outline-none
                                              focus:ring-2 focus:ring-blue-300 focus:border-transparent
                                              transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">
                                    New Password
                                </label>
                                <input type="password" name="new_password"
                                       placeholder="Min. 8 characters"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                              text-gray-700 bg-gray-50 focus:bg-white focus:outline-none
                                              focus:ring-2 focus:ring-blue-300 focus:border-transparent
                                              transition-all duration-200">
                            </div>

                            <div>
                                <label class="block text-xs font-semibold text-gray-400 uppercase tracking-wide mb-1.5">
                                    Confirm Password
                                </label>
                                <input type="password" name="new_password_confirmation"
                                       placeholder="Re-type new password"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm
                                              text-gray-700 bg-gray-50 focus:bg-white focus:outline-none
                                              focus:ring-2 focus:ring-blue-300 focus:border-transparent
                                              transition-all duration-200">
                            </div>

                            {{-- INFO --}}
                            <div class="bg-gray-50 rounded-xl px-4 py-3 flex items-start gap-2">
                                <i class="fa-regular fa-circle-info text-blue-400 mt-0.5 text-sm shrink-0"></i>
                                <p class="text-xs text-gray-400 leading-relaxed">
                                    Last changed
                                    <span class="font-medium text-gray-500">
                                        {{ auth()->user()->password_changed_at
                                            ? \Carbon\Carbon::parse(auth()->user()->password_changed_at)->diffForHumans()
                                            : 'never' }}.
                                    </span>
                                    We recommend updating every 6 months for security.
                                </p>
                            </div>

                            <button type="submit"
                                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold
                                           text-sm py-2.5 rounded-xl transition-all duration-200 shadow-sm
                                           hover:shadow-md flex items-center justify-center gap-2">
                                <i class="fa-solid fa-lock text-xs"></i>
                                Update Password
                            </button>

                        </div>
                    </div>

                    {{-- ══════════════════════════════
                         COL 3 — NOTIFICATIONS
                    ══════════════════════════════ --}}
                    <div class="bg-white rounded-2xl border border-blue-400 border-l-4
                                shadow-lg hover:shadow-2xl transition-all duration-300
                                 p-6 h-full">

                        <h2 class="text-sm font-semibold text-gray-700 mb-5 flex items-center gap-2">
                            <span class="w-1 h-4 bg-blue-500 rounded-full"></span>
                            Notifications
                        </h2>
                        <p class="text-xs text-gray-500 ">Stay updated with important alerts and manage
                            how you receive notifications.</p>
                        <br>

                        <div class="space-y-3">

                            {{-- Meeting Reminders --}}
                            <div class="flex items-center justify-between p-3.5 rounded-xl
                                        bg-gray-50 hover:bg-blue-50 transition-colors duration-200 group">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700 group-hover:text-blue-700 transition-colors">
                                        Meeting Reminders
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">15 mins before start</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                                    <input type="checkbox" name="notif_meeting_reminders" value="1"
                                           class="sr-only peer"
                                        {{ old('notif_meeting_reminders', auth()->user()->notif_meeting_reminders ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-300 peer-checked:bg-blue-600 rounded-full
                                                transition-colors duration-300
                                                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                                after:bg-white after:rounded-full after:h-5 after:w-5
                                                after:shadow-sm after:transition-all
                                                peer-checked:after:translate-x-5"></div>
                                </label>
                            </div>

                            {{-- Email Notifications --}}
                            <div class="flex items-center justify-between p-3.5 rounded-xl
                                        bg-gray-50 hover:bg-blue-50 transition-colors duration-200 group">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700 group-hover:text-blue-700 transition-colors">
                                        Email Notifications
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">Weekly summaries</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                                    <input type="checkbox" name="notif_email" value="1"
                                           class="sr-only peer"
                                        {{ old('notif_email', auth()->user()->notif_email ?? true) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-300 peer-checked:bg-blue-600 rounded-full
                                                transition-colors duration-300
                                                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                                after:bg-white after:rounded-full after:h-5 after:w-5
                                                after:shadow-sm after:transition-all
                                                peer-checked:after:translate-x-5"></div>
                                </label>
                            </div>

                            {{-- Sound Alerts --}}
                            <div class="flex items-center justify-between p-3.5 rounded-xl
                                        bg-gray-50 hover:bg-blue-50 transition-colors duration-200 group">
                                <div>
                                    <p class="text-sm font-semibold text-gray-700 group-hover:text-blue-700 transition-colors">
                                        Sound Alerts
                                    </p>
                                    <p class="text-xs text-gray-400 mt-0.5">In-app notifications</p>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0 ml-3">
                                    <input type="checkbox" name="notif_sound" value="1"
                                           class="sr-only peer"
                                        {{ old('notif_sound', auth()->user()->notif_sound ?? false) ? 'checked' : '' }}>
                                    <div class="w-11 h-6 bg-gray-300 peer-checked:bg-blue-600 rounded-full
                                                transition-colors duration-300
                                                after:content-[''] after:absolute after:top-0.5 after:left-0.5
                                                after:bg-white after:rounded-full after:h-5 after:w-5
                                                after:shadow-sm after:transition-all
                                                peer-checked:after:translate-x-5"></div>
                                </label>
                            </div>

                        </div>
                    </div>

                </div>
            </form>

        </div>
    </div>

</div>

<script>
    function previewAvatar(event) {
        const file = event.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = e => {
            document.getElementById('avatar-preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
</script>

</body>
</html>
