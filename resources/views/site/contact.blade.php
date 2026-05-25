@php $title = 'Contact — '.($site['site_name'] ?? config('app.name')); @endphp
<x-layout title="{{ $title }}" description="Get in touch. We answer every message — usually with treats in hand.">
    <section class="bg-fire text-bone paper">
        <div class="mx-auto max-w-7xl px-4 md:px-6 py-16 md:py-20">
            <div class="text-bone/80 text-sm font-bold uppercase tracking-[0.3em]">Contact</div>
            <h1 class="mt-3 font-display text-5xl md:text-7xl font-black uppercase leading-[0.9]">
                Say hi. <span class="text-ink">Bring snacks.</span>
            </h1>
            <p class="mt-4 max-w-2xl font-editorial italic text-xl text-bone/85">
                Questions, compliments, wild ideas — drop us a line and we'll bark back, usually within a day.
            </p>
        </div>
        <div aria-hidden="true" class="relative">
            <div class="absolute top-full -mt-px left-0 right-0 h-10 paper torn-bottom bg-fire"></div>
        </div>
    </section>

    <section class="bg-bone py-16 md:py-20">
        <div class="mx-auto max-w-7xl px-4 md:px-6 grid lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 brush-card bg-bone p-6 md:p-8">
                <h2 class="font-display text-2xl md:text-3xl font-extrabold uppercase">Drop us a line</h2>

                @if(session('success'))
                    <div class="mt-4 inline-block border-2 border-ink bg-grass px-4 py-2 font-bold uppercase tracking-wider">{{ session('success') }}</div>
                @endif

                <form method="POST" action="{{ route('contact.send') }}" class="mt-6 grid sm:grid-cols-2 gap-5">
                    @csrf
                    <input type="text" name="hp_field" class="hidden" tabindex="-1" autocomplete="off">

                    <div class="sm:col-span-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-ink/70 mb-1">Your name *</label>
                        <input name="name" value="{{ old('name') }}" required class="w-full border-2 border-ink bg-bone px-3 py-2 focus:outline-none focus:ring-2 focus:ring-fire/60">
                        @error('name')<p class="mt-1 text-xs text-fire">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-ink/70 mb-1">Email *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required class="w-full border-2 border-ink bg-bone px-3 py-2 focus:outline-none focus:ring-2 focus:ring-fire/60">
                        @error('email')<p class="mt-1 text-xs text-fire">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-ink/70 mb-1">Phone</label>
                        <input name="phone" value="{{ old('phone') }}" class="w-full border-2 border-ink bg-bone px-3 py-2 focus:outline-none focus:ring-2 focus:ring-fire/60">
                    </div>

                    <div class="sm:col-span-1">
                        <label class="block text-xs font-bold uppercase tracking-wider text-ink/70 mb-1">Subject</label>
                        <input name="subject" value="{{ old('subject') }}" class="w-full border-2 border-ink bg-bone px-3 py-2 focus:outline-none focus:ring-2 focus:ring-fire/60">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-ink/70 mb-1">Message *</label>
                        <textarea name="message" rows="6" required class="w-full border-2 border-ink bg-bone px-3 py-2 focus:outline-none focus:ring-2 focus:ring-fire/60">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-fire">{{ $message }}</p>@enderror
                    </div>

                    <div class="sm:col-span-2">
                        <x-site.rough-button type="submit" variant="fire">Send message</x-site.rough-button>
                    </div>
                </form>
            </div>

            <aside class="space-y-5">
                <div class="brush-card bg-bone p-6">
                    <h3 class="font-display text-lg font-extrabold uppercase mb-3">Reach us</h3>
                    @if(!empty($site['contact_email']))
                        <div class="mb-3">
                            <div class="text-xs uppercase tracking-widest text-ink/55">Email</div>
                            <a href="mailto:{{ $site['contact_email'] }}" class="font-display font-bold hover:text-fire">{{ $site['contact_email'] }}</a>
                        </div>
                    @endif
                    @if(!empty($site['contact_phone']))
                        <div class="mb-3">
                            <div class="text-xs uppercase tracking-widest text-ink/55">Phone</div>
                            <a href="tel:{{ preg_replace('/[^+\d]/','',$site['contact_phone']) }}" class="font-display font-bold hover:text-fire">{{ $site['contact_phone'] }}</a>
                        </div>
                    @endif
                    @if(!empty($site['contact_address']))
                        <div>
                            <div class="text-xs uppercase tracking-widest text-ink/55">Address</div>
                            <div class="font-editorial italic text-ink/80">{{ $site['contact_address'] }}</div>
                        </div>
                    @endif
                </div>

                <div class="brush-card bg-grass p-6">
                    <h3 class="font-display text-lg font-extrabold uppercase mb-3">Follow the chaos</h3>
                    <div class="flex flex-wrap gap-2">
                        @foreach([
                            'social_facebook' => 'Facebook',
                            'social_instagram' => 'Instagram',
                            'social_tiktok' => 'TikTok',
                            'social_youtube' => 'YouTube',
                        ] as $key => $label)
                            @if(!empty($site[$key]))
                                <a href="{{ $site[$key] }}" target="_blank" rel="noopener" class="btn-rough is-ink is-sm">{{ $label }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>

                @if(!empty($site['map_embed']))
                    <div class="brush-card bg-bone p-2 overflow-hidden">
                        <div class="aspect-video">{!! $site['map_embed'] !!}</div>
                    </div>
                @endif
            </aside>
        </div>
    </section>
</x-layout>
