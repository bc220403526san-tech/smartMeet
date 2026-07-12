@props(['meeting'])

<div class="flex gap-2">

    <!-- VIEW -->
    <a href="{{ route('admin.meetings.show', $meeting) }}"
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

    <!-- FLAG / UNFLAG — sirf jab meeting "upcoming" ho, ya already flagged ho (unflag ke liye) -->
    @if($meeting->status === 'upcoming' || $meeting->status === 'flagged')
        <form action="{{ route('admin.meetings.flag', $meeting) }}" method="POST" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit"
                    title="{{ $meeting->status === 'flagged' ? 'Remove Flag' : 'Flag for Review' }}"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-orange-100 transition group shadow-sm hover:shadow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="{{ $meeting->status === 'flagged' ? 'currentColor' : 'none' }}"
                     viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"
                     class="w-4 h-4 {{ $meeting->status === 'flagged' ? 'text-orange-500' : 'text-gray-600 group-hover:text-orange-500' }} transition">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M3 3v1.5M3 21V5.25m0 0A2.25 2.25 0 0 1 5.25 3h13.5A2.25 2.25 0 0 1 21 5.25v6.5a2.25 2.25 0 0 1-2.25 2.25H5.25A2.25 2.25 0 0 0 3 16.25"/>
                </svg>
            </button>
        </form>
    @endif

    <!-- CANCEL MEETING — sirf tab jab abhi cancel/complete nahi hui -->
    @if(!in_array($meeting->status, ['cancelled', 'completed']))
        <form action="{{ route('admin.meetings.cancel', $meeting) }}" method="POST"
              onsubmit="return confirm('Cancel this meeting? All participants will be notified.')" class="inline">
            @csrf
            @method('PATCH')
            <button type="submit" title="Cancel Meeting"
                    class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                     stroke-width="1.5" stroke="currentColor"
                     class="w-4 h-4 text-gray-600 group-hover:text-red-600 transition">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9.75 9.75l4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                </svg>
            </button>
        </form>
    @endif

    <!-- DELETE -->
    <form action="{{ route('admin.meetings.destroy', $meeting) }}" method="POST"
          onsubmit="return confirm('Delete this meeting?')" class="inline">
        @csrf
        @method('DELETE')
        <button type="submit" title="Delete"
                class="p-2 rounded-lg bg-gray-100 hover:bg-red-100 transition group shadow-sm hover:shadow">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                 stroke-width="1.5" stroke="currentColor"
                 class="w-4 h-4 text-red-500 group-hover:text-red-700 transition">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
            </svg>
        </button>
    </form>

</div>
