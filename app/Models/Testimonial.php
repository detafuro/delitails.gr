<?php

namespace App\Models;

use App\Models\Concerns\HasTranslations;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasTranslations;

    protected array $translatable = ['author', 'pet_name', 'quote'];

    protected $fillable = [
        'author', 'pet_name', 'quote', 'rating',
        'avatar', 'is_active', 'sort_order',
    ];

    protected $casts = ['is_active' => 'boolean', 'sort_order' => 'integer', 'rating' => 'integer'];

    public function scopeActive($q) { return $q->where('is_active', true); }
    public function scopeOrdered($q) { return $q->orderBy('sort_order')->orderBy('id'); }
}
