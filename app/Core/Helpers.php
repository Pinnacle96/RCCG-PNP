<?php
/**
 * Helper Functions
 * Utility functions used throughout the application
 */

class Helpers {
    /**
     * Generate URL-safe slug from string
     */
    public static function slug(string $title, string $separator = '-'): string {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]+/', $separator, $slug);
        $slug = preg_replace('/' . $separator . '+/', $separator, $slug);
        $slug = trim($slug, $separator);
        return $slug;
    }

    /**
     * Generate unique slug with suffix if exists
     */
    public static function uniqueSlug(string $title, string $table, string $column, ?string $id = null): string {
        $slug = self::slug($title);
        $exists = Database::fetchOne(
            "SELECT 1 FROM `$table` WHERE `$column` = ?" . ($id ? " AND id != ?" : ""),
            array_merge([$slug], $id ? [$id] : [])
        );

        if (!$exists) {
            return $slug;
        }

        $newSlug = $slug;
        $counter = 2;
        while ($exists) {
            $newSlug = $slug . '-' . $counter;
            $exists = Database::fetchOne(
                "SELECT 1 FROM `$table` WHERE `$column` = ?" . ($id ? " AND id != ?" : ""),
                array_merge([$newSlug], $id ? [$id] : [])
            );
            $counter++;
        }

        return $newSlug;
    }

    /**
     * Format currency (NGN)
     */
    public static function currency(float $amount): string {
        return CURRENCY_SYMBOL . number_format($amount, 2);
    }

    /**
     * Format date to readable string
     */
    public static function formatDate(string $date, string $format = 'F d, Y'): string {
        return date($format, strtotime($date));
    }

    /**
     * Format datetime to readable string
     */
    public static function formatDateTime(string $datetime, string $format = 'F d, Y h:i A'): string {
        return date($format, strtotime($datetime));
    }

    /**
     * Get relative time (e.g., "2 hours ago")
     */
    public static function timeAgo(string $datetime): string {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;

        if ($diff < 60) return 'Just now';
        if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
        if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
        if ($diff < 2592000) return floor($diff / 86400) . ' days ago';
        if ($diff < 31536000) return floor($diff / 2592000) . ' months ago';
        return floor($diff / 31536000) . ' years ago';
    }

    /**
     * Generate random string
     */
    public static function randomString(int $length = 16): string {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charsLength = strlen($chars);
        $result = '';
        for ($i = 0; $i < $length; $i++) {
            $result .= $chars[random_int(0, $charsLength - 1)];
        }
        return $result;
    }

    /**
     * Generate UUID v4
     */
    public static function uuid(): string {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 65535), mt_rand(0, 65535),
            mt_rand(0, 65535),
            mt_rand(16384, 20479),
            mt_rand(0, 16383),
            mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535)
        );
    }

    /**
     * Sanitize input (strip tags, trim)
     */
    public static function sanitize(string $input): string {
        return trim(strip_tags($input));
    }

    /**
     * Escape output for HTML
     */
    public static function escape(string $input): string {
        return htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Render admin-authored rich text (TinyMCE) safely.
     * Whitelists common formatting tags and strips scripts/iframes/event handlers.
     */
    public static function safeHtml(?string $html): string {
        if ($html === null || $html === '') {
            return '';
        }
        $allowed = '<p><br><b><strong><i><em><u><ul><ol><li><a><h1><h2><h3><h4><blockquote><pre><code><span><img>';
        $clean = strip_tags($html, $allowed);
        // Remove inline event handlers and javascript: URLs that survive strip_tags.
        $clean = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);
        $clean = preg_replace('/(href|src)\s*=\s*("|\')\s*javascript:[^"\']*("|\')/i', '$1=$2#$3', $clean);
        return $clean;
    }

    /**
     * Get member initial for avatar
     */
    public static function getInitials(string $firstName, string $lastName): string {
        return strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
    }

    /**
     * Generate avatar URL from initials
     */
    public static function avatarUrl(string $initials, ?string $color = null): string {
        $color = $color ?? substr(md5($initials), 0, 6);
        return 'https://ui-avatars.com/api/?name=' . urlencode($initials) . '&background=' . $color . '&color=fff&size=128';
    }
}
