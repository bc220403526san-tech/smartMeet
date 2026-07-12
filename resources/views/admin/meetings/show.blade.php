<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Admin Dashboard" />
    </x-slot>


    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4 overflow-y-auto">

        @php
            $statusConfig = [
                'upcoming'   => ['badge' => 'bg-white/20 text-white', 'dot' => 'bg-sky-200',    'label' => 'Upcoming'],
                'active'     => ['badge' => 'bg-white/20 text-white', 'dot' => 'bg-green-200 animate-pulse', 'label' => 'Live now'],
                'completed'  => ['badge' => 'bg-white/20 text-white', 'dot' => 'bg-gray-200',   'label' => 'Completed'],
                'incomplete' => ['badge' => 'bg-white/20 text-white', 'dot' => 'bg-red-200',    'label' => 'Incomplete'],
                'cancelled'  => ['badge' => 'bg-white/20 text-white', 'dot' => 'bg-red-200',    'label' => 'Cancelled'],
                'flagged'    => ['badge' => 'bg-white/20 text-white', 'dot' => 'bg-yellow-200', 'label' => 'Flagged for review'],
            ];
            $s = $statusConfig[$meeting->status] ?? ['badge' => 'bg-white/20 text-white', 'dot' => 'bg-gray-200', 'label' => ucfirst($meeting->status)];

            $organizer = $meeting->organizer;
            $orgName   = optional($organizer)->name ?? 'Unknown';
            $orgInitials = collect(explode(' ', $orgName))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');

            $date = \Carbon\Carbon::parse($meeting->date);
        @endphp

            <!-- TOP HEADER -->
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div>
                <a href="{{ route('admin.meetings.index') }}"
                   class="text-sm text-blue-600 mb-2 inline-flex items-center gap-1 hover:gap-2 transition-all font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18"/>
                    </svg>
                    Back to Meetings
                </a>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">Meeting Details</h1>
                <p class="text-gray-400 mt-1 text-sm sm:text-base">Review meeting logistics, participants and agenda.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="px-4 py-3 bg-blue-50 border border-blue-100 text-blue-700 text-sm rounded-xl">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="px-4 py-3 bg-red-50 border border-red-100 text-red-600 text-sm rounded-xl">
                {{ session('error') }}
            </div>
        @endif

        <!-- MEETING TICKET -->
        <div class="flex flex-col md:flex-row bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">

            <!-- LEFT: MAIN INFO -->
            <div class="flex-1 p-5 sm:p-6">
                <div class="flex items-center gap-2 text-xs font-semibold uppercase tracking-wider text-blue-500 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    Meeting Pass
                </div>

                <h3 class="text-xl sm:text-2xl font-bold text-gray-800 leading-snug">
                    {{ $meeting->title }}
                </h3>

                <div class="flex flex-wrap gap-x-6 gap-y-2 text-sm text-gray-500 mt-4">
                    <span class="inline-flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5"/>
                        </svg>
                        {{ $date->format('D, M d, Y') }}
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                        @if($meeting->duration)
                            &middot; {{ $meeting->duration }} min
                        @endif
                    </span>
                    <span class="inline-flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z"/>
                        </svg>
                        {{ $meeting->participants->count() }} participants
                    </span>
                </div>

                <div class="mt-5 pt-5 border-t border-gray-100">
                    <h4 class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Agenda</h4>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        {{ $meeting->description ?? 'No description provided.' }}
                    </p>
                </div>

                <!-- ORGANIZER STRIP -->
                <div class="mt-5 flex items-center justify-between gap-3 bg-blue-50/60 border border-blue-100 rounded-2xl p-4">
                    <div class="flex items-center gap-3 min-w-0">
                        @if(optional($organizer)->image_url)
                            <img src="{{ $organizer->image_url }}" class="w-10 h-10 rounded-full object-cover shrink-0" alt="{{ $orgName }}">
                        @else
                            <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 font-semibold flex items-center justify-center shrink-0">
                                {{ $orgInitials }}
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-xs text-gray-400 uppercase tracking-wide">Organized by</p>
                            <p class="font-medium text-gray-700 truncate">{{ $orgName }}</p>
                        </div>
                    </div>
                    @if($organizer)
                        <a href="{{ route('admin.users.show', $organizer->id) }}" title="View profile"
                           class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-blue-600 hover:border-blue-200 transition shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                            </svg>
                        </a>
                    @endif
                </div>
            </div>

            <!-- RIGHT: TICKET STUB -->
            <div class="relative md:w-60 shrink-0 bg-gradient-to-br from-blue-400 to-blue-500 text-white p-5 sm:p-6 flex md:flex-col justify-between items-center md:items-start
                        border-t border-dashed border-white/30 md:border-t-0 md:border-l md:border-dashed">

                <div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $s['badge'] }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                        {{ $s['label'] }}
                    </span>
                </div>

                <div class="text-center md:text-left md:mt-6">
                    <p class="text-[11px] uppercase tracking-widest text-white/70">{{ $date->format('M') }}</p>
                    <p class="text-4xl font-bold leading-none mt-1">{{ $date->format('d') }}</p>
                    <p class="text-xs text-white/70 mt-1">{{ $date->format('Y') }}</p>
                </div>

                <div class="text-center md:text-left md:mt-6">
                    <p class="text-[11px] uppercase tracking-widest text-white/70">Starts</p>
                    <p class="text-lg font-semibold mt-1">
                        {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                    </p>
                </div>
            </div>
        </div>

        <!-- PARTICIPANTS -->
        <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <div>
                        <h2 class="font-semibold text-gray-800 text-lg">Participants</h2>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $meeting->participants->count() }} people invited</p>
                    </div>
                    @if($meeting->participants->count() > 4)
                        <button class="text-blue-600 text-sm font-medium hover:text-blue-700">Manage all</button>
                    @endif
                </div>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($meeting->participants as $participant)
                    @php
                        $pUser = $participant->user ?? null;
                        $pName = optional($pUser)->name ?? 'Unknown';
                        $pInit = collect(explode(' ', $pName))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
                        $pRole = strtoupper($participant->role ?? 'VIEWER');

                        $roleColors = [
                            'ADMIN'   => 'bg-blue-50 text-blue-600 border-blue-100',
                            'SPEAKER' => 'bg-blue-50 text-blue-500 border-blue-100',
                            'VIEWER'  => 'bg-gray-100 text-gray-500 border-gray-200',
                        ];
                        $roleClass = $roleColors[$pRole] ?? 'bg-gray-100 text-gray-500 border-gray-200';
                    @endphp
                    <div class="flex items-center justify-between px-5 py-4 hover:bg-blue-50/30 transition duration-200">
                        <div class="flex items-center gap-3 min-w-0">
                            @if(optional($pUser)->image_url)
                                <img src="{{ $pUser->image_url }}" class="w-10 h-10 rounded-full object-cover shrink-0" alt="{{ $pName }}">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-100 text-gray-500 text-sm font-semibold flex items-center justify-center shrink-0">
                                    {{ $pInit }}
                                </div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-medium text-gray-700 truncate">{{ $pName }}</p>
                                <p class="text-xs text-gray-400 truncate">
                                    {{ optional($pUser)->job_title ?? optional($pUser)->role ?? '' }}
                                </p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full border {{ $roleClass }} shrink-0">
                            {{ $pRole }}
                        </span>
                    </div>
                @empty
                    <div class="px-5 py-12 text-center text-gray-400 text-sm">
                        No participants added yet.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</x-layouts.app>
