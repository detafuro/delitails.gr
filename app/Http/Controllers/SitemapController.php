<?php

namespace App\Http\Controllers;

use App\Http\Middleware\SetLocale;
use App\Models\Post;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;

/** sitemap.xml with every public page in every language (+ hreflang alternates). */
class SitemapController extends Controller
{
    public function __invoke()
    {
        $xml = Cache::remember('sitemap.xml', now()->addHour(), fn () => $this->build());

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=utf-8']);
    }

    private function build(): string
    {
        $entries = [];
        $add = function (string $route, array $params = [], ?string $lastmod = null, string $freq = 'weekly', string $prio = '0.6') use (&$entries) {
            $urls = [];
            foreach (SetLocale::SUPPORTED as $locale) {
                $urls[$locale] = route($route, array_merge($params, ['locale' => $locale]));
            }
            $entries[] = compact('urls', 'lastmod', 'freq', 'prio');
        };

        $add('home', [], null, 'weekly', '1.0');
        $add('products.index', [], null, 'weekly', '0.9');
        $add('about', [], null, 'monthly', '0.7');
        $add('blog.index', [], null, 'weekly', '0.7');
        $add('faq', [], null, 'monthly', '0.6');
        $add('contact', [], null, 'yearly', '0.5');
        if (Setting::get('stores_page_status', 'draft') === 'public') {
            $add('stores', [], null, 'monthly', '0.6');
        }

        foreach (ProductCategory::active()->ordered()->get() as $cat) {
            $add('products.index', ['category' => $cat->slug], $cat->updated_at?->toDateString(), 'weekly', '0.8');
        }
        foreach (array_keys(Product::TYPES) as $type) {
            $add('products.index', ['type' => $type], null, 'weekly', '0.8');
        }
        foreach (Product::published()->get() as $p) {
            $add('products.show', ['product' => $p->slug], $p->updated_at?->toDateString(), 'weekly', '0.8');
        }
        foreach (Post::published()->get() as $post) {
            $add('blog.show', ['post' => $post->slug], ($post->updated_at ?? $post->published_at)?->toDateString(), 'monthly', '0.6');
        }

        $out = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $out .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">'."\n";
        foreach ($entries as $e) {
            foreach ($e['urls'] as $locale => $loc) {
                $out .= "  <url>\n    <loc>".e($loc)."</loc>\n";
                foreach ($e['urls'] as $altLocale => $altUrl) {
                    $out .= '    <xhtml:link rel="alternate" hreflang="'.$altLocale.'" href="'.e($altUrl).'"/>'."\n";
                }
                $out .= '    <xhtml:link rel="alternate" hreflang="x-default" href="'.e($e['urls'][SetLocale::DEFAULT])."\"/>\n";
                if ($e['lastmod']) $out .= "    <lastmod>{$e['lastmod']}</lastmod>\n";
                $out .= "    <changefreq>{$e['freq']}</changefreq>\n    <priority>{$e['prio']}</priority>\n  </url>\n";
            }
        }

        return $out.'</urlset>';
    }
}
