@forelse($participants as $participant)

    @php
        $latest = $participant->joinedMeetings->first();

        $currentlyJoined = $latest
            && !is_null($latest->joined_at)
            && (
                is_null($latest->left_at)
                || $latest->left_at < $latest->joined_at
            );

        $isActiveNow = $currentlyJoined
            && optional($latest?->meeting)->status === 'active';

        $everAttended = $latest
            && (
                !is_null($latest->joined_at)
                || !is_null($latest->left_at)
            );

        if ($isActiveNow) {
            $statusLabel = 'Active Now';
            $statusClass = 'bg-emerald-50 text-emerald-700 border-emerald-100';
            $dotClass = 'bg-emerald-500 animate-pulse';
        } elseif ($everAttended) {
            $statusLabel = 'Attended';
            $statusClass = 'bg-blue-50 text-blue-700 border-blue-100';
            $dotClass = 'bg-blue-500';
        } elseif ($latest && $latest->status === 'declined') {
            $statusLabel = 'Declined';
            $statusClass = 'bg-red-50 text-red-700 border-red-100';
            $dotClass = 'bg-red-500';
        } elseif ($latest && $latest->status === 'accepted') {
            $statusLabel = 'Accepted';
            $statusClass = 'bg-violet-50 text-violet-700 border-violet-100';
            $dotClass = 'bg-violet-500';
        } else {
            $statusLabel = 'Invited';
            $statusClass = 'bg-gray-50 text-gray-600 border-gray-200';
            $dotClass = 'bg-gray-400';
        }

        $lastActive = $latest?->updated_at
            ? $latest->updated_at->diffForHumans()
            : 'Never';
    @endphp

    <tr data-participant-id="{{ $participant->id }}">

        <td class="px-5 py-4">
            <div class="flex items-center gap-3">

                <img
                    src="{{ $participant->image_url }}"
                    alt="{{ $participant->name }}"
                    class="h-9 w-9 rounded-full object-cover ring-1 ring-gray-200"
                >

                <div class="min-w-0">
                    <p class="font-semibold text-gray-800 truncate">
                        {{ $participant->name }}
                    </p>

                    <p class="text-[11px] text-gray-400 mt-0.5">
                        Participant #{{ $participant->id }}
                    </p>
                </div>

            </div>
        </td>

        <td class="px-5 py-4">
            <p class="text-sm text-gray-600">
                {{ $participant->email }}
            </p>
        </td>

        <td class="px-5 py-4">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $statusClass }}">
                <span class="h-2 w-2 rounded-full {{ $dotClass }}"></span>

                {{ $statusLabel }}
            </span>
        </td>

        <td class="px-5 py-4">
            <p class="text-sm text-gray-600">
                {{ $lastActive }}
            </p>
        </td>

        <td class="px-5 py-4">
            <div class="flex items-center gap-2">

                <a
                    href="{{ route('organizer.participants.show', $participant->id) }}"
                    class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:border-blue-200 hover:bg-blue-50 hover:text-blue-600"
                    title="View participant"
                >
                    <i class="fa-regular fa-eye text-xs"></i>
                </a>

                <button
                    type="button"
                    class="delete-participant-btn inline-flex h-8 w-8 items-center justify-center rounded-lg border border-gray-200 bg-white text-gray-500 transition hover:border-red-200 hover:bg-red-50 hover:text-red-600"
                    data-id="{{ $participant->id }}"
                    data-name="{{ $participant->name }}"
                    title="Remove participant"
                >
                    <i class="fa-regular fa-trash-can text-xs"></i>
                </button>

            </div>
        </td>

    </tr>

@empty

    <tr>
        <td colspan="5"
            class="px-5 py-12 text-center">

            <div class="flex flex-col items-center justify-center">

                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-gray-50 text-gray-400 ring-1 ring-gray-100">
                    <i class="fa-solid fa-user-group"></i>
                </div>

                <p class="mt-3 text-sm font-semibold text-gray-600">
                    No participants found
                </p>

                <p class="mt-1 text-xs text-gray-400">
                    Participants will appear here after they are invited to your meetings.
                </p>

            </div>

        </td>
    </tr>

@endforelse
