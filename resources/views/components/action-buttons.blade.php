<div class="flex items-center gap-2">

    <!-- VIEW -->
    <div class="relative group">
        <button class="w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-400 hover:bg-blue-50 hover:text-blue-600 hover:border-blue-200 transition flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
            </svg>
        </button>

        <span class="absolute -top-10 left-1/2 -translate-x-1/2 whitespace-nowrap
                     bg-gray-900 text-white text-xs px-3 py-1 rounded-lg
                     opacity-0 group-hover:opacity-100 transition pointer-events-none">
            View details of this meeting
        </span>
    </div>

    <!-- CANCEL -->
    <div class="relative group">
        <button class="w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-400 hover:bg-red-50 hover:text-red-500 hover:border-red-200 transition flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </button>

        <span class="absolute -top-10 left-1/2 -translate-x-1/2 whitespace-nowrap
                     bg-gray-900 text-white text-xs px-3 py-1 rounded-lg
                     opacity-0 group-hover:opacity-100 transition pointer-events-none">
            Cancel this meeting
        </span>
    </div>

    <!-- FLAG -->
    <div class="relative group">
        <button class="w-9 h-9 rounded-xl border border-gray-200 bg-white text-gray-400 hover:bg-yellow-50 hover:text-yellow-600 hover:border-yellow-200 transition flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor" class="size-4">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5"/>
            </svg>
        </button>

        <span class="absolute -top-10 left-1/2 -translate-x-1/2 whitespace-nowrap
                     bg-gray-900 text-white text-xs px-3 py-1 rounded-lg
                     opacity-0 group-hover:opacity-100 transition pointer-events-none">
            Flag this meeting
        </span>
    </div>

</div>
