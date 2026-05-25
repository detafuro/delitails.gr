<x-admin.layout title="Products" subtitle="Manage the catalog">
    <x-slot:actions>
        <a href="{{ route('admin.products.create') }}" class="btn-rough is-fire is-sm">+ New product</a>
    </x-slot:actions>

    <form method="GET" class="brush-card mb-6 grid md:grid-cols-5 gap-3 p-4">
        <x-admin.form-input name="q" placeholder="Search by title, SKU…" :value="request('q')" class="md:col-span-2"/>
        <x-admin.select name="category_id" :value="request('category_id')" placeholder="All animals"
            :options="$categories->pluck('name','id')->toArray()"/>
        <x-admin.select name="type" :value="request('type')" placeholder="All types"
            :options="\App\Models\Product::TYPES"/>
        <x-admin.select name="status" :value="request('status')" placeholder="Any status"
            :options="['published'=>'Published','draft'=>'Draft','featured'=>'Featured']"/>
        <div class="md:col-span-5 flex gap-2">
            <button class="btn-rough is-bone is-sm">Filter</button>
            <a href="{{ route('admin.products.index') }}" class="btn-rough is-ghost is-sm">Reset</a>
        </div>
    </form>

    <x-admin.table :headings="['Title','Animal','Type','Status','Featured','']">
        @forelse($products as $product)
            <tr class="bg-bone hover:bg-fire/5">
                <td class="px-3 py-3">
                    <a class="font-semibold hover:text-fire" href="{{ route('admin.products.edit', $product) }}">{{ $product->title }}</a>
                    <div class="text-xs text-ink/50">/{{ $product->slug }}</div>
                </td>
                <td class="px-3 py-3 text-sm">{{ $product->category?->name ?? '—' }}</td>
                <td class="px-3 py-3 text-sm">{{ $product->type_label ?? '—' }}</td>
                <td class="px-3 py-3"><x-admin.status-badge :status="$product->is_published"/></td>
                <td class="px-3 py-3">
                    @if($product->is_featured)
                        <span class="inline-flex items-center gap-1 border-2 border-ink px-2 py-0.5 text-[10px] font-bold uppercase bg-fire text-bone">★ Featured</span>
                    @else
                        <span class="text-ink/30 text-xs">—</span>
                    @endif
                </td>
                <td class="px-3 py-3 text-right">
                    <div class="flex justify-end gap-2">
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn-rough is-bone is-sm">Edit</a>
                        <x-admin.confirm-delete :action="route('admin.products.destroy', $product)" message="Delete this product and all its images?"/>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6" class="px-3 py-10 text-center text-ink/50">No products yet. <a class="underline" href="{{ route('admin.products.create') }}">Create one</a>.</td></tr>
        @endforelse
    </x-admin.table>

    <x-admin.pagination :paginator="$products"/>
</x-admin.layout>
