<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasTranslations;

    protected array $translatable = ['title', 'short_description', 'description', 'characteristics', 'weight', 'seo_title', 'seo_description'];

    use HasFactory;

    public const TYPE_NATURAL_CHEWS = 'natural_chews';
    public const TYPE_TRAINING_TREATS = 'training_treats';

    public const TYPES = [
        self::TYPE_NATURAL_CHEWS => 'Natural Chews',
        self::TYPE_TRAINING_TREATS => 'Training Treats',
    ];

    // How the treat is packed. The retail pouch and the counter-top display box
    // hold the same treat, so they share everything but the pack and the listing
    // they show up in.
    public const PACK_RETAIL = 'retail';
    public const PACK_DISPLAY_BOX = 'display_box';

    public const PACK_TYPES = [
        self::PACK_RETAIL => 'Retail pack',
        self::PACK_DISPLAY_BOX => 'Display box',
    ];

    /** Route name prefix for each pack type's own catalogue. */
    public const PACK_ROUTES = [
        self::PACK_RETAIL => 'products',
        self::PACK_DISPLAY_BOX => 'display-boxes',
    ];

    protected $fillable = [
        'title', 'slug', 'sku', 'weight', 'short_description', 'description', 'characteristics',
        'price', 'sale_price', 'stock_status', 'featured_image',
        'category_id', 'type', 'pack_type', 'is_published', 'is_featured', 'sort_order',
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

    public function scopePack($q, string $packType)
    {
        return $q->where('pack_type', $packType);
    }

    public function getPackTypeLabelAttribute(): ?string
    {
        return self::PACK_TYPES[$this->pack_type] ?? null;
    }

    public function isDisplayBox(): bool
    {
        return $this->pack_type === self::PACK_DISPLAY_BOX;
    }

    /** 'products.show' or 'display-boxes.show', depending on how this one is packed. */
    public function routeName(string $action = 'show'): string
    {
        return (self::PACK_ROUTES[$this->pack_type] ?? self::PACK_ROUTES[self::PACK_RETAIL]).'.'.$action;
    }

    public function url(): string
    {
        return route($this->routeName('show'), $this->slug);
    }
}
