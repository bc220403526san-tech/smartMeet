
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

        @media (max-width:520px){
            .password-grid{
                grid-template-columns:1fr!important;
            }
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
        image="images/login-illustration.png"
    />

    <div class="auth-panel w-full md:w-1/2 flex justify-center items-center
        rounded-b-3xl md:rounded-r-3xl md:rounded-bl-none
        min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
        px-4 py-6 sm:px-6 md:px-10 border-r-4 border-blue-700">

        <div class="relative z-10 w-full max-w-[470px]">

            <div class="auth-card bg-white rounded-[26px] p-5 sm:p-6 md:p-7">

                <div class="flex flex-col items-center mb-5">
                    <div class="auth-icon w-11 h-11 rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100
                        border border-blue-100 flex items-center justify-center mb-2.5">

                        <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>

                    <h2 class="display-font text-[27px] leading-none font-bold tracking-[-0.03em] text-slate-900">
                        Create an account
                    </h2>

                    <p class="text-[12px] text-slate-400 mt-2">
                        Fill in your details below to get started
                    </p>
                </div>

                <x-success />
                <x-error />

                @if ($errors->any())
                    <div class="mb-3 px-3 py-2 bg-red-50 border border-red-100 rounded-xl">
                        <ul class="text-xs text-red-600 space-y-0.5 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="post" action="{{ route('register') }}" class="space-y-3" id="register-form">
                    @csrf

                    <div>
                        <label class="text-[12px] font-semibold text-slate-600 mb-1.5 block">Full name</label>

                        <input type="text" name="name" placeholder="name" value="{{ old('name') }}"
                               class="premium-input w-full px-3.5 py-2.5 text-sm rounded-xl
                               border border-slate-200 bg-slate-50/80
                               focus:ring-0 focus:outline-none"
                               required>
                    </div>

                    <div>
                        <label class="text-[12px] font-semibold text-slate-600 mb-1.5 block">Email address</label>

                        <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                               class="premium-input w-full px-3.5 py-2.5 text-sm rounded-xl
                               border {{ $errors->has('email') ? 'border-red-300 bg-red-50' : 'border-slate-200 bg-slate-50/80' }}
                               focus:ring-0 focus:outline-none"
                               required>

                        @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="password-grid grid grid-cols-2 gap-3">

                        <div>
                            <label class="text-[12px] font-semibold text-slate-600 mb-1.5 block">Password</label>

                            <div class="relative">
                                <input type="password" name="password" id="reg-password" placeholder="password"
                                       class="premium-input w-full px-3.5 py-2.5 pr-10 text-sm rounded-xl
                                       border border-slate-200 bg-slate-50/80
                                       focus:ring-0 focus:outline-none"
                                       oninput="checkStrength(this.value)"
                                       required>

                                <button type="button" onclick="togglePassword('reg-password', this)"
                                        class="absolute inset-y-0 right-0 flex items-center px-3
                                        text-slate-400 hover:text-blue-600 transition-colors">

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
                            <label class="text-[12px] font-semibold text-slate-600 mb-1.5 block">Confirm</label>

                            <div class="relative">
                                <input type="password" name="password_confirmation" id="reg-password-confirm" placeholder="confirm"
                                       class="premium-input w-full px-3.5 py-2.5 pr-10 text-sm rounded-xl
                                       border border-slate-200 bg-slate-50/80
                                       focus:ring-0 focus:outline-none"
                                       required>

                                <button type="button" onclick="togglePassword('reg-password-confirm', this)"
                                        class="absolute inset-y-0 right-0 flex items-center px-3
                                        text-slate-400 hover:text-blue-600 transition-colors">

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

                        <p id="strength-label" class="text-[10.5px] mt-1 text-slate-400">
                            8+ chars, upper &amp; lower case, number, symbol
                        </p>
                    </div>

                    <div>
                        <label class="text-[12px] font-semibold text-slate-600 block mb-1.5">Role</label>

                        <div class="relative">
                            <select name="role"
                                    class="premium-input w-full appearance-none px-3.5 py-2.5 text-sm
                                    border border-slate-200 rounded-xl
                                    bg-slate-50/80 text-slate-700
                                    focus:outline-none focus:ring-0">

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

                        <p class="text-[11px] text-slate-400">
                            I agree to the
                            <a href="#" class="text-blue-600 hover:text-blue-700 hover:underline underline-offset-4">
                                Terms and Conditions
                            </a>
                        </p>
                    </div>

                    <button type="submit"
                            class="primary-btn w-full py-2.5 rounded-xl
                            text-white text-sm font-semibold
                            hover:-translate-y-0.5 hover:shadow-[0_16px_34px_rgba(37,99,235,0.30)]
                            active:translate-y-0 active:scale-[0.995]
                            transition-all duration-300 mt-1">
                        Register
                    </button>

                </form>

                <p class="text-center mt-4 text-[11px] text-slate-400">
                    Already have an account?
                    <a href="/login"
                       class="text-blue-600 hover:text-blue-700 hover:underline underline-offset-4 font-semibold">
                        Log in
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
            label.className = 'text-[10.5px] mt-1 text-slate-400';
            return;
        }

        const idx = Math.max(score - 1, 0);
        bar.style.width = widths[idx];
        bar.className = 'h-full rounded-full transition-all duration-300 ' + colors[idx];
        label.textContent = labels[idx];
        label.className = 'text-[10.5px] mt-1 ' + textColors[idx];
    }
</script>
</body>
</html>
