@props([
    'src',                 // storage-relative path, e.g. products/foo.jpg
    'alt' => '',
    'sizes' => '100vw',
    'widths' => null,      // override candidate widths
    'loading' => 'lazy',   // lazy | eager
    'fetchpriority' => null,
    'fallback' => null,    // storage-relative path used when $src is empty
])
@php
    use App\Support\Media;
    $path = $src ?: $fallback;
    $ok = $path && Media::supports($path) && Media::dimensions($path);
@endphp
@if($ok)
    @php
        [$w, $h] = Media::dimensions($path);
        $ws = Media::widths($path, $widths ?: Media::WIDTHS);
        $srcset = implode(', ', array_map(fn ($x) => Media::url($path, $x).' '.$x.'w', $ws));
        $default = collect($ws)->first(fn ($x) => $x >= 800) ?? end($ws);
    @endphp
    <img src="{{ Media::url($path, $default) }}" srcset="{{ $srcset }}" sizes="{{ $sizes }}"
         width="{{ $w }}" height="{{ $h }}" alt="{{ $alt }}"
         loading="{{ $loading }}" decoding="async" @if($fetchpriority) fetchpriority="{{ $fetchpriority }}" @endif
         {{ $attributes }}>
@elseif($path)
    <img src="{{ asset('storage/'.$path) }}" alt="{{ $alt }}" loading="{{ $loading }}" decoding="async" {{ $attributes }}>
@endif
