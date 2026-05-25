@php
    $title = ($product->seo_title ?: $product->title).' — '.($site['site_name'] ?? config('app.name'));
    $description = $product->seo_description ?: $product->short_description ?: ($site['seo_default_description'] ?? '');
@endphp
<x-layout title="{{ $title }}" description="{{ $description }}">
    <section class="bg-bone pt-10 md:pt-16 pb-20 md:pb-28">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <nav class="text-xs uppercase tracking-widest text-ink/60 mb-6">
                <a class="hover:text-fire" href="{{ route('home') }}">Home</a> /
                <a class="hover:text-fire" href="{{ route('products.index') }}">Products</a>
                @if($product->category)
                    / <a class="hover:text-fire" href="{{ route('products.index', ['category' => $product->category->slug]) }}">{{ $product->category->name }}</a>
                @endif
                / <span class="text-ink">{{ $product->title }}</span>
            </nav>

            <div class="grid lg:grid-cols-2 gap-10">
                {{-- Gallery --}}
                <div x-data="{active: 0}" class="space-y-4">
                    @php
                        $images = [];
                        if ($product->featured_image) $images[] = $product->featured_image;
                        foreach($product->images as $img) $images[] = $img->path;
                    @endphp
                    <div class="relative aspect-square border-2 border-ink bg-bone sticker-shadow overflow-hidden">
                        @if(count($images))
                            @foreach($images as $i => $path)
                                <img x-show="active === {{ $i }}" src="{{ asset('storage/'.$path) }}" alt="{{ $product->title }}" class="absolute inset-0 h-full w-full object-cover">
                            @endforeach
                        @else
                            <div class="absolute inset-0 halftone-fire flex items-center justify-center">
                                <div class="text-center px-4">
                                    <div class="font-display text-5xl font-black uppercase text-ink">{{ Str::words($product->title, 2, '') }}</div>
                                </div>
                            </div>
                        @endif
                    </div>
                    @if(count($images) > 1)
                        <div class="grid grid-cols-5 gap-2 pb-6">
                            @foreach($images as $i => $path)
                                <button type="button" @click="active={{ $i }}"
                                        :class="active === {{ $i }} ? 'border-fire' : 'border-ink'"
                                        class="aspect-square border-2 overflow-hidden">
                                    <img src="{{ asset('storage/'.$path) }}" class="h-full w-full object-cover">
                                </button>
                            @endforeach
                        </div>
                    @endif

                    {{-- Share --}}
                    @php
                        $shareUrl = urlencode(url()->current());
                        $shareText = urlencode($product->title);
                    @endphp
                    <div class="mt-4 pt-6 border-t-2 border-dashed border-ink/30 flex flex-wrap items-center gap-3">
                        <span class="text-xs font-bold uppercase tracking-[0.3em] text-ink/60">Share</span>
                        <div class="flex flex-wrap gap-2">
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ $shareUrl }}" target="_blank" rel="noopener" aria-label="Share on Facebook"
                               class="inline-flex h-10 w-10 items-center justify-center border-2 border-ink bg-bone text-ink hover:bg-fire hover:text-bone transition">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12a10 10 0 1 0-11.6 9.9v-7H8v-2.9h2.4V9.4c0-2.4 1.4-3.7 3.6-3.7 1 0 2.1.2 2.1.2v2.3h-1.2c-1.2 0-1.6.7-1.6 1.5V12h2.7l-.4 2.9h-2.3v7A10 10 0 0 0 22 12z"/></svg>
                            </a>
                            <a href="https://twitter.com/intent/tweet?text={{ $shareText }}&url={{ $shareUrl }}" target="_blank" rel="noopener" aria-label="Share on X"
                               class="inline-flex h-10 w-10 items-center justify-center border-2 border-ink bg-bone text-ink hover:bg-fire hover:text-bone transition">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </a>
                            <a href="https://wa.me/?text={{ $shareText }}%20{{ $shareUrl }}" target="_blank" rel="noopener" aria-label="Share on WhatsApp"
                               class="inline-flex h-10 w-10 items-center justify-center border-2 border-ink bg-bone text-ink hover:bg-grass transition">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M17.5 14.4c-.3-.1-1.7-.8-2-.9-.3-.1-.5-.1-.7.1-.2.3-.7.9-.9 1.1-.2.2-.3.2-.6.1-.3-.1-1.2-.4-2.3-1.4-.9-.8-1.4-1.7-1.6-2-.2-.3 0-.5.1-.6.1-.1.3-.3.4-.5.1-.2.2-.3.2-.5.1-.2 0-.4 0-.5l-.9-2.2c-.2-.5-.5-.5-.7-.5h-.6c-.2 0-.5.1-.8.4-.3.3-1 1-1 2.4s1 2.8 1.2 3c.1.2 2 3.2 4.9 4.4.7.3 1.2.5 1.6.6.7.2 1.3.2 1.8.1.6-.1 1.7-.7 1.9-1.4.2-.7.2-1.2.2-1.4 0-.1-.3-.2-.6-.3zM12 2C6.5 2 2 6.5 2 12c0 1.9.5 3.7 1.5 5.3L2 22l4.9-1.5C8.4 21.5 10.2 22 12 22c5.5 0 10-4.5 10-10S17.5 2 12 2z"/></svg>
                            </a>
                            <a href="mailto:?subject={{ $shareText }}&body={{ $shareUrl }}" aria-label="Share by email"
                               class="inline-flex h-10 w-10 items-center justify-center border-2 border-ink bg-bone text-ink hover:bg-fire hover:text-bone transition">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="5" width="18" height="14" rx="1"/><path d="M3 7l9 6 9-6"/></svg>
                            </a>
                            <button type="button"
                                    x-data="{ copied: false }"
                                    @click="navigator.clipboard.writeText(window.location.href); copied = true; setTimeout(() => copied = false, 1500)"
                                    :aria-label="copied ? 'Copied' : 'Copy link'"
                                    class="inline-flex h-10 w-10 items-center justify-center border-2 border-ink bg-bone text-ink hover:bg-fire hover:text-bone transition">
                                <svg x-show="!copied" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/></svg>
                                <svg x-show="copied" x-cloak width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 13l4 4 10-10"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Details --}}
                <div>
                    @if($product->category)
                        <div class="text-fire text-xs font-bold uppercase tracking-[0.3em]">{{ $product->category->name }}</div>
                    @endif
                    <h1 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase leading-[0.95]">{{ $product->title }}</h1>

                    <div class="mt-4 flex flex-wrap items-center gap-2">
                        @if($product->type_label)
                            <span class="inline-flex items-center gap-1.5 border-2 border-ink bg-fire text-bone px-2 py-1 text-[11px] font-bold uppercase tracking-wider">{{ $product->type_label }}</span>
                        @endif
                        @if($product->category)
                            <a href="{{ route('products.index', ['category' => $product->category->slug]) }}"
                               class="inline-flex items-center gap-1.5 border-2 border-ink bg-bone px-2 py-1 text-[11px] font-bold uppercase tracking-wider hover:bg-grass">
                                {{ $product->category->name }}
                            </a>
                        @endif
                    </div>

                    @if($product->short_description)
                        <p class="mt-5 font-editorial italic text-xl text-ink/80 leading-relaxed">{{ $product->short_description }}</p>
                    @endif

                    <div class="mt-6 flex flex-wrap items-center gap-3">
                        <x-site.rough-button href="{{ route('stores') }}" variant="fire">Find a stockist</x-site.rough-button>
                        <x-site.rough-button href="{{ route('products.index') }}" variant="grass">Keep browsing</x-site.rough-button>
                    </div>

                    @if($product->characteristics)
                        <div class="mt-8 border-2 border-dashed border-ink bg-grass/15 p-5">
                            <h2 class="font-display text-xl font-extrabold uppercase mb-3">Why it's good</h2>
                            <div class="characteristics text-ink/85 font-editorial leading-relaxed text-xl">
                                {!! $product->characteristics !!}
                            </div>
                        </div>
                    @endif

                    @if($product->description)
                        <div class="mt-8">
                            <h2 class="font-display text-xl font-extrabold uppercase mb-3">The details</h2>
                            <div class="prose prose-ink max-w-none font-editorial leading-relaxed text-lg text-ink/80 whitespace-pre-line">{{ $product->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Why natural chews work --}}
            <div class="mt-16 md:mt-20">
                <div class="text-fire text-sm font-bold uppercase tracking-[0.3em]">The lowdown</div>
                <h2 class="mt-2 font-display text-3xl md:text-5xl font-black uppercase">Why these treats work</h2>

                <div class="mt-8 space-y-3" x-data="{ open: 0 }">
                    @php
                        $accordion = [
                            [
                                'title' => 'Natural & single-ingredient',
                                'body' => "<p>Real food, full stop. One animal, one cut, air-dried to lock in nutrients. No fillers, no shortcuts, no surprises in the bag — just the kind of chew a dog would pick for themselves if they were running the kitchen.</p>",
                            ],
                            [
                                'title' => 'Air-dried, never rushed',
                                'body' => "<p>Every batch is dried low-and-slow under controlled conditions. That keeps the protein, the smell and the satisfying chew exactly where they belong — and lets your dog get the most out of every bite.</p>",
                            ],
                            [
                                'title' => 'Respect the dog',
                                'body' => "<p>Single-protein means easier digestion, fewer surprises and a treat that respects your dog's instinctive diet. No additives, no preservatives, no apologies.</p>",
                            ],
                            [
                                'title' => "Don't forget",
                                'body' => "<p class='mb-3'>A few good rules for the bowl:</p>
                                           <ul>
                                               <li>Lead with real meat</li>
                                               <li>Less processing, more dog</li>
                                               <li>High-quality protein wins every time</li>
                                               <li>Fresh water always goes alongside</li>
                                           </ul>",
                            ],
                        ];
                    @endphp

                    @foreach($accordion as $i => $item)
                        <div class="brush-card bg-bone overflow-hidden">
                            <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                                    class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                                <span class="font-display text-lg md:text-xl font-extrabold uppercase">{{ $item['title'] }}</span>
                                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center border-2 border-ink bg-fire text-bone font-black leading-none"
                                      :class="{'bg-ink': open === {{ $i }}}"
                                      x-text="open === {{ $i }} ? '−' : '+'">+</span>
                            </button>
                            <div x-show="open === {{ $i }}" x-collapse x-cloak>
                                <div class="border-t-2 border-dashed border-ink/30 px-5 py-4 text-ink/80 font-editorial text-xl leading-relaxed characteristics">
                                    {!! $item['body'] !!}
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    @if($related->count())
        <x-site.torn-section bg="ink" :top="true">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">You might also like</div>
                <h2 class="mt-2 font-display text-4xl md:text-5xl font-black uppercase">More for the bowl</h2>
                <div class="cards-fire-shadow mt-8 grid grid-cols-2 md:grid-cols-4 gap-5">
                    @foreach($related as $r)
                        <x-site.product-card :product="$r"/>
                    @endforeach
                </div>
            </div>
        </x-site.torn-section>
    @endif
</x-layout>
