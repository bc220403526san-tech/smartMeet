@props([
    'title' => '',
    'subtitle' => '',
    'image' => ''
])

<!-- LEFT HERO SECTION -->
<div class="auth-hero w-full lg:w-[52%] relative flex flex-col bg-white overflow-hidden
            rounded-t-[28px] lg:rounded-l-[28px] lg:rounded-tr-none
            min-h-[500px] sm:min-h-[560px] lg:min-h-[calc(100vh-2rem)]">

    <!-- Decorative background -->
    <div class="pointer-events-none absolute inset-0 overflow-hidden">
        <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-blue-50"></div>
        <div class="absolute top-[22%] -right-24 w-52 h-52 rounded-full bg-indigo-50/80"></div>
        <div class="absolute -bottom-28 left-[18%] w-80 h-80 rounded-full bg-sky-50/80"></div>

        <div class="absolute top-[18%] left-[8%] w-2 h-2 rounded-full bg-blue-300"></div>
        <div class="absolute top-[28%] right-[14%] w-3 h-3 rounded-full bg-indigo-200"></div>
        <div class="absolute bottom-[16%] left-[12%] w-2.5 h-2.5 rounded-full bg-sky-300"></div>
    </div>

    <!-- Logo -->
    <div class="relative z-20 flex items-center gap-2.5 px-5 pt-5 sm:px-7 sm:pt-7 lg:px-9 lg:pt-8">
        <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-2xl bg-blue-50 flex items-center justify-center shadow-sm ring-1 ring-blue-100">
            <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet Logo"
                 class="w-8 h-8 sm:w-9 sm:h-9 object-contain">
        </div>
        <h1 class="text-lg sm:text-xl font-bold tracking-tight text-blue-600">SmartMeet</h1>
    </div>

    <!-- Hero content -->
    <div class="relative z-10 flex-1 flex flex-col justify-center px-5 sm:px-8 lg:px-12 xl:px-16 py-7 sm:py-9 lg:py-6">
        <div class="w-full max-w-[620px] mx-auto">

            <!-- Text -->
            <div class="text-center lg:text-left mb-4 sm:mb-5 lg:mb-2">
                <span class="inline-flex items-center gap-2 rounded-full bg-blue-50 border border-blue-100
                             px-3 py-1.5 text-[11px] sm:text-xs font-semibold text-blue-600 mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                    Connect • Meet • Collaborate
                </span>

                <h2 class="text-2xl sm:text-3xl xl:text-[34px] leading-tight font-bold text-slate-800">
                    {{ $title }}
                </h2>
                <p class="text-sm sm:text-base text-slate-500 font-medium mt-2">
                    {{ $subtitle }}
                </p>
            </div>

            <!-- Illustration: stable on tablet/mobile, not excessively reduced -->
            <div class="hero-illustration relative w-full min-h-[285px] sm:min-h-[340px] lg:min-h-[390px]
                        flex items-center justify-center">
                <div class="absolute w-[72%] aspect-square rounded-full bg-gradient-to-br from-blue-50 via-indigo-50/70 to-transparent blur-sm"></div>

                <img src="{{ asset($image) }}"
                     alt="Hero Image"
                     class="relative z-10 w-auto h-auto object-contain
                            max-h-[300px] sm:max-h-[360px] md:max-h-[390px]
                            lg:max-h-[430px] xl:max-h-[480px]
                            max-w-[94%] sm:max-w-[88%] lg:max-w-[96%]">
            </div>

            <!-- Small feature chips -->
            <div class="hidden sm:flex flex-wrap justify-center lg:justify-start gap-2 mt-2">
                <span class="px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-xs text-slate-500">
                    HD Meetings
                </span>
                <span class="px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-xs text-slate-500">
                    Live Collaboration
                </span>
                <span class="px-3 py-1.5 rounded-full bg-slate-50 border border-slate-100 text-xs text-slate-500">
                    Secure & Simple
                </span>
            </div>
        </div>
    </div>

    <!-- Curved divider decoration (desktop only) -->
    <div class="hidden lg:block pointer-events-none absolute top-0 right-0 h-full w-16 xl:w-20 z-20 overflow-hidden">
        <svg viewBox="0 0 80 1000" preserveAspectRatio="none" class="w-full h-full">
            <path d="M80,0
                     C28,115 30,205 66,295
                     C94,365 92,445 48,515
                     C9,578 13,675 58,742
                     C94,797 94,890 34,1000
                     L80,1000 Z"
                  fill="#eff6ff"/>
            <path d="M79,0
                     C27,115 29,205 65,295
                     C93,365 91,445 47,515
                     C8,578 12,675 57,742
                     C93,797 93,890 33,1000"
                  fill="none"
                  stroke="#bfdbfe"
                  stroke-width="2"/>
        </svg>
    </div>
</div>
