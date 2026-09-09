{{--
    Standalone layout for contest pages: no site header, nav or footer — just the
    logo, the campaign, and a single legal line. Keeps the Delitails palette and
    type so it still reads as the brand.
--}}
@php
    $siteName = $site['site_name'] ?? config('app.name', 'Delitails');
    $pageTitle = html_entity_decode($title ?? \App\Support\Seo::defaultTitle(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $pageDesc = html_entity_decode($description ?? \App\Support\Seo::defaultDescription(), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $faviconPath = $site['favicon'] ?? null;
    $ogImage = $image ?? asset('og-image.png');
    $alternates = \App\Support\Locale::alternates();
    $canonicalUrl = $canonical ?? ($alternates[app()->getLocale()] ?? url()->current());
    $otherLocale = app()->getLocale() === 'el' ? 'en' : 'el';
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $pageTitle }}</title>
    @if($pageDesc)<meta name="description" content="{{ $pageDesc }}">@endif
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @unless(config('app.indexable'))
        <meta name="robots" content="noindex, nofollow">
    @endunless
    @foreach($alternates as $altLocale => $altUrl)
        <link rel="alternate" hreflang="{{ $altLocale }}" href="{{ $altUrl }}">
    @endforeach
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $siteName }}">
    <meta property="og:locale" content="{{ app()->getLocale() === 'el' ? 'el_GR' : 'en_US' }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    @if($pageDesc)<meta property="og:description" content="{{ $pageDesc }}">@endif
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    @if($pageDesc)<meta name="twitter:description" content="{{ $pageDesc }}">@endif
    <meta name="twitter:image" content="{{ $ogImage }}">
    @if($faviconPath)<link rel="icon" href="{{ asset('storage/'.$faviconPath) }}">@endif
    <link rel="preload" href="/fonts/cera-pro/CeraPro-Medium.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/cera-pro/CeraPro-Black.woff2" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @if(!empty($site['analytics_scripts']))
        {!! $site['analytics_scripts'] !!}
    @endif
</head>
<body class="min-h-screen bg-bone text-ink antialiased selection:bg-fire selection:text-bone">

    {{-- Logo bar: brand mark and language, nothing else --}}
    <div class="absolute inset-x-0 top-0 z-30">
        <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 md:px-6 py-4 md:py-6">
            <a href="{{ route('home') }}" class="shrink-0" aria-label="{{ $siteName }}">
                @if(!empty($site['logo']))
                    @php [$lw, $lh] = \App\Support\Media::dimensions($site['logo']) ?? [400, 129]; @endphp
                    {{-- The wordmark is solid black, so inverting it gives a clean white mark on the dark hero. --}}
                    <img src="{{ asset('storage/'.$site['logo']) }}" alt="{{ $siteName }}"
                         width="{{ $lw }}" height="{{ $lh }}" fetchpriority="high"
                         class="h-10 md:h-14 w-auto object-contain object-left {{ ($logoOnDark ?? true) ? 'brightness-0 invert' : '' }}">
                @else
                    <span class="font-display text-2xl font-black uppercase tracking-tight {{ ($logoOnDark ?? true) ? 'text-bone' : 'text-ink' }}">{{ $siteName }}</span>
                @endif
            </a>

            <a href="{{ \App\Support\Locale::alternateUrl($otherLocale) }}"
               class="inline-flex h-9 items-center justify-center border-2 px-3 font-display text-xs font-black uppercase tracking-wider transition
                      {{ ($logoOnDark ?? true) ? 'border-bone/40 text-bone hover:bg-bone hover:text-ink' : 'border-ink text-ink hover:bg-ink hover:text-bone' }}"
               aria-label="{{ $otherLocale === 'en' ? 'Switch to English' : 'Αλλαγή σε Ελληνικά' }}">
                {{ $otherLocale === 'en' ? 'EN' : 'ΕΛ' }}
            </a>
        </div>
    </div>

    <main>
        {{ $slot }}
    </main>

    {{-- One quiet legal line --}}
    <footer class="border-t-2 border-ink/15 bg-bone">
        <div class="mx-auto max-w-6xl px-4 md:px-6 py-6 flex flex-col sm:flex-row items-center justify-between gap-3
                    text-xs uppercase tracking-widest text-ink/50">
            <div>&copy; {{ date('Y') }} {{ $siteName }}</div>
            <div class="flex flex-wrap items-center justify-center gap-x-5 gap-y-2">
                @if(!empty($site['contact_email']))
                    <a href="mailto:{{ $site['contact_email'] }}" class="hover:text-fire">{{ $site['contact_email'] }}</a>
                @endif
                <a href="{{ route('home') }}" class="hover:text-fire">delitails.gr</a>
            </div>
        </div>
    </footer>
</body>
</html>
