@props(['showName' => true])

<div class="flex items-center gap-2 cursor-pointer">
    <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-300">
        @auth
            <img src="{{ Auth::user()->image_url }}"
                 class="w-full h-full object-cover"
                 alt="{{ Auth::user()->name }}">
        @endauth
    </div>

    @if($showName)
        <div class="hidden sm:block">
            <p class="text-sm font-semibold">{{ Auth::user()?->name }}</p>
            <p class="text-xs text-gray-500 capitalize">{{ Auth::user()?->role }}</p>
        </div>
    @endif
</div>
