<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    @vite('resources/css/app.css')
</head>

<body class="bg-[#f4f5f7]">

<input type="checkbox" id="sidebar-toggle">

<div class="layout-wrapper flex h-screen overflow-hidden">

    <label for="sidebar-toggle" class="sidebar-overlay"></label>
    <x-sidebar.organizer-menu />

    <div class="flex-1 flex flex-col min-w-0 m-3 ml-0">

        <x-header.page-title title="Organizer Dashboard" />

        <div class="flex-1 overflow-y-auto mt-3 p-4 sm:p-6 bg-gray-50 rounded-lg">

            <!-- TOP BAR -->
            <div class="flex items-center justify-between mb-5">

                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <a href="{{ route('organizer.meetings.index') }}"
                       class="hover:text-blue-600 transition flex items-center gap-1">
                        <i class="fa-solid fa-arrow-left text-xs"></i>
                        Back to Meetings
                    </a>
                    <span>/</span>
                    <span class="text-gray-700 font-medium">Meeting Details</span>
                </div>

                <div class="flex items-center gap-2">

                    {{-- Edit — sirf upcoming --}}
                    @if($meeting->status === 'upcoming')
                        <a href="{{ route('organizer.meetings.edit', $meeting) }}"
                           class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-200
                                  rounded-lg hover:bg-gray-50 hover:border-gray-300 transition shadow-sm">
                            Edit Meeting
                        </a>
                    @endif

                    {{-- Cancel — upcoming ya active --}}
                    @if(in_array($meeting->status, ['upcoming', 'active']))
                        <form
                            action="{{ route('organizer.meetings.cancel', $meeting) }}"
                              method="POST"
                              onsubmit="return confirm('Cancel this meeting?')">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                    class="px-4 py-2 text-sm font-medium text-red-500 bg-white
                                           border border-red-200 rounded-lg hover:bg-red-50 transition shadow-sm">
                                <i class="fa-solid fa-xmark text-xs mr-1"></i>
                                Cancel Meeting
                            </button>
                        </form>
                    @endif

                    {{-- Status Badge --}}
                    <span class="px-4 py-2 text-sm font-semibold rounded-lg
                        {{ $meeting->status == 'upcoming'  ? 'bg-blue-50 text-blue-600'     : '' }}
                        {{ $meeting->status == 'active'    ? 'bg-green-50 text-green-600'   : '' }}
                        {{ $meeting->status == 'completed' ? 'bg-gray-100 text-gray-600'    : '' }}
                        {{ $meeting->status == 'cancelled' ? 'bg-red-50 text-red-500'       : '' }}
                        {{ $meeting->status == 'flagged'   ? 'bg-yellow-50 text-yellow-600' : '' }}">
                        {{ ucfirst($meeting->status) }}
                    </span>

                </div>
            </div>

            <!-- MAIN GRID -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                <!-- LEFT -->
                <div class="lg:col-span-2 flex flex-col gap-5">

                    <!-- MEETING INFO CARD -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-l-4 border-blue-500">

                            <!-- BADGES -->
                            <div class="flex flex-wrap items-center gap-3 mb-4">
                                @if($meeting->status === 'upcoming')
                                    @php
                                        $tz       = $meeting->timezone ?? 'Asia/Karachi';
                                        $today    = \Carbon\Carbon::now($tz)->startOfDay();
                                        $meetDate = \Carbon\Carbon::parse($meeting->date, $tz)->startOfDay();
                                        $daysLeft = $today->diffInDays($meetDate, false);
                                    @endphp

                                    <span class="flex items-center gap-1.5 text-xs font-medium
                                      text-orange-500 bg-orange-50 px-3 py-1 rounded-full">
                                      <i class="fa-regular fa-clock text-xs"></i>
                                @if($daysLeft == 0)
                                            Today
                                        @elseif($daysLeft == 1)
                                            Tomorrow
                                        @elseif($daysLeft > 0)
                                            In {{ $daysLeft }} Days
                                        @else
                                            Overdue
                                        @endif
                                     </span>
                                @endif

                            </div>

                            <!-- TITLE -->
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-800 mb-3">
                                {{ $meeting->title }}
                            </h1>

                            <!-- DESCRIPTION -->
                            <p class="text-sm text-gray-500 leading-relaxed mb-6">
                                {{ $meeting->description ?? 'No description provided.' }}
                            </p>

                            <!-- INFO GRID -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                                <!-- DATE -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-blue-100 flex items-center justify-center">
                                            <i class="fa-regular fa-calendar text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 font-semibold uppercase">Date</p>
                                            <p class="text-sm font-semibold text-gray-700 mt-1">
                                                {{ \Carbon\Carbon::parse($meeting->date)->format('F d, Y') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- TIME -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-indigo-100 flex items-center justify-center">
                                            <i class="fa-regular fa-clock text-indigo-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 font-semibold uppercase">Time</p>
                                            <p class="text-sm font-semibold text-gray-700 mt-1">
                                                {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- DURATION -->
                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                                            <i class="fa-regular fa-hourglass text-orange-500"></i>
                                        </div>
                                        <div>
                                            <p class="text-xs text-gray-400 font-semibold uppercase">Duration</p>
                                            <p class="text-sm font-semibold text-gray-700 mt-1">
                                                {{ $meeting->duration }} Minutes
                                            </p>
                                        </div>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>

                    <!-- AGENDA CARD -->
                    @php
                        $agendaItems = json_decode($meeting->agenda, true) ?? [];
                    @endphp

                    @if(count($agendaItems) > 0)
                        <div class="flex flex-col gap-3">
                            @foreach($agendaItems as $index => $item)
                                <div class="flex items-start gap-4 p-4 rounded-xl
                                {{ $index == 0 ? 'border border-blue-100 bg-blue-50/40' : 'border border-gray-100
                                hover:bg-blue-50/40 bg-gray-200 transition' }}">

                                <div class="w-9 h-9 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0
                                  {{ $index == 0 ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600' }}
                                   text-sm font-bold">
                                   {{ $index + 1 }}
                                    </div>

                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-gray-800">
                                            {{ $item['title'] }}
                                        </p>
                                        @if(!empty($item['description']))
                                            <p class="text-xs text-gray-500 mt-2 leading-relaxed">
                                                {{ $item['description'] }}
                                            </p>
                                        @endif
                                    </div>

                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-sm text-gray-400 text-center py-6">No agenda added.</p>
                    @endif

                </div>

                <!-- RIGHT: Participants -->
                <div class="lg:col-span-1 flex flex-col">
                    <div class="relative overflow-hidden bg-white rounded-3xl shadow-lg border border-blue-100 p-5 flex flex-col h-full">

                        <div class="absolute top-0 left-0 w-full h-28 bg-gradient-to-r from-blue-600 via-indigo-500 to-cyan-400 opacity-95"></div>
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/20 rounded-full blur-3xl"></div>
                        <div class="absolute top-16 -left-8 w-28 h-28 bg-cyan-300/20 rounded-full blur-2xl"></div>

                        <div class="relative z-10 flex flex-col h-full">

                            <!-- Header -->
                            <div class="flex items-center justify-between mb-6">
                                <div>
                                    <p class="text-xs uppercase tracking-[3px] text-blue-100 font-semibold mb-1">
                                        Team Members
                                    </p>
                                    <h2 class="text-2xl font-bold text-white leading-tight">Participants</h2>
                                    <p class="text-sm text-blue-100 mt-1">Active meeting members</p>
                                </div>

                                <div class="bg-white/20 backdrop-blur-md border border-white/20
                                            rounded-2xl px-4 py-3 text-center min-w-[80px]">
                                    <p class="text-2xl font-bold text-white">
                                        {{ $meeting->participants->count() }}
                                    </p>
                                    <p class="text-[10px] uppercase tracking-widest text-blue-100">Total</p>
                                </div>
                            </div>

                            <!-- WHITE INNER CARD -->
                            <div class="bg-white rounded-2xl p-4 shadow-inner flex-1">

                                @if($meeting->participants->count() > 0)
                                    <div class="flex flex-col divide-y divide-gray-100">

                                        {{-- Organizer --}}
                                        <div class="flex items-center gap-3 py-3 hover:bg-blue-50 rounded-xl px-2 transition">
                                            <div class="relative w-11 h-11 flex-shrink-0">
                                                <img src="{{ $meeting->organizer->image_url }}"
                                                     class="w-11 h-11 rounded-full object-cover ring-2 ring-blue-100">
                                                <span class="absolute bottom-0 right-0 w-[18px] h-[18px]
                                                             bg-gradient-to-r from-pink-400 to-red-400
                                                             rounded-full flex items-center justify-center
                                                             border-2 border-white shadow-sm">
                                                    <i class="fa-solid fa-crown text-white text-[7px]"></i>

                                                </span>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm font-semibold text-gray-800 truncate">
                                                    {{ $meeting->organizer->name }}
                                                </p>
                                                <p class="text-[10px] font-semibold text-blue-500 uppercase tracking-[2px] mt-0.5">
                                                    Meeting Organizer
                                                </p>
                                            </div>
                                        </div>

                                        {{-- Participants --}}
                                        @foreach($meeting->participants->take(4) as $participant)
                                            <div class="flex items-center gap-3 py-3 hover:bg-blue-50 rounded-xl px-2 transition">
                                                <div class="w-11 h-11 flex-shrink-0">
                                                    <img src="{{ $participant->user->image_url }}"
                                                         class="w-11 h-11 rounded-full object-cover ring-2 ring-gray-100">
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-sm font-semibold text-gray-800 truncate">
                                                        {{ $participant->user->name }}
                                                    </p>
                                                    <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-[2px] mt-0.5 truncate">
                                                        {{ ucfirst($participant->user->role) }}
                                                    </p>
                                                </div>
                                            </div>
                                        @endforeach

                                    </div>

{{--                                    @if($meeting->participants->count() > 4)--}}
{{--                                        <button class="mt-5 w-full bg-gradient-to-r from-blue-600 to-indigo-500--}}
{{--                                                       hover:from-blue-700 hover:to-indigo-600 text-white--}}
{{--                                                       text-sm font-semibold py-3 rounded-xl transition-all--}}
{{--                                                       duration-300 shadow-md hover:shadow-lg">--}}
{{--                                            View All {{ $meeting->participants->count() }} Participants--}}
{{--                                        </button>--}}
{{--                                    @endif--}}

                                @else
                                    <div class="text-center py-10 text-gray-400">
                                        <i class="fa fa-users text-3xl mb-2"></i>
                                        <p class="text-sm">No participants added.</p>
                                    </div>
                                @endif

                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

</body>
</html>
