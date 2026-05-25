<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Faq extends Model
{
    protected $fillable = [
        'question', 'answer', 'group_id',
        'sort_order', 'is_active', 'show_on_homepage',
        'seo_title', 'seo_description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'show_on_homepage' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(FaqGroup::class, 'group_id');
    }

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeHomepage($q) { return $q->where('show_on_homepage', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('id'); }
}
