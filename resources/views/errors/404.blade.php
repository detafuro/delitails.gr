@php $title = __('Page not found').' — '.($site['site_name'] ?? config('app.name')); @endphp
<x-layout title="{{ $title }}" description="{{ __('That page wandered off. Head back to the treats.') }}">
    <section class="bg-bone">
        <div class="mx-auto max-w-3xl px-4 md:px-6 py-20 md:py-28 text-center">
            <div class="font-display text-7xl md:text-9xl font-black text-fire leading-none">404</div>
            <h1 class="mt-4 font-display text-3xl md:text-5xl font-black uppercase leading-tight">{{ __('Nothing to sniff here.') }}</h1>
            <p class="mt-4 font-editorial italic text-xl text-ink/80">{{ __('That page wandered off. Head back to the treats.') }}</p>
            <div class="mt-8 flex flex-wrap justify-center gap-3">
                <x-site.rough-button href="{{ route('home') }}" variant="fire">{{ __('Back home') }}</x-site.rough-button>
                <x-site.rough-button href="{{ route('products.index') }}" variant="grass">{{ __('See the treats') }}</x-site.rough-button>
            </div>
        </div>
    </section>
</x-layout>
