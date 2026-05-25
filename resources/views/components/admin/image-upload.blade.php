@props([
    'name',
    'label' => null,
    'currentPath' => null,
    'multiple' => false,
    'hint' => null,
    'removable' => true,
])
@php
    $removeFlag = '__remove_'.$name;
@endphp
<div {{ $attributes->only('class') }} x-data="{ remove: false }">
    @if($label)
        <label class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/70">{{ $label }}</label>
    @endif

    @if($currentPath)
        <div class="mb-3 flex items-center gap-3 border-2 border-ink bg-bone p-2"
             :class="remove ? 'opacity-50 line-through' : ''">
            <img src="{{ asset('storage/'.$currentPath) }}" alt="" class="h-20 w-20 object-cover border border-ink/30">
            <div class="text-xs text-ink/60 break-all flex-1">{{ $currentPath }}</div>
            @if($removable)
                <label class="ml-auto inline-flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="{{ $removeFlag }}" value="1" x-model="remove"
                           class="h-4 w-4 border-2 border-ink accent-fire cursor-pointer">
                    <span class="text-xs font-bold uppercase tracking-wider">Remove on save</span>
                </label>
            @endif
        </div>
    @endif

    <div x-data="{name:''}" class="border-2 border-dashed border-ink/40 bg-bone/40 p-3">
        <input type="file" name="{{ $name }}{{ $multiple ? '[]' : '' }}"
               @if($multiple) multiple @endif
               accept="image/*"
               @change="name = Array.from($event.target.files).map(f => f.name).join(', ')"
               class="block w-full text-sm text-ink file:btn-rough file:is-bone file:is-sm file:cursor-pointer file:mr-3">
        <p class="mt-2 text-xs text-ink/50" x-text="name || 'No file selected. Picking one will replace the current image.'"></p>
    </div>
    @if($hint)<p class="mt-1 text-xs text-ink/50">{{ $hint }}</p>@endif
    @error($name) <p class="mt-1 text-xs text-fire">{{ $message }}</p> @enderror
    @error($name.'.*') <p class="mt-1 text-xs text-fire">{{ $message }}</p> @enderror
</div>
