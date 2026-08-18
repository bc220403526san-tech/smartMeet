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
        title="Oops! Locked Out"
        subtitle="We’ll help you get back in within seconds."
        image="images/forget-password.png"
    />
    <div class="flex w-full items-center justify-center bg-blue-50
    rounded-b-2xl md:rounded-r-2xl md:rounded-bl-none
    min-h-[55vh] sm:min-h-[60vh] md:min-h-[calc(100vh-3rem)]
    px-4 py-8 sm:px-6 md:px-8 lg:w-1/2 border-r-4 border-blue-700">
        <div class="w-full max-w-md bg-white p-6 rounded-2xl shadow-lg border border-gray-100
        hover:shadow-xl transition-all duration-300">
            <div class="flex flex-col items-center mb-6">
                <div class="w-11 h-11 rounded-full bg-blue-50 flex items-center justify-center mb-3">
                    <svg class="w-5 h-5 text-blue-600" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                </div>
                <h2 class="text-lg font-semibold text-gray-800">Forgot your password?</h2>
                <p class="text-xs text-gray-400 mt-0.5 text-center">
                    Enter your email and we'll send you a reset link
                </p>
            </div>

            {{-- SUCCESS MESSAGE --}}
            @if (session('success'))
                <div class="auto-hide-alert mb-4 rounded-xl bg-green-50 border border-green-100 px-4 py-3 flex items-center gap-2">
                    <svg class="w-3.5 h-3.5 text-green-500 shrink-0" viewBox="0 0 24 24" fill="none"
                         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M20 6L9 17l-5-5"/>
                    </svg>
                    <p class="text-xs text-green-600">{{ session('success') }}</p>
                </div>
            @endif

            {{-- ERROR MESSAGES --}}
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

            <form action="{{ route('password.email') }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label class="text-xs text-gray-400 block mb-1">Email address</label>
                    <input
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="Email"
                        required
                        class="w-full px-3 py-2 text-sm border border-gray-200 bg-gray-50 rounded-xl
                       focus:ring-2 focus:ring-blue-100 focus:border-blue-400
                       outline-none transition">
                </div>
                <button
                    type="submit"
                    class="w-full py-2.5 rounded-xl bg-blue-600 text-white text-sm font-medium
                   hover:bg-blue-700 hover:scale-[1.01] transition-all duration-200 mt-1">
                    Send Reset Link
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
        document.querySelectorAll('.auto-hide-alert').forEach(function (alert) {
            setTimeout(function () {
                alert.style.transition = 'opacity 0.5s ease';
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 500);
            }, 1500);
        });
    });
</script>
</body>
</html>
