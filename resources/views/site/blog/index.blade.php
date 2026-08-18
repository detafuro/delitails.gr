@php $title = __('Blog').' — '.($site['site_name'] ?? config('app.name')); @endphp
<x-layout title="{{ $title }}" description="{{ __('Field notes, recipes, and loud opinions from the pack.') }}">
    <section class="bg-ink text-bone paper">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-10 md:py-14">
            <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">{{ __('Bark journal') }}</div>
            <h1 class="mt-3 font-display text-5xl md:text-7xl font-black uppercase leading-[0.9]">
                {!! __('Field notes <span class="text-fire">from</span> the pack') !!}
            </h1>
            <p class="mt-4 max-w-2xl font-editorial italic text-xl text-bone/75">{{ __('Stories, recipes, opinions and the occasional rant. From our kitchen, to your kitchen, to their bowl.') }}</p>
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-ink"></div>
        </div>
    </section>

    <section class="bg-bone pt-20 md:pt-28 pb-14 md:pb-20">
        <div class="mx-auto max-w-7xl px-4 md:px-6 grid lg:grid-cols-4 gap-10">
            <aside class="lg:col-span-1 space-y-6">
                <form method="GET" class="brush-card bg-bone p-5">
                    <h2 class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">{{ __('Search') }}</h2>
                    <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('Find a post…') }}"
                           class="mt-3 w-full border-2 border-ink bg-bone px-3 py-2 font-display uppercase placeholder:text-ink/40 focus:outline-none focus:ring-2 focus:ring-fire/50">
                    <button class="btn-rough is-fire is-sm mt-3 w-full justify-center">{{ __('Search') }}</button>
                </form>

                @if($categories->count())
                    <div class="brush-card bg-bone p-5">
                        <h2 class="font-display text-xs font-bold uppercase tracking-[0.25em] text-ink/60">{{ __('Categories') }}</h2>
                        <ul class="mt-3 space-y-1">
                            <li><a href="{{ route('blog.index') }}" class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ !$activeCategory ? 'border-ink bg-grass' : '' }}">{{ __('All posts') }}</a></li>
                            @foreach($categories as $cat)
                                @php $on = $activeCategory && $activeCategory->id === $cat->id; @endphp
                                <li><a href="{{ route('blog.index', ['category' => $cat->slug]) }}" class="block border-2 border-transparent px-3 py-2 font-display uppercase tracking-wider text-sm hover:border-ink {{ $on ? 'border-ink bg-grass' : '' }}">{{ $cat->t('name') }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </aside>

            <div class="lg:col-span-3">
                @if($posts->count())
                    <div class="space-y-6">
                        @foreach($posts as $post)
                            <x-site.blog-card :post="$post" :horizontal="true"/>
                        @endforeach
                    </div>
                    <x-site.pagination :paginator="$posts"/>
                @else
                    <div class="brush-card bg-bone p-10 text-center">
                        <div class="font-display text-2xl font-black uppercase">{{ __('No posts yet.') }}</div>
                        <p class="mt-2 text-ink/60">{{ __('Stay tuned. We are writing as fast as the dogs let us.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-layout>
