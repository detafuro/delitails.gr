<?php

namespace App\Http\Controllers;

use App\Models\Faq;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function __invoke()
    {
        $categories = ProductCategory::active()->ordered()->limit(8)->get();
        $featured = Product::published()->featured()->ordered()->with('category')->limit(8)->get();
        if ($featured->count() === 0) {
            $featured = Product::published()->ordered()->with('category')->limit(8)->get();
        }
        $posts = Post::published()->latest('published_at')->limit(3)->get();
        $faqs = Faq::active()->homepage()->ordered()->limit(5)->get();
        if ($faqs->count() === 0) {
            $faqs = Faq::active()->ordered()->limit(5)->get();
        }
        $stores = Store::active()->ordered()->limit(4)->get();
        $testimonials = Testimonial::active()->ordered()->limit(6)->get();

        return view('site.home', compact('categories', 'featured', 'posts', 'faqs', 'stores', 'testimonials'));
    }
}
