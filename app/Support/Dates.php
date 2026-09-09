<?php

namespace App\Support;

use DateTimeInterface;
use Illuminate\Support\Carbon;

/**
 * The app stores timestamps in UTC, but everyone running and entering these
 * contests is in Greece — a deadline typed as "20:00" has to mean 20:00 in
 * Athens. These two helpers do that conversion at the edges (admin form in,
 * display out); everything in between stays UTC.
 */
class Dates
{
    public static function tz(): string
    {
        return config('app.display_timezone') ?: config('app.timezone', 'UTC');
    }

    /** A UTC timestamp shown in the site's timezone. */
    public static function local(DateTimeInterface|string|null $value): ?Carbon
    {
        return $value ? Carbon::parse($value)->setTimezone(self::tz()) : null;
    }

    /** Admin input (wall-clock time in the site's timezone) stored as UTC. */
    public static function fromLocal(DateTimeInterface|string|null $value): ?Carbon
    {
        return $value ? Carbon::parse($value, self::tz())->utc() : null;
    }

    /** Short, readable, locale-aware: "9 Σεπ 2026, 20:00". */
    public static function format(DateTimeInterface|string|null $value, string $format = 'j M Y, H:i'): string
    {
        return self::local($value)?->translatedFormat($format) ?? '—';
    }
}
