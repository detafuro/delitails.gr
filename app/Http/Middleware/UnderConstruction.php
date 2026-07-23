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
        'construction/unlock',
        'up',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        try {
            $enabled = Setting::get('under_construction') === 'on';
        } catch (\Throwable $e) {
            $enabled = false;
        }

        if (! $enabled || $request->user()?->isAdmin() || $request->is(...self::ALLOWED_PATHS) || $this->hasGuestBypass($request)) {
            return $next($request);
        }

        return response()->view('site.under-construction', [], 503)
            ->header('Retry-After', 3600);
    }

    /**
     * A guest who entered the passcode carries a hash of it in their session;
     * changing the passcode in admin invalidates existing guest bypasses.
     */
    private function hasGuestBypass(Request $request): bool
    {
        if (! $request->hasSession()) {
            return false;
        }

        $passcode = (string) Setting::get('under_construction_passcode', '');

        return $passcode !== ''
            && $request->session()->get('construction_bypass') === hash('sha256', $passcode);
    }
}
