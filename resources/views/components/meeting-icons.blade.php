@props(['meeting'])

@php
    $closed = in_array($meeting->status, ['completed', 'ended', 'cancelled'], true);
@endphp

<div class="flex items-center gap-2">
    <a href="{{ route('organizer.meetings.show', $meeting) }}"
       title="View meeting"
       class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-100 text-gray-500 hover:text-blue-600 transition flex items-center justify-center shadow-sm">
        <i class="fa-regular fa-eye text-sm"></i>
    </a>

    @unless($closed)
        <button type="button"
                title="Send email invite"
                onclick="window.dispatchEvent(new CustomEvent('smartmeet-email-invite', { detail: { meetingId: '{{ $meeting->id }}' } }))"
                class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-100 text-gray-500 hover:text-blue-600 transition flex items-center justify-center shadow-sm">
            <i class="fa-regular fa-envelope text-sm"></i>
        </button>

        <button type="button"
                title="Copy invite link"
                onclick="copyLinkFromTable(@js(route('meetings.join.link', $meeting->unique_code)), this)"
                class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-100 text-gray-500 hover:text-blue-600 transition flex items-center justify-center shadow-sm">
            <i class="fa-solid fa-link text-sm"></i>
        </button>
    @endunless

    @if($meeting->status === 'upcoming')
        <a href="{{ route('organizer.meetings.edit', $meeting) }}"
           title="Edit meeting"
           class="w-10 h-10 rounded-xl bg-gray-100 hover:bg-blue-100 text-gray-500 hover:text-blue-600 transition flex items-center justify-center shadow-sm">
            <i class="fa-regular fa-pen-to-square text-sm"></i>
        </a>
    @endif

    @if(in_array($meeting->status, ['upcoming', 'active'], true))
        <form method="POST"
              action="{{ route('organizer.meetings.cancel', $meeting) }}"
              onsubmit="return confirm('Cancel this meeting? Participants will no longer be able to join.');">
            @csrf
            @method('PATCH')
            <button type="submit"
                    title="Cancel meeting"
                    class="w-10 h-10 rounded-xl bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 transition flex items-center justify-center shadow-sm border border-red-100">
                <i class="fa-solid fa-xmark text-base"></i>
            </button>
        </form>
    @elseif($closed)
        <span class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-gray-200 bg-gray-50 text-gray-500 text-xs font-semibold">
            <i class="fa-solid fa-lock text-[11px]"></i>
            Closed
        </span>
    @endif
</div>

@once
    <script>
        function copyLinkFromTable(url, button) {
            navigator.clipboard.writeText(url).then(() => {
                const icon = button?.querySelector('i');
                if (!icon) return;
                const old = icon.className;
                icon.className = 'fa-solid fa-check text-green-600';
                setTimeout(() => icon.className = old, 1600);
            }).catch(() => {
                window.prompt('Copy meeting link:', url);
            });
        }
    </script>
@endonce
