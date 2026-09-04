<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Meeting Audit Logs"/>
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 min-h-screen">
        <div class="mb-5">
            <h1 class="text-xl font-semibold text-gray-900">Meeting Audit Logs</h1>
            <p class="text-sm text-gray-500 mt-1">
                Admin-only participant session, IP, device and network audit history.
            </p>
        </div>

        <form method="GET" action="{{ route('admin.audit') }}"
              class="bg-white border border-gray-100 rounded-2xl p-4 mb-4 flex flex-col md:flex-row gap-3">
            <div class="flex-1">
                <input
                    type="text"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search participant, email, meeting, IP, device..."
                    class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <div class="md:w-48">
                <input
                    type="date"
                    name="date"
                    value="{{ request('date') }}"
                    class="w-full rounded-xl border-gray-200 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                >
            </div>

            <button type="submit"
                    class="px-5 py-2.5 rounded-xl bg-gray-900 text-white text-sm font-medium hover:bg-gray-800 transition">
                Search
            </button>

            @if(request()->filled('search') || request()->filled('date'))
                <a href="{{ route('admin.audit') }}"
                   class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-700 text-sm font-medium text-center hover:bg-gray-200 transition">
                    Clear
                </a>
            @endif
        </form>

        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[1250px] w-full text-sm">
                    <thead class="bg-gray-50 text-gray-500">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium">Participant</th>
                        <th class="px-4 py-3 text-left font-medium">Meeting</th>
                        <th class="px-4 py-3 text-left font-medium">Public IP</th>
                        <th class="px-4 py-3 text-left font-medium">Device / System</th>
                        <th class="px-4 py-3 text-left font-medium">Browser</th>
                        <th class="px-4 py-3 text-left font-medium">Network</th>
                        <th class="px-4 py-3 text-left font-medium">Joined</th>
                        <th class="px-4 py-3 text-left font-medium">Left</th>
                        <th class="px-4 py-3 text-left font-medium">Duration</th>
                        <th class="px-4 py-3 text-left font-medium">Details</th>
                    </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                    @forelse($logs as $log)
                        @php
                            $duration = null;
                            if ($log->joined_at && $log->left_at) {
                                $seconds = $log->joined_at->diffInSeconds($log->left_at);
                                $hours = intdiv($seconds, 3600);
                                $minutes = intdiv($seconds % 3600, 60);
                                $secs = $seconds % 60;
                                $duration = $hours > 0
                                    ? sprintf('%dh %02dm %02ds', $hours, $minutes, $secs)
                                    : ($minutes > 0 ? sprintf('%dm %02ds', $minutes, $secs) : sprintf('%ds', $secs));
                            }
                        @endphp

                        <tr class="hover:bg-gray-50/70 align-top">
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-900">{{ $log->user?->name ?? 'Unknown user' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $log->user?->email ?? '—' }}</div>
                                <div class="text-xs text-gray-400 mt-1">User #{{ $log->user_id }}</div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-900">{{ $log->meeting?->title ?? 'Deleted meeting' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ $log->meeting?->unique_code ?? ('Meeting #' . $log->meeting_id) }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded-lg">
                                    {{ $log->public_ip ?: 'Unavailable' }}
                                </span>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-gray-900">{{ $log->device_type ?: 'Unknown' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    {{ $log->system_name ?: ($log->operating_system ?: 'Unknown system') }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-gray-900">{{ $log->browser ?: 'Unknown' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $log->operating_system ?: 'Unknown OS' }}</div>
                            </td>

                            <td class="px-4 py-4">
                                <div class="text-gray-900 uppercase">{{ $log->network_effective_type ?: ($log->network_type ?: '—') }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">
                                    @if(!is_null($log->network_downlink))
                                        {{ rtrim(rtrim(number_format($log->network_downlink, 2), '0'), '.') }} Mbps
                                    @else
                                        —
                                    @endif
                                    ·
                                    {{ !is_null($log->network_rtt) ? $log->network_rtt . ' ms' : '—' }}
                                </div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="text-gray-900">{{ $log->joined_at?->timezone(config('app.timezone'))->format('d M Y') ?? '—' }}</div>
                                <div class="text-xs text-gray-500 mt-0.5">{{ $log->joined_at?->timezone(config('app.timezone'))->format('h:i:s A') ?? '—' }}</div>
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($log->left_at)
                                    <div class="text-gray-900">{{ $log->left_at->timezone(config('app.timezone'))->format('d M Y') }}</div>
                                    <div class="text-xs text-gray-500 mt-0.5">{{ $log->left_at->timezone(config('app.timezone'))->format('h:i:s A') }}</div>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-emerald-700 bg-emerald-50 px-2 py-1 rounded-lg text-xs font-medium">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Session open
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap text-gray-700">
                                {{ $duration ?? 'In progress' }}
                            </td>

                            <td class="px-4 py-4">
                                <details class="group">
                                    <summary class="cursor-pointer text-indigo-600 hover:text-indigo-800 font-medium select-none">
                                        View
                                    </summary>
                                    <div class="mt-3 w-80 max-w-[80vw] text-xs text-gray-600 space-y-2">
                                        <div>
                                            <span class="font-medium text-gray-800">Session UUID:</span>
                                            <span class="break-all">{{ $log->session_uuid }}</span>
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-800">Local IP:</span>
                                            {{ $log->local_ip ?: 'Not exposed by browser' }}
                                        </div>
                                        <div>
                                            <span class="font-medium text-gray-800">User Agent:</span>
                                            <span class="break-all">{{ $log->user_agent ?: 'Unavailable' }}</span>
                                        </div>
                                    </div>
                                </details>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="px-6 py-14 text-center">
                                <div class="text-gray-700 font-medium">No audit logs found</div>
                                <div class="text-sm text-gray-400 mt-1">Participant room sessions will appear here.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($logs->hasPages())
                <div class="px-4 py-4 border-t border-gray-100">
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
