<?php

namespace App\Support;

use App\Models\Setting;

/** Small helpers for page titles/descriptions (locale-aware defaults from settings). */
class Seo
{
    public static function siteName(): string
    {
        return Setting::get('site_name') ?: config('app.name', 'Delitails');
    }

    /** Locale-aware setting: *_el for Greek visitors when filled, else the base value. */
    public static function setting(string $key): ?string
    {
        $value = app()->getLocale() === 'el' ? Setting::get($key.'_el') : null;

        return ($value !== null && $value !== '') ? $value : (Setting::get($key) ?: null);
    }

    public static function defaultTitle(): string
    {
        return self::setting('seo_default_title') ?: self::siteName().' — '.__('Premium pet treats');
    }

    public static function defaultDescription(): string
    {
        return self::setting('seo_default_description') ?: __('Small-batch, hand-baked treats for the loudest, best-behaved (and not so) pets around.');
    }

    /** "Page title — Site" (or just the page title if it already mentions the site). */
    public static function title(string ...$parts): string
    {
        $parts = array_values(array_filter(array_map('trim', $parts)));
        $site = self::siteName();
        if (! $parts || str_contains(end($parts), $site)) {
            return implode(' — ', $parts) ?: $site;
        }

        return implode(' — ', $parts).' — '.$site;
    }
}
