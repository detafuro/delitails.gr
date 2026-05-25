<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Post extends Model
{
    protected $fillable = [
        'title', 'slug', 'excerpt', 'body', 'featured_image',
        'category_id', 'author', 'tags',
        'is_published', 'published_at',
        'seo_title', 'seo_description',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'tags' => 'array',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $p) {
            if (empty($p->slug)) $p->slug = Str::slug($p->title);
            if ($p->is_published && empty($p->published_at)) {
                $p->published_at = now();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'category_id');
    }

    public function scopePublished($q)
    {
        return $q->where('is_published', true)->where('published_at', '<=', now());
    }
}
