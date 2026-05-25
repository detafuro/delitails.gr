<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group', 'type'];

    protected static function booted(): void
    {
        static::saved(fn () => Cache::forget('settings.all'));
        static::deleted(fn () => Cache::forget('settings.all'));
    }

    public static function all_cached(): array
    {
        return Cache::rememberForever('settings.all', function () {
            return static::query()->pluck('value', 'key')->toArray();
        });
    }

    public static function get(string $key, $default = null)
    {
        return static::all_cached()[$key] ?? $default;
    }

    public static function set(string $key, $value, string $group = 'general', string $type = 'text'): self
    {
        $setting = static::firstOrNew(['key' => $key]);
        $setting->value = $value;
        $setting->group = $group;
        $setting->type = $type;
        $setting->save();
        return $setting;
    }
}
