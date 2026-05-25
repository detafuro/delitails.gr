<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductCategoryController extends AdminController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', ProductCategory::class);

        $query = ProductCategory::query()->withCount('products');
        if ($search = $request->string('q')->toString()) {
            $query->where('name', 'like', "%{$search}%");
        }
        $categories = $query->orderBy('sort_order')->orderBy('name')->paginate(15)->withQueryString();

        return view('admin.product_categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', ProductCategory::class);
        $category = new ProductCategory();
        $parents = ProductCategory::ordered()->get();
        return view('admin.product_categories.create', compact('category', 'parents'));
    }

    public function store(StoreProductCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        ProductCategory::create($data);
        return redirect()->route('admin.product-categories.index')->with('success', 'Category created.');
    }

    public function edit(ProductCategory $category)
    {
        $this->authorize('update', $category);
        $parents = ProductCategory::ordered()->where('id', '!=', $category->id)->get();
        return view('admin.product_categories.edit', compact('category', 'parents'));
    }

    public function update(StoreProductCategoryRequest $request, ProductCategory $category)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);

        if ($request->hasFile('image')) {
            if ($category->image) Storage::disk('public')->delete($category->image);
            $data['image'] = $request->file('image')->store('categories', 'public');
        }

        $category->update($data);
        return redirect()->route('admin.product-categories.index')->with('success', 'Category updated.');
    }

    public function destroy(ProductCategory $category)
    {
        $this->authorize('delete', $category);
        if ($category->image) Storage::disk('public')->delete($category->image);
        $category->delete();
        return redirect()->route('admin.product-categories.index')->with('success', 'Category deleted.');
    }
}
