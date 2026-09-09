<x-mail::message>
# {{ __('Congratulations, :name!', ['name' => $entry->name]) }}

{{ __('Your entry in :contest was drawn as the winner.', ['contest' => $contest->t('title')]) }}

@if($contest->t('prize'))
**{{ __('Prize') }}:** {{ $contest->t('prize') }}
@endif

<x-mail::button :url="route('contests.winner', ['locale' => app()->getLocale(), 'contest' => $contest->slug])">
{{ __('See the announcement') }}
</x-mail::button>

{{ __('Reply to this email and we will arrange how to get your prize to you.') }}

{{ __('Thanks for playing,') }}<br>
{{ \App\Support\Seo::siteName() }}
</x-mail::message>
