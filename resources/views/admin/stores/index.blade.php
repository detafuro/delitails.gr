<x-admin.layout title="Stores">
    <x-slot:actions>
        <a href="{{ route('admin.stores.create') }}" class="btn-rough is-fire is-sm">+ New store</a>
    </x-slot:actions>

    <form method="GET" class="mb-4 flex gap-2">
        <x-admin.form-input name="q" placeholder="Search by city, postcode, name…" :value="request('q')" class="max-w-md"/>
        <button class="btn-rough is-bone is-sm">Search</button>
    </form>

    <x-admin.table :headings="['Name','City','Address','Phone','Active','']">
        @forelse($stores as $store)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3"><a class="font-semibold hover:text-fire" href="{{ route('admin.stores.edit', $store) }}">{{ $store->name }}</a></td>
                <td class="px-3 py-3 text-sm">{{ $store->city }}</td>
                <td class="px-3 py-3 text-sm">{{ \Str::limit($store->address, 50) }}</td>
                <td class="px-3 py-3 text-sm">{{ $store->phone ?: '—' }}</td>
                <td class="px-3 py-3"><x-admin.status-badge :status="$store->is_active" on="Active" off="Hidden"/></td>
                <td class="px-3 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.stores.edit', $store) }}" class="btn-rough is-bone is-sm">Edit</a>
                        <x-admin.confirm-delete :action="route('admin.stores.destroy', $store)"/>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-3 py-10 text-center text-ink/50">No stores yet.</td></tr>
        @endforelse
    </x-admin.table>
    <x-admin.pagination :paginator="$stores"/>
</x-admin.layout>
