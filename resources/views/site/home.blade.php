@php
    $siteName = $site['site_name'] ?? config('app.name', 'Delitails');
    $heroHeading = $site['hero_heading'] ?? 'TREATS WITH ATTITUDE.';
    $heroSub = $site['hero_subheading'] ?? 'Loud little snacks for picky pets and the humans who feed them. Small-batch, big personality.';
    $heroCtaText = $site['hero_cta_text'] ?? 'Shop the pack';
    $heroCtaLink = $site['hero_cta_link'] ?? route('products.index');
    $title = ($site['seo_default_title'] ?? null) ?: $siteName.' — Premium pet treats';
    $description = $site['seo_default_description'] ?? $heroSub;
@endphp
<x-layout title="{{ $title }}" description="{{ $description }}">
    {{-- HERO --}}
    <section class="relative overflow-hidden bg-grass paper">
        <div class="absolute inset-0 halftone-light opacity-30 pointer-events-none"></div>

        <div class="relative mx-auto max-w-7xl px-4 md:px-6 py-16 md:py-24 grid lg:grid-cols-3 gap-10 items-center">
            <div class="relative z-10 lg:col-span-2">
                <div class="inline-flex items-center gap-2 ribbon mb-5">
                    <span>★ 100% Natural</span>
                </div>
                <h1 class="font-display text-5xl sm:text-6xl md:text-7xl lg:text-8xl font-black uppercase leading-[0.9] tracking-tight text-ink">
                    @foreach(explode(' ', $heroHeading) as $i => $word)
                        <span @if(strtolower(str_replace('.', '', str_replace(',', '', $word))) === 'dogs' || strtolower(str_replace('.', '', str_replace(',', '', $word))) === 'tails') class="text-fire" @endif>{{ $word }}</span>{{ $i < count(explode(' ', $heroHeading)) - 1 ? ' ' : '' }}
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
                    <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-fire"></span> Small batch</span>
                    <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-fire"></span> No nasties</span>
                    <span class="flex items-center gap-2"><span class="h-2 w-2 rounded-full bg-fire"></span> Vet-approved</span>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -inset-6 rotate-[-3deg] bg-fire/15 border-2 border-ink"></div>
                <div class="relative aspect-[4/5] border-2 border-ink bg-bone sticker-shadow overflow-hidden">
                    @if(!empty($site['hero_image']))
                        <img src="{{ asset('storage/'.$site['hero_image']) }}" alt="" class="h-full w-full object-cover">
                    @else
                        <div class="h-full w-full halftone-fire flex items-center justify-center">
                            <div class="text-center px-4">
                                <div class="font-display text-6xl md:text-7xl font-black uppercase text-ink leading-none">CHEW</div>
                                <div class="font-display text-6xl md:text-7xl font-black uppercase text-fire leading-none">LOUD</div>
                                <div class="font-display text-6xl md:text-7xl font-black uppercase text-ink leading-none">CHEW</div>
                                <div class="font-display text-6xl md:text-7xl font-black uppercase text-fire leading-none">PROUD</div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="absolute -bottom-6 -left-6 stamp bg-bone rotate-tilt-2">
                    <span class="font-display text-xs font-black uppercase text-center leading-tight">100%<br>WAGS</span>
                </div>
            </div>
        </div>
    </section>

    {{-- FEATURED PRODUCTS --}}
    <x-site.torn-section bg="bone" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <div class="text-fire text-sm font-bold uppercase tracking-[0.3em]">Customer favourites</div>
                    <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">The hot picks</h2>
                </div>
                <x-site.rough-button href="{{ route('products.index') }}" variant="ink">View everything</x-site.rough-button>
            </div>

            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
                @forelse($featured as $product)
                    <x-site.product-card :product="$product"/>
                @empty
                    <div class="col-span-full text-center text-ink/60 py-10">No products yet. Stand by.</div>
                @endforelse
            </div>
        </div>
    </x-site.torn-section>

    {{-- BRAND VALUES (Stamps) --}}
    <x-site.torn-section bg="grass" :top="true" :bottom="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="text-center mb-10">
                <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">What we stand for</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">Made loud, made right</h2>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <x-site.stamp-icon glyph="✻" label="Small batch" bg="bone"/>
                <x-site.stamp-icon glyph="✚" label="Vet approved" bg="fire"/>
                <x-site.stamp-icon glyph="✦" label="Clean labels" bg="bone"/>
                <x-site.stamp-icon glyph="✺" label="Tail-tested" bg="ink"/>
            </div>
        </div>
    </x-site.torn-section>

    {{-- ABOUT PREVIEW --}}
    <section class="bg-bone">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-20 md:py-28 grid lg:grid-cols-2 gap-12 items-center">
            <div class="relative">
                <div class="absolute -inset-4 rotate-[2deg] bg-ink/10 border-2 border-ink"></div>
                <div class="relative aspect-[4/5] border-2 border-ink bg-fire overflow-hidden">
                    <img src="{{ asset('storage/our-story-feat.jpg') }}" alt="Our story" class="h-full w-full object-cover">
                </div>
            </div>
            <div>
                <div class="text-fire text-sm font-bold uppercase tracking-[0.3em]">Our story</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase leading-[0.95]">
                    A <span class="underline-brush">brand</span> created by a producer
                </h2>
                <p class="mt-5 font-editorial text-xl md:text-2xl text-ink/80 leading-relaxed italic">
                    Delitails creates natural, single-protein chews, training treats, and sausages designed with care, simplicity, and quality at their core. Our approach is built on honesty: clean ingredients, no unnecessary additives, and products you can trust.
                </p>
                <div class="mt-7">
                    <x-site.rough-button href="{{ route('about') }}" variant="fire">Read our story</x-site.rough-button>
                </div>
            </div>
        </div>
    </section>

    {{-- BLOG PREVIEW --}}
    <x-site.torn-section bg="ink" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">Bark journal</div>
                    <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">Words for the pack</h2>
                </div>
                <x-site.rough-button href="{{ route('blog.index') }}" variant="bone">Read the blog</x-site.rough-button>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                @forelse($posts as $post)
                    <x-site.blog-card :post="$post"/>
                @empty
                    <div class="col-span-full text-bone/50 text-center py-10">Nothing to read yet.</div>
                @endforelse
            </div>
        </div>
    </x-site.torn-section>

    @if(false)
    {{-- STORE LOCATOR PREVIEW (HIDDEN) --}}
    <x-site.torn-section bg="fire" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-10">
                <div>
                    <div class="text-bone/80 text-sm font-bold uppercase tracking-[0.3em]">Find us in the wild</div>
                    <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase text-bone">Stockists</h2>
                </div>
                <x-site.rough-button href="{{ route('stores') }}" variant="ink">All stores</x-site.rough-button>
            </div>

            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @forelse($stores as $s)
                    <div class="brush-card bg-bone p-5 text-ink">
                        <div class="text-xs font-bold uppercase tracking-widest text-fire">{{ $s->city }}</div>
                        <h3 class="mt-1 font-display text-xl font-extrabold uppercase">{{ $s->name }}</h3>
                        <p class="mt-2 text-sm text-ink/70">{{ $s->address }}</p>
                        @if($s->phone)<p class="mt-1 text-sm">{{ $s->phone }}</p>@endif
                        @if($s->map_link)
                            <a href="{{ $s->map_link }}" target="_blank" rel="noopener" class="mt-3 inline-block text-xs font-bold uppercase tracking-widest text-fire hover:text-ink">View on map →</a>
                        @endif
                    </div>
                @empty
                    @for($i=0;$i<4;$i++)
                        <div class="brush-card bg-bone/90 p-5 text-ink/40 text-center">Coming soon</div>
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
                <div class="text-fire text-sm font-bold uppercase tracking-[0.3em]">Asked & answered</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">FAQ shortlist</h2>
            </div>
            <x-site.faq-accordion :faqs="$faqs"/>
            <div class="text-center mt-8">
                <x-site.rough-button href="{{ route('faq') }}" variant="ink">All the questions</x-site.rough-button>
            </div>
        </div>
    </x-site.torn-section>

    {{-- TESTIMONIALS --}}
    <x-site.torn-section bg="grass" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="text-center mb-12">
                <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">From the pack</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">Word on the street</h2>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($testimonials as $t)
                    <div class="brush-card bg-bone p-6 {{ $loop->iteration % 2 === 0 ? 'rotate-tilt-2' : 'rotate-tilt-1' }}">
                        <div class="text-fire text-2xl mb-2">{!! str_repeat('★', $t->rating) !!}</div>
                        <p class="font-editorial italic text-xl leading-relaxed">"{{ $t->quote }}"</p>
                        <div class="mt-4 flex items-center gap-3">
                            @if($t->avatar)
                                <img src="{{ asset('storage/'.$t->avatar) }}" alt="" class="h-12 w-12 rounded-full object-cover border-2 border-ink">
                            @else
                                <div class="h-12 w-12 rounded-full border-2 border-ink bg-fire halftone-light"></div>
                            @endif
                            <div>
                                <div class="font-display font-extrabold uppercase">{{ $t->author }}</div>
                                @if($t->pet_name)<div class="text-xs uppercase tracking-widest text-ink/60">w/ {{ $t->pet_name }}</div>@endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center text-ink/60">No reviews yet.</div>
                @endforelse
            </div>
        </div>
    </x-site.torn-section>

    {{-- NEWSLETTER --}}
    <x-site.torn-section bg="ink" :top="true">
        <div class="mx-auto max-w-4xl px-4 md:px-6 text-center">
            <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">{{ $site['newsletter_heading'] ?? 'Join the pack' }}</div>
            <h2 class="mt-3 font-display text-4xl md:text-6xl font-black uppercase">Don't miss a bite</h2>
            <p class="mt-4 font-editorial italic text-xl md:text-2xl text-bone/70 max-w-2xl mx-auto">
                {{ $site['newsletter_text'] ?? 'New drops, late-night deals and just enough chaos. Drop your email below — we promise not to spam.' }}
            </p>

            @if(session('success'))
                <div class="mt-5 inline-block border-2 border-bone bg-grass px-4 py-2 font-bold uppercase tracking-wider text-ink">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('newsletter.subscribe') }}" class="mt-6 mx-auto flex flex-col sm:flex-row gap-3 max-w-xl">
                @csrf
                <input type="text" name="hp_field" class="hidden" tabindex="-1" autocomplete="off">
                <input type="email" name="email" required placeholder="your@email.com"
                       class="flex-1 border-2 border-bone bg-ink-700 text-bone placeholder:text-bone/40 px-4 py-3 font-display uppercase focus:outline-none focus:ring-2 focus:ring-fire/60">
                <x-site.rough-button variant="fire" type="submit">Sign me up</x-site.rough-button>
            </form>
            @error('email') <p class="mt-2 text-sm text-fire-light">{{ $message }}</p> @enderror
        </div>
    </x-site.torn-section>
</x-layout>
