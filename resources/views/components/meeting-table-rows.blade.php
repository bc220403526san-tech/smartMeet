@forelse($meetings as $meeting)
    @php
        $statusConfig = [
            'upcoming'   => ['bg' => 'bg-blue-50 text-blue-700 border-blue-100',   'dot' => 'bg-blue-500',                      'label' => 'Upcoming'],
            'active'     => ['bg' => 'bg-green-50 text-green-700 border-green-100', 'dot' => 'bg-green-500 animate-pulse',        'label' => 'Active'],
            'completed'  => ['bg' => 'bg-gray-100 text-gray-600 border-gray-200',   'dot' => 'bg-gray-400',                      'label' => 'Completed'],
            'cancelled'  => ['bg' => 'bg-red-50 text-red-600 border-red-100',       'dot' => 'bg-red-400',                       'label' => 'Cancelled'],
            'flagged'    => ['bg' => 'bg-orange-50 text-orange-600 border-orange-100', 'dot' => 'bg-orange-400',                 'label' => 'Flagged'],
        ];
        $s = $statusConfig[$meeting->status] ?? ['bg' => 'bg-gray-100 text-gray-500 border-gray-200', 'dot' => 'bg-gray-400', 'label' => ucfirst($meeting->status)];
    @endphp
    <tr class="hover:bg-blue-50/30 transition duration-200" data-meeting-id="{{ $meeting->id }}">
        <td class="px-5 py-4">
            <p class="font-semibold text-gray-800">{{ $meeting->title }}</p>
            <p class="text-xs text-gray-400 mt-1">IM # M-{{ $meeting->id }}</p>
        </td>
        <td class="px-5 py-4">
            <p class="font-medium text-gray-700">{{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}</p>
        </td>
        <td class="px-5 py-4">
            <div class="inline-flex items-center bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-xl">
                <span class="font-medium text-gray-700">{{ $meeting->participants->count() }} Participants</span>
            </div>
        </td>
        <td class="px-5 py-4">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold border {{ $s['bg'] }}">
                <span class="w-2 h-2 rounded-full {{ $s['dot'] }}"></span>
                {{ $s['label'] }}
            </span>
        </td>
        <td class="px-5 py-4">
            <x-meeting-attend-button :meeting="$meeting" />
        </td>
        <td class="px-5 py-4">
            <x-meeting-icons :meeting="$meeting" />
        </td>
    </tr>
@empty
    <tr>
        <td colspan="6" class="px-5 py-12 text-center text-gray-400 text-sm">
            <i class="fa fa-calendar-xmark text-4xl mb-3 block"></i>
            No meetings found.
            <div class="mt-3">
                <a href="{{ route('organizer.meetings.create') }}" class="inline-block text-blue-600 text-sm hover:underline">
                    + Create your first meeting
                </a>
            </div>
        </td>
    </tr>
@endforelse
