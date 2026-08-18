@php
    $titleParts = [];
    if ($activeCategory) $titleParts[] = $activeCategory->t('name');
    if (!empty($activeType) && isset($types[$activeType])) $titleParts[] = __($types[$activeType]);
    $title = (count($titleParts) ? implode(' · ', $titleParts).' — ' : '').__('Products').' — '.($site['site_name'] ?? config('app.name'));
    $description = $activeCategory?->t('seo_description') ?? ($site['seo_default_description'] ?? null);

    $linkBase = fn(array $extra = []) => route('products.index', array_filter(array_merge(
        ['category' => $activeCategory?->slug, 'type' => $activeType, 'q' => request('q'), 'sort' => $sort !== 'featured' ? $sort : null],
        $extra
    ), fn($v) => $v !== null && $v !== ''));
@endphp
<x-layout title="{{ $title }}" description="{{ $description }}">
    {{-- Header --}}
    <section class="bg-fire paper text-bone">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-10 md:py-14">
            <div class="text-bone/80 text-sm font-bold uppercase tracking-[0.3em]">{{ __('Catalogue') }}</div>
            <h1 class="mt-3 font-display text-5xl md:text-7xl font-black uppercase leading-[0.9]">
                @if($activeCategory)
                    {{ $activeCategory->t('name') }}
                @elseif($activeType && isset($types[$activeType]))
                    {{ __($types[$activeType]) }}
                @else
                    {!! __('Every treat <span class="text-ink">we got</span>') !!}
                @endif
            </h1>
            @if($activeCategory?->t('description'))
                <p class="mt-4 max-w-2xl font-editorial italic text-xl text-bone/85">{{ $activeCategory->t('description') }}</p>
            @else
                <p class="mt-4 max-w-2xl font-editorial italic text-xl text-bone/85">{{ __('Real food, loud labels, zero filler. Pick your favourites — or let your pet do it for you.') }}</p>
            @endif

            {{-- Active filter chips --}}
            @if($activeCategory || $activeType)
                <div class="mt-6 flex flex-wrap gap-2">
                    @if($activeCategory)
                        <a href="{{ $linkBase(['category' => null]) }}"
                           class="inline-flex items-center gap-2 border-2 border-bone bg-ink px-3 py-1 font-display text-xs font-bold uppercase tracking-wider">
                            {{ __('Animal') }}: {{ $activeCategory->t('name') }} <span class="text-fire-light">✕</span>
                        </a>
                    @endif
                    @if($activeType && isset($types[$activeType]))
                        <a href="{{ $linkBase(['type' => null]) }}"
                           class="inline-flex items-center gap-2 border-2 border-bone bg-ink px-3 py-1 font-display text-xs font-bold uppercase tracking-wider">
                            {{ __('Type') }}: {{ __($types[$activeType]) }} <span class="text-fire-light">✕</span>
                        </a>
                    @endif
                    <a href="{{ route('products.index') }}"
                       class="inline-flex items-center gap-2 border-2 border-bone bg-bone text-ink px-3 py-1 font-display text-xs font-bold uppercase tracking-wider">
                        {{ __('Clear all') }}
                    </a>
                </div>
            @endif
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-fire"></div>
        </div>
    </section>

    <section class="bg-bone pt-20 md:pt-28 pb-14 md:pb-20">
        <div class="mx-auto max-w-7xl px-4 md:px-6 grid lg:grid-cols-4 gap-10">
            {{-- Sidebar (desktop) --}}
            <aside class="hidden lg:block lg:col-span-1 space-y-6">
                <div class="brush-card bg-bone p-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">{{ __('Search') }}</h3>
                    <form method="GET" class="mt-3">
                        @if($activeCategory)<input type="hidden" name="category" value="{{ $activeCategory->slug }}">@endif
                        @if($activeType)<input type="hidden" name="type" value="{{ $activeType }}">@endif
                        <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Find a treat…') }}"
                               class="w-full border-2 border-ink bg-bone px-3 py-2 font-display uppercase placeholder:text-ink/40 focus:outline-none focus:ring-2 focus:ring-fire/50">
                        <button class="btn-rough is-fire is-sm mt-3 w-full justify-center">{{ __('Search') }}</button>
                    </form>
                </div>

                <div class="brush-card bg-bone p-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">{{ __('Type') }}</h3>
                    <ul class="mt-3 space-y-1">
                        <li>
                            <a href="{{ $linkBase(['type' => null]) }}"
                               class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ !$activeType ? 'border-ink bg-fire text-bone' : '' }}">
                                {{ __('All types') }}
                            </a>
                        </li>
                        @foreach($types as $key => $label)
                            @php $on = $activeType === $key; @endphp
                            <li>
                                <a href="{{ $linkBase(['type' => $key]) }}"
                                   class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ $on ? 'border-ink bg-fire text-bone' : '' }}">
                                    {{ __($label) }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="brush-card bg-bone p-5">
                    <h3 class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">{{ __('Animal') }}</h3>
                    <ul class="mt-3 space-y-1">
                        <li>
                            <a href="{{ $linkBase(['category' => null]) }}"
                               class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ !$activeCategory ? 'border-ink bg-grass' : '' }}">
                                {{ __('All animals') }}
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            @php $on = $activeCategory && $activeCategory->id === $cat->id; @endphp
                            <li>
                                <a href="{{ $linkBase(['category' => $cat->slug]) }}"
                                   class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ $on ? 'border-ink bg-grass' : '' }}">
                                    {{ $cat->t('name') }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </aside>

            {{-- Grid --}}
            <div class="lg:col-span-3 min-w-0">
                @php
                    $sortOptions = ['featured' => __('Featured'), 'newest' => __('Newest'), 'name' => __('Name A–Z')];
                    $activeFilters = ($activeCategory ? 1 : 0) + ($activeType ? 1 : 0) + (request('q') ? 1 : 0);
                @endphp

                {{-- Mobile / tablet: search + quick chips + filter sheet --}}
                <div class="lg:hidden mb-6 space-y-4" x-data="{ sheet: false }" x-effect="document.documentElement.classList.toggle('overflow-hidden', sheet)">
                    <div class="flex items-center gap-2">
                        <button type="button" @click="sheet = true"
                                class="inline-flex h-11 items-center gap-2 border-2 border-ink bg-bone px-3 font-display text-xs font-black uppercase tracking-wider">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round"><path d="M4 6h16M7 12h10M10 18h4"/></svg>
                            {{ __('Filters & search') }}
                            @if($activeFilters)
                                <span class="inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-fire px-1.5 text-[11px] font-black text-bone">{{ $activeFilters }}</span>
                            @endif
                        </button>
                        <form method="GET" class="ml-auto">
                            @foreach(request()->except(['sort','page']) as $k => $v)
                                <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                            @endforeach
                            <select name="sort" onchange="this.form.submit()" aria-label="{{ __('Sort') }}"
                                    class="h-11 border-2 border-ink bg-bone px-3 font-display uppercase text-xs">
                                @foreach($sortOptions as $key => $label)
                                    <option value="{{ $key }}" @selected($sort === $key)>{{ __('Sort') }}: {{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>

                    <div class="text-sm text-ink/60">{{ __('Showing :count treats', ['count' => $products->total()]) }}</div>

                    {{-- Bottom sheet --}}
                    <template x-teleport="body">
                        <div x-show="sheet" x-cloak class="fixed inset-0 z-[110] lg:hidden" @keydown.escape.window="sheet = false">
                            <div class="absolute inset-0 bg-ink/70" x-show="sheet" x-transition.opacity @click="sheet = false"></div>
                            <form method="GET" action="{{ route('products.index') }}" @submit="$el.querySelectorAll('input').forEach(i => { if (i.value === '') i.disabled = true })"
                                  x-show="sheet" x-transition:enter="transition transform ease-out duration-200" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
                                  x-transition:leave="transition transform ease-in duration-150" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
                                  class="absolute inset-x-0 bottom-0 max-h-[85vh] overflow-y-auto border-t-2 border-ink bg-bone paper"
                                  role="dialog" aria-modal="true" aria-label="{{ __('Filters') }}">
                                @if($sort !== 'featured')<input type="hidden" name="sort" value="{{ $sort }}">@endif

                                <div class="sticky top-0 z-10 flex items-center justify-between border-b-2 border-ink bg-bone px-5 py-4">
                                    <span class="font-display text-xl font-black uppercase">{{ __('Filters') }}</span>
                                    <button type="button" @click="sheet = false" class="inline-flex h-9 w-9 items-center justify-center border-2 border-ink text-xl leading-none" aria-label="{{ __('Close') }}">&times;</button>
                                </div>

                                <div class="px-5 py-5 space-y-6">
                                    <div>
                                        <label for="sheet-q" class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">{{ __('Search') }}</label>
                                        <div class="relative mt-3">
                                            <input id="sheet-q" type="search" name="q" value="{{ request('q') }}" placeholder="{{ __('Find a treat…') }}" enterkeyhint="search"
                                                   class="w-full border-2 border-ink bg-bone pl-11 pr-3 py-3 font-display uppercase placeholder:text-ink/40 focus:outline-none focus:ring-2 focus:ring-fire/50">
                                            <span class="pointer-events-none absolute left-0 top-0 h-full w-11 inline-flex items-center justify-center text-ink">
                                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="m20 20-3-3"/></svg>
                                            </span>
                                        </div>
                                    </div>

                                    <fieldset>
                                        <legend class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">{{ __('Type') }}</legend>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <label class="cursor-pointer">
                                                <input type="radio" name="type" value="" class="peer sr-only" @checked(!$activeType)>
                                                <span class="inline-flex h-10 items-center border-2 border-ink bg-bone px-3 font-display text-xs font-bold uppercase tracking-wider peer-checked:bg-fire peer-checked:text-bone">{{ __('All types') }}</span>
                                            </label>
                                            @foreach($types as $key => $label)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="type" value="{{ $key }}" class="peer sr-only" @checked($activeType === $key)>
                                                    <span class="inline-flex h-10 items-center border-2 border-ink bg-bone px-3 font-display text-xs font-bold uppercase tracking-wider peer-checked:bg-fire peer-checked:text-bone">{{ __($label) }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </fieldset>

                                    <fieldset>
                                        <legend class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">{{ __('Animal') }}</legend>
                                        <div class="mt-3 flex flex-wrap gap-2">
                                            <label class="cursor-pointer">
                                                <input type="radio" name="category" value="" class="peer sr-only" @checked(!$activeCategory)>
                                                <span class="inline-flex h-10 items-center border-2 border-ink bg-bone px-3 font-display text-xs font-bold uppercase tracking-wider peer-checked:bg-grass">{{ __('All animals') }}</span>
                                            </label>
                                            @foreach($categories as $cat)
                                                <label class="cursor-pointer">
                                                    <input type="radio" name="category" value="{{ $cat->slug }}" class="peer sr-only" @checked($activeCategory && $activeCategory->id === $cat->id)>
                                                    <span class="inline-flex h-10 items-center border-2 border-ink bg-bone px-3 font-display text-xs font-bold uppercase tracking-wider peer-checked:bg-grass">{{ $cat->t('name') }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </fieldset>
                                </div>

                                <div class="sticky bottom-0 flex gap-3 border-t-2 border-ink bg-bone px-5 py-4">
                                    <a href="{{ route('products.index') }}" class="btn-rough is-bone is-sm flex-1 justify-center">{{ __('Clear all') }}</a>
                                    <button type="submit" class="btn-rough is-fire is-sm flex-1 justify-center">{{ __('Apply') }}</button>
                                </div>
                            </form>
                        </div>
                    </template>
                </div>

                {{-- Desktop: count + sort --}}
                <form method="GET" class="hidden lg:flex mb-6 flex-wrap items-center gap-3">
                    @foreach(request()->except(['sort','page']) as $k => $v)
                        <input type="hidden" name="{{ $k }}" value="{{ $v }}">
                    @endforeach
                    <span class="text-sm text-ink/60">{{ __('Showing :count treats', ['count' => $products->total()]) }}</span>
                    <select name="sort" onchange="this.form.submit()"
                            class="ml-auto border-2 border-ink bg-bone px-3 py-2 font-display uppercase text-xs">
                        @foreach($sortOptions as $key => $label)
                            <option value="{{ $key }}" @selected($sort === $key)>{{ __('Sort') }}: {{ $label }}</option>
                        @endforeach
                    </select>
                </form>

                @if($products->count() === 0)
                    <div class="brush-card bg-bone p-10 text-center">
                        <div class="font-display text-2xl font-black uppercase">{{ __('No treats match.') }}</div>
                        <p class="mt-2 text-ink/60">{{ __('Try clearing your filters and dive back in.') }}</p>
                        <x-site.rough-button class="mt-5" href="{{ route('products.index') }}" variant="fire">{{ __('Reset') }}</x-site.rough-button>
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
