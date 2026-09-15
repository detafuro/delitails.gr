@if(!empty($site['analytics_scripts']))
    {{--
        Admin-pasted third-party tags (Google tag, Meta Pixel…) weigh ~350 KB of JS.
        Rendered straight into <head> they competed with the first paint on mobile,
        so they are parked in an inert <template> and injected once the page has
        loaded and the main thread is idle — or immediately on first interaction.
    --}}
    <template id="deferred-analytics">{!! $site['analytics_scripts'] !!}</template>
    <script>
        (function () {
            var done = false;
            function inject() {
                if (done) return;
                done = true;
                var tpl = document.getElementById('deferred-analytics');
                if (!tpl) return;
                Array.prototype.forEach.call(tpl.content.childNodes, function (node) {
                    if (node.nodeName === 'SCRIPT') {
                        // Scripts cloned from a template never run; rebuild them.
                        var s = document.createElement('script');
                        for (var i = 0; i < node.attributes.length; i++) {
                            s.setAttribute(node.attributes[i].name, node.attributes[i].value);
                        }
                        s.text = node.text;
                        document.head.appendChild(s);
                    } else if (node.nodeType === 1 && node.nodeName !== 'NOSCRIPT') {
                        document.body.appendChild(node.cloneNode(true));
                    }
                });
            }
            function whenIdle() {
                if ('requestIdleCallback' in window) requestIdleCallback(inject, { timeout: 3000 });
                else setTimeout(inject, 1500);
            }
            // Never start before the first contentful paint: `load` alone can fire
            // while the first frame is still pending, and then the tags' download
            // and parse get counted against the LCP.
            function afterFirstPaint(cb) {
                var fired = false;
                function go() { if (!fired) { fired = true; cb(); } }
                try {
                    var po = new PerformanceObserver(function (list) {
                        if (list.getEntriesByName('first-contentful-paint').length) {
                            po.disconnect();
                            go();
                        }
                    });
                    po.observe({ type: 'paint', buffered: true });
                } catch (e) {
                    setTimeout(go, 2500); // no Paint Timing API: a safe margin after load
                }
                setTimeout(go, 8000); // tracking must never be lost
            }
            function schedule() { afterFirstPaint(whenIdle); }
            if (document.readyState === 'complete') schedule();
            else window.addEventListener('load', schedule, { once: true });
            ['pointerdown', 'keydown', 'touchstart', 'scroll'].forEach(function (type) {
                window.addEventListener(type, inject, { once: true, passive: true });
            });
        })();
    </script>
@endif
