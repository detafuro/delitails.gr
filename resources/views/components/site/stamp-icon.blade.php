@props(['label' => '', 'glyph' => '★', 'bg' => 'bone'])
@php
    $bgClass = [
        'bone' => 'bg-bone',
        'grass' => 'bg-grass',
        'fire' => 'bg-fire text-bone',
        'ink' => 'bg-ink text-bone',
    ][$bg] ?? 'bg-bone';
@endphp
<div class="flex flex-col items-center text-center">
    <div class="stamp {{ $bgClass }}">
        <span class="font-display text-2xl font-black">{{ $glyph }}</span>
    </div>
    <div class="mt-3 font-display text-sm font-extrabold uppercase tracking-wider">{{ $label }}</div>
</div>
