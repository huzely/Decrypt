(function(){
    const data = window.__SITE;
    if (!data || !data.slug) return;
    try {
        const payload = new FormData();
        payload.append('type', 'view');
        payload.append('slug', data.slug);
        if (navigator.sendBeacon) {
            navigator.sendBeacon('/api/track.php', payload);
        } else {
            fetch('/api/track.php', {method:'POST', body: payload, credentials:'same-origin'});
        }
    } catch (e) {}
})();
