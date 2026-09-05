<x-layouts.app>
    <x-header.page-title title="Admin Dashboard" />

    <div class="h-[calc(100vh-5rem)] overflow-y-auto">
        <div class="p-3 sm:p-4 bg-gray-50 rounded-2xl m-2 mt-0 space-y-4">

            <!-- PAGE HEADER -->
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
                    {{ $user->name }} · {{ ucfirst($user->role) }}
                </p>
            </div>

            <!-- HISTORY TABLE -->
            <div class="overflow-hidden bg-white border border-gray-200 rounded-2xl shadow-sm">
                @if($meetings->isEmpty())
                    <div class="px-4 py-16 text-center">
                        <i class="fa-regular fa-calendar-xmark text-3xl text-gray-300"></i>

                        <p class="mt-3 font-semibold text-gray-700">
                            No meeting history found
                        </p>

                        <p class="mt-1 text-sm text-gray-400">
                            There are no meetings recorded for this user yet.
                        </p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full min-w-[980px] text-sm">
                            <thead class="bg-blue-50 border-b border-blue-100">
                            <tr class="text-left text-xs font-semibold uppercase tracking-wider text-blue-700">
                                <th class="px-5 py-4">Meeting</th>
                                <th class="px-4 py-4">Date</th>

                                <th class="px-4 py-4">
                                    {{ $user->role === 'organizer'
                                        ? 'Participants'
                                        : 'Organizer' }}
                                </th>

                                <th class="px-4 py-4">Joined</th>
                                <th class="px-4 py-4">Left</th>
                                <th class="px-4 py-4">Duration</th>

                                <th class="px-4 py-4">
                                    {{ $user->role === 'organizer'
                                        ? 'Status'
                                        : 'Sessions' }}
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
                                        ? "{$hours}h {$minutes}m"
                                        : (
                                            $minutes > 0
                                                ? "{$minutes}m {$remainingSeconds}s"
                                                : "{$remainingSeconds}s"
                                        );

                                    $sessionCount = $item->sessions->count();
                                @endphp

                                <tr class="hover:bg-gray-50/70 transition">
                                    <td class="px-5 py-4">
                                        <p class="font-semibold text-gray-800">
                                            {{ $meeting->title ?? 'Untitled Meeting' }}
                                        </p>

                                        <p class="mt-1 text-xs text-gray-400">
                                            #{{ $meeting->id }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap text-gray-600">
                                        {{ $meeting->date
                                            ? \Illuminate\Support\Carbon::parse($meeting->date)->format('M d, Y')
                                            : '—' }}
                                    </td>

                                    <td class="px-4 py-4 text-gray-600">
                                        @if($user->role === 'organizer')
                                            {{ $item->participants_count ?? 0 }}
                                        @else
                                            {{ $meeting->organizer?->name ?? '—' }}
                                        @endif
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap text-gray-600">
                                        {{ $item->first_joined_at?->format('h:i A') ?? '—' }}
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap text-gray-600">
                                        {{ $item->last_left_at?->format('h:i A') ?? '—' }}
                                    </td>

                                    <td class="px-4 py-4 whitespace-nowrap font-medium text-gray-700">
                                        {{ $seconds > 0 ? $duration : '—' }}
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
    </div>
</x-layouts.app>
