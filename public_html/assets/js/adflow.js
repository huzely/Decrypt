(function(){
    const data = window.__SITE;
    if (!data) {
        console.error('Thiếu window.__SITE');
        return;
    }
    if (!data.adsEnabled || !data.adLink || data.bypass) return;

    const overlay = document.createElement('div');
    overlay.id = 'ad-overlay';
    overlay.innerHTML = `
        <div class="box">
            <button class="close" aria-label="Đóng">✕</button>
            <h2>${escapeHtml(data.adTitle || 'Quảng cáo')}</h2>
            <p>${escapeHtml(data.adBody || 'Ưu đãi hot trên Shopee')}</p>
        </div>
    `;
    document.body.appendChild(overlay);

    function go(){
        try {
            const payload = new FormData();
            payload.append('type', 'ad_click');
            payload.append('slug', data.slug || '');
            if (navigator.sendBeacon) {
                navigator.sendBeacon('/api/track.php', payload);
            }
        } catch (e) {}
        window.open(`/${encodeURIComponent(data.slug)}?ad=0`, '_blank', 'noopener');
        window.location.href = data.adLink;
    }

    overlay.addEventListener('click', go);
    overlay.querySelector('.close').addEventListener('click', function(evt){
        evt.stopPropagation();
        go();
    });

    function escapeHtml(str){
        return String(str).replace(/[&<>"']/g, ch => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;','\'':'&#39;'}[ch]));
    }
})();
