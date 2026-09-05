@props([
    'user',
    'size' => 'md',
    'ring' => true,
])

@php
    $sizeClasses = match ($size) {
        'xs' => 'w-8 h-8',
        'sm' => 'w-10 h-10',
        'lg' => 'w-20 h-20 sm:w-24 sm:h-24',
        'xl' => 'w-24 h-24 sm:w-28 sm:h-28',
        default => 'w-12 h-12',
    };

    $ringClasses = $ring ? 'ring-2 ring-gray-100' : '';
@endphp

<div {{ $attributes->merge([
    'class' => "{$sizeClasses} {$ringClasses} rounded-full overflow-hidden shrink-0 bg-white"
]) }}>
    <img
        src="{{ $user->image_url }}"
        alt="{{ $user->name }}"
        class="block w-full h-full rounded-full object-cover"
        loading="lazy"
        referrerpolicy="no-referrer"
    >
</div>
