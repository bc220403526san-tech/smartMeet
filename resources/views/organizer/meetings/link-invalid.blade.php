<!DOCTYPE html>
<html>
<head>
    <title>Meeting Unavailable</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
<div class="bg-white shadow-lg rounded-2xl p-10 max-w-md text-center">
    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
        </svg>
    </div>
    <h1 class="text-xl font-bold text-gray-800 mb-2">Meeting Unavailable</h1>
    <p class="text-gray-500">{{ $message }}</p>

    @php
        $homeUrl = route('login');
        if (auth()->check()) {
            $homeUrl = match (auth()->user()->role) {
                'admin'      => url('/admin/dashboard'),
                'organizer'  => url('/organizer/dashboard'),
                default      => url('/participant/dashboard'),
            };
        }
    @endphp

    <a href="{{ $homeUrl }}" class="inline-block mt-6 bg-indigo-600 text-white px-6 py-2.5 rounded-lg font-medium hover:bg-indigo-700 transition">
        Go to Homepage
    </a>
</div>
</body>
</html>
