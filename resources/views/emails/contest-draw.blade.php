<x-mail::message>
# Contest drawn

**{{ $contest->title }}** was drawn on {{ \App\Support\Dates::format($draw->created_at) }}
({{ $draw->is_automatic ? 'automatically at closing time' : 'manually by '.$draw->performerLabel() }}).

**Entries in the draw:** {{ $draw->entries_count }}

@foreach($draw->result ?? [] as $row)
- **{{ $contest->awardLabel((int) $row['rank'], false) }}:** {{ $row['name'] }} — {{ $row['email'] }}
@endforeach

<x-mail::button :url="route('admin.contests.entries.index', $contest)">
Open entries in admin
</x-mail::button>

{{ $contest->winnerSlots() > 1 ? 'The winners have' : 'The winner has' }} been emailed automatically. The public announcement page is live at
[{{ route('contests.winner', ['locale' => 'el', 'contest' => $contest->slug]) }}]({{ route('contests.winner', ['locale' => 'el', 'contest' => $contest->slug]) }}).
</x-mail::message>
