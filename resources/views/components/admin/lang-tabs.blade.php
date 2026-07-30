{{-- Requires an Alpine scope with `lang` ('en'|'el') on an ancestor element. --}}
<div class="flex items-center gap-2">
    <button type="button" @click="lang='en'" :class="lang==='en' ? 'is-fire' : 'is-bone'" class="btn-rough is-sm">English</button>
    <button type="button" @click="lang='el'" :class="lang==='el' ? 'is-fire' : 'is-bone'" class="btn-rough is-sm">Ελληνικά</button>
    <span class="text-xs text-ink/50">Greek falls back to the English text when left empty.</span>
</div>
