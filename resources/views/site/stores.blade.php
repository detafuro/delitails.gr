@php $title = 'Stores — '.($site['site_name'] ?? config('app.name')); @endphp
<x-layout title="{{ $title }}" description="Find a stockist near you. We are in cities, towns, and corner shops worth knowing.">
    <section class="bg-grass paper">
        <div class="mx-auto max-w-4xl px-4 md:px-6 py-16 md:py-20 text-center">
            <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">Stockists</div>
            <h1 class="mt-3 font-display text-5xl md:text-7xl font-black uppercase leading-[0.9]">
                Find us <span class="text-fire">in the wild</span>
            </h1>
            <form method="GET" class="mt-8 mx-auto flex flex-col sm:flex-row gap-3 max-w-xl">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="City, postcode or store name…"
                       class="flex-1 border-2 border-ink bg-bone px-4 py-3 font-display uppercase placeholder:text-ink/40 focus:outline-none">
                <x-site.rough-button variant="fire">Find</x-site.rough-button>
            </form>
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-grass"></div>
        </div>
    </section>

    <section class="bg-bone py-16 md:py-20">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            @if($stores->count() === 0)
                <div class="brush-card p-10 text-center">
                    <div class="font-display text-2xl font-black uppercase">No stockists match.</div>
                    <p class="mt-2 text-ink/60">Try a different city — or order online, the dogs won't mind.</p>
                    <x-site.rough-button class="mt-5" href="{{ route('products.index') }}" variant="fire">Shop online</x-site.rough-button>
                </div>
            @else
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                    @foreach($stores as $store)
                        <div class="brush-card bg-bone p-5 {{ $loop->iteration % 3 === 0 ? 'rotate-tilt-2' : '' }}">
                            <div class="flex items-center justify-between">
                                <span class="ribbon text-[10px]">{{ $store->city }}</span>
                                @if($store->postcode)<span class="text-xs uppercase tracking-widest text-ink/50">{{ $store->postcode }}</span>@endif
                            </div>
                            <h3 class="mt-3 font-display text-2xl font-black uppercase">{{ $store->name }}</h3>
                            <p class="mt-2 text-ink/75">{{ $store->address }}</p>
                            @if($store->phone)<p class="mt-1 text-sm">📞 {{ $store->phone }}</p>@endif
                            @if($store->email)<p class="mt-0.5 text-sm">✉ {{ $store->email }}</p>@endif

                            @if($store->opening_hours)
                                <div class="mt-3 border-t-2 border-dashed border-ink/30 pt-3">
                                    <div class="text-xs uppercase tracking-widest text-ink/60 mb-1">Opening hours</div>
                                    <pre class="font-sans text-sm whitespace-pre-wrap text-ink/80">{{ $store->opening_hours }}</pre>
                                </div>
                            @endif

                            <div class="mt-4 flex flex-wrap gap-2">
                                @if($store->map_link)
                                    <a href="{{ $store->map_link }}" target="_blank" rel="noopener" class="btn-rough is-bone is-sm">View map</a>
                                @endif
                                @if($store->phone)
                                    <a href="tel:{{ preg_replace('/[^+\d]/','',$store->phone) }}" class="btn-rough is-ghost is-sm">Call</a>
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
