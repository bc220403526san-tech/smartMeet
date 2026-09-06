<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Organizer Dashboard" />
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-6 overflow-y-auto min-h-screen">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-500">
                <a href="{{ route('organizer.participants.index') }}"
                   class="flex items-center gap-1 transition hover:text-blue-600">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to Participants
                </a>
                <span>/</span>
                <span class="font-medium text-gray-700">Participant Details</span>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-8 xl:grid-cols-3">
            {{-- PROFILE --}}
            <section class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-blue-50 xl:col-span-1">
                @php
                    $nameParts = preg_split('/\s+/', trim($participant->name ?? 'User'));
                    $initials = '';
                    foreach (array_slice($nameParts, 0, 2) as $namePart) {
                        $initials .= strtoupper(substr($namePart, 0, 1));
                    }
                @endphp

                <div class="text-center">
                    <div class="relative mx-auto w-fit">
                        @if(!empty($participant->image_url))
                            <img src="{{ $participant->image_url }}"
                                 alt="{{ $participant->name }}"
                                 class="h-24 w-24 rounded-full object-cover shadow-md">
                        @else
                            <div class="flex h-24 w-24 items-center justify-center rounded-full bg-blue-600 text-2xl font-bold text-white shadow-md">
                                {{ $initials }}
                            </div>
                        @endif

                        <span class="absolute bottom-1.5 right-1.5 h-3.5 w-3.5 rounded-full border-2 border-white {{ $pStats['isActiveNow'] ? 'bg-emerald-500' : 'bg-gray-300' }}"></span>
                    </div>

                    <h2 class="mt-4 text-xl font-bold text-gray-900">{{ $participant->name }}</h2>
                    <p class="mt-1 break-all text-sm text-gray-500">{{ $participant->email }}</p>

                    <div class="mt-4 flex flex-wrap justify-center gap-2">
                        <span class="rounded-full bg-blue-50 px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-blue-600">
                            Participant
                        </span>
                        <span class="rounded-full px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider {{ $pStats['isActiveNow'] ? 'bg-emerald-50 text-emerald-600' : 'bg-gray-100 text-gray-500' }}">
                            {{ $pStats['isActiveNow'] ? 'Active Now' : 'Inactive' }}
                        </span>
                    </div>
                </div>

                <div class="mt-7 grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div class="rounded-2xl bg-blue-50 p-4 text-center shadow-sm ring-1 ring-blue-100">
                        <p class="text-xl font-bold text-blue-600">{{ $pStats['totalMeetings'] }}</p>
                        <p class="mt-1 text-[9px] font-semibold uppercase tracking-wide text-gray-500">Meetings</p>
                    </div>
                    <div class="rounded-2xl bg-emerald-50 p-4 text-center shadow-sm ring-1 ring-emerald-100">
                        <p class="text-xl font-bold text-emerald-600">{{ $pStats['attended'] }}</p>
                        <p class="mt-1 text-[9px] font-semibold uppercase tracking-wide text-gray-500">Attended</p>
                    </div>
                    <div class="rounded-2xl bg-indigo-50 p-4 text-center shadow-sm ring-1 ring-indigo-100">
                        <p class="text-xl font-bold text-indigo-600">{{ $pStats['attendanceRate'] }}%</p>
                        <p class="mt-1 text-[9px] font-semibold uppercase tracking-wide text-gray-500">Attendance</p>
                    </div>
                </div>

                <div class="mt-7 space-y-5">
                    <div class="rounded-2xl bg-blue-50/70 p-5 shadow-sm ring-1 ring-blue-100">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Joined Platform</p>
                        <p class="mt-1 text-sm font-semibold text-gray-700">{{ $pStats['joinedOn'] }}</p>
                    </div>
                    <div class="rounded-2xl bg-amber-50/70 p-5 shadow-sm ring-1 ring-amber-100">
                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Last Meeting Activity</p>
                        <p class="mt-1 text-sm font-semibold text-gray-700">{{ $pStats['lastActive'] }}</p>
                    </div>
                </div>
            </section>

            {{-- MEETING HISTORY --}}
            <section class="self-start rounded-2xl bg-white p-5 shadow-sm ring-1 ring-blue-50 sm:p-6 xl:col-span-2">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Meeting History</h3>
                        <p class="mt-1 text-xs text-gray-400">Participant attendance details for your meetings.</p>
                    </div>
                    <span class="w-fit rounded-xl bg-blue-50 px-3 py-2 text-xs font-semibold text-blue-600">
                        {{ $pStats['totalMeetings'] }} total
                    </span>
                </div>

                @php
                    $meetingHistoryCount = $participant->joinedMeetings->count();
                @endphp

                <div class="mt-6 space-y-5 {{ $meetingHistoryCount > 3 ? 'max-h-[520px] overflow-y-auto pr-3' : '' }}">
                    @forelse($participant->joinedMeetings->take(10) as $record)
                        @php
                            $meetingItem = $record->meeting;
                            $joinedAt = $record->joined_at;
                            $leftAt = $record->left_at;
                            $hasAttended = !is_null($joinedAt) || !is_null($leftAt);
                        @endphp

                        <div class="rounded-2xl bg-blue-50/40 p-5 shadow-sm ring-1 ring-blue-100">
                            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h4 class="truncate text-base font-bold text-gray-900">
                                            {{ $meetingItem ? $meetingItem->title : 'Meeting' }}
                                        </h4>

                                        @if($hasAttended)
                                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-emerald-600">
                                                Attended
                                            </span>
                                        @else
                                            <span class="rounded-full bg-amber-50 px-2.5 py-1 text-[10px] font-bold uppercase tracking-wide text-amber-600">
                                                Not Joined
                                            </span>
                                        @endif
                                    </div>

                                    @if($meetingItem && $meetingItem->date)
                                        <p class="mt-1 text-xs text-gray-400">
                                            <i class="fa-regular fa-calendar mr-1"></i>
                                            {{ \Carbon\Carbon::parse($meetingItem->date)->format('M d, Y') }}
                                            @if($meetingItem->time)
                                                <span class="mx-1">•</span>
                                                {{ \Carbon\Carbon::parse($meetingItem->time)->format('h:i A') }}
                                            @endif
                                        </p>
                                    @endif
                                </div>

                                <div class="grid w-full grid-cols-1 gap-4 sm:grid-cols-3 lg:w-auto lg:min-w-[520px]">
                                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-blue-100">
                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Meeting Duration</p>
                                        <p class="mt-1 text-sm font-bold text-gray-700">
                                            {{ $meetingItem && $meetingItem->duration ? $meetingItem->duration . ' min' : '—' }}
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-emerald-100">
                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Joined</p>
                                        <p class="mt-1 text-sm font-bold text-gray-700">
                                            {{ $joinedAt ? \Carbon\Carbon::parse($joinedAt)->format('h:i A') : 'Not joined' }}
                                        </p>
                                    </div>

                                    <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-indigo-100">
                                        <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">Left</p>
                                        <p class="mt-1 text-sm font-bold text-gray-700">
                                            {{ $leftAt ? \Carbon\Carbon::parse($leftAt)->format('h:i A') : '—' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="rounded-2xl bg-gray-50 py-10 text-center ring-1 ring-gray-100">
                            <i class="fa-regular fa-calendar-xmark text-2xl text-gray-300"></i>
                            <p class="mt-2 text-sm text-gray-400">No meeting history available.</p>
                        </div>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</x-layouts.app>
