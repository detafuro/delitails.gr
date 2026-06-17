<x-admin.layout title="Site settings" subtitle="Global branding, contact, SEO, hero & footer">
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
          x-data="{tab: 'brand'}" class="space-y-6">
        @csrf @method('PUT')

        <nav class="flex flex-wrap gap-2">
            @php
                $tabs = [
                    'brand' => 'Brand',
                    'contact' => 'Contact',
                    'hero' => 'Hero',
                    'newsletter' => 'Newsletter',
                    'announcement' => 'Announcement',
                    'seo' => 'SEO & scripts',
                    'footer' => 'Footer',
                ];
            @endphp
            @foreach($tabs as $key => $label)
                <button type="button" @click="tab='{{ $key }}'"
                        :class="tab==='{{ $key }}' ? 'is-fire' : 'is-bone'"
                        class="btn-rough is-sm">{{ $label }}</button>
            @endforeach
        </nav>

        {{-- Brand --}}
        <section x-show="tab==='brand'" class="brush-card p-6 space-y-5">
            <x-admin.form-input name="site_name" label="Site name" :value="$settings['site_name'] ?? config('app.name')"/>
            <div class="grid sm:grid-cols-3 gap-5">
                <x-admin.image-upload name="logo" label="Logo" :currentPath="$settings['logo'] ?? null"/>
                <x-admin.image-upload name="footer_logo" label="Footer logo" :currentPath="$settings['footer_logo'] ?? null"/>
                <x-admin.image-upload name="favicon" label="Favicon" :currentPath="$settings['favicon'] ?? null"/>
            </div>
        </section>

        {{-- Contact --}}
        <section x-show="tab==='contact'" x-cloak class="brush-card p-6 space-y-5">
            <div class="grid sm:grid-cols-2 gap-5">
                <x-admin.form-input name="contact_email" label="Contact email" type="email" :value="$settings['contact_email'] ?? ''"/>
                <x-admin.form-input name="contact_phone" label="Phone" :value="$settings['contact_phone'] ?? ''"/>
            </div>
            <x-admin.textarea name="contact_address" label="Address" :value="$settings['contact_address'] ?? ''" rows="2"/>
            <div class="grid sm:grid-cols-2 gap-5">
                <x-admin.form-input name="social_facebook" label="Facebook URL" :value="$settings['social_facebook'] ?? ''"/>
                <x-admin.form-input name="social_instagram" label="Instagram URL" :value="$settings['social_instagram'] ?? ''"/>
                <x-admin.form-input name="social_tiktok" label="TikTok URL" :value="$settings['social_tiktok'] ?? ''"/>
                <x-admin.form-input name="social_youtube" label="YouTube URL" :value="$settings['social_youtube'] ?? ''"/>
            </div>
            <x-admin.textarea name="map_embed" label="Map embed HTML (iframe)" :value="$settings['map_embed'] ?? ''" rows="3"/>
        </section>

        {{-- Hero --}}
        <section x-show="tab==='hero'" x-cloak class="brush-card p-6 space-y-5">
            <x-admin.form-input name="hero_heading" label="Hero heading" :value="$settings['hero_heading'] ?? ''"/>
            <x-admin.textarea name="hero_subheading" label="Hero subheading" :value="$settings['hero_subheading'] ?? ''" rows="2"/>
            <div class="grid sm:grid-cols-2 gap-5">
                <x-admin.form-input name="hero_cta_text" label="CTA text" :value="$settings['hero_cta_text'] ?? 'Shop the pack'"/>
                <x-admin.form-input name="hero_cta_link" label="CTA link" :value="$settings['hero_cta_link'] ?? '/products'"/>
            </div>
            <div class="border-t-2 border-dashed border-ink/30 pt-5 mt-2">
                <x-admin.image-upload name="hero_image" label="Hero image"
                    :currentPath="$settings['hero_image'] ?? null"
                    hint="Replaces the 'CHEW LOUD / CHEW PROUD' placeholder on the home hero. Portrait or square works best (aspect 4:5)."/>
            </div>
        </section>

        {{-- Newsletter --}}
        <section x-show="tab==='newsletter'" x-cloak class="brush-card p-6 space-y-5">
            <x-admin.form-input name="newsletter_heading" label="Newsletter heading" :value="$settings['newsletter_heading'] ?? 'Join the pack'"/>
            <x-admin.textarea name="newsletter_text" label="Newsletter text" :value="$settings['newsletter_text'] ?? ''" rows="3"/>
        </section>

        {{-- Announcement --}}
        <section x-show="tab==='announcement'" x-cloak class="brush-card p-6 space-y-5">
            <x-admin.textarea name="announcement_messages" label="Announcement messages"
                :value="$settings['announcement_messages'] ?? ''" rows="6"
                hint="One message per line. They scroll across the top of the site."/>
        </section>

        {{-- SEO --}}
        <section x-show="tab==='seo'" x-cloak class="brush-card p-6 space-y-5">
            <x-admin.form-input name="seo_default_title" label="Default SEO title" :value="$settings['seo_default_title'] ?? ''"/>
            <x-admin.textarea name="seo_default_description" label="Default SEO description" :value="$settings['seo_default_description'] ?? ''" rows="3"/>
            <x-admin.textarea name="analytics_scripts" label="Analytics / head scripts" :value="$settings['analytics_scripts'] ?? ''" rows="6"
                hint="Raw HTML; injected into the <head>. Use with care."/>
        </section>

        {{-- Footer --}}
        <section x-show="tab==='footer'" x-cloak class="brush-card p-6 space-y-5">
            <x-admin.textarea name="footer_text" label="Footer text" :value="$settings['footer_text'] ?? ''" rows="3"/>
        </section>

        <div class="flex gap-3">
            <button type="submit" class="btn-rough is-fire">Save settings</button>
        </div>
    </form>
</x-admin.layout>
