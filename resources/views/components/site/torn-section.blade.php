@props([
    'bg' => 'bone', // bone | ink | grass | fire
    'top' => false,
    'bottom' => false,
    'padding' => 'lg', // sm | md | lg | xl
])
@php
    $bgClasses = [
        'bone' => 'bg-bone text-ink',
        'ink' => 'bg-ink text-bone',
        'grass' => 'bg-grass text-ink',
        'fire' => 'bg-fire text-bone',
    ][$bg] ?? 'bg-bone text-ink';
    // The torn top edge (h-10 = 2.5rem) sits above the section and reads as extra top
    // padding, so sections with a top edge get 2.5rem less padding-top to look balanced.
    $padMap = $top ? [
        'sm' => 'pt-[1.5rem] pb-10',
        'md' => 'pt-[3.5rem] pb-16',
        'lg' => 'pt-6 md:pt-[3.5rem] pb-14 md:pb-28',
        'xl' => 'pt-[3.5rem] md:pt-[6.5rem] pb-24 md:pb-36',
    ] : [
        'sm' => 'py-10',
        'md' => 'py-16',
        'lg' => 'py-20 md:py-28',
        'xl' => 'py-24 md:py-36',
    ];
    $padMap = $padMap[$padding] ?? $padMap['lg'];
@endphp
<section {{ $attributes->merge(['class' => 'relative paper '.$bgClasses]) }}>
    @if($top)
        <div aria-hidden="true" class="absolute bottom-full -mb-px left-0 right-0 h-10 paper torn-top {{ ['bone'=>'bg-bone','ink'=>'bg-ink','grass'=>'bg-grass','fire'=>'bg-fire'][$bg] }}"></div>
    @endif
    <div class="relative {{ $padMap }}">
        {{ $slot }}
    </div>
    @if($bottom)
        <div aria-hidden="true" class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom {{ ['bone'=>'bg-bone','ink'=>'bg-ink','grass'=>'bg-grass','fire'=>'bg-fire'][$bg] }}"></div>
    @endif
</section>
