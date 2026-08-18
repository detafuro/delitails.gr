@php $title = __('Stores').' — '.($site['site_name'] ?? config('app.name')); @endphp
<x-layout title="{{ $title }}" description="{{ __('Find a stockist near you. We are in cities, towns, and corner shops worth knowing.') }}">
    <section class="bg-grass paper">
        <div class="mx-auto max-w-4xl px-4 md:px-6 py-10 md:py-14 text-center">
            <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">{{ __('Stockists') }}</div>
            <h1 class="mt-3 font-display text-5xl md:text-7xl font-black uppercase leading-[0.9]">
                {!! __('Find us <span class="text-fire">in the wild</span>') !!}
            </h1>
            <form method="GET" class="mt-8 mx-auto flex flex-col sm:flex-row gap-3 max-w-xl">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('City, postcode or store name…') }}"
                       class="flex-1 border-2 border-ink bg-bone px-4 py-3 font-display uppercase placeholder:text-ink/40 focus:outline-none">
                <x-site.rough-button variant="fire">{{ __('Find') }}</x-site.rough-button>
            </form>
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-grass"></div>
        </div>
    </section>

    <section class="bg-bone pt-20 md:pt-28 pb-14 md:pb-20">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            @if($stores->count() === 0)
                <div class="brush-card p-10 text-center">
                    <div class="font-display text-2xl font-black uppercase">{{ __('No stockists match.') }}</div>
                    <p class="mt-2 text-ink/60">{{ __("Try a different city — or order online, the dogs won't mind.") }}</p>
                    <x-site.rough-button class="mt-5" href="{{ route('products.index') }}" variant="fire">{{ __('Shop online') }}</x-site.rough-button>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($stores as $store)
                        <div class="brush-card bg-bone p-5">
                            <div class="flex items-center justify-between">
                                <span class="ribbon bg-ink text-[10px]">{{ $store->city }}</span>
                                @if($store->postcode)<span class="text-xs uppercase tracking-widest text-ink/50">{{ $store->postcode }}</span>@endif
                            </div>
                            <h3 class="mt-3 font-display text-2xl font-black uppercase">{{ $store->name }}</h3>
                            <p class="mt-2 flex items-start gap-1.5 text-ink/75">
                                <svg class="mt-0.5 h-4 w-4 shrink-0 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2c-3.866 0-7 3.134-7 7 0 4.97 7 13 7 13s7-8.03 7-13c0-3.866-3.134-7-7-7Z"/><circle cx="12" cy="9" r="2.5"/></svg>
                                <span>{{ $store->address }}</span>
                            </p>
                            @if($store->phone)
                                <p class="mt-1 flex items-center gap-1.5 text-sm">
                                    <svg class="h-4 w-4 shrink-0 text-ink" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92Z"/></svg>
                                    <span>{{ $store->phone }}</span>
                                </p>
                            @endif
                            @if($store->email)<p class="mt-0.5 text-sm">✉ {{ $store->email }}</p>@endif

                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($store->map_link)
                                    <a href="{{ $store->map_link }}" target="_blank" rel="noopener" class="btn-rough is-grass is-sm">{{ __('View map') }}</a>
                                @endif
                                @if($store->phone)
                                    <a href="tel:{{ preg_replace('/[^+\d]/','',$store->phone) }}" class="btn-rough is-fire is-sm">{{ __('Call') }}</a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
                <x-site.pagination :paginator="$stores"/>
            @endif
        </div>
    </section>
</x-layout>
