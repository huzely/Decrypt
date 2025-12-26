(function(){
    function initAdFlow(settings) {
        if (!settings || settings.disabled) return;
        if (window.location.search.includes('ad=0')) return;
        const overlay = document.createElement('div');
        overlay.className = 'overlay-ad';
        overlay.innerHTML = `
            <div class="box">
                <h3>${settings.title || 'Ưu đãi Shopee'}</h3>
                <p>${settings.body || 'Nhấp để xem ưu đãi hot trên Shopee'}</p>
                <button type="button" id="closeAd">Tắt quảng cáo & mở bài</button>
            </div>
        `;
        document.body.appendChild(overlay);
        function go() {
            if (typeof window.trackEvent === 'function' && settings.token) {
                try { window.trackEvent('ad_click', settings.slug || '', settings.token); } catch(e){}
            }
            if (settings.slug) {
                window.open('/' + settings.slug + '?ad=0', '_blank', 'noopener');
            }
            window.location.href = settings.link;
        }
        overlay.addEventListener('click', go, { once: true });
        const btn = overlay.querySelector('#closeAd');
        if (btn) btn.addEventListener('click', function(e){ e.stopPropagation(); go(); }, { once: true });
    }
    window.initAdFlow = initAdFlow;
})();
