<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    /** $pack comes from the route default: 'retail' for /products, 'display_box' for /display-boxes. */
    public function index(Request $request, string $pack = Product::PACK_RETAIL)
    {
        $pack = isset(Product::PACK_TYPES[$pack]) ? $pack : Product::PACK_RETAIL;
        $indexRoute = Product::PACK_ROUTES[$pack].'.index';

        // Only offer filters that lead somewhere: a pack may not cover every
        // animal or every type.
        $categories = ProductCategory::active()->ordered()
            ->whereHas('products', fn ($q) => $q->published()->pack($pack))
            ->get();

        $availableTypes = Product::published()->pack($pack)->distinct()->pluck('type')->all();
        $types = array_intersect_key(Product::TYPES, array_flip($availableTypes));

        $query = Product::published()->pack($pack)->with('category', 'images');

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('translations', fn ($t) => $t->where('value', 'like', "%{$search}%"));
            });
        }

        $activeCategory = null;
        if ($slug = $request->string('category')->toString()) {
            $activeCategory = $categories->firstWhere('slug', $slug);
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        $activeType = null;
        if (($type = $request->string('type')->toString()) !== '' && isset($types[$type])) {
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

        return view('site.products.index', compact(
            'products', 'categories', 'activeCategory', 'activeType', 'types', 'sort', 'pack', 'indexRoute'
        ));
    }

    public function show(Product $product, string $pack = Product::PACK_RETAIL)
    {
        abort_unless($product->is_published, 404);
        // /products/x and /display-boxes/x are separate catalogues: don't serve
        // one pack's product under the other's URL.
        abort_unless($product->pack_type === $pack, 404);
        $product->load('category', 'images');

        $related = Product::published()->pack($pack)->with('images')
            ->where('id', '!=', $product->id)
            ->when($product->category_id, fn ($q) => $q->where('category_id', $product->category_id))
            ->limit(4)->get();

        if ($related->count() === 0) {
            $related = Product::published()->pack($pack)->with('images')->where('id', '!=', $product->id)->limit(4)->get();
        }

        $indexRoute = $product->routeName('index');

        return view('site.products.show', compact('product', 'related', 'indexRoute'));
    }
}
