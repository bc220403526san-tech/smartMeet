@props([
    'title' => '',
    'subtitle' => '',
    'image' => ''
])

<!-- LEFT SIDEBAR / HERO SECTION -->
<div class="auth-hero w-full md:w-1/2 relative flex flex-col justify-center items-center bg-white overflow-hidden
            rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none
            shadow-lg
            min-h-[45vh] sm:min-h-[50vh] md:min-h-[calc(100vh-3rem)]">

    <!-- Logo -->
    <div class="absolute top-4 left-4 sm:left-6 flex items-center gap-2 z-20">
        <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet Logo"
             class="w-8 h-8 sm:w-10 sm:h-10 object-contain">
        <h1 class="text-md sm:text-lg font-semibold text-blue-600">SmartMeet</h1>
    </div>

    <!-- Image Area -->
    <div class="w-full flex justify-center items-center px-4 pt-20 pb-6 sm:pt-24 md:pt-16 md:pb-10">
        <div class="relative w-full max-w-[280px] sm:max-w-[350px] md:max-w-[430px] lg:max-w-[470px]
                    aspect-square flex items-center justify-center">

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

    <!-- ONLY END CURVE / DESIGN -->
    <div class="hidden md:block absolute top-0 -right-[38px] w-[76px] h-full z-30 pointer-events-none">
        <svg viewBox="0 0 76 1000" preserveAspectRatio="none" class="w-full h-full">
            <path
                d="M0,0
                   C58,120 18,230 54,340
                   C84,430 8,520 48,620
                   C82,710 22,825 76,1000
                   L0,1000 Z"
                fill="#ffffff"/>
        </svg>
    </div>

</div>
