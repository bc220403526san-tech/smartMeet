<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Data Deletion | SmartMeet</title>

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
            User Data Deletion
        </h1>

        <p class="text-sm text-gray-500 mb-8">
            Last updated: September 2, 2026
        </p>

        <div class="space-y-7 text-gray-700 leading-relaxed">

            <p>
                SmartMeet respects your right to request deletion of your
                account and associated personal information.
            </p>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    1. How to Request Data Deletion
                </h2>

                <p>
                    If you signed in to SmartMeet using Facebook, Google,
                    or your email address, you may request deletion of your
                    SmartMeet account and associated personal data.
                </p>

                <p class="mt-3">
                    Contact the SmartMeet support team using the email address
                    associated with your SmartMeet account and state that you
                    would like your account and personal data deleted.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    2. Information to Include
                </h2>

                <p>
                    Please include your registered email address so that
                    SmartMeet can identify the correct account and process
                    your request securely.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    3. Facebook Login Users
                </h2>

                <p>
                    If you used Facebook Login, you may remove SmartMeet
                    from the apps and websites connected to your Facebook
                    account. Removing SmartMeet from Facebook does not
                    necessarily delete information already stored by
                    SmartMeet.
                </p>

                <p class="mt-3">
                    To request deletion of information stored by SmartMeet,
                    follow the deletion request instructions provided on
                    this page.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    4. What Happens After a Request
                </h2>

                <p>
                    After verifying the request, SmartMeet will process
                    deletion of the applicable account and associated
                    personal information, subject to information that may
                    need to be retained for legitimate security, fraud
                    prevention, or legal purposes.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    5. Privacy Policy
                </h2>

                <p>
                    For more information about how SmartMeet handles
                    personal information, please review our
                    <a href="{{ url('/privacy-policy') }}"
                       class="font-medium underline">
                        Privacy Policy
                    </a>.
                </p>
            </section>

            <section>
                <h2 class="text-xl font-semibold text-gray-900 mb-2">
                    6. Contact
                </h2>

                <p>
                    For account or data deletion requests, please contact
                    the SmartMeet support team.
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
