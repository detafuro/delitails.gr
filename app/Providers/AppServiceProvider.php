<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
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
