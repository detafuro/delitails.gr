<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    public const TYPE_NATURAL_CHEWS = 'natural_chews';
    public const TYPE_TRAINING_TREATS = 'training_treats';

    public const TYPES = [
        self::TYPE_NATURAL_CHEWS => 'Natural Chews',
        self::TYPE_TRAINING_TREATS => 'Training Treats',
    ];

    protected $fillable = [
        'title', 'slug', 'sku', 'short_description', 'description', 'characteristics',
        'price', 'sale_price', 'stock_status', 'featured_image',
        'category_id', 'type', 'is_published', 'is_featured', 'sort_order',
        'seo_title', 'seo_description',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_published' => 'boolean',
        'is_featured' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::saving(function (self $p) {
            if (empty($p->slug)) {
                $p->slug = Str::slug($p->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function scopePublished($q)
    {
        return $q->where('is_published', true);
    }

    public function scopeFeatured($q)
    {
        return $q->where('is_featured', true);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderByDesc('created_at');
    }

    public function getCurrentPriceAttribute(): float
    {
        return (float) ($this->sale_price ?? $this->price);
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && (float) $this->sale_price < (float) $this->price;
    }

    public function getTypeLabelAttribute(): ?string
    {
        return self::TYPES[$this->type] ?? null;
    }

    public function scopeOfType($q, ?string $type)
    {
        return $type ? $q->where('type', $type) : $q;
    }
}
