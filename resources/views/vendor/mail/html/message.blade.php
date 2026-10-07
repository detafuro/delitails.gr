@php $siteName = \App\Support\Seo::siteName(); @endphp
<x-mail::layout>
{{-- Header --}}
<x-slot:header>
<x-mail::header :url="rtrim(config('app.url'), '/').'/'.app()->getLocale()">
{{ $siteName }}
</x-mail::header>
</x-slot:header>

{{-- Body --}}
{!! $slot !!}

{{-- Subcopy --}}
@isset($subcopy)
<x-slot:subcopy>
<x-mail::subcopy>
{!! $subcopy !!}
</x-mail::subcopy>
</x-slot:subcopy>
@endisset

{{-- Footer --}}
<x-slot:footer>
<x-mail::footer>
**{{ $siteName }}** — {{ __('Treats with attitude.') }}
@if(\App\Models\Setting::get('contact_email'))

[{{ \App\Models\Setting::get('contact_email') }}](mailto:{{ \App\Models\Setting::get('contact_email') }}) · [delitails.gr]({{ rtrim(config('app.url'), '/').'/'.app()->getLocale() }})
@endif

© {{ date('Y') }} {{ $siteName }}. {{ __('All rights reserved.') }}
</x-mail::footer>
</x-slot:footer>
</x-mail::layout>
