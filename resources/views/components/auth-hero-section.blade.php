@props([
    'title' => '',
    'subtitle' => '',
    'image' => ''
])

<!-- LEFT SIDEBAR / HERO SECTION -->
<div class="w-full md:w-1/2 relative flex flex-col justify-center items-center bg-white overflow-hidden
                rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none
                shadow-lg md:shadow-[20px_0_30px_rgba(1,1,1,0.15)]
                border-l-0 border-l-4 border-blue-700
                min-h-[45vh] sm:min-h-[50vh] md:min-h-[calc(100vh-3rem)]">

    <!-- Logo -->
    <div class="absolute top-4 left-4 sm:left-6 flex items-center gap-2 z-20">
        <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet Logo"
             class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
        <h1 class="text-md sm:text-lg font-semibold text-blue-600">SmartMeet</h1>
    </div>

    <!-- Image -->
    <div class="w-full flex justify-center items-center px-4 pt-20 pb-6 sm:pt-24 md:pt-16 md:pb-10">
        <div class="relative w-full max-w-[260px] sm:max-w-[340px] md:max-w-[440px] lg:max-w-[500px] aspect-square flex items-center justify-center">

            <img src="{{ asset($image) }}"
                 alt="Hero Image"
                 class="w-full h-full object-contain">

            <!-- Desktop text over image -->
            <div class="hidden md:block absolute top-6 left-2 lg:left-4 z-10">
                <h2 class="text-xl lg:text-2xl font-bold text-gray-800 mb-2">
                    {{ $title }}
                </h2>
                <p class="text-sm lg:text-base text-gray-600 font-medium">
                    {{ $subtitle }}
                </p>
            </div>

        </div>
    </div>

    <!-- Mobile text below image -->
    <div class="md:hidden text-center px-4 pb-8">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">
            {{ $title }}
        </h2>
        <p class="text-sm sm:text-base text-gray-500 mt-1">
            {{ $subtitle }}
        </p>
    </div>

</div>
