@props(['faqs' => collect()])
<div class="space-y-3" x-data="{open: null}">
    @foreach($faqs as $i => $faq)
        <div class="brush-card bg-bone overflow-hidden">
            <button type="button" @click="open === {{ $i }} ? open = null : open = {{ $i }}"
                    class="flex w-full items-center justify-between gap-4 px-5 py-4 text-left">
                <span class="font-display text-lg md:text-xl font-extrabold uppercase">{{ $faq->question }}</span>
                <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center border-2 border-ink bg-fire text-bone font-black leading-none"
                      :class="{'bg-ink': open === {{ $i }}}"
                      x-text="open === {{ $i }} ? '−' : '+'">+</span>
            </button>
            <div x-show="open === {{ $i }}" x-collapse x-cloak>
                <div class="border-t-2 border-dashed border-ink/30 px-5 py-4 text-ink/80 font-editorial text-xl leading-relaxed">
                    {!! nl2br(e($faq->answer)) !!}
                </div>
            </div>
        </div>
    @endforeach
</div>
