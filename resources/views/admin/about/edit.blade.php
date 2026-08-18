@php
    // Built-in copy, shown as placeholders so it's clear what the fallback is.
    $defaults = [
        'about_what_label' => 'What we do',
        'about_what_heading' => 'Just carefully crafted treats you can trust.',
        'about_what_lead' => 'We believe dogs are more than pets—they are family. And just like every member of the family, they deserve the very best.',
        'about_what_body' => "That belief is what inspired Delitails.\n\nWe create premium single-protein chews, training treats, and natural sausages made with simplicity, honesty, and quality at the heart of everything we do.\n\nNo unnecessary fillers. No confusing ingredient lists. Just carefully crafted treats you can trust.",
        'about_philosophy_label' => 'Our philosophy',
        'about_philosophy_heading' => 'See the difference in every wag.',
        'about_philosophy_lead' => 'Our philosophy is simple: better treats lead to happier, healthier dogs—and you can see the difference in every wag.',
        'about_philosophy_body' => 'We are not just another brand—we are a brand created by a producer. Every Delitails product is crafted by us, giving us full control over quality, consistency, and sourcing. This means complete transparency in everything we offer, from the first ingredient to the final treat.',
        'about_bones_label' => 'What we stand on',
        'about_bones_heading' => 'Our four bones',
        'about_bone_1_title' => 'Real food',   'about_bone_1_text' => 'No fillers, no fluff. If it does not earn its place, it does not go in.',
        'about_bone_2_title' => 'Small batch', 'about_bone_2_text' => 'We bake in runs, not factories. Fresher, better, louder.',
        'about_bone_3_title' => 'Pets first',  'about_bone_3_text' => 'Every recipe is approved by the toughest critics on four legs.',
        'about_bone_4_title' => 'Local roots', 'about_bone_4_text' => 'Made close to home, sourced from people we shake hands with.',
    ];
    $v = fn (string $key) => $settings[$key] ?? '';
    $ph = fn (string $key) => $defaults[$key] ?? '';
