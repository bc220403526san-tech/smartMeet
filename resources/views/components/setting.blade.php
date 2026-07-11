{{--@props([])--}}

{{-- Notification --}}
{{--<div class="relative">--}}
{{--    <button id="notif-bell-btn" onclick="toggleNotifDropdown()" type="button"--}}
{{--            class="relative block">--}}
{{--        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"--}}
{{--             stroke-width="1.5" stroke="currentColor"--}}
{{--             class="w-5 h-5 text-gray-600 hover:text-blue-500 cursor-pointer">--}}
{{--            <path stroke-linecap="round" stroke-linejoin="round"--}}
{{--                  d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>--}}
{{--        </svg>--}}
{{--        <span id="notif-badge"--}}
{{--              class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold--}}
{{--                     min-w-[16px] h-4 px-[3px] rounded-full flex items-center justify-center leading-none">0</span>--}}
{{--    </button>--}}

{{--    <div id="notif-dropdown"--}}
{{--         class="hidden absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-lg border border-gray-100 z-50 max-h-96 overflow-y-auto">--}}
{{--        <div class="flex justify-between items-center px-4 py-3 border-b border-gray-100 sticky top-0 bg-white">--}}
{{--            <h4 class="text-sm font-semibold text-gray-800">Notifications</h4>--}}
{{--            <button onclick="markAllNotificationsRead()" class="text-xs text-blue-600 hover:underline">--}}
{{--                Mark all read--}}
{{--            </button>--}}
{{--        </div>--}}
{{--        <div id="notif-list" class="divide-y divide-gray-50">--}}
{{--            <p class="text-center text-gray-400 text-xs py-6">Loading...</p>--}}
{{--        </div>--}}
{{--    </div>--}}
{{--</div>--}}

{{-- Settings --}}
{{--<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"--}}
{{--     stroke-width="1.5" stroke="currentColor"--}}
{{--     class="w-5 h-5 text-gray-600 hover:text-blue-500 cursor-pointer mr-5">--}}
{{--    <path stroke-linecap="round" stroke-linejoin="round"--}}
{{--          d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/>--}}
{{--    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>--}}
{{--</svg>--}}

{{--<script>--}}
{{--    // ============================================================--}}
{{--    // NOTIFICATION BELL — dropdown toggle, fetch, mark read--}}
{{--    // ============================================================--}}
{{--    function toggleNotifDropdown() {--}}
{{--        document.getElementById('notif-dropdown').classList.toggle('hidden');--}}
{{--    }--}}

{{--    document.addEventListener('click', function (e) {--}}
{{--        const bell = document.getElementById('notif-bell-btn');--}}
{{--        const dropdown = document.getElementById('notif-dropdown');--}}
{{--        if (bell && dropdown && !bell.contains(e.target) && !dropdown.contains(e.target)) {--}}
{{--            dropdown.classList.add('hidden');--}}
{{--        }--}}
{{--    });--}}

{{--    function renderNotifications(notifications) {--}}
{{--        const list = document.getElementById('notif-list');--}}
{{--        if (!notifications || notifications.length === 0) {--}}
{{--            list.innerHTML = '<p class="text-center text-gray-400 text-xs py-6">No notifications yet.</p>';--}}
{{--            return;--}}
{{--        }--}}
{{--        list.innerHTML = notifications.map(n => `--}}
{{--            <a href="${n.link || '#'}" onclick="markNotificationRead(${n.id})"--}}
{{--               class="block px-4 py-3 hover:bg-gray-50 transition ${n.is_read ? '' : 'bg-blue-50/50'}">--}}
{{--                <p class="text-sm font-medium text-gray-800">${n.title}</p>--}}
{{--                ${n.message ? `<p class="text-xs text-gray-500 mt-0.5">${n.message}</p>` : ''}--}}
{{--                <p class="text-[10px] text-gray-400 mt-1">${n.time}</p>--}}
{{--            </a>--}}
{{--        `).join('');--}}
{{--    }--}}

{{--    function fetchNotifications() {--}}
{{--        fetch("{{ route('notifications.index') }}", { headers: { 'X-Requested-With': 'XMLHttpRequest' } })--}}
{{--            .then(res => res.json())--}}
{{--            .then(data => {--}}
{{--                const badge = document.getElementById('notif-badge');--}}
{{--                if (data.unread_count > 0) {--}}
{{--                    badge.textContent = data.unread_count > 9 ? '9+' : data.unread_count;--}}
{{--                    badge.classList.remove('hidden');--}}
{{--                } else {--}}
{{--                    badge.classList.add('hidden');--}}
{{--                }--}}
{{--                renderNotifications(data.notifications);--}}
{{--            })--}}
{{--            .catch(() => {});--}}
{{--    }--}}

{{--    function markNotificationRead(id) {--}}
{{--        const tokenTag = document.querySelector('meta[name="csrf-token"]');--}}
{{--        fetch(`/notifications/${id}/read`, {--}}
{{--            method: 'POST',--}}
{{--            headers: { 'X-CSRF-TOKEN': tokenTag ? tokenTag.content : '' }--}}
{{--        }).then(fetchNotifications);--}}
{{--    }--}}

{{--    function markAllNotificationsRead() {--}}
{{--        const tokenTag = document.querySelector('meta[name="csrf-token"]');--}}
{{--        fetch("{{ route('notifications.readAll') }}", {--}}
{{--            method: 'POST',--}}
{{--            headers: { 'X-CSRF-TOKEN': tokenTag ? tokenTag.content : '' }--}}
{{--        }).then(fetchNotifications);--}}
{{--    }--}}

{{--    document.addEventListener('DOMContentLoaded', function () {--}}
{{--        fetchNotifications();--}}
{{--        setInterval(fetchNotifications, 10000);--}}
{{--    });--}}
{{--</script>--}}
