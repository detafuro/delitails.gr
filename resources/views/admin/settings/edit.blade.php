<x-admin.layout title="Site settings" subtitle="Global branding, contact, homepage, SEO & footer">
    <form method="POST" action="{{ route('admin.settings.update') }}" enctype="multipart/form-data"
          x-data="{tab: 'brand'}" class="space-y-6">
        @csrf @method('PUT')

        <nav class="flex flex-wrap gap-2">
            @php
                $tabs = [
                    'brand' => 'Brand',
                    'contact' => 'Contact',
                    'homepage' => 'Homepage',
                    'newsletter' => 'Newsletter',
                    'announcement' => 'Announcement',
                    'seo' => 'SEO & scripts',
                    'footer' => 'Footer',
                    'pages' => 'Pages',
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
            <div class="grid sm:grid-cols-2 gap-5">
                <x-admin.textarea name="contact_address" label="Address" :value="$settings['contact_address'] ?? ''" rows="2"/>
                <x-admin.textarea name="contact_address_el" label="Address (Ελληνικά)" :value="$settings['contact_address_el'] ?? ''" rows="2"
                    hint="Shown to Greek visitors; falls back to the English address when empty."/>
            </div>
            <div class="grid sm:grid-cols-2 gap-5">
                <x-admin.form-input name="social_facebook" label="Facebook URL" :value="$settings['social_facebook'] ?? ''"/>
                <x-admin.form-input name="social_instagram" label="Instagram URL" :value="$settings['social_instagram'] ?? ''"/>
                <x-admin.form-input name="social_tiktok" label="TikTok URL" :value="$settings['social_tiktok'] ?? ''"/>
                <x-admin.form-input name="social_youtube" label="YouTube URL" :value="$settings['social_youtube'] ?? ''"/>
            </div>
            <x-admin.textarea name="map_embed" label="Map embed HTML (iframe)" :value="$settings['map_embed'] ?? ''" rows="3"/>
        </section>

        {{-- Homepage --}}
        <section x-show="tab==='homepage'" x-cloak class="brush-card p-6 space-y-5" x-data="{ lang: 'en' }">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <h3 class="font-display text-lg font-black uppercase">Hero</h3>
                <x-admin.lang-tabs/>
            </div>
            <div x-show="lang==='en'" class="space-y-5">
                <x-admin.form-input name="hero_heading" label="Hero heading" :value="$settings['hero_heading'] ?? ''"/>
                <x-admin.textarea name="hero_subheading" label="Hero subheading" :value="$settings['hero_subheading'] ?? ''" rows="2"/>
                <div class="grid sm:grid-cols-2 gap-5">
                    <x-admin.form-input name="hero_cta_text" label="CTA text" :value="$settings['hero_cta_text'] ?? 'Explore the pack'"/>
                    <x-admin.form-input name="hero_cta_link" label="CTA link" :value="$settings['hero_cta_link'] ?? '/products'"/>
                </div>
            </div>
            <div x-show="lang==='el'" x-cloak class="space-y-5">
                <x-admin.form-input name="hero_heading_el" label="Hero heading (Ελληνικά)" :value="$settings['hero_heading_el'] ?? ''"/>
                <x-admin.textarea name="hero_subheading_el" label="Hero subheading (Ελληνικά)" :value="$settings['hero_subheading_el'] ?? ''" rows="2"/>
                <div class="grid sm:grid-cols-2 gap-5">
                    <x-admin.form-input name="hero_cta_text_el" label="CTA text (Ελληνικά)" :value="$settings['hero_cta_text_el'] ?? ''"/>
                </div>
            </div>
            <x-admin.image-upload name="hero_image" label="Hero image"
                :currentPath="$settings['hero_image'] ?? null"
                hint="Replaces the 'CHEW LOUD / CHEW PROUD' placeholder on the home hero. Portrait or square works best (aspect 4:5)."/>
            <div class="border-t-2 border-dashed border-ink/30 pt-5 mt-2 space-y-5">
                <h3 class="font-display text-lg font-black uppercase">Choose your chew</h3>
                <div class="grid sm:grid-cols-2 gap-5">
                    <x-admin.image-upload name="lineup_chews_image" label="Chews image"
                        :currentPath="$settings['lineup_chews_image'] ?? null"
                        hint="Replaces the CHEWS placeholder. Landscape 4:3 — 1200×900 recommended."/>
                    <x-admin.image-upload name="lineup_treats_image" label="Treats image"
                        :currentPath="$settings['lineup_treats_image'] ?? null"
                        hint="Replaces the TREATS placeholder. Landscape 4:3 — 1200×900 recommended."/>
                </div>
            </div>
            <div class="border-t-2 border-dashed border-ink/30 pt-5 mt-2 space-y-5">
                <h3 class="font-display text-lg font-black uppercase">Our story</h3>
                <x-admin.image-upload name="our_story_image" label="Our story photo"
                    :currentPath="$settings['our_story_image'] ?? null"
                    hint="Photo in the homepage Our Story section. Portrait 4:5 — 1200×1500 recommended. Falls back to the default photo when empty."/>
            </div>
        </section>

        {{-- Newsletter --}}
        <section x-show="tab==='newsletter'" x-cloak class="brush-card p-6 space-y-5" x-data="{ lang: 'en' }">
            <x-admin.lang-tabs/>
            <div x-show="lang==='en'" class="space-y-5">
                <x-admin.form-input name="newsletter_heading" label="Newsletter heading" :value="$settings['newsletter_heading'] ?? 'Join the pack'"/>
                <x-admin.textarea name="newsletter_text" label="Newsletter text" :value="$settings['newsletter_text'] ?? ''" rows="3"/>
            </div>
            <div x-show="lang==='el'" x-cloak class="space-y-5">
                <x-admin.form-input name="newsletter_heading_el" label="Newsletter heading (Ελληνικά)" :value="$settings['newsletter_heading_el'] ?? ''"/>
                <x-admin.textarea name="newsletter_text_el" label="Newsletter text (Ελληνικά)" :value="$settings['newsletter_text_el'] ?? ''" rows="3"
                    hint="Shown to Greek visitors; falls back to the English text when empty."/>
            </div>
        </section>

        {{-- Announcement --}}
        <section x-show="tab==='announcement'" x-cloak class="brush-card p-6 space-y-5" x-data="{ lang: 'en' }">
            <x-admin.lang-tabs/>
            <div x-show="lang==='en'">
                <x-admin.textarea name="announcement_messages" label="Announcement messages"
                    :value="$settings['announcement_messages'] ?? ''" rows="6"
                    hint="One message per line. They scroll across the top of the site."/>
            </div>
            <div x-show="lang==='el'" x-cloak>
                <x-admin.textarea name="announcement_messages_el" label="Announcement messages (Ελληνικά)"
                    :value="$settings['announcement_messages_el'] ?? ''" rows="6"
                    hint="One message per line. Shown to Greek visitors; falls back to the English messages when empty."/>
            </div>
        </section>

        {{-- SEO --}}
        <section x-show="tab==='seo'" x-cloak class="brush-card p-6 space-y-5">
            <x-admin.form-input name="seo_default_title" label="Default SEO title" :value="$settings['seo_default_title'] ?? ''"/>
            <x-admin.textarea name="seo_default_description" label="Default SEO description" :value="$settings['seo_default_description'] ?? ''" rows="3"/>
            <x-admin.textarea name="analytics_scripts" label="Analytics / head scripts" :value="$settings['analytics_scripts'] ?? ''" rows="6"
                hint="Raw HTML; injected into the <head>. Use with care."/>
        </section>

        {{-- Footer --}}
        <section x-show="tab==='footer'" x-cloak class="brush-card p-6 space-y-5" x-data="{ lang: 'en' }">
            <x-admin.lang-tabs/>
            <div x-show="lang==='en'">
                <x-admin.textarea name="footer_text" label="Footer text" :value="$settings['footer_text'] ?? ''" rows="3"/>
            </div>
            <div x-show="lang==='el'" x-cloak>
                <x-admin.textarea name="footer_text_el" label="Footer text (Ελληνικά)" :value="$settings['footer_text_el'] ?? ''" rows="3"
                    hint="Shown to Greek visitors; falls back to the English text when empty."/>
            </div>
        </section>

        {{-- Pages --}}
        <section x-show="tab==='pages'" x-cloak class="brush-card p-6 space-y-5">
            <div class="grid sm:grid-cols-2 gap-5">
                <x-admin.select name="stores_page_status" label="Stockists page"
                    :options="['draft' => 'Draft (hidden)', 'public' => 'Public']"
                    :value="$settings['stores_page_status'] ?? 'draft'"/>
                <x-admin.select name="under_construction" label="Under construction mode"
                    :options="['off' => 'Off (site visible)', 'on' => 'On (visitors see a coming-soon page)']"
                    :value="$settings['under_construction'] ?? 'off'"/>
                <x-admin.form-input name="under_construction_passcode" label="Guest passcode"
                    :value="$settings['under_construction_passcode'] ?? ''"/>
            </div>
            <p class="text-xs text-ink/60">
                Draft hides the stockists page (404 for visitors — admins can still preview it at /stores)
                and removes every stockist link across the site: header button, nav item, footer link,
                the homepage stockists band, and the "Find a stockist" button on products.
                Switch to Public to restore everything once stockists exist.
            </p>
            <p class="text-xs text-ink/60">
                Under construction mode replaces the whole public site with a branded coming-soon page
                (HTTP 503) for visitors. Logged-in admins keep seeing the real site, and the login page
                stays reachable at /login. Turn it off to relaunch instantly.
                If a guest passcode is set, the coming-soon page shows a "Guest login" button — anyone
                with the passcode can browse the full site for their session. Changing the passcode
                kicks existing guests back out; leaving it empty hides the button.
            </p>
        </section>

        <div class="flex gap-3">
            <button type="submit" class="btn-rough is-fire">Save settings</button>
        </div>
    </form>
</x-admin.layout>
