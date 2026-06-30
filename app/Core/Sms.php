<?php
/**
 * SMS gateway helper (Africa's Talking).
 *
 * Returns false (without throwing) when no real API key is configured, so the
 * admin SMS blast degrades gracefully on a fresh install and logs each attempt.
 */
class Sms {
    public static function configured(): bool {
        return defined('SMS_API_KEY')
            && SMS_API_KEY !== ''
            && strpos(SMS_API_KEY, 'your_') === false;
    }

    /** Send a single SMS. Returns true on gateway success. */
    public static function send(string $to, string $message): bool {
        if (!self::configured() || $to === '') {
            return false;
        }

        try {
            $ch = curl_init('https://api.africastalking.com/version1/messaging');
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => [
                    'apiKey: ' . SMS_API_KEY,
                    'Content-Type: application/x-www-form-urlencoded',
                    'Accept: application/json',
                ],
                CURLOPT_POSTFIELDS => http_build_query([
                    'username' => SMS_USERNAME,
                    'to' => $to,
                    'message' => $message,
                ]),
                CURLOPT_TIMEOUT => 20,
            ]);
            $response = curl_exec($ch);
            $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($status < 200 || $status >= 300 || $response === false) {
                return false;
            }
            $data = json_decode((string) $response, true);
            $recipients = $data['SMSMessageData']['Recipients'] ?? [];
            return !empty($recipients) && ($recipients[0]['status'] ?? '') === 'Success';
        } catch (\Throwable $e) {
            error_log('SMS send failed: ' . $e->getMessage());
            return false;
        }
    }
}
