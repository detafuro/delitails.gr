@props([
    'name',
    'label' => null,
    'value' => null,
    'required' => false,
    'hint' => null,
])
@php
    $id = $attributes->get('id') ?: $name;
    $val = old($name, $value);
@endphp
<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $id }}-editor" class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/70">
            {{ $label }} @if($required)<span class="text-fire">*</span>@endif
        </label>
    @endif
    <div
        id="{{ $id }}-editor"
        data-quill-editor="{{ $id }}"
        class="min-h-64 bg-white border-2 border-ink"
        style="font-family: inherit;"
    ></div>
    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        class="hidden"
        @if($required) required @endif
    >{{ $val }}</textarea>
    @if($hint)<p class="mt-1 text-xs text-ink/50">{{ $hint }}</p>@endif
    @error($name) <p class="mt-1 text-xs text-fire">{{ $message }}</p> @enderror
</div>
