<x-admin.layout title="Dashboard">
    @php
        $tiles = [
            ['Products', $stats['products'] ?? 0, route('admin.products.index'), 'bg-fire'],
            ['Categories', $stats['categories'] ?? 0, route('admin.product-categories.index'), 'bg-grass'],
            ['Blog posts', $stats['posts'] ?? 0, route('admin.posts.index'), 'bg-ink text-bone'],
            ['FAQs', $stats['faqs'] ?? 0, route('admin.faqs.index'), 'bg-bone'],
            ['Stores', $stats['stores'] ?? 0, route('admin.stores.index'), 'bg-grass'],
            ['Unread messages', $stats['messages'] ?? 0, route('admin.messages.index'), 'bg-fire'],
            ['Subscribers', $stats['subscribers'] ?? 0, route('admin.subscribers.index'), 'bg-ink text-bone'],
        ];
    @endphp

    <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4 lg:gap-5">
        @foreach($tiles as [$label,$count,$href,$bg])
            <a href="{{ $href }}" class="brush-card p-5 {{ $bg }}">
                <div class="text-xs font-bold uppercase tracking-widest opacity-70">{{ $label }}</div>
                <div class="mt-2 font-display text-4xl font-black">{{ $count }}</div>
            </a>
        @endforeach
    </div>

    <div class="mt-8 grid lg:grid-cols-2 gap-6">
        <div class="brush-card p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-xl font-extrabold uppercase">Recent products</h2>
                <a href="{{ route('admin.products.index') }}" class="text-xs font-bold uppercase tracking-wider underline">View all</a>
            </div>
            <ul class="mt-3 divide-y divide-ink/10">
                @forelse($latestProducts as $p)
                    <li class="flex items-center justify-between gap-3 py-2 text-sm">
                        <a href="{{ route('admin.products.edit', $p) }}" class="font-semibold hover:text-fire">{{ $p->title }}</a>
                        <x-admin.status-badge :status="$p->is_published"/>
                    </li>
                @empty
                    <li class="py-3 text-sm text-ink/50">No products yet.</li>
                @endforelse
            </ul>
        </div>

        <div class="brush-card p-5">
            <div class="flex items-center justify-between">
                <h2 class="font-display text-xl font-extrabold uppercase">Recent messages</h2>
                <a href="{{ route('admin.messages.index') }}" class="text-xs font-bold uppercase tracking-wider underline">Inbox</a>
            </div>
            <ul class="mt-3 divide-y divide-ink/10">
                @forelse($latestMessages as $m)
                    <li class="py-2 text-sm">
                        <a href="{{ route('admin.messages.show', $m) }}" class="font-semibold hover:text-fire">
                            {{ $m->name }} <span class="text-ink/40 font-normal">— {{ $m->subject ?: '(no subject)' }}</span>
                        </a>
                        <div class="text-xs text-ink/50">{{ $m->created_at->diffForHumans() }}</div>
                    </li>
                @empty
                    <li class="py-3 text-sm text-ink/50">Inbox is empty.</li>
                @endforelse
            </ul>
        </div>
    </div>
</x-admin.layout>
