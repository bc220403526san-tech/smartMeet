<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        @keyframes heroFade {
            from { opacity: 0; transform: translateY(18px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes heroFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        @keyframes orbOne {
            0%,100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(16px,-12px) scale(1.05); }
        }

        @keyframes orbTwo {
            0%,100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-14px,10px) scale(1.08); }
        }

        .auth-visual {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 18% 18%, rgba(59,130,246,.15), transparent 27%),
                radial-gradient(circle at 82% 82%, rgba(96,165,250,.12), transparent 30%),
                linear-gradient(145deg, #f8fbff 0%, #eef5ff 48%, #f8fbff 100%);
        }

        .auth-visual::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                linear-gradient(rgba(37,99,235,.025) 1px, transparent 1px),
                linear-gradient(90deg, rgba(37,99,235,.025) 1px, transparent 1px);
            background-size: 34px 34px;
            mask-image: linear-gradient(to bottom right, #000, transparent 74%);
            pointer-events: none;
        }

        .hero-orb {
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
        }

        .hero-orb.one {
            width: 210px;
            height: 210px;
            top: -70px;
            right: -55px;
            background: rgba(59,130,246,.10);
            animation: orbOne 8s ease-in-out infinite;
        }

        .hero-orb.two {
            width: 160px;
            height: 160px;
            bottom: -55px;
            left: -45px;
            border: 1px solid rgba(37,99,235,.09);
            box-shadow: 0 0 0 34px rgba(37,99,235,.025);
            animation: orbTwo 9s ease-in-out infinite;
        }

        .hero-content {
            animation: heroFade .75s cubic-bezier(.22,1,.36,1) both;
        }

        .hero-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            color: #2563eb;
            letter-spacing: -.02em;
        }

        .hero-logo img {
            width: 44px;
            height: 44px;
            object-fit: contain;
            filter: drop-shadow(0 8px 18px rgba(37,99,235,.16));
        }

        .image-stage {
            position: relative;
            width: min(82%, 520px);
            margin: 28px auto 0;
            padding: 26px;
            border-radius: 32px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(191,211,238,.75);
            box-shadow:
                0 28px 70px rgba(15,23,42,.10),
                inset 0 1px 0 rgba(255,255,255,.9);
            backdrop-filter: blur(10px);
        }

        .image-stage::before {
            content: "";
            position: absolute;
            inset: 18px;
            border-radius: 24px;
            border: 1px dashed rgba(37,99,235,.14);
            pointer-events: none;
        }

        .image-stage img {
            width: 100%;
            position: relative;
            z-index: 1;
            animation: heroFloat 5.5s ease-in-out infinite;
            filter: drop-shadow(0 22px 36px rgba(15,23,42,.12));
        }

        .float-pill {
            position: absolute;
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 9px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.94);
            border: 1px solid #e2eaf5;
            box-shadow: 0 12px 28px rgba(15,23,42,.08);
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            backdrop-filter: blur(10px);
        }

        .float-pill.one {
            left: -20px;
            top: 24%;
        }

        .float-pill.two {
            right: -18px;
            bottom: 18%;
        }

        .float-pill i {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34,197,94,.10);
        }

        @media (max-width: 767px) {
            .auth-visual {
                min-height: 500px;
            }

            .image-stage {
                width: min(88%, 470px);
            }
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

<body class="min-h-screen bg-gray-50">

<div class="min-h-screen flex flex-col md:flex-row p-3 sm:p-4 md:p-6">

    <!-- LEFT HERO ONLY REDESIGNED -->
    <section class="auth-visual w-full md:w-1/2 min-h-[45vh] md:min-h-[calc(100vh-3rem)]
        rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none border-l-4 border-blue-700">

        <div class="hero-orb one"></div>
        <div class="hero-orb two"></div>

        <div class="hero-content relative z-10 h-full flex flex-col px-6 sm:px-10 md:px-12 py-7 md:py-9">

            <a href="{{ url('/') }}" class="hero-logo">
                <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet logo">
                <span class="text-xl">SmartMeet</span>
            </a>

            <div class="flex-1 flex flex-col justify-center">
                <div class="max-w-xl mx-auto md:mx-0 text-center md:text-left">
                    <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-50 border border-blue-100
                        text-[11px] font-semibold text-blue-600 mb-4">
                        Smart online collaboration
                    </span>

                    <h1 class="text-3xl sm:text-4xl md:text-[42px] font-bold tracking-[-0.035em] text-slate-900 leading-tight">
                        Welcome to SmartMeet
                    </h1>

                    <p class="mt-2 text-sm sm:text-base text-slate-500 font-medium">
                        Connect, Meet, Collaborate
                    </p>
                </div>

                <div class="image-stage">
                    <div class="float-pill one"><i></i> Real-time meetings</div>
                    <div class="float-pill two"><i></i> Stay connected</div>
                    <img src="{{ asset('images/meeting.png') }}" alt="SmartMeet meeting illustration">
                </div>
            </div>

        </div>
    </section>

    <!-- RIGHT SIDE UNCHANGED -->
    <div class="w-full md:w-1/2 flex justify-center items-center bg-blue-50
    rounded-b-2xl md:rounded-r-2xl md:rounded-bl-none
    min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
    px-4 py-8 sm:px-6 md:px-8 border-r-4 border-blue-700">

        <div class="w-full max-w-md bg-white p-6 rounded-2xl shadow-lg border border-gray-100
        hover:shadow-xl transition-all duration-300">

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

            @if ($errors->any())
                <div class="mb-3 px-3 py-2 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="text-xs text-red-600 space-y-0.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-3">
                @csrf

                <div>
                    <label class="text-xs text-gray-400 block mb-1">Email address</label>
                    <input type="email" name="email" placeholder="Email"
                           value="{{ old('email') }}"
                           class="w-full px-3 py-2 text-sm border
                           {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                           rounded-xl focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                           outline-none transition"
                           required>

                    @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="text-xs text-gray-400">Password</label>
                        <a href="{{ route('forgot.password') }}" class="text-xs text-blue-500 hover:underline">Forgot password?</a>
                    </div>

                    <div class="relative">
                        <input type="password" name="password" id="login-password" placeholder="Your password"
                               class="w-full px-3 py-2 pr-10 text-sm border border-gray-200 bg-gray-50 rounded-xl
                               focus:ring-2 focus:ring-blue-100 focus:border-blue-400 outline-none transition"
                               required>

                        <button type="button" onclick="togglePassword('login-password', this)"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600">
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
                        class="w-full py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium
                        hover:bg-blue-700 hover:scale-[1.01] transition-all duration-200 mt-1">
                    Log in
                </button>
            </form>

            <div class="flex items-center my-4">
                <div class="flex-grow border-t border-gray-100"></div>
                <span class="mx-3 text-gray-300 text-xs">or continue with</span>
                <div class="flex-grow border-t border-gray-100"></div>
            </div>

            <div class="flex flex-col gap-2">
                <a href="{{ route('social.redirect', 'google') }}"
                   class="w-full flex items-center justify-center gap-2 border border-gray-200 bg-gray-50 py-2.5 rounded-xl
                   hover:bg-gray-100 transition text-sm text-gray-600 font-medium">
                    <svg class="w-4 h-4" viewBox="0 0 48 48">
                        <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5 12.9 4.5 4 13.4 4 24.5S12.9 44.5 24 44.5 44 35.6 44 24.5c0-1.3-.1-2.7-.4-4z"/>
                        <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 16 18.9 12 24 12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5c-7.7 0-14.3 4.4-17.7 10.2z"/>
                        <path fill="#4CAF50" d="M24 44.5c5.1 0 9.8-2 13.4-5.2l-6.2-5.1c-2.1 1.5-4.7 2.3-7.2 2.3-5.2 0-9.6-3.3-11.2-7.9l-6.5 5C9.6 40.1 16.3 44.5 24 44.5z"/>
                        <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3-3.5 5.4-6.6 6.9l6.2 5.1c3.6-3.3 5.7-8.1 5.7-13.5 0-1.3-.1-2.7-.4-4z"/>
                    </svg>
                    Continue with Google
                </a>

                <a href="{{ route('social.redirect', 'facebook') }}"
                   class="w-full flex items-center justify-center gap-2 border border-blue-200 bg-blue-50 py-2.5 rounded-xl
                   hover:bg-blue-100 transition text-sm text-blue-700 font-medium">
                    <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.931-1.956 1.886v2.269h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                    </svg>
                    Continue with Facebook
                </a>
            </div>

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
