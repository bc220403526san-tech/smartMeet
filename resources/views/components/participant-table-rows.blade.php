@forelse($participants as $participant)
    @php
        $latest = $participant->joinedMeetings->first();
        $rawStatus = $latest->status ?? 'invited';
        $meetingIsActive = optional($latest?->meeting)->status === 'active';

        // FIXED: 'joined' status kabhi exist hi nahi karta tha (enum sirf
        // invited/accepted/declined hai). Asal join/leave ka pata joined_at
        // aur left_at columns se chalta hai.
        $currentlyJoined = $latest
            && ! is_null($latest->joined_at)
            && (is_null($latest->left_at) || $latest->left_at < $latest->joined_at);

        $everAttended = $latest && (! is_null($latest->joined_at) || ! is_null($latest->left_at));

        if ($currentlyJoined && $meetingIsActive) {
            $badge = ['bg' => 'bg-green-50 text-green-700 border-green-100', 'dot' => 'bg-green-500 animate-pulse', 'label' => 'Active Now'];
        } elseif ($everAttended) {
            $badge = ['bg' => 'bg-blue-50 text-blue-700 border-blue-100', 'dot' => 'bg-blue-500', 'label' => 'Attended'];
        } elseif ($rawStatus === 'declined') {
            $badge = ['bg' => 'bg-red-50 text-red-600 border-red-100', 'dot' => 'bg-red-400', 'label' => 'Declined'];
        } elseif ($rawStatus === 'accepted') {
            $badge = ['bg' => 'bg-yellow-50 text-yellow-700 border-yellow-100', 'dot' => 'bg-yellow-500', 'label' => 'Accepted'];
        } else {
            $badge = ['bg' => 'bg-gray-50 text-gray-600 border-gray-200', 'dot' => 'bg-gray-400', 'label' => 'Invited'];
        }

        $lastActive = $latest?->updated_at ? $latest->updated_at->diffForHumans() : 'Never';
    @endphp
    <tr class="hover:bg-blue-50/30 transition duration-200" data-participant-id="{{ $participant->id }}">
        <td class="px-5 py-4">
            <div class="flex items-center gap-3">
                <img src="{{ $participant->image_url }}" class="w-10 h-10 rounded-full object-cover ring-2 ring-gray-100" alt="">
                <div>
                    <p class="font-semibold text-gray-800">{{ $participant->name }}</p>
                    <p class="text-xs text-gray-400">ID: #P-{{ str_pad($participant->id, 3, '0', STR_PAD_LEFT) }}</p>
                </div>
            </div>
        </td>
        <td class="px-5 py-4">
            <p class="text-sm text-gray-600">{{ $participant->email }}</p>
        </td>
        <td class="px-5 py-4">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $badge['bg'] }}">
                <span class="w-2 h-2 rounded-full {{ $badge['dot'] }}"></span>
                {{ $badge['label'] }}
            </span>
        </td>
        <td class="px-5 py-4">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-medium border border-gray-200 bg-gray-50 text-gray-600">
                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                {{ $lastActive }}
            </span>
        </td>
        <td class="px-5 py-4">
            <div class="flex items-center gap-2">
                <a href="{{ route('organizer.participants.show', $participant->id) }}"
                   class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow inline-flex"
                   title="View Participant">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                    </svg>
                </a>
                <button type="button"
                        class="delete-participant-btn p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow"
                        title="Delete Participant"
                        data-id="{{ $participant->id }}"
                        data-name="{{ $participant->name }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4 text-gray-600 group-hover:text-red-600 transition">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                    </svg>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-5 py-12 text-center text-gray-400 text-sm">
            <i class="fa fa-users-slash text-4xl mb-3 block"></i>
            No participants found.
        </td>
    </tr>
@endforelse
