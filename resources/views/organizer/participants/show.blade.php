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

    <!-- Overlay (closes sidebar on mobile) -->
    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    <!-- SIDEBAR -->
    <x-sidebar.organizer-menu />

    <!-- ══════════════════════════ MAIN ══════════════════════════ -->
    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0 lg:ml-0">

        <!-- HEADER -->
        <x-header.page-title
            title="Organizer Dashboard"
        />

        <!-- Content -->
        <div class="p-4 sm:p-6 bg-gray-50 rounded-xl mt-3 overflow-y-auto flex-1">

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-7">
                <div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-400 mb-1.5 flex-wrap">
                        <a href="#" class="text-blue-500 font-medium hover:underline">Participants</a>
                        <span>›</span>
                        <span class="text-blue-500">Sarah Chen</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">Participant Details</h1>
                </div>
                <div class="flex items-center gap-3 sm:mt-1 flex-shrink-0">
                    <button class="flex items-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 border border-slate-200 rounded-xl bg-white text-slate-500 text-sm font-semibold hover:bg-slate-50 hover:border-slate-300 transition whitespace-nowrap">
                        ← Back
                    </button>
                    <button class="flex items-center gap-2 px-3 sm:px-5 py-2 sm:py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition whitespace-nowrap">
                        Edit Participant
                    </button>
                </div>
            </div>

            <!-- Cards: stack on mobile, side-by-side on lg+ -->
            <div class="grid grid-cols-1 lg:grid-cols-[1fr_1.3fr] gap-6 max-w-6xl mx-auto items-start">

                <!-- Profile Card -->
                <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-sm border border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300 ease-in-out">
                    <div class="flex justify-center mb-5 relative">
                        <div class="w-20 h-20 rounded-full border-2 border-indigo-200 overflow-hidden bg-indigo-100 flex items-center justify-center text-indigo-400 text-3xl font-bold">
                            SC
                        </div>
                        <span class="w-3.5 h-3.5 bg-green-400 border-2 border-white rounded-full absolute bottom-0 right-[calc(50%-42px)]"></span>
                    </div>
                    <div class="text-center text-lg font-bold text-slate-900 mb-1">Sarah Chen</div>
                    <div class="text-center text-sm text-slate-400 mb-4">sarah.chen@techflow.io</div>
                    <div class="flex justify-center gap-2 mb-6 flex-wrap">
                        <span class="px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide bg-blue-50 text-blue-500 border border-blue-200">Participant</span>
                        <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wide bg-green-50 text-green-600 border border-green-200">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-400 inline-block"></span>
                            Active
                        </span>
                    </div>
                    <div class="flex items-center justify-between bg-slate-50 border border-slate-200 rounded-xl px-4 py-3.5">
                        <div>
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Join Date</div>
                            <div class="text-sm font-semibold text-slate-900">Oct 10, 2023</div>
                        </div>
                        <div class="w-9 h-9 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-400">
                            <svg width="17" height="17" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div class="bg-white rounded-2xl p-6 sm:p-7 shadow-sm border border-l-4 border-blue-500 hover:shadow-lg transition-all duration-300 ease-in-out">
                    <div class="text-base font-bold text-slate-900 mb-6">Recent Activity</div>
                    <div class="flex flex-col gap-4 sm:gap-5">

                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#3b82f6" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800 truncate">Q4 Product Roadmap Sync</div>
                                <div class="text-xs text-slate-400 mt-0.5">Oct 24, 2023 · 45 minutes</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-violet-50 flex items-center justify-center flex-shrink-0">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#8b5cf6" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800 truncate">Design Systems Deep Dive</div>
                                <div class="text-xs text-slate-400 mt-0.5">Oct 22, 2023 · 1 hour 15 mins</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 sm:gap-4">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f59e0b" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800 truncate">Agile Sprint Planning</div>
                                <div class="text-xs text-slate-400 mt-0.5">Oct 20, 2023 · 30 minutes</div>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 sm:gap-4 mb-1">
                            <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-rose-50 flex items-center justify-center flex-shrink-0">
                                <svg width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#f43f5e" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </div>
                            <div class="min-w-0">
                                <div class="text-sm font-semibold text-slate-800 truncate">UX Review & Feedback Session</div>
                                <div class="text-xs text-slate-400 mt-0.5">Oct 18, 2023 · 50 minutes</div>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
