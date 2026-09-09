@php $fields = $contest->fieldDefinitions(); @endphp
<x-admin.layout title="Entries" :subtitle="$contest->title">
    <x-slot:actions>
        <a href="{{ route('admin.contests.edit', $contest) }}" class="btn-rough is-bone is-sm">Back to contest</a>
        <a href="{{ route('admin.contests.entries.export', ['contest' => $contest] + request()->only('q', 'filter')) }}"
           class="btn-rough is-fire is-sm">Export CSV</a>
    </x-slot:actions>

    <div class="grid sm:grid-cols-3 gap-4 mb-6">
        @foreach([
            ['Entries', $stats['total'], 'bg-bone'],
            ['Newsletter consent', $stats['marketing'], 'bg-grass'],
            ['Today', $stats['today'], 'bg-bone'],
        ] as [$label, $value, $bg])
            <div class="brush-card p-4 {{ $bg }}">
                <div class="text-xs font-bold uppercase tracking-wider text-ink/60">{{ $label }}</div>
                <div class="font-display text-3xl font-black">{{ $value }}</div>
            </div>
        @endforeach
    </div>

    <div class="mb-4 flex flex-wrap items-center gap-2">
        <form method="GET" class="flex gap-2">
            <x-admin.form-input name="q" placeholder="Name, email or phone…" :value="request('q')" class="w-64"/>
            <input type="hidden" name="filter" value="{{ request('filter') }}">
            <button class="btn-rough is-bone is-sm">Search</button>
        </form>
        <div class="flex gap-2 ml-auto">
            @foreach(['' => 'All', 'marketing' => 'Newsletter consent', 'winners' => 'Winner & runners-up'] as $key => $label)
                <a href="{{ route('admin.contests.entries.index', ['contest' => $contest, 'filter' => $key, 'q' => request('q')]) }}"
                   class="btn-rough is-sm {{ request('filter', '') === $key ? 'is-fire' : 'is-bone' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>

    <x-admin.table :headings="array_merge(['Name','Email','Phone'], array_map(fn($f) => $f['label'], $fields), ['Consent','Result','Entered',''])">
        @forelse($entries as $entry)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3 font-semibold">{{ $entry->name }}</td>
                <td class="px-3 py-3 text-sm"><a class="hover:text-fire" href="mailto:{{ $entry->email }}">{{ $entry->email }}</a></td>
                <td class="px-3 py-3 text-sm">{{ $entry->phone ?: '—' }}</td>
                @foreach($fields as $field)
                    <td class="px-3 py-3 text-sm">{{ $entry->extraValue($field['key']) ?? '—' }}</td>
                @endforeach
                <td class="px-3 py-3">
                    @if($entry->marketing_consent)
                        <span class="inline-block border-2 border-ink bg-grass px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">Newsletter</span>
                    @else
                        <span class="text-xs text-ink/40">Terms only</span>
                    @endif
                </td>
                <td class="px-3 py-3">
                    @if($entry->award_rank === 1)
                        <span class="inline-block border-2 border-ink bg-fire text-bone px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">★ Winner</span>
                    @elseif($entry->award_rank)
                        <span class="inline-block border-2 border-ink bg-bone px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">Runner-up {{ $entry->award_rank - 1 }}</span>
                    @else
                        <span class="text-xs text-ink/40">—</span>
                    @endif
                </td>
                <td class="px-3 py-3 text-sm whitespace-nowrap">{{ \App\Support\Dates::format($entry->created_at, 'j M, H:i') }}</td>
                <td class="px-3 py-3 text-right">
                    <x-admin.confirm-delete :action="route('admin.contests.entries.destroy', [$contest, $entry])"
                        message="Delete this entry? If the contest is already drawn, the result is not recalculated."/>
                </td>
            </tr>
        @empty
            <tr><td colspan="{{ 7 + count($fields) }}" class="px-3 py-10 text-center text-ink/50">No entries match.</td></tr>
        @endforelse
    </x-admin.table>

    <x-admin.pagination :paginator="$entries"/>

    {{-- GDPR --}}
    <div class="brush-card mt-8 p-5">
        <h3 class="font-display text-lg font-extrabold uppercase">Data clean-up (GDPR)</h3>
        <p class="mt-1 text-sm text-ink/65 max-w-3xl">
            Removes the name, email, phone and answers from every entry in this contest, keeping only the
            counts and the draw log so the result stays verifiable. Export the CSV first if you still need
            the data. Best run once the prize has been handed over.
            @if($contest->entries_purged_at)
                <strong class="block mt-2">Already cleaned on {{ \App\Support\Dates::format($contest->entries_purged_at) }}.</strong>
            @endif
        </p>
        <div class="mt-4">
            <x-admin.confirm-delete :action="route('admin.contests.entries.purge', $contest)"
                label="Erase entrant data"
                message="This permanently removes the personal data of every entrant in this contest. It cannot be undone."/>
        </div>
    </div>
</x-admin.layout>
