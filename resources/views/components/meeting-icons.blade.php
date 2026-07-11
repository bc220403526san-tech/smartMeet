@props(['meeting'])
<div class="flex gap-2">

    <!-- VIEW -->
    <a href="{{ route('organizer.meetings.show', $meeting) }}"
       class="p-2 rounded-lg bg-gray-100 hover:bg-blue-100 transition group shadow-sm hover:shadow"
       title="View Meeting">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor"
             class="w-4 h-4 text-gray-600 group-hover:text-blue-600 transition">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
        </svg>
    </a>

    <!-- ====== SEND EMAIL — SHARED functionality, same as show page ====== -->
    <button onclick="event.stopPropagation(); openEmailModal(
                {{ $meeting->id }},
                '{{ addslashes($meeting->title) }}',
                '{{ addslashes($meeting->participants->pluck('user.email')->filter()->implode(', ')) }}'
             )"
            class="p-2 rounded-lg bg-gray-100 hover:bg-purple-100 transition group shadow-sm hover:shadow"
            title="Send Email to Participants">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor"
             class="w-4 h-4 text-gray-600 group-hover:text-purple-600 transition">
            <path stroke-linecap="round" stroke-linejoin="round"
                  d="M21.75 6.75v10.5a2.25 2.25 0 0 1-2.25 2.25h-15a2.25 2.25 0 0 1-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0 0 19.5 4.5h-15a2.25 2.25 0 0 0-2.25 2.25m19.5 0v.243a2.25 2.25 0 0 1-1.07 1.916l-7.5 4.615a2.25 2.25 0 0 1-2.36 0L3.32 8.91a2.25 2.25 0 0 1-1.07-1.916V6.75"/>
        </svg>
    </button>

    <!-- INVITE LINK -->
    <div class="relative inline-block">
        <button onclick="event.stopPropagation(); copyLinkFromTable('{{ $meeting->unique_code }}', this)"
                class="p-2 rounded-lg bg-gray-100 hover:bg-indigo-100 transition group shadow-sm hover:shadow"
                title="Copy Invite Link">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-4 h-4 text-gray-600 group-hover:text-indigo-600 transition">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244"/>
            </svg>
        </button>
        <span class="copy-toast absolute -top-9 left-1/2 -translate-x-1/2 bg-gray-900 text-white text-xs px-3 py-1.5 rounded-lg opacity-0 pointer-events-none transition-opacity duration-300 whitespace-nowrap z-50">
            Link Copied!
        </span>
    </div>

    <!-- EDIT -->
    @if($meeting->status === 'upcoming')
        <a href="{{ route('organizer.meetings.edit', $meeting) }}"
           class="p-2 rounded-lg bg-gray-100 hover:bg-green-100 transition group shadow-sm hover:shadow inline-flex"
           title="Edit Meeting">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-4 h-4 text-gray-600 group-hover:text-green-600 transition">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
            </svg>
        </a>
    @else
        <span class="p-2 rounded-lg bg-gray-50 inline-flex cursor-not-allowed" title="Only upcoming meetings can be edited">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-4 h-4 text-gray-300">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Z"/>
            </svg>
        </span>
    @endif

    <!-- CANCEL -->
    @if(in_array($meeting->status, ['upcoming', 'active']))
        <form action="{{ route('organizer.meetings.cancel', $meeting) }}"
              method="POST"
              onsubmit="return confirm('Are you sure you want to cancel this meeting?')">
            @csrf
            @method('PATCH')
            <button type="submit"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow"
                    title="Cancel Meeting">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor"
                     class="w-4 h-4 text-red-500 group-hover:text-red-700 transition">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M6 18 18 6M6 6l12 12"/>
                </svg>
            </button>
        </form>
    @else
        <span class="p-2 rounded-lg bg-gray-50 inline-flex cursor-not-allowed">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-4 h-4 text-gray-300">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M6 18 18 6M6 6l12 12"/>
            </svg>
        </span>
    @endif

</div>

<script>
    // ====== COPY LINK FUNCTION ====== (page-agnostic, stays here — sirf link copy karta hai)
    function copyLinkFromTable(code, btn) {
        const link = `{{ url('/meetings/join') }}/${code}`;

        navigator.clipboard.writeText(link).then(() => {
            const toast = btn.parentElement.querySelector('.copy-toast');
            toast.classList.remove('opacity-0');
            toast.classList.add('opacity-100');

            setTimeout(() => {
                toast.classList.remove('opacity-100');
                toast.classList.add('opacity-0');
            }, 1500);
        }).catch(() => {
            alert('Failed to copy link. Please copy manually: ' + link);
        });
    }
</script>
