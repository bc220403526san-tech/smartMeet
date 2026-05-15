{{-- resources/views/components/sidebar-link.blade.php --}}
@props(['route', 'label', 'active' => false])

<li class="{{ $active
                ? 'bg-blue-100 text-blue-600'
                : 'text-gray-600 hover:bg-gray-100' }}
           p-2 rounded-lg flex items-center gap-1 text-sm
           transition-colors duration-150">

    <a href="{{ route($route) }}" class="flex items-center gap-2 w-full">

        <svg xmlns="http://www.w3.org/2000/svg"
             fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5 flex-shrink-0">
            {{ $icon }}
        </svg>

        <span>{{ $label }}</span>

        @if($active)
            <span class="ml-auto w-1.5 h-1.5 bg-blue-500 rounded-full animate-bounce [animation-duration:0.5s]"></span>
        @endif

    </a>
</li>

