@php
    $title = \App\Support\Seo::title(__('About'), __('A brand created by a producer'));
    // Admin-managed copy (Admin → About page): Greek visitors get the *_el variant when filled, else English, else the built-in text.
    $pick = fn (string $key, string $default = '') => ((app()->getLocale() === 'el' ? ($site[$key.'_el'] ?? null) : null) ?: ($site[$key] ?? null)) ?: $default;
    $paragraphs = fn (string $text) => array_values(array_filter(array_map('trim', preg_split("/(\r?\n){2,}/", $text))));
    $whatBody = $pick('about_what_body', __('That belief is what inspired Delitails.')."\n\n".__('We create premium single-protein chews, training treats, and natural sausages made with simplicity, honesty, and quality at the heart of everything we do.')."\n\n".__('No unnecessary fillers. No confusing ingredient lists. Just carefully crafted treats you can trust.'));
    $philosophyBody = $pick('about_philosophy_body', __('We are not just another brand—we are a brand created by a producer. Every Delitails product is crafted by us, giving us full control over quality, consistency, and sourcing. This means complete transparency in everything we offer, from the first ingredient to the final treat.'));
@endphp
<x-layout title="{{ $title }}" description="{{ __('The story behind the loudest pet treats around.') }}">
    <section class="bg-grass paper">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-12 md:py-16 grid lg:grid-cols-5 gap-10 items-end">
            <div class="lg:col-span-3">
                <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">{{ __('About us') }}</div>
                <h1 class="mt-3 font-display text-5xl md:text-7xl lg:text-8xl font-black uppercase leading-[0.9]">
                    {!! __('Not just <span class="text-fire">another</span> brand') !!}
                </h1>
            </div>
            <div class="lg:col-span-2">
                <p class="font-editorial italic text-2xl md:text-3xl text-ink/80 leading-relaxed">
                    {{ __('What makes Delitails different is simple: we are not just another brand—we are a brand created by a producer.') }}
                </p>
            </div>
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-grass"></div>
        </div>
    </section>

    {{-- Story block 1 --}}
    <section class="bg-bone pt-20 md:pt-28 pb-24 md:pb-28">
        <div class="mx-auto max-w-7xl px-4 md:px-6 grid lg:grid-cols-2 gap-12 items-center">
            <div class="relative order-2 lg:order-1">
                <div class="absolute -inset-4 -rotate-[3deg] bg-fire/15 border-2 border-ink"></div>
                <div class="relative aspect-square border-2 border-ink overflow-hidden">
                    <x-site.img :src="$site['about_what_image'] ?? null" fallback="our-history.png" :alt="$pick('about_what_label', __('What we do'))" sizes="(min-width: 1024px) 50vw, 100vw" class="h-full w-full object-cover"/>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <div class="text-fire text-sm font-bold uppercase tracking-[0.3em]">{{ $pick('about_what_label', __('What we do')) }}</div>
                <h2 class="mt-3 font-display text-4xl md:text-5xl font-black uppercase leading-tight">
                    {{ $pick('about_what_heading', __('Just carefully crafted treats you can trust.')) }}
                </h2>
                <p class="mt-5 font-editorial italic text-xl md:text-2xl text-ink/80 leading-relaxed">
                    {{ $pick('about_what_lead', __('We believe dogs are more than pets—they are family. And just like every member of the family, they deserve the very best.')) }}
                </p>
                @foreach($paragraphs($whatBody) as $paragraph)
                    <p class="mt-4 text-ink/75 leading-relaxed whitespace-pre-line">{{ $paragraph }}</p>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Story block 2 --}}
    <x-site.torn-section bg="ink" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">{{ $pick('about_philosophy_label', __('Our philosophy')) }}</div>
                <h2 class="mt-3 font-display text-4xl md:text-5xl font-black uppercase leading-tight text-bone">
                    {{ $pick('about_philosophy_heading', __('See the difference in every wag.')) }}
                </h2>
                <p class="mt-5 font-editorial italic text-xl md:text-2xl text-bone/80 leading-relaxed">
                    {{ $pick('about_philosophy_lead', __('Our philosophy is simple: better treats lead to happier, healthier dogs—and you can see the difference in every wag.')) }}
                </p>
                @foreach($paragraphs($philosophyBody) as $paragraph)
                    <p class="mt-4 text-bone/70 leading-relaxed whitespace-pre-line">{{ $paragraph }}</p>
                @endforeach
            </div>
            <div class="relative">
                <div class="absolute -inset-4 rotate-[3deg] bg-grass/20 border-2 border-bone"></div>
                <div class="relative aspect-square border-2 border-bone overflow-hidden">
                    <x-site.img :src="$site['about_philosophy_image'] ?? null" fallback="our-story-feat.jpg" :alt="$pick('about_philosophy_label', __('Our philosophy'))" sizes="(min-width: 1024px) 50vw, 100vw" class="h-full w-full object-cover"/>
                </div>
            </div>
        </div>
    </x-site.torn-section>

    {{-- Values --}}
    <x-site.torn-section bg="grass" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="text-center mb-12">
                <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">{{ $pick('about_bones_label', __('What we stand on')) }}</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">{{ $pick('about_bones_heading', __('Our four bones')) }}</h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $defaults = [
                        [__('Real food'), __('No fillers, no fluff. If it does not earn its place, it does not go in.')],
                        [__('Small batch'), __('We bake in runs, not factories. Fresher, better, louder.')],
                        [__('Pets first'), __('Every recipe is approved by the toughest critics on four legs.')],
                        [__('Local roots'), __('Made close to home, sourced from people we shake hands with.')],
                    ];
                    $values = [];
                    foreach ($defaults as $i => [$dh, $db]) {
                        $values[] = [$pick('about_bone_'.($i+1).'_title', $dh), $pick('about_bone_'.($i+1).'_text', $db)];
                    }
                @endphp
                @foreach($values as $i => [$h, $b])
                    <div class="brush-card bg-bone p-6 {{ $i % 2 ? 'rotate-tilt-2' : 'rotate-tilt-1' }}">
                        <div class="font-display text-5xl font-black text-fire">0{{ $i+1 }}</div>
                        <h3 class="mt-2 font-display text-xl font-extrabold uppercase">{{ $h }}</h3>
                        <p class="mt-2 text-base text-ink/70 font-editorial italic">{{ $b }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </x-site.torn-section>

    {{-- CTA --}}
    <x-site.torn-section bg="fire" :top="true">
        <div class="mx-auto max-w-4xl px-4 md:px-6 text-center text-bone">
            <h2 class="font-display text-4xl md:text-6xl font-black uppercase leading-[0.95]">{!! __('Come say hi.<br>Bring the dog.') !!}</h2>
            <p class="mt-5 font-editorial italic text-2xl text-bone/80">{{ __('We love hearing what your pets think. Even when they hate us.') }}</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <x-site.rough-button href="{{ route('contact') }}" variant="ink">{{ __('Contact us') }}</x-site.rough-button>
                <x-site.rough-button href="{{ route('products.index') }}" variant="bone">{{ __('Shop the pack') }}</x-site.rough-button>
            </div>
        </div>
    </x-site.torn-section>
</x-layout>
