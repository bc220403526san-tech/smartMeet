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
                <img src="{{ asset('images/login-illustration.png') }}" class="w-full h-auto object-contain" alt="Login Illustration">

                <!-- Text Overlay -->
                <div class="absolute top-20 left-0 text-left z-10">
                    <h2 class="text-2xl font-bold mb-2">Welcome to SmartMeet</h2>
                    <p class="text-gray-600 text-center font-medium">Connect, Meet, Collaborate</p>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT LOGIN CARD -->
    <div class="w-1/2 flex justify-center items-center bg-blue-50 border-r-8  rounded-tr-lg / md / lg / xl / 2xl / full  rounded-br-lg / md / lg / xl / 2xl / full">
        <div class="w-[400px] bg-white p-8 rounded-xl shadow-xl hover:shadow-2xl transition-shadow duration-300">
            <h2 class="text-2xl font-bold mb-6 text-center text-blue-600">Log in</h2>

            <!-- Form -->
            <form class="space-y-4">
                <div>
                    <label class="text-sm text-gray-500">Enter Your Email here:</label>
                    <input type="email" placeholder="Email"
                           class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition">
                </div>

                <div>
                    <label class="text-sm text-gray-500">Enter Your Password here:</label>
                    <input type="password" placeholder="Password"
                           class="w-full mt-1 p-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition">
                </div>

                <div>
                    <label class="text-sm text-gray-500">Select Your Role Below:</label>
                    <select class="w-full mt-1 p-3 border border-gray-300 focus:outline-none focus:border-blue-400 focus:ring-2 focus:ring-blue-200 transition">
                        <option>Role</option>
                        <option>Admin</option>
                        <option>Organizer</option>
                        <option>Participant</option>
                    </select>
                </div>

                <button class="w-full bg-blue-500 text-white py-3 rounded-lg shadow-md hover:bg-blue-600 transition-colors duration-300">
                    Log in
                </button>
            </form>

            <p class="text-center mt-3 text-gray-500">
                <a href="/forgot-password" class="text-blue-500 text-sm hover:underline">Forget Password</a>
            </p>
        </div>
    </div>

</div>
</body>
</html>
