<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('images/s-logo.png') }}">
    <title>{{ env('APPNAME') }}</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-100">
<div class="min-h-screen flex flex-col md:flex-row p-3 sm:p-4 md:p-6">
    <x-auth-hero-section
        title="Reset & Go!"
        subtitle="Your account is waiting —"
        image="images/reset-password.jpg"
    />
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
                <p class="text-xs text-gray-400 mt-0.5 text-center">Choose a strong new password for your account</p>
            </div>

            {{-- CONTAINER for both server-side and JS-generated alerts --}}
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

                <!-- NEW PASSWORD -->
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

                <!-- CONFIRM PASSWORD -->
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

        // ---------- AUTO-HIDE EXISTING (server-side) ALERTS ----------
        function autoHide(el) {
            setTimeout(function () {
                el.style.transition = 'opacity 0.5s ease';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }, 2500);
        }
        document.querySelectorAll('.auto-hide-alert').forEach(autoHide);

        // ---------- PASSWORD EYE TOGGLE ----------
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

        // ---------- VALIDATE ON SUBMIT (show error alert at top) ----------
        const form = document.getElementById('reset-form');
        const alertContainer = document.getElementById('alert-container');

        function showErrorAlert(message) {
            // purana JS alert hata do (agar pehle se ek dikha hua ho)
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
