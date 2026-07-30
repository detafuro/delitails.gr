<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\Translation;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Lang;
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
        Carbon::setLocale(app()->getLocale());
        $this->loadTranslationOverrides();

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

    /**
     * Overlay admin-edited translations (translations table) on top of the
     * lang/*.json defaults. The JSON file must be force-loaded first,
     * otherwise addLines() marks the locale as loaded and the file is skipped.
     */
    private function loadTranslationOverrides(): void
    {
        $locale = app()->getLocale();

        try {
            $overrides = Schema::hasTable('translations') ? Translation::overrides($locale) : [];
        } catch (\Throwable $e) {
            return;
        }

        if (! $overrides) {
            return;
        }

        app('translator')->load('*', '*', $locale);
        Lang::addLines(
            collect($overrides)->mapWithKeys(fn ($value, $key) => ['*.'.$key => $value])->all(),
            $locale
        );
    }
}
