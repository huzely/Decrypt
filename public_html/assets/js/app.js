document.addEventListener('DOMContentLoaded', () => {
    const adLink = document.documentElement.dataset.adLink || '';
    const adTitle = document.documentElement.dataset.adTitle || 'Quảng cáo';
    const adBody = document.documentElement.dataset.adBody || '';
    const token = window.TRACK_TOKEN || '';

    const track = (type, slug) => {
        const data = new URLSearchParams();
        data.append('type', type);
        if (slug) data.append('slug', slug);
        data.append('token', token);
        const payload = data.toString();
        if (navigator.sendBeacon) {
            navigator.sendBeacon('/api/track.php', payload);
        } else {
            fetch('/api/track.php', { method: 'POST', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: payload, keepalive:true }).catch(()=>{});
        }
    };

    const createOverlay = (slug) => {
        const overlay = document.createElement('div');
        overlay.className = 'ad-overlay';
        overlay.innerHTML = `
            <div class="ad-box">
                <h3>${adTitle}</h3>
                <p>${adBody}</p>
                <button class="btn btn-secondary close" type="button">Đóng</button>
            </div>
        `;
        overlay.addEventListener('click', () => {
            track('ad_forced_redirect', slug);
            window.location.href = adLink;
        }, { once: true });
        overlay.querySelector('.close').addEventListener('click', (e) => {
            e.stopPropagation();
            track('ad_close_click', slug);
            overlay.remove();
        }, { once: true });
        document.body.appendChild(overlay);
    };

    document.querySelectorAll('[data-article-link]').forEach(link => {
        link.addEventListener('click', (e) => {
            const slug = link.dataset.articleLink;
            if (!adLink) return;
            e.preventDefault();
            window.open(`/${slug}?ad=0`, '_blank', 'noopener');
            createOverlay(slug);
        });
    });

    const articleWrap = document.querySelector('.article');
    if (articleWrap) {
        const slug = articleWrap.dataset.slug;
        track('article_view', slug);
        if (adLink && !articleWrap.dataset.skipAd) {
            createOverlay(slug);
        }
    }
});
