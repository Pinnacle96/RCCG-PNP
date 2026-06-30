<?php
/**
 * QR Code helper.
 *
 * Wraps endroid/qr-code to produce a base64 PNG data URI that can be embedded
 * directly in an <img> tag — no extra HTTP endpoint or temp file required.
 * Used for member attendance QR codes (SSOT §9.7).
 */
class Qr {
    public static function dataUri(string $text): string {
        if ($text === '' || !class_exists(\Endroid\QrCode\QrCode::class)) {
            return '';
        }
        try {
            $qrCode = new \Endroid\QrCode\QrCode($text);
            $writer = new \Endroid\QrCode\Writer\PngWriter();
            return $writer->write($qrCode)->getDataUri();
        } catch (\Throwable $e) {
            error_log('QR generation failed: ' . $e->getMessage());
            return '';
        }
    }
}
