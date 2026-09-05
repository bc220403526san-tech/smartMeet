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

<div class="min-h-screen flex flex-col md:flex-row p-3 sm:p-4 md:p-6">

    <x-auth-hero-section
        title="Welcome to Smartmeet"
        subtitle="Connect, Meet, Collaborate"
        image="images/login-illustration.png"
    />

    <div class="auth-panel relative overflow-hidden w-full md:w-1/2 flex justify-center items-center
        bg-gradient-to-br from-blue-50/90 via-white to-sky-50/80
        rounded-b-3xl md:rounded-r-3xl md:rounded-bl-none
        min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
        px-4 py-6 sm:px-6 md:px-8 border-r-4 border-blue-700">

        <div class="absolute -top-24 -right-24 w-72 h-72 rounded-full bg-blue-200/20 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-28 -left-24 w-80 h-80 rounded-full bg-sky-200/20 blur-3xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.035] pointer-events-none"
             style="background-image: linear-gradient(#2563eb 1px, transparent 1px), linear-gradient(90deg,#2563eb 1px, transparent 1px); background-size: 32px 32px;"></div>

        <div class="auth-card relative w-full max-w-md bg-white/90 backdrop-blur-xl
            p-5 sm:p-6 rounded-[1.35rem]
            shadow-[0_24px_70px_rgba(15,23,42,0.10)]
            border border-white/80
            hover:shadow-[0_30px_90px_rgba(15,23,42,0.13)]
            transition-all duration-500">

            <div class="flex flex-col items-center mb-4">
                <div class="auth-icon w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100
                    border border-blue-100 shadow-sm flex items-center justify-center mb-2.5">

                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>

                </div>

                <h2 class="text-xl font-semibold tracking-[-0.02em] text-slate-900">
                    Create an account
                </h2>

                <p class="text-xs text-slate-400 mt-1">
                    Fill in your details below to get started
                </p>
            </div>

            <x-success />
            <x-error />

            @if ($errors->any())
                <div class="mb-3 px-3 py-2 bg-red-50 border border-red-100 rounded-xl shadow-sm">
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
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Full name</label>

                    <input type="text" name="name" placeholder="name" value="{{ old('name') }}"
                           class="premium-input w-full px-3 py-2 text-sm rounded-xl
                           border border-slate-200 bg-slate-50/80
                           focus:ring-0 focus:border-blue-400 outline-none"
                           required>
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500 mb-1 block">Email address</label>

                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                           class="premium-input w-full px-3 py-2 text-sm rounded-xl
                           border {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-slate-200 bg-slate-50/80' }}
                           focus:ring-0 focus:border-blue-400 outline-none"
                           required>

                    @error('email')
                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">

                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Password</label>

                        <div class="relative">
                            <input type="password" name="password" id="reg-password" placeholder="password"
                                   class="premium-input w-full px-3 py-2 pr-9 text-sm rounded-xl
                                   border border-slate-200 bg-slate-50/80
                                   focus:ring-0 focus:border-blue-400 outline-none"
                                   oninput="checkStrength(this.value)"
                                   required>

                            <button type="button" onclick="togglePassword('reg-password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-2.5
                                    text-slate-400 hover:text-blue-600 transition-colors duration-200">

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

                    <div>
                        <label class="text-xs font-medium text-slate-500 mb-1 block">Confirm</label>

                        <div class="relative">
                            <input type="password" name="password_confirmation" id="reg-password-confirm" placeholder="confirm"
                                   class="premium-input w-full px-3 py-2 pr-9 text-sm rounded-xl
                                   border border-slate-200 bg-slate-50/80
                                   focus:ring-0 focus:border-blue-400 outline-none"
                                   required>

                            <button type="button" onclick="togglePassword('reg-password-confirm', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-2.5
                                    text-slate-400 hover:text-blue-600 transition-colors duration-200">

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

                </div>

                <div class="-mt-1">
                    <div class="w-full h-1 bg-slate-100 rounded-full overflow-hidden">
                        <div id="strength-bar"
                             class="h-full w-0 rounded-full transition-all duration-300 bg-slate-200"></div>
                    </div>

                    <p id="strength-label" class="text-[10.5px] mt-0.5 text-slate-400">
                        8+ chars, upper &amp; lower case, number, symbol
                    </p>
                </div>

                <div>
                    <label class="text-xs font-medium text-slate-500 block mb-1">Role</label>

                    <div class="relative">
                        <select name="role"
                                class="premium-input w-full appearance-none px-3 py-2 text-sm
                                border border-slate-200 rounded-xl
                                bg-slate-50/80 text-slate-700
                                focus:outline-none focus:ring-0 focus:border-blue-400">

                            <option selected disabled>Select a role</option>
                            <option value="organizer">Organizer</option>
                            <option value="participant">Participant</option>

                        </select>

                        <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none">
                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2"
                                 viewBox="0 0 24 24">
                                <path d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2 pt-0.5">

                    <input type="checkbox" name="terms"
                           class="w-3.5 h-3.5 accent-blue-600 border-slate-300 rounded">

                    <p class="text-xs text-slate-400">
                        I agree to the
                        <a href="#"
                           class="text-blue-600 hover:text-blue-700 hover:underline underline-offset-4 transition">
                            Terms and Conditions
                        </a>
                    </p>

                </div>

                <button type="submit"
                        class="premium-button w-full py-2.5 rounded-xl
                        bg-gradient-to-r from-blue-600 via-blue-600 to-blue-700
                        text-white text-sm font-semibold
                        hover:-translate-y-0.5
                        hover:shadow-[0_15px_32px_rgba(37,99,235,0.28)]
                        active:translate-y-0 active:scale-[0.995]
                        transition-all duration-300 mt-1">
                    Register
                </button>

            </form>

            <p class="text-center mt-3 text-xs text-slate-400">
                Already have an account?
                <a href="/login"
                   class="text-blue-600 hover:text-blue-700 hover:underline underline-offset-4 font-semibold transition">
                    Log in
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
            bar.className = 'h-full w-0 rounded-full transition-all duration-300 bg-slate-200';
            label.textContent = '8+ chars, upper & lower case, number, symbol';
            label.className = 'text-[10.5px] mt-0.5 text-slate-400';
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
