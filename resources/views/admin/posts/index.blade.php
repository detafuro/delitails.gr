<x-admin.layout title="Blog posts">
    <x-slot:actions>
        <a href="{{ route('admin.blog-categories.index') }}" class="btn-rough is-bone is-sm">Categories</a>
        <a href="{{ route('admin.posts.create') }}" class="btn-rough is-fire is-sm">+ New post</a>
    </x-slot:actions>

    <form method="GET" class="mb-4 flex gap-2">
        <x-admin.form-input name="q" placeholder="Search…" :value="request('q')" class="max-w-sm"/>
        <button class="btn-rough is-bone is-sm">Search</button>
    </form>

    <x-admin.table :headings="['Title','Category','Author','Date','Status','']">
        @forelse($posts as $post)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3"><a class="font-semibold hover:text-fire" href="{{ route('admin.posts.edit', $post) }}">{{ $post->title }}</a></td>
                <td class="px-3 py-3 text-sm">{{ $post->category?->name ?? '—' }}</td>
                <td class="px-3 py-3 text-sm">{{ $post->author ?: '—' }}</td>
                <td class="px-3 py-3 text-sm">{{ $post->published_at?->format('M j, Y') ?: '—' }}</td>
                <td class="px-3 py-3"><x-admin.status-badge :status="$post->is_published"/></td>
                <td class="px-3 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.posts.edit', $post) }}" class="btn-rough is-bone is-sm">Edit</a>
                        <x-admin.confirm-delete :action="route('admin.posts.destroy', $post)"/>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-3 py-10 text-center text-ink/50">No posts yet.</td></tr>
        @endforelse
    </x-admin.table>
    <x-admin.pagination :paginator="$posts"/>
</x-admin.layout>
