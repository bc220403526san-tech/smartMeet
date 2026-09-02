<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Privacy Policy | SmartMeet</title>

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
            Privacy Policy
        </h1>

        <p class="text-sm text-gray-500 mb-8">
            Last updated: September 2, 2026
        </p>

        <div class="space-y-7 text-gray-700 leading-relaxed">

            <p>
                SmartMeet respects your privacy and is committed to protecting
                the personal information you provide while using our platform.
            </p>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    1. Information We Collect
                </h2>

                <p>
                    We may collect information such as your name, email address,
                    profile information, account information, and information
                    associated with your use of SmartMeet.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    2. Social Login Information
                </h2>

                <p>
                    When you choose to sign in using Facebook or Google,
                    SmartMeet may receive basic profile information made
                    available by that provider, such as your name, email
                    address, profile identifier, and profile picture,
                    depending on the permissions you grant.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    3. How We Use Your Information
                </h2>

                <p>
                    We use your information to create and manage your account,
                    authenticate users, provide meeting functionality, improve
                    SmartMeet, communicate with users, and maintain the security
                    of our platform.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    4. Information Sharing
                </h2>

                <p>
                    We do not sell your personal information. Information may
                    only be shared when necessary to provide the service,
                    comply with legal obligations, protect our users, or
                    maintain the security of SmartMeet.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    5. Data Security
                </h2>

                <p>
                    We take reasonable technical and organizational measures
                    to protect personal information against unauthorized access,
                    loss, misuse, disclosure, or alteration.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    6. Data Retention and Deletion
                </h2>

                <p>
                    Personal information is retained only as necessary to
                    provide SmartMeet and meet applicable requirements.
                    Users may request deletion of their account and associated
                    personal data by following our Data Deletion instructions.
                </p>

                <p class="mt-3">
                    <a href="{{ url('/data-deletion') }}"
                       class="font-medium underline">
                        View Data Deletion Instructions
                    </a>
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    7. Changes to This Policy
                </h2>

                <p>
                    We may update this Privacy Policy when necessary.
                    Changes will be published on this page with an updated date.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    8. Contact Us
                </h2>

                <p>
                    If you have questions about this Privacy Policy or your
                    personal information, please contact the SmartMeet support team.
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
