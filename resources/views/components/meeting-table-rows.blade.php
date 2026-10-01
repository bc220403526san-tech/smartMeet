@forelse($meetings as $meeting)
    @php
        $timezone = $meeting->timezone ?: 'Asia/Karachi';

        $startTime = \Carbon\Carbon::parse(
            trim($meeting->date . ' ' . $meeting->time),
            $timezone
        );

        $endTime = $startTime
            ->copy()
            ->addMinutes((int) $meeting->duration);
    @endphp

    <tr class="hover:bg-gray-50/80 transition" data-meeting-id="{{ $meeting->id }}" data-current-status="{{ $meeting->status }}" data-start-ms="{{ $startTime->utc()->valueOf() }}" data-end-ms="{{ $endTime->utc()->valueOf() }}">
        <td class="px-5 py-4 align-top">
            <div class="min-w-[220px]">
                <p class="font-semibold text-gray-800">
                    {{ $meeting->title }}
                </p>

                @if($meeting->description)
                    <p class="mt-1 text-xs text-gray-400 line-clamp-2">
                        {{ $meeting->description }}
                    </p>
                @endif
            </div>
        </td>

        <td class="px-5 py-4 align-top">
            <div class="min-w-[190px]">
                <p class="font-medium text-gray-700">
                    {{ $startTime->format('d M Y') }}
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    {{ $startTime->format('h:i:s A') }}
                    –
                    {{ $endTime->format('h:i:s A') }}
                </p>

                <p class="mt-1 text-[11px] text-gray-400">
                    {{ $timezone }}
                </p>

                @if(in_array($meeting->status, ['upcoming', 'active'], true))
                    <p class="mt-2 text-xs font-semibold {{ $meeting->status === 'active' ? 'text-green-600' : 'text-blue-600' }}" data-meeting-timer>
                        {{ $meeting->status === 'active' ? '00:00 / ' . sprintf('%02d:%02d', intdiv((int) $meeting->duration, 60), (int) $meeting->duration % 60) : 'Starts in…' }}
                    </p>
                @endif
            </div>
        </td>

        <td class="px-5 py-4 align-top">
            <div class="flex items-center gap-2">
                <div class="flex -space-x-2">
                    @foreach($meeting->participants->take(4) as $participant)
                        @php
                            $participantName =
                                $participant->user->name ??
                                'Participant';

                            $initials = strtoupper(
                                mb_substr($participantName, 0, 1)
                            );
                        @endphp

                        <div class="w-8 h-8 rounded-full border-2 border-white bg-blue-100 text-blue-700 flex items-center justify-center text-xs font-bold"
                             title="{{ $participantName }}">
                            {{ $initials }}
                        </div>
                    @endforeach
                </div>

                <span class="text-xs text-gray-500">
                    {{ $meeting->participants->count() }}
                </span>
            </div>
        </td>

        <td class="px-5 py-4 align-top">
            @if($meeting->status === 'active')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-50 text-green-700 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                    Active
                </span>
            @elseif($meeting->status === 'upcoming')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-yellow-50 text-yellow-700 text-xs font-semibold">
                    <span class="w-2 h-2 rounded-full bg-yellow-500"></span>
                    Upcoming
                </span>
            @elseif($meeting->status === 'completed')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-100 text-gray-600 text-xs font-semibold">
                    Completed
                </span>
            @elseif($meeting->status === 'cancelled')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-red-50 text-red-700 text-xs font-semibold">
                    Cancelled
                </span>
            @elseif($meeting->status === 'ended')
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">
                    Ended
                </span>
            @endif
        </td>

        <td class="px-5 py-4 align-top">
            @if($meeting->status === 'active')
                <a href="{{ route('organizer.meetings.attend', $meeting) }}"
                   class="inline-flex items-center justify-center gap-2 px-4 py-2 rounded-xl bg-green-600 text-white text-xs font-semibold hover:bg-green-700 transition shadow-sm">
                    Attend
                </a>
            @elseif($meeting->status === 'upcoming')
                <span class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-yellow-50 text-yellow-700 text-xs font-medium">
                    Not started
                </span>
            @elseif(in_array($meeting->status, ['completed', 'ended'], true))
                <span class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-gray-100 text-gray-500 text-xs font-medium">
                    Ended
                </span>
            @else
                <span class="inline-flex items-center justify-center px-3 py-2 rounded-xl bg-red-50 text-red-600 text-xs font-medium">
                    Unavailable
                </span>
            @endif
        </td>

        <td class="px-5 py-4 align-top">
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('organizer.meetings.show', $meeting) }}"
                   class="inline-flex items-center justify-center px-3 py-2 rounded-xl border border-gray-200 text-gray-600 text-xs hover:bg-gray-50 transition">
                    View
                </a>

                @if($meeting->status === 'upcoming')
                    <a href="{{ route('organizer.meetings.edit', $meeting) }}"
                       class="inline-flex items-center justify-center px-3 py-2 rounded-xl border border-blue-200 text-blue-600 text-xs hover:bg-blue-50 transition">
                        Edit
                    </a>
                @endif

                @if(in_array($meeting->status, ['upcoming', 'active'], true))
                    <form method="POST"
                          action="{{ route('organizer.meetings.cancel', $meeting) }}"
                          onsubmit="return confirm('Cancel this meeting?');">
                        @csrf
                        @method('PATCH')

                        <button type="submit"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-xl border border-red-200 text-red-600 text-xs hover:bg-red-50 transition">
                            Cancel
                        </button>
                    </form>
                @endif

                @if(in_array($meeting->status, ['completed', 'ended', 'cancelled'], true))
                    <form method="POST"
                          action="{{ route('organizer.meetings.destroy', $meeting) }}"
                          onsubmit="return confirm('Delete this meeting permanently?');">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="inline-flex items-center justify-center px-3 py-2 rounded-xl border border-red-200 text-red-600 text-xs hover:bg-red-50 transition">
                            Delete
                        </button>
                    </form>
                @endif
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-5 py-16 text-center">
            <div class="max-w-sm mx-auto">
                <div class="w-14 h-14 mx-auto rounded-2xl bg-gray-100 flex items-center justify-center text-gray-400">
                    <svg xmlns="http://www.w3.org/2000/svg"
                         class="w-7 h-7"
                         fill="none"
                         viewBox="0 0 24 24"
                         stroke-width="1.5"
                         stroke="currentColor">
                        <path stroke-linecap="round"
                              stroke-linejoin="round"
                              d="M8.25 6.75h7.5m-7.5 3h7.5m-7.5 3h4.5M6 3.75h12A2.25 2.25 0 0 1 20.25 6v12A2.25 2.25 0 0 1 18 20.25H6A2.25 2.25 0 0 1 3.75 18V6A2.25 2.25 0 0 1 6 3.75Z"/>
                    </svg>
                </div>

                <h3 class="mt-4 font-semibold text-gray-700">
                    No meetings found
                </h3>

                <p class="mt-1 text-sm text-gray-400">
                    Create a meeting or change the selected filters.
                </p>
            </div>
        </td>
    </tr>
@endforelse
