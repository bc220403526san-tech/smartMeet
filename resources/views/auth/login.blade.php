<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APP_NAME') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&family=Urbanist:wght@500;600;700;800&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css','resources/js/app.js'])

    <style>
        :root{
            --brand:#2563eb;
            --brand-dark:#1d4ed8;
            --ink:#0f172a;
            --muted:#64748b;
            --line:#dbe4f0;
            --panel:#ffffff;
            --soft:#f8fbff;
        }

        html{scroll-behavior:smooth}

        body{
            font-family:'Manrope',sans-serif;
            background:
                radial-gradient(circle at 8% 12%, rgba(59,130,246,.10), transparent 26%),
                radial-gradient(circle at 92% 88%, rgba(96,165,250,.09), transparent 30%),
                #f7f9fc;
        }

        h1,h2,h3,.display-font{
            font-family:'Urbanist',sans-serif;
        }

        @keyframes fadeSlide{
            from{opacity:0;transform:translateY(18px)}
            to{opacity:1;transform:translateY(0)}
        }

        @keyframes floatIcon{
            0%,100%{transform:translateY(0)}
            50%{transform:translateY(-4px)}
        }

        @keyframes shine{
            from{left:-70%}
            to{left:130%}
        }

        .auth-panel{
            position:relative;
            overflow:hidden;
            background:
                radial-gradient(circle at 85% 10%, rgba(59,130,246,.10), transparent 28%),
                radial-gradient(circle at 15% 90%, rgba(37,99,235,.06), transparent 28%),
                linear-gradient(180deg,#ffffff 0%,#f8fbff 100%);
        }

        .auth-panel::before{
            content:"";
            position:absolute;
            width:220px;
            height:220px;
            border-radius:999px;
            top:-90px;
            right:-80px;
            background:linear-gradient(135deg,rgba(59,130,246,.14),rgba(147,197,253,.04));
            filter:blur(2px);
        }

        .auth-panel::after{
            content:"";
            position:absolute;
            width:170px;
            height:170px;
            border-radius:999px;
            bottom:-70px;
            left:-55px;
            border:1px solid rgba(37,99,235,.08);
            box-shadow:0 0 0 34px rgba(37,99,235,.025);
        }

        .auth-card{
            animation:fadeSlide .7s cubic-bezier(.22,1,.36,1) both;
            border:1px solid rgba(219,228,240,.92);
            box-shadow:
                0 28px 70px rgba(15,23,42,.10),
                0 8px 24px rgba(37,99,235,.04);
        }

        .auth-icon{
            animation:floatIcon 4s ease-in-out infinite;
            box-shadow:
                0 10px 24px rgba(37,99,235,.12),
                inset 0 1px 0 rgba(255,255,255,.9);
        }

        .premium-input{
            transition:
                border-color .22s ease,
                background-color .22s ease,
                box-shadow .22s ease,
                transform .22s ease;
        }

        .premium-input:hover{
            border-color:#c7d2e0;
            background:#fff;
        }

        .premium-input:focus{
            background:#fff;
            border-color:#60a5fa;
            box-shadow:0 0 0 4px rgba(59,130,246,.09);
            transform:translateY(-1px);
        }

        .primary-btn{
            position:relative;
            overflow:hidden;
            isolation:isolate;
            background:linear-gradient(135deg,#3b82f6 0%,#2563eb 55%,#1d4ed8 100%);
            box-shadow:
                0 12px 28px rgba(37,99,235,.24),
                inset 0 1px 0 rgba(255,255,255,.24);
        }

        .primary-btn::after{
            content:"";
            position:absolute;
            top:-55%;
            left:-70%;
            width:32%;
            height:210%;
            transform:rotate(18deg);
            background:linear-gradient(90deg,transparent,rgba(255,255,255,.38),transparent);
            pointer-events:none;
        }

        .primary-btn:hover::after{
            animation:shine .75s ease;
        }

        .social-btn{
            transition:
                transform .22s ease,
                box-shadow .22s ease,
                border-color .22s ease,
                background-color .22s ease;
        }

        .social-btn:hover{
            transform:translateY(-2px);
            box-shadow:0 10px 26px rgba(15,23,42,.07);
        }

        @media (prefers-reduced-motion:reduce){
            *,*::before,*::after{
                animation-duration:.01ms!important;
                animation-iteration-count:1!important;
                transition-duration:.01ms!important;
            }
        }
    </style>
</head>

<body class="min-h-screen text-slate-800">

<div class="min-h-screen flex flex-col md:flex-row p-3 sm:p-4 md:p-6">

    <x-auth-hero-section
        title="Welcome to Smartmeet"
        subtitle="Connect, Meet, Collaborate"
        image="images/meeting.png"
    />

    <div class="auth-panel w-full md:w-1/2 flex justify-center items-center
        rounded-b-3xl md:rounded-r-3xl md:rounded-bl-none
        min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
        px-4 py-8 sm:px-6 md:px-10 border-r-4 border-blue-700">

        <div class="relative z-10 w-full max-w-[460px]">

            <div class="auth-card bg-white rounded-[26px] p-6 sm:p-7 md:p-8">

                <div class="flex flex-col items-center mb-7">
                    <div class="auth-icon w-12 h-12 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100
                        border border-blue-100 flex items-center justify-center mb-3">

                        <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/>
                            <polyline points="10 17 15 12 10 7"/>
                            <line x1="15" y1="12" x2="3" y2="12"/>
                        </svg>
                    </div>

                    <h2 class="display-font text-[28px] leading-none font-bold tracking-[-0.03em] text-slate-900">
                        Welcome back
                    </h2>

                    <p class="text-[13px] text-slate-400 mt-2">
                        Log in to your account to continue
                    </p>
                </div>

                <x-success />
                <x-error />

                @if ($errors->any())
                    <div class="mb-4 px-3 py-2.5 bg-red-50 border border-red-200 rounded-xl">
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
                        <label class="text-[12px] font-semibold text-slate-600 block mb-1.5">
                            Email address
                        </label>

                        <input type="email" name="email" placeholder="Email"
                               value="{{ old('email') }}"
                               class="premium-input w-full px-3.5 py-3 text-sm border
                               {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-slate-200 bg-slate-50/80' }}
                               rounded-xl focus:ring-0 focus:outline-none"
                               required>

                        @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="text-[12px] font-semibold text-slate-600">Password</label>

                            <a href="{{ route('forgot.password') }}"
                               class="text-[12px] text-blue-600 hover:text-blue-700 hover:underline underline-offset-4 transition">
                                Forgot password?
                            </a>
                        </div>

                        <div class="relative">
                            <input type="password" name="password" id="login-password" placeholder="Your password"
                                   class="premium-input w-full px-3.5 py-3 pr-11 text-sm
                                   border border-slate-200 bg-slate-50/80 rounded-xl
                                   focus:ring-0 focus:outline-none"
                                   required>

                            <button type="button" onclick="togglePassword('login-password', this)"
                                    class="absolute inset-y-0 right-0 flex items-center px-3.5 text-slate-400 hover:text-blue-600 transition-colors">

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
                            class="primary-btn w-full py-3 rounded-xl text-white text-sm font-semibold
                            hover:-translate-y-0.5 hover:shadow-[0_16px_34px_rgba(37,99,235,0.30)]
                            active:translate-y-0 active:scale-[0.995]
                            transition-all duration-300">
                        Log in
                    </button>
                </form>

                <div class="flex items-center my-5">
                    <div class="flex-grow border-t border-slate-100"></div>
                    <span class="mx-3 text-slate-300 text-[11px] font-medium">or continue with</span>
                    <div class="flex-grow border-t border-slate-100"></div>
                </div>

                <div class="grid grid-cols-1 gap-2.5">

                    <a href="{{ route('social.redirect', 'google') }}"
                       class="social-btn w-full flex items-center justify-center gap-2.5
                       border border-slate-200 bg-white py-3 rounded-xl
                       hover:bg-slate-50 hover:border-slate-300
                       text-sm text-slate-600 font-semibold">

                        <svg class="w-4 h-4" viewBox="0 0 48 48">
                            <path fill="#FFC107" d="M43.6 20.5H42V20H24v8h11.3C33.6 32.7 29.2 36 24 36c-6.6 0-12-5.4-12-12s5.4-12 12-12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5 12.9 4.5 4 13.4 4 24.5S12.9 44.5 24 44.5 44 35.6 44 24.5c0-1.3-.1-2.7-.4-4z"/>
                            <path fill="#FF3D00" d="M6.3 14.7l6.6 4.8C14.5 16 18.9 12 24 12c3 0 5.7 1.1 7.8 2.9l5.7-5.7C33.9 6.5 29.2 4.5 24 4.5c-7.7 0-14.3 4.4-17.7 10.2z"/>
                            <path fill="#4CAF50" d="M24 44.5c5.1 0 9.8-2 13.4-5.2l-6.2-5.1c-2.1 1.5-4.7 2.3-7.2 2.3-5.2 0-9.6-3.3-11.2-7.9l-6.5 5C9.6 40.1 16.3 44.5 24 44.5z"/>
                            <path fill="#1976D2" d="M43.6 20.5H42V20H24v8h11.3c-1.1 3-3.5 5.4-6.6 6.9l6.2 5.1c3.6-3.3 5.7-8.1 5.7-13.5 0-1.3-.1-2.7-.4-4z"/>
                        </svg>

                        Continue with Google
                    </a>

                    <a href="{{ route('social.redirect', 'facebook') }}"
                       class="social-btn w-full flex items-center justify-center gap-2.5
                       border border-blue-200 bg-blue-50/70 py-3 rounded-xl
                       hover:bg-blue-100 hover:border-blue-300
                       text-sm text-blue-700 font-semibold">

                        <svg class="w-4 h-4 text-blue-600" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24v-8.437H7.078v-3.49h3.047V9.41c0-3.025 1.792-4.697 4.533-4.697 1.312 0 2.686.236 2.686.236v2.97h-1.513c-1.491 0-1.956.931-1.956 1.886v2.269h3.328l-.532 3.49h-2.796V24C19.612 23.094 24 18.1 24 12.073z"/>
                        </svg>

                        Continue with Facebook
                    </a>

                </div>

                <p class="text-center mt-5 text-[12px] text-slate-400">
                    Don't have an account?
                    <a href="{{ route('register') }}"
                       class="text-blue-600 hover:text-blue-700 hover:underline underline-offset-4 font-semibold transition">
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
