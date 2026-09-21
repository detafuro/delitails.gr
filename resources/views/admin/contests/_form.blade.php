@csrf
@php
    use App\Models\Contest;
    $fieldRows = old('fields', $contest->extra_fields ?: []);
@endphp
<div class="grid lg:grid-cols-3 gap-6">
    {{-- Content --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="brush-card p-5 space-y-5" x-data="{ lang: 'en' }" @invalid.capture="lang = 'en'">
            <x-admin.lang-tabs/>

            <div x-show="lang==='en'" class="space-y-5">
                <x-admin.form-input name="title" label="Title" :value="$contest->title" required/>
                <x-admin.form-input name="slug" label="Slug" :value="$contest->slug" hint="Leave blank to auto-generate. Shared by both languages."/>
                <x-admin.form-input name="prize" label="Prize" :value="$contest->prize" hint="One line, shown big on the landing page. E.g. “A month of treats + a Delitails hoodie”."/>
                <x-admin.textarea name="excerpt" label="Teaser" :value="$contest->excerpt" rows="2" hint="Short line for the contests list and social previews."/>
                <x-admin.rich-textarea name="description" label="Description" :value="$contest->description"/>
                <x-admin.rich-textarea name="terms" label="Terms &amp; conditions" :value="$contest->terms"
                    hint="Your legal text for this contest. Shown on the landing page and linked from the entry form."/>
                <x-admin.rich-textarea name="winner_message" label="Winner page message" :value="$contest->winner_message"
                    hint="Optional intro on the winner announcement page. The winner’s name is added automatically."/>
                <div class="border-t-2 border-dashed border-ink/30 pt-4">
                    <h3 class="font-display text-lg font-extrabold uppercase mb-2">SEO</h3>
                    <x-admin.form-input name="seo_title" label="SEO title" :value="$contest->seo_title"/>
                    <x-admin.textarea class="mt-3" name="seo_description" label="SEO description" :value="$contest->seo_description" rows="3"/>
                </div>
            </div>

            <div x-show="lang==='el'" x-cloak class="space-y-5">
                <x-admin.form-input name="el[title]" label="Title (Ελληνικά)" :value="$contest->translation('title')"/>
                <x-admin.form-input name="el[prize]" label="Prize (Ελληνικά)" :value="$contest->translation('prize')"/>
                <x-admin.textarea name="el[excerpt]" label="Teaser (Ελληνικά)" :value="$contest->translation('excerpt')" rows="2"/>
                <x-admin.rich-textarea name="el[description]" label="Description (Ελληνικά)" :value="$contest->translation('description')"/>
                <x-admin.rich-textarea name="el[terms]" label="Terms &amp; conditions (Ελληνικά)" :value="$contest->translation('terms')"/>
                <x-admin.rich-textarea name="el[winner_message]" label="Winner page message (Ελληνικά)" :value="$contest->translation('winner_message')"/>
                <div class="border-t-2 border-dashed border-ink/30 pt-4">
                    <h3 class="font-display text-lg font-extrabold uppercase mb-2">SEO</h3>
                    <x-admin.form-input name="el[seo_title]" label="SEO title (Ελληνικά)" :value="$contest->translation('seo_title')"/>
                    <x-admin.textarea class="mt-3" name="el[seo_description]" label="SEO description (Ελληνικά)" :value="$contest->translation('seo_description')" rows="3"/>
                </div>
            </div>
        </div>

        {{-- Entry form builder --}}
        <div class="brush-card p-5 space-y-5">
            <div>
                <h3 class="font-display text-lg font-extrabold uppercase">Entry form</h3>
                <p class="text-xs text-ink/60">Name and email are always asked. Everything below is per contest.</p>
            </div>

            <div class="grid sm:grid-cols-2 gap-5">
                <x-admin.form-input name="name_label" label="Name field label" :value="$contest->name_label"
                    hint="Leave blank for “Your name”. E.g. “Company name” for business contests."/>
                <x-admin.form-input name="el[name_label]" label="Name field label (Ελληνικά)" :value="$contest->translation('name_label')"
                    hint="Κενό = «Το όνομά σας»."/>
                <x-admin.select name="phone_field" label="Phone number"
                    :options="[Contest::PHONE_OFF => 'Do not ask', Contest::PHONE_OPTIONAL => 'Ask (optional)', Contest::PHONE_REQUIRED => 'Ask (required)']"
                    :value="$contest->phone_field ?? Contest::PHONE_OPTIONAL" required/>
                <div class="flex items-end pb-1">
                    <x-admin.toggle name="newsletter_opt_in" label="Newsletter opt-in checkbox"
                        :value="$contest->newsletter_opt_in ?? true"
                        hint="Separate, unticked consent. Only ticked entrants join the subscriber list."/>
                </div>
            </div>

            <div class="border-t-2 border-dashed border-ink/30 pt-4"
                 x-data="{ rows: {{ Js::from(array_values((array) $fieldRows)) }},
                           add() { this.rows.push({label:'',label_el:'',key:'',type:'text',options:'',required:false}) } }">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h4 class="font-display font-extrabold uppercase">Extra questions</h4>
                    <button type="button" class="btn-rough is-bone is-sm" @click="add()">+ Add question</button>
                </div>

                <template x-if="rows.length === 0">
                    <p class="text-sm text-ink/50">No extra questions — just name, email@if($contest->collectsPhone()) and phone@endif.</p>
                </template>

                <template x-for="(row, i) in rows" :key="i">
                    <div class="mb-3 border-2 border-ink/25 bg-bone/60 p-3 space-y-3">
                        <div class="grid sm:grid-cols-2 gap-3">
                            <label class="block">
                                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/70">Question (English)</span>
                                <input type="text" :name="`fields[${i}][label]`" x-model="row.label" placeholder="Dog's name"
                                       class="w-full border-2 border-ink bg-bone px-3 py-2">
                            </label>
                            <label class="block">
                                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/70">Question (Ελληνικά)</span>
                                <input type="text" :name="`fields[${i}][label_el]`" x-model="row.label_el" placeholder="Όνομα σκύλου"
                                       class="w-full border-2 border-ink bg-bone px-3 py-2">
                            </label>
                        </div>
                        <div class="grid sm:grid-cols-3 gap-3">
                            <label class="block">
                                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/70">Type</span>
                                <select :name="`fields[${i}][type]`" x-model="row.type" class="w-full border-2 border-ink bg-bone px-3 py-2">
                                    @foreach(Contest::FIELD_TYPES as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                            </label>
                            <label class="block">
                                <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/70">Key</span>
                                <input type="text" :name="`fields[${i}][key]`" x-model="row.key" placeholder="auto"
                                       class="w-full border-2 border-ink bg-bone px-3 py-2">
                            </label>
                            <div class="flex items-end justify-between gap-3 pb-2">
                                <label class="inline-flex items-center gap-2 cursor-pointer">
                                    <input type="hidden" :name="`fields[${i}][required]`" value="0">
                                    <input type="checkbox" :name="`fields[${i}][required]`" value="1" x-model="row.required"
                                           class="h-5 w-5 border-2 border-ink accent-fire">
                                    <span class="text-sm font-bold uppercase tracking-wider">Required</span>
                                </label>
                                <button type="button" class="btn-rough is-fire is-sm" @click="rows.splice(i,1)">Remove</button>
                            </div>
                        </div>
                        <label class="block" x-show="row.type === 'select'" x-cloak>
                            <span class="mb-1 block text-xs font-bold uppercase tracking-wider text-ink/70">Dropdown options (one per line)</span>
                            <textarea :name="`fields[${i}][options]`" x-model="row.options" rows="3"
                                      class="w-full border-2 border-ink bg-bone px-3 py-2"></textarea>
                        </label>
                    </div>
                </template>
            </div>
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="space-y-5">
        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Schedule</h3>
            @if($contest->exists)
                <div class="flex items-center gap-2">
                    <x-admin.contest-state :contest="$contest"/>
                    <span class="text-xs text-ink/55">{{ $contest->entries()->count() }} entries</span>
                </div>
            @endif
            <x-admin.toggle name="is_published" label="Published"
                :value="$contest->is_published ?? false"
                hint="Off keeps the contest invisible on the site, whatever the dates say."/>
            <x-admin.form-input name="starts_at" label="Opens" type="datetime-local" required
                :value="optional(\App\Support\Dates::local($contest->starts_at))->format('Y-m-d\TH:i')"/>
            <x-admin.form-input name="ends_at" label="Closes" type="datetime-local" required
                :value="optional(\App\Support\Dates::local($contest->ends_at))->format('Y-m-d\TH:i')"
                hint="Entries close automatically at this time."/>
            <div class="text-xs text-ink/55">Times are Greek time ({{ \App\Support\Dates::tz() }}).</div>
        </div>

        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Draw</h3>
            <x-admin.toggle name="auto_draw" label="Draw automatically at closing"
                :value="$contest->auto_draw ?? true"
                hint="Off means you press the Draw button yourself."/>
            <div class="grid grid-cols-2 gap-3">
                <x-admin.form-input name="winners_count" label="Winners" type="number" min="1" max="20"
                    :value="$contest->winners_count ?? 1" required/>
                <x-admin.form-input name="runners_up_count" label="Runners-up" type="number" min="0" max="20"
                    :value="$contest->runners_up_count ?? 2" required/>
            </div>
        </div>

        <div class="brush-card p-5 space-y-4">
            <h3 class="font-display text-lg font-extrabold uppercase">Banner</h3>
            <x-admin.image-upload name="banner_image" :currentPath="$contest->banner_image"
                hint="Landscape works best (16:9). Shown in the hero and on social shares."/>
        </div>

        @if($contest->exists)
            <div class="brush-card p-5 space-y-3">
                <h3 class="font-display text-lg font-extrabold uppercase">Links</h3>
                <a href="{{ route('contests.show', ['locale' => 'el', 'contest' => $contest->slug]) }}" target="_blank"
                   class="block text-sm font-semibold hover:text-fire">Landing page (EL) ↗</a>
                <a href="{{ route('contests.show', ['locale' => 'en', 'contest' => $contest->slug]) }}" target="_blank"
                   class="block text-sm font-semibold hover:text-fire">Landing page (EN) ↗</a>
                @if($contest->isDrawn())
                    <a href="{{ route('contests.winner', ['locale' => 'el', 'contest' => $contest->slug]) }}" target="_blank"
                       class="block text-sm font-semibold hover:text-fire">Winner announcement ↗</a>
                @endif
                <a href="{{ route('admin.contests.entries.index', $contest) }}" class="block text-sm font-semibold hover:text-fire">Entries →</a>
            </div>
        @endif
    </div>
</div>

<div class="mt-6 flex gap-3">
    <button type="submit" class="btn-rough is-fire">{{ $contest->exists ? 'Update contest' : 'Create contest' }}</button>
    <a href="{{ route('admin.contests.index') }}" class="btn-rough is-bone">Cancel</a>
</div>
