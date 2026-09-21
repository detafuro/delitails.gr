@php
    use App\Support\Turnstile;
    $fields = $contest->fieldDefinitions();
    $inputClass = 'w-full border-2 border-ink bg-bone px-3 py-2 focus:outline-none focus:ring-2 focus:ring-fire/60';
    $labelClass = 'block text-xs font-bold uppercase tracking-wider text-ink/70 mb-1';
@endphp

@if(Turnstile::enabled())
    @push('head')
        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
    @endpush
@endif

{{-- Sits on the dark hero: a hard fire-coloured offset instead of the site's ink one, and no hover tilt on a form. --}}
<div class="border-2 border-ink bg-bone text-ink p-6 md:p-8 shadow-[8px_8px_0_0_var(--color-fire)]"
     x-data="{
        sending: false,
        done: {{ session('success') ? 'true' : 'false' }},
        message: @js(session('success') ?? ''),
        errors: {},
        first(key) { return (this.errors[key] || [])[0] },
        async submit(form) {
            this.sending = true;
            this.errors = {};
            this.message = '';
            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });
                const data = await response.json().catch(() => ({}));
                if (response.ok && data.ok) {
                    this.done = true;
                    this.message = data.message;
                    form.reset();
                } else {
                    this.errors = data.errors || {};
                    this.message = data.message || @js(__('Something went wrong. Please try again.'));
                    if (window.turnstile) window.turnstile.reset();
                }
            } catch (e) {
                this.message = @js(__('Something went wrong. Please try again.'));
            }
            this.sending = false;
        }
     }">

    {{-- Confirmation (also covers the no-JS redirect, via session flash) --}}
    <div x-show="done" x-cloak class="border-2 border-ink bg-grass px-4 py-4">
        <div class="font-display text-xl font-black uppercase">{{ __('You are in the hat!') }}</div>
        <p class="mt-1 text-ink/80" x-text="message"></p>
    </div>
    @if(session('success'))
        <noscript>
            <div class="border-2 border-ink bg-grass px-4 py-4">
                <div class="font-display text-xl font-black uppercase">{{ __('You are in the hat!') }}</div>
                <p class="mt-1 text-ink/80">{{ session('success') }}</p>
            </div>
        </noscript>
    @endif

    {{-- No x-cloak here: without JS the form must still render. --}}
    <div x-show="!done">
        <h2 class="font-display text-2xl md:text-3xl font-extrabold uppercase">{{ __('Enter the contest') }}</h2>
        <p class="mt-1 text-sm text-ink/65">{{ __('One entry per email. Takes about 20 seconds.') }}</p>

        <div x-show="!done && message" x-cloak
             class="mt-4 border-2 border-ink bg-fire/15 px-4 py-3 text-sm font-semibold" x-text="message"></div>

        @if($errors->any())
            <div class="mt-4 border-2 border-ink bg-fire/15 px-4 py-3 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('contests.enter', ['contest' => $contest->slug]) }}"
              class="mt-5 grid sm:grid-cols-2 gap-5" @submit.prevent="submit($event.target)">
            @csrf
            <input type="text" name="hp_field" class="hidden" tabindex="-1" autocomplete="off" aria-hidden="true">

            <div class="sm:col-span-1">
                <label for="entry-name" class="{{ $labelClass }}">{{ __('Your name') }} *</label>
                <input id="entry-name" name="name" required autocomplete="name" value="{{ old('name') }}" class="{{ $inputClass }}">
                <p class="mt-1 text-xs text-fire" x-show="first('name')" x-text="first('name')" x-cloak></p>
                @error('name')<p class="mt-1 text-xs text-fire">{{ $message }}</p>@enderror
            </div>

            <div class="sm:col-span-1">
                <label for="entry-email" class="{{ $labelClass }}">{{ __('Email') }} *</label>
                <input id="entry-email" name="email" type="email" required autocomplete="email" value="{{ old('email') }}" class="{{ $inputClass }}">
                <p class="mt-1 text-xs text-fire" x-show="first('email')" x-text="first('email')" x-cloak></p>
                @error('email')<p class="mt-1 text-xs text-fire">{{ $message }}</p>@enderror
            </div>

            @if($contest->collectsPhone())
                <div class="sm:col-span-1">
                    <label for="entry-phone" class="{{ $labelClass }}">{{ __('Phone') }} {{ $contest->phoneRequired() ? '*' : '' }}</label>
                    <input id="entry-phone" name="phone" type="tel" autocomplete="tel" value="{{ old('phone') }}"
                           @if($contest->phoneRequired()) required @endif class="{{ $inputClass }}">
                    <p class="mt-1 text-xs text-fire" x-show="first('phone')" x-text="first('phone')" x-cloak></p>
                    @error('phone')<p class="mt-1 text-xs text-fire">{{ $message }}</p>@enderror
                </div>
            @endif

            @foreach($fields as $field)
                @php
                    $name = 'extra['.$field['key'].']';
                    $id = 'entry-'.$field['key'];
                    $old = old('extra.'.$field['key']);
                @endphp
                <div class="{{ $field['type'] === 'textarea' ? 'sm:col-span-2' : 'sm:col-span-1' }}">
                    @if($field['type'] === 'checkbox')
                        <label class="flex items-start gap-3 cursor-pointer select-none pt-1">
                            <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked($old)
                                   @if($field['required']) required @endif
                                   class="mt-0.5 h-5 w-5 border-2 border-ink accent-fire cursor-pointer">
                            <span class="text-sm font-semibold">{{ $field['label'] }} @if($field['required'])<span class="text-fire">*</span>@endif</span>
                        </label>
                    @else
                        <label for="{{ $id }}" class="{{ $labelClass }}">{{ $field['label'] }} {{ $field['required'] ? '*' : '' }}</label>
                        @if($field['type'] === 'textarea')
                            <textarea id="{{ $id }}" name="{{ $name }}" rows="3" @if($field['required']) required @endif class="{{ $inputClass }}">{{ $old }}</textarea>
                        @elseif($field['type'] === 'select')
                            <select id="{{ $id }}" name="{{ $name }}" @if($field['required']) required @endif class="{{ $inputClass }}">
                                <option value="">{{ __('Choose…') }}</option>
                                @foreach($field['options'] as $option)
                                    <option value="{{ $option }}" @selected($old === $option)>{{ $option }}</option>
                                @endforeach
                            </select>
                        @else
                            <input id="{{ $id }}" name="{{ $name }}" value="{{ $old }}" @if($field['required']) required @endif class="{{ $inputClass }}">
                        @endif
                    @endif
                    <p class="mt-1 text-xs text-fire" x-show="first('extra.{{ $field['key'] }}')" x-text="first('extra.{{ $field['key'] }}')" x-cloak></p>
                    @error('extra.'.$field['key'])<p class="mt-1 text-xs text-fire">{{ $message }}</p>@enderror
                </div>
            @endforeach

            {{-- Consents --}}
            <div class="sm:col-span-2 space-y-3 border-t-2 border-dashed border-ink/25 pt-4">
                <label class="flex items-start gap-3 cursor-pointer select-none">
                    <input type="checkbox" name="accept_terms" value="1" required @checked(old('accept_terms'))
                           class="mt-0.5 h-5 w-5 border-2 border-ink accent-fire cursor-pointer">
                    <span class="text-sm">
                        {!! __('I accept the <a href=":url" class="font-bold underline hover:text-fire">terms &amp; conditions</a> of this contest.', ['url' => '#terms']) !!}
                        <span class="text-fire">*</span>
                    </span>
                </label>
                <p class="text-xs text-fire" x-show="first('accept_terms')" x-text="first('accept_terms')" x-cloak></p>
                @error('accept_terms')<p class="text-xs text-fire">{{ $message }}</p>@enderror

                @if($contest->newsletter_opt_in)
                    <label class="flex items-start gap-3 cursor-pointer select-none">
                        <input type="checkbox" name="marketing_consent" value="1" @checked(old('marketing_consent'))
                               class="mt-0.5 h-5 w-5 border-2 border-ink accent-fire cursor-pointer">
                        <span class="text-sm">{{ __('Yes, email me treats, news and future contests. (Optional — you can unsubscribe any time.)') }}</span>
                    </label>
                @endif

                <p class="text-xs text-ink/55">
                    {{ __('We use your details only to run this contest and contact the winner. If you win, your first name and last initial may be published on the announcement page.') }}
                </p>
            </div>

            @if(Turnstile::enabled())
                <div class="sm:col-span-2">
                    <div class="cf-turnstile" data-sitekey="{{ Turnstile::siteKey() }}" data-theme="light"
                         data-language="{{ app()->getLocale() }}"></div>
                    <p class="mt-1 text-xs text-fire" x-show="first('cf-turnstile-response')" x-text="first('cf-turnstile-response')" x-cloak></p>
                </div>
            @endif

            <div class="sm:col-span-2 flex items-center gap-4">
                <button type="submit" class="btn-rough is-fire" :disabled="sending">
                    <span x-show="!sending">{{ __('Enter the contest') }}</span>
                    <span x-show="sending" x-cloak>{{ __('Sending…') }}</span>
                </button>
                <span class="text-xs text-ink/55">{{ __('Closes') }} {{ \App\Support\Dates::format($contest->ends_at) }}</span>
            </div>
        </form>
    </div>
</div>
