<x-layouts.app>
    <x-slot:header>
        <x-header.page-title title="Admin Dashboard" />
    </x-slot:header>

    @php
        $nameParts = preg_split('/\s+/', trim($user->name ?? ''));
        $initials = collect($nameParts)
            ->filter()
            ->take(2)
            ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
            ->implode('');

        $initials = $initials ?: 'U';
        $hasProfileImage = !empty($user->image);
    @endphp

    <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <a href="{{ route('admin.users.show', $user) }}"
                   class="inline-flex items-center gap-2 text-sm font-medium text-blue-600">
                    <i class="fa-solid fa-arrow-left text-xs"></i>
                    Back to User Details
                </a>

                <h1 class="mt-2 text-2xl sm:text-3xl font-bold text-gray-800 tracking-tight">
                    Meeting History
                </h1>

                <p class="mt-1 text-sm text-gray-400">
                    View recorded meeting activity for this user.
                </p>
            </div>

            <div class="flex items-center gap-3 px-4 py-3 bg-white border border-gray-200 rounded-2xl shadow-sm">
                @if($hasProfileImage)
                    <div class="w-12 h-12 rounded-full overflow-hidden border-2 border-white shadow-sm bg-white shrink-0">
                        <img src="{{ $user->image_url }}"
                             alt="{{ $user->name }}"
                             class="w-full h-full object-cover rounded-full">
                    </div>
                @else
                    <div class="w-12 h-12 rounded-full bg-blue-600 text-white flex items-center justify-center shrink-0">
                        <span class="text-sm font-semibold tracking-wide">{{ $initials }}</span>
                    </div>
                @endif

                <div class="min-w-0">
                    <p class="font-semibold text-gray-800 truncate">{{ $user->name }}</p>
                    <p class="text-xs text-gray-400">{{ ucfirst($user->role) }}</p>
                </div>
            </div>
        </div>

        <div class="overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-sm">
            @if($meetings->isEmpty())
                <div class="px-4 py-16 text-center">
                    <i class="fa-regular fa-calendar-xmark text-3xl text-gray-300"></i>
                    <p class="mt-3 font-semibold text-gray-700">No meeting history found</p>
                    <p class="mt-1 text-sm text-gray-400">
                        There are no meetings recorded for this user yet.
                    </p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[1100px] text-sm">
                        <thead class="bg-blue-50 border-b border-blue-100">
                        <tr class="text-left text-xs font-semibold uppercase tracking-wider text-blue-700">
                            <th class="px-5 py-4">Meeting</th>
                            <th class="px-4 py-4">Meeting Date</th>
                            <th class="px-4 py-4">
                                {{ $user->role === 'organizer' ? 'Participants' : 'Organizer' }}
                            </th>
                            <th class="px-4 py-4">Joined At</th>
                            <th class="px-4 py-4">Left At</th>
                            <th class="px-4 py-4">Actual Duration</th>
                            <th class="px-4 py-4">
                                {{ $user->role === 'organizer' ? 'Status' : 'Sessions' }}
                            </th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                        @foreach($meetings as $item)
                            @php
                                $meeting = $item->meeting;
                                $seconds = (int) $item->total_seconds;
                                $hours = intdiv($seconds, 3600);
                                $minutes = intdiv($seconds % 3600, 60);
                                $remainingSeconds = $seconds % 60;

                                $duration = $hours > 0
                                    ? "{$hours}h {$minutes}m {$remainingSeconds}s"
                                    : ($minutes > 0
                                        ? "{$minutes}m {$remainingSeconds}s"
                                        : "{$remainingSeconds}s");

                                $sessionCount = $item->sessions->count();
                            @endphp

                            <tr class="hover:bg-gray-50/70 transition">
                                <td class="px-5 py-4">
                                    <p class="font-semibold text-gray-800">
                                        {{ $meeting->title ?? 'Untitled Meeting' }}
                                    </p>
                                    <p class="mt-1 text-xs text-gray-400">
                                        Meeting #{{ $meeting->id }}
                                    </p>
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap text-gray-600">
                                    {{ $meeting->date
                                        ? \Illuminate\Support\Carbon::parse($meeting->date)->format('M d, Y')
                                        : 'Not recorded' }}
                                </td>

                                <td class="px-4 py-4 text-gray-600">
                                    @if($user->role === 'organizer')
                                        {{ $item->participants_count ?? 0 }}
                                    @else
                                        {{ $meeting->organizer?->name ?? 'Not recorded' }}
                                    @endif
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($item->first_joined_at)
                                        <div class="font-semibold text-gray-700">
                                            {{ $item->first_joined_at->format('h:i:s A') }}
                                        </div>
                                        <div class="mt-0.5 text-xs text-gray-400">
                                            {{ $item->first_joined_at->format('M d, Y') }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Not recorded</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($item->last_left_at)
                                        <div class="font-semibold text-gray-700">
                                            {{ $item->last_left_at->format('h:i:s A') }}
                                        </div>
                                        <div class="mt-0.5 text-xs text-gray-400">
                                            {{ $item->last_left_at->format('M d, Y') }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Not recorded</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4 whitespace-nowrap">
                                    @if($seconds > 0)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full
                                                         text-xs font-semibold bg-green-50 text-green-700">
                                                {{ $duration }}
                                            </span>
                                    @else
                                        <span class="text-gray-400">Not recorded</span>
                                    @endif
                                </td>

                                <td class="px-4 py-4">
                                    @if($user->role === 'organizer')
                                        <span class="inline-flex px-2.5 py-1 rounded-full
                                                         text-xs font-semibold bg-gray-100 text-gray-600">
                                                {{ ucfirst($meeting->status ?? 'unknown') }}
                                            </span>
                                    @else
                                        <span class="inline-flex px-2.5 py-1 rounded-full
                                                         text-xs font-semibold bg-blue-50 text-blue-600">
                                                {{ $sessionCount }}
                                            {{ \Illuminate\Support\Str::plural('session', $sessionCount) }}
                                            </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
