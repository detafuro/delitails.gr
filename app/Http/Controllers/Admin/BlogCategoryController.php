<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StoreBlogCategoryRequest;
use App\Models\BlogCategory;
use Illuminate\Support\Str;

class BlogCategoryController extends AdminController
{
    public function index()
    {
        $this->authorize('viewAny', BlogCategory::class);
        $categories = BlogCategory::withCount('posts')->orderBy('sort_order')->orderBy('name')->paginate(20);
        return view('admin.blog_categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', BlogCategory::class);
        $blog_category = new BlogCategory();
        return view('admin.blog_categories.create', compact('blog_category'));
    }

    public function store(StoreBlogCategoryRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        BlogCategory::create($data);
        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category created.');
    }

    public function edit(BlogCategory $blog_category)
    {
        $this->authorize('update', $blog_category);
        return view('admin.blog_categories.edit', compact('blog_category'));
    }

    public function update(StoreBlogCategoryRequest $request, BlogCategory $blog_category)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['name']);
        $blog_category->update($data);
        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category updated.');
    }

    public function destroy(BlogCategory $blog_category)
    {
        $this->authorize('delete', $blog_category);
        $blog_category->delete();
        return redirect()->route('admin.blog-categories.index')->with('success', 'Blog category deleted.');
    }
}
