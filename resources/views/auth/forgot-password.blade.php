<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/smartMeet-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="h-screen">

<!-- PARENT DIV -->
<div class="flex h-full p-10 bg-grey-600">

    <!-- LEFT SIDEBAR -->
    <div class="w-1/2 relative flex flex-col justify-center items-center shadow-[20px_0_30px_rgba(1,1,1,0.2)]
    rounded-tl-xl / md / lg / xl / 2xl / full  rounded-bl-xl / md / lg / xl / 2xl / full border-l-8">

        <!-- Logo -->
        <div class="absolute top-0 left-8 flex items-center">
            <img src="{{ asset('images/smartMeet-logo.png') }}" class="w-30 h-30 object-contain">
            <h1 class="text-xl font-semibold text-blue-600">SmartMeet</h1>
        </div>

        <!-- Illustration Card -->
        <div class="mt-10 flex gap-6">
            <div class="relative w-[500px] h-[500px] flex items-center justify-center rounded-lg overflow-hidden">
                <img src="{{ asset('images/forget-password.png') }}" class="w-full h-auto object-contain" alt="Login Illustration">

                <!-- Text Overlay -->
                <div class="absolute top-20 left-0 text-left z-10">
                    <h2 class="text-2xl font-bold mb-2">Oops! Locked Out</h2>
                    <p class="text-gray-600 font-medium font-sm"> No problem — we’ll help you get <br> back in within seconds.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT FORGOT PASSWORD CARD -->
    <div class="w-1/2 flex justify-center items-center bg-blue-50 border-r-8 rounded-tr-xl rounded-br-xl">

        <div class="w-[400px] bg-white p-8 rounded-xl shadow-xl hover:shadow-2xl transition-shadow duration-300">

            <!-- Heading -->
            <h2 class="text-2xl font-bold mb-4 text-center text-blue-600">
                Forgot Password?
            </h2>
            <!-- Form -->
            <form action="{{ route('reset.password') }}" method="get" class="space-y-4">

                <!-- Email -->
                <div>
                    <label class="text-sm text-gray-500">Enter your email to recover your account:</label>
                    <input type="email" placeholder="Email"
                           class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition">
                </div>

                <!-- Button -->
                <button type="submit" class="w-full bg-blue-500 text-white py-3 rounded-lg shadow-md hover:bg-blue-600 transition-colors duration-300">
                    Send Reset Link
                </button>
            </form>

            <!-- Back to Login -->
            <p class="text-center mt-4 text-gray-500">
                <a href="/" class="text-blue-500 text-sm hover:underline">
                    Back to Login
                </a>
            </p>

        </div>
    </div>

</div>
</body>
</html>
