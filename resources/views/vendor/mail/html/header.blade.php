@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
<img src="{{ rtrim(config('app.url'), '/') }}/storage/branding/logo-white.png"
     width="190" class="logo" alt="{{ \App\Support\Seo::siteName() }}">
</a>
</td>
</tr>
