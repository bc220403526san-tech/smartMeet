<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Organizer Dashboard" />
    </x-slot>

    <div class="p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto min-h-screen">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-1.5 text-xs text-slate-400 mb-1.5 flex-wrap">
                    <a href="{{ route('organizers.participants.index') }}" class="text-blue-500 font-medium hover:underline">Participants</a>
                    <span>›</span>
                    <span class="text-blue-500">{{ $participant->name }}</span>
                </div>
                <h1 class="text-lg sm:text-2xl font-bold text-slate-900 tracking-tight">Participant Details</h1>
            </div>
            <div class="flex items-center gap-3 sm:mt-1 flex-shrink-0">
                <a href="{{ route('organizers.participants.index') }}"
                   class="flex items-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 border border-slate-200 rounded-xl bg-white text-slate-500 text-sm font-semibold hover:bg-slate-50 hover:border-slate-300 transition whitespace-nowrap">
                    ← Back
                </a>
            </div>
        </div>

        <!-- Centered Card -->
        <div class="flex justify-center items-start">
            <div class="w-full max-w-md">
                <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-100 hover:shadow-xl transition-all duration-300 ease-in-out relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-40 h-40 bg-gradient-to-bl from-blue-50/60 to-indigo-50/30 rounded-full blur-2xl -mr-16 -mt-16"></div>
                    <div class="absolute bottom-0 left-0 w-40 h-40 bg-gradient-to-tr from-purple-50/40 to-pink-50/20 rounded-full blur-2xl -ml-16 -mb-16"></div>

                    <div class="relative z-10">
                        <!-- Avatar -->
                        <div class="flex justify-center mb-4 relative">
                            <div class="relative">
                                @if($participant->image_url)
                                    <img src="{{ $participant->image_url }}" alt="{{ $participant->name }}"
                                         class="w-20 h-20 rounded-full border-3 border-white shadow-md object-cover">
                                @else
                                    <div class="w-20 h-20 rounded-full border-3 border-white shadow-md overflow-hidden bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white text-2xl font-bold">
                                        {{ collect(explode(' ', $participant->name))->map(fn($w) => strtoupper(substr($w, 0, 1)))->take(2)->implode('') }}
                                    </div>
                                @endif

                                {{-- Dot rang: green = abhi active, gray = inactive/offline --}}
                                @if($pStats['isActiveNow'])
                                    <span class="w-3.5 h-3.5 bg-green-400 border-2 border-white rounded-full absolute bottom-0 right-0 shadow-sm"></span>
                                @else
                                    <span class="w-3.5 h-3.5 bg-gray-300 border-2 border-white rounded-full absolute bottom-0 right-0 shadow-sm"></span>
                                @endif
                            </div>
                        </div>

                        <!-- Name -->
                        <div class="text-center mb-1">
                            <h2 class="text-xl font-bold text-slate-900">{{ $participant->name }}</h2>
                        </div>

                        <!-- Email -->
                        <div class="flex items-center justify-center gap-2 mb-4">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-slate-400">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75" />
                            </svg>
                            <span class="text-sm text-slate-500">{{ $participant->email }}</span>
                        </div>

                        <!-- Badges: Role + real Active/Inactive status -->
                        <div class="flex justify-center gap-2 mb-5 flex-wrap">
                            <span class="px-3 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wide bg-blue-50 text-blue-600 border border-blue-200">
                                Participant
                            </span>

                            @if($pStats['isActiveNow'])
                                <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wide bg-emerald-50 text-emerald-600 border border-emerald-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 inline-block animate-pulse"></span>
                                    Active
                                </span>
                            @else
                                <span class="flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wide bg-gray-100 text-gray-500 border border-gray-200">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block"></span>
                                    Inactive
                                </span>
                            @endif

                            {{-- Detailed status label: Active Now / Attended / Accepted / Declined / Invited --}}
                            @php
                                $labelColors = [
                                    'Active Now' => 'bg-emerald-50 text-emerald-600 border-emerald-200',
                                    'Attended'   => 'bg-blue-50 text-blue-600 border-blue-200',
                                    'Accepted'   => 'bg-indigo-50 text-indigo-600 border-indigo-200',
                                    'Declined'   => 'bg-red-50 text-red-600 border-red-200',
                                    'Invited'    => 'bg-amber-50 text-amber-600 border-amber-200',
                                ];
                                $labelColor = $labelColors[$pStats['label']] ?? 'bg-gray-50 text-gray-600 border-gray-200';
                            @endphp
                            <span class="px-3 py-1 rounded-full text-[10px] font-semibold uppercase tracking-wide {{ $labelColor }} border">
                                {{ $pStats['label'] }}
                            </span>
                        </div>

                        <div class="border-t border-gray-100 my-4"></div>

                        <!-- Stats Grid -->
                        <div class="grid grid-cols-3 gap-2 mb-4">
                            <div class="text-center bg-gradient-to-br from-blue-50 to-blue-100/40 rounded-lg py-2 px-1 border border-blue-100">
                                <p class="text-lg font-bold text-blue-600">{{ $pStats['totalMeetings'] }}</p>
                                <p class="text-[8px] font-medium text-slate-500 uppercase tracking-wider">Meetings</p>
                            </div>
                            <div class="text-center bg-gradient-to-br from-emerald-50 to-emerald-100/40 rounded-lg py-2 px-1 border border-emerald-100">
                                <p class="text-lg font-bold text-emerald-600">{{ $pStats['attended'] }}</p>
                                <p class="text-[8px] font-medium text-slate-500 uppercase tracking-wider">Attended</p>
                            </div>
                            <div class="text-center bg-gradient-to-br from-purple-50 to-purple-100/40 rounded-lg py-2 px-1 border border-purple-100">
                                <p class="text-lg font-bold text-purple-600">{{ $pStats['attendanceRate'] }}%</p>
                                <p class="text-[8px] font-medium text-slate-500 uppercase tracking-wider">Attendance</p>
                            </div>
                        </div>

                        @if($pStats['latestMeeting'])
                            <div class="flex items-center justify-between bg-gradient-to-r from-slate-50 to-gray-50 border border-slate-200 rounded-lg px-4 py-2.5 mb-2">
                                <div>
                                    <div class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Latest Meeting</div>
                                    <div class="text-xs font-semibold text-slate-900">{{ $pStats['latestMeeting'] }}</div>
                                </div>
                            </div>
                        @endif

                        <!-- Join Date -->
                        <div class="flex items-center justify-between bg-gradient-to-r from-slate-50 to-gray-50 border border-slate-200 rounded-lg px-4 py-2.5">
                            <div>
                                <div class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Joined Platform</div>
                                <div class="text-xs font-semibold text-slate-900">{{ $pStats['joinedOn'] }}</div>
                            </div>
                            <div class="w-8 h-8 bg-white border border-slate-200 rounded-lg flex items-center justify-center text-slate-400 shadow-sm">
                                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                                    <line x1="16" y1="2" x2="16" y2="6"/>
                                    <line x1="8" y1="2" x2="8" y2="6"/>
                                    <line x1="3" y1="10" x2="21" y2="10"/>
                                </svg>
                            </div>
                        </div>

                        <div class="mt-3 text-center text-[10px] text-slate-400">
                            Last active: {{ $pStats['lastActive'] }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
