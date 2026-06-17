@php $title = 'About — '.($site['site_name'] ?? config('app.name')); @endphp
<x-layout title="{{ $title }}" description="The story behind the loudest pet treats around.">
    <section class="bg-grass paper">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-20 md:py-28 grid lg:grid-cols-5 gap-10 items-end">
            <div class="lg:col-span-3">
                <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">About us</div>
                <h1 class="mt-3 font-display text-5xl md:text-7xl lg:text-8xl font-black uppercase leading-[0.9]">
                    You know that dogs love it, <span class="text-fire">'cause tails</span> show it!
                </h1>
            </div>
            <div class="lg:col-span-2">
                <p class="font-editorial italic text-2xl md:text-3xl text-ink/80 leading-relaxed">
                    A small kitchen, a stubborn dog, and a strong opinion about what pets deserve. That's the whole pitch.
                </p>
            </div>
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-grass"></div>
        </div>
    </section>

    {{-- Story block 1 --}}
    <section class="bg-bone py-20 md:py-28">
        <div class="mx-auto max-w-7xl px-4 md:px-6 grid lg:grid-cols-2 gap-12 items-center">
            <div class="relative order-2 lg:order-1">
                <div class="absolute -inset-4 -rotate-[3deg] bg-fire/15 border-2 border-ink"></div>
                <div class="relative aspect-square border-2 border-ink bg-ink overflow-hidden">
                    <div class="absolute inset-0 halftone opacity-60"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="text-center">
                            <div class="font-display text-6xl md:text-8xl font-black uppercase text-bone">2018</div>
                            <div class="text-bone/70 font-display text-xs uppercase tracking-[0.4em] mt-2">Year zero</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="order-1 lg:order-2">
                <div class="text-fire text-sm font-bold uppercase tracking-[0.3em]">What we do</div>
                <h2 class="mt-3 font-display text-4xl md:text-5xl font-black uppercase leading-tight">
                    Just carefully crafted treats you can trust.
                </h2>
                <p class="mt-5 font-editorial italic text-xl md:text-2xl text-ink/80 leading-relaxed">
                    We believe dogs are more than pets—they are family. And just like every member of the family, they deserve the very best.
                </p>
                <p class="mt-4 text-ink/75 leading-relaxed">
                    That belief is what inspired Delitails.
                </p>
                <p class="mt-4 text-ink/75 leading-relaxed">
                    We create premium single-protein chews, training treats, and natural sausages made with simplicity, honesty, and quality at the heart of everything we do.
                </p>
                <div class="mt-4 text-ink/75 leading-relaxed space-y-2">
                    <p>No unnecessary fillers.</p>
                    <p>No confusing ingredient lists.</p>
                    <p>Just carefully crafted treats you can trust.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Story block 2 --}}
    <x-site.torn-section bg="ink" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6 grid lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">Chapter two</div>
                <h2 class="mt-3 font-display text-4xl md:text-5xl font-black uppercase leading-tight text-bone">
                    Loud, on purpose
                </h2>
                <p class="mt-5 font-editorial italic text-xl md:text-2xl text-bone/80 leading-relaxed">
                    Pet food is full of beige labels and bland promises. We went the other way. Bright, brash, unmissable — because the treats inside are the real deal and they deserved a wrapper to match.
                </p>
                <p class="mt-4 text-bone/70 leading-relaxed">
                    Every batch is made in small runs. Every ingredient earns its spot. Every recipe is tested by an army of unimpressed pets and the humans who love them.
                </p>
            </div>
            <div class="relative">
                <div class="absolute -inset-4 rotate-[3deg] bg-grass/20 border-2 border-bone"></div>
                <div class="relative aspect-square border-2 border-bone bg-fire overflow-hidden flex items-center justify-center">
                    <div class="font-editorial italic text-bone text-3xl md:text-5xl text-center px-6 leading-tight">
                        "Better treats. <br>Louder packs."
                    </div>
                </div>
            </div>
        </div>
    </x-site.torn-section>

    {{-- Values --}}
    <x-site.torn-section bg="grass" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6">
            <div class="text-center mb-12">
                <div class="text-ink/70 text-sm font-bold uppercase tracking-[0.3em]">What we stand on</div>
                <h2 class="mt-2 font-display text-4xl md:text-6xl font-black uppercase">Our four bones</h2>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                @php
                    $values = [
                        ['Real food', 'No fillers, no fluff. If it does not earn its place, it does not go in.'],
                        ['Small batch', 'We bake in runs, not factories. Fresher, better, louder.'],
                        ['Pets first', 'Every recipe is approved by the toughest critics on four legs.'],
                        ['Local roots', 'Made close to home, sourced from people we shake hands with.'],
                    ];
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
            <h2 class="font-display text-4xl md:text-6xl font-black uppercase leading-[0.95]">Come say hi.<br>Bring the dog.</h2>
            <p class="mt-5 font-editorial italic text-2xl text-bone/80">We love hearing what your pets think. Even when they hate us.</p>
            <div class="mt-7 flex flex-wrap justify-center gap-3">
                <x-site.rough-button href="{{ route('contact') }}" variant="ink">Contact us</x-site.rough-button>
                <x-site.rough-button href="{{ route('products.index') }}" variant="bone">Shop the pack</x-site.rough-button>
            </div>
        </div>
    </x-site.torn-section>
</x-layout>
