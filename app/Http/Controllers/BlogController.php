<?php

namespace App\Http\Controllers;

use App\Models\BlogCategory;
use App\Models\Post;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Post::published()->with('category');

        if ($search = $request->string('q')->toString()) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
        }

        $activeCategory = null;
        if ($slug = $request->string('category')->toString()) {
            $activeCategory = BlogCategory::where('slug', $slug)->first();
            if ($activeCategory) {
                $query->where('category_id', $activeCategory->id);
            }
        }

        $posts = $query->latest('published_at')->paginate(9)->withQueryString();
        $categories = BlogCategory::active()->orderBy('name')->get();

        return view('site.blog.index', compact('posts', 'categories', 'activeCategory'));
    }

    public function show(Post $post)
    {
        abort_unless($post->is_published, 404);
        $related = Post::published()
            ->where('id', '!=', $post->id)
            ->when($post->category_id, fn ($q) => $q->where('category_id', $post->category_id))
            ->latest('published_at')->limit(3)->get();
        if ($related->count() === 0) {
            $related = Post::published()->where('id', '!=', $post->id)->latest('published_at')->limit(3)->get();
        }
        return view('site.blog.show', compact('post', 'related'));
    }
}
