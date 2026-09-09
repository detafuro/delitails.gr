<x-admin.layout title="Contests" subtitle="Every giveaway, its entries and its draw">
    <x-slot:actions>
        <a href="{{ route('admin.settings.edit') }}" class="btn-rough is-bone is-sm">Contest settings</a>
        <a href="{{ route('admin.contests.create') }}" class="btn-rough is-fire is-sm">+ New contest</a>
    </x-slot:actions>

    <form method="GET" class="mb-4 flex gap-2">
        <x-admin.form-input name="q" placeholder="Search…" :value="request('q')" class="max-w-sm"/>
        <button class="btn-rough is-bone is-sm">Search</button>
    </form>

    <x-admin.table :headings="['Contest','Runs','Progress','Entries','State','']">
        @forelse($contests as $contest)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3">
                    <a class="font-semibold hover:text-fire" href="{{ route('admin.contests.edit', $contest) }}">{{ $contest->title }}</a>
                    @if($contest->prize)<div class="text-xs text-ink/55">{{ $contest->prize }}</div>@endif
                </td>
                <td class="px-3 py-3 text-sm whitespace-nowrap">
                    {{ \App\Support\Dates::format($contest->starts_at) }}<br>
                    <span class="text-ink/55">→ {{ \App\Support\Dates::format($contest->ends_at) }}</span>
                </td>
                <td class="px-3 py-3 w-40">
                    <div class="h-2 w-full border border-ink/30 bg-bone">
                        <div class="h-full bg-fire" style="width: {{ $contest->progress }}%"></div>
                    </div>
                    <div class="mt-1 text-[11px] text-ink/55">
                        @if($contest->state === \App\Models\Contest::STATE_ACTIVE)
                            closes {{ $contest->ends_at->diffForHumans() }}
                        @elseif($contest->state === \App\Models\Contest::STATE_SCHEDULED)
                            opens {{ $contest->starts_at->diffForHumans() }}
                        @else
                            {{ $contest->progress }}%
                        @endif
                    </div>
                </td>
                <td class="px-3 py-3">
                    <a href="{{ route('admin.contests.entries.index', $contest) }}" class="font-display text-lg font-black hover:text-fire">{{ $contest->entries_count }}</a>
                </td>
                <td class="px-3 py-3"><x-admin.contest-state :contest="$contest"/></td>
                <td class="px-3 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.contests.entries.index', $contest) }}" class="btn-rough is-bone is-sm">Entries</a>
                        <a href="{{ route('admin.contests.edit', $contest) }}" class="btn-rough is-bone is-sm">Edit</a>
                        <x-admin.confirm-delete :action="route('admin.contests.destroy', $contest)"
                            message="Deleting a contest also deletes all of its entries and its draw log. This cannot be undone."/>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-3 py-10 text-center text-ink/50">No contests yet. Create one to get started.</td></tr>
        @endforelse
    </x-admin.table>
    <x-admin.pagination :paginator="$contests"/>
</x-admin.layout>
