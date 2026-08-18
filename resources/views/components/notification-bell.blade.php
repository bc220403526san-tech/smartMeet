@props([])
{{-- Notification Bell + Dropdown --}}
<div class="relative">
    <button id="notif-bell-btn" onclick="toggleNotifDropdown()" type="button" class="relative block mr-5">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 text-gray-600 hover:text-blue-500 cursor-pointer">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"/>
        </svg>
        <span id="notif-badge"
              class="hidden absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-bold
                     min-w-[16px] h-4 px-[3px] rounded-full flex items-center justify-center leading-none">0</span>
    </button>
    <div id="notif-dropdown"
         class="hidden absolute right-0 mt-2 w-80 bg-white border border-gray-200 rounded-lg shadow-lg z-50">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
            <span class="text-sm font-semibold text-gray-800">Notifications</span>
            <button type="button" onclick="markAllNotificationsRead()"
                    class="text-[11px] text-blue-500 hover:underline">
                Mark all read
            </button>
        </div>
        <div id="notif-list" class="max-h-64 overflow-y-auto overscroll-contain">
            <p class="text-center text-gray-400 text-xs py-6">Loading...</p>
        </div>
    </div>
</div>
<script>
    function toggleNotifDropdown() {
        const dropdown = document.getElementById('notif-dropdown');
        if (!dropdown) return;
        dropdown.classList.toggle('hidden');
        if (!dropdown.classList.contains('hidden')) {
            fetchNotifications();
        }
    }
    document.addEventListener('click', function (e) {
        const bell = document.getElementById('notif-bell-btn');
        const dropdown = document.getElementById('notif-dropdown');
        if (bell && dropdown && !bell.contains(e.target) && !dropdown.contains(e.target)) {
            dropdown.classList.add('hidden');
        }
    });
    function renderNotifications(notifications) {
        const list = document.getElementById('notif-list');
        if (!notifications || notifications.length === 0) {
            list.innerHTML = '<p class="text-center text-gray-400 text-xs py-6">No notifications yet.</p>';
            return;
        }
        list.innerHTML = notifications.map(n => {
            const openUrl = `/notifications/${n.id}/open`;
            return `
        <a href="${openUrl}"
           class="block px-4 py-2 hover:bg-gray-50 transition ${n.is_read ? '' : 'bg-blue-50/50'}">
            <p class="text-sm font-medium text-gray-800">${n.title}</p>
            ${n.message ? `<p class="text-xs text-gray-500 mt-0.5">${n.message}</p>` : ''}
            <p class="text-[10px] text-gray-400 mt-1">${n.time}</p>
        </a>`;
        }).join('');
    }
    function fetchNotifications() {
        fetch("{{ route('notifications.index') }}", {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(data => {
                updateBadge(data.unread_count);
                renderNotifications(data.notifications);
            })
            .catch(err => {
                console.error('Notification fetch failed:', err);
                const list = document.getElementById('notif-list');
                if (list) list.innerHTML = '<p class="text-center text-red-400 text-xs py-6">Failed to load.</p>';
            });
    }
    let currentUnreadCount = 0;
    function updateBadge(count) {
        const badge = document.getElementById('notif-badge');
        if (!badge) return;
        currentUnreadCount = Math.max(0, count);
        if (currentUnreadCount > 0) {
            badge.textContent = currentUnreadCount > 9 ? '9+' : currentUnreadCount;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }
    function markNotificationRead(id) {
        const tokenTag = document.querySelector('meta[name="csrf-token"]');
        const item = document.querySelector(`[data-notif-id="${id}"]`);
        if (item && item.classList.contains('bg-blue-50/50')) {
            item.classList.remove('bg-blue-50/50');
            updateBadge(currentUnreadCount - 1);
        }
        fetch(`/notifications/${id}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': tokenTag ? tokenTag.content : '',
                'Accept': 'application/json',
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return fetchNotifications();
            })
            .catch(err => console.error('Mark read failed:', err));
    }
    function markAllNotificationsRead() {
        const tokenTag = document.querySelector('meta[name="csrf-token"]');
        updateBadge(0);
        document.querySelectorAll('#notif-list a').forEach(a => a.classList.remove('bg-blue-50/50'));
        fetch("{{ route('notifications.readAll') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': tokenTag ? tokenTag.content : '',
                'Accept': 'application/json',
            }
        })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(() => fetchNotifications())
            .catch(err => console.error('Mark all read failed:', err));
    }
    document.addEventListener('DOMContentLoaded', function () {
        fetchNotifications();
        setInterval(fetchNotifications, 10000);
    });
</script>
