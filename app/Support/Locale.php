<?php

namespace App\Support;

use App\Http\Middleware\SetLocale;

/**
 * URL helpers for the /{locale}/... public routes.
 */
class Locale
{
    public static function default(): string
    {
        return SetLocale::DEFAULT;
    }

    /** The current page in another locale (falls back to that locale's home). */
    public static function alternateUrl(string $locale): string
    {
        $request = request();
        $route = $request->route();

        if ($route && $route->getName() && in_array('locale', $route->parameterNames(), true)) {
            $params = array_merge($route->originalParameters(), ['locale' => $locale]);
            $url = route($route->getName(), $params);
            $query = $request->getQueryString();

            return $query ? $url.'?'.$query : $url;
        }

        return url('/'.$locale);
    }

    /** All supported locales mapped to the current page's URL in each (for hreflang). */
    public static function alternates(): array
    {
        $out = [];
        foreach (SetLocale::SUPPORTED as $locale) {
            $out[$locale] = self::alternateUrl($locale);
        }

        return $out;
    }

    /** Given a URL (e.g. the referer), the same path with its locale prefix swapped; else that locale's home. */
    public static function switchUrl(string $locale, ?string $fromUrl): string
    {
        if ($fromUrl && str_starts_with($fromUrl, url('/'))) {
            $path = parse_url($fromUrl, PHP_URL_PATH) ?: '/';
            $query = parse_url($fromUrl, PHP_URL_QUERY);
            $segments = explode('/', trim($path, '/'));

            if (in_array($segments[0] ?? '', SetLocale::SUPPORTED, true)) {
                $segments[0] = $locale;

                return url('/'.implode('/', $segments)).($query ? '?'.$query : '');
            }
        }

        return url('/'.$locale);
    }
}
