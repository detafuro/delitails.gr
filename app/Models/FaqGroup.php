<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FaqGroup extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean', 'sort_order' => 'integer'];

    protected static function booted(): void
    {
        static::saving(function (self $g) {
            if (empty($g->slug)) $g->slug = Str::slug($g->name);
        });
    }

    public function faqs(): HasMany
    {
        return $this->hasMany(Faq::class, 'group_id')->orderBy('sort_order');
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('name'); }
}
