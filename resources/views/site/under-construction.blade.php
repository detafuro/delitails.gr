@php
    $siteName = $site['site_name'] ?? config('app.name', 'Delitails');
    $faviconPath = $site['favicon'] ?? null;
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $siteName }} — {{ __('Under construction') }}</title>
    @if($faviconPath)<link rel="icon" href="{{ asset('storage/'.$faviconPath) }}">@endif
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-bone text-ink antialiased selection:bg-fire selection:text-bone overflow-x-hidden">

    <main class="min-h-screen flex items-center justify-center px-4 py-16">
        <div class="w-full max-w-xl text-center">
            @if(!empty($site['logo']))
                <img src="{{ asset('storage/'.$site['logo']) }}" alt="{{ $siteName }}" class="h-20 mx-auto mb-8">
            @else
                <div class="font-display text-3xl font-black uppercase mb-8">{{ $siteName }}</div>
            @endif

            <div class="brush-card bg-bone p-8 md:p-12">
                <div class="inline-block bg-fire text-bone font-display text-xs font-extrabold uppercase tracking-[0.2em] px-3 py-1.5 -rotate-2 mb-6">
                    {{ __('Coming soon') }}
                </div>
                <h1 class="font-display text-4xl md:text-5xl font-black uppercase leading-none">
                    {!! __('Under<br>construction') !!}
                </h1>
                <p class="font-editorial italic text-ink/70 leading-relaxed mt-6">
                    {{ __("We're baking something loud behind the scenes. The full site will be live soon — sniff back shortly.") }}
                </p>

                @if(!empty($site['contact_email']))
                    <a href="mailto:{{ $site['contact_email'] }}" class="btn-rough is-fire mt-8">{{ __('Get in touch') }}</a>
                @endif

                @if(!empty($site['under_construction_passcode']))
                    <details class="mt-8 border-t-2 border-dashed border-ink/30 pt-6" @if($errors->has('passcode')) open @endif>
                        <summary class="btn-rough is-ghost is-sm cursor-pointer list-none inline-flex">{{ __('Guest login') }}</summary>
                        <form method="POST" action="{{ route('construction.unlock') }}" class="mt-4 flex flex-col sm:flex-row justify-center gap-2">
                            @csrf
                            <input type="password" name="passcode" required autocomplete="off" placeholder="{{ __('Passcode') }}"
                                   class="border-2 border-ink bg-bone px-3 py-2 font-sans text-ink focus:outline-none focus:ring-2 focus:ring-fire/60">
                            <button type="submit" class="btn-rough is-ink is-sm justify-center">{{ __('Enter') }}</button>
                        </form>
                        @error('passcode')
                            <p class="mt-2 text-sm font-bold text-fire">{{ $message }}</p>
                        @enderror
                    </details>
                @endif
            </div>

            @php $otherLocale = app()->getLocale() === 'el' ? 'en' : 'el'; @endphp
            <div class="mt-6">
                <a href="{{ route('lang.switch', $otherLocale) }}" class="text-xs font-bold uppercase tracking-widest text-ink/60 hover:text-fire underline underline-offset-4">
                    {{ $otherLocale === 'en' ? 'English' : 'Ελληνικά' }}
                </a>
            </div>

            <div class="mt-8 flex justify-center gap-2">
                @foreach([
                    'social_facebook' => 'Facebook',
                    'social_instagram' => 'Instagram',
                    'social_tiktok' => 'TikTok',
                    'social_youtube' => 'YouTube',
                ] as $key => $label)
                    @if(!empty($site[$key]))
                        <a href="{{ $site[$key] }}" target="_blank" rel="noopener" aria-label="{{ $label }}" class="inline-flex h-10 w-10 items-center justify-center border-2 border-ink hover:bg-fire hover:border-fire hover:text-bone">
                            <x-social-icon :name="str_replace('social_', '', $key)" />
                        </a>
                    @endif
                @endforeach
            </div>
        </div>
    </main>

</body>
</html>
