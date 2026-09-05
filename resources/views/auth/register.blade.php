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

        .float-pill.one { left: -20px; top: 24%; }
        .float-pill.two { right: -18px; bottom: 18%; }

        .float-pill i {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34,197,94,.10);
        }

        @media (max-width: 767px) {
            .auth-visual { min-height: 500px; }
            .image-stage { width: min(88%, 470px); }
        }


        /* CENTERED RESPONSIVE HERO IMAGE */
        .auth-visual {
            display: flex;
            flex-direction: column;
        }

        .hero-content {
            min-height: 100%;
            height: 100%;
        }

        .hero-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            width: 100%;
            transform: translateY(-8px);
        }

        .hero-copy {
            width: 100%;
            max-width: 560px;
            margin: 0 auto;
            text-align: center;
        }

        .image-stage {
            width: min(72%, 440px);
            margin: 18px auto 0;
            padding: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .image-stage img {
            display: block;
            width: 100%;
            max-width: 400px;
            max-height: 300px;
            object-fit: contain;
            margin: 0 auto;
        }

        @media (max-width: 1100px) {
            .hero-main {
                transform: translateY(-4px);
            }

            .image-stage {
                width: min(76%, 410px);
            }

            .image-stage img {
                max-height: 280px;
            }
        }

        @media (max-width: 900px) {
            .hero-content {
                padding-left: 1.75rem !important;
                padding-right: 1.75rem !important;
            }

            .image-stage {
                width: min(80%, 390px);
            }

            .image-stage img {
                max-height: 260px;
            }
        }

        @media (max-width: 767px) {
            .auth-visual {
                min-height: 470px !important;
            }

            .hero-content {
                min-height: 470px;
                padding-top: 1.4rem !important;
                padding-bottom: 1.2rem !important;
            }

            .hero-main {
                transform: none;
                justify-content: center;
            }

            .hero-copy {
                max-width: 500px;
            }

            .image-stage {
                width: min(78%, 360px);
                margin-top: 14px;
                padding: 14px;
            }

            .image-stage img {
                max-height: 220px;
            }

            .float-pill {
                font-size: 9px;
                padding: 7px 9px;
            }

            .float-pill.one {
                left: -8px;
            }

            .float-pill.two {
                right: -8px;
            }
        }

        @media (max-width: 520px) {
            .auth-visual {
                min-height: 430px !important;
            }

            .hero-content {
                min-height: 430px;
                padding-left: 1.1rem !important;
                padding-right: 1.1rem !important;
                padding-top: 1.1rem !important;
            }

            .hero-logo img {
                width: 38px;
                height: 38px;
            }

            .hero-logo span {
                font-size: 1.05rem !important;
            }

            .hero-copy h1 {
                font-size: 1.9rem !important;
                line-height: 1.08 !important;
            }

            .hero-copy p {
                font-size: .86rem !important;
            }

            .hero-badge {
                font-size: 10px;
                margin-bottom: 9px;
            }

            .image-stage {
                width: min(82%, 330px);
                margin-top: 12px;
                border-radius: 24px;
            }

            .image-stage::before {
                inset: 10px;
                border-radius: 18px;
            }

            .image-stage img {
                max-height: 190px;
            }

            .float-pill {
                display: none;
            }
        }

        @media (max-width: 390px) {
            .auth-visual {
                min-height: 405px !important;
            }

            .hero-content {
                min-height: 405px;
            }

            .image-stage {
                width: min(86%, 300px);
            }

            .image-stage img {
                max-height: 170px;
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
                    <img src="{{ asset('images/login-illustration.png') }}" alt="SmartMeet illustration">
                </div>
            </div>

        </div>
    </section>

    <!-- RIGHT SIDE UNCHANGED -->
    <div class="w-full md:w-1/2 flex justify-center items-center bg-gradient-to-br from-blue-50 via-white to-green-50
    rounded-b-2xl md:rounded-r-2xl md:rounded-bl-none
    min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
    px-4 py-6 sm:px-6 md:px-8 border-r-4 border-blue-700">

        <div class="w-full max-w-md bg-white/80 backdrop-blur-xl
        p-5 sm:p-6 rounded-2xl
        shadow-lg border border-white/60
        hover:shadow-xl transition-all duration-300">

            <div class="flex flex-col items-center mb-4">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center mb-2">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-800">Create an account</h2>
                <p class="text-xs text-gray-400 mt-0.5">Fill in your details below to get started</p>
            </div>

            <x-success />
            <x-error />

            @if ($errors->any())
                <div class="mb-3 px-3 py-2 bg-red-50 border border-red-100 rounded-lg">
                    <ul class="text-xs text-red-600 space-y-0.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="post" action="{{ route('register') }}" class="space-y-2.5" id="register-form">
                @csrf

                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Full name</label>
                    <input type="text" name="name" placeholder="name" value="{{ old('name') }}"
                           class="w-full px-3 py-2 text-sm rounded-xl
                    border border-gray-200 bg-gray-50
                    focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                    outline-none transition"
                           required>
                </div>

                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Email address</label>
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                           class="w-full px-3 py-2 text-sm rounded-xl
                    border {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                    focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                    outline-none transition"
                           required>

                    @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">

                    <div>
                        <label class="text-xs text-gray-400 mb-1 block">Password</label>

                        <div class="relative">
                            <input type="password" name="password" id="reg-password" placeholder="password"
                                   class="w-full px-3 py-2 pr-9 text-sm rounded-xl
                            border border-gray-200 bg-gray-50
                            focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                            outline-none transition"
                                   oninput="checkStrength(this.value)"
                                   required>

                            <button type="button" onclick="togglePassword('reg-password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4 eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="w-4 h-4 eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.87 19.87 0 0 1 4.22-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a19.86 19.86 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="text-xs text-gray-400 mb-1 block">Confirm</label>

                        <div class="relative">
                            <input type="password" name="password_confirmation" id="reg-password-confirm" placeholder="confirm"
                                   class="w-full px-3 py-2 pr-9 text-sm rounded-xl
                            border border-gray-200 bg-gray-50
                            focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                            outline-none transition"
                                   required>

                            <button type="button" onclick="togglePassword('reg-password-confirm', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400 hover:text-gray-600">
                                <svg class="w-4 h-4 eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>
                                    <circle cx="12" cy="12" r="3"/>
                                </svg>
                                <svg class="w-4 h-4 eye-closed hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a19.87 19.87 0 0 1 4.22-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a19.86 19.86 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                    <line x1="1" y1="1" x2="23" y2="23"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                </div>

                <div class="-mt-1">
                    <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div id="strength-bar" class="h-full w-0 rounded-full transition-all duration-300 bg-gray-200"></div>
                    </div>
                    <p id="strength-label" class="text-[10.5px] mt-0.5 text-gray-400">
                        8+ chars, upper &amp; lower case, number, symbol
                    </p>
                </div>

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

                <div class="flex items-center gap-2 pt-0.5">
                    <input type="checkbox" name="terms" class="w-3.5 h-3.5 accent-blue-500 border-gray-300 rounded">
                    <p class="text-xs text-gray-400">
                        I agree to the
                        <a href="#" class="text-blue-500 hover:underline">Terms and Conditions</a>
                    </p>
                </div>

                <button type="submit"
                        class="w-full py-2.5 rounded-xl
                bg-blue-600 text-white text-sm font-medium
                hover:bg-blue-700 hover:scale-[1.01]
                transition-all duration-200 mt-1">
                    Register
                </button>
            </form>

            <p class="text-center mt-3 text-xs text-gray-400">
                Already have an account?
                <a href="/login" class="text-blue-600 hover:underline font-medium">Log in</a>
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

    function checkStrength(value) {
        const bar = document.getElementById('strength-bar');
        const label = document.getElementById('strength-label');

        let score = 0;
        if (value.length >= 8) score++;
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score++;
        if (/\d/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;

        const widths = ['5%', '35%', '65%', '100%'];
        const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
        const labels = ['Weak password', 'Fair password', 'Good password', 'Strong password'];
        const textColors = ['text-red-500', 'text-orange-500', 'text-yellow-600', 'text-green-600'];

        if (value.length === 0) {
            bar.style.width = '0%';
            bar.className = 'h-full w-0 rounded-full transition-all duration-300 bg-gray-200';
            label.textContent = '8+ chars, upper & lower case, number, symbol';
            label.className = 'text-[10.5px] mt-0.5 text-gray-400';
            return;
        }

        const idx = Math.max(score - 1, 0);
        bar.style.width = widths[idx];
        bar.className = 'h-full rounded-full transition-all duration-300 ' + colors[idx];
        label.textContent = labels[idx];
        label.className = 'text-[10.5px] mt-0.5 ' + textColors[idx];
    }
</script>

</body>
</html>
