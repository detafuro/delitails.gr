@props(['post'])
<a href="{{ route('blog.show', $post->slug) }}" class="group block brush-card bg-bone overflow-hidden">
    <div class="relative aspect-[4/3] overflow-hidden border-b-2 border-ink">
        @if($post->featured_image)
            <img src="{{ asset('storage/'.$post->featured_image) }}" alt="{{ $post->title }}"
                 class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @else
            <div class="flex h-full w-full items-center justify-center halftone-fire">
                <span class="font-editorial text-2xl text-ink/60">{{ Str::words($post->title, 3, '') }}</span>
            </div>
        @endif
        @if($post->category)
            <span class="absolute left-3 top-3 ribbon text-[10px]">{{ $post->category->name }}</span>
        @endif
    </div>
    <div class="p-5">
        <div class="text-xs uppercase tracking-wider text-ink/55">
            {{ $post->published_at?->format('M j, Y') }} @if($post->author) · {{ $post->author }} @endif
        </div>
        <h3 class="mt-1 font-display text-xl md:text-2xl font-black uppercase leading-tight text-ink">{{ $post->title }}</h3>
        @if($post->excerpt)
            <p class="mt-2 text-sm text-ink/70">{{ Str::limit($post->excerpt, 120) }}</p>
        @endif
        <div class="mt-3 inline-flex items-center gap-1 font-display text-sm font-extrabold uppercase tracking-wider text-fire">
            Read it <span class="wiggle inline-block">→</span>
        </div>
    </div>
</a>
