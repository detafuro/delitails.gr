@props([
    'count' => 0,
    'grid' => 'md:grid-cols-3 lg:grid-cols-4', // desktop grid columns
    'gap' => 'md:gap-5',
    'tone' => 'ink', // dot colour: ink | bone
])
@php $dot = $tone === 'bone' ? 'border-bone' : 'border-ink'; $dotOn = $tone === 'bone' ? 'bg-bone' : 'bg-ink'; @endphp
{{-- Mobile: one-slide-per-view scroll-snap carousel with dots. md+: plain grid. Wrap each item in <x-site.carousel-item>. --}}
<div x-data="{
        i: 0, n: {{ (int) $count }},
        go(k) { const el = this.$refs.track; const c = el.children[Math.max(0, Math.min(k, this.n - 1))]; if (c) el.scrollTo({ left: c.offsetLeft - el.offsetLeft, behavior: 'smooth' }); },
        sync() { const el = this.$refs.track; let best = 0, d = Infinity; [...el.children].forEach((c, k) => { const dist = Math.abs((c.offsetLeft - el.offsetLeft) - el.scrollLeft); if (dist < d) { d = dist; best = k; } }); this.i = best; }
     }" {{ $attributes }}>
    <div x-ref="track" @scroll.passive.debounce.80ms="sync()"
         class="flex snap-x snap-mandatory gap-4 overflow-x-auto scroll-smooth pb-2 -mx-4 px-4 no-scrollbar
                md:mx-0 md:px-0 md:pb-0 md:grid {{ $grid }} {{ $gap }} md:overflow-visible md:snap-none">
        {{ $slot }}
    </div>

    <div class="mt-5 flex items-center justify-center gap-2 md:hidden" x-show="n > 1" role="group" aria-label="{{ __('Slides') }}">
        <template x-for="k in n" :key="k">
            <button type="button" @click="go(k - 1)"
                    class="h-2.5 w-2.5 border-2 {{ $dot }} transition"
                    :class="i === k - 1 ? '{{ $dotOn }}' : 'bg-transparent'"
                    :aria-current="i === k - 1 ? 'true' : null"
                    :aria-label="'{{ __('Slide') }} ' + k + ' / ' + n"></button>
        </template>
    </div>
</div>
