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

{{-- 1. Checkbox --}}
<input type="checkbox" id="sidebar-toggle">

{{-- 2. layout-wrapper --}}
<div class="layout-wrapper flex h-full">

    {{-- 3. Overlay --}}
    <label for="sidebar-toggle" class="sidebar-overlay"></label>

    {{-- 4. Sidebar --}}
    <x-sidebar.admin-menu />

    {{-- 5. Main Area --}}
    <div class="flex-1 flex flex-col m-2">

        <!-- CONTENT -->
        <main class="flex-1 overflow-y-auto rounded-lg">
            {{ $slot }}
        </main>

    </div>

</div>

</body>
</html>
