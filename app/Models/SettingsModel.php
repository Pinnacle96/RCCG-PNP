<?php

namespace App\Models;

class SettingsModel extends \Model {
    protected string $table = 'site_settings';

    private static ?array $cache = null;

    public function allSettings(): array {
        if (self::$cache !== null) {
            return self::$cache;
        }

        $rows = \Database::fetchAll('SELECT setting_key, setting_val FROM site_settings');
        $settings = [];

        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_val'];
        }

        self::$cache = $settings;
        return $settings;
    }

    public function get(string $key, ?string $default = null): ?string {
        $settings = $this->allSettings();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    public function set(string $key, ?string $value, string $group = 'general'): void {
        $existing = $this->findBy('setting_key', $key);

        if ($existing) {
            $this->update((int) $existing['id'], [
                'setting_val' => $value,
                'group_name' => $group,
            ]);
        } else {
            $this->create([
                'setting_key' => $key,
                'setting_val' => $value,
                'group_name' => $group,
            ]);
        }

        self::$cache = null;
    }

    public static function clearCache(): void {
        self::$cache = null;
    }
}
