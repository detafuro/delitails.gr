<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\Admin\StorePostRequest;
use App\Models\BlogCategory;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends AdminController
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Post::class);
        $query = Post::query()->with('category');
        if ($search = $request->string('q')->toString()) {
            $query->where('title', 'like', "%{$search}%");
        }
        $posts = $query->latest()->paginate(15)->withQueryString();
        return view('admin.posts.index', compact('posts'));
    }

    public function create()
    {
        $this->authorize('create', Post::class);
        $post = new Post();
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.posts.create', compact('post', 'categories'));
    }

    public function store(StorePostRequest $request)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        if (isset($data['tags'])) {
            $data['tags'] = collect(explode(',', $data['tags']))->map(fn ($t) => trim($t))->filter()->values()->all();
        }
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }
        Post::create($data);
        return redirect()->route('admin.posts.index')->with('success', 'Post created.');
    }

    public function edit(Post $post)
    {
        $this->authorize('update', $post);
        $categories = BlogCategory::orderBy('name')->get();
        return view('admin.posts.edit', compact('post', 'categories'));
    }

    public function update(StorePostRequest $request, Post $post)
    {
        $data = $request->validated();
        $data['slug'] = $data['slug'] ?: Str::slug($data['title']);
        if (isset($data['tags'])) {
            $data['tags'] = collect(explode(',', $data['tags']))->map(fn ($t) => trim($t))->filter()->values()->all();
        }
        if ($request->hasFile('featured_image')) {
            if ($post->featured_image) Storage::disk('public')->delete($post->featured_image);
            $data['featured_image'] = $request->file('featured_image')->store('posts', 'public');
        }
        $post->update($data);
        return redirect()->route('admin.posts.edit', $post)->with('success', 'Post updated.');
    }

    public function destroy(Post $post)
    {
        $this->authorize('delete', $post);
        if ($post->featured_image) Storage::disk('public')->delete($post->featured_image);
        $post->delete();
        return redirect()->route('admin.posts.index')->with('success', 'Post deleted.');
    }
}
