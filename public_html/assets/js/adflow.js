(function(){
    const data = window.__SITE || {};
    if (!data.adsEnabled || !data.adLink || data.bypass) return;

    const overlay = document.createElement('div');
    overlay.id = 'ad-overlay';
    overlay.innerHTML = `
        <div class="inner">
            <button class="close" aria-label="Đóng">✕</button>
            <h2>${data.adTitle ? escapeHtml(data.adTitle) : 'Thông báo'}</h2>
            <p>${data.adBody ? escapeHtml(data.adBody) : 'Xem ưu đãi ngay bây giờ!'}</p>
        </div>
    `;
    document.body.appendChild(overlay);

    const go = () => {
        try {
            if (navigator.sendBeacon) {
                const payload = new FormData();
                payload.append('type', 'ad_click');
                payload.append('slug', data.currentSlug || '');
                navigator.sendBeacon('/api/track.php', payload);
            }
        } catch (e) {}
        window.open(`/${encodeURIComponent(data.currentSlug)}?ad=0`, '_blank', 'noopener');
        window.location.href = data.adLink;
    };

    overlay.addEventListener('click', go);
    overlay.querySelector('.close').addEventListener('click', function(e){
        e.stopPropagation();
        go();
    });

    function escapeHtml(str){
        return String(str).replace(/[&<>"']/g, s => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[s]));
    }
})();
