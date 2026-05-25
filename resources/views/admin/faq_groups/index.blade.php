<x-admin.layout title="FAQ groups">
    <x-slot:actions>
        <a href="{{ route('admin.faqs.index') }}" class="btn-rough is-bone is-sm">All FAQs</a>
        <a href="{{ route('admin.faq-groups.create') }}" class="btn-rough is-fire is-sm">+ New group</a>
    </x-slot:actions>

    <x-admin.table :headings="['Name','Slug','FAQs','Active','']">
        @forelse($groups as $g)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3"><a class="font-semibold hover:text-fire" href="{{ route('admin.faq-groups.edit', $g) }}">{{ $g->name }}</a></td>
                <td class="px-3 py-3 text-sm text-ink/60">/{{ $g->slug }}</td>
                <td class="px-3 py-3">{{ $g->faqs_count }}</td>
                <td class="px-3 py-3"><x-admin.status-badge :status="$g->is_active" on="Active" off="Hidden"/></td>
                <td class="px-3 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.faq-groups.edit', $g) }}" class="btn-rough is-bone is-sm">Edit</a>
                        <x-admin.confirm-delete :action="route('admin.faq-groups.destroy', $g)"/>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-3 py-10 text-center text-ink/50">No groups yet.</td></tr>
        @endforelse
    </x-admin.table>
    <x-admin.pagination :paginator="$groups"/>
</x-admin.layout>
