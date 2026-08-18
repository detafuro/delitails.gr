<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Public pages live under /{locale}/... — the route parameter decides the
 * language. Non-prefixed routes (admin, auth) use the visitor's last locale
 * from the session, else the app default (Greek).
 */
class SetLocale
{
    public const SUPPORTED = ['el', 'en'];

    /** Primary language: bare / and legacy URLs redirect here. */
    public const DEFAULT = 'el';

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->route()?->parameter('locale');

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = $request->hasSession() ? $request->session()->get('locale') : null;
        }

        if (! in_array($locale, self::SUPPORTED, true)) {
            $locale = self::DEFAULT;
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);
        URL::defaults(['locale' => $locale]);

        if ($request->route()?->hasParameter('locale')) {
            if ($request->hasSession()) {
                $request->session()->put('locale', $locale);
            }
            // Route params are passed to controllers positionally — drop the prefix so
            // show(Product $product) still receives the product. Still available via
            // $route->originalParameters() and URL::defaults for route().
            $request->route()->forgetParameter('locale');
        }

        return $next($request);
    }
}
