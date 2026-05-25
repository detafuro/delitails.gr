<x-admin.layout title="Testimonials">
    <x-slot:actions>
        <a href="{{ route('admin.testimonials.create') }}" class="btn-rough is-fire is-sm">+ New testimonial</a>
    </x-slot:actions>

    <x-admin.table :headings="['Author','Pet','Rating','Active','']">
        @forelse($testimonials as $t)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3"><a class="font-semibold hover:text-fire" href="{{ route('admin.testimonials.edit', $t) }}">{{ $t->author }}</a></td>
                <td class="px-3 py-3 text-sm">{{ $t->pet_name ?: '—' }}</td>
                <td class="px-3 py-3 text-sm">{!! str_repeat('★', $t->rating) !!}</td>
                <td class="px-3 py-3"><x-admin.status-badge :status="$t->is_active" on="Active" off="Hidden"/></td>
                <td class="px-3 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.testimonials.edit', $t) }}" class="btn-rough is-bone is-sm">Edit</a>
                        <x-admin.confirm-delete :action="route('admin.testimonials.destroy', $t)"/>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="5" class="px-3 py-10 text-center text-ink/50">No testimonials yet.</td></tr>
        @endforelse
    </x-admin.table>
    <x-admin.pagination :paginator="$testimonials"/>
</x-admin.layout>
