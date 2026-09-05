<x-layouts.app>
    <x-slot name="header">
        <x-header.page-title title="Admin Dashboard" />
    </x-slot>

    @php
        $organizer = $meeting->organizer;

        // Organizer attendance is recorded directly on the meeting.
        $organizerHasJoined = !empty($meeting->organizer_joined_at)
            || !empty($meeting->actual_start);

        // Participants section must contain ONLY participant users.
        // If the organizer also exists in the meeting_participants table,
        // exclude that organizer record from this UI.
        $displayParticipants = $meeting->participants
            ->filter(function ($participant) use ($organizer) {
                $participantUser = $participant->user;

                if (!$participantUser) {
                    return true;
                }

                if ($organizer && (int) $participantUser->id === (int) $organizer->id) {
                    return false;
                }

                return $participantUser->role === 'participant';
            })
            ->values();

        // Actual participant attendance comes from MeetingParticipantLog.
        // A participant is "Joined" only if at least one real join session was recorded.
        $joinedParticipantIds = \App\Models\MeetingParticipantLog::query()
            ->where('meeting_id', $meeting->id)
            ->whereNotNull('joined_at')
            ->pluck('user_id')
            ->unique()
            ->map(fn ($id) => (int) $id)
            ->all();

        $meetingDate = $meeting->date ? \Carbon\Carbon::parse($meeting->date) : null;
        $meetingTime = $meeting->time ? \Carbon\Carbon::parse($meeting->time) : null;

        $statusConfig = [
            'upcoming' => ['label' => 'Upcoming', 'class' => 'bg-blue-50 text-blue-700 border-blue-100', 'dot' => 'bg-blue-500'],
            'active' => ['label' => 'Active', 'class' => 'bg-green-50 text-green-700 border-green-100', 'dot' => 'bg-green-500 animate-pulse'],
            'completed' => ['label' => 'Completed', 'class' => 'bg-gray-100 text-gray-700 border-gray-200', 'dot' => 'bg-gray-400'],
            'ended' => ['label' => 'Ended', 'class' => 'bg-purple-50 text-purple-700 border-purple-100', 'dot' => 'bg-purple-500'],
            'cancelled' => ['label' => 'Cancelled', 'class' => 'bg-red-50 text-red-700 border-red-100', 'dot' => 'bg-red-500'],
            'flagged' => ['label' => 'Flagged', 'class' => 'bg-orange-50 text-orange-700 border-orange-100', 'dot' => 'bg-orange-500'],
        ];

        $status = $statusConfig[$meeting->status] ?? [
            'label' => ucfirst($meeting->status ?? 'Unknown'),
            'class' => 'bg-gray-100 text-gray-700 border-gray-200',
            'dot' => 'bg-gray-400',
        ];

        $agendaItems = [];
        if (!empty($meeting->agenda)) {
            $decodedAgenda = json_decode($meeting->agenda, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decodedAgenda)) {
                $agendaItems = $decodedAgenda;
            }
        }
    @endphp


    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4">
        <x-success />
        <x-error />

        <div class="flex flex-col lg:flex-row lg:items-end lg:justify-between gap-3">
            <div>
                <a href="{{ route('admin.meetings.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-blue-600 hover:text-blue-700 transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to Manage Meetings
                </a>
                <h1 class="mt-3 text-2xl sm:text-3xl font-bold text-gray-900">Meeting Details</h1>
                <p class="mt-1 text-sm text-gray-500">Review meeting schedule, organizer, agenda and attendance.</p>
            </div>
            <span class="inline-flex items-center gap-2 w-fit px-3.5 py-2 rounded-xl text-sm font-semibold border {{ $status['class'] }}">
                <span class="w-2 h-2 rounded-full {{ $status['dot'] }}"></span>
                {{ $status['label'] }}
            </span>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-4">
            <section class="xl:col-span-2 bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 sm:p-6">
                    <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 text-blue-600 text-[11px] font-bold uppercase tracking-wider">
                        <i class="fa-solid fa-video text-[10px]"></i> Meeting
                    </span>
                    <h2 class="mt-3 text-2xl sm:text-3xl font-bold text-gray-900 break-words">{{ $meeting->title }}</h2>
                    @if($meeting->description)
                        <p class="mt-2 text-sm sm:text-base text-gray-500 leading-relaxed">{{ $meeting->description }}</p>
                    @endif
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-2xl shadow-sm p-5 sm:p-6">
                <div class="grid grid-cols-2 gap-3 h-full">
                    <div class="rounded-2xl bg-gray-50 border border-gray-200 p-4">
                        <p class="text-[11px] uppercase tracking-wide text-gray-400">Meeting ID</p>
                        <p class="mt-2 text-xl font-bold text-gray-900">#{{ $meeting->id }}</p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 border border-gray-200 p-4">
                        <p class="text-[11px] uppercase tracking-wide text-gray-400">Participants</p>
                        <p class="mt-2 text-xl font-bold text-gray-900">{{ $displayParticipants->count() }}</p>
                    </div>
                </div>
            </section>
        </div>

        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-900">Meeting Information</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Scheduled meeting details</p>
                </div>
            </div>

            <div class="p-5 sm:p-6 grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-3">
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-calendar-days"></i></div>
                    <div><p class="text-xs text-gray-400">Date</p><p class="font-semibold text-gray-900">{{ $meetingDate ? $meetingDate->format('M d, Y') : 'Not set' }}</p></div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center shrink-0"><i class="fa-regular fa-clock"></i></div>
                    <div><p class="text-xs text-gray-400">Time</p><p class="font-semibold text-gray-900">{{ $meetingTime ? $meetingTime->format('h:i A') : 'Not set' }}</p></div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-hourglass-half"></i></div>
                    <div><p class="text-xs text-gray-400">Duration</p><p class="font-semibold text-gray-900">{{ $meeting->duration ? $meeting->duration . ' min' : 'Not set' }}</p></div>
                </div>
                <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 text-purple-600 flex items-center justify-center shrink-0"><i class="fa-solid fa-earth-americas"></i></div>
                    <div class="min-w-0"><p class="text-xs text-gray-400">Timezone</p><p class="font-semibold text-gray-900 break-words">{{ $meeting->timezone ?: 'Not set' }}</p></div>
                </div>
            </div>
        </section>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">
            <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center"><i class="fa-solid fa-user-tie"></i></div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Organizer</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Meeting owner and attendance</p>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    @if($organizer)
                        <div class="rounded-2xl border border-blue-100 bg-blue-50/50 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div class="flex items-center gap-3 min-w-0">
                                <x-user-avatar :user="$organizer" size="md" />
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 truncate">{{ $organizer->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $organizer->email }}</p>
                                    <p class="mt-1 text-xs text-gray-400">{{ ucfirst($organizer->role) }}</p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-2 shrink-0">
                                @if($organizerHasJoined)
                                    <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-green-50 border border-green-200 text-xs font-semibold text-green-700"><i class="fa-solid fa-circle-check"></i> Joined</span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-semibold text-gray-500"><i class="fa-solid fa-circle-xmark"></i> Not Joined</span>
                                @endif
                                <a href="{{ route('admin.users.show', $organizer) }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-medium text-gray-600 hover:text-blue-600 transition">
                                    <i class="fa-regular fa-eye"></i> View User
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="p-5 rounded-2xl border border-dashed border-gray-200 bg-gray-50 text-sm text-gray-400">Organizer information is not available.</div>
                    @endif
                </div>
            </section>

            <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center"><i class="fa-solid fa-list-check"></i></div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Agenda</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Topics planned for this meeting</p>
                    </div>
                </div>
                <div class="p-5 sm:p-6">
                    @if(count($agendaItems) > 0)
                        <div class="space-y-2">
                            @foreach($agendaItems as $item)
                                <div class="flex items-start gap-3 p-3.5 bg-gray-50 border border-gray-200 rounded-xl">
                                    <span class="mt-1.5 w-2 h-2 rounded-full bg-blue-500 shrink-0"></span>
                                    <p class="text-sm text-gray-600 leading-relaxed">{{ is_array($item) ? ($item['title'] ?? json_encode($item)) : $item }}</p>
                                </div>
                            @endforeach
                        </div>
                    @elseif($meeting->agenda)
                        <div class="p-4 bg-gray-50 border border-gray-200 rounded-2xl"><p class="text-sm text-gray-600 leading-relaxed">{{ $meeting->agenda }}</p></div>
                    @else
                        <div class="p-6 text-center bg-gray-50 border border-dashed border-gray-200 rounded-2xl text-sm text-gray-400">No agenda added for this meeting.</div>
                    @endif
                </div>
            </section>
        </div>

        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-green-50 text-green-600 flex items-center justify-center"><i class="fa-solid fa-users"></i></div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Participants</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Participant list and attendance</p>
                    </div>
                </div>
                <span class="inline-flex items-center justify-center min-w-8 h-8 px-2 rounded-xl bg-gray-100 text-gray-700 text-xs font-semibold">{{ $displayParticipants->count() }}</span>
            </div>

            <div class="p-5 sm:p-6 grid grid-cols-1 xl:grid-cols-2 gap-3">
                @forelse($displayParticipants as $participant)
                    @php
                        $participantUser = $participant->user;
                        $hasJoinedMeeting = $participantUser
                            ? in_array((int) $participantUser->id, $joinedParticipantIds, true)
                            : false;
                    @endphp

                    <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="flex items-center gap-3 min-w-0">
                            @if($participantUser)
                                <x-user-avatar :user="$participantUser" size="sm" />
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center shrink-0"><i class="fa-solid fa-user"></i></div>
                            @endif
                            <div class="min-w-0">
                                <p class="font-medium text-gray-900 truncate">{{ $participantUser?->name ?? 'Unknown User' }}</p>
                                <p class="text-sm text-gray-500 truncate">{{ $participantUser?->email ?? 'No email available' }}</p>
                                @if($participant->status)
                                    <p class="mt-1 text-[11px] uppercase tracking-wide text-gray-400">{{ ucfirst($participant->status) }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            @if($hasJoinedMeeting)
                                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-green-50 border border-green-200 text-xs font-semibold text-green-700"><i class="fa-solid fa-circle-check"></i> Joined</span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-gray-200 text-xs font-semibold text-gray-500"><i class="fa-solid fa-circle-xmark"></i> Not Joined</span>
                            @endif
                            @if($participantUser)
                                <a href="{{ route('admin.users.show', $participantUser) }}" class="inline-flex w-9 h-9 items-center justify-center rounded-xl bg-white border border-gray-200 text-gray-500 hover:text-blue-600 transition">
                                    <i class="fa-regular fa-eye text-xs"></i>
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="xl:col-span-2 p-10 text-center bg-gray-50 border border-dashed border-gray-200 rounded-2xl">
                        <i class="fa-solid fa-users-slash text-3xl text-gray-300"></i>
                        <p class="mt-3 text-sm font-medium text-gray-600">No participants found</p>
                        <p class="mt-1 text-xs text-gray-400">This meeting currently has no participant records.</p>
                    </div>
                @endforelse
            </div>
        </section>

        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-red-50 text-red-500 flex items-center justify-center"><i class="fa-solid fa-shield-halved"></i></div>
                <div>
                    <h3 class="font-semibold text-gray-900">Admin Actions</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Moderate this meeting without changing completed/ended history.</p>
                </div>
            </div>
            <div class="p-5 sm:p-6 flex flex-wrap gap-2">
                @if(in_array($meeting->status, ['upcoming', 'active'], true))
                    <form action="{{ route('admin.meetings.cancel', $meeting) }}" method="POST" onsubmit="return confirm('Cancel this meeting and notify users?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-red-50 text-red-600 border border-red-200 hover:bg-red-100 transition">
                            <i class="fa-solid fa-ban text-xs"></i> Cancel Meeting
                        </button>
                    </form>
                @endif

                @if(in_array($meeting->status, ['upcoming', 'flagged'], true))
                    <form action="{{ route('admin.meetings.flag', $meeting) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-orange-50 text-orange-600 border border-orange-200 hover:bg-orange-100 transition">
                            <i class="fa-solid fa-flag text-xs"></i>
                            {{ $meeting->status === 'flagged' ? 'Remove Flag' : 'Flag Meeting' }}
                        </button>
                    </form>
                @endif

                <form action="{{ route('admin.meetings.destroy', $meeting) }}" method="POST" onsubmit="return confirm('Permanently delete this meeting? This action cannot be undone.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl text-sm font-medium bg-gray-50 text-red-500 border border-gray-200 hover:bg-red-50 hover:border-red-200 transition">
                        <i class="fa-solid fa-trash text-xs"></i> Delete Meeting
                    </button>
                </form>
            </div>
        </section>
    </div>
</x-layouts.app>
