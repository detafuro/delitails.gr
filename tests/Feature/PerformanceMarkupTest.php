<?php

namespace Tests\Feature;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/** Guards the page-weight decisions that PageSpeed regressions came from. */
class PerformanceMarkupTest extends TestCase
{
    use RefreshDatabase;

    private const TAG = '<script async src="https://www.googletagmanager.com/gtag/js?id=G-TEST"></script>';

    public function test_third_party_tags_are_deferred_out_of_the_head(): void
    {
        Setting::set('analytics_scripts', self::TAG);

        $html = $this->get('/el')->assertOk()->getContent();
        $head = substr($html, 0, strpos($html, '</head>'));

        $this->assertStringNotContainsString('googletagmanager.com', $head);
        $this->assertMatchesRegularExpression(
            '~<template id="deferred-analytics">\s*'.preg_quote(self::TAG, '~').'\s*</template>~',
            $html
        );
    }

    public function test_no_analytics_markup_when_the_setting_is_empty(): void
    {
        $this->get('/el')->assertOk()->assertDontSee('deferred-analytics', false);
    }
}
