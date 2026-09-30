@props(['url', 'label' => 'Copy link'])
{{-- Copy a URL to the clipboard. navigator.clipboard needs a secure context;
     falls back to a hidden textarea + execCommand on plain http. --}}
<button type="button"
        x-data="{
            copied: false,
            copy() {
                const url = @js($url);
                const done = () => { this.copied = true; setTimeout(() => this.copied = false, 1800); };
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(url).then(done).catch(() => this.fallback(url, done));
                } else {
                    this.fallback(url, done);
                }
            },
            fallback(url, done) {
                const el = document.createElement('textarea');
                el.value = url;
                el.setAttribute('readonly', '');
                el.style.position = 'fixed';
                el.style.opacity = '0';
                document.body.appendChild(el);
                el.select();
                try { document.execCommand('copy'); done(); } catch (e) { window.prompt('Copy this link:', url); }
                el.remove();
            }
        }"
        @click="copy()"
        :title="copied ? 'Copied!' : @js($url)"
        {{ $attributes->merge(['class' => 'btn-rough is-bone is-sm']) }}>
    <span x-show="!copied">{{ $label }}</span>
    <span x-show="copied" x-cloak>✓ Copied</span>
</button>
