<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    public function index(Request $request)
    {
        $categories = ProductCategory::active()->ordered()->get();

        $query = Product::published()->with('category', 'images');

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $activeCategory = null;
        if ($slug = $request->string('category')->toString()) {
            $activeCategory = ProductCategory::where('slug', $slug)->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        $activeType = null;
        if (($type = $request->string('type')->toString()) !== '' && isset(Product::TYPES[$type])) {
            $activeType = $type;
            $query->where('type', $type);
        }

        $sort = $request->string('sort', 'featured')->toString();
        if (! in_array($sort, ['featured', 'newest', 'name'], true)) {
            $sort = 'featured';
        }
        match ($sort) {
            'newest' => $query->latest(),
            'name' => $query->orderBy('title'),
            default => $query->orderByDesc('is_featured')->orderBy('sort_order')->latest(),
        };

        $products = $query->paginate(12)->withQueryString();

        $types = Product::TYPES;

        return view('site.products.index', compact('products', 'categories', 'activeCategory', 'activeType', 'types', 'sort'));
    }

    public function show(Product $product)
    {
        abort_unless($product->is_published, 404);
        $product->load('category', 'images');

        $related = Product::published()->with('images')
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->limit(4)->get();

        if ($related->count() === 0) {
            $related = Product::published()->with('images')->where('id', '!=', $product->id)->limit(4)->get();
        }

        return view('site.products.show', compact('product', 'related'));
    }
}
