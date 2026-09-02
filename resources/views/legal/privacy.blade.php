<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/s-logo.png') }}">
    <title>Privacy Policy | SmartMeet</title>
    @vite('resources/css/app.css')
</head>

<body class="min-h-screen bg-gray-50 text-gray-800 antialiased">
<div class="min-h-screen flex flex-col">

    <header class="sticky top-0 z-30 bg-white/95 backdrop-blur border-b border-gray-200 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 sm:px-6">
            <div class="min-h-16 py-3 flex items-center justify-between gap-4">
                <a href="{{ url('/') }}" class="flex items-center gap-3 group">
                    <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white border border-gray-200 shadow-sm flex items-center justify-center overflow-hidden">
                        <img src="{{ asset('images/s-logo.png') }}"
                             alt="SmartMeet Logo"
                             class="w-8 h-8 sm:w-9 sm:h-9 object-contain">
                    </div>
                    <div>
                        <div class="text-lg sm:text-xl font-extrabold tracking-tight text-gray-900 group-hover:text-orange-700 transition">
                            SmartMeet
                        </div>
                        <div class="hidden sm:block text-[11px] text-gray-500">
                            Meet. Connect. Collaborate.
                        </div>
                    </div>
                </a>

                <a href="{{ url('/') }}"
                   class="inline-flex items-center gap-2 px-3 sm:px-4 py-2 rounded-lg border border-gray-200 bg-white text-xs sm:text-sm font-semibold text-gray-700 hover:bg-gray-50 transition">
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 18l-6-6 6-6"/>
                    </svg>
                    <span class="hidden xs:inline">Back to Home</span>
                    <span class="xs:hidden">Home</span>
                </a>
            </div>
        </div>
    </header>

    <main class="flex-1">
        <section class="border-b border-gray-200 bg-gradient-to-b from-white to-orange-50/40">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 py-10 sm:py-14">
                <div class="max-w-3xl">
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 mb-4 rounded-full border border-orange-200 bg-orange-50 text-xs font-bold text-orange-700 uppercase tracking-wide">
                        SmartMeet Legal
                    </div>
                    <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-gray-900">
                        Privacy Policy
                    </h1>
                    <p class="mt-4 text-sm sm:text-base text-gray-600 leading-relaxed">
                        Learn how SmartMeet collects, uses, protects, and manages your personal information.
                    </p>
                    <p class="mt-3 text-xs sm:text-sm text-gray-500">
                        Last updated: September 2, 2026
                    </p>
                </div>
            </div>
        </section>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 sm:py-10">
            <nav class="flex flex-wrap gap-2 mb-6 sm:mb-8" aria-label="Legal pages">
                <a href="{{ url('/privacy-policy') }}" class="px-3 py-2 rounded-lg border text-xs sm:text-sm font-semibold transition bg-orange-50 text-orange-700 border-orange-200">Privacy</a>
                <a href="{{ url('/terms') }}" class="px-3 py-2 rounded-lg border text-xs sm:text-sm font-semibold transition bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900">Terms</a>
                <a href="{{ url('/data-deletion') }}" class="px-3 py-2 rounded-lg border text-xs sm:text-sm font-semibold transition bg-white text-gray-600 border-gray-200 hover:bg-gray-50 hover:text-gray-900">Data Deletion</a>

            </nav>

            <article class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="p-5 sm:p-8 lg:p-10">
                    <div class="space-y-8 text-sm sm:text-base text-gray-600 leading-7">

                        <p class="text-base sm:text-lg text-gray-700">
                            SmartMeet respects your privacy and is committed to protecting the personal information you provide while using our platform.
                        </p>
                        <section>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">1</div>
                                <div class="min-w-0">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Information We Collect</h2>

                                    <p>We may collect information such as your name, email address, profile information, account information, and information associated with your use of SmartMeet.</p>

                                </div>
                            </div>
                        </section><section>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">2</div>
                                <div class="min-w-0">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Social Login Information</h2>

                                    <p>When you choose to sign in using Facebook or Google, SmartMeet may receive basic profile information made available by that provider, such as your name, email address, profile identifier, and profile picture, depending on the permissions you grant.</p>

                                </div>
                            </div>
                        </section><section>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">3</div>
                                <div class="min-w-0">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">How We Use Your Information</h2>

                                    <p>We use your information to create and manage your account, authenticate users, provide meeting functionality, improve SmartMeet, communicate with users, and maintain the security of our platform.</p>

                                </div>
                            </div>
                        </section><section>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">4</div>
                                <div class="min-w-0">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Information Sharing</h2>

                                    <p>We do not sell your personal information. Information may only be shared when necessary to provide the service, comply with legal obligations, protect our users, or maintain the security of SmartMeet.</p>

                                </div>
                            </div>
                        </section><section>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">5</div>
                                <div class="min-w-0">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Data Security</h2>

                                    <p>We take reasonable technical and organizational measures to protect personal information against unauthorized access, loss, misuse, disclosure, or alteration.</p>

                                </div>
                            </div>
                        </section><section>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">6</div>
                                <div class="min-w-0">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Data Retention and Deletion</h2>

                                    <p>Personal information is retained only as necessary to provide SmartMeet and meet applicable requirements. Users may request deletion of their account and associated personal data by following our Data Deletion instructions.</p>
                                    <a href="{{ url('/data-deletion') }}" class="inline-flex mt-3 font-semibold text-orange-700 hover:underline">View Data Deletion Instructions →</a>

                                </div>
                            </div>
                        </section><section>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">7</div>
                                <div class="min-w-0">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Changes to This Policy</h2>

                                    <p>We may update this Privacy Policy when necessary. Changes will be published on this page with an updated date.</p>

                                </div>
                            </div>
                        </section><section>
                            <div class="flex items-start gap-3">
                                <div class="shrink-0 w-8 h-8 rounded-lg bg-orange-50 border border-orange-100 text-orange-700 flex items-center justify-center text-sm font-bold">8</div>
                                <div class="min-w-0">
                                    <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-2">Contact Us</h2>

                                    <p>If you have questions about this Privacy Policy or your personal information, please contact the SmartMeet support team.</p>

                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </article>
        </div>
    </main>

    <footer class="border-t border-gray-200 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 py-7">
            <p class="text-xs sm:text-sm text-gray-500 text-center">
                &copy; {{ date('Y') }} SmartMeet. All rights reserved.
            </p>
        </div>
    </footer>

</div>
</body>
</html>
