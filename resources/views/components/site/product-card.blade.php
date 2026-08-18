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
   class="group relative flex h-full flex-col brush-card bg-bone text-ink p-3 transition">
    <div class="relative shrink-0 aspect-square overflow-hidden border-2 border-ink/80 bg-bone-dark">
        @if($primary)
            <x-site.img :src="$primary" :alt="$product->t('title')" sizes="(min-width: 1280px) 25vw, (min-width: 768px) 33vw, (min-width: 640px) 50vw, 85vw" :widths="[320, 480, 768, 1024]"
                 class="absolute inset-0 h-full w-full object-cover transition duration-300 {{ $hover ? 'group-hover:opacity-0' : 'group-hover:scale-105' }}"/>
            @if($hover)
                <x-site.img :src="$hover" alt="" aria-hidden="true" sizes="(min-width: 1280px) 25vw, (min-width: 768px) 33vw, (min-width: 640px) 50vw, 85vw" :widths="[320, 480, 768, 1024]"
                     class="absolute inset-0 h-full w-full object-cover opacity-0 transition duration-300 group-hover:opacity-100 group-hover:scale-105"/>
            @endif
        @else
            <div class="flex h-full w-full items-center justify-center halftone-grass">
                <span class="font-display text-3xl font-black uppercase text-ink/40">{{ Str::words($product->t('title'), 2, '') }}</span>
            </div>
        @endif

        @if($product->type_label)
            <span class="absolute left-3 top-3 z-10 inline-flex items-center gap-1 {{ $product->type === \App\Models\Product::TYPE_TRAINING_TREATS ? 'bg-fire text-bone' : 'bg-grass text-ink' }} px-2 py-1 text-[10px] font-bold uppercase tracking-wider">{{ __($product->type_label) }}</span>
        @endif
    </div>
    <div class="mt-3 flex flex-1 items-start justify-between gap-2">
        <div class="min-w-0">
            <h3 class="font-display text-lg font-extrabold uppercase leading-tight">{{ $product->t('title') }}</h3>
            <div class="text-xs uppercase tracking-wider text-ink/55">{{ $product->category?->t('name') }}</div>
        </div>
        @if($product->weight)
            <span class="mt-0.5 inline-flex shrink-0 items-center gap-1 whitespace-nowrap text-xs font-bold uppercase tracking-wider text-ink/70" title="{{ __('Net weight') }}">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="h-3.5 w-3.5" aria-hidden="true">
                    <path d="M12 3a4 4 0 0 0-4 4h8a4 4 0 0 0-4-4z"/>
                    <path d="M4 7h16l1.5 12a2 2 0 0 1-2 2h-15a2 2 0 0 1-2-2L4 7z"/>
                    <path d="M9 14a3 3 0 0 0 6 0"/>
                </svg>
                <span class="sr-only">{{ __('Net weight') }}:</span>{{ $product->t('weight') }}
            </span>
        @endif
    </div>
</a>
