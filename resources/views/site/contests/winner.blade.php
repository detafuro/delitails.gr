@php
    use App\Support\Dates;
    use App\Support\Seo;

    $winners = $contest->awarded->filter(fn ($e) => $contest->isWinningRank($e->award_rank));
    $runnersUp = $contest->awarded->reject(fn ($e) => $contest->isWinningRank($e->award_rank));
    $title = Seo::title(__('Winner'), $contest->t('title'));
    $banner = $contest->banner_image ? asset('storage/'.$contest->banner_image) : null;
@endphp
<x-contest-layout :title="$title"
                  :description="__('The winner of :contest has been drawn.', ['contest' => $contest->t('title')])"
                  :image="$banner"
                  :logoOnDark="false">
    <section class="relative overflow-hidden bg-grass">
        <div class="relative mx-auto max-w-6xl px-4 md:px-6 pt-28 md:pt-36 pb-14 md:pb-20 text-center">
            <div class="text-xs font-bold uppercase tracking-[0.3em] text-ink/60">{{ __('The draw is done') }}</div>
            <h1 class="mt-3 font-display text-4xl md:text-7xl font-black uppercase leading-[0.95]">
                {{ $contest->winnerSlots() > 1 ? __('We have our winners') : __('We have a winner') }}
            </h1>
            <p class="mt-4 font-editorial italic text-xl text-ink/75">{{ $contest->t('title') }}</p>
        </div>
    </section>

    <section class="bg-bone py-14 md:py-20">
        <div class="mx-auto max-w-3xl px-4 md:px-6">
            @if($contest->t('winner_message'))
                <div class="quill-content mb-8 text-center font-editorial text-lg text-ink/80">{!! $contest->t('winner_message') !!}</div>
            @endif

            <div class="space-y-5">
                @foreach($winners as $winner)
                    <div class="brush-card bg-fire text-bone p-8 md:p-10 text-center">
                        <div class="text-xs font-bold uppercase tracking-[0.3em] text-bone/70">{{ $contest->awardLabel($winner->award_rank) }}</div>
                        <div class="mt-3 font-display text-4xl md:text-6xl font-black uppercase">{{ $winner->masked_name }}</div>
                        @if($winners->count() === 1 && $contest->t('prize'))
                            <p class="mt-4 font-editorial italic text-xl text-bone/85">{{ $contest->t('prize') }}</p>
                        @endif
                    </div>
                @endforeach
            </div>
            @if($winners->count() > 1 && $contest->t('prize'))
                <p class="mt-6 text-center font-editorial italic text-xl text-ink/75">{{ $contest->t('prize') }}</p>
            @endif

            @if($runnersUp->isNotEmpty())
                <div class="mt-8 brush-card bg-bone p-6 md:p-8">
                    <h2 class="font-display text-xl font-extrabold uppercase">{{ __('Runners-up') }}</h2>
                    <p class="mt-1 text-sm text-ink/60">{{ __('In line if the winner does not reply in time.') }}</p>
                    <ol class="mt-4 space-y-2">
                        @foreach($runnersUp as $i => $entry)
                            <li class="flex items-center gap-3 border-2 border-ink bg-bone px-4 py-2">
                                <span class="font-display text-lg font-black text-fire">{{ $loop->iteration }}</span>
                                <span class="font-semibold">{{ $entry->masked_name }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            @endif

            {{-- Transparency record --}}
            <div class="mt-8 border-2 border-dashed border-ink/30 p-5 text-sm text-ink/70">
                <div class="font-bold uppercase tracking-wider text-ink">{{ __('For the record') }}</div>
                <ul class="mt-2 space-y-1">
                    <li>{{ __('Drawn on') }}: {{ Dates::format($contest->drawn_at) }}</li>
                    @if($contest->latestDraw)
                        <li>{{ __('Entries in the draw') }}: {{ $contest->latestDraw->entries_count }}</li>
                    @endif
                    <li>{{ __('Method') }}: {{ __('random selection by the site, one entry per email address') }}</li>
                </ul>
                <p class="mt-3 text-xs text-ink/55">
                    {{ __('Winners are shown by first name and last initial to protect their privacy.') }}
                </p>
            </div>

            <div class="mt-10 flex flex-wrap justify-center gap-3">
                <x-site.rough-button :href="route('contests.show', ['contest' => $contest->slug])" variant="bone">
                    {{ __('Back to the contest') }}
                </x-site.rough-button>
                <x-site.rough-button :href="route('home')" variant="fire">
                    {{ __('Shop the treats') }}
                </x-site.rough-button>
            </div>
        </div>
    </section>
</x-contest-layout>
