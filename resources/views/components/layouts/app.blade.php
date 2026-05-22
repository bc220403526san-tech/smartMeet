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

<body class="bg-gray-100 h-screen overflow-hidden">

{{-- Overlay --}}
<div id="overlay" class="fixed inset-0 bg-black/40 z-30 hidden lg:hidden"></div>

<div class="flex h-screen overflow-hidden">

    {{-- Sidebar --}}
    <div id="sidebar"
         class="fixed lg:static inset-y-0 left-0 z-40 shrink-0
                    -translate-x-full lg:translate-x-0
                    transition-transform duration-300 ease-in-out">
        @php
            $sidebar = 'sidebar.' . Auth::user()->role . '-menu';
        @endphp
        <x-dynamic-component :component="$sidebar" />
    </div>

    {{-- Main Area --}}
    <div class="flex-1 flex flex-col min-w-0 overflow-hidden">

        {{-- Header slot — har page apna header pass karega --}}
        {{ $header }}

        {{-- Page Content --}}
        <main class="flex-1 overflow-y-auto">
            {{ $slot }}
        </main>

    </div>

</div>

<script>
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('overlay');
    const hamburger = document.getElementById('hamburger');

    hamburger?.addEventListener('click', () => {
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    });

    overlay.addEventListener('click', () => {
        sidebar.classList.add('-translate-x-full');
        overlay.classList.add('hidden');
    });
</script>

</body>
</html>
