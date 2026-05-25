@props([
    'name',
    'label' => null,
    'value' => false,
    'hint' => null,
])
@php
    $checked = (bool) old($name, $value);
@endphp
<label class="flex items-start gap-3 cursor-pointer select-none" {{ $attributes->only('class') }}>
    <input type="hidden" name="{{ $name }}" value="0">
    <input type="checkbox" name="{{ $name }}" value="1" @checked($checked)
           class="mt-1 h-5 w-5 cursor-pointer border-2 border-ink accent-fire">
    <span>
        @if($label)<span class="block text-sm font-bold uppercase tracking-wider text-ink">{{ $label }}</span>@endif
        @if($hint)<span class="block text-xs text-ink/55">{{ $hint }}</span>@endif
    </span>
</label>
