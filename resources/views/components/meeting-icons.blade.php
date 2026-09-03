@props(['meeting'])

@php
    $ended = $meeting->status === 'completed' || !empty($meeting->organizer_left_at);
    $closed = $ended || $meeting->status === 'cancelled';
@endphp

<div class="flex gap-2 items-center">

    <!-- VIEW is always available -->
    <a href="{{ route('organizer.meetings.show', $meeting) }}"
       class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow"
       title="View meeting">
        <i class="fa-regular fa-eye w-4 text-center text-gray-600 group-hover:text-blue-600 transition"></i>
    </a>

    @unless($closed)
        <!-- EMAIL -->
        <button onclick="event.stopPropagation(); openEmailModal(
                    {{ $meeting->id }},
                    '{{ addslashes($meeting->title) }}',
                    '{{ addslashes($meeting->participants->pluck('user.email')->filter()->implode(', ')) }}'
                 )"
                class="p-2 rounded-lg bg-gray-100 hover:bg-purple-100 transition group shadow-sm hover:shadow"
                title="Email participants">
            <i class="fa-regular fa-envelope w-4 text-center text-gray-600 group-hover:text-purple-600 transition"></i>
        </button>

        <!-- INVITE LINK -->
        <div class="relative inline-block">
            <button onclick="event.stopPropagation(); copyLinkFromTable('{{ $meeting->unique_code }}', this)"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-indigo-100 transition group shadow-sm hover:shadow"
                    title="Copy invite link">
                <i class="fa-solid fa-link w-4 text-center text-gray-600 group-hover:text-indigo-600 transition"></i>
            </button>
            <span class="copy-toast absolute -top-9 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-3 py-1.5 rounded-lg opacity-0 pointer-events-none transition-opacity duration-300 whitespace-nowrap z-50">
                Link copied
            </span>
        </div>

        <!-- EDIT -->
        @if($meeting->status === 'upcoming')
            <a href="{{ route('organizer.meetings.edit', $meeting) }}"
               class="p-2 rounded-lg bg-gray-100 hover:bg-green-100 transition group shadow-sm hover:shadow inline-flex"
               title="Edit meeting">
                <i class="fa-regular fa-pen-to-square w-4 text-center text-gray-600 group-hover:text-green-600 transition"></i>
            </a>
        @endif

        <!-- CANCEL -->
        @if(in_array($meeting->status, ['upcoming', 'active'], true))
            <form action="{{ route('organizer.meetings.cancel', $meeting) }}"
                  method="POST"
                  data-end-url="{{ route('meetings.ended', $meeting) }}"
                  onsubmit="return cancelMeetingFromTable(event, this)">
                @csrf
                @method('PATCH')
                <button type="submit"
                        class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow"
                        title="End meeting">
                    <i class="fa-solid fa-circle-stop w-4 text-center text-red-500 group-hover:text-red-700 transition"></i>
                </button>
            </form>
        @endif
    @else
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-50 border border-slate-200 text-[11px] font-semibold text-slate-500">
            <i class="fa-solid fa-lock"></i>
            Closed
        </span>
    @endunless
</div>

<script>
    function copyLinkFromTable(code, btn) {
        const link = `{{ url('/meetings/join') }}/${code}`;

        navigator.clipboard.writeText(link).then(() => {
            const toast = btn.parentElement.querySelector('.copy-toast');
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');

            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
            }, 1600);
        }).catch(() => {
            alert('Failed to copy link. Please copy manually: ' + link);
        });
    }

    async function cancelMeetingFromTable(event, form) {
        event.preventDefault();

        if (!confirm('End this meeting for everyone? This cannot be undone.')) {
            return false;
        }

        const button = form.querySelector('button[type="submit"]');
        if (button) button.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                },
                body: new FormData(form),
            });

            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }

            window.location.href = form.dataset.endUrl + '?reason=cancelled';
        } catch (error) {
            console.error('[SmartMeet] meeting cancel failed', error);
            if (button) button.disabled = false;
            alert('Meeting could not be ended. Please try again.');
        }

        return false;
    }
</script>
