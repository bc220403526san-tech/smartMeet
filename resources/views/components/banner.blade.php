@props(['title', 'desc', 'actionButton' => null, 'actionRoute' => null])

<div class="w-full h-40 sm:h-52 md:h-64 rounded-2xl overflow-hidden shadow-xl relative mb-4">

    <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c"
         class="absolute inset-0 w-full h-full object-cover scale-105">

    <div class="absolute inset-0 bg-gradient-to-r from-[#041427]/95 via-[#052a4a]/85 to-[#0b4f7a]/70"></div>

    <div class="absolute top-[-50px] left-[-50px] w-80 h-80 bg-blue-400/30 rounded-full blur-3xl"></div>
    <div class="absolute bottom-[-50px] right-[-50px] w-80 h-80 bg-cyan-300/30 rounded-full blur-3xl"></div>

    <!-- Content -->
    <div class="relative h-full flex items-center px-5 sm:px-8 md:px-10">

        <div class="max-w-xl">

            <h1 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-semibold text-white tracking-wide">
                {{ $title }}
            </h1>

            <p class="text-blue-100 mt-2 sm:mt-3 text-xs sm:text-sm md:text-base leading-relaxed hidden sm:block">
                {{ $desc }}
            </p>

            @if($actionButton && $actionRoute)
                <a href="{{ route($actionRoute) }}"
                   class="inline-block mt-3 sm:mt-5 md:mt-6 bg-white text-blue-700 px-4 sm:px-5 md:px-6 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm font-semibold shadow-md hover:bg-blue-50 hover:scale-105 transition">
                    {{ $actionButton }}
                </a>
            @endif

        </div>

    </div>
</div>
