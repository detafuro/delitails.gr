@php use App\Models\Contest; @endphp
<div class="brush-card p-5 space-y-5">
    <div class="flex flex-wrap items-start justify-between gap-4">
        <div>
            <h3 class="font-display text-lg font-extrabold uppercase">The draw</h3>
            <p class="text-xs text-ink/60">
                Picks {{ $contest->winners_count }} winner{{ $contest->winners_count > 1 ? 's' : '' }}
                @if($contest->runners_up_count) and {{ $contest->runners_up_count }} runner{{ $contest->runners_up_count > 1 ? 's' : '' }}-up @endif
                at random, emails the winner, and publishes the announcement page.
            </p>
        </div>

        @if($contest->entries_count > 0)
            <div x-data="{open:false}" class="inline-block">
                <button type="button" @click="open=true" class="btn-rough is-fire is-sm">
                    {{ $contest->isDrawn() ? 'Draw again' : 'Draw now' }}
                </button>
                <div x-show="open" x-cloak x-transition.opacity
                     class="fixed inset-0 z-50 flex items-center justify-center bg-ink/70 p-4" @click.self="open=false">
                    <div class="brush-card max-w-md p-6 bg-bone">
                        <h3 class="font-display text-2xl font-extrabold uppercase">
                            {{ $contest->isDrawn() ? 'Draw again?' : 'Run the draw?' }}
                        </h3>
                        <p class="mt-2 text-ink/70">
                            {{ $contest->entries_count }} entries are in the hat.
                            @if($contest->isDrawn())
                                This replaces the current result on the public announcement page and emails the new winner.
                                The old result stays in the log below.
                            @else
                                The winner is emailed immediately and the announcement page goes live.
                            @endif
                            @if(! $contest->hasEnded())
                                <strong class="block mt-2">This contest is still running — drawing now closes it early in practice.</strong>
                            @endif
                        </p>
                        <div class="mt-5 flex gap-3 justify-end">
                            <button type="button" @click="open=false" class="btn-rough is-bone is-sm">Cancel</button>
                            <form method="POST" action="{{ route('admin.contests.draw', $contest) }}">
                                @csrf
                                <button class="btn-rough is-fire is-sm">Yes, draw</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <span class="text-sm text-ink/50">No entries yet — nothing to draw.</span>
        @endif
    </div>

    @if($contest->state === Contest::STATE_ENDED && $contest->auto_draw)
        <p class="border-2 border-dashed border-ink/30 px-3 py-2 text-sm text-ink/70">
            This contest has closed and auto-draw is on — the scheduler picks the winner within five minutes.
        </p>
    @endif

    {{-- Current result --}}
    @if($contest->awarded->isNotEmpty())
        <div>
            <h4 class="font-display font-extrabold uppercase mb-2">Current result</h4>
            <div class="space-y-2">
                @foreach($contest->awarded as $entry)
                    <div class="flex flex-wrap items-center gap-3 border-2 border-ink px-3 py-2 {{ $entry->award_rank === 1 ? 'bg-grass' : 'bg-bone' }}">
                        <span class="font-display font-black uppercase text-sm">
                            {{ $entry->award_rank === 1 ? '★ Winner' : 'Runner-up '.($entry->award_rank - 1) }}
                        </span>
                        <span class="font-semibold">{{ $entry->name }}</span>
                        <span class="text-sm text-ink/70">{{ $entry->email }}</span>
                        @if($entry->phone)<span class="text-sm text-ink/70">{{ $entry->phone }}</span>@endif
                        <span class="ml-auto text-xs text-ink/55">
                            shown publicly as “{{ $entry->masked_name }}”
                            @if($entry->award_rank === 1)
                                · {{ $entry->notified_at ? 'emailed '.$entry->notified_at->diffForHumans() : 'email not sent' }}
                            @endif
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Audit log --}}
    @if($contest->draws->isNotEmpty())
        <div>
            <h4 class="font-display font-extrabold uppercase mb-2">Draw log</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full border-2 border-ink text-sm">
                    <thead class="bg-ink text-bone">
                        <tr>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider">When</th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider">Entries</th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider">Result</th>
                            <th class="px-3 py-2 text-left text-xs font-bold uppercase tracking-wider">By</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-ink/10 bg-bone">
                        @foreach($contest->draws as $draw)
                            <tr>
                                <td class="px-3 py-2 whitespace-nowrap">{{ \App\Support\Dates::format($draw->created_at) }}</td>
                                <td class="px-3 py-2">{{ $draw->entries_count }}</td>
                                <td class="px-3 py-2">
                                    @foreach($draw->result ?? [] as $row)
                                        <div>
                                            <span class="font-bold">{{ $row['rank'] == 1 ? 'Winner' : 'Runner-up '.($row['rank'] - 1) }}:</span>
                                            {{ $row['name'] }} — {{ $row['email'] }}
                                        </div>
                                    @endforeach
                                </td>
                                <td class="px-3 py-2">{{ $draw->performerLabel() }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
