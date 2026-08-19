@php
    $siteName = $site['site_name'] ?? config('app.name', 'Delitails');
    // Locale-aware setting: Greek visitors get the *_el variant when it is filled in.
    $pick = fn (string $key) => (app()->getLocale() === 'el' ? ($site[$key.'_el'] ?? null) : null) ?: ($site[$key] ?? null);
    $heroHeading = $pick('hero_heading') ?? __('TREATS WITH ATTITUDE.');
    $heroSub = $pick('hero_subheading') ?? __('Loud little snacks for picky pets and the humans who feed them. Small-batch, big personality.');
    $heroCtaText = $pick('hero_cta_text') ?? __('Explore the pack');
    $heroCtaLink = $site['hero_cta_link'] ?? route('products.index');
    $title = \App\Support\Seo::defaultTitle();
    $description = \App\Support\Seo::setting('seo_default_description') ?: $heroSub;
@endphp
<x-layout title="{{ $title }}" description="{{ $description }}">
    {{-- HERO --}}
    <section class="relative overflow-hidden bg-grass paper">
        <div class="relative mx-auto max-w-7xl px-4 md:px-6 py-16 md:py-24 grid lg:grid-cols-3 gap-10 items-center">
            <div class="relative z-10 lg:col-span-2">
                <div class="inline-flex items-center gap-2 ribbon mb-5">
                    <span>★ {{ __('100% Natural') }}</span>
                </div>
                <h1 class="font-display text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black uppercase leading-[0.9] tracking-tight text-ink">
                    @php
                        // Words highlighted in fire orange (EN + EL), compared without punctuation/accents.
                        $heroWords = explode(' ', $heroHeading);
                        $highlight = ['dogs', 'tails', 'σκυλοι', 'σκύλοι', 'ουρες', 'ουρές'];
                        $norm = fn (string $w) => mb_strtolower(trim($w, ".,!;:—-«»\"'"));
                    @endphp
                    @foreach($heroWords as $i => $word)
                        <span @if(in_array($norm($word), $highlight, true)) class="text-fire" @endif>{{ $word }}</span>{{ $i < count($heroWords) - 1 ? ' ' : '' }}
                    @endforeach
                </h1>
                <p class="mt-6 max-w-xl text-xl md:text-2xl text-ink/80 font-editorial italic">
                    {{ $heroSub }}
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <x-site.rough-button href="{{ $heroCtaLink }}" variant="ink">
                        {{ $heroCtaText }} <span class="wiggle inline-block">→</span>
                    </x-site.rough-button>
                    {{-- <x-site.rough-button href="{{ route('stores') }}" variant="fire">
                        Find a store
                    </x-site.rough-button> --}}
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-3 text-xs font-bold uppercase tracking-widest text-ink/70">
                    <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-fire"></span> {{ __('Small batch') }}</span>
                    <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-fire"></span> {{ __('Single-protein') }}</span>
                    <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-fire"></span> {{ __('Vet-approved') }}</span>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-6 rotate-[-3deg] bg-fire/15 border-2 border-ink"></div>
                <div class="relative aspect-[4/5] border-2 border-ink bg-bone sticker-shadow overflow-hidden">
                    @if(!empty($site['hero_image']))
                        <x-site.img :src="$site['hero_image']" alt="" sizes="(min-width: 1024px) 33vw, (min-width: 640px) 60vw, 100vw" loading="eager" fetchpriority="high" class="h-full w-full object-cover"/>
                    @else
                        <div class="h-full w-full halftone-fire flex items-center justify-center">
                            <div class="text-center px-4">
                                <div class="font-display text-6xl md:text-7xl font-black uppercase text-ink leading-none">{{ __('CHEW') }}</div>
                                <div class="font-display text-6xl md:text-7xl font-black uppercase text-fire leading-none">{{ __('LOUD') }}</div>
                                <div class="font-display text-6xl md:text-7xl font-black uppercase text-ink leading-none">{{ __('CHEW') }}</div>
                                <div class="font-display text-6xl md:text-7xl font-black uppercase text-fire leading-none">{{ __('PROUD') }}</div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="absolute -bottom-6 -left-6 stamp bg-bone rotate-tilt-2">
                    <span class="font-display text-xs font-black uppercase text-center leading-tight">{!! __('100%<br>WAGS') !!}</span>
                </div>
            </div>
        </div>
    </section>

    {{-- CATEGORY LINEUP --}}
    <x-site.torn-section bg="bone" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="text-center mb-16 md:mb-24">
                <div class="text-fire text-sm font-bold uppercase tracking-[0.3em]">{{ __('Two ways to treat') }}</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">{{ __('Choose your chew') }}</h2>
            </div>

            @php
                $lineup = [
                    [
                        'number' => '01',
                        'badge' => __('Chews'),
                        'word' => __('CHEWS'),
                        'title' => __('Natural Single-Protein Chews'),
                        'text' => __('Wholesome, simple yet delicious — our single-protein chews are made from carefully selected natural ingredients with no additives or fillers. Perfect for sensitive dogs, they support dental health, satisfy chewing instincts, and offer a healthy snack you can trust.'),
                        'link' => route('products.index', ['type' => \App\Models\Product::TYPE_NATURAL_CHEWS]),
                        'cta' => __('Explore chews'),
                        'image' => $site['lineup_chews_image'] ?? null,
                    ],
                    [
                        'number' => '02',
                        'badge' => __('Treats'),
                        'badge_class' => 'bg-fire text-bone',
                        'word' => __('TREATS'),
                        'title' => __('Training Treats'),
                        'text' => __('Make every training session a success! Our grain-free treats are bite-sized, delicious, and crafted from natural ingredients, making them the ideal reward to motivate your dog while keeping them healthy and happy.'),
                        'link' => route('products.index', ['type' => \App\Models\Product::TYPE_TRAINING_TREATS]),
                        'cta' => __('Explore treats'),
                        'image' => $site['lineup_treats_image'] ?? null,
                    ],
                    // Natural Sausages hidden until the category is available — restore this entry when it is.
                    // [
                    //     'number' => '03',
                    //     'badge' => __('Sausages'),
                    //     'word' => __('SAUSAGES'),
                    //     'title' => __('Natural Sausages'),
                    //     'text' => __('Made with real meat and collagen casing, our natural sausages deliver flavor, nutrition, and added benefits for your dog\'s joints, skin, and coat. The difference from ordinary sausages is noticeable in every bite — wholesome, delicious, and crafted with care by us, the producers.'),
                    //     'link' => route('products.index'),
                    //     'cta' => __('Explore sausages'),
                    // ],
                ];
            @endphp

            <div class="space-y-16 md:space-y-24">
                @foreach($lineup as $i => $cat)
                    <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                        <div class="relative {{ $i % 2 === 1 ? 'lg:order-2' : '' }}">
                            <div class="absolute -inset-4 {{ $i % 2 === 1 ? 'rotate-[2deg]' : 'rotate-[-2deg]' }} bg-ink/15 border-2 border-ink"></div>
                            <div class="relative aspect-[4/3] border-2 border-ink bg-bone sticker-shadow overflow-hidden">
                                @if(!empty($cat['image']))
                                    <x-site.img :src="$cat['image']" :alt="$cat['title']" sizes="(min-width: 1024px) 50vw, 100vw" class="h-full w-full object-cover"/>
                                @else
                                    <div class="h-full w-full halftone-fire flex items-center justify-center">
                                        <span class="font-display text-5xl md:text-7xl font-black uppercase text-ink/30 {{ $i % 2 === 1 ? 'rotate-tilt-2' : 'rotate-tilt-1' }}">{{ $cat['word'] }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="absolute -bottom-6 {{ $i % 2 === 1 ? '-right-5' : '-left-5' }} stamp bg-bone {{ $i % 2 === 1 ? 'rotate-tilt-1' : 'rotate-tilt-2' }}">
                                <span class="font-display text-lg font-black">{{ $cat['number'] }}</span>
                            </div>
                        </div>
                        <div class="{{ $i % 2 === 1 ? 'lg:order-1' : '' }}">
                            <span class="inline-flex items-center border-2 border-ink {{ $cat['badge_class'] ?? 'bg-grass text-ink' }} px-2.5 py-1 text-xs font-bold uppercase tracking-widest">{{ $cat['badge'] }}</span>
                            <h3 class="mt-4 font-display text-3xl md:text-5xl font-black uppercase leading-[0.95]">{{ $cat['title'] }}</h3>
                            <p class="mt-4 text-ink/85 leading-relaxed text-[17px]">{{ $cat['text'] }}</p>
                            <div class="mt-7">
                                <x-site.rough-button href="{{ $cat['link'] }}" variant="ink">
                                    {{ $cat['cta'] }} <span aria-hidden="true" class="wiggle inline-block">→</span>
                                </x-site.rough-button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </x-site.torn-section>

    {{-- FEATURED PRODUCTS --}}
    <x-site.torn-section bg="fire" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="flex flex-col items-start md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <div class="text-bone/80 text-sm font-bold uppercase tracking-[0.3em]">{{ __('Customer favourites') }}</div>
                    <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase text-bone">{{ __('The hot picks') }}</h2>
                </div>
                <x-site.rough-button href="{{ route('products.index') }}" variant="ink" class="hidden md:inline-flex">{{ __('View everything') }}</x-site.rough-button>
            </div>

            @if($featured->isEmpty())
                <div class="text-center text-bone/70 py-10">{{ __('No products yet. Stand by.') }}</div>
            @else
                <x-site.mobile-carousel :count="$featured->count()" grid="md:grid-cols-3 lg:grid-cols-4" tone="ink">
                    @foreach($featured as $product)
                        <x-site.carousel-item><x-site.product-card :product="$product"/></x-site.carousel-item>
                    @endforeach
                </x-site.mobile-carousel>

                <div class="mt-8 flex justify-center md:hidden">
                    <x-site.rough-button href="{{ route('products.index') }}" variant="ink">{{ __('View everything') }}</x-site.rough-button>
                </div>
            @endif
        </div>
    </x-site.torn-section>

    {{-- BRAND VALUES (Stamps) --}}
    <x-site.torn-section bg="grass" :top="true" :bottom="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="text-center mb-10">
                <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">{{ __('What we stand for') }}</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">{{ __('Made loud, made right') }}</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <x-site.stamp-icon glyph="✻" :label="__('Small batch')" bg="bone"/>
                <x-site.stamp-icon glyph="✚" :label="__('Vet approved')" bg="fire"/>
                <x-site.stamp-icon glyph="✦" :label="__('Clean labels')" bg="bone"/>
                <x-site.stamp-icon glyph="✺" :label="__('Tail-tested')" bg="ink"/>
            </div>
        </div>
    </x-site.torn-section>

    {{-- ABOUT PREVIEW --}}
    <section class="bg-bone">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-20 md:py-28 grid lg:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <div class="absolute -inset-4 rotate-[2deg] bg-ink/10 border-2 border-ink"></div>
                <div class="relative aspect-[4/5] border-2 border-ink bg-fire overflow-hidden">
                    <x-site.img :src="$site['our_story_image'] ?? null" fallback="our-story-feat.jpg" :alt="__('Our story')" sizes="(min-width: 1024px) 50vw, 100vw" class="h-full w-full object-cover"/>
                </div>
            </div>
            <div>
                <div class="text-fire text-sm font-bold uppercase tracking-[0.3em]">{{ __('Our story') }}</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase leading-[0.95]">
                    {!! __('A brand created by a <span class="underline-brush">producer</span>') !!}
                </h2>
                <p class="mt-5 text-ink/85 leading-relaxed text-[17px]">
                    {{ __('Delitails creates natural, single-protein chews, training treats, and sausages designed with care, simplicity, and quality at their core. Our approach is built on honesty: clean ingredients, no unnecessary additives, and products you can trust.') }}
                </p>
                <div class="mt-7">
                    <x-site.rough-button href="{{ route('about') }}" variant="fire">{{ __('Read our story') }}</x-site.rough-button>
                </div>
            </div>
        </div>
    </section>

    {{-- BLOG PREVIEW --}}
    <x-site.torn-section bg="ink" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="flex flex-col items-start md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">{{ __('Bark journal') }}</div>
                    <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">{{ __('Words for the pack') }}</h2>
                </div>
                <x-site.rough-button href="{{ route('blog.index') }}" variant="bone" class="hidden md:inline-flex">{{ __('Read the blog') }}</x-site.rough-button>
            </div>

            @if($posts->isEmpty())
                <div class="text-bone/50 text-center py-10">{{ __('Nothing to read yet.') }}</div>
            @else
                <x-site.mobile-carousel :count="$posts->count()" grid="md:grid-cols-3" gap="md:gap-6" tone="bone">
                    @foreach($posts as $post)
                        <x-site.carousel-item><x-site.blog-card :post="$post"/></x-site.carousel-item>
                    @endforeach
                </x-site.mobile-carousel>

                <div class="mt-8 flex justify-center md:hidden">
                    <x-site.rough-button href="{{ route('blog.index') }}" variant="bone">{{ __('Read the blog') }}</x-site.rough-button>
                </div>
            @endif
        </div>
    </x-site.torn-section>

    @if(($site['stores_page_status'] ?? 'draft') === 'public')
    {{-- STORE LOCATOR PREVIEW (hidden while stockists page is draft) --}}
    <x-site.torn-section bg="fire" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="flex flex-col items-start md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <div class="text-bone/80 text-sm font-bold uppercase tracking-[0.3em]">{{ __('Find us in the wild') }}</div>
                    <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase text-bone">{{ __('Stockists') }}</h2>
                </div>
                <x-site.rough-button href="{{ route('stores') }}" variant="ink">{{ __('All stores') }}</x-site.rough-button>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($stores as $s)
                    <div class="brush-card bg-bone p-5 text-ink">
                        <div class="text-xs font-bold uppercase tracking-widest text-fire">{{ $s->city }}</div>
                        <h3 class="mt-1 font-display text-xl font-extrabold uppercase">{{ $s->name }}</h3>
                        <p class="mt-2 text-sm text-ink/70">{{ $s->address }}</p>
                        @if($s->phone)<p class="mt-1 text-sm">{{ $s->phone }}</p>@endif
                        @if($s->map_link)
                            <a href="{{ $s->map_link }}" target="_blank" rel="noopener" class="mt-3 inline-block text-xs font-bold uppercase tracking-widest text-fire hover:text-ink">{{ __('View on map') }} →</a>
                        @endif
                    </div>
                @empty
                    @for($i=0;$i<4;$i++)
                        <div class="brush-card bg-bone/90 p-5 text-ink/40 text-center">{{ __('Coming soon') }}</div>
                    @endfor
                @endforelse
            </div>
        </div>
    </x-site.torn-section>
    @endif

    {{-- FAQ PREVIEW --}}
    <x-site.torn-section bg="bone" :top="true">
        <div class="mx-auto max-w-4xl px-4 md:px-6">
            <div class="text-center mb-10">
                <div class="text-fire text-sm font-bold uppercase tracking-[0.3em]">{{ __('Asked & answered') }}</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">{{ __('FAQ shortlist') }}</h2>
            </div>
            <x-site.faq-accordion :faqs="$faqs"/>
            <div class="text-center mt-8">
                <x-site.rough-button href="{{ route('faq') }}" variant="ink">{{ __('All the questions') }}</x-site.rough-button>
            </div>
        </div>
    </x-site.torn-section>

    {{-- TESTIMONIALS --}}
    <x-site.torn-section bg="grass" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="text-center mb-12">
                <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">{{ __('From the pack') }}</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">{{ __('Word on the street') }}</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($testimonials as $t)
                    <div class="brush-card bg-bone p-6 {{ $loop->iteration % 2 === 0 ? 'rotate-tilt-2' : 'rotate-tilt-1' }}">
                        <div class="flex items-center gap-1.5 text-fire mb-2" role="img" aria-label="{{ __(':rating out of 5 stars', ['rating' => $t->rating]) }}">
                            @for($star = 0; $star < $t->rating; $star++)
                                <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true" class="h-6 w-6 {{ $star % 2 === 0 ? 'rotate-[-8deg]' : 'rotate-[8deg]' }}">
                                    <ellipse cx="5.2" cy="9" rx="2.1" ry="2.9" transform="rotate(-24 5.2 9)"/>
                                    <ellipse cx="18.8" cy="9" rx="2.1" ry="2.9" transform="rotate(24 18.8 9)"/>
                                    <ellipse cx="9.2" cy="5" rx="2.1" ry="3" transform="rotate(-9 9.2 5)"/>
                                    <ellipse cx="14.8" cy="5" rx="2.1" ry="3" transform="rotate(9 14.8 5)"/>
                                    <path d="M12 10c-3.4 0-6.3 3.1-6.3 6 0 1.9 1.4 3.2 3.2 3.2 1.1 0 2-.5 3.1-.5s2 .5 3.1.5c1.8 0 3.2-1.3 3.2-3.2 0-2.9-2.9-6-6.3-6z"/>
                                </svg>
                            @endfor
                        </div>
                        <p class="font-editorial italic text-xl leading-relaxed">"{{ $t->t('quote') }}"</p>
                        <div class="mt-4">
                            <div class="font-display font-extrabold uppercase">{{ $t->t('author') }}</div>
                            @if($t->t('pet_name'))<div class="text-xs uppercase tracking-widest text-ink/60">{{ __('w/') }} {{ $t->t('pet_name') }}</div>@endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-ink/60">{{ __('No reviews yet.') }}</div>
                @endforelse
            </div>
        </div>
    </x-site.torn-section>

    {{-- NEWSLETTER --}}
    <x-site.torn-section bg="ink" :top="true">
        <div class="mx-auto max-w-4xl px-4 md:px-6 text-center">
            <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">{{ $pick('newsletter_heading') ?? __('Join the pack') }}</div>
            <h2 class="mt-3 font-display text-4xl md:text-6xl font-black uppercase">{{ __("Don't miss a bite") }}</h2>
            <p class="mt-4 font-editorial italic text-xl md:text-2xl text-bone/70 max-w-2xl mx-auto">
                {{ $pick('newsletter_text') ?? __('New drops, late-night deals and just enough chaos. Drop your email below — we promise not to spam.') }}
            </p>

            @if(session('success'))
                <div class="mt-5 inline-block border-2 border-bone bg-grass px-4 py-2 font-bold uppercase tracking-wider text-ink">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('newsletter.subscribe') }}" class="mt-6 mx-auto flex flex-col sm:flex-row gap-3 max-w-xl">
                @csrf
                <input type="text" name="hp_field" class="hidden" tabindex="-1" autocomplete="off">
                <input type="email" name="email" required placeholder="your@email.com"
                       class="flex-1 border-2 border-bone bg-ink-700 text-bone placeholder:text-bone/40 px-4 py-3 font-display uppercase focus:outline-none focus:ring-2 focus:ring-fire/60">
                <x-site.rough-button variant="fire" type="submit">{{ __('Sign me up') }}</x-site.rough-button>
            </form>
            @error('email') <p class="mt-2 text-sm text-fire-light">{{ $message }}</p> @enderror
        </div>
    </x-site.torn-section>
</x-layout>
