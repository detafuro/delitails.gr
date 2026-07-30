<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Translation extends Model
{
    protected $fillable = ['locale', 'key', 'key_hash', 'value'];

    public const CACHE_KEY = 'translations.overrides';

    public static function overrides(string $locale): array
    {
        return Cache::rememberForever(self::CACHE_KEY.'.'.$locale, fn () => self::query()
            ->where('locale', $locale)
            ->pluck('value', 'key')
            ->all());
    }

    public static function flushCache(string $locale): void
    {
        Cache::forget(self::CACHE_KEY.'.'.$locale);
    }
}
