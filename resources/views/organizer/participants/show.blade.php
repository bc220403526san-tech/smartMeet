<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Organizer Dashboard" />
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-6 overflow-y-auto min-h-screen">

        {{-- PAGE HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="mb-1.5 flex flex-wrap items-center gap-1.5 text-xs text-slate-400">
                    <a href="{{ route('organizer.participants.index') }}"
                       class="font-medium text-blue-500 hover:underline">
                        Participants
                    </a>

                    <span>›</span>
                    <span class="text-slate-500">{{ $participant->name }}</span>
                </div>

                <h1 class="text-xl font-bold tracking-tight text-slate-900 sm:text-2xl">
                    Participant Details
                </h1>

                <p class="mt-1 text-sm text-slate-500">
                    View meeting participation, activity and limited access information.
                </p>
            </div>

            <a href="{{ route('organizer.participants.index') }}"
               class="inline-flex w-fit items-center gap-2 rounded-xl bg-white px-4 py-2.5
                      text-sm font-semibold text-slate-600 shadow-sm transition
                      hover:-translate-y-0.5 hover:text-blue-600 hover:shadow-md">
                <i class="fa-solid fa-arrow-left text-xs"></i>
                Back to Participants
            </a>
        </div>

        {{-- MAIN PROFILE GRID --}}
        <div class="grid grid-cols-1 gap-5 xl:grid-cols-3">

            {{-- LEFT PROFILE CARD --}}
            <section class="rounded-3xl bg-white p-6 shadow-sm xl:col-span-1">
                <div class="text-center">
                    @php
                        $nameParts = preg_split('/\s+/', trim($participant->name ?? 'User'));
                        $initials = '';
                        foreach (array_slice($nameParts, 0, 2) as $namePart) {
                            $initials .= strtoupper(substr($namePart, 0, 1));
                        }
                    @endphp

                    <div class="relative mx-auto w-fit">
                        @if(!empty($participant->image_url))
                            <img src="{{ $participant->image_url }}"
                                 alt="{{ $participant->name }}"
                                 class="h-24 w-24 rounded-full object-cover ring-4 ring-blue-50 shadow-md">
                        @else
                            <div class="flex h-24 w-24 items-center justify-center rounded-full
                                        bg-gradient-to-br from-blue-500 to-indigo-600
                                        text-2xl font-bold text-white ring-4 ring-blue-50 shadow-md">
                                {{ $initials }}
                            </div>
                        @endif

                        @if($pStats['isActiveNow'])
                            <span class="absolute bottom-1 right-1 h-4 w-4 rounded-full
                                         bg-emerald-500 ring-4 ring-white"></span>
                        @else
                            <span class="absolute bottom-1 right-1 h-4 w-4 rounded-full
                                         bg-slate-300 ring-4 ring-white"></span>
                        @endif
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-slate-900">
                        {{ $participant->name }}
                    </h2>

                    <p class="mt-1 break-all text-sm text-slate-500">
                        {{ $participant->email }}
                    </p>

                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-[10px]
                                     font-bold uppercase tracking-wider text-blue-600">
                            Participant
                        </span>

                        @if($pStats['isActiveNow'])
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50
                                         px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-600">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                Active Now
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100
                                         px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-500">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                Inactive
                            </span>
                        @endif

                        <span class="rounded-full bg-indigo-50 px-3 py-1.5 text-[10px]
                                     font-bold uppercase tracking-wider text-indigo-600">
                            {{ $pStats['label'] }}
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-3 gap-2">
                    <div class="rounded-2xl bg-blue-50 p-3 text-center">
                        <p class="text-xl font-bold text-blue-600">
                            {{ $pStats['totalMeetings'] }}
                        </p>
                        <p class="mt-1 text-[9px] font-semibold uppercase tracking-wide text-slate-500">
                            Meetings
                        </p>
                    </div>

                    <div class="rounded-2xl bg-emerald-50 p-3 text-center">
                        <p class="text-xl font-bold text-emerald-600">
                            {{ $pStats['attended'] }}
                        </p>
                        <p class="mt-1 text-[9px] font-semibold uppercase tracking-wide text-slate-500">
                            Attended
                        </p>
                    </div>

                    <div class="rounded-2xl bg-indigo-50 p-3 text-center">
                        <p class="text-xl font-bold text-indigo-600">
                            {{ $pStats['attendanceRate'] }}%
                        </p>
                        <p class="mt-1 text-[9px] font-semibold uppercase tracking-wide text-slate-500">
                            Attendance
                        </p>
                    </div>
                </div>

                <div class="mt-5 space-y-3">
                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-blue-500 shadow-sm">
                                <i class="fa-regular fa-calendar text-xs"></i>
                            </div>

                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                    Joined Platform
                                </p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-700">
                                    {{ $pStats['joinedOn'] }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-2xl bg-slate-50 p-4">
                        <div class="flex items-center gap-3">
                            <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-white text-indigo-500 shadow-sm">
                                <i class="fa-regular fa-clock text-xs"></i>
                            </div>

                            <div>
                                <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                    Last Meeting Activity
                                </p>
                                <p class="mt-0.5 text-sm font-semibold text-slate-700">
                                    {{ $pStats['lastActive'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- RIGHT --}}
            <div class="space-y-5 xl:col-span-2">

                {{-- LIMITED ACCESS DETAILS --}}
                <section class="rounded-3xl bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                                <i class="fa-solid fa-shield-halved text-sm"></i>
                            </div>

                            <div>
                                <h3 class="text-base font-semibold text-slate-900">
                                    Access Information
                                </h3>
                                <p class="mt-0.5 text-xs text-slate-400">
                                    Limited technical information available to the meeting organizer.
                                </p>
                            </div>
                        </div>

                        <span class="w-fit rounded-full bg-blue-50 px-3 py-1.5 text-[10px]
                                     font-bold uppercase tracking-wider text-blue-600">
                            Limited Access
                        </span>
                    </div>

                    <div class="mt-5 grid grid-cols-1 gap-3 sm:grid-cols-2">
                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-blue-500 shadow-sm">
                                    <i class="fa-solid fa-network-wired text-xs"></i>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                        Last Known IP
                                    </p>
                                    <p class="mt-1 break-all text-sm font-semibold text-slate-700">
                                        {{ $accessInfo['ip'] }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-indigo-500 shadow-sm">
                                    <i class="fa-regular fa-window-maximize text-xs"></i>
                                </div>

                                <div class="min-w-0">
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                        Browser
                                    </p>
                                    <p class="mt-1 text-sm font-semibold text-slate-700">
                                        {{ $accessInfo['browser'] }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-cyan-500 shadow-sm">
                                    <i class="fa-solid fa-display text-xs"></i>
                                </div>

                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                        Device / Platform
                                    </p>
                                    <p class="mt-1 text-sm font-semibold text-slate-700">
                                        {{ $accessInfo['device'] }} · {{ $accessInfo['platform'] }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="rounded-2xl bg-slate-50 p-4">
                            <div class="flex items-start gap-3">
                                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl bg-white text-emerald-500 shadow-sm">
                                    <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                </div>

                                <div>
                                    <p class="text-[10px] font-semibold uppercase tracking-wider text-slate-400">
                                        Session Last Active
                                    </p>
                                    <p class="mt-1 text-sm font-semibold text-slate-700">
                                        {{ $accessInfo['lastActive'] }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 rounded-2xl bg-blue-50 px-4 py-3 text-xs leading-5 text-blue-700">
                        <i class="fa-solid fa-circle-info mr-1.5"></i>
                        Session IDs, authentication tokens, passwords and raw session data are not exposed to organizers.
                    </div>
                </section>

                {{-- RECENT MEETINGS --}}
                <section class="rounded-3xl bg-white p-5 shadow-sm sm:p-6">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                                <i class="fa-solid fa-video text-sm"></i>
                            </div>

                            <div>
                                <h3 class="text-base font-semibold text-slate-900">
                                    Recent Meeting History
                                </h3>
                                <p class="mt-0.5 text-xs text-slate-400">
                                    Meetings with you as the organizer.
                                </p>
                            </div>
                        </div>

                        <span class="rounded-xl bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-500">
                            {{ $pStats['totalMeetings'] }} total
                        </span>
                    </div>

                    <div class="mt-5 space-y-3">
                        @forelse($participant->joinedMeetings->take(6) as $record)
                            @php
                                $meetingItem = $record->meeting;
                                $attended = !is_null($record->joined_at) || !is_null($record->left_at);
                            @endphp

                            <div class="flex flex-col gap-3 rounded-2xl bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between">
                                <div class="min-w-0">
                                    <p class="truncate text-sm font-semibold text-slate-800">
                                        {{ $meetingItem?->title ?? 'Meeting' }}
                                    </p>

                                    <div class="mt-1 flex flex-wrap items-center gap-x-3 gap-y-1 text-[11px] text-slate-400">
                                        @if($meetingItem && $meetingItem->date)
                                            <span>
                                                <i class="fa-regular fa-calendar mr-1"></i>
                                                {{ \Carbon\Carbon::parse($meetingItem->date)->format('M d, Y') }}
                                            </span>
                                        @endif

                                        @if($meetingItem && $meetingItem->time)
                                            <span>
                                                <i class="fa-regular fa-clock mr-1"></i>
                                                {{ \Carbon\Carbon::parse($meetingItem->time)->format('h:i A') }}
                                            </span>
                                        @endif

                                        @if($meetingItem && $meetingItem->duration)
                                            <span>{{ $meetingItem->duration }} min</span>
                                        @endif
                                    </div>
                                </div>

                                @if($attended)
                                    <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-emerald-50
                                                 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wide text-emerald-600">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                        Attended
                                    </span>
                                @elseif($record->status === 'declined')
                                    <span class="inline-flex w-fit rounded-full bg-red-50 px-3 py-1.5
                                                 text-[10px] font-bold uppercase tracking-wide text-red-500">
                                        Declined
                                    </span>
                                @else
                                    <span class="inline-flex w-fit rounded-full bg-amber-50 px-3 py-1.5
                                                 text-[10px] font-bold uppercase tracking-wide text-amber-600">
                                        Invited
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="rounded-2xl bg-slate-50 py-10 text-center text-sm text-slate-400">
                                No meeting history available.
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        </div>
    </div>
</x-layouts.app>
