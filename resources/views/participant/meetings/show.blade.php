<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Meeting Details" />
    </x-slot>
    <x-success />
    <x-error />

    @php
        $organizer = $meeting->organizer;
        $orgInitials = $organizer
            ? mb_strtoupper(mb_substr($organizer->name, 0, 1) . mb_substr(strrchr($organizer->name, ' ') ?: '', 1, 1))
            : '?';
    @endphp

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">

        <!-- PAGE HEADER (same style as Meetings index) -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-2">
            <div>
                <a href="{{ route('participant.meetings.index') }}"
                   class="inline-flex items-center gap-1.5 text-sm text-blue-600 hover:text-blue-700 font-medium transition mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                    </svg>
                    Back to Meetings
                </a>
                <h1 class="text-2xl font-bold text-gray-800">Meeting Details</h1>
                <p class="text-sm text-gray-500 mt-1">
                    Review meeting logistics, participants and agenda.
                </p>
            </div>
            @if($meeting->status === 'active')
                <a href="{{ route('participant.meetings.attend', $meeting) }}"
                   class="mt-3 sm:mt-0 inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold shadow-sm hover:shadow-md transition-all">
                    <i class="fa-solid fa-video"></i>
                    Join Now
                </a>
            @endif
        </div>

        <!-- ====== 2-CARD GRID ====== -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

            <!-- ============ CARD 1 : MEETING INFO ============ -->
            <div class="lg:col-span-2 bg-white rounded-2xl p-5 border border-blue-100 shadow-sm hover:shadow-md transition-all duration-300">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <span class="inline-flex items-center gap-1.5 bg-blue-50 text-blue-600 text-[11px] font-semibold px-3 py-1 rounded-full">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 {{ $meeting->status === 'active' ? 'animate-pulse' : '' }}"></span>
                            {{ strtoupper($meeting->status) }}
                        </span>
                        <h2 class="text-xl font-bold text-gray-800 mt-3 leading-snug">
                            {{ $meeting->title }}
                        </h2>
                        <p class="text-xs text-gray-400 mt-1">Meeting ID · SM-{{ $meeting->id }}</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-500/10 rounded-2xl flex items-center justify-center backdrop-blur-sm border border-blue-200/50 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-6 h-6 text-blue-600">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                    </div>
                </div>

                <!-- Info strip: date / time / duration -->
                <div class="grid grid-cols-3 divide-x divide-gray-100 border border-gray-100 rounded-xl mt-4">
                    <div class="px-4 py-3 text-center">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Date</p>
                        <p class="text-sm font-bold text-gray-800">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</p>
                    </div>
                    <div class="px-4 py-3 text-center">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Time</p>
                        <p class="text-sm font-bold text-gray-800">{{ $startTime->format('g:i A') }}<span class="text-gray-300 mx-0.5">–</span>{{ $endTime->format('g:i A') }}</p>
                    </div>
                    <div class="px-4 py-3 text-center">
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wide mb-1">Duration</p>
                        <p class="text-sm font-bold text-gray-800">{{ $meeting->duration }} mins</p>
                    </div>
                </div>

                <!-- Description -->
                <div class="mt-4">
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                        <i class="fa-solid fa-align-left text-gray-300"></i>
                        Description
                    </h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ $meeting->description ?? 'No description provided for this meeting.' }}
                    </p>
                </div>
            </div>

            <!-- ============ CARD 2 : ORGANIZER ============ -->
            <div class="bg-white rounded-2xl p-5 border border-blue-100 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-9 h-9 bg-blue-500/10 rounded-xl flex items-center justify-center border border-blue-200/50 shrink-0">
                        <i class="fa-solid fa-user-tie text-blue-600 text-sm"></i>
                    </div>
                    <h4 class="text-sm font-semibold text-gray-700">Organizer</h4>
                </div>

                <div class="flex-1 flex flex-col items-center justify-center text-center">
                    <div class="w-20 h-20 rounded-2xl overflow-hidden bg-blue-50 flex items-center justify-center text-blue-600 font-bold text-xl border border-blue-100">
                        @if($organizer && $organizer->avatar)
                            <img src="{{ Storage::url($organizer->avatar) }}" class="w-full h-full object-cover" alt="{{ $organizer->name }}">
                        @else
                            {{ $orgInitials }}
                        @endif
                    </div>
                    <h3 class="font-bold mt-4 text-gray-800">{{ $organizer->name ?? 'Unknown' }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ $organizer->email ?? '' }}</p>
                    @if($organizer?->role)
                        <span class="mt-3 inline-flex items-center gap-1 bg-blue-50 text-blue-600 text-[10px] font-semibold px-3 py-1 rounded-full uppercase tracking-wide">
                            {{ $organizer->role }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <!-- ============ PARTICIPANTS (full width) ============ -->
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="flex justify-between items-center px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <h3 class="text-sm font-semibold text-gray-700 flex items-center gap-2">
                    <div class="w-8 h-8 bg-white rounded-lg flex items-center justify-center border border-blue-200/50 shrink-0">
                        <i class="fa-solid fa-users text-blue-600 text-xs"></i>
                    </div>
                    Participants
                    <span class="text-gray-400 font-normal">({{ $meeting->participants->count() }})</span>
                </h3>
            </div>

            <div class="p-4 sm:p-5">
                @if($meeting->participants->count())
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        @foreach($meeting->participants as $participant)
                            @php
                                $pUser = $participant->user;
                                $pInitials = $pUser ? mb_strtoupper(mb_substr($pUser->name, 0, 1)) : '?';

                                // Attendance history:
                                // once a participant has attended, keep showing Joined.
                                // left_at also covers older records where joined_at was cleared.
                                $joinedAt = $participant->joined_at ?? $participant->pivot?->joined_at;
                                $leftAt   = $participant->left_at ?? $participant->pivot?->left_at;
                                $hasAttended = $joinedAt !== null || $leftAt !== null;
                            @endphp
                            <div class="flex items-center justify-between p-3 bg-gray-50 hover:bg-blue-50/50 border border-gray-100 rounded-xl transition">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-9 h-9 rounded-full overflow-hidden bg-blue-100 flex items-center justify-center text-xs font-bold text-blue-700 shrink-0">
                                        @if($pUser && $pUser->avatar)
                                            <img src="{{ Storage::url($pUser->avatar) }}" class="w-full h-full object-cover" alt="{{ $pUser->name }}">
                                        @else
                                            {{ $pInitials }}
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="font-semibold text-sm text-gray-700 truncate">{{ $pUser->name ?? 'Unknown' }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $pUser->email ?? '' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-2">
                                    @if($hasAttended)
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold px-2.5 py-1 rounded-full border border-green-100 bg-green-50 text-green-600 uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                            Joined
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 text-[10px] font-semibold px-2.5 py-1 rounded-full border border-amber-100 bg-amber-50 text-amber-600 uppercase tracking-wide">
                                            <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                            Not Joined
                                        </span>
                                    @endif

                                    @if(isset($participant->role))
                                        <span class="text-[10px] bg-gray-200 text-gray-600 px-2 py-1 rounded-full font-semibold uppercase">
                                            {{ $participant->role }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 text-gray-400">
                        <i class="fa-solid fa-user-slash text-3xl mb-2 block"></i>
                        <p class="text-sm">No participants added yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
