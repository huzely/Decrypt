(function(){
    const data = window.__SITE || {};
    if (!data.currentSlug) return;
    try {
        const payload = new FormData();
        payload.append('type', 'view');
        payload.append('slug', data.currentSlug);
        if (navigator.sendBeacon) {
            navigator.sendBeacon('/api/track.php', payload);
        } else {
            fetch('/api/track.php', { method: 'POST', body: payload, credentials: 'same-origin' });
        }
    } catch (e) {
        console.error(e);
    }
})();
