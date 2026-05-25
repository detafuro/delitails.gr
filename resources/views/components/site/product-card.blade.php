@props(['product'])
@php
    // Gallery mirrors the product page: featured image first, then gallery images.
    $gallery = [];
    if ($product->featured_image) $gallery[] = $product->featured_image;
    foreach ($product->images as $img) $gallery[] = $img->path;
    $primary = $gallery[0] ?? null;
    $hover = $gallery[1] ?? null; // 2nd gallery image, revealed on hover
@endphp
<a href="{{ route('products.show', $product->slug) }}"
   class="group relative block brush-card bg-bone text-ink p-3 transition">
    <div class="relative aspect-square overflow-hidden border-2 border-ink/80 bg-bone-dark">
        @if($primary)
            <img src="{{ asset('storage/'.$primary) }}" alt="{{ $product->title }}"
                 class="absolute inset-0 h-full w-full object-cover transition duration-300 {{ $hover ? 'group-hover:opacity-0' : 'group-hover:scale-105' }}">
            @if($hover)
                <img src="{{ asset('storage/'.$hover) }}" alt="" aria-hidden="true" loading="lazy"
                     class="absolute inset-0 h-full w-full object-cover opacity-0 transition duration-300 group-hover:opacity-100 group-hover:scale-105">
            @endif
        @else
            <div class="flex h-full w-full items-center justify-center halftone-grass">
                <span class="font-display text-3xl font-black uppercase text-ink/40">{{ Str::words($product->title, 2, '') }}</span>
            </div>
        @endif

        @if($product->is_featured)
            <span class="absolute right-3 top-3 z-10 inline-flex items-center gap-1 border-2 border-ink bg-grass text-ink px-2 py-0.5 text-[10px] font-bold uppercase">★ Fave</span>
        @endif
        @if($product->type_label)
            <span class="absolute left-3 bottom-3 z-10 inline-flex items-center gap-1 border-2 border-ink bg-bone text-ink px-2 py-0.5 text-[10px] font-bold uppercase">{{ $product->type_label }}</span>
        @endif
    </div>
    <div class="mt-3 flex items-end justify-between gap-2">
        <div>
            <h3 class="font-display text-lg font-extrabold uppercase leading-tight">{{ $product->title }}</h3>
            <div class="text-xs uppercase tracking-wider text-ink/55">{{ $product->category?->name }}</div>
        </div>
        <span class="inline-flex items-center gap-1 font-display text-xs font-extrabold uppercase tracking-wider text-fire">
            View <span class="wiggle inline-block">→</span>
        </span>
    </div>
</a>
