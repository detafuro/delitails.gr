@php
    use Illuminate\Support\Str;
    $siteName = $site['site_name'] ?? config('app.name', 'Delitails');
    $announcementRaw = $site['announcement_messages'] ?? '';
    $announcements = collect(preg_split("/\r\n|\n|\r/", $announcementRaw))
        ->map(fn ($l) => trim($l))->filter()->values();
    if ($announcements->isEmpty()) {
        $announcements = collect([
            'Free shipping over €40 — let them eat treats.',
            'Hand-baked. Small-batch. Loud as hell.',
            'New treats just landed. Sink your teeth in.',
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

            <nav class="hidden lg:flex flex-1 items-center justify-center gap-1">
                @php
                    $links = [
                        ['products.index', 'Products'],
                        ['about', 'About'],
                        ['blog.index', 'Blog'],
                        ['stores', 'Stores'],
                        ['faq', 'FAQ'],
                        ['contact', 'Contact'],
                    ];
                @endphp
                @foreach($links as [$route,$label])
                    @php $active = request()->routeIs($route) || request()->routeIs($route.'.*'); @endphp
                    <a href="{{ route($route) }}"
                       class="relative px-3 py-2 font-display text-sm font-bold uppercase tracking-wider hover:text-fire transition
                              {{ $active ? 'text-fire' : '' }}">
                        {{ $label }}
                        @if($active)<span class="absolute -bottom-1 left-2 right-2 h-1 bg-fire"></span>@endif
                    </a>
                @endforeach
            </nav>

            <div class="ml-auto flex items-center gap-2">
                {{-- Search --}}
                <button type="button" @click="searchOpen = !searchOpen" class="inline-flex h-10 w-10 items-center justify-center border-2 border-ink bg-bone hover:bg-grass" aria-label="Search">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                </button>

                {{-- Stockists CTA --}}
                <a href="{{ route('stores') }}" class="relative inline-flex h-10 items-center gap-1 border-2 border-ink bg-fire text-bone px-3 font-bold uppercase tracking-wider text-xs hover:rotate-tilt-1" aria-label="Find a store">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 2c-3.866 0-7 3.134-7 7 0 4.97 7 13 7 13s7-8.03 7-13c0-3.866-3.134-7-7-7Z"/><circle cx="12" cy="9" r="2.5"/></svg>
                    <span class="hidden sm:inline">Find a store</span>
                </a>

                {{-- Mobile menu --}}
                <button @click="mobile=true" class="lg:hidden inline-flex h-10 w-10 items-center justify-center border-2 border-ink bg-bone hover:bg-grass" aria-label="Menu">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
                </button>
            </div>
        </div>

        {{-- Expandable search --}}
        <div x-show="searchOpen" x-cloak x-collapse class="border-t-2 border-ink bg-bone">
            <form action="{{ route('products.index') }}" method="GET" class="mx-auto flex max-w-7xl items-center gap-3 px-4 md:px-6 py-3">
                <input type="text" name="q" autofocus placeholder="Search treats…" value="{{ request('q') }}"
                       class="flex-1 border-2 border-ink bg-bone px-3 py-2 font-display uppercase placeholder:text-ink/40 focus:outline-none focus:ring-2 focus:ring-fire/50">
                <button class="btn-rough is-fire is-sm">Search</button>
            </form>
        </div>

        {{-- Mobile drawer --}}
        <div x-show="mobile" x-cloak x-transition.opacity class="fixed inset-0 z-50 bg-ink/70 lg:hidden" @click.self="mobile=false">
            <div class="absolute right-0 top-0 h-full w-80 bg-bone border-l-2 border-ink p-5 overflow-y-auto"
                 x-show="mobile" x-transition:enter="transition transform" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
                <div class="flex justify-between items-center mb-6">
                    <span class="font-display text-xl font-black uppercase">Menu</span>
                    <button @click="mobile=false" class="text-2xl">&times;</button>
                </div>
                <nav class="space-y-1">
                    @foreach($links as [$route,$label])
                        <a href="{{ route($route) }}" class="block border-b-2 border-ink/10 py-3 font-display font-bold uppercase tracking-wider">{{ $label }}</a>
                    @endforeach
                </nav>
                <div class="mt-6 space-y-2">
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('profile.edit') }}" class="btn-rough is-bone w-full justify-center">{{ auth()->user()->isAdmin() ? 'Admin' : 'My account' }}</a>
                        <form method="POST" action="{{ route('logout') }}">@csrf
                            <button class="btn-rough is-ghost w-full justify-center">Log out</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn-rough is-bone w-full justify-center">Sign in</a>
                        <a href="{{ route('register') }}" class="btn-rough is-fire w-full justify-center">Sign up</a>
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
                <p class="font-editorial text-bone/70 leading-relaxed">{{ $site['footer_text'] ?? 'Loud treats for good dogs and louder cats. Hand-baked, small batch, raised on rebellion.' }}</p>

                <div class="mt-5 flex gap-2">
                    @foreach([
                        'social_facebook' => 'FB',
                        'social_instagram' => 'IG',
                        'social_tiktok' => 'TT',
                        'social_youtube' => 'YT',
                    ] as $key => $label)
                        @if(!empty($site[$key]))
                            <a href="{{ $site[$key] }}" target="_blank" rel="noopener" class="inline-flex h-10 w-10 items-center justify-center border-2 border-bone font-bold hover:bg-fire hover:border-fire">{{ $label }}</a>
                        @endif
                    @endforeach
                </div>
            </div>

            <div>
                <h4 class="font-display text-sm font-extrabold uppercase tracking-widest text-grass">Shop</h4>
                <ul class="mt-4 space-y-2 text-bone/80">
                    <li><a class="hover:text-grass" href="{{ route('products.index') }}">All treats</a></li>
                    <li><a class="hover:text-grass" href="{{ route('products.index') }}?sort=newest">New drops</a></li>
                    <li><a class="hover:text-grass" href="{{ route('products.index') }}?sort=price_asc">By price</a></li>
                    <li><a class="hover:text-grass" href="{{ route('stores') }}">Find a store</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-display text-sm font-extrabold uppercase tracking-widest text-grass">Inside</h4>
                <ul class="mt-4 space-y-2 text-bone/80">
                    <li><a class="hover:text-grass" href="{{ route('about') }}">About us</a></li>
                    <li><a class="hover:text-grass" href="{{ route('blog.index') }}">Blog</a></li>
                    <li><a class="hover:text-grass" href="{{ route('faq') }}">FAQ</a></li>
                    <li><a class="hover:text-grass" href="{{ route('contact') }}">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="font-display text-sm font-extrabold uppercase tracking-widest text-grass">Get in touch</h4>
                <ul class="mt-4 space-y-2 text-bone/80">
                    @if(!empty($site['contact_email']))<li><a class="hover:text-grass" href="mailto:{{ $site['contact_email'] }}">{{ $site['contact_email'] }}</a></li>@endif
                    @if(!empty($site['contact_phone']))<li>{{ $site['contact_phone'] }}</li>@endif
                    @if(!empty($site['contact_address']))<li class="text-bone/60">{{ $site['contact_address'] }}</li>@endif
                </ul>
            </div>
        </div>

        <div class="border-t-2 border-bone/20">
            <div class="mx-auto max-w-7xl px-4 md:px-6 py-5 flex flex-col md:flex-row gap-3 items-center justify-between text-xs uppercase tracking-widest text-bone/50">
                <div>&copy; {{ date('Y') }} {{ $siteName }}. All rights reserved.</div>
                <div class="font-sans text-bone/60 normal-case tracking-normal text-sm">
                    Designed &amp; developed by <a href="https://nifty.gr/" target="_blank" rel="noopener" class="font-semibold text-bone/80 hover:text-fire-light">Nifty</a>.
                </div>
            </div>
        </div>
    </footer>
</body>
</html>
