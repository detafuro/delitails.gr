@props([
    'href' => null,
    'variant' => 'ink', // ink | fire | grass | bone | ghost
    'size' => 'md', // sm | md
    'type' => 'button',
    'icon' => null,
])
@php
    $variantClass = 'is-'.$variant;
    if ($variant === 'ink') $variantClass = '';
    $sizeClass = $size === 'sm' ? 'is-sm' : '';
    $base = 'btn-rough '.$variantClass.' '.$sizeClass;
@endphp
@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
        @if($icon !== null) <span aria-hidden="true">{{ $icon }}</span> @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $base]) }}>
        {{ $slot }}
        @if($icon !== null) <span aria-hidden="true">{{ $icon }}</span> @endif
    </button>
@endif
