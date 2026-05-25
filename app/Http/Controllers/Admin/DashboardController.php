<?php

namespace App\Http\Controllers\Admin;

use App\Models\ContactMessage;
use App\Models\Faq;
use App\Models\NewsletterSubscriber;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Store;

class DashboardController extends AdminController
{
    public function __invoke()
    {
        $stats = [
            'products' => Product::count(),
            'categories' => ProductCategory::count(),
            'posts' => Post::count(),
            'faqs' => Faq::count(),
            'stores' => Store::count(),
            'messages' => ContactMessage::where('is_read', false)->count(),
            'subscribers' => NewsletterSubscriber::where('is_active', true)->count(),
        ];

        $latestMessages = ContactMessage::latest()->limit(5)->get();
        $latestProducts = Product::latest()->limit(5)->get();

        return view('admin.dashboard', compact('stats', 'latestMessages', 'latestProducts'));
    }
}
