@props([
    'title',
    'value',
    'color' => 'blue',
    'icon' => 'fa-circle'
])

@php
    $colors = [
        'blue' => ['bg-blue-50','border-blue-500','text-blue-600','bg-blue-100'],
        'green' => ['bg-green-50','border-green-500','text-green-600','bg-green-100'],
        'purple' => ['bg-purple-50','border-purple-500','text-purple-600','bg-purple-100'],
        'yellow' => ['bg-yellow-50','border-yellow-500','text-yellow-600','bg-yellow-100'],
        'orange' => ['bg-orange-50','border-orange-500','text-orange-600','bg-orange-100'],
    ];

    $c = $colors[$color] ?? $colors['blue'];
@endphp

<div class="{{ $c[0] }} p-4 rounded-xl border-l-4 {{ $c[1] }} shadow-lg hover:shadow-xl transition duration-300">

    {{-- ICON --}}
    <div class="w-10 h-10 flex items-center justify-center {{ $c[3] }} rounded-md mb-3 {{ $c[2] }}">
        <i class="fa-solid {{ $icon }} text-lg"></i>
    </div>

    {{-- TITLE --}}
    <p class="text-gray-500 text-sm uppercase">{{ $title }}</p>

    {{-- VALUE --}}
    <h2 class="text-xl font-bold">{{ $value }}</h2>

    {{-- EXTRA TEXT --}}
    <p class="text-sm mt-1 text-gray-600">
        {{ $slot }}
    </p>

</div>
