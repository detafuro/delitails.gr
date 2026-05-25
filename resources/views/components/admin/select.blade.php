@props([
    'name',
    'label' => null,
    'options' => [],
    'value' => null,
    'required' => false,
    'placeholder' => null,
])
@php
    $id = $attributes->get('id') ?: $name;
    $val = (string) old($name, $value);
@endphp
<div {{ $attributes->only('class') }}>
    @if($label)
        <label for="{{ $id }}" class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/70">
            {{ $label }} @if($required)<span class="text-fire">*</span>@endif
        </label>
    @endif
    <select id="{{ $id }}" name="{{ $name }}" @if($required) required @endif
        {{ $attributes->except(['class','id'])->merge(['class' => 'w-full border-2 border-ink bg-bone px-3 py-2 font-sans text-ink focus:outline-none focus:ring-2 focus:ring-fire/60']) }}>
        @if($placeholder)<option value="">{{ $placeholder }}</option>@endif
        @foreach($options as $optVal => $optLabel)
            <option value="{{ $optVal }}" @selected($val === (string) $optVal)>{{ $optLabel }}</option>
        @endforeach
    </select>
    @error($name) <p class="mt-1 text-xs text-fire">{{ $message }}</p> @enderror
</div>
