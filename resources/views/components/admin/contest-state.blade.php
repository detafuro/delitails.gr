@props(['contest'])
@php
    $state = $contest->state;
    $style = [
        'draft' => 'bg-bone text-ink/60',
        'scheduled' => 'bg-bone text-ink',
        'active' => 'bg-grass text-ink',
        'ended' => 'bg-fire/20 text-ink',
        'completed' => 'bg-ink text-bone',
    ][$state] ?? 'bg-bone text-ink';
    $label = ['draft' => 'Draft', 'scheduled' => 'Scheduled', 'active' => 'Running', 'ended' => 'Ended — awaiting draw', 'completed' => 'Completed'][$state] ?? $state;
@endphp
<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1.5 border-2 border-ink px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider '.$style]) }}>
    <span class="h-1.5 w-1.5 rounded-full {{ $state === 'active' ? 'bg-ink animate-pulse' : 'bg-current opacity-60' }}"></span>
    {{ $label }}
</span>
