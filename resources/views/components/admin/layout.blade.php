@props(['title' => 'Admin'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-area min-h-screen bg-[#F3F1EF] text-ink antialiased" x-data="{ sidebar: false }">
    <div class="flex min-h-screen">
        {{-- Sidebar --}}
        <aside class="fixed inset-y-0 left-0 z-40 w-72 transform bg-ink text-bone transition-transform lg:translate-x-0"
               :class="sidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
            <div class="flex h-full flex-col">
                <div class="flex items-center justify-between gap-3 px-5 py-5 border-b border-white/10">
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
                        <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-fire text-bone font-black">D</span>
                        <span class="font-display text-xl font-extrabold uppercase tracking-tight">Delitails CMS</span>
                    </a>
                    <button class="lg:hidden text-bone/70" @click="sidebar=false">&times;</button>
                </div>

                <nav class="flex-1 overflow-y-auto px-3 py-4 space-y-1 text-sm">
                    @php
                        $sections = [
                            ['heading' => 'Catalog', 'links' => [
                                ['admin.dashboard', 'Dashboard', '◆'],
                                ['admin.products.index', 'Products', '▣'],
                                ['admin.product-categories.index', 'Product categories', '⌗'],
                            ]],
                            ['heading' => 'Content', 'links' => [
                                ['admin.posts.index', 'Blog posts', '✎'],
                                ['admin.blog-categories.index', 'Blog categories', '⌗'],
                                ['admin.faqs.index', 'FAQs', '?'],
                                ['admin.faq-groups.index', 'FAQ groups', '⊞'],
                                ['admin.testimonials.index', 'Testimonials', '★'],
                                ['admin.stores.index', 'Stores', '⌖'],
                            ]],
                            ['heading' => 'Inbox', 'links' => [
                                ['admin.messages.index', 'Contact messages', '✉'],
                                ['admin.subscribers.index', 'Subscribers', '⌁'],
                            ]],
                            ['heading' => 'System', 'links' => [
                                ['admin.settings.edit', 'Site settings', '⚙'],
                            ]],
                        ];
                    @endphp

                    @foreach ($sections as $section)
                        <div class="px-2 pt-4 pb-1 text-[10px] uppercase tracking-[0.18em] text-bone/40">
                            {{ $section['heading'] }}
                        </div>
                        @foreach ($section['links'] as [$route, $label, $glyph])
                            @php $active = request()->routeIs($route) || request()->routeIs($route.'.*'); @endphp
                            <a href="{{ route($route) }}"
                               class="flex items-center gap-3 rounded-md px-3 py-2 transition
                                      {{ $active ? 'bg-fire text-bone' : 'text-bone/80 hover:bg-white/5 hover:text-bone' }}">
                                <span class="w-5 text-center font-bold">{{ $glyph }}</span>
                                <span class="font-semibold tracking-wide">{{ $label }}</span>
                            </a>
                        @endforeach
                    @endforeach
                </nav>

                <div class="border-t border-white/10 px-4 py-4">
                    <div class="text-xs text-bone/60 mb-2">Signed in as</div>
                    <div class="text-sm font-semibold">{{ auth()->user()->name }}</div>
                    <div class="text-xs text-bone/50 truncate">{{ auth()->user()->email }}</div>
                    <div class="mt-3 flex gap-2">
                        <a href="{{ route('home') }}" class="text-xs font-bold uppercase tracking-wider text-bone/80 hover:text-bone">View site</a>
                        <form method="POST" action="{{ route('logout') }}" class="ml-auto">
                            @csrf
                            <button class="text-xs font-bold uppercase tracking-wider text-fire-light hover:text-fire">Logout</button>
                        </form>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Backdrop --}}
        <div x-show="sidebar" x-transition.opacity @click="sidebar=false"
             class="fixed inset-0 z-30 bg-black/50 lg:hidden" style="display:none"></div>

        {{-- Main --}}
        <main class="flex-1 lg:pl-72">
            <header class="sticky top-0 z-20 flex items-center gap-4 border-b-2 border-ink bg-bone/95 backdrop-blur px-4 lg:px-8 py-4">
                <button class="lg:hidden btn-rough is-bone is-sm" @click="sidebar=true">Menu</button>
                <div>
                    <h1 class="font-display text-2xl md:text-3xl font-black uppercase tracking-tight">{{ $title }}</h1>
                    @isset($subtitle)
                        <p class="text-sm text-ink/60">{{ $subtitle }}</p>
                    @endisset
                </div>
                <div class="ml-auto flex items-center gap-3">
                    @isset($actions) {{ $actions }} @endisset
                </div>
            </header>

            @if (session('success'))
                <div x-data="{open:true}" x-show="open" x-transition
                     class="mx-4 lg:mx-8 mt-4 flex items-center gap-3 border-2 border-ink bg-grass/90 px-4 py-3 sticker-shadow">
                    <span class="font-bold uppercase tracking-wider">Saved</span>
                    <span class="text-ink/80">{{ session('success') }}</span>
                    <button @click="open=false" class="ml-auto font-bold">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="mx-4 lg:mx-8 mt-4 border-2 border-ink bg-fire/15 px-4 py-3">
                    <div class="font-bold uppercase tracking-wider">Heads up:</div>
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
                    </ul>
                </div>
            @endif

            <div class="p-4 lg:p-8">
                {{ $slot }}
            </div>
        </main>
    </div>
</body>
</html>
