<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Meeting Audit Logs"/>
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 min-h-screen">

        {{-- Compact header + search + filter --}}
        <div class="mb-4 rounded-2xl border border-blue-100 bg-gradient-to-r from-blue-50 via-white to-indigo-50 shadow-sm">
            <div class="p-4 lg:p-5 flex flex-col xl:flex-row xl:items-center gap-4">
                <div class="flex items-center gap-3 min-w-0 xl:w-[34%]">
                    <div class="w-10 h-10 shrink-0 rounded-xl bg-blue-600 text-white flex items-center justify-center shadow-sm">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
                    </div>

                    <div class="min-w-0">
                        <h1 class="text-lg font-semibold text-gray-900">Meeting Audit Logs</h1>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Participant session, IP, device and network history.
                        </p>
                    </div>
                </div>

                <form method="GET"
                      action="{{ route('admin.audit') }}"
                      class="flex-1 grid grid-cols-1 sm:grid-cols-[minmax(0,1fr)_180px_auto_auto] gap-2 items-center">

                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                        <input
                            type="text"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Search participant, email, meeting, IP, device..."
                            class="w-full h-10 pl-10 pr-3 rounded-xl border border-gray-200 bg-white text-sm text-gray-700
                                   shadow-sm placeholder:text-gray-400 focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                        >
                    </div>

                    <input
                        type="date"
                        name="date"
                        value="{{ request('date') }}"
                        class="w-full h-10 rounded-xl border border-gray-200 bg-white text-sm text-gray-700
                               shadow-sm focus:border-blue-400 focus:ring-2 focus:ring-blue-100"
                    >

                    <button type="submit"
                            class="h-10 px-4 inline-flex items-center justify-center gap-2 rounded-xl bg-blue-600 text-white
                                   text-sm font-semibold shadow-sm hover:bg-blue-700 transition whitespace-nowrap">
                        <i class="fa-solid fa-filter text-xs"></i>
                        Apply
                    </button>

                    @if(request()->filled('search') || request()->filled('date'))
                        <a href="{{ route('admin.audit') }}"
                           class="h-10 px-4 inline-flex items-center justify-center rounded-xl border border-gray-200
                                  bg-white text-gray-600 text-sm font-medium hover:bg-gray-50 transition whitespace-nowrap">
                            Clear
                        </a>
                    @endif
                </form>
            </div>
        </div>

        {{-- Audit table --}}
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-3.5 border-b border-gray-100 bg-gradient-to-r from-white to-blue-50/50
                        flex items-center justify-between gap-3">
                <div>
                    <h2 class="text-base font-semibold text-gray-900 flex items-center gap-2">
                        <span class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 inline-flex items-center justify-center">
                            <i class="fa-solid fa-list-check text-xs"></i>
                        </span>
                        Participant Sessions
                    </h2>
                    <p class="text-xs text-gray-500 mt-0.5 sm:ml-10">
                        Recorded participant access and session information.
                    </p>
                </div>

                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full
                            bg-blue-50 border border-blue-100 text-xs font-semibold text-blue-700 shrink-0">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    {{ $logs->total() }} {{ \Illuminate\Support\Str::plural('record', $logs->total()) }}
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-[1120px] w-full text-sm">
                    <thead>
                    <tr class="bg-slate-50 border-b border-gray-200">
                        <th class="px-5 py-3 text-left text-[11px] uppercase tracking-wider font-semibold text-gray-500">Participant</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wider font-semibold text-gray-500">Meeting</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wider font-semibold text-gray-500">Public IP</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wider font-semibold text-gray-500">Device / System</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wider font-semibold text-gray-500">Browser</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wider font-semibold text-gray-500">Network</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wider font-semibold text-gray-500">Joined</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wider font-semibold text-gray-500">Left</th>
                        <th class="px-4 py-3 text-left text-[11px] uppercase tracking-wider font-semibold text-gray-500">Duration</th>
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

                        <tr class="hover:bg-blue-50/30 transition-colors align-middle">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 shrink-0 rounded-full
                                                bg-gradient-to-br from-blue-100 to-indigo-100
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

                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-800">
                                    {{ $log->meeting?->title ?? 'Deleted meeting' }}
                                </div>
                                <div class="text-xs text-indigo-500 mt-1">
                                    {{ $log->meeting?->unique_code ?? ('Meeting #' . $log->meeting_id) }}
                                </div>
                            </td>

                            <td class="px-4 py-4">
                                <span class="inline-flex items-center gap-1.5 font-mono text-xs text-slate-700
                                             bg-slate-100 border border-slate-200 px-2.5 py-1.5 rounded-lg">
                                    <i class="fa-solid fa-globe text-[10px] text-slate-400"></i>
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
                                <div class="text-xs text-blue-600 mt-1 font-medium">
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
                                    <span class="inline-flex items-center gap-1.5 text-emerald-700
                                                 bg-emerald-50 border border-emerald-100
                                                 px-2.5 py-1.5 rounded-full text-xs font-semibold">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        Session open
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-4 whitespace-nowrap">
                                @if($duration)
                                    <span class="inline-flex items-center gap-1.5 text-gray-700 bg-gray-50
                                                 border border-gray-200 px-2.5 py-1.5 rounded-lg
                                                 text-xs font-medium">
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
                                <div class="text-gray-800 font-semibold">No audit logs found</div>
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
