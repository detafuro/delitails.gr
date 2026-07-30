<x-admin.layout title="Translations" subtitle="Greek text for every string on the public site">
    <form method="POST" action="{{ route('admin.translations.update') }}"
          x-data="{ q: '', matches(text) { return this.q === '' || text.toLowerCase().includes(this.q.toLowerCase()) } }"
          class="space-y-6">
        @csrf @method('PUT')

        <div class="flex flex-wrap items-center gap-3">
            <input type="text" x-model="q" placeholder="Filter strings…"
                   class="w-full sm:w-96 border-2 border-ink bg-bone px-3 py-2 focus:outline-none focus:ring-2 focus:ring-fire/50">
            <span class="text-sm text-ink/60">{{ $strings->count() }} strings — edited ones are marked. Emptying a field or restoring the default removes the override.</span>
            <button type="submit" class="btn-rough is-fire ml-auto">Save translations</button>
        </div>

        <div class="space-y-3">
            @foreach($strings as $s)
                <div class="brush-card bg-bone p-4"
                     x-show="matches({{ Js::from($s['source'].' '.$s['value']) }})">
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <div class="text-[10px] font-bold uppercase tracking-widest text-ink/50 mb-1">English (source)</div>
                            <div class="text-sm text-ink/80 whitespace-pre-wrap break-words">{{ $s['source'] }}</div>
                        </div>
                        <div>
                            <div class="flex items-center gap-2 mb-1">
                                <span class="text-[10px] font-bold uppercase tracking-widest text-ink/50">Greek</span>
                                @if($s['overridden'])
                                    <span class="inline-block border border-ink bg-grass px-1.5 text-[10px] font-bold uppercase">Edited</span>
                                @endif
                            </div>
                            @if(mb_strlen($s['default']) > 80)
                                <textarea name="t[{{ $s['hash'] }}]" rows="{{ min(6, (int) ceil(mb_strlen($s['default']) / 90) + 1) }}"
                                          class="w-full border-2 {{ $s['overridden'] ? 'border-fire' : 'border-ink' }} bg-bone px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-fire/50">{{ $s['value'] }}</textarea>
                            @else
                                <input type="text" name="t[{{ $s['hash'] }}]" value="{{ $s['value'] }}"
                                       class="w-full border-2 {{ $s['overridden'] ? 'border-fire' : 'border-ink' }} bg-bone px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-fire/50">
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="flex gap-3">
            <button type="submit" class="btn-rough is-fire">Save translations</button>
        </div>
    </form>
</x-admin.layout>
