@php
    $title = \App\Support\Seo::title($post->t('seo_title') ?: $post->t('title'), $post->t('seo_title') ? '' : __('Blog'));
    $description = $post->t('seo_description') ?: $post->t('excerpt') ?: \App\Support\Seo::defaultDescription();
    $ogImage = $post->featured_image ? asset('storage/'.$post->featured_image) : null;
    $jsonLd = [
        [
            '@context' => 'https://schema.org', '@type' => 'BlogPosting',
            'headline' => $post->t('title'), 'description' => $description, 'url' => url()->current(),
            'image' => $ogImage ? [$ogImage] : null,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'author' => ['@type' => $post->author ? 'Person' : 'Organization', 'name' => $post->author ?: \App\Support\Seo::siteName()],
            'publisher' => ['@type' => 'Organization', 'name' => \App\Support\Seo::siteName(), 'logo' => ['@type' => 'ImageObject', 'url' => asset('og-image.png')]],
            'mainEntityOfPage' => url()->current(),
            'inLanguage' => app()->getLocale(),
        ],
        [
            '@context' => 'https://schema.org', '@type' => 'BreadcrumbList',
            'itemListElement' => array_values(array_filter([
                ['@type' => 'ListItem', 'position' => 1, 'name' => __('Home'), 'item' => route('home')],
                ['@type' => 'ListItem', 'position' => 2, 'name' => __('Blog'), 'item' => route('blog.index')],
                $post->category ? ['@type' => 'ListItem', 'position' => 3, 'name' => $post->category->t('name'), 'item' => route('blog.index', ['category' => $post->category->slug])] : null,
                ['@type' => 'ListItem', 'position' => $post->category ? 4 : 3, 'name' => $post->t('title'), 'item' => url()->current()],
            ])),
        ],
    ];
@endphp
<x-layout title="{{ $title }}" description="{{ $description }}" :image="$ogImage" :jsonld="$jsonLd">
    <article class="bg-bone">
        {{-- Hero --}}
        <header class="relative bg-grass paper">
            <div class="mx-auto max-w-7xl px-4 md:px-6 py-10 md:py-14">
                <nav aria-label="{{ __('Breadcrumb') }}" class="text-xs uppercase tracking-widest text-ink/60 mb-5">
                    <a href="{{ route('home') }}" class="hover:text-fire">{{ __('Home') }}</a> /
                    <a href="{{ route('blog.index') }}" class="hover:text-fire">{{ __('Blog') }}</a>
                    @if($post->category) / <a href="{{ route('blog.index', ['category' => $post->category->slug]) }}" class="hover:text-fire">{{ $post->category->t('name') }}</a> @endif
                </nav>
                <h1 class="font-display text-4xl md:text-6xl font-black uppercase leading-[0.95]">{{ $post->t('title') }}</h1>
                <div class="mt-4 flex flex-wrap items-center gap-3 text-sm">
                    @if($post->author) <span class="font-display font-bold uppercase">{{ $post->author }}</span> @endif
                    @if($post->published_at) <span class="text-ink/60">· <time datetime="{{ $post->published_at->toDateString() }}">{{ $post->published_at->translatedFormat('j M Y') }}</time></span> @endif
                    @if($post->category) <span class="ribbon text-[10px]">{{ $post->category->t('name') }}</span> @endif
                </div>
            </div>
            <div aria-hidden="true" class="relative">
                <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-grass"></div>
            </div>
        </header>

        @if($post->featured_image)
            <div class="mx-auto max-w-7xl px-4 md:px-6 mt-20 md:mt-24">
                <div class="aspect-[16/9] border-2 border-ink bg-bone overflow-hidden sticker-shadow">
                    <x-site.img :src="$post->featured_image" :alt="$post->t('title')" sizes="(min-width: 1280px) 1232px, 100vw" loading="eager" fetchpriority="high" class="h-full w-full object-cover"/>
                </div>
            </div>
        @endif

        <div class="mx-auto max-w-7xl px-4 md:px-6 {{ $post->featured_image ? 'pt-12 md:pt-16' : 'pt-20 md:pt-28' }} pb-14 md:pb-20">
            @if($post->excerpt)
                <p class="font-editorial italic text-2xl md:text-3xl text-ink/80 leading-relaxed border-l-4 border-fire pl-5">{{ $post->t('excerpt') }}</p>
            @endif
            <div class="mt-6 text-ink/85 leading-relaxed text-[17px] quill-content">
                {!! $post->t('body') !!}
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
                <h2 class="font-display text-3xl md:text-5xl font-black uppercase">{{ __('Keep reading') }}</h2>
                <div class="mt-8 grid md:grid-cols-3 gap-6">
                    @foreach($related as $r)
                        <x-site.blog-card :post="$r"/>
                    @endforeach
                </div>
            </div>
        </x-site.torn-section>
    @endif
</x-layout>
