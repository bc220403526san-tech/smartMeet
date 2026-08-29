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
    <div class="w-full md:w-1/2 flex justify-center items-center bg-blue-50
    rounded-b-2xl md:rounded-r-2xl md:rounded-bl-none
    min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
    px-4 py-8 sm:px-6 md:px-8 border-r-4 border-blue-700">
        <!-- CARD -->
        <div class="w-full max-w-md bg-white p-6 rounded-2xl shadow-lg border border-gray-100
        hover:shadow-xl transition-all duration-300">
            <!-- ICON + TITLE -->
            <div class="flex flex-col items-center mb-5">
                <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-800">Welcome back</h2>
                <p class="text-xs text-gray-400 mt-0.5">Log in to your account to continue</p>
            </div>
            <x-success />
            <x-error />
            <form action="{{ route('login') }}" method="POST" class="space-y-3">
                @csrf
                <!-- EMAIL -->
                <div>
                    <label class="text-xs text-gray-400 block mb-1">Email address</label>
                    <input type="email" name="email" placeholder="Email"
                           class="w-full px-3 py-2 text-sm border border-gray-200 bg-gray-50 rounded-xl
                  focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                  outline-none transition" required>
                </div>
                <!-- PASSWORD -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs text-gray-400">Password</label>
                        <a href="{{ route('forgot.password') }}" class="text-xs text-blue-500 hover:underline">Forgot password?</a>
                    </div>
                    <!-- WRAPPER FOR EYE ICON -->
                    <div class="relative">
                        <input type="password" name="password" id="login-password" placeholder="Your password"
                               class="w-full px-3 py-2 pr-10 text-sm border border-gray-200 bg-gray-50 rounded-xl
                      focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                      outline-none transition" required>
                        <button type="button" onclick="togglePassword('login-password', this)"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
                            <!-- EYE (visible state icon) -->
                            <svg class="w-4 h-4 eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                            <!-- EYE-OFF (hidden state icon) -->
                            <svg class="w-4 h-4 eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.87 19.87 0 0 1 4.22-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a19.86 19.86 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- LOGIN BUTTON -->
                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium
               hover:bg-blue-700 hover:scale-[1.01] transition-all duration-200 mt-1">
                    Log in
                </button>
            </form>
            <!-- DIVIDER -->
            <div class="flex items-center my-4">
                <div class="flex-grow border-t border-gray-100"></div>
                <span class="mx-3 text-gray-300 text-xs">or continue with</span>
                <div class="flex-grow border-t border-gray-100"></div>
            </div>
            <!-- SOCIAL BUTTONS -->
            <div class="flex flex-col gap-2">
                <!-- GOOGLE LOGIN -->
                <a href="{{ route('social.redirect', 'google') }}"
                   class="w-full flex items-center justify-center gap-2
                          border border-gray-200 bg-gray-50 py-2.5 rounded-xl
                          hover:bg-gray-100 transition text-sm text-gray-600 font-medium">
                    <svg class="w-4 h-4" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12
            12-12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5 12.9 4.5 4 13.4 4 24.5S12.9 44.5 24 44.5
            44 35.6 44 24.5c0-1.3-.1-2.7-.4-4z"/>
                        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 16 18.9 12 24 12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9
            6.5 29.2 4.5 24 4.5c-7.7 0-14.3 4.4-17.7 10.2z"/>
                        <path fill="#4CAF50" d="M24 44.5c5.1 0 9.8-2 13.4-5.2l-6.2-5.1c-2.1 1.5-4.7 2.3-7.2
            2.3-5.2 0-9.6-3.3-11.2-7.9l-6.5 5C9.6 40.1 16.3 44.5 24 44.5z"/>
                        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3-3.5 5.4-6.6 6.9l6.2 5.1c3.6-3.3
            5.7-8.1 5.7-13.5 0-1.3-.1-2.7-.4-4z"/>
                    </svg>
                    Continue with Google
                </a>
                <!-- FACEBOOK LOGIN -->
                <a href="{{ route('social.redirect', 'facebook') }}"
                   class="w-full flex items-center justify-center gap-2
          border border-blue-200 bg-blue-50 py-2.5 rounded-xl
          hover:bg-blue-100 transition text-sm text-blue-700 font-medium">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1
                 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025
                 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513
                 c-1.491 0-1.956.931-1.956 1.886v2.269h3.328l-.532 3.49h-2.796V24
                 C19.612 23.094 24 18.1 24 12.073z"/>
                    </svg>
                    Continue with Facebook
                </a>
            </div>
            <!-- SIGN UP -->
            <p class="text-center mt-4 text-xs text-gray-400">
                Don't have an account?
                <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-medium">Sign up</a>
            </p>
        </div>
    </div>
</div>

<script>
    function togglePassword(inputId, btn) {
        const input = document.getElementById(inputId);
        const eyeOpen = btn.querySelector('.eye-open');
        const eyeClosed = btn.querySelector('.eye-closed');
        if (input.type === 'password') {
            input.type = 'text';
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            input.type = 'password';
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
</script>
</body>
</html>
