<?php

use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\FaqGroupController;
use App\Http\Controllers\Admin\NewsletterSubscriberController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StoreController;
use App\Http\Controllers\Admin\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    // Products
    Route::resource('products', ProductController::class)->except(['show']);
    Route::delete('products/{product}/images/{image}', [ProductController::class, 'destroyImage'])
        ->name('products.images.destroy');

    // Product categories
    Route::resource('product-categories', ProductCategoryController::class)
        ->except(['show'])
        ->parameters(['product-categories' => 'category']);

    // FAQs
    Route::resource('faqs', FaqController::class)->except(['show']);
    Route::resource('faq-groups', FaqGroupController::class)
        ->except(['show'])
        ->parameters(['faq-groups' => 'group']);

    // Blog
    Route::resource('posts', PostController::class)->except(['show']);
    Route::resource('blog-categories', BlogCategoryController::class)
        ->except(['show'])
        ->parameters(['blog-categories' => 'blog_category']);

    // Stores
    Route::resource('stores', StoreController::class)->except(['show']);

    // Testimonials
    Route::resource('testimonials', TestimonialController::class)->except(['show']);

    // Messages
    Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::get('messages/{message}', [ContactMessageController::class, 'show'])->name('messages.show');
    Route::delete('messages/{message}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

    // Subscribers
    Route::get('subscribers', [NewsletterSubscriberController::class, 'index'])->name('subscribers.index');
    Route::delete('subscribers/{subscriber}', [NewsletterSubscriberController::class, 'destroy'])->name('subscribers.destroy');

    // Settings
    Route::get('settings', [SettingsController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingsController::class, 'update'])->name('settings.update');
});
