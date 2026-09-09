@php
    use App\Models\Contest;
    use App\Support\Dates;
    use App\Support\Seo;

    $state = $contest->state;
    $title = Seo::title($contest->t('seo_title') ?: $contest->t('title'));
    $description = $contest->t('seo_description') ?: ($contest->t('excerpt') ?: __('Enter our contest and win :prize.', ['prize' => $contest->t('prize') ?: __('a treat bundle')]));
    $banner = $contest->banner_image ? asset('storage/'.$contest->banner_image) : null;
@endphp
<x-layout :title="$title" :description="$description" :image="$banner">
    {{-- Hero --}}
    <section class="relative overflow-hidden bg-ink text-bone paper">
        @if($contest->banner_image)
            <x-site.img :src="$contest->banner_image" alt="" loading="eager" fetchpriority="high"
                        sizes="100vw" class="absolute inset-0 h-full w-full object-cover opacity-30"/>
            <div aria-hidden="true" class="absolute inset-0 bg-gradient-to-r from-ink via-ink/85 to-ink/40"></div>
        @endif

        <div class="relative mx-auto max-w-7xl px-4 md:px-6 py-12 md:py-20">
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('contests.index') }}" class="text-xs font-bold uppercase tracking-[0.3em] text-bone/60 hover:text-fire-light">{{ __('Contests') }}</a>
                <span class="inline-flex items-center gap-2 border-2 px-2.5 py-1 text-[11px] font-bold uppercase tracking-wider
                    {{ $state === Contest::STATE_ACTIVE ? 'border-grass bg-grass text-ink' : 'border-bone/40 text-bone/80' }}">
                    @switch($state)
                        @case(Contest::STATE_ACTIVE) <span class="h-1.5 w-1.5 rounded-full bg-ink animate-pulse"></span>{{ __('Open for entries') }} @break
                        @case(Contest::STATE_SCHEDULED) {{ __('Coming soon') }} @break
                        @case(Contest::STATE_COMPLETED) {{ __('Winner announced') }} @break
                        @case(Contest::STATE_DRAFT) {{ __('Draft — admin preview') }} @break
                        @default {{ __('Closed') }}
                    @endswitch
                </span>
            </div>

            <h1 class="mt-4 font-display text-4xl md:text-7xl font-black uppercase leading-[0.95] max-w-4xl">{{ $contest->t('title') }}</h1>

            @if($contest->t('prize'))
                <p class="mt-4 max-w-2xl font-editorial italic text-xl md:text-2xl text-bone/85">
                    <span class="text-fire-light font-bold not-italic uppercase text-sm tracking-[0.2em] block mb-1">{{ __('The prize') }}</span>
                    {{ $contest->t('prize') }}
                </p>
            @endif

            <div class="mt-8 flex flex-wrap items-end gap-6">
                @if($state === Contest::STATE_ACTIVE)
                    <x-site.countdown :to="$contest->ends_at" :label="__('Entries close in')" tone="ink"/>
                    <x-site.rough-button href="#enter" variant="fire">{{ __('Enter now') }}</x-site.rough-button>
                @elseif($state === Contest::STATE_SCHEDULED)
                    <x-site.countdown :to="$contest->starts_at" :label="__('Opens in')" tone="ink"/>
                @elseif($contest->isDrawn())
                    <x-site.rough-button :href="route('contests.winner', ['contest' => $contest->slug])" variant="fire">
                        {{ __('See the winner') }}
                    </x-site.rough-button>
                @endif
            </div>

            <p class="mt-6 text-sm text-bone/60">
                {{ __('Runs') }}: {{ Dates::format($contest->starts_at) }} → {{ Dates::format($contest->ends_at) }}
            </p>
        </div>

        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-ink"></div>
        </div>
    </section>

    {{-- Body --}}
    <section class="bg-bone pt-20 md:pt-28 pb-14 md:pb-20">
        <div class="mx-auto max-w-7xl px-4 md:px-6 grid lg:grid-cols-5 gap-10">
            <div class="lg:col-span-3 space-y-10">
                @if($contest->t('description'))
                    <div class="quill-content font-editorial text-lg leading-relaxed text-ink/85">
                        {!! $contest->t('description') !!}
                    </div>
                @endif

                <div id="terms" class="brush-card bg-bone p-6 md:p-8 scroll-mt-28">
                    <h2 class="font-display text-2xl font-extrabold uppercase">{{ __('Terms & conditions') }}</h2>
                    @if($contest->t('terms'))
                        <div class="quill-content mt-4 text-sm leading-relaxed text-ink/80">{!! $contest->t('terms') !!}</div>
                    @else
                        <p class="mt-3 text-sm text-ink/70">{{ __('Full terms are published before entries open.') }}</p>
                    @endif
                    <p class="mt-5 border-t-2 border-dashed border-ink/25 pt-4 text-xs text-ink/55">
                        {{ __('One entry per email address. The winner is drawn at random and contacted by email at the address given.') }}
                    </p>
                </div>
            </div>

            {{-- Entry column --}}
            <div class="lg:col-span-2">
                <div class="lg:sticky lg:top-28 space-y-5">
                    @if($state === Contest::STATE_ACTIVE)
                        @include('site.contests._entry-form')
                    @elseif($state === Contest::STATE_SCHEDULED)
                        <div class="brush-card bg-grass p-6 md:p-8 text-center">
                            <div class="font-display text-2xl font-black uppercase">{{ __('Not open yet') }}</div>
                            <p class="mt-2 text-ink/75">{{ __('Entries open on :date. Come back then — or follow us so you do not miss it.', ['date' => Dates::format($contest->starts_at)]) }}</p>
                            <div class="mt-5 flex justify-center"><x-site.countdown :to="$contest->starts_at"/></div>
                        </div>
                    @elseif($contest->isDrawn())
                        <div class="brush-card bg-ink text-bone p-6 md:p-8 text-center">
                            <div class="font-display text-2xl font-black uppercase">{{ __('This contest is over') }}</div>
                            <p class="mt-2 text-bone/75">{{ __('The winner has been drawn and announced.') }}</p>
                            <a href="{{ route('contests.winner', ['contest' => $contest->slug]) }}" class="btn-rough is-fire mt-5">{{ __('See the winner') }}</a>
                        </div>
                    @else
                        <div class="brush-card bg-bone p-6 md:p-8 text-center">
                            <div class="font-display text-2xl font-black uppercase">{{ __('Entries are closed') }}</div>
                            <p class="mt-2 text-ink/75">{{ __('The draw is happening — the winner will be announced on this page shortly.') }}</p>
                        </div>
                    @endif

                    <div class="brush-card bg-bone p-5">
                        <div class="text-xs font-bold uppercase tracking-widest text-ink/55">{{ __('How the draw works') }}</div>
                        <ul class="mt-3 space-y-2 text-sm text-ink/75">
                            <li class="flex gap-2"><span class="text-fire font-black">1.</span>{{ __('Every valid entry goes in the hat, once per email.') }}</li>
                            <li class="flex gap-2"><span class="text-fire font-black">2.</span>{{ __('At closing time the system draws at random.') }}</li>
                            <li class="flex gap-2"><span class="text-fire font-black">3.</span>{{ __('The winner is emailed and announced here.') }}</li>
                            @if($contest->runners_up_count)
                                <li class="flex gap-2"><span class="text-fire font-black">4.</span>{{ __('Runners-up are drawn too, in case the winner does not reply.') }}</li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layout>
