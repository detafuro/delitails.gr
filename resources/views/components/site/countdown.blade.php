@props([
    'to',                 // Carbon — the moment being counted down to (UTC)
    'label' => null,
    'tone' => 'bone',     // bone | ink — the surface it sits on
])
@php
    $target = \Illuminate\Support\Carbon::parse($to)->toIso8601ZuluString();
    $box = $tone === 'ink'
        ? 'border-bone/30 bg-bone/10 text-bone'
        : 'border-ink bg-bone text-ink';
    $cap = $tone === 'ink' ? 'text-bone/60' : 'text-ink/55';
@endphp
<div {{ $attributes->merge(['class' => 'inline-block']) }}
     x-data="{
        target: new Date('{{ $target }}').getTime(),
        parts: { d: '00', h: '00', m: '00', s: '00' },
        over: false,
        tick() {
            let left = Math.max(0, this.target - Date.now());
            this.over = left === 0;
            const sec = Math.floor(left / 1000);
            const pad = (n) => String(n).padStart(2, '0');
            this.parts = {
                d: pad(Math.floor(sec / 86400)),
                h: pad(Math.floor(sec % 86400 / 3600)),
                m: pad(Math.floor(sec % 3600 / 60)),
                s: pad(sec % 60),
            };
        },
        init() { this.tick(); setInterval(() => this.tick(), 1000); }
     }">
    @if($label)
        <div class="mb-2 text-xs font-bold uppercase tracking-[0.3em] {{ $cap }}">{{ $label }}</div>
    @endif
    <div class="flex items-stretch gap-2 sm:gap-3" role="timer" aria-live="off">
        @foreach([['d', __('Days')], ['h', __('Hours')], ['m', __('Min')], ['s', __('Sec')]] as [$key, $unit])
            <div class="min-w-[3.75rem] sm:min-w-[4.5rem] border-2 {{ $box }} px-2 py-2 text-center">
                <div class="font-display text-2xl sm:text-4xl font-black leading-none tabular-nums" x-text="parts.{{ $key }}">00</div>
                <div class="mt-1 text-[10px] font-bold uppercase tracking-wider {{ $cap }}">{{ $unit }}</div>
            </div>
        @endforeach
    </div>
</div>
