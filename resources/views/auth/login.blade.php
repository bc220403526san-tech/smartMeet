<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        html, body { overflow-x: hidden; }

        .auth-page-shell {
            min-height: 100svh;
        }

        .auth-right {
            background:
                radial-gradient(circle at 92% 88%, rgba(220,252,231,.72), transparent 29%),
                radial-gradient(circle at 12% 12%, rgba(219,234,254,.82), transparent 30%),
                linear-gradient(145deg, #eff6ff 0%, #f8fbff 52%, #f0fdf4 100%);
        }

        .auth-card {
            width: min(100%, 31rem);
        }

        @media (max-width: 1023px) {
            .auth-page-shell {
                min-height: 100svh;
            }

            .auth-right {
                min-height: auto !important;
            }
        }

        @media (max-width: 640px) {
            .auth-page-shell {
                padding: 0 !important;
                background: #fff;
            }

            .auth-card {
                border-radius: 22px;
            }
        }
    </style>
</head>

<body class="bg-slate-100 text-slate-800">

<div class="auth-page-shell min-h-screen p-2 sm:p-3 lg:p-4">
    <div class="min-h-[calc(100svh-1rem)] sm:min-h-[calc(100svh-1.5rem)] lg:min-h-[calc(100svh-2rem)]
                flex flex-col lg:flex-row max-w-[1800px] mx-auto
                rounded-[30px] overflow-hidden bg-white
                border border-blue-100 shadow-[0_20px_60px_rgba(15,23,42,0.08)]">

        <x-auth-hero-section
            title="Welcome to Smartmeet"
            subtitle="Connect, Meet, Collaborate"
            image="images/login-illustration.png"
        />

        <!-- RIGHT LOGIN SECTION -->
        <div class="auth-right w-full lg:w-[48%] relative flex items-center justify-center
                    px-4 sm:px-7 md:px-10 lg:px-10 xl:px-14
                    py-10 sm:py-12 lg:py-8
                    min-h-[560px] lg:min-h-[calc(100vh-2rem)]">

            <!-- Decorative elements -->
            <div class="pointer-events-none absolute inset-0 overflow-hidden">
                <div class="absolute top-10 right-10 w-24 h-24 rounded-full border border-blue-100/70"></div>
                <div class="absolute bottom-14 left-12 w-16 h-16 rounded-full border border-emerald-100"></div>
            </div>

            <!-- CARD -->
            <div class="auth-card relative z-10 bg-white/95 backdrop-blur-sm
                        p-5 sm:p-7 md:p-8 rounded-[26px]
                        shadow-[0_18px_45px_rgba(15,23,42,0.10)]
                        border border-white ring-1 ring-slate-100">

                <!-- ICON + TITLE -->
                <div class="flex flex-col items-center mb-6">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-50 to-indigo-50
                                flex items-center justify-center mb-3 ring-1 ring-blue-100 shadow-sm">
                        <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                    </div>
                    <h2 class="text-xl sm:text-[22px] font-bold text-slate-800">Welcome back</h2>
                    <p class="text-xs sm:text-sm text-slate-400 mt-1">Log in to your account to continue</p>
                </div>

                <x-success />
                <x-error />

                <form action="{{ route('login') }}" method="POST" class="space-y-4">
                    @csrf

                    <!-- EMAIL -->
                    <div>
                        <label class="text-xs font-medium text-slate-500 block mb-1.5">Email address</label>
                        <input type="email" name="email" placeholder="Email"
                               class="w-full px-4 py-3 text-sm border border-slate-200 bg-slate-50/80 rounded-xl
                                      focus:ring-4 focus:ring-blue-50 focus:border-blue-400 focus:bg-white
                                      outline-none transition" required>
                    </div>

                    <!-- PASSWORD -->
                    <div>
                        <div class="flex items-center justify-between gap-3 mb-1.5">
                            <label class="text-xs font-medium text-slate-500">Password</label>
                            <a href="{{ route('forgot.password') }}"
                               class="text-xs font-medium text-blue-600 hover:text-blue-700 hover:underline">
                                Forgot password?
                            </a>
                        </div>

                        <div class="relative">
                            <input type="password" name="password" id="login-password" placeholder="Your password"
                                   class="w-full px-4 py-3 pr-11 text-sm border border-slate-200 bg-slate-50/80 rounded-xl
                                          focus:ring-4 focus:ring-blue-50 focus:border-blue-400 focus:bg-white
                                          outline-none transition" required>

                            <button type="button" onclick="togglePassword('login-password', this)"
                                    aria-label="Show or hide password"
                                    class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-blue-600 transition">
                                <svg class="w-4 h-4 eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="w-4 h-4 eye-closed hidden" viewBox="0 0 24 24" fill="none"
                                     stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.87 19.87 0 0 1 4.22-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a19.86 19.86 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- LOGIN BUTTON -->
                    <button type="submit"
                            class="w-full py-3 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600
                                   text-white text-sm font-semibold
                                   hover:from-blue-700 hover:to-indigo-700
                                   hover:-translate-y-0.5 hover:shadow-lg hover:shadow-blue-200/60
                                   active:translate-y-0 transition-all duration-200">
                        Log in
                    </button>
                </form>

                <!-- DIVIDER -->
                <div class="flex items-center my-5">
                    <div class="flex-grow border-t border-slate-100"></div>
                    <span class="mx-3 text-slate-300 text-[11px] sm:text-xs">or continue with</span>
                    <div class="flex-grow border-t border-slate-100"></div>
                </div>

                <!-- SOCIAL BUTTONS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                    <a href="{{ route('social.redirect', 'google') }}"
                       class="w-full flex items-center justify-center gap-2 border border-slate-200 bg-white py-2.5 px-3
                              rounded-xl hover:bg-slate-50 hover:border-slate-300 transition
                              text-xs sm:text-sm text-slate-600 font-medium">
                        <svg class="w-4 h-4 shrink-0" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5 12.9 4.5 4 13.4 4 24.5S12.9 44.5 24 44.5 44 35.6 44 24.5c0-1.3-.1-2.7-.4-4z"/>
                            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 16 18.9 12 24 12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5c-7.7 0-14.3 4.4-17.7 10.2z"/>
                            <path fill="#4CAF50" d="M24 44.5c5.1 0 9.8-2 13.4-5.2l-6.2-5.1c-2.1 1.5-4.7 2.3-7.2 2.3-5.2 0-9.6-3.3-11.2-7.9l-6.5 5C9.6 40.1 16.3 44.5 24 44.5z"/>
                            <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3-3.5 5.4-6.6 6.9l6.2 5.1c3.6-3.3 5.7-8.1 5.7-13.5 0-1.3-.1-2.7-.4-4z"/>
                        </svg>
                        Google
                    </a>

                    <a href="{{ route('social.redirect', 'facebook') }}"
                       class="w-full flex items-center justify-center gap-2 border border-blue-100 bg-blue-50/70 py-2.5 px-3
                              rounded-xl hover:bg-blue-100 transition
                              text-xs sm:text-sm text-blue-700 font-medium">
                        <svg class="w-4 h-4 shrink-0 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.931-1.956 1.886v2.269h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                        </svg>
                        Facebook
                    </a>
                </div>

                <!-- SIGN UP -->
                <p class="text-center mt-5 text-xs sm:text-sm text-slate-400">
                    Don't have an account?
                    <a href="{{ route('register') }}" class="text-blue-600 hover:underline font-semibold">
                        Sign up
                    </a>
                </p>
            </div>
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
