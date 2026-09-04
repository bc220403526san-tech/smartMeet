
<x-slot name="header">
    <div class="flex items-center justify-between gap-4 w-full">
        <x-header.page-title title="Meeting Audit Logs"/>

        <form method="GET"
              action="{{ route('admin.audit') }}"
              class="w-full max-w-md">
            <div class="relative">
                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>

                <input
                    type="search"
                    name="search"
                    value="{{ request('search') }}"
                    placeholder="Search participant, email, meeting, IP, device..."
                    autocomplete="off"
                    class="w-full h-10 pl-11 pr-4 rounded-xl border border-gray-200 bg-gray-50
                               text-sm text-gray-700 placeholder:text-gray-400
                               focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                >
            </div>
        </form>
    </div>
</x-slot>

<div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 min-h-screen">

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-lg font-semibold text-gray-900">
                        Participant Sessions
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        Recorded participant access and session information.
                    </p>
                </div>

                <div class="text-sm text-gray-500 whitespace-nowrap">
                    {{ $logs->total() }}
                    {{ \Illuminate\Support\Str::plural('record', $logs->total()) }}
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-[1120px] w-full text-sm">
                <thead>
                <tr class="bg-gray-50 border-b border-gray-200">
                    <th class="px-5 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Participant
                    </th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Meeting
                    </th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Public IP
                    </th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Device / System
                    </th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Browser
                    </th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Network
                    </th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Joined
                    </th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Left
                    </th>
                    <th class="px-4 py-3.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">
                        Duration
                    </th>
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
                                : ($minutes > 0
                                    ? sprintf('%dm %02ds', $minutes, $secs)
                                    : sprintf('%ds', $secs));
                        }

                        $participantName = $log->user?->name ?? 'Unknown user';

                        $initials = collect(preg_split('/\s+/', trim($participantName)))
                            ->filter()
                            ->take(2)
                            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
                            ->implode('');
                    @endphp

                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 shrink-0 rounded-full bg-blue-50 border border-blue-200
                                                    text-blue-600 flex items-center justify-center text-xs font-semibold">
                                    {{ $initials ?: '?' }}
                                </div>

                                <div class="min-w-0">
                                    <div class="font-semibold text-gray-900">
                                        {{ $participantName }}
                                    </div>
                                    <div class="text-xs text-gray-500 mt-0.5">
                                        {{ $log->user?->email ?? '—' }}
                                    </div>
                                    <div class="text-xs text-gray-400 mt-0.5">
                                        User #{{ $log->user_id }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <div class="font-semibold text-gray-800">
                                {{ $log->meeting?->title ?? 'Deleted meeting' }}
                            </div>
                            <div class="text-xs text-blue-500 mt-1">
                                {{ $log->meeting?->unique_code ?? ('Meeting #' . $log->meeting_id) }}
                            </div>
                        </td>

                        <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 font-mono text-xs text-gray-700
                                                 bg-gray-100 border border-gray-200 px-2.5 py-1.5 rounded-lg">
                                        <i class="fa-solid fa-globe text-[10px] text-gray-400"></i>
                                        {{ $log->public_ip ?: 'Unavailable' }}
                                    </span>
                        </td>

                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-800 flex items-center gap-1.5">
                                <i class="fa-solid fa-desktop text-xs text-blue-500"></i>
                                {{ $log->device_type ?: 'Unknown' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $log->system_name ?: ($log->operating_system ?: 'Unknown system') }}
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            <div class="font-medium text-gray-800">
                                {{ $log->browser ?: 'Unknown' }}
                            </div>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $log->operating_system ?: 'Unknown OS' }}
                            </div>
                        </td>

                        <td class="px-4 py-4">
                            @if($log->network_effective_type || $log->network_type)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                     bg-emerald-50 border border-emerald-100
                                                     text-emerald-700 text-xs font-semibold uppercase">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                            {{ $log->network_effective_type ?: $log->network_type }}
                                        </span>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif

                            <div class="text-xs text-gray-500 mt-1.5 whitespace-nowrap">
                                @if(!is_null($log->network_downlink))
                                    {{ rtrim(rtrim(number_format($log->network_downlink, 2), '0'), '.') }} Mbps
                                @else
                                    —
                                @endif

                                <span class="text-gray-300 mx-1">•</span>

                                {{ !is_null($log->network_rtt) ? $log->network_rtt . ' ms' : '—' }}
                            </div>
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-800">
                                {{ $log->joined_at?->timezone(config('app.timezone'))->format('d M Y') ?? '—' }}
                            </div>
                            <div class="text-xs text-blue-600 mt-1">
                                {{ $log->joined_at?->timezone(config('app.timezone'))->format('h:i:s A') ?? '—' }}
                            </div>
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($log->left_at)
                                <div class="font-medium text-gray-800">
                                    {{ $log->left_at->timezone(config('app.timezone'))->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $log->left_at->timezone(config('app.timezone'))->format('h:i:s A') }}
                                </div>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                     bg-emerald-50 border border-emerald-100
                                                     text-emerald-700 text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            Session open
                                        </span>
                            @endif
                        </td>

                        <td class="px-4 py-4 whitespace-nowrap">
                            @if($duration)
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg
                                                     bg-gray-50 border border-gray-200 text-gray-700 text-xs font-medium">
                                            <i class="fa-regular fa-clock text-gray-400"></i>
                                            {{ $duration }}
                                        </span>
                            @else
                                <span class="text-xs font-medium text-emerald-600">
                                            In progress
                                        </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-6 py-14 text-center">
                            <div class="text-gray-800 font-semibold">
                                No audit logs found
                            </div>
                            <div class="text-sm text-gray-400 mt-1">
                                Participant room sessions will appear here.
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-5 py-4 border-t border-gray-100 bg-gray-50/60
                        flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <p class="text-xs text-gray-500">
                @if($logs->total() > 0)
                    Showing
                    <span class="font-semibold text-gray-700">{{ $logs->firstItem() }}</span>
                    to
                    <span class="font-semibold text-gray-700">{{ $logs->lastItem() }}</span>
                    of
                    <span class="font-semibold text-gray-700">{{ $logs->total() }}</span>
                    results
                @else
                    No results
                @endif
            </p>

            @if($logs->hasPages())
                <div>
                    {{ $logs->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
</x-layouts.app>
