<?php

use App\Helpers\SettingsHelper;

if (!function_exists('setting')) {
    function setting($key = null, $default = null)
    {
        if ($key === null) {
            return new class {
                public function set($key, $value) { SettingsHelper::set($key, $value); }
                public function save() { SettingsHelper::save(); }
            };
        }

        return SettingsHelper::get($key, $default);
    }
}
