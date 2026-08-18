@props([
    'title',
    'value',
    'icon' => 'fa-circle',
    'live' => false,
    'primary' => false,
    'color' => 'blue', // blue | emerald | purple | amber | red
])

@php
    $colors = [
        'blue'    => 'text-blue-600 bg-blue-50 group-hover:bg-blue-100',
        'emerald' => 'text-emerald-600 bg-emerald-50 group-hover:bg-emerald-100',
        'purple'  => 'text-purple-600 bg-purple-50 group-hover:bg-purple-100',
        'amber'   => 'text-amber-600 bg-amber-50 group-hover:bg-amber-100',
        'red'     => 'text-red-500 bg-red-50 group-hover:bg-red-100',
    ];
    $c = $colors[$color] ?? $colors['blue'];
@endphp

<div class="relative bg-white rounded-2xl border border-gray-100 p-5 shadow-sm hover:shadow-xl hover:scale-[1.02] transition-all duration-300 cursor-pointer group overflow-hidden">

    <div class="relative">
        <div class="flex items-start justify-between mb-3">
            <div class="w-9 h-9 flex items-center justify-center rounded-lg transition-all duration-300 {{ $c }}">
                <i class="fa-solid {{ $icon }} text-sm"></i>
            </div>

            @if($live)
                <span class="flex items-center gap-1.5 text-[11px] font-semibold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full">
                    <span class="relative flex h-1.5 w-1.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-1.5 w-1.5 bg-emerald-500"></span>
                    </span>
                    Live
                </span>
            @endif
        </div>

        <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">{{ $title }}</p>
        <h2 class="text-2xl font-bold text-gray-900 mt-1 tabular-nums leading-tight">{{ $value }}</h2>

        <p class="text-xs mt-2 text-gray-500 flex items-center gap-1">
            {{ $slot }}
        </p>
    </div>
</div>
