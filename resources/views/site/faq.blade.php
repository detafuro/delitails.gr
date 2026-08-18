@php $title = __('FAQ').' — '.($site['site_name'] ?? config('app.name')); @endphp
<x-layout title="{{ $title }}" description="{{ __('Answers to the questions our pack asks most.') }}">
    <section class="bg-ink text-bone paper">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-10 md:py-14">
            <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">{{ __('Asked & answered') }}</div>
            <h1 class="mt-3 font-display text-5xl md:text-7xl font-black uppercase leading-[0.9]">
                {!! __('Got <span class="text-fire">questions?</span><br>We got it.') !!}
            </h1>
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-ink"></div>
        </div>
    </section>

    <section class="bg-bone pt-20 md:pt-28 pb-14 md:pb-20">
        <div class="mx-auto max-w-7xl px-4 md:px-6 space-y-12">
            @if($groups->isEmpty() && $orphans->isEmpty())
                <div class="brush-card p-10 text-center">
                    <div class="font-display text-2xl font-black uppercase">{{ __('Nothing matches') }}</div>
                    <p class="mt-2 text-ink/60">{{ __('Try a different keyword, or drop us a line.') }}</p>
                    <x-site.rough-button class="mt-5" href="{{ route('contact') }}" variant="fire">{{ __('Ask us directly') }}</x-site.rough-button>
                </div>
            @endif

            @foreach($groups as $g)
                <section>
                    <div class="flex items-center gap-3 mb-5">
                        <span class="inline-block h-3 w-3 bg-fire"></span>
                        <h2 class="font-display text-3xl md:text-4xl font-black uppercase">{{ $g->t('name') }}</h2>
                    </div>
                    <x-site.faq-accordion :faqs="$g->faqs"/>
                </section>
            @endforeach

            @if($orphans->count())
                <section>
                    <div class="flex items-center gap-3 mb-5">
                        <span class="inline-block h-3 w-3 bg-fire"></span>
                        <h2 class="font-display text-3xl md:text-4xl font-black uppercase">{{ __('More questions') }}</h2>
                    </div>
                    <x-site.faq-accordion :faqs="$orphans"/>
                </section>
            @endif
        </div>
    </section>

    <x-site.torn-section bg="ink" :top="true">
        <div class="mx-auto max-w-7xl px-4 md:px-6 text-center text-bone">
            <h2 class="font-display text-4xl md:text-6xl font-black uppercase">{{ __('Still curious?') }}</h2>
            <p class="mt-4 font-editorial italic text-bone/80 text-xl">{{ __('If we missed your question, throw it at us. We answer every one.') }}</p>
            <div class="mt-6">
                <x-site.rough-button href="{{ route('contact') }}" variant="fire">{{ __('Ask the team') }}</x-site.rough-button>
            </div>
        </div>
    </x-site.torn-section>
</x-layout>
