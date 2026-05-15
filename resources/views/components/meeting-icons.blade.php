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

    <!-- CANCEL — sirf upcoming ya active cancel ho sakti hai -->
    @if(in_array($meeting->status, ['upcoming', 'active']))
        <form
{{--            action="{{ route('organizer.meetings.cancel', $meeting) }}"--}}
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
