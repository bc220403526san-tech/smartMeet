<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Outfit:wght@500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        html { scroll-behavior: smooth; }

        body {
            font-family: 'DM Sans', sans-serif;
            background:
                radial-gradient(circle at 12% 10%, rgba(59,130,246,.10), transparent 28%),
                radial-gradient(circle at 88% 88%, rgba(96,165,250,.08), transparent 30%),
                #f8fafc;
        }

        h1, h2, h3, .brand-font {
            font-family: 'Outfit', sans-serif;
        }

        @keyframes authFadeUp {
            from { opacity: 0; transform: translateY(20px) scale(.985); }
            to { opacity: 1; transform: translateY(0) scale(1); }
        }

        @keyframes authFadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes softFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-5px); }
        }

        @keyframes buttonShine {
            0% { left: -60%; }
            100% { left: 130%; }
        }

        .auth-panel {
            animation: authFadeIn .65s ease both;
        }

        .auth-card {
            animation: authFadeUp .72s cubic-bezier(.22,1,.36,1) .08s both;
        }

        .auth-icon {
            animation: softFloat 4s ease-in-out infinite;
        }

        .premium-input {
            transition:
                border-color .22s ease,
                background-color .22s ease,
                box-shadow .22s ease,
                transform .22s ease;
        }

        .premium-input:hover {
            border-color: #cbd5e1;
            background: #fff;
        }

        .premium-input:focus {
            transform: translateY(-1px);
            box-shadow: 0 0 0 4px rgba(59,130,246,.09);
        }

        .premium-button {
            position: relative;
            overflow: hidden;
            isolation: isolate;
            box-shadow: 0 10px 24px rgba(37,99,235,.22);
        }

        .premium-button::after {
            content: "";
            position: absolute;
            top: -50%;
            left: -60%;
            width: 28%;
            height: 200%;
            transform: rotate(18deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.38), transparent);
            pointer-events: none;
        }

        .premium-button:hover::after {
            animation: buttonShine .7s ease;
        }

        .social-button {
            transition:
                transform .22s ease,
                box-shadow .22s ease,
                border-color .22s ease,
                background-color .22s ease;
        }

        .social-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 24px rgba(15,23,42,.07);
        }

        .auth-card::before {
            content: "";
            position: absolute;
            inset: 0;
            border-radius: 1.35rem;
            padding: 1px;
            background: linear-gradient(
                135deg,
                rgba(255,255,255,.95),
                rgba(191,219,254,.70),
                rgba(255,255,255,.75)
            );
            -webkit-mask:
                linear-gradient(#fff 0 0) content-box,
                linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            pointer-events: none;
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: .01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: .01ms !important;
            }
        }
    </style>
</head>

<body class="min-h-screen text-slate-800">

