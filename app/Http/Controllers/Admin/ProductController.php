<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreProductRequest;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends AdminController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Product::class);

        $query = Product::query()->with('category');

        if ($search = $request->string('q')->toString()) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }

        if ($categoryId = $request->integer('category_id')) {
            $query->where('category_id', $categoryId);
        }

        if (($type = $request->string('type')->toString()) !== '' && isset(Product::TYPES[$type])) {
            $query->where('type', $type);
        }

        if (($status = $request->string('status')->toString()) !== '') {
            if ($status === 'published') $query->where('is_published', true);
            if ($status === 'draft') $query->where('is_published', false);
            if ($status === 'featured') $query->where('is_featured', true);
        }

        $products = $query->orderBy('sort_order')->latest()->paginate(15)->withQueryString();
        $categories = ProductCategory::ordered()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $this->authorize('create', Product::class);
        $product = new Product();
        $categories = ProductCategory::ordered()->get();
        return view('admin.products.create', compact('product', 'categories'));
    }

    public function store(StoreProductRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('products', 'public');
        }

        $gallery = $data['gallery'] ?? null;
        unset($data['gallery']);

        $product = Product::create($data);

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $i => $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'sort_order' => $i,
                ]);
            }
        }

        return redirect()->route('admin.products.index')->with('success', 'Product created.');
    }

    public function edit(Product $product)
    {
        $this->authorize('update', $product);
        $product->load('images');
        $categories = ProductCategory::ordered()->get();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(StoreProductRequest $request, Product $product)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);

        if ($request->hasFile('featured_image')) {
            if ($product->featured_image) Storage::disk('public')->delete($product->featured_image);
            $data['featured_image'] = $request->file('featured_image')->store('products', 'public');
        }

        unset($data['gallery']);
        $product->update($data);

        if ($request->hasFile('gallery')) {
            $start = $product->images()->max('sort_order') ?? 0;
            foreach ($request->file('gallery') as $i => $file) {
                $path = $file->store('products', 'public');
                ProductImage::create([
                    'product_id' => $product->id,
                    'path' => $path,
                    'sort_order' => $start + $i + 1,
                ]);
            }
        }

        return redirect()->route('admin.products.edit', $product)->with('success', 'Product updated.');
    }

    public function destroy(Product $product)
    {
        $this->authorize('delete', $product);
        foreach ($product->images as $img) {
            Storage::disk('public')->delete($img->path);
        }
        if ($product->featured_image) Storage::disk('public')->delete($product->featured_image);
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    public function destroyImage(Product $product, ProductImage $image)
    {
        $this->authorize('update', $product);
        abort_unless($image->product_id === $product->id, 404);
        Storage::disk('public')->delete($image->path);
        $image->delete();
        return back()->with('success', 'Image removed.');
    }
}