@endphp
<x-admin.layout title="About page" subtitle="Texts & photos for the About page — What we do, Our philosophy, Our four bones">
    <form method="POST" action="{{ route('admin.about.update') }}" enctype="multipart/form-data"
          x-data="{tab: 'what'}" class="space-y-6">
        @csrf @method('PUT')

        <nav class="flex flex-wrap gap-2">
            @foreach(['what' => 'What we do', 'philosophy' => 'Our philosophy', 'bones' => 'Our four bones'] as $key => $label)
                <button type="button" @click="tab='{{ $key }}'"
                        :class="tab==='{{ $key }}' ? 'is-fire' : 'is-bone'"
                        class="btn-rough is-sm">{{ $label }}</button>
            @endforeach
            <span class="self-center text-xs text-ink/50">Empty fields fall back to the built-in copy shown as placeholder.</span>
        </nav>

        {{-- What we do --}}
        <section x-show="tab==='what'" class="brush-card p-6 space-y-5" x-data="{ lang: 'en' }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="font-display text-lg font-black uppercase">What we do</h3>
                <x-admin.lang-tabs/>
            </div>
            <div x-show="lang==='en'" class="space-y-5">
                <x-admin.form-input name="about_what_label" label="Section label" :value="$v('about_what_label')" :placeholder="$ph('about_what_label')"/>
                <x-admin.form-input name="about_what_heading" label="Heading" :value="$v('about_what_heading')" :placeholder="$ph('about_what_heading')"/>
                <x-admin.textarea name="about_what_lead" label="Lead (italic intro)" :value="$v('about_what_lead')" :placeholder="$ph('about_what_lead')" rows="2"/>
                <x-admin.textarea name="about_what_body" label="Body" :value="$v('about_what_body')" :placeholder="$ph('about_what_body')" rows="7"
                    hint="Separate paragraphs with a blank line."/>
            </div>
            <div x-show="lang==='el'" x-cloak class="space-y-5">
                <x-admin.form-input name="about_what_label_el" label="Section label (Ελληνικά)" :value="$v('about_what_label_el')"/>
                <x-admin.form-input name="about_what_heading_el" label="Heading (Ελληνικά)" :value="$v('about_what_heading_el')"/>
                <x-admin.textarea name="about_what_lead_el" label="Lead (Ελληνικά)" :value="$v('about_what_lead_el')" rows="2"/>
                <x-admin.textarea name="about_what_body_el" label="Body (Ελληνικά)" :value="$v('about_what_body_el')" rows="7"
                    hint="Separate paragraphs with a blank line."/>
            </div>
            <x-admin.image-upload name="about_what_image" label="Photo"
                :currentPath="$settings['about_what_image'] ?? null"
                hint="Square photo next to the text — 1200×1200 recommended. Falls back to the default photo when empty."/>
        </section>

        {{-- Our philosophy --}}
        <section x-show="tab==='philosophy'" x-cloak class="brush-card p-6 space-y-5" x-data="{ lang: 'en' }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="font-display text-lg font-black uppercase">Our philosophy</h3>
                <x-admin.lang-tabs/>
            </div>
            <div x-show="lang==='en'" class="space-y-5">
                <x-admin.form-input name="about_philosophy_label" label="Section label" :value="$v('about_philosophy_label')" :placeholder="$ph('about_philosophy_label')"/>
                <x-admin.form-input name="about_philosophy_heading" label="Heading" :value="$v('about_philosophy_heading')" :placeholder="$ph('about_philosophy_heading')"/>
                <x-admin.textarea name="about_philosophy_lead" label="Lead (italic intro)" :value="$v('about_philosophy_lead')" :placeholder="$ph('about_philosophy_lead')" rows="2"/>
                <x-admin.textarea name="about_philosophy_body" label="Body" :value="$v('about_philosophy_body')" :placeholder="$ph('about_philosophy_body')" rows="6"
                    hint="Separate paragraphs with a blank line."/>
            </div>
            <div x-show="lang==='el'" x-cloak class="space-y-5">
                <x-admin.form-input name="about_philosophy_label_el" label="Section label (Ελληνικά)" :value="$v('about_philosophy_label_el')"/>
                <x-admin.form-input name="about_philosophy_heading_el" label="Heading (Ελληνικά)" :value="$v('about_philosophy_heading_el')"/>
                <x-admin.textarea name="about_philosophy_lead_el" label="Lead (Ελληνικά)" :value="$v('about_philosophy_lead_el')" rows="2"/>
                <x-admin.textarea name="about_philosophy_body_el" label="Body (Ελληνικά)" :value="$v('about_philosophy_body_el')" rows="6"
                    hint="Separate paragraphs with a blank line."/>
            </div>
            <x-admin.image-upload name="about_philosophy_image" label="Photo"
                :currentPath="$settings['about_philosophy_image'] ?? null"
                hint="Square photo next to the text — 1200×1200 recommended. Falls back to the default photo when empty."/>
        </section>

        {{-- Our four bones --}}
        <section x-show="tab==='bones'" x-cloak class="brush-card p-6 space-y-5" x-data="{ lang: 'en' }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="font-display text-lg font-black uppercase">Our four bones</h3>
                <x-admin.lang-tabs/>
            </div>
            <div x-show="lang==='en'" class="space-y-5">
                <div class="grid sm:grid-cols-2 gap-5">
                    <x-admin.form-input name="about_bones_label" label="Section label" :value="$v('about_bones_label')" :placeholder="$ph('about_bones_label')"/>
                    <x-admin.form-input name="about_bones_heading" label="Heading" :value="$v('about_bones_heading')" :placeholder="$ph('about_bones_heading')"/>
                </div>
                @for($i = 1; $i <= 4; $i++)
                    <div class="border-t-2 border-dashed border-ink/30 pt-5 space-y-3">
                        <div class="font-display text-sm font-black uppercase text-fire">Bone 0{{ $i }}</div>
                        <x-admin.form-input name="about_bone_{{ $i }}_title" label="Title" :value="$v('about_bone_'.$i.'_title')" :placeholder="$ph('about_bone_'.$i.'_title')"/>
                        <x-admin.textarea name="about_bone_{{ $i }}_text" label="Text" :value="$v('about_bone_'.$i.'_text')" :placeholder="$ph('about_bone_'.$i.'_text')" rows="2"/>
                    </div>
                @endfor
            </div>
            <div x-show="lang==='el'" x-cloak class="space-y-5">
                <div class="grid sm:grid-cols-2 gap-5">
                    <x-admin.form-input name="about_bones_label_el" label="Section label (Ελληνικά)" :value="$v('about_bones_label_el')"/>
                    <x-admin.form-input name="about_bones_heading_el" label="Heading (Ελληνικά)" :value="$v('about_bones_heading_el')"/>
                </div>
                @for($i = 1; $i <= 4; $i++)
                    <div class="border-t-2 border-dashed border-ink/30 pt-5 space-y-3">
                        <div class="font-display text-sm font-black uppercase text-fire">Bone 0{{ $i }}</div>
                        <x-admin.form-input name="about_bone_{{ $i }}_title_el" label="Title (Ελληνικά)" :value="$v('about_bone_'.$i.'_title_el')"/>
                        <x-admin.textarea name="about_bone_{{ $i }}_text_el" label="Text (Ελληνικά)" :value="$v('about_bone_'.$i.'_text_el')" rows="2"/>
                    </div>
                @endfor
            </div>
        </section>

        <div class="flex gap-3">
            <button type="submit" class="btn-rough is-fire">Save</button>
            <a href="{{ route('about') }}" target="_blank" class="btn-rough is-bone">View page ↗</a>
        </div>
    </form>
</x-admin.layout>
