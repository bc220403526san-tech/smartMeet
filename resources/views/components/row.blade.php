@props(['title', 'date', 'time', 'organizer', 'image', 'status'])

@php
    $colors = [
        'Upcoming'     => 'bg-blue-100 text-blue-600',
        'In Progress'  => 'bg-yellow-100 text-yellow-600',
        'Completed'    => 'bg-gray-200 text-gray-600',
        'In-Completed' => 'bg-red-200 text-red-600',
        'Active'       => 'bg-green-200 text-green-600',
    ];
@endphp

{{-- Mobile: Card layout | Desktop: Grid layout --}}

{{-- DESKTOP (md+) --}}
<div class="hidden md:grid md:grid-cols-5 items-center py-4 px-3 hover:bg-gray-50 transition">

    <!-- TITLE -->
    <div>
        <p class="font-semibold text-sm">{{ $title }}</p>
        <p class="text-xs text-gray-500">Agenda</p>
    </div>

    <!-- DATE TIME -->
    <div class="text-sm">
        {{ $date }} <br>
        <span class="text-xs text-gray-500">{{ $time }}</span>
    </div>

    <!-- ORGANIZER -->
    <div class="flex items-center gap-2">
        <img src="{{ asset($image) }}" class="w-7 h-7 rounded-full object-cover">
        <span class="text-sm">{{ $organizer }}</span>
    </div>

    <!-- STATUS -->
    <div>
        <span class="{{ $colors[$status] ?? 'bg-gray-100 text-gray-600' }} text-xs px-2 py-1 rounded-full">
            {{ $status }}
        </span>
    </div>

    <!-- ACTIONS -->
    <div>
        {{ $slot }}
    </div>

</div>

{{-- MOBILE (card style) --}}
<div class="md:hidden p-4 hover:bg-gray-50 transition">

    <!-- Top: Title + Status -->
    <div class="flex justify-between items-start mb-3">
        <div>
            <p class="font-semibold text-sm">{{ $title }}</p>
            <p class="text-xs text-gray-500 mt-0.5">Agenda</p>
        </div>
        <span class="{{ $colors[$status] ?? 'bg-gray-100 text-gray-600' }} text-xs px-2 py-1 rounded-full shrink-0 ml-2">
            {{ $status }}
        </span>
    </div>

    <!-- Middle: Date + Organizer -->
    <div class="flex items-center justify-between mb-3">
        <div class="text-xs text-gray-500">
            <p>{{ $date }}</p>
            <p>{{ $time }}</p>
        </div>
        <div class="flex items-center gap-2">
            <img src="{{ asset($image) }}" class="w-6 h-6 rounded-full object-cover">
            <span class="text-xs text-gray-700">{{ $organizer }}</span>
        </div>
    </div>

    <!-- Bottom: Actions -->
    <div class="flex gap-2">
        {{ $slot }}
    </div>

</div>
