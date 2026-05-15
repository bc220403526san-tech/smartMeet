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

<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen overflow-hidden">

    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    <!-- SIDEBAR -->
    <x-sidebar.organizer-menu />

    <!-- ══════════════════════════ MAIN ══════════════════════════ -->
    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0">

        <!-- HEADER -->
        <x-header.page-title
            title="Organizer Dashboard"
        />

        <!-- Content -->
        <div class="p-4 sm:p-6 bg-gray-50 rounded-xl mt-3 overflow-y-auto flex-1">

            <!-- Page Heading -->
            <div class="mb-6">
                <div class="flex items-center gap-2 text-xs text-slate-400 font-medium uppercase tracking-wider mb-2 flex-wrap">
                    <span>Management</span>
                    <span>›</span>
                    <span class="text-blue-500">Participants</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Edit Participant</h1>
                <p class="text-sm text-slate-400 mt-1">Update profile details and permissions for team members.</p>
            </div>

            <!-- Main Card -->
            <div class="bg-white rounded-2xl p-5 sm:p-8 shadow-sm border border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300 ease-in-out">

                <!--
                    Layout:
                    - Mobile (default): stack avatar block above form fields
                    - sm+: side by side
                -->
                <div class="flex flex-col sm:flex-row gap-6 sm:gap-10">

                    <!-- Left: Avatar + Status -->
                    <div class="flex sm:flex-col items-center sm:items-center gap-5 sm:gap-4 sm:flex-shrink-0">

                        <!-- Avatar upload -->
                        <div class="relative group w-24 h-32 sm:w-28 sm:h-36 flex-shrink-0">
                            <input
                                id="avatarInput"
                                type="file"
                                accept="image/*"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                            />
                            <div class="w-full h-full rounded-xl overflow-hidden bg-slate-100 border border-slate-200
                              pointer-events-none">
                                <img src="{{asset('images/image4.avif')}}"
                                     alt="SC"
                                     class="w-full h-full object-cover flex items-center justify-center bg-indigo-100">
                            </div>
                            <div class="absolute inset-0 rounded-xl bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity pointer-events-none flex flex-col items-center justify-center gap-1">
                                <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                                    <polyline points="17 8 12 3 7 8"/>
                                    <line x1="12" y1="3" x2="12" y2="15"/>
                                </svg>
                                <span class="text-white text-[11px] font-semibold">Upload</span>
                            </div>
                        </div>

                        <!-- Change Picture + Status: side-by-side on mobile, stacked on sm+ -->
                        <div class="flex flex-col gap-3 flex-1 sm:flex-none sm:w-full sm:items-center">
                            <label
                                for="avatarInput"
                                class="text-sm text-blue-500 font-medium hover:text-blue-700 hover:underline transition cursor-pointer text-center"
                            >
                                Change Profile Picture
                            </label>

                            <!-- Status toggle -->
                            <div class="w-full border border-slate-200 rounded-xl px-3 sm:px-4 py-3 flex items-center justify-between gap-4 bg-slate-50">
                                <div>
                                    <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">System Status</div>
                                    <div class="text-sm font-semibold text-slate-700 whitespace-nowrap">Active Account</div>
                                </div>
                                <label class="cursor-pointer relative inline-flex items-center flex-shrink-0">
                                    <input type="checkbox" checked class="sr-only peer">
                                    <div class="w-10 h-5 rounded-full bg-gray-300 peer-checked:bg-blue-600 transition-colors duration-300 relative">
                                        <div class="absolute top-[3px] left-[3px] w-[14px] h-[14px] bg-white rounded-full shadow transition-transform duration-300 peer-checked:translate-x-5"></div>
                                    </div>
                                </label>
                            </div>
                        </div>

                    </div>

                    <!-- Right: Form Fields -->
                    <div class="flex-1 flex flex-col gap-4 sm:gap-5">

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Full Name</label>
                            <input
                                type="text"
                                value="Sarah Chen"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium bg-slate-50 focus:outline-none focus:border-blue-400 focus:bg-white transition"
                            />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Email Address</label>
                            <input
                                type="email"
                                value="sarah.chen@techflow.io"
                                class="w-full px-4 py-3 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium bg-slate-50 focus:outline-none focus:border-blue-400 focus:bg-white transition"
                            />
                        </div>

                        <div class="flex flex-col gap-1.5">
                            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Role</label>
                            <div class="relative">
                                <select class="w-full appearance-none px-4 py-3 border border-slate-200 rounded-xl text-sm text-slate-800 font-medium bg-slate-50 focus:outline-none focus:border-blue-400 focus:bg-white transition cursor-pointer">
                                    <option>Participant</option>
                                    <option>Organizer</option>
                                </select>
                                <div class="pointer-events-none absolute right-4 top-1/2 -translate-y-1/2 text-slate-400">
                                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path d="M6 9l6 6 6-6"/>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="xs:flex-row gap-4 mt-2 justify-center">
                            <button class="w-40 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-bold rounded-xl transition">
                                Update Participant
                            </button>
                            <button class="w-32 py-2 bg-slate-100 ml-3 hover:bg-slate-200 text-slate-500 text-sm font-semibold rounded-xl transition">
                                Cancel
                            </button>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>


</body>
</html>
