@props([
    'name',
    'label' => null,
    'value' => null,
    'rows' => 5,
    'required' => false,
    'hint' => null,
])
@php
    $id = $attributes->get('id') ?: $name;
    $val = old($name, $value);
@endphp
<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $id }}" class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/70">
            {{ $label }} @if($required)<span class="text-fire">*</span>@endif
        </label>
    @endif
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($required) required @endif
        {{ $attributes->except(['class','id'])->merge(['class' => 'w-full border-2 border-ink bg-bone px-3 py-2 font-sans text-ink placeholder:text-ink/40 focus:outline-none focus:ring-2 focus:ring-fire/60']) }}
    >{{ $val }}</textarea>
    @if($hint)<p class="mt-1 text-xs text-ink/50">{{ $hint }}</p>@endif
    @error($name) <p class="mt-1 text-xs text-fire">{{ $message }}</p> @enderror
</div>
