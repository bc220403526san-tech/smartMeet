@props(['totalUsers', 'activeUsers', 'inactiveUsers'])
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">

    <!-- Total Users -->
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group">
        <div class="w-12 h-12 bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl flex items-center justify-center mb-4
                    group-hover:from-blue-500 group-hover:to-indigo-600 transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="w-6 h-6 text-blue-600 group-hover:text-white transition-all duration-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 text-base mb-1">Total Users</h3>
        <p class="text-xs text-gray-500 leading-relaxed">All registered users in the system.</p>
        <div class="mt-3 flex items-center gap-2">
            <span class="text-xs text-blue-600 font-medium">{{ $totalUsers }} users</span>
            <span class="text-xs text-gray-300">•</span>
            <span class="text-xs text-gray-400">View all →</span>
        </div>
    </div>

    <!-- Active Users -->
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group">
        <div class="w-12 h-12 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-xl flex items-center justify-center mb-4
                    group-hover:from-emerald-500 group-hover:to-teal-600 transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="w-6 h-6 text-emerald-600 group-hover:text-white transition-all duration-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 text-base mb-1">Active Users</h3>
        <p class="text-xs text-gray-500 leading-relaxed">Users currently active on the platform.</p>
        <div class="mt-3 flex items-center gap-2">
            <span class="text-xs text-emerald-600 font-medium flex items-center gap-1">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                {{ $activeUsers }} active
            </span>
            <span class="text-xs text-gray-300">•</span>
            <span class="text-xs text-gray-400">View →</span>
        </div>
    </div>

    <!-- Inactive Users -->
    <div class="bg-white rounded-2xl p-5 border border-gray-100 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group">
        <div class="w-12 h-12 bg-gradient-to-br from-red-50 to-red-100 rounded-xl flex items-center justify-center mb-4
                    group-hover:from-red-500 group-hover:to-rose-600 transition-all duration-300">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                 class="w-6 h-6 text-red-500 group-hover:text-white transition-all duration-300">
                <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
            </svg>
        </div>
        <h3 class="font-semibold text-gray-800 text-base mb-1">Inactive Users</h3>
        <p class="text-xs text-gray-500 leading-relaxed">Users who are currently inactive or disabled.</p>
        <div class="mt-3 flex items-center gap-2">
            <span class="text-xs text-red-500 font-medium">{{ $inactiveUsers }} inactive</span>
            <span class="text-xs text-gray-300">•</span>
            <span class="text-xs text-gray-400">Manage →</span>
        </div>
    </div>

</div>
