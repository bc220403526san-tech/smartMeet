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
    px-4 py-6 sm:px-6 md:px-8 border-r-4 border-blue-700">
        <!-- CARD -->
        <div class="w-full max-w-md bg-white/80 backdrop-blur-xl
        p-5 sm:p-6 rounded-2xl
        shadow-lg border border-white/60
        hover:shadow-xl transition-all duration-300">
            <!-- ICON + TITLE -->
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

            <!-- MESSAGES (compact, single block) -->
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
                <!-- FULL NAME -->
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Full name</label>
                    <input type="text" name="name" placeholder="name" value="{{ old('name') }}"
                           class="w-full px-3 py-2 text-sm rounded-xl
                   border border-gray-200 bg-gray-50
                   focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                   outline-none transition"
                           required>
                </div>
                <!-- EMAIL -->
                <div>
                    <label class="text-xs text-gray-400 mb-1 block">Email address</label>
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}"
                           class="w-full px-3 py-2 text-sm rounded-xl
                   border border-gray-200 bg-gray-50
                   focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                   outline-none transition"
                           required>
                </div>
                <!-- PASSWORD + CONFIRM (side by side to save height) -->
                <div class="grid grid-cols-2 gap-3">
                    <!-- PASSWORD -->
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
                    <!-- CONFIRM PASSWORD -->
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
                <!-- STRENGTH METER (slim single bar, not tall) -->
                <div class="-mt-1">
                    <div class="w-full h-1 bg-gray-100 rounded-full overflow-hidden">
                        <div id="strength-bar" class="h-full w-0 rounded-full transition-all duration-300 bg-gray-200"></div>
                    </div>
                    <p id="strength-label" class="text-[10.5px] mt-0.5 text-gray-400">
                        8+ chars, upper &amp; lower case, number, symbol
                    </p>
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
                <div class="flex items-center gap-2 pt-0.5">
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
