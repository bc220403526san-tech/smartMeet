@props([
    'user',
    'size' => 'md',
    'ring' => true,
])

@php
    /*
     * Exact rule:
     * 1) uploaded/local image
     * 2) social/provider avatar
     * 3) initials
     */
    $hasImage = !empty($user?->image);
    $hasAvatar = !empty($user?->avatar);
    $hasRealProfileImage = $hasImage || $hasAvatar;

    $nameParts = preg_split('/\s+/', trim($user?->name ?: 'User'));

    $initials = collect($nameParts)
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->implode('');

    $initials = $initials ?: 'U';

    $sizeClasses = match ($size) {
        'xs' => 'w-8 h-8 text-xs',
        'sm' => 'w-10 h-10 text-sm',
        'lg' => 'w-20 h-20 sm:w-24 sm:h-24 text-2xl sm:text-3xl',
        'xl' => 'w-24 h-24 sm:w-28 sm:h-28 text-3xl sm:text-4xl',
        default => 'w-12 h-12 text-base',
    };

    $ringClasses = $ring ? 'ring-2 ring-gray-100' : '';
@endphp

@if($hasRealProfileImage)
    <div {{ $attributes->merge([
        'class' => "{$sizeClasses} {$ringClasses} rounded-full overflow-hidden shrink-0 bg-white"
    ]) }}>
        <img
            src="{{ $user->image_url }}"
            alt="{{ $user->name }}"
            class="w-full h-full rounded-full object-cover"
        >
    </div>
@else
    <div {{ $attributes->merge([
        'class' => "{$sizeClasses} {$ringClasses} rounded-full shrink-0 bg-blue-600 text-white flex items-center justify-center font-semibold tracking-wide"
    ]) }}>
        {{ $initials }}
    </div>
@endif
