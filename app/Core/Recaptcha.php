<?php

/**
 * Google reCAPTCHA v3 server-side verification.
 *
 * Degrades gracefully: when keys are not configured (placeholder/blank), every
 * check passes so local dev and unconfigured installs keep working. Enforcement
 * only kicks in once real keys are supplied via config.local.php.
 */
class Recaptcha {
    /** True only when a real secret key is configured. */
    public static function configured(): bool {
        $secret = defined('RECAPTCHA_SECRET_KEY') ? (string) RECAPTCHA_SECRET_KEY : '';
        return $secret !== '' && strpos($secret, 'your_') === false;
    }

    /** Whether the site key is configured (controls front-end script loading). */
    public static function siteConfigured(): bool {
        $site = defined('RECAPTCHA_SITE_KEY') ? (string) RECAPTCHA_SITE_KEY : '';
        return $site !== '' && strpos($site, 'your_') === false;
    }

    /**
     * Verify a reCAPTCHA token. Returns true when verification passes OR when
     * reCAPTCHA is not configured (graceful no-op).
     */
    public static function verify(?string $token): bool {
        if (!self::configured()) {
            return true;
        }
        if (!$token) {
            return false;
        }

        try {
            $ch = curl_init('https://www.google.com/recaptcha/api/siteverify');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => http_build_query([
                    'secret' => RECAPTCHA_SECRET_KEY,
                    'response' => $token,
                    'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
                ]),
                CURLOPT_TIMEOUT => 8,
            ]);
            $raw = curl_exec($ch);
            curl_close($ch);
            if ($raw === false) {
                return false;
            }

            $result = json_decode((string) $raw, true);
            if (empty($result['success'])) {
                return false;
            }
            // v3 returns a score; v2 checkbox responses have no score.
            if (isset($result['score'])) {
                $min = defined('RECAPTCHA_MIN_SCORE') ? (float) RECAPTCHA_MIN_SCORE : 0.5;
                return (float) $result['score'] >= $min;
            }
            return true;
        } catch (\Throwable $e) {
            error_log('reCAPTCHA verification error: ' . $e->getMessage());
            return false;
        }
    }
}
