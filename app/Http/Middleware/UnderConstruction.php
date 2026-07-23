<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UnderConstruction
{
    /**
     * Paths that stay reachable while the site is under construction, so
     * admins can still sign in and manage the site.
     */
    private const ALLOWED_PATHS = [
        'admin', 'admin/*',
        'login', 'logout',
        'forgot-password', 'reset-password/*',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $enabled = Setting::get('under_construction') === 'on';
        } catch (\Throwable $e) {
            $enabled = false;
        }

        if (! $enabled || $request->user()?->isAdmin() || $request->is(...self::ALLOWED_PATHS)) {
            return $next($request);
        }

        return response()->view('site.under-construction', [], 503)
            ->header('Retry-After', 3600);
    }
}
