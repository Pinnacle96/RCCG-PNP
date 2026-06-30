<?php

class Settings {
    private static ?\App\Models\SettingsModel $model = null;

    public static function get(string $key, ?string $default = null): ?string {
        try {
            return self::model()->get($key, $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function all(): array {
        try {
            return self::model()->allSettings();
        } catch (\Throwable $e) {
            return [];
        }
    }

    private static function model(): \App\Models\SettingsModel {
        if (self::$model === null) {
            self::$model = new \App\Models\SettingsModel();
        }

        return self::$model;
    }
}
