<x-admin.layout title="FAQs">
    <x-slot:actions>
        <a href="{{ route('admin.faq-groups.index') }}" class="btn-rough is-bone is-sm">Manage groups</a>
        <a href="{{ route('admin.faqs.create') }}" class="btn-rough is-fire is-sm">+ New FAQ</a>
    </x-slot:actions>

    <form method="GET" class="brush-card mb-6 grid md:grid-cols-3 gap-3 p-4">
        <x-admin.form-input name="q" placeholder="Search…" :value="request('q')"/>
        <x-admin.select name="group_id" :value="request('group_id')" placeholder="All groups"
            :options="$groups->pluck('name','id')->toArray()"/>
        <div class="flex gap-2"><button class="btn-rough is-bone is-sm">Filter</button>
            <a href="{{ route('admin.faqs.index') }}" class="btn-rough is-ghost is-sm">Reset</a></div>
    </form>

    <x-admin.table :headings="['Question','Group','Sort','Home','Active','']">
        @forelse($faqs as $faq)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3"><a class="font-semibold hover:text-fire" href="{{ route('admin.faqs.edit', $faq) }}">{{ \Str::limit($faq->question, 70) }}</a></td>
                <td class="px-3 py-3 text-sm">{{ $faq->group?->name ?? '—' }}</td>
                <td class="px-3 py-3 text-sm">{{ $faq->sort_order }}</td>
                <td class="px-3 py-3">@if($faq->show_on_homepage)<span class="text-fire">●</span>@else<span class="text-ink/30">○</span>@endif</td>
                <td class="px-3 py-3"><x-admin.status-badge :status="$faq->is_active" on="Active" off="Hidden"/></td>
                <td class="px-3 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn-rough is-bone is-sm">Edit</a>
                        <x-admin.confirm-delete :action="route('admin.faqs.destroy', $faq)"/>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-3 py-10 text-center text-ink/50">No FAQs yet.</td></tr>
        @endforelse
    </x-admin.table>
    <x-admin.pagination :paginator="$faqs"/>
</x-admin.layout>