<div class="min-h-screen flex flex-col md:flex-row p-3 sm:p-4 md:p-6 gap-0">

    <x-auth-hero-section
        title="Welcome to Smartmeet"
        subtitle="Connect, Meet, Collaborate"
        image="images/meeting.png"
    />

    <div class="auth-panel relative overflow-hidden w-full md:w-1/2 flex justify-center items-center
        bg-gradient-to-br from-blue-50/90 via-white to-sky-50/80
        rounded-b-3xl md:rounded-r-3xl md:rounded-bl-none
        min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
        px-4 py-8 sm:px-6 md:px-8 border-r-4 border-blue-700">

        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-blue-200/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-28 -left-24 w-80 h-80 rounded-full bg-sky-200/20 blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.035] pointer-events-none"
             style="background-image: linear-gradient(#2563eb 1px, transparent 1px), linear-gradient(90deg,#2563eb 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="auth-card relative w-full max-w-md bg-white/90 backdrop-blur-xl
            p-6 sm:p-7 rounded-[1.35rem]
            shadow-[0_24px_70px_rgba(15,23,42,0.10)]
            border border-white/80
            hover:shadow-[0_30px_90px_rgba(15,23,42,0.13)]
            transition-all duration-500">

            <div class="flex flex-col items-center mb-6">
                <div class="auth-icon w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100
                    border border-blue-100 shadow-sm flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                        <polyline points="10 17 15 12 10 7"/>
                        <line x1="15" y1="12" x2="3" y2="12"/>
                    </svg>
                </div>

                <h2 class="text-xl sm:text-2xl font-semibold tracking-[-0.02em] text-slate-900">
                    Welcome back
                </h2>

                <p class="text-xs sm:text-sm text-slate-400 mt-1">
                    Log in to your account to continue
                </p>
            </div>

            <x-success />
            <x-error />

            @if ($errors->any())
                <div class="mb-4 px-3 py-2.5 bg-red-50 border border-red-200 rounded-xl shadow-sm">
                    <ul class="text-xs text-red-600 space-y-0.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label class="text-xs font-medium text-slate-500 block mb-1.5">
                        Email address
                    </label>

                    <input type="email" name="email" placeholder="Email"
                           value="{{ old('email') }}"
                           class="premium-input w-full px-3.5 py-2.5 text-sm border
                           {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-slate-200 bg-slate-50/80' }}
                           rounded-xl focus:ring-0 focus:border-blue-400
                           outline-none"
                           required>

                    @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-xs font-medium text-slate-500">Password</label>

                        <a href="{{ route('forgot.password') }}"
                           class="text-xs text-blue-600 hover:text-blue-700 hover:underline underline-offset-4 transition">
                            Forgot password?
                        </a>
                    </div>

                    <div class="relative">
                        <input type="password" name="password" id="login-password" placeholder="Your password"
                               class="premium-input w-full px-3.5 py-2.5 pr-10 text-sm
                               border border-slate-200 bg-slate-50/80 rounded-xl
                               focus:ring-0 focus:border-blue-400 outline-none"
                               required>

                        <button type="button" onclick="togglePassword('login-password', this)"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-slate-400
                                hover:text-blue-600 transition-colors duration-200">

                            <svg class="w-4 h-4 eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>

                            <svg class="w-4 h-4 eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                 stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.87 19.87 0 0 1 4.22-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a19.86 19.86 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>

                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="premium-button w-full py-2.5 rounded-xl
                        bg-gradient-to-r from-blue-600 via-blue-600 to-blue-700
                        text-white text-sm font-semibold
                        hover:-translate-y-0.5 hover:shadow-[0_15px_32px_rgba(37,99,235,0.28)]
                        active:translate-y-0 active:scale-[0.995]
                        transition-all duration-300 mt-1">
                    Log in
                </button>
            </form>

            <div class="flex items-center my-5">
                <div class="flex-grow border-t border-slate-100"></div>
                <span class="mx-3 text-slate-300 text-xs">or continue with</span>
                <div class="flex-grow border-t border-slate-100"></div>
            </div>

            <div class="flex flex-col gap-2.5">

                <a href="{{ route('social.redirect', 'google') }}"
                   class="social-button w-full flex items-center justify-center gap-2.5
                   border border-slate-200 bg-white py-2.5 rounded-xl
                   hover:bg-slate-50 hover:border-slate-300
                   text-sm text-slate-600 font-medium">

                    <svg class="w-4 h-4" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5 12.9 4.5 4 13.4 4 24.5S12.9 44.5 24 44.5 44 35.6 44 24.5c0-1.3-.1-2.7-.4-4z"/>
                        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 16 18.9 12 24 12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5c-7.7 0-14.3 4.4-17.7 10.2z"/>
                        <path fill="#4CAF50" d="M24 44.5c5.1 0 9.8-2 13.4-5.2l-6.2-5.1c-2.1 1.5-4.7 2.3-7.2 2.3-5.2 0-9.6-3.3-11.2-7.9l-6.5 5C9.6 40.1 16.3 44.5 24 44.5z"/>
                        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3-3.5 5.4-6.6 6.9l6.2 5.1c3.6-3.3 5.7-8.1 5.7-13.5 0-1.3-.1-2.7-.4-4z"/>
                    </svg>

                    Continue with Google
                </a>

                <a href="{{ route('social.redirect', 'facebook') }}"
                   class="social-button w-full flex items-center justify-center gap-2.5
                   border border-blue-200 bg-blue-50/70 py-2.5 rounded-xl
                   hover:bg-blue-100/70 hover:border-blue-300
                   text-sm text-blue-700 font-medium">

                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.931-1.956 1.886v2.269h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                    </svg>

                    Continue with Facebook
                </a>

            </div>

            <p class="text-center mt-5 text-xs text-slate-400">
                Don't have an account?
                <a href="{{ route('register') }}"
                   class="text-blue-600 hover:text-blue-700 hover:underline underline-offset-4 font-semibold transition">
                    Sign up
                </a>
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
