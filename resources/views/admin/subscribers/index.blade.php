<x-admin.layout title="Newsletter subscribers">
    <x-admin.table :headings="['Email','Subscribed','Status','']">
        @forelse($subscribers as $s)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3 font-mono text-sm">{{ $s->email }}</td>
                <td class="px-3 py-3 text-sm">{{ $s->created_at->format('M j, Y') }}</td>
                <td class="px-3 py-3"><x-admin.status-badge :status="$s->is_active" on="Active" off="Unsubscribed"/></td>
                <td class="px-3 py-3 text-right">
                    <x-admin.confirm-delete :action="route('admin.subscribers.destroy', $s)"/>
                </td>
            </tr>
        @empty
            <tr><td colspan="4" class="px-3 py-10 text-center text-ink/50">No subscribers yet.</td></tr>
        @endforelse
    </x-admin.table>
    <x-admin.pagination :paginator="$subscribers"/>
</x-admin.layout>
