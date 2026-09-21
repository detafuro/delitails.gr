@php
    use App\Models\Contest;
    use App\Support\Dates;
    use App\Support\Seo;

    $state = $contest->state;
    $title = Seo::title($contest->t('seo_title') ?: $contest->t('title'));
    $description = $contest->t('seo_description')
        ?: ($contest->t('excerpt') ?: __('Enter our contest and win :prize.', ['prize' => $contest->t('prize') ?: __('a treat bundle')]));
    $banner = $contest->banner_image ? asset('storage/'.$contest->banner_image) : null;
    $isOpen = $state === Contest::STATE_ACTIVE;
@endphp
<x-contest-layout :title="$title" :description="$description" :image="$banner">

    {{-- ===================== THE OFFER ===================== --}}
    <section class="relative overflow-hidden bg-ink text-bone">
        @if($contest->banner_image)
            <x-site.img :src="$contest->banner_image" alt="" loading="eager" fetchpriority="high" sizes="100vw"
                        class="absolute inset-0 h-full w-full object-cover opacity-25"/>
            <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-b from-ink via-ink/90 to-ink"></div>
        @endif

        <div class="relative mx-auto max-w-6xl px-4 md:px-6 pt-28 md:pt-36 pb-16 md:pb-24">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-14 items-start">

                {{-- Message + prize --}}
                <div class="lg:pt-6">
                    <div class="inline-flex items-center gap-2 border-2 px-3 py-1 text-[11px] font-black uppercase tracking-[0.2em]
                        {{ $isOpen ? 'border-grass bg-grass text-ink' : 'border-bone/40 text-bone/70' }}">
                        @if($isOpen)
                            <span class="h-1.5 w-1.5 rounded-full bg-ink animate-pulse"></span>{{ __('Free to enter') }}
                        @elseif($state === Contest::STATE_SCHEDULED)
                            {{ __('Coming soon') }}
                        @elseif($contest->isDrawn())
                            {{ __('Winner announced') }}
                        @else
                            {{ __('Entries closed') }}
                        @endif
                    </div>

                    <h1 class="mt-5 font-display text-[2.75rem] leading-[0.92] md:text-7xl lg:text-[5rem] font-black uppercase">
                        {{ $contest->t('title') }}
                    </h1>

                    @if($contest->t('excerpt'))
                        <p class="mt-5 max-w-xl font-editorial italic text-xl md:text-2xl text-bone/75 leading-snug">
                            {{ $contest->t('excerpt') }}
                        </p>
                    @endif

                    @if($contest->t('prize'))
                        <div class="mt-8 border-l-4 border-fire pl-5">
                            <div class="text-[11px] font-black uppercase tracking-[0.3em] text-fire-light">{{ __('You could win') }}</div>
                            <div class="mt-1 font-display text-2xl md:text-4xl font-black uppercase leading-tight">{{ $contest->t('prize') }}</div>
                        </div>
                    @endif

                    @if($isOpen)
                        <div class="mt-10">
                            <x-site.countdown :to="$contest->ends_at" :label="__('Entries close in')" tone="ink"/>
                        </div>
                    @elseif($state === Contest::STATE_SCHEDULED)
                        <div class="mt-10">
                            <x-site.countdown :to="$contest->starts_at" :label="__('Opens in')" tone="ink"/>
                        </div>
                    @endif
                </div>

                {{-- The one action on the page --}}
                <div id="enter" class="scroll-mt-6">
                    @if($isOpen)
                        @include('site.contests._entry-form')
                    @elseif($state === Contest::STATE_SCHEDULED)
                        <div class="border-2 border-bone/30 bg-bone/5 p-8 text-center">
                            <div class="font-display text-2xl font-black uppercase">{{ __('Not open yet') }}</div>
                            <p class="mt-3 text-bone/70">{{ __('Entries open on :date. Come back then — or follow us so you do not miss it.', ['date' => Dates::format($contest->starts_at)]) }}</p>
                        </div>
                    @elseif($contest->isDrawn())
                        <div class="border-2 border-bone/30 bg-bone/5 p-8 text-center">
                            <div class="font-display text-2xl font-black uppercase">{{ __('This contest is over') }}</div>
                            <p class="mt-3 text-bone/70">{{ __('The winner has been drawn and announced.') }}</p>
                            <a href="{{ route('contests.winner', ['contest' => $contest->slug]) }}" class="btn-rough is-fire mt-6">{{ __('See the winner') }}</a>
                        </div>
                    @else
                        <div class="border-2 border-bone/30 bg-bone/5 p-8 text-center">
                            <div class="font-display text-2xl font-black uppercase">{{ __('Entries are closed') }}</div>
                            <p class="mt-3 text-bone/70">{{ __('The draw is happening — the winner will be announced on this page shortly.') }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ===================== HOW IT WORKS (only while it still matters) ===================== --}}
    @unless($contest->hasEnded())
    <section class="bg-fire text-bone">
        <div class="mx-auto max-w-6xl px-4 md:px-6 py-10 md:py-12">
            <div class="grid sm:grid-cols-3 gap-8 sm:gap-6">
                @foreach([
                    ['01', __('Fill in the form'), __('Twenty seconds, one entry per email.')],
                    ['02', __('Follow us'), __('On Instagram, so we can find you if you win.')],
                    ['03', __('Get into the draw!'), __('It runs automatically the moment entries close.')],
                ] as [$step, $heading, $copy])
                    <div class="flex gap-4">
                        <div class="font-display text-3xl font-black leading-none text-bone/50">{{ $step }}</div>
                        <div>
                            <div class="font-display text-lg font-black uppercase leading-tight">{{ $heading }}</div>
                            <p class="mt-1 text-sm text-bone/85 leading-relaxed">{{ $copy }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
    @endunless

    {{-- ===================== DETAIL + TERMS ===================== --}}
    <section class="bg-bone">
        <div class="mx-auto max-w-6xl px-4 md:px-6 py-16 md:py-20">
            @if($contest->t('description'))
                <div class="quill-content text-lg leading-relaxed text-ink/85 [&>*+*]:mt-6 [&_li+li]:mt-2">
                    {!! $contest->t('description') !!}
                </div>
            @endif

            <div id="terms" class="mt-12 scroll-mt-8 border-2 border-ink bg-bone">
                <h2 class="border-b-2 border-ink bg-ink px-5 py-3 font-display text-lg font-black uppercase tracking-wider text-bone">
                    {{ __('Terms & conditions') }}
                </h2>
                <div class="px-5 py-5">
                    @if($contest->t('terms'))
                        <div class="quill-content text-sm leading-relaxed text-ink/80">{!! $contest->t('terms') !!}</div>
                    @else
                        <p class="text-sm text-ink/70">{{ __('Full terms are published before entries open.') }}</p>
                    @endif

                    <dl class="mt-6 grid sm:grid-cols-2 gap-x-8 gap-y-3 border-t-2 border-dashed border-ink/25 pt-5 text-sm">
                        <div class="flex justify-between gap-4 sm:block">
                            <dt class="text-[11px] font-bold uppercase tracking-widest text-ink/50">{{ __('Opens') }}</dt>
                            <dd class="font-semibold">{{ Dates::format($contest->starts_at) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 sm:block">
                            <dt class="text-[11px] font-bold uppercase tracking-widest text-ink/50">{{ __('Closes') }}</dt>
                            <dd class="font-semibold">{{ Dates::format($contest->ends_at) }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 sm:block">
                            <dt class="text-[11px] font-bold uppercase tracking-widest text-ink/50">{{ __('Entry') }}</dt>
                            <dd class="font-semibold">{{ __('One per email address') }}</dd>
                        </div>
                        <div class="flex justify-between gap-4 sm:block">
                            <dt class="text-[11px] font-bold uppercase tracking-widest text-ink/50">{{ __('Winner') }}</dt>
                            <dd class="font-semibold">
                                {{ $contest->winners_count === 1 ? __('One winner') : __(':count winners', ['count' => $contest->winners_count]) }}@if($contest->runners_up_count)
                                    · {{ $contest->runners_up_count === 1 ? __('one runner-up') : __(':count runners-up', ['count' => $contest->runners_up_count]) }}
                                @endif
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>

            @if($isOpen)
                <div class="mt-12 text-center">
                    <x-site.rough-button href="#enter" variant="fire">{{ __('Enter the contest') }}</x-site.rough-button>
                </div>
            @endif
        </div>
    </section>
</x-contest-layout>
