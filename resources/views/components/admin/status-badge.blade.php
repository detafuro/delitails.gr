@props(['status' => false, 'on' => 'Published', 'off' => 'Draft'])
@php
    $active = (bool) $status;
@endphp
<span class="inline-flex items-center gap-1.5 border-2 border-ink px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider {{ $active ? 'bg-grass text-ink' : 'bg-bone text-ink/60' }}">
    <span class="h-1.5 w-1.5 rounded-full {{ $active ? 'bg-ink' : 'bg-ink/40' }}"></span>
    {{ $active ? $on : $off }}
</span>
