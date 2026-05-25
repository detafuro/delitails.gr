@props(['paginator'])
@if($paginator->hasPages())
<nav class="mt-6 flex flex-wrap items-center justify-between gap-3 text-sm">
    <div class="text-ink/60">
        Showing <span class="font-semibold text-ink">{{ $paginator->firstItem() }}</span>
        to <span class="font-semibold text-ink">{{ $paginator->lastItem() }}</span>
        of <span class="font-semibold text-ink">{{ $paginator->total() }}</span>
    </div>
    <div class="flex flex-wrap items-center gap-1">
        @if($paginator->onFirstPage())
            <span class="border-2 border-ink/20 bg-bone px-3 py-1.5 text-ink/40 cursor-not-allowed">&lt;</span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" class="border-2 border-ink bg-bone px-3 py-1.5 font-bold hover:bg-ink hover:text-bone">&lt;</a>
        @endif

        @foreach($paginator->getUrlRange(max(1, $paginator->currentPage()-2), min($paginator->lastPage(), $paginator->currentPage()+2)) as $page => $url)
            @if($page == $paginator->currentPage())
                <span class="border-2 border-ink bg-fire text-bone px-3 py-1.5 font-bold">{{ $page }}</span>
            @else
                <a href="{{ $url }}" class="border-2 border-ink bg-bone px-3 py-1.5 font-bold hover:bg-ink hover:text-bone">{{ $page }}</a>
            @endif
        @endforeach

        @if($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" class="border-2 border-ink bg-bone px-3 py-1.5 font-bold hover:bg-ink hover:text-bone">&gt;</a>
        @else
            <span class="border-2 border-ink/20 bg-bone px-3 py-1.5 text-ink/40 cursor-not-allowed">&gt;</span>
        @endif
    </div>
</nav>
@endif
