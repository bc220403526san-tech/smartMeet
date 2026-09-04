<x-layouts.app>
    <x-slot name="header">
        <x-header.search-bar placeholder="Search participant, email, meeting, IP, device..." />
    </x-slot>

    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 min-h-screen">

        {{-- Section heading outside table --}}
        <div class="mb-3 px-1 sm:px-2 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-600 to-indigo-600
                            text-white flex items-center justify-center shadow-sm">
                    <i class="fa-solid fa-shield-halved text-sm"></i>
                </div>

                <div>
                    <h1 class="text-lg sm:text-xl font-semibold text-gray-900">
                        Participant Sessions
                    </h1>
                    <p class="text-sm text-gray-500 mt-0.5">
                        Recorded participant access, device, network and session activity.
                    </p>
                </div>
            </div>

            <div class="inline-flex items-center self-start sm:self-auto gap-2 px-3 py-1.5 rounded-full
                        border border-blue-100 bg-blue-50 text-blue-700 text-xs font-semibold">
                <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                {{ $logs->total() }}
                {{ \Illuminate\Support\Str::plural('record', $logs->total()) }}
            </div>
        </div>

        {{-- Table card --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-[1180px] w-full text-sm">
                    <thead>
                    <tr class="bg-blue-50/80 border-b border-blue-100">
                        <th class="px-5 py-3.5 text-left text-[11px] font-semibold text-blue-700 uppercase tracking-wider">
                            Participant
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-blue-700 uppercase tracking-wider">
                            Meeting
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-blue-700 uppercase tracking-wider">
                            Public IP
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-blue-700 uppercase tracking-wider">
                            Device / System
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-blue-700 uppercase tracking-wider">
                            Browser
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-blue-700 uppercase tracking-wider">
                            Network
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-blue-700 uppercase tracking-wider">
                            Joined
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-blue-700 uppercase tracking-wider">
                            Left
                        </th>
                        <th class="px-4 py-3.5 text-left text-[11px] font-semibold text-blue-700 uppercase tracking-wider">
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

                        <tr class="group hover:bg-blue-50/35 transition-colors duration-150 align-middle">

                            {{-- Participant --}}
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 shrink-0 rounded-full
                                                    bg-gradient-to-br from-blue-50 to-indigo-100
                                                    border border-blue-200 text-blue-700
                                                    flex items-center justify-center text-xs font-bold">
                                        {{ $initials ?: '?' }}
                                    </div>

                                    <div class="min-w-0">
                                        <div class="font-semibold text-gray-900">
                                            {{ $participantName }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            {{ $log->user?->email ?? '—' }}
                                        </div>
                                        <div class="text-[11px] text-gray-400 mt-0.5">
                                            User #{{ $log->user_id }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Meeting --}}
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-800">
                                    {{ $log->meeting?->title ?? 'Deleted meeting' }}
                                </div>
                                <div class="text-xs text-blue-500 mt-1">
                                    {{ $log->meeting?->unique_code ?? ('Meeting #' . $log->meeting_id) }}
                                </div>
                            </td>

                            {{-- IP --}}
                            <td class="px-4 py-4">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg
                                                 bg-slate-100 border border-slate-200 text-slate-700
                                                 font-mono text-xs">
                                        <i class="fa-solid fa-globe text-[10px] text-slate-400"></i>
                                        {{ $log->public_ip ?: 'Unavailable' }}
                                    </span>
                            </td>

                            {{-- Device --}}
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-800 flex items-center gap-1.5">
                                    <i class="fa-solid fa-desktop text-xs text-blue-500"></i>
                                    {{ $log->device_type ?: 'Unknown' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $log->system_name ?: ($log->operating_system ?: 'Unknown system') }}
                                </div>
                            </td>

                            {{-- Browser --}}
                            <td class="px-4 py-4">
                                <div class="font-medium text-gray-800">
                                    {{ $log->browser ?: 'Unknown' }}
                                </div>
                                <div class="text-xs text-gray-500 mt-1">
                                    {{ $log->operating_system ?: 'Unknown OS' }}
                                </div>
                            </td>

                            {{-- Network --}}
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

                            {{-- Joined --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                <div class="font-medium text-gray-800">
                                    {{ $log->joined_at?->timezone(config('app.timezone'))->format('d M Y') ?? '—' }}
                                </div>
                                <div class="text-xs text-blue-600 mt-1 font-medium">
                                    {{ $log->joined_at?->timezone(config('app.timezone'))->format('h:i:s A') ?? '—' }}
                                </div>
                            </td>

                            {{-- Left --}}
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

                            {{-- Duration --}}
                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($duration)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg
                                                     bg-gray-50 border border-gray-200 text-gray-700 text-xs font-medium">
                                            <i class="fa-regular fa-clock text-gray-400"></i>
                                            {{ $duration }}
                                        </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full
                                                     bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                            In progress
                                        </span>
                                @endif
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="w-11 h-11 mx-auto rounded-xl bg-blue-50
                                                text-blue-500 flex items-center justify-center mb-3">
                                    <i class="fa-solid fa-shield-halved"></i>
                                </div>
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

            {{-- Footer / pagination --}}
            <div class="px-5 sm:px-6 py-4 border-t border-gray-100 bg-gray-50/70
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
