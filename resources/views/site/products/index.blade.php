@php
    $titleParts = [];
    if ($activeCategory) $titleParts[] = $activeCategory->name;
    if (!empty($activeType) && isset($types[$activeType])) $titleParts[] = $types[$activeType];
    $title = (count($titleParts) ? implode(' · ', $titleParts).' — ' : '').'Treats — '.($site['site_name'] ?? config('app.name'));
    $description = $activeCategory?->seo_description ?? ($site['seo_default_description'] ?? null);

    $linkBase = fn(array $extra = []) => route('products.index', array_filter(array_merge(
        ['category' => $activeCategory?->slug, 'type' => $activeType, 'q' => request('q'), 'sort' => $sort !== 'featured' ? $sort : null],
        $extra
    ), fn($v) => $v !== null && $v !== ''));
@endphp
<x-layout title="{{ $title }}" description="{{ $description }}">
    {{-- Header --}}
    <section class="bg-fire paper text-bone">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-16 md:py-20">
            <div class="text-bone/80 text-sm font-bold uppercase tracking-[0.3em]">Catalogue</div>
            <h1 class="mt-3 font-display text-5xl md:text-7xl font-black uppercase leading-[0.9]">
                @if($activeCategory)
                    {{ $activeCategory->name }}
                @elseif($activeType && isset($types[$activeType]))
                    {{ $types[$activeType] }}
                @else
                    Every treat <span class="text-ink">we got</span>
                @endif
            </h1>
            @if($activeCategory?->description)
                <p class="mt-4 max-w-2xl font-editorial italic text-xl text-bone/85">{{ $activeCategory->description }}</p>
            @else
                <p class="mt-4 max-w-2xl font-editorial italic text-xl text-bone/85">Real food, loud labels, zero filler. Pick your favourites — or let your pet do it for you.</p>
            @endif

            {{-- Active filter chips --}}
            @if($activeCategory || $activeType)
                <div class="mt-6 flex flex-wrap gap-2">
                    @if($activeCategory)
                        <a href="{{ $linkBase(['category' => null]) }}"
                           class="inline-flex items-center gap-2 border-2 border-bone bg-ink px-3 py-1 font-display text-xs font-bold uppercase tracking-wider">
                            Animal: {{ $activeCategory->name }} <span class="text-fire-light">✕</span>
                        </a>
                    @endif
                    @if($activeType && isset($types[$activeType]))
                        <a href="{{ $linkBase(['type' => null]) }}"
                           class="inline-flex items-center gap-2 border-2 border-bone bg-ink px-3 py-1 font-display text-xs font-bold uppercase tracking-wider">
                            Type: {{ $types[$activeType] }} <span class="text-fire-light">✕</span>
                        </a>
                    @endif
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 border-2 border-bone bg-bone text-ink px-3 py-1 font-display text-xs font-bold uppercase tracking-wider">
                        Clear all
                    </a>
                </div>
            @endif
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-fire"></div>
        </div>
    </section>

    <section class="bg-bone py-10 md:py-14">
        <div class="mx-auto max-w-7xl px-4 md:px-6 grid lg:grid-cols-4 gap-10">
            {{-- Sidebar --}}
            <aside class="lg:col-span-1 space-y-6">
                <div class="brush-card bg-bone p-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">Search</h3>
                    <form method="GET" class="mt-3">
                        @if($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory->slug }}">@endif
                        @if($activeType)<input type="hidden" name="type" value="{{ $activeType }}">@endif
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="Find a treat…"
                               class="w-full border-2 border-ink bg-bone px-3 py-2 font-display uppercase placeholder:text-ink/40 focus:outline-none focus:ring-2 focus:ring-fire/50">
                        <button class="btn-rough is-fire is-sm mt-3 w-full justify-center">Search</button>
                    </form>
                </div>

                <div class="brush-card bg-bone p-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">Type</h3>
                    <ul class="mt-3 space-y-1">
                        <li>
                            <a href="{{ $linkBase(['type' => null]) }}"
                               class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ !$activeType ? 'border-ink bg-fire text-bone' : '' }}">
                                All types
                            </a>
                        </li>
                        @foreach($types as $key => $label)
                            @php $on = $activeType === $key; @endphp
                            <li>
                                <a href="{{ $linkBase(['type' => $key]) }}"
                                   class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ $on ? 'border-ink bg-fire text-bone' : '' }}">
                                    {{ $label }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="brush-card bg-bone p-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">Animal</h3>
                    <ul class="mt-3 space-y-1">
                        <li>
                            <a href="{{ $linkBase(['category' => null]) }}"
                               class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ !$activeCategory ? 'border-ink bg-grass' : '' }}">
                                All animals
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            @php $on = $activeCategory && $activeCategory->id === $cat->id; @endphp
                            <li>
                                <a href="{{ $linkBase(['category' => $cat->slug]) }}"
                                   class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ $on ? 'border-ink bg-grass' : '' }}">
                                    {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            {{-- Grid --}}
            <div class="lg:col-span-3">
                <form method="GET" class="mb-6 flex flex-wrap items-center gap-3">
                    @foreach(request()->except(['sort','page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <span class="text-sm text-ink/60">Showing {{ $products->total() }} treats</span>
                    <select name="sort" onchange="this.form.submit()"
                            class="ml-auto border-2 border-ink bg-bone px-3 py-2 font-display uppercase text-xs">
                        @foreach([
                            'featured' => 'Featured',
                            'newest' => 'Newest',
                            'name' => 'Name A–Z',
                        ] as $key => $label)
                            <option value="{{ $key }}" @selected($sort === $key)>Sort: {{ $label }}</option>
                        @endforeach
                    </select>
                </form>

                @if($products->count() === 0)
                    <div class="brush-card bg-bone p-10 text-center">
                        <div class="font-display text-2xl font-black uppercase">No treats match.</div>
                        <p class="mt-2 text-ink/60">Try clearing your filters and dive back in.</p>
                        <x-site.rough-button class="mt-5" href="{{ route('products.index') }}" variant="fire">Reset</x-site.rough-button>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-5">
                        @foreach($products as $product)
                            <x-site.product-card :product="$product"/>
                        @endforeach
                    </div>
                    <x-site.pagination :paginator="$products"/>
                @endif
            </div>
        </div>
    </section>
</x-layout>
