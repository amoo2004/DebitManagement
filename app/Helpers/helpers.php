<?php

use App\Helpers\SettingsHelper;

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return SettingsHelper::get($key, $default);
    }
}
