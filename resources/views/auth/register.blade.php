<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-50">
<!-- PARENT DIV -->
<div class="min-h-screen flex flex-col md:flex-row p-3 sm:p-4 md:p-6">

    <!-- LEFT SIDEBAR / HERO SECTION -->
    <x-auth-hero-section
        title="Welcome to Smartmeet"
        subtitle="Connect, Meet, Collaborate"
        image="images/login-illustration.png"
    />


    <!-- RIGHT LOGIN CARD SECTION -->
    <div class="w-full md:w-1/2 flex justify-center items-center bg-gradient-to-br from-blue-50 via-white to-green-50
    rounded-b-2xl md:rounded-r-2xl md:rounded-bl-none
    min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
    px-4 py-8 sm:px-6 md:px-8 border-r-4 border-blue-700">

        <!-- CARD -->
        <div class="w-full max-w-md bg-white/80 backdrop-blur-xl
        p-6 sm:p-7 rounded-2xl
        shadow-lg border border-white/60
        hover:shadow-xl transition-all duration-300">

            <!-- ICON + TITLE -->
            <div class="flex flex-col items-center mb-5">
                <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-800">Create an account</h2>
                <p class="text-xs text-gray-400 mt-0.5">Fill in your details below to get started</p>
                <x-error />
            </div>
            @if ($errors->any())
                <div class="mb-2 p-3 bg-red-100 text-sm text-red-700 rounded animate-fade">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif
            <x-success />

            <form method="post" action="{{ route('register') }}" class="space-y-3">
                @csrf

                <!-- FULL NAME -->
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Full name</label>
                    <input type="text" name="name" placeholder="name"
                           class="w-full px-3 py-2 text-sm rounded-xl
                   border border-gray-200 bg-gray-50
                   focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                   outline-none transition"
                           required>
                </div>

                <!-- EMAIL -->
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Email address</label>
                    <input type="email" name="email" placeholder="Email"
                           class="w-full px-3 py-2 text-sm rounded-xl
                   border border-gray-200 bg-gray-50
                   focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                   outline-none transition"
                           required>
                </div>

                <!-- PASSWORD + CONFIRM -->
                <div class="grid grid-cols-2 gap-3">

                    <!-- PASSWORD -->
                    <div>
                        <label class="text-xs text-gray-400 mb-1 block">Password</label>
                        <input type="password" name="password" placeholder="password"
                               class="w-full px-3 py-2 text-sm rounded-xl
                       border border-gray-200 bg-gray-50
                       focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                       outline-none transition"
                               required>
                    </div>

                    <!-- CONFIRM PASSWORD -->
                    <div>
                        <label class="text-xs text-gray-400 mb-1 block">Confirm</label>
                        <input type="password" name="password_confirmation" placeholder="password"
                               class="w-full px-3 py-2 text-sm rounded-xl
                       border border-gray-200 bg-gray-50
                       focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                       outline-none transition"
                               required>
                    </div>

                </div>

                <!-- ROLE -->
                <div>
                    <label class="text-xs text-gray-400 block mb-1">Role</label>
                    <div class="relative">
                        <select name="role"
                                class="w-full appearance-none px-3 py-2 text-sm
                        border border-gray-200 rounded-xl
                        bg-gray-50 text-gray-700
                        focus:outline-none focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                        transition">
                            <option selected disabled>Select a role</option>
                            <option value="admin">Admin</option>
                            <option value="organizer">Organizer</option>
                            <option value="participant">Participant</option>
                        </select>
                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg class="w-3.5 h-3.5 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- TERMS -->
                <div class="flex items-center gap-2 pt-1">
                    <input type="checkbox" name="terms" class="w-3.5 h-3.5 accent-blue-500 border-gray-300 rounded">
                    <p class="text-xs text-gray-400">
                        I agree to the
                        <a href="#" class="text-blue-500 hover:underline">Terms and Conditions</a>
                    </p>
                </div>

                <!-- BUTTON -->
                <button type="submit"
                        class="w-full py-2.5 rounded-xl
                bg-blue-600 text-white text-sm font-medium
                hover:bg-blue-700 hover:scale-[1.01]
                transition-all duration-200 mt-1">
                    Register
                </button>

            </form>

            <!-- LOGIN LINK -->
            <p class="text-center mt-4 text-xs text-gray-400">
                Already have an account?
                <a href="/login" class="text-blue-600 hover:underline font-medium">Log in</a>
            </p>

        </div>
    </div>
</div>
</body>
</html>
