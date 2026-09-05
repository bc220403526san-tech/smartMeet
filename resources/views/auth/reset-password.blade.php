<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APPNAME') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        @keyframes heroFade {
            from { opacity: 0; transform: translateY(14px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes heroFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-7px); }
        }

        @keyframes orbOne {
            0%,100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(12px,-10px) scale(1.05); }
        }

        @keyframes orbTwo {
            0%,100% { transform: translate(0,0) scale(1); }
            50% { transform: translate(-12px,8px) scale(1.06); }
        }

        .auth-visual {
            position: relative;
            overflow: hidden;
            background:
                radial-gradient(circle at 18% 18%, rgba(59,130,246,.14), transparent 26%),
                radial-gradient(circle at 80% 82%, rgba(96,165,250,.10), transparent 30%),
                linear-gradient(145deg, #fbfdff 0%, #eef5ff 48%, #f8fbff 100%);
        }

        .auth-visual::before {
            content: "";
            position: absolute;
            inset: 0;
            background-image:
                radial-gradient(circle at 20% 24%, rgba(37,99,235,.055) 0 2px, transparent 2.5px),
                radial-gradient(circle at 80% 72%, rgba(37,99,235,.045) 0 2px, transparent 2.5px);
            background-size: 44px 44px, 58px 58px;
            pointer-events: none;
        }

        .hero-orb {
            position: absolute;
            border-radius: 999px;
            pointer-events: none;
        }

        .hero-orb.one {
            width: 220px;
            height: 220px;
            top: -80px;
            right: -60px;
            background: rgba(59,130,246,.10);
            animation: orbOne 8s ease-in-out infinite;
        }

        .hero-orb.two {
            width: 170px;
            height: 170px;
            bottom: -65px;
            left: -48px;
            border: 1px solid rgba(37,99,235,.09);
            box-shadow: 0 0 0 34px rgba(37,99,235,.025);
            animation: orbTwo 9s ease-in-out infinite;
        }

        .hero-content {
            animation: heroFade .7s cubic-bezier(.22,1,.36,1) both;
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
            filter: drop-shadow(0 8px 18px rgba(37,99,235,.14));
        }

        .hero-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transform: translateY(-8px);
        }

        .hero-copy {
            text-align: center;
            max-width: 560px;
            margin: 0 auto;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            padding: 7px 11px;
            border-radius: 999px;
            background: rgba(255,255,255,.82);
            border: 1px solid #dce8fb;
            color: #2563eb;
            font-size: 11px;
            font-weight: 700;
            box-shadow: 0 8px 24px rgba(37,99,235,.06);
            margin-bottom: 12px;
        }

        .hero-badge::before {
            content: "";
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59,130,246,.10);
        }

        .image-stage {
            position: relative;
            width: min(76%, 470px);
            margin: 18px auto 0;
            padding: 20px;
            border-radius: 30px;
            background: rgba(255,255,255,.72);
            border: 1px solid rgba(191,211,238,.72);
            box-shadow:
                0 24px 58px rgba(15,23,42,.09),
                inset 0 1px 0 rgba(255,255,255,.95);
            backdrop-filter: blur(10px);
        }

        .image-stage::before {
            content: "";
            position: absolute;
            inset: 14px;
            border-radius: 22px;
            border: 1px dashed rgba(37,99,235,.12);
            pointer-events: none;
        }

        .image-stage img {
            width: 100%;
            max-height: 330px;
            object-fit: contain;
            position: relative;
            z-index: 1;
            animation: heroFloat 5.5s ease-in-out infinite;
            filter: drop-shadow(0 20px 32px rgba(15,23,42,.11));
        }

        .float-pill {
            position: absolute;
            z-index: 3;
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 8px 11px;
            border-radius: 999px;
            background: rgba(255,255,255,.95);
            border: 1px solid #e2eaf5;
            box-shadow: 0 11px 26px rgba(15,23,42,.08);
            font-size: 10px;
            font-weight: 700;
            color: #475569;
            backdrop-filter: blur(10px);
        }

        .float-pill.one { left: -16px; top: 22%; }
        .float-pill.two { right: -14px; bottom: 16%; }

        .float-pill i {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: #22c55e;
            box-shadow: 0 0 0 4px rgba(34,197,94,.10);
        }

        @media (max-width: 1024px) {
            .image-stage {
                width: min(82%, 440px);
            }

            .hero-main {
                transform: translateY(-4px);
            }
        }

        @media (max-width: 767px) {
            .auth-visual {
                min-height: 500px;
            }

            .hero-main {
                transform: translateY(0);
                padding-top: 18px;
                padding-bottom: 14px;
            }

            .image-stage {
                width: min(86%, 420px);
                margin-top: 14px;
            }
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

        /* CENTER DIVIDER SHADOW — visible between hero and form side */
        @media (min-width: 768px) {
            .auth-visual {
                position: relative;
                z-index: 2;
                overflow: visible;
                box-shadow:
                    18px 0 30px -22px rgba(15, 23, 42, 0.34),
                    10px 0 20px -18px rgba(37, 99, 235, 0.28);
            }

            /* Keep hero artwork clipped while allowing the center shadow outside */
            .auth-visual > .hero-orb {
                pointer-events: none;
            }
        }

        /* On mobile the two sections stack, so use a soft bottom divider shadow */
        @media (max-width: 767px) {
            .auth-visual {
                position: relative;
                z-index: 2;
                box-shadow:
                    0 18px 30px -24px rgba(15, 23, 42, 0.30),
                    0 10px 18px -18px rgba(37, 99, 235, 0.22);
            }
        }

    </style>

</head>
<body class="min-h-screen bg-gray-100">

<div class="min-h-screen flex flex-col md:flex-row p-3 sm:p-4 md:p-6">

    <!-- LEFT IMAGE SIDE IMPROVED -->
    <section class="auth-visual w-full md:w-1/2 min-h-[45vh] md:min-h-[calc(100vh-3rem)]
        rounded-t-2xl md:rounded-l-2xl md:rounded-tr-none border-l-4 border-blue-700">

        <div class="hero-orb one"></div>
        <div class="hero-orb two"></div>

        <div class="hero-content relative z-10 h-full flex flex-col px-6 sm:px-10 md:px-12 py-7 md:py-9">

            <a href="{{ url('/') }}" class="hero-logo">
                <img src="{{ asset('images/s-logo.png') }}" alt="SmartMeet logo">
                <span class="text-xl">SmartMeet</span>
            </a>

            <div class="hero-main">

                <div class="hero-copy">
                    <span class="hero-badge">Almost there</span>

                    <h1 class="text-3xl sm:text-4xl md:text-[40px] font-bold tracking-[-0.035em] text-slate-900 leading-tight">
                        Reset & Go!
                    </h1>

                    <p class="mt-2 text-sm sm:text-base text-slate-500 font-medium">
                        Your account is waiting —
                    </p>
                </div>

                <div class="image-stage">
                    <div class="float-pill one"><i></i> Secure password</div>
                    <div class="float-pill two"><i></i> Back in moments</div>
                    <img src="{{ asset('images/reset-password.jpg') }}" alt="Reset password illustration">
                </div>

            </div>
        </div>
    </section>

    <!-- RIGHT SIDE UNCHANGED -->
    <div class="w-full md:w-1/2 flex justify-center items-center bg-blue-50
    rounded-b-2xl md:rounded-r-2xl md:rounded-bl-none
    min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
    px-4 py-8 sm:px-6 md:px-8 border-r-4 border-blue-700">

        <div class="w-full max-w-md bg-white p-6 rounded-2xl
        shadow-lg border border-gray-100
        hover:shadow-xl transition-all duration-300">

            <div class="flex flex-col items-center mb-5">
                <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>

                <h2 class="text-lg font-semibold text-gray-800">Reset your password</h2>

                <p class="text-xs text-gray-400 mt-0.5 text-center">
                    Choose a strong new password for your account
                </p>
            </div>

            <div id="alert-container">
                @if ($errors->any())
                    <div class="auto-hide-alert mb-4 rounded-xl bg-red-50 border border-red-100 px-4 py-3">
                        <ul class="space-y-1">
                            @foreach ($errors->all() as $error)
                                <li class="flex items-center gap-2 text-xs text-red-500">
                                    <svg class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none"
                                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10"/>
                                        <line x1="12" y1="8" x2="12" y2="12"/>
                                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                                    </svg>
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if (session('success'))
                    <div class="auto-hide-alert mb-4 rounded-xl bg-green-50 border border-green-100 px-4 py-3 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 6L9 17l-5-5"/>
                        </svg>
                        <p class="text-xs text-green-600">{{ session('success') }}</p>
                    </div>
                @endif
            </div>

            <form action="{{ route('password.update') }}" method="POST" class="space-y-3" id="reset-form">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">
                <input type="hidden" name="email" value="{{ old('email', $email) }}">

                <div>
                    <label class="text-xs text-gray-400 mb-1 block">New password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password"
                            id="password"
                            placeholder="••••••••"
                            required
                            class="w-full px-3 py-2 pr-10 text-sm rounded-xl
                           border {{ $errors->has('password') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                           focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                           outline-none transition">

                        <button type="button"
                                id="toggle-password"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="eye-password" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Confirm password</label>
                    <div class="relative">
                        <input
                            type="password"
                            name="password_confirmation"
                            id="password_confirmation"
                            placeholder="••••••••"
                            required
                            class="w-full px-3 py-2 pr-10 text-sm rounded-xl
                           border {{ $errors->has('password_confirmation') ? 'border-red-300 bg-red-50' : 'border-gray-200 bg-gray-50' }}
                           focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                           outline-none transition">

                        <button type="button"
                                id="toggle-confirm"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <svg id="eye-confirm" class="w-4 h-4" viewBox="0 0 24 24" fill="none"
                                 stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                <circle cx="12" cy="12" r="3"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <button
                    type="submit"
                    class="w-full py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium
                   hover:bg-blue-700 hover:scale-[1.01] transition-all duration-200 mt-1">
                    Reset password
                </button>
            </form>

            <p class="text-center mt-4 text-xs text-gray-400">
                Remembered your password?
                <a href="/login" class="text-blue-600 hover:underline font-medium">Back to login</a>
            </p>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {

        function autoHide(el) {
            setTimeout(function () {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }, 2500);
        }

        document.querySelectorAll('.auto-hide-alert').forEach(autoHide);

        function togglePassword(inputId, eyeId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(eyeId);
            if (!input || !eye) return;

            if (input.type === 'password') {
                input.type = 'text';
                eye.innerHTML = `
                <path d="M17.94 17.94A10.94 10.94 0 0 1 12 20c-7 0-11-8-11-8a18.6 18.6 0 0 1 5.06-5.94"/>
                <path d="M9.9 4.24A10.94 10.94 0 0 1 12 4c7 0 11 8 11 8a18.6 18.6 0 0 1-2.16 3.19"/>
                <path d="M14.12 14.12a3 3 0 1 1-4.24-4.24"/>
                <line x1="1" y1="1" x2="23" y2="23"/>
                `;
            } else {
                input.type = 'password';
                eye.innerHTML = `
                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                <circle cx="12" cy="12" r="3"/>
                `;
            }
        }

        const eyePasswordBtn = document.getElementById('toggle-password');
        const eyeConfirmBtn  = document.getElementById('toggle-confirm');

        if (eyePasswordBtn) {
            eyePasswordBtn.addEventListener('click', function () {
                togglePassword('password', 'eye-password');
            });
        }

        if (eyeConfirmBtn) {
            eyeConfirmBtn.addEventListener('click', function () {
                togglePassword('password_confirmation', 'eye-confirm');
            });
        }

        const form = document.getElementById('reset-form');
        const alertContainer = document.getElementById('alert-container');

        function showErrorAlert(message) {
            const existing = document.getElementById('js-error-alert');
            if (existing) existing.remove();

            const div = document.createElement('div');
            div.id = 'js-error-alert';
            div.className = 'auto-hide-alert mb-4 rounded-xl bg-red-50 border border-red-100 px-4 py-3 flex items-center gap-2';
            div.innerHTML = `
            <svg class="w-3.5 h-3.5 text-red-500 shrink-0" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
            <p class="text-xs text-red-500">${message}</p>
            `;

            alertContainer.prepend(div);
            autoHide(div);
        }

        form.addEventListener('submit', function (e) {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirmation').value;

            const rules = {
                length: password.length >= 8,
                case: /[a-z]/.test(password) && /[A-Z]/.test(password),
                number: /[0-9]/.test(password),
                symbol: /[^A-Za-z0-9]/.test(password),
            };

            if (!rules.length || !rules.case || !rules.number || !rules.symbol) {
                e.preventDefault();
                showErrorAlert('Password must be at least 8 characters and include uppercase, lowercase, a number, and a special character.');
                return;
            }

            if (password !== confirm) {
                e.preventDefault();
                showErrorAlert('Password and confirm password do not match.');
                return;
            }
        });

    });
</script>

</body>
</html>
