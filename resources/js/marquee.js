/**
 * Seamless, width-aware marquee.
 * Clones the source group until the track is wider than the viewport, so the
 * text repeats with no gaps on any screen, and derives the animation duration
 * from the group width so the scroll speed stays constant (px/s) everywhere.
 */
const SPEED = 55; // pixels per second

function build(root) {
    const track = root.querySelector('[data-marquee-track]');
    const group = track && track.querySelector('[data-marquee-group]');
    if (!track || !group) return null;

    let clones = [];

    const layout = () => {
        clones.forEach((c) => c.remove());
        clones = [];

        const groupWidth = group.getBoundingClientRect().width;
        const containerWidth = root.getBoundingClientRect().width;
        if (!groupWidth || !containerWidth) return;

        // Enough copies so the track always spans the container plus one full
        // group — that spare group is what scrolls in as the first one leaves.
        const copies = Math.ceil(containerWidth / groupWidth) + 1;
        for (let i = 1; i < copies; i++) {
            const clone = group.cloneNode(true);
            clone.removeAttribute('data-marquee-group');
            clone.setAttribute('aria-hidden', 'true');
            track.appendChild(clone);
            clones.push(clone);
        }

        root.style.setProperty('--marquee-shift', `-${groupWidth}px`);
        root.style.setProperty('--marquee-duration', `${(groupWidth / SPEED).toFixed(2)}s`);
        root.setAttribute('data-marquee-ready', '');
    };

    return layout;
}

function init() {
    document.querySelectorAll('[data-marquee]').forEach((root) => {
        const layout = build(root);
        if (!layout) return;

        layout();

        // Re-measure once webfonts land (text width changes).
        if (document.fonts && document.fonts.ready) {
            document.fonts.ready.then(layout).catch(() => {});
        }

        if ('ResizeObserver' in window) {
            let last = root.getBoundingClientRect().width;
            let frame = null;
            const ro = new ResizeObserver(() => {
                const width = root.getBoundingClientRect().width;
                if (Math.abs(width - last) < 1) return;
                last = width;
                cancelAnimationFrame(frame);
                frame = requestAnimationFrame(layout);
            });
            ro.observe(root);
        } else {
            window.addEventListener('resize', layout);
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
