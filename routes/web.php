<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\ConstructionAccessController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\FaqPageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\ProductCatalogController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\StoreLocatorController;
use App\Http\Middleware\SetLocale;
use App\Support\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Public site: every page lives under /{locale}/... (Greek is the default).
Route::prefix('{locale}')
    ->where(['locale' => implode('|', SetLocale::SUPPORTED)])
    ->group(function () {
        Route::get('/', HomeController::class)->name('home');
        Route::get('/about', AboutController::class)->name('about');
        Route::get('/products', [ProductCatalogController::class, 'index'])->name('products.index');
        Route::get('/products/{product:slug}', [ProductCatalogController::class, 'show'])->name('products.show');
        Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
        Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
        Route::get('/faq', FaqPageController::class)->name('faq');
        Route::get('/stores', StoreLocatorController::class)->name('stores');
        Route::get('/contact', [ContactController::class, 'show'])->name('contact');
        Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');
        Route::post('/newsletter', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');
    });

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// Bare root → the visitor's last language, else Greek.
Route::get('/', function (Request $request) {
    $locale = $request->session()->get('locale');
    $locale = in_array($locale, SetLocale::SUPPORTED, true) ? $locale : Locale::default();

    return redirect('/'.$locale);
})->name('root');

// Language switch: same page in the other language.
Route::get('/lang/{lang}', function (Request $request, string $lang) {
    abort_unless(in_array($lang, SetLocale::SUPPORTED, true), 404);
    session(['locale' => $lang]);

    return redirect(Locale::switchUrl($lang, $request->headers->get('referer')));
})->name('lang.switch');

Route::post('/construction/unlock', [ConstructionAccessController::class, 'unlock'])
    ->middleware('throttle:10,1')->name('construction.unlock');

Route::get('/dashboard', function () {
    if (auth()->user()?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/admin.php';
require __DIR__.'/auth.php';

// Legacy unprefixed URLs (/about, /products/x) → /el/... ; anything else is a real 404.
Route::fallback(function (Request $request) {
    $path = trim($request->path(), '/');
    $first = explode('/', $path)[0] ?? '';

    if ($request->isMethod('GET') && $path !== '' && ! in_array($first, SetLocale::SUPPORTED, true)) {
        $target = '/'.Locale::default().'/'.$path;
        try {
            $matched = app('router')->getRoutes()->match(Request::create($target, 'GET'));
            if (! $matched->isFallback) {
                $query = $request->getQueryString();

                return redirect($target.($query ? '?'.$query : ''), 301);
            }
        } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            // fall through to 404
        }
    }

    abort(404);
});
