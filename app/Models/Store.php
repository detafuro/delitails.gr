<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Store extends Model
{
    protected $fillable = [
        'name', 'slug', 'city', 'postcode', 'address',
        'phone', 'email', 'opening_hours', 'map_link',
        'latitude', 'longitude', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $s) {
            if (empty($s->slug)) $s->slug = Str::slug($s->name.'-'.$s->city);
        });
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('city'); }
}
