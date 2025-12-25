(function(){
    const hamburger = document.getElementById('hamburger');
    const menu = document.getElementById('mobileMenu');
    if (hamburger && menu) {
        hamburger.addEventListener('click', () => {
            menu.classList.toggle('open');
        });
    }

    const interstitial = document.getElementById('interstitial');
    if (interstitial) {
        const skip = document.getElementById('btnSkip');
        const btnShopee = document.getElementById('btnShopee');
        const frequency = parseInt(interstitial.dataset.frequency, 10) || 1;
        const unit = interstitial.dataset.unit;
        const storageKey = 'shopee_interstitial';

        const shouldShow = () => {
            const data = JSON.parse(localStorage.getItem(storageKey) || '{}');
            if (unit === 'session') {
                return !sessionStorage.getItem(storageKey);
            }
            if (unit === 'hours') {
                const last = data.last || 0;
                return (Date.now() - last) > frequency * 3600 * 1000;
            }
            if (unit === 'articles') {
                const count = data.count || 0;
                return count >= frequency;
            }
            return true;
        };

        if (shouldShow()) {
            interstitial.classList.add('show');
        }

        const markShown = () => {
            const data = JSON.parse(localStorage.getItem(storageKey) || '{}');
            if (unit === 'hours') {
                data.last = Date.now();
            }
            if (unit === 'articles') {
                data.count = 0;
            }
            sessionStorage.setItem(storageKey, '1');
            localStorage.setItem(storageKey, JSON.stringify(data));
            interstitial.classList.remove('show');
        };

        if (skip) {
            skip.addEventListener('click', () => {
                markShown();
            });
        }

        if (btnShopee) {
            btnShopee.addEventListener('click', () => {
                const link = btnShopee.dataset.link;
                const article = btnShopee.dataset.article;
                window.open(link, '_blank', 'noopener');
                fetch(baseUrl('track-click'), {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: 'csrf_token=' + encodeURIComponent(window.csrfToken || '') + '&article_id=' + article
                });
                markShown();
            });
        }

        if (unit === 'articles') {
            const data = JSON.parse(localStorage.getItem(storageKey) || '{}');
            data.count = (data.count || 0) + 1;
            localStorage.setItem(storageKey, JSON.stringify(data));
            if (data.count >= frequency) {
                interstitial.classList.add('show');
            }
        }
    }
})();

function baseUrl(path) {
    const base = document.querySelector('body').dataset.base || '/';
    return base.replace(/\/$/, '') + '/' + path.replace(/^\//, '');
}
