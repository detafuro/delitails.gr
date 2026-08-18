<?php

namespace App\Providers;

use App\Models\Setting;
use App\Translation\DatabaseTranslationLoader;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Admin-edited translations overlay the lang/*.json defaults.
        $this->app->extend('translation.loader', fn ($loader, $app) => new DatabaseTranslationLoader($app['files'], $app['path.lang']));
    }

    public function boot(): void
    {
        Carbon::setLocale(app()->getLocale());
        URL::defaults(['locale' => app()->getLocale()]);

        View::composer('*', function ($view) {
            static $cached = null;
            if ($cached === null) {
                try {
                    $cached = Schema::hasTable('settings') ? Setting::all_cached() : [];
                } catch (\Throwable $e) {
                    $cached = [];
                }
            }
            $view->with('site', $cached);
        });
    }
}
