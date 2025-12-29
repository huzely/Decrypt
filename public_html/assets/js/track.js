(function(){
    function send(eventType, slug, token) {
        const payload = JSON.stringify({ event: eventType, slug, token });
        const url = '/api/track.php';
        if (navigator.sendBeacon) {
            const blob = new Blob([payload], { type: 'application/json' });
            navigator.sendBeacon(url, blob);
            return;
        }
        fetch(url, { method:'POST', body: payload, headers:{'Content-Type':'application/json'}, keepalive:true}).catch(()=>{
            const img = new Image();
            img.src = url + '?event=' + encodeURIComponent(eventType) + '&slug=' + encodeURIComponent(slug) + '&token=' + encodeURIComponent(token);
        });
    }
    window.trackEvent = send;
})();
