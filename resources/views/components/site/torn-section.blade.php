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
    $padMap = [
        'sm' => 'py-10',
        'md' => 'py-16',
        'lg' => 'py-20 md:py-28',
        'xl' => 'py-24 md:py-36',
    ][$padding] ?? 'py-20 md:py-28';
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
