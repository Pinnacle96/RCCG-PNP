<?php

class Paystack {
    public static function configured(): bool {
        return PAYSTACK_SECRET_KEY !== '' && !str_contains(PAYSTACK_SECRET_KEY, 'your_paystack_secret_key_here');
    }

    public static function initialize(array $payload): array {
        return self::request('POST', '/transaction/initialize', $payload);
    }

    public static function verify(string $reference): array {
        return self::request('GET', '/transaction/verify/' . rawurlencode($reference));
    }

    public static function validWebhookSignature(string $rawBody, ?string $signature): bool {
        if (!self::configured() || !$signature) {
            return false;
        }

        return hash_equals(hash_hmac('sha512', $rawBody, PAYSTACK_SECRET_KEY), $signature);
    }

    private static function request(string $method, string $path, array $payload = []): array {
        if (!self::configured()) {
            throw new RuntimeException('Paystack is not configured.');
        }

        $ch = curl_init(rtrim(PAYSTACK_BASE_URL, '/') . $path);
        $headers = [
            'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
            'Content-Type: application/json',
            'Cache-Control: no-cache',
        ];

        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ]);

        if ($method !== 'GET') {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        }

        $raw = curl_exec($ch);
        $error = curl_error($ch);
        $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($raw === false) {
            throw new RuntimeException('Paystack request failed: ' . $error);
        }

        $decoded = json_decode($raw, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid Paystack response.');
        }

        if ($status >= 400 || empty($decoded['status'])) {
            throw new RuntimeException($decoded['message'] ?? 'Paystack request failed.');
        }

        return $decoded;
    }
}
