@props(['meeting'])
@if($meeting->status === 'active')
    <a href="{{ route('organizer.meetings.attend', $meeting->id) }}"
       class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-600 hover:bg-green-700 active:scale-95 text-white text-xs font-semibold rounded-lg transition-all duration-150 shadow-sm">
        <i class="fa fa-video text-[11px]"></i>
        Attend
    </a>
@elseif($meeting->status === 'upcoming')
    <span title="Meeting hasn't started yet"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-400 border border-gray-200 text-xs font-medium rounded-lg cursor-not-allowed select-none">
        <i class="fa fa-clock text-[11px]"></i>
        Upcoming
    </span>
@elseif($meeting->status === 'completed')
    <span title="This meeting has ended"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 text-gray-400 border border-gray-200 text-xs font-medium rounded-lg cursor-not-allowed select-none">
        <i class="fa fa-circle-check text-[11px]"></i>
        Ended
    </span>
@elseif($meeting->status === 'cancelled')
    <span title="This meeting was cancelled"
          class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-50 text-red-300 border border-red-100 text-xs font-medium rounded-lg cursor-not-allowed select-none">
        <i class="fa fa-xmark text-[11px]"></i>
        Cancelled
    </span>
@else
    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-yellow-50 text-yellow-400 border border-yellow-100 text-xs font-medium rounded-lg cursor-not-allowed select-none">
        <i class="fa fa-flag text-[11px]"></i>
        {{ ucfirst($meeting->status) }}
    </span>
@endif
