@props([
    'bg' => 'bone', // bone | grass | fire | ink
    'tilt' => false,
])
@php
    $bgClasses = [
        'bone' => 'bg-bone text-ink',
        'ink' => 'bg-ink text-bone',
        'grass' => 'bg-grass text-ink',
        'fire' => 'bg-fire text-bone',
    ][$bg] ?? 'bg-bone text-ink';
@endphp
<div {{ $attributes->merge(['class' => 'brush-card '.$bgClasses.' '.($tilt ? 'rotate-tilt-1' : '')]) }}>
    {{ $slot }}
</div>
