<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $productId = $this->route('product')?->id;

        return [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', Rule::unique('products', 'slug')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:64', Rule::unique('products', 'sku')->ignore($productId)],
            'short_description' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'characteristics' => ['nullable', 'string', 'max:5000'],
            'stock_status' => ['required', Rule::in(['in_stock', 'out_of_stock', 'preorder'])],
            'category_id' => ['nullable', 'exists:product_categories,id'],
            'type' => ['nullable', Rule::in(array_keys(\App\Models\Product::TYPES))],
            'featured_image' => ['nullable', 'image', 'max:5120'],
            'gallery' => ['nullable', 'array'],
            'gallery.*' => ['image', 'max:5120'],
            'is_published' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_published' => (bool) $this->boolean('is_published'),
            'is_featured' => (bool) $this->boolean('is_featured'),
        ]);
    }
}
