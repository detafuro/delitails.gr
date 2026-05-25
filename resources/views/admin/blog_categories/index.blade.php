<x-admin.layout title="Blog categories">
    <x-slot:actions>
        <a href="{{ route('admin.blog-categories.create') }}" class="btn-rough is-fire is-sm">+ New category</a>
    </x-slot:actions>
    <x-admin.table :headings="['Name','Slug','Posts','Active','']">
        @forelse($categories as $cat)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3"><a class="font-semibold hover:text-fire" href="{{ route('admin.blog-categories.edit', $cat) }}">{{ $cat->name }}</a></td>
                <td class="px-3 py-3 text-sm text-ink/60">/{{ $cat->slug }}</td>
                <td class="px-3 py-3">{{ $cat->posts_count }}</td>
                <td class="px-3 py-3"><x-admin.status-badge :status="$cat->is_active" on="Active" off="Hidden"/></td>
                <td class="px-3 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.blog-categories.edit', $cat) }}" class="btn-rough is-bone is-sm">Edit</a>
                        <x-admin.confirm-delete :action="route('admin.blog-categories.destroy', $cat)"/>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-3 py-10 text-center text-ink/50">No categories yet.</td></tr>
        @endforelse
    </x-admin.table>
    <x-admin.pagination :paginator="$categories"/>
</x-admin.layout>
