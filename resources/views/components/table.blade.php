<div class="bg-white shadow hover:shadow-xl border border-blue-500 border-l-4 border-r-4 rounded-md shadow-[inset_0_4px_10px_rgba(0,0,0,0.2)]">

    <!-- TABLE HEADER  -->
    <div class="hidden md:grid md:grid-cols-5 text-xs text-blue-600 px-3 font-semibold
         border border-gray-200 bg-blue-50 rounded-sm shadow-[inset_0_4px_10px_rgba(0,0,0,0.2)] p-4">
        <p>TITLE & AGENDA</p>
        <p>DATE & TIME</p>
        <p>ORGANIZER</p>
        <p>STATUS</p>
        <p>ACTIONS</p>
    </div>

    <!-- ROWS SLOT -->
    <div class="divide-y">
        {{ $slot }}
    </div>

    <!-- PAGINATION -->
    <div class="flex justify-between items-center mt-4 p-4 text-sm">
        <p>Per page: 10</p>
        <div class="flex gap-2">
            <span class="bg-blue-500 text-white px-3 py-1 rounded">1</span>
            <span class="px-3 py-1">2</span>
            <span class="px-3 py-1">3</span>
        </div>
    </div>

</div>
