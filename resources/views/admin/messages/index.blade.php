<x-admin.layout title="Contact messages">
    <x-admin.table :headings="['From','Subject','Received','Status','']">
        @forelse($messages as $m)
            <tr class="bg-bone hover:bg-fire/5 {{ $m->is_read ? '' : 'font-semibold' }}">
                <td class="px-3 py-3">
                    <a class="hover:text-fire" href="{{ route('admin.messages.show', $m) }}">{{ $m->name }}</a>
                    <div class="text-xs text-ink/50">{{ $m->email }}</div>
                </td>
                <td class="px-3 py-3 text-sm">{{ $m->subject ?: '—' }}</td>
                <td class="px-3 py-3 text-sm">{{ $m->created_at->format('M j, Y H:i') }}</td>
                <td class="px-3 py-3"><x-admin.status-badge :status="$m->is_read" on="Read" off="New"/></td>
                <td class="px-3 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.messages.show', $m) }}" class="btn-rough is-bone is-sm">Open</a>
                        <x-admin.confirm-delete :action="route('admin.messages.destroy', $m)"/>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-3 py-10 text-center text-ink/50">Inbox is empty.</td></tr>
        @endforelse
    </x-admin.table>
    <x-admin.pagination :paginator="$messages"/>
</x-admin.layout>
