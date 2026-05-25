<?php

use App\Models\Setting;

if (! function_exists('site')) {
    function site(string $key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('site_image')) {
    function site_image(string $key, ?string $default = null): ?string
    {
        $path = Setting::get($key);
        return $path ? asset('storage/'.$path) : $default;
    }
}
