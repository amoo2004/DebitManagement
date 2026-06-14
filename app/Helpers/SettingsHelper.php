<?php
namespace App\Helpers;

class SettingsHelper
{
    protected static $settings = [];

    public static function load(): void
    {
        $path = storage_path('app/settings.json');
        if (file_exists($path)) {
            self::$settings = json_decode(file_get_contents($path), true) ?? [];
        }
    }

    public static function get($key, $default = null)
    {
        if (empty(self::$settings)) {
            self::load();
        }
        return self::$settings[$key] ?? $default;
    }

    public static function set($key, $value): void
    {
        self::$settings[$key] = $value;
    }

    public static function save(): void
    {
        $path = storage_path('app/settings.json');
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
        file_put_contents($path, json_encode(self::$settings, JSON_PRETTY_PRINT));
    }
}
