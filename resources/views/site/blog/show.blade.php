@php
    $title = ($post->seo_title ?: $post->title).' — '.($site['site_name'] ?? config('app.name'));
    $description = $post->seo_description ?: $post->excerpt ?: ($site['seo_default_description'] ?? '');
@endphp
<x-layout title="{{ $title }}" description="{{ $description }}">
    <article class="bg-bone">
        {{-- Hero --}}
        <header class="relative bg-grass paper">
            <div class="mx-auto max-w-4xl px-4 md:px-6 py-16 md:py-24">
                <nav class="text-xs uppercase tracking-widest text-ink/60 mb-5">
                    <a href="{{ route('home') }}" class="hover:text-fire">Home</a> /
                    <a href="{{ route('blog.index') }}" class="hover:text-fire">Blog</a>
                    @if($post->category) / <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-fire">{{ $post->category->name }}</a> @endif
                </nav>
                <h1 class="font-display text-4xl md:text-6xl font-black uppercase leading-[0.95]">{{ $post->title }}</h1>
                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                    @if($post->author) <span class="font-display font-bold uppercase">{{ $post->author }}</span> @endif
                    @if($post->published_at) <span class="text-ink/60">· {{ $post->published_at->format('M j, Y') }}</span> @endif
                    @if($post->category) <span class="ribbon text-[10px]">{{ $post->category->name }}</span> @endif
                </div>
            </div>
            <div aria-hidden="true" class="relative">
                <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-grass"></div>
            </div>
        </header>

        @if($post->featured_image)
            <div class="mx-auto max-w-5xl px-4 md:px-6 mt-12">
                <div class="aspect-[16/9] border-2 border-ink bg-bone overflow-hidden sticker-shadow">
                    <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}" class="h-full w-full object-cover">
                </div>
            </div>
        @endif

        <div class="mx-auto max-w-3xl px-4 md:px-6 py-12 md:py-16">
            @if($post->excerpt)
                <p class="font-editorial italic text-2xl md:text-3xl text-ink/80 leading-relaxed border-l-4 border-fire pl-5">{{ $post->excerpt }}</p>
            @endif
            <div class="mt-6 text-ink/85 leading-relaxed text-xl quill-content">
                {!! $post->body !!}
            </div>

            @if(is_array($post->tags) && count($post->tags))
                <div class="mt-10 flex flex-wrap gap-2">
                    @foreach($post->tags as $tag)
                        <span class="border-2 border-ink px-3 py-1 text-xs font-bold uppercase tracking-wider">#{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
    </article>

    @if($related->count())
        <x-site.torn-section bg="ink" :top="true">
            <div class="mx-auto max-w-7xl px-4 md:px-6">
                <h2 class="font-display text-3xl md:text-5xl font-black uppercase">Keep reading</h2>
                <div class="mt-8 grid md:grid-cols-3 gap-6">
                    @foreach($related as $r)
                        <x-site.blog-card :post="$r"/>
                    @endforeach
                </div>
            </div>
        </x-site.torn-section>
    @endif
</x-layout>
