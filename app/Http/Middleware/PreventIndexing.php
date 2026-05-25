<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventIndexing
{
    /**
     * Send a "noindex" robots directive on every response while the site is
     * not yet meant to appear in search results. Toggle via SITE_INDEXABLE.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! config('app.indexable')) {
            $response->headers->set('X-Robots-Tag', 'noindex, nofollow');
        }

        return $response;
    }
}
