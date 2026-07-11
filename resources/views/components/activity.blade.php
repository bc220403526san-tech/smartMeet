@props(['activities', 'limit' => 6])

<div class="bg-gray-100 border border-blue-400 p-5 rounded-xl shadow-lg w-full">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="font-semibold">Recent Activity</h2>
        <button type="button"
                id="toggleActivitiesBtn"
                data-expanded="false"
                onclick="toggleActivities()"
                class="text-blue-500 text-sm hover:underline focus:outline-none hidden">
            View All →
        </button>
    </div>

    <!-- LIST -->
    <div id="activityList" class="space-y-3">
        @forelse($activities as $index => $activity)
            <div id="activity-{{ $activity['key'] }}"
                 class="activity-item flex items-center gap-3 p-2 hover:bg-white rounded-xl transition
                        {{ $index >= $limit ? 'hidden' : '' }}"
                {{ $index >= $limit ? 'data-extra=true' : '' }}>

                <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                    <img src="{{ $activity['image'] }}" alt="{{ $activity['name'] }}"
                         class="w-full h-full object-cover">
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm truncate">{{ $activity['name'] }}</p>
                    <p class="text-sm text-gray-500 truncate">{{ $activity['message'] }}</p>
                    <p class="text-xs text-gray-400">{{ $activity['time'] }}</p>
                </div>

                <span class="text-[10px] px-2 py-0.5 rounded-full flex-shrink-0
                    {{ $activity['type'] == 'meeting'
                        ? 'bg-blue-100 text-blue-600'
                        : 'bg-green-100 text-green-600' }}">
                    {{ ucfirst($activity['type']) }}
                </span>

                <button type="button"
                        data-key="{{ $activity['key'] }}"
                        onclick="removeActivity(this)"
                        title="Remove"
                        class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full
                               text-red-400 bg-white border border-gray-200 shadow-sm
                               hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>
        @empty
            <p id="emptyMsg" class="text-sm text-gray-400 text-center py-4">
                No recent activity found.
            </p>
        @endforelse
    </div>

</div>

@once
    <script>
        const ACTIVITY_LIMIT = {{ $limit }};
        const CSRF_TOKEN     = document.querySelector('meta[name="csrf-token"]')?.content;

        function toggleActivities() {
            const btn        = document.getElementById('toggleActivitiesBtn');
            const extraItems = document.querySelectorAll('#activityList .activity-item[data-extra]');
            const isExpanded = btn.dataset.expanded === 'true';

            extraItems.forEach(item => item.classList.toggle('hidden'));
            btn.textContent      = isExpanded ? 'View All →' : 'Show Less ↑';
            btn.dataset.expanded = isExpanded ? 'false' : 'true';

            // ✅ Scroll logic
            setTimeout(() => {
                if (!isExpanded) {
                    // View All — neeche scroll karo
                    document.getElementById('activityList').scrollIntoView({
                        behavior: 'smooth',
                        block: 'end'
                    });
                } else {
                    // Show Less — upar scroll karo
                    document.getElementById('activityList').scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }, 50);
        }

        // ─── Check Toggle Button Visibility ───────────────────────────────────────
        function checkToggleBtn() {
            const btn        = document.getElementById('toggleActivitiesBtn');
            const allItems   = document.querySelectorAll('#activityList .activity-item');
            const extraItems = document.querySelectorAll('#activityList .activity-item[data-extra]');

            if (extraItems.length > 0 && allItems.length > ACTIVITY_LIMIT) {
                btn.classList.remove('hidden');
            } else {
                btn.classList.add('hidden');
                btn.dataset.expanded = 'false';
                btn.textContent = 'View All →';
            }
        }

        // ─── Render Activities ─────────────────────────────────────────────────────
        function renderActivities(activities) {
            const list       = document.getElementById('activityList');
            const isExpanded = document.getElementById('toggleActivitiesBtn')?.dataset.expanded === 'true';

            if (!activities || activities.length === 0) {
                list.innerHTML = `
                <p id="emptyMsg" class="text-sm text-gray-400 text-center py-4">
                    No recent activity found.
                </p>`;
                checkToggleBtn();
                return;
            }

            list.innerHTML = activities.map((a, index) => {
                const isExtra   = index >= ACTIVITY_LIMIT;
                const hiddenCls = isExtra && !isExpanded ? 'hidden' : '';
                const extraAttr = isExtra ? 'data-extra="true"' : '';

                return `
            <div id="activity-${a.key}"
                 class="activity-item flex items-center gap-3 p-2 hover:bg-white rounded-xl transition ${hiddenCls}"
                 ${extraAttr}>

                <div class="w-10 h-10 rounded-full overflow-hidden flex-shrink-0">
                    <img src="${a.image}" alt="${a.name}" class="w-full h-full object-cover">
                </div>

                <div class="flex-1 min-w-0">
                    <p class="font-medium text-sm truncate">${a.name}</p>
                    <p class="text-sm text-gray-500 truncate">${a.message}</p>
                    <p class="text-xs text-gray-400">${a.time}</p>
                </div>

                <span class="text-[10px] px-2 py-0.5 rounded-full flex-shrink-0 ${
                    a.type === 'meeting'
                        ? 'bg-blue-100 text-blue-600'
                        : 'bg-green-100 text-green-600'
                }">
                    ${a.type.charAt(0).toUpperCase() + a.type.slice(1)}
                </span>

                <button type="button"
                        data-key="${a.key}"
                        onclick="removeActivity(this)"
                        title="Remove"
                        class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full
                               text-red-400 bg-white border border-gray-200 shadow-sm
                               hover:bg-red-50 hover:text-red-600 transition-all duration-200">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="2" stroke="currentColor" class="w-4 h-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

            </div>`;
            }).join('');

            checkToggleBtn();
        }

        // ─── Remove Activity ───────────────────────────────────────────────────────
        function removeActivity(btn) {
            const key = btn.dataset.key;
            const el  = document.getElementById(`activity-${key}`);

            if (el) {
                el.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                el.style.opacity    = '0';
                el.style.transform  = 'translateX(16px)';
            }

            fetch(`/admin/activities/${key}?limit=${ACTIVITY_LIMIT}`, {
                method:  'DELETE',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'Accept':       'application/json',
                    'Content-Type': 'application/json',
                },
            })
                .then(res => {
                    if (!res.ok) throw new Error('Server error: ' + res.status);
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        setTimeout(() => renderActivities(data.activities), 250);
                    } else {
                        if (el) {
                            el.style.opacity   = '1';
                            el.style.transform = 'none';
                        }
                        alert('Failed to remove activity.');
                    }
                })
                .catch(err => {
                    console.error(err);
                    if (el) {
                        el.style.opacity   = '1';
                        el.style.transform = 'none';
                    }
                    alert('Something went wrong: ' + err.message);
                });
        }

        // ─── On Page Load — Check Toggle Button ───────────────────────────────────
        document.addEventListener('DOMContentLoaded', checkToggleBtn);
    </script>
@endonce
