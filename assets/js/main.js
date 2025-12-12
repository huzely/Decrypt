(function(){
    const overlay = document.createElement('div');
    overlay.className = 'ad-overlay hidden';
    overlay.innerHTML = '<div class="ad-box"><button class="close-ad">×</button><h3 class="ad-title"></h3><p class="ad-body"></p></div>';
    document.body.appendChild(overlay);
    const adTitle = overlay.querySelector('.ad-title');
    const adBody = overlay.querySelector('.ad-body');
    const closeBtn = overlay.querySelector('.close-ad');

    function fetchToken(postId){
        return fetch('/issue_token.php?post_id=' + postId).then(r => r.json());
    }

    function showOverlay(postId, targetUrl, shopeeUrl){
        overlay.classList.remove('hidden');
        document.body.classList.add('no-scroll');
        fetch('/track_view.php').catch(()=>{});
        const adTitleVal = document.querySelector('[data-ad-title]')?.dataset.adTitle || 'Quảng cáo';
        const adBodyVal = document.querySelector('[data-ad-body]')?.dataset.adBody || 'Khuyến mãi hấp dẫn trên Shopee';
        adTitle.textContent = adTitleVal;
        adBody.textContent = adBodyVal;
        const closeAction = ()=>{
            overlay.classList.add('hidden');
            document.body.classList.remove('no-scroll');
            if (shopeeUrl) {
                window.open(targetUrl, '_blank');
                window.location.href = shopeeUrl;
            } else {
                window.location.href = targetUrl;
            }
        };
        closeBtn.onclick = closeAction;
        overlay.onclick = closeAction;
    }

    function handleCardClick(e){
        const card = e.target.closest('.card');
        if (!card) return;
        e.preventDefault();
        const postId = card.dataset.postId;
        const targetUrl = card.dataset.url + '?skip_ad=1';
        const hasShopee = card.dataset.hasShopee === '1';
        if (!hasShopee) { window.location.href = card.dataset.url; return; }
        fetchToken(postId).then(data => {
            const token = data.token;
            const shopeeUrl = '/shopee_redirect.php?post_id=' + postId + '&token=' + encodeURIComponent(token);
            showOverlay(postId, targetUrl, shopeeUrl);
        });
    }

    document.addEventListener('click', function(e){
        if (e.target.closest('.card-link')) {
            handleCardClick(e);
        }
    });

    // Post page overlay
    document.addEventListener('DOMContentLoaded', ()=>{
        const body = document.body;
        const postId = body.dataset.postId;
        const hasShopee = body.dataset.hasShopee === '1';
        const skipAd = body.dataset.skipAd === '1';
        const autoplay = body.dataset.autoplay === '1';
        const play = body.dataset.play === '1';
        const btnWatch = document.getElementById('btnWatch');
        if (btnWatch) {
            btnWatch.addEventListener('click', function(ev){
                ev.preventDefault();
                if (!hasShopee) { window.location.href = btnWatch.dataset.target; return; }
                fetchToken(postId).then(data => {
                    const token = data.token;
                    const shopeeUrl = '/shopee_redirect.php?post_id=' + postId + '&token=' + encodeURIComponent(token);
                    showOverlay(postId, btnWatch.dataset.target, shopeeUrl);
                });
            });
        }
        if (!skipAd && hasShopee && !play) {
            fetchToken(postId).then(data => {
                const targetUrl = window.location.pathname + '?skip_ad=1' + (autoplay ? '&play=1' : '');
                const shopeeUrl = '/shopee_redirect.php?post_id=' + postId + '&token=' + encodeURIComponent(data.token);
                showOverlay(postId, targetUrl, shopeeUrl);
            });
        }
    });

    // Hamburger menu for admin
    const burger = document.querySelector('.hamburger');
    const sidebar = document.querySelector('.admin-sidebar');
    if (burger && sidebar) {
        burger.addEventListener('click', ()=>{
            sidebar.classList.toggle('open');
            burger.classList.toggle('open');
        });
    }
})();
