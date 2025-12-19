(function(){
    const overlay = document.getElementById('ad-overlay');
    if (!overlay) return;
    const adTitle = document.getElementById('ad-title');
    const adBody = document.getElementById('ad-body');
    const closeBtn = document.getElementById('ad-skip');
    const goBtn = document.getElementById('ad-go');
    const ad = window.__AD__ || {};
    const adLink = ad.link || '';

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

    function shouldShowFromClient(){
        if (!ad.enabled) return false;
        const freq = ad.frequency || 'once';
        if (freq === 'once') {
            return !sessionStorage.getItem('ad_seen_once');
        }
        if (freq === 'hourly') {
            const last = parseInt(localStorage.getItem('ad_last_ts') || '0', 10);
            const hours = parseInt(ad.hours || '4', 10);
            return (Date.now() - last) > hours * 3600 * 1000;
        }
        if (freq === 'per_n_posts') {
            const n = parseInt(ad.everyPosts || '3', 10);
            const c = parseInt(sessionStorage.getItem('ad_counter') || '0', 10) + 1;
            sessionStorage.setItem('ad_counter', c.toString());
            if (c >= n) {
                sessionStorage.setItem('ad_counter', '0');
                return true;
            }
            return false;
        }
        return false;
    }

    function markShown(){
        sessionStorage.setItem('ad_seen_once', '1');
        localStorage.setItem('ad_last_ts', Date.now().toString());
    }

    function openOverlay(slug, goToArticle){
        if (!adLink || !shouldShowFromClient()) {
            if (goToArticle) window.location.href = goToArticle;
            return;
        }
        overlay.hidden = false;
        fillAd();
        const proceedArticle = () => {
            overlay.hidden = true;
            if (goToArticle) window.location.href = goToArticle;
        };
        if (closeBtn) closeBtn.onclick = proceedArticle;
        if (goBtn) goBtn.onclick = () => {
            track('ad_forced_redirect', slug);
            window.open(adLink, '_blank', 'noopener');
            markShown();
            proceedArticle();
        };
        if (closeBtn) closeBtn.addEventListener('click', () => track('ad_close_click', slug));
    }

    document.addEventListener('DOMContentLoaded', function(){
        const navToggle = document.getElementById('nav-toggle');
        const navDrawer = document.getElementById('nav-drawer');
        if (navToggle && navDrawer) {
            navToggle.addEventListener('click', () => {
                navToggle.classList.toggle('active');
                navDrawer.classList.toggle('open');
            });
        }

        // Home page interception
        document.querySelectorAll('a.post-link').forEach(a => {
            a.addEventListener('click', function(e){
                const slug = this.dataset.slug;
                if (!slug) return;
                e.preventDefault();
                openOverlay(slug, this.href);
            });
        });

        // Article direct access
        if (ad.show) {
            openOverlay(ad.slug, null);
        }
    });
})();
