@php
    use App\Support\Dates;
    use App\Support\Seo;
    $title = Seo::title(__('Contests'));
@endphp
<x-layout :title="$title" :description="__('Win treats. Enter our latest contest — free, quick, and open to every good dog and cat in Greece.')">
    <section class="relative bg-ink text-bone paper">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-10 md:py-14">
            <div class="text-fire-light text-sm font-bold uppercase tracking-[0.3em]">{{ __('Contests') }}</div>
            <h1 class="mt-3 font-display text-5xl md:text-7xl font-black uppercase leading-[0.9]">
                {!! __('Free stuff. <span class="text-fire">No catch.</span>') !!}
            </h1>
            <p class="mt-4 max-w-2xl font-editorial italic text-xl text-bone/75">
                {{ __('Enter in twenty seconds, one entry per email, winners drawn at random and announced right here.') }}
            </p>
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-ink"></div>
        </div>
    </section>

    <section class="bg-bone pt-20 md:pt-28 pb-14 md:pb-20">
        <div class="mx-auto max-w-7xl px-4 md:px-6 space-y-14">
            {{-- Open --}}
            <div>
                <h2 class="font-display text-3xl font-black uppercase">{{ __('Open now') }}</h2>
                @if($open->isEmpty())
                    <p class="mt-3 font-editorial italic text-lg text-ink/65">
                        {{ __('Nothing running right now — the next one is never far off.') }}
                    </p>
                @else
                    <div class="mt-6 grid md:grid-cols-2 gap-6">
                        @foreach($open as $contest)
                            <a href="{{ route('contests.show', ['contest' => $contest->slug]) }}" class="brush-card bg-bone overflow-hidden block">
                                @if($contest->banner_image)
                                    <x-site.img :src="$contest->banner_image" alt="" sizes="(min-width: 768px) 50vw, 100vw"
                                                class="h-48 w-full object-cover border-b-2 border-ink"/>
                                @endif
                                <div class="p-6">
                                    <span class="inline-flex items-center gap-2 border-2 border-ink bg-grass px-2 py-0.5 text-[10px] font-bold uppercase tracking-wider">
                                        <span class="h-1.5 w-1.5 rounded-full bg-ink animate-pulse"></span>{{ __('Open for entries') }}
                                    </span>
                                    <h3 class="mt-3 font-display text-2xl font-black uppercase leading-tight">{{ $contest->t('title') }}</h3>
                                    @if($contest->t('prize'))
                                        <p class="mt-1 text-sm font-bold uppercase tracking-wider text-fire">{{ $contest->t('prize') }}</p>
                                    @endif
                                    @if($contest->t('excerpt'))
                                        <p class="mt-3 font-editorial italic text-ink/75">{{ $contest->t('excerpt') }}</p>
                                    @endif
                                    <div class="mt-5 flex items-center justify-between gap-3">
                                        <span class="text-xs uppercase tracking-widest text-ink/55">
                                            {{ __('Closes') }} {{ Dates::format($contest->ends_at, 'j M, H:i') }}
                                        </span>
                                        <span class="btn-rough is-fire is-sm">{{ __('Enter') }}</span>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Coming --}}
            @if($upcoming->isNotEmpty())
                <div>
                    <h2 class="font-display text-3xl font-black uppercase">{{ __('Coming up') }}</h2>
                    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($upcoming as $contest)
                            <a href="{{ route('contests.show', ['contest' => $contest->slug]) }}" class="brush-card bg-bone p-5 block">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-ink/55">{{ __('Opens') }} {{ Dates::format($contest->starts_at, 'j M, H:i') }}</div>
                                <h3 class="mt-2 font-display text-xl font-black uppercase leading-tight">{{ $contest->t('title') }}</h3>
                                @if($contest->t('prize'))<p class="mt-1 text-sm text-ink/70">{{ $contest->t('prize') }}</p>@endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Past --}}
            @if($past->isNotEmpty())
                <div>
                    <h2 class="font-display text-3xl font-black uppercase">{{ __('Past contests') }}</h2>
                    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
                        @foreach($past as $contest)
                            <a href="{{ $contest->isDrawn() ? route('contests.winner', ['contest' => $contest->slug]) : route('contests.show', ['contest' => $contest->slug]) }}"
                               class="brush-card bg-bone p-5 block">
                                <div class="text-[10px] font-bold uppercase tracking-wider text-ink/55">
                                    {{ $contest->isDrawn() ? __('Winner announced') : __('Closed') }} · {{ Dates::format($contest->ends_at, 'j M Y') }}
                                </div>
                                <h3 class="mt-2 font-display text-xl font-black uppercase leading-tight">{{ $contest->t('title') }}</h3>
                                @if($contest->isDrawn())
                                    <p class="mt-2 text-sm font-bold text-fire">{{ __('See the winner') }} →</p>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>
</x-layout>
