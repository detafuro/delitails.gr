@props(['headings' => []])
<div class="overflow-hidden border-2 border-ink bg-bone">
    <table class="min-w-full divide-y-2 divide-ink/15 text-sm">
        @if(count($headings))
            <thead class="bg-ink text-bone">
                <tr>
                    @foreach($headings as $h)
                        <th class="px-3 py-3 text-left font-bold uppercase tracking-wider text-xs">{{ $h }}</th>
                    @endforeach
                </tr>
            </thead>
        @endif
        <tbody class="divide-y divide-ink/10">
            {{ $slot }}
        </tbody>
    </table>
</div>
