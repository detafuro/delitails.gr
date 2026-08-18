@props(['post', 'horizontal' => false])
@if($horizontal)
<a href="{{ route('blog.show', $post->slug) }}" class="group flex flex-col sm:flex-row brush-card bg-bone overflow-hidden">
    <div class="relative shrink-0 sm:w-64 md:w-80 sm:min-h-56 aspect-[4/3] sm:aspect-auto overflow-hidden border-b-2 sm:border-b-0 sm:border-r-2 border-ink">
        @if($post->featured_image)
            <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->t('title') }}"
                 class="absolute inset-0 h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="absolute inset-0 flex items-center justify-center halftone-fire">
                <span class="font-editorial text-2xl text-ink/60">{{ Str::words($post->t('title'), 3, '') }}</span>
            </div>
        @endif
        @if($post->category)
            <span class="absolute left-3 top-3 ribbon text-[10px]">{{ $post->category->t('name') }}</span>
        @endif
    </div>
    <div class="flex-1 p-5 md:p-6 flex flex-col justify-center">
        <div class="text-xs uppercase tracking-wider text-ink/55">
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('j M Y') }}</time> @if($post->author) · {{ $post->author }} @endif
        </div>
        <h3 class="mt-1 font-display text-xl md:text-2xl font-black uppercase leading-tight text-ink">{{ $post->t('title') }}</h3>
        @if($post->excerpt)
            <p class="mt-2 text-sm text-ink/70">{{ Str::limit($post->t('excerpt'), 180) }}</p>
        @endif
        <div class="mt-3 inline-flex items-center gap-1 font-display text-sm font-extrabold uppercase tracking-wider text-fire">
            {{ __('Read it') }} <span class="wiggle inline-block">→</span>
        </div>
    </div>
</a>
@else
<a href="{{ route('blog.show', $post->slug) }}" class="group flex h-full flex-col brush-card bg-bone overflow-hidden">
    <div class="relative shrink-0 aspect-[4/3] overflow-hidden border-b-2 border-ink">
        @if($post->featured_image)
            <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->t('title') }}"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center halftone-fire">
                <span class="font-editorial text-2xl text-ink/60">{{ Str::words($post->t('title'), 3, '') }}</span>
            </div>
        @endif
        @if($post->category)
            <span class="absolute left-3 top-3 ribbon text-[10px]">{{ $post->category->t('name') }}</span>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-5">
        <div class="text-xs uppercase tracking-wider text-ink/55">
            <time datetime="{{ $post->published_at?->toDateString() }}">{{ $post->published_at?->translatedFormat('j M Y') }}</time> @if($post->author) · {{ $post->author }} @endif
        </div>
        <h3 class="mt-1 font-display text-xl md:text-2xl font-black uppercase leading-tight text-ink">{{ $post->t('title') }}</h3>
        @if($post->excerpt)
            <p class="mt-2 text-sm text-ink/70">{{ Str::limit($post->t('excerpt'), 120) }}</p>
        @endif
        <div class="mt-auto pt-3 inline-flex items-center gap-1 font-display text-sm font-extrabold uppercase tracking-wider text-fire">
            {{ __('Read it') }} <span class="wiggle inline-block">→</span>
        </div>
    </div>
</a>
@endif
