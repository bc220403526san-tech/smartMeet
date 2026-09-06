
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invite Link Not Available — SmartMeet</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50 text-gray-900">
<main class="flex min-h-screen items-center justify-center px-4 py-10">
    <div class="w-full max-w-lg rounded-3xl bg-white p-6 shadow-lg sm:p-8">

        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-blue-50">
            <svg class="h-7 w-7 text-blue-600"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke-width="1.8"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M18.364 18.364A9 9 0 1 0 5.636 5.636m12.728 12.728L5.636 5.636M9.75 9.75v.008h.008V9.75H9.75Zm4.5 4.5v.008h.008v-.008h-.008Z" />
            </svg>
        </div>

        <div class="mt-5 text-center">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-blue-500">
                Meeting Invite
            </p>

            <h1 class="mt-2 text-2xl font-bold text-gray-900">
                Organizer accounts cannot use participant invite links
            </h1>

            <p class="mx-auto mt-3 max-w-md text-sm leading-6 text-gray-500">
                This meeting link is intended for participant accounts only.
                An organizer cannot join a meeting through a participant invite link.
            </p>
        </div>

        @if(isset($meeting))
            <div class="mt-6 rounded-2xl bg-gray-50 p-4">
                <p class="text-[10px] font-semibold uppercase tracking-wider text-gray-400">
                    Meeting
                </p>

                <p class="mt-1 text-sm font-bold text-gray-800">
                    {{ $meeting->title }}
                </p>

                <div class="mt-3 flex flex-wrap gap-x-5 gap-y-2 text-xs text-gray-500">
                    @if($meeting->date)
                        <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar text-blue-500"></i>
                                {{ \Carbon\Carbon::parse($meeting->date)->format('M d, Y') }}
                            </span>
                    @endif

                    @if($meeting->time)
                        <span class="inline-flex items-center gap-1.5">
                                <i class="fa-regular fa-clock text-blue-500"></i>
                                {{ \Carbon\Carbon::parse($meeting->time)->format('h:i A') }}
                            </span>
                    @endif
                </div>
            </div>
        @endif

        <a href="{{ $backUrl ?? url('/') }}"
           class="mt-6 inline-flex h-11 w-full items-center justify-center gap-2 rounded-xl bg-blue-600 px-4 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">
            <svg class="h-4 w-4"
                 xmlns="http://www.w3.org/2000/svg"
                 fill="none"
                 viewBox="0 0 24 24"
                 stroke-width="1.8"
                 stroke="currentColor">
                <path stroke-linecap="round"
                      stroke-linejoin="round"
                      d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
            </svg>

            {{ $backLabel ?? 'Back' }}
        </a>

        <p class="mt-4 text-center text-xs text-gray-400">
            To join this meeting, use a Participant account.
        </p>
    </div>
</main>
</body>
</html>
