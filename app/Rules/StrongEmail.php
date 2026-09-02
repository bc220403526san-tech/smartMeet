<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class StrongEmail implements ValidationRule
{
    /**
     * Strong domain-level email validation.
     *
     * Important:
     * - This rejects malformed addresses, single-label domains such as user@dfvvv,
     *   and domains that do not publish an MX record.
     * - It cannot prove that an individual mailbox such as random@gmail.com exists.
     *   Mailbox ownership/existence must still be confirmed through email verification
     *   and/or delivery/bounce events from the mail provider.
     */
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail
    ): void {
        $email = strtolower(trim((string) $value));

        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $fail('Please enter a valid email address.');
            return;
        }

        $atPosition = strrpos($email, '@');

        if ($atPosition === false) {
            $fail('Please enter a valid email address.');
            return;
        }

        $domain = substr($email, $atPosition + 1);
        $domain = rtrim($domain, '.');

        // Reject local/single-label domains such as user@dfvvv.
        if (
            $domain === '' ||
            !str_contains($domain, '.') ||
            str_starts_with($domain, '.') ||
            str_ends_with($domain, '.')
        ) {
            $fail('Please enter an email address with a valid public mail domain.');
            return;
        }

        // Convert internationalized domains when ext-intl is available.
        if (function_exists('idn_to_ascii')) {
            $asciiDomain = idn_to_ascii($domain, IDNA_DEFAULT);

            if (is_string($asciiDomain) && $asciiDomain !== '') {
                $domain = $asciiDomain;
            }
        }

        // Require a real mail exchanger. This is deliberately stricter than
        // accepting an A/AAAA-only fallback.
        if (!function_exists('checkdnsrr') || !checkdnsrr($domain, 'MX')) {
            $fail('The email domain cannot receive email or does not exist.');
            return;
        }
    }
}
