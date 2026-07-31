@php
    use Illuminate\Support\Str;
    $siteName = $site['site_name'] ?? config('app.name', 'Delitails');
    $announcementRaw = $site['announcement_messages'] ?? '';
    if (app()->getLocale() === 'el' && trim($site['announcement_messages_el'] ?? '') !== '') {
        $announcementRaw = $site['announcement_messages_el'];
    }
    $announcements = collect(preg_split("/\r\n|\n|\r/", $announcementRaw))
        ->map(fn ($l) => trim($l))->filter()->values();
    if ($announcements->isEmpty()) {
        $announcements = collect([
            __('Free shipping over €40 — let them eat treats.'),
            __('Hand-baked. Small-batch. Loud as hell.'),
            __('New treats just landed. Sink your teeth in.'),
        ]);
    }
    $pageTitle = $title ?? ($site['seo_default_title'] ?? $siteName);
    $pageDesc = $description ?? ($site['seo_default_description'] ?? '');
    $faviconPath = $site['favicon'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @unless(config('app.indexable'))<meta name="robots" content="noindex, nofollow">@endunless
    <title>{{ $pageTitle }}</title>
    @if($pageDesc)<meta name="description" content="{{ $pageDesc }}">@endif
    <meta property="og:title" content="{{ $pageTitle }}">
    @if($pageDesc)<meta property="og:description" content="{{ $pageDesc }}">@endif
    @if($faviconPath)<link rel="icon" href="{{ asset('storage/'.$faviconPath) }}">@endif
    <link rel="preload" href="/fonts/cera-pro/CeraPro-Regular.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="/fonts/cera-pro/CeraPro-Black.woff2" as="font" type="font/woff2" crossorigin>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('head')
    @if(!empty($site['analytics_scripts']))
        {!! $site['analytics_scripts'] !!}
    @endif
</head>
<body class="min-h-screen bg-bone text-ink antialiased selection:bg-fire selection:text-bone overflow-x-hidden">

    {{-- Announcement bar --}}
    <div class="relative bg-ink text-bone overflow-hidden border-b-2 border-ink">
        <div class="marquee-track py-2 text-xs md:text-sm font-bold uppercase tracking-[0.15em]">
            @for($i = 0; $i < 2; $i++)
                @foreach($announcements as $msg)
                    <span class="flex items-center gap-3">
                        <span class="inline-block h-1.5 w-1.5 rounded-full bg-fire"></span>
                        {{ $msg }}
                    </span>
                @endforeach
            @endfor
        </div>
    </div>

    {{-- Header --}}
    <header class="sticky top-0 z-40 border-b-2 border-ink bg-bone/95 backdrop-blur"
            x-data="{ mobile:false, searchOpen:false }">
        <div class="mx-auto flex max-w-7xl items-center gap-4 px-4 md:px-6 py-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2 shrink-0">
                @if(!empty($site['logo']))
                    <img src="{{ asset('storage/'.$site['logo']) }}" alt="{{ $siteName }}" class="h-20 w-auto">
                @else
                    <span class="inline-flex h-10 w-10 items-center justify-center rounded-full bg-fire text-bone font-black text-lg shadow-[3px_3px_0_0_#191818]">D</span>
                    <span class="font-display text-xl md:text-2xl font-black uppercase tracking-tight">{{ $siteName }}</span>
                @endif
            </a>

            <nav class="hidden lg:flex flex-1 items-center justify-center gap-3">
                @php
                    $storesPublic = ($site['stores_page_status'] ?? 'draft') === 'public';
                    $links = array_values(array_filter([
                        ['products.index', 'Products'],
                        ['about', 'About'],
                        ['blog.index', 'Blog'],
                        $storesPublic ? ['stores', 'Stores'] : null,
                        ['faq', 'FAQ'],
                        ['contact', 'Contact'],
                    ]));
                @endphp
                @foreach($links as [$route,$label])
                    @php $active = request()->routeIs($route) || request()->routeIs($route.'.*'); @endphp
                    <a href="{{ route($route) }}"
                       class="nav-btn {{ $active ? 'is-current' : '' }}"
                       @if($active) aria-current="page" @endif>
                        {{ __($label) }}
                    </a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-2">
                {{-- Language switch --}}
                @php $otherLocale = app()->getLocale() === 'el' ? 'en' : 'el'; @endphp
                <a href="{{ route('lang.switch', $otherLocale) }}"
                   class="inline-flex h-10 items-center justify-center border-2 border-ink bg-bone px-2.5 font-display text-xs font-black uppercase tracking-wider hover:bg-grass"
                   aria-label="{{ $otherLocale === 'en' ? 'Switch to English' : 'Αλλαγή σε Ελληνικά' }}">
                    {{ $otherLocale === 'en' ? 'EN' : 'ΕΛ' }}
                </a>

                {{-- Search --}}
                <button type="button" @click="searchOpen = !searchOpen" class="inline-flex h-10 w-10 items-center justify-center border-2 border-ink bg-bone hover:bg-grass" aria-label="{{ __('Search') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                </button>

                {{-- Stockists CTA (hidden while stockists page is draft) --}}
                @if($storesPublic)
                <a href="{{ route('stores') }}" class="relative inline-flex h-10 items-center gap-1 border-2 border-ink bg-fire text-bone px-3 font-bold uppercase tracking-wider text-xs hover:rotate-tilt-1" aria-label="{{ __('Find a store') }}">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2c-3.866 0-7 3.134-7 7 0 4.97 7 13 7 13s7-8.03 7-13c0-3.866-3.134-7-7-7Z"/><circle cx="12" cy="9" r="2.5"/></svg>
                    <span class="hidden sm:inline">{{ __('Find a store') }}</span>
                </a>
                @endif

                {{-- Mobile menu --}}
                <button @click="mobile=true" class="lg:hidden inline-flex h-10 w-10 items-center justify-center border-2 border-ink bg-bone hover:bg-grass" aria-label="{{ __('Menu') }}">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                </button>
            </div>
        </div>

        {{-- Expandable search --}}
        <div x-show="searchOpen" x-cloak x-collapse class="border-t-2 border-ink bg-bone">
            <form action="{{ route('products.index') }}" method="GET" class="mx-auto flex max-w-7xl items-center gap-3 px-4 md:px-6 py-3">
                <input type="text" name="q" autofocus placeholder="{{ __('Search treats…') }}" value="{{ request('q') }}"
                       class="flex-1 border-2 border-ink bg-bone px-3 py-2 font-display uppercase placeholder:text-ink/40 focus:outline-none focus:ring-2 focus:ring-fire/50">
                <button class="btn-rough is-fire is-sm">{{ __('Search') }}</button>
            </form>
        </div>

        {{-- Mobile drawer --}}
        <div x-show="mobile" x-cloak x-transition.opacity class="fixed inset-0 z-50 bg-ink/70 lg:hidden" @click.self="mobile=false">
            <div class="absolute right-0 top-0 h-full w-80 bg-bone border-l-2 border-ink p-5 overflow-y-auto"
                 x-show="mobile" x-transition:enter="transition transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
                <div class="flex justify-between items-center mb-6">
                    <span class="font-display text-xl font-black uppercase">{{ __('Menu') }}</span>
                    <button @click="mobile=false" class="text-2xl">&times;</button>
                </div>
                <nav class="space-y-1">
                    @foreach($links as [$route,$label])
                        <a href="{{ route($route) }}" class="block border-b-2 border-ink/10 py-3 font-display font-bold uppercase tracking-wider">{{ __($label) }}</a>
                    @endforeach
                </nav>
                <div class="mt-6 space-y-2">
                    <a href="{{ route('lang.switch', $otherLocale) }}" class="btn-rough is-grass w-full justify-center">
                        {{ $otherLocale === 'en' ? 'English' : 'Ελληνικά' }}
                    </a>
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('profile.edit') }}" class="btn-rough is-bone w-full justify-center">{{ auth()->user()->isAdmin() ? __('Admin') : __('My account') }}</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="btn-rough is-ghost w-full justify-center">{{ __('Log out') }}</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-rough is-bone w-full justify-center">{{ __('Sign in') }}</a>
                        <a href="{{ route('register') }}" class="btn-rough is-fire w-full justify-center">{{ __('Sign up') }}</a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <main>
        {{ $slot }}
    </main>

    {{-- Footer --}}
    <footer class="relative bg-ink text-bone">
        <div aria-hidden="true" class="absolute bottom-full -mb-px left-0 right-0 h-10 paper torn-top bg-ink"></div>

        <div class="mx-auto max-w-7xl px-4 md:px-6 py-16 grid md:grid-cols-2 lg:grid-cols-5 gap-10">
            <div class="lg:col-span-2">
                @if(!empty($site['footer_logo']))
                    <img src="{{ asset('storage/'.$site['footer_logo']) }}" alt="" class="h-24 mb-4">
                @else
                    <div class="font-display text-3xl font-black uppercase mb-3">{{ $siteName }}</div>
                @endif
                <p class="font-editorial text-bone/70 leading-relaxed">{{ $site['footer_text'] ?? __('Loud treats for good dogs and louder cats. Hand-baked, small batch, raised on rebellion.') }}</p>

                <div class="mt-5 flex gap-2">
                    @foreach([
                        'social_facebook' => 'Facebook',
                        'social_instagram' => 'Instagram',
                        'social_tiktok' => 'TikTok',
                        'social_youtube' => 'YouTube',
                    ] as $key => $label)
                        @if(!empty($site[$key]))
                            <a href="{{ $site[$key] }}" target="_blank" rel="noopener" aria-label="{{ $label }}" class="inline-flex h-10 w-10 items-center justify-center border-2 border-bone hover:bg-fire hover:border-fire">
                                <x-social-icon :name="str_replace('social_', '', $key)" />
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <h4 class="font-display text-sm font-extrabold uppercase tracking-widest text-grass">{{ __('Products') }}</h4>
                <ul class="mt-4 space-y-2 text-bone/80">
                    @foreach(\App\Models\Product::TYPES as $typeKey => $typeLabel)
                        <li><a class="hover:text-grass" href="{{ route('products.index', ['type' => $typeKey]) }}">{{ __($typeLabel) }}</a></li>
                    @endforeach
                    @if(($site['stores_page_status'] ?? 'draft') === 'public')
                        <li><a class="hover:text-grass" href="{{ route('stores') }}">{{ __('Find a store') }}</a></li>
                    @endif
                </ul>
            </div>

            <div>
                <h4 class="font-display text-sm font-extrabold uppercase tracking-widest text-grass">{{ __('Inside') }}</h4>
                <ul class="mt-4 space-y-2 text-bone/80">
                    <li><a class="hover:text-grass" href="{{ route('about') }}">{{ __('About us') }}</a></li>
                    <li><a class="hover:text-grass" href="{{ route('blog.index') }}">{{ __('Blog') }}</a></li>
                    <li><a class="hover:text-grass" href="{{ route('faq') }}">{{ __('FAQ') }}</a></li>
                    <li><a class="hover:text-grass" href="{{ route('contact') }}">{{ __('Contact') }}</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-display text-sm font-extrabold uppercase tracking-widest text-grass">{{ __('Get in touch') }}</h4>
                <ul class="mt-4 space-y-2 text-bone/80">
                    @if(!empty($site['contact_email']))<li><a class="hover:text-grass" href="mailto:{{ $site['contact_email'] }}">{{ $site['contact_email'] }}</a></li>@endif
                    @if(!empty($site['contact_phone']))<li>{{ $site['contact_phone'] }}</li>@endif
                    @if(!empty($site['contact_address']))<li class="text-bone/60">{{ $site['contact_address'] }}</li>@endif
                </ul>
            </div>
        </div>

        <div class="border-t-2 border-bone/20">
            <div class="mx-auto max-w-7xl px-4 md:px-6 py-5 flex flex-col md:flex-row gap-3 items-center justify-between text-xs uppercase tracking-widest text-bone/50">
                <div>&copy; {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved.') }}</div>
                <div class="font-sans text-bone/60 normal-case tracking-normal text-sm">
                    {{ __('Designed & developed by') }} <a href="https://nifty.gr/" target="_blank" rel="noopener" class="font-semibold text-bone/80 hover:text-fire-light">Nifty</a>.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
