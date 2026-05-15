<!-- STATS -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">

    <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border-1 border-blue-600 border-r-7">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
             class="size-8 p-2 bg-blue-100 rounded-sm mt-2 mb-4 text-blue-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
        </svg>
        <p class="text-sm text-gray-500">TOTAL USERS</p>
        <h2 class="text-2xl font-bold">{{ $totalUsers }}</h2>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border-1 border-green-600 border-r-7">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="size-8 p-2 bg-green-100 rounded-sm mt-2 mb-4 text-green-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
        </svg>
        <p class="text-sm text-gray-500">ACTIVE USERS</p>
        <h2 class="text-2xl font-bold">{{ $activeUsers }}</h2>
    </div>

    <div class="bg-white p-4 rounded-lg shadow-sm hover:shadow-lg transition-shadow duration-300 border-1 border-gray-400 border-r-7">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
             class="size-8 p-2 bg-gray-100 rounded-sm mt-2 mb-4 text-gray-600">
            <path stroke-linecap="round" stroke-linejoin="round" d="M22 10.5h-6m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
        </svg>
        <p class="text-sm text-gray-500">INACTIVE USERS</p>
        <h2 class="text-2xl font-bold">{{ $inactiveUsers }}</h2>
    </div>

</div>
