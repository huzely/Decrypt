(function(){
    const overlay = document.getElementById('ad-overlay');
    const adTitle = document.getElementById('ad-title');
    const adBody = document.getElementById('ad-body');
    const closeBtn = document.querySelector('.ad-close');

    const ad = window.__AD__ || {};
    const adLink = ad.link || '';
    if (!overlay) return;

    function fillAd(){
        if (adTitle) adTitle.textContent = ad.title || '';
        if (adBody) adBody.textContent = ad.body || '';
    }

    function track(event, slug){
        if (!event || !slug) return;
        const payload = new URLSearchParams({event, slug, token: ad.token || ''});
        if (!navigator.sendBeacon('/api/track.php', payload)) {
            fetch('/api/track.php', {method:'POST', body: payload, keepalive: true});
        }
    }

    function triggerRedirect(slug){
        if (!adLink) return;
        overlay.hidden = false;
        fillAd();
        const handler = () => {
            track('ad_forced_redirect', slug);
            window.open('/' + slug + '?ad=0', '_blank', 'noopener');
            window.location.href = adLink;
        };
        overlay.onclick = handler;
        if (closeBtn) closeBtn.onclick = handler;
    }

    document.addEventListener('DOMContentLoaded', function(){
        if (!adLink) return;
        // Home page interception
        document.querySelectorAll('a.post-link').forEach(a => {
            a.addEventListener('click', function(e){
                const slug = this.dataset.slug;
                if (!slug) return;
                e.preventDefault();
                triggerRedirect(slug);
            });
        });

        // Article direct access
        if (ad.show) {
            const slug = ad.slug;
            triggerRedirect(slug);
        }
    });
})();
