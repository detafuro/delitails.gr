<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;

/**
 * Cloudflare Turnstile — invisible anti-bot check on the public entry form.
 * Keys come from site settings (so the client can rotate them without a deploy)
 * and fall back to env. With no keys configured the widget is simply not
 * rendered and verification passes: the honeypot still applies.
 */
class Turnstile
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public static function siteKey(): ?string
    {
        return Setting::get('turnstile_site_key') ?: (config('services.turnstile.site_key') ?: null);
    }

    public static function secretKey(): ?string
    {
        return Setting::get('turnstile_secret_key') ?: (config('services.turnstile.secret_key') ?: null);
    }

    public static function enabled(): bool
    {
        return (bool) (self::siteKey() && self::secretKey());
    }

    public static function verify(?string $token, ?string $ip = null): bool
    {
        if (! self::enabled()) {
            return true;
        }
        if (! $token) {
            return false;
        }

        try {
            $response = Http::asForm()->timeout(8)->post(self::VERIFY_URL, array_filter([
                'secret' => self::secretKey(),
                'response' => $token,
                'remoteip' => $ip,
            ]));

            return (bool) ($response->json('success') ?? false);
        } catch (\Throwable $e) {
            report($e);

            // Cloudflare unreachable: don't lock real entrants out of the contest.
            return true;
        }
    }
}
