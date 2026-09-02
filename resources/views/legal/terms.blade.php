<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Terms of Service | SmartMeet</title>

    @vite(['resources/css/app.css'])
</head>

<body class="bg-gray-50 text-gray-900">

<header class="bg-white border-b border-gray-200">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-5">
        <a href="{{ url('/') }}" class="text-2xl font-bold text-gray-900">
            SmartMeet
        </a>
    </div>
</header>

<main>
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-10">

        <h1 class="text-3xl sm:text-4xl font-bold text-gray-900 mb-2">
            Terms of Service
        </h1>

        <p class="text-sm text-gray-500 mb-8">
            Last updated: September 2, 2026
        </p>

        <div class="space-y-7 text-gray-700 leading-relaxed">

            <p>
                These Terms of Service govern your access to and use of
                SmartMeet. By accessing or using SmartMeet, you agree to
                these Terms.
            </p>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    1. Use of SmartMeet
                </h2>

                <p>
                    SmartMeet provides online meeting, communication, and
                    collaboration features. You agree to use the platform
                    responsibly and in accordance with applicable laws.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    2. User Accounts
                </h2>

                <p>
                    You are responsible for providing accurate account
                    information and maintaining the security of your account.
                    You are also responsible for activities performed through
                    your account.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    3. Social Login
                </h2>

                <p>
                    SmartMeet may allow authentication through third-party
                    services such as Facebook and Google. Your use of those
                    services may also be subject to the respective provider's
                    terms and privacy policies.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    4. Acceptable Use
                </h2>

                <p>
                    You must not use SmartMeet for unlawful, abusive,
                    fraudulent, harmful, or unauthorized activities.
                    You must not attempt to interfere with the security,
                    availability, or normal operation of the platform.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    5. Meetings and User Content
                </h2>

                <p>
                    Users are responsible for the meetings they create,
                    communications they make, and content they share through
                    SmartMeet. Users must have the necessary rights to any
                    content they upload or share.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    6. Service Availability
                </h2>

                <p>
                    We aim to keep SmartMeet available and reliable, but
                    uninterrupted or error-free operation cannot be guaranteed
                    at all times.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    7. Account Suspension or Termination
                </h2>

                <p>
                    SmartMeet may restrict or terminate access when an account
                    violates these Terms, creates security risks, or is used
                    for unlawful or abusive activities.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    8. Privacy
                </h2>

                <p>
                    Information about how SmartMeet handles personal
                    information is available in our
                    <a href="{{ url('/privacy-policy') }}"
                       class="font-medium underline">
                        Privacy Policy
                    </a>.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    9. Changes to These Terms
                </h2>

                <p>
                    We may update these Terms when necessary. Updated Terms
                    will be published on this page with an updated date.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    10. Contact
                </h2>

                <p>
                    For questions regarding these Terms of Service,
                    please contact the SmartMeet support team.
                </p>
            </section>

        </div>
    </div>
</main>

<footer class="bg-white border-t border-gray-200 mt-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 py-6 text-center text-sm text-gray-500">
        &copy; {{ date('Y') }} SmartMeet. All rights reserved.
    </div>
</footer>

</body>
</html>
