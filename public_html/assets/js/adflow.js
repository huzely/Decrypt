(function () {
  'use strict';

  var S = window.__SITE || {};

  function truthy(v){ return v === 1 || v === true || v === '1'; }

  if (!truthy(S.adsEnabled) || !S.adLink || S.bypass === true) return;

  function openFlow(slug){
    if (typeof window.trackEvent === 'function' && S.token) {
      try { window.trackEvent('ad_click', slug || '', S.token); } catch(e){}
    }
    try {
      window.open('/' + slug + '?ad=0', '_blank', 'noopener');
    } catch(e){}
    window.location.href = S.adLink;
  }

  function buildOverlay(){
    var o = document.createElement('div');
    o.id = 'adOverlay';
    o.style.cssText =
      'position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.75);display:flex;align-items:center;justify-content:center;';
    var b = document.createElement('div');
    b.style.cssText =
      'background:#fff;border-radius:14px;padding:18px;max-width:90%;text-align:center;';
    b.innerHTML =
      '<div style="text-align:right"><button id="adClose" style="font-size:20px;border:0;background:none;cursor:pointer">✕</button></div>' +
      '<h3>'+(S.adTitle||'Quảng cáo')+'</h3>' +
      '<p>'+(S.adBody||'Nhấn để tiếp tục')+'</p>';
    o.appendChild(b);
    document.body.appendChild(o);

    o.addEventListener('click', function(){
      openFlow(S.currentSlug);
    });
  }

  document.addEventListener('DOMContentLoaded', buildOverlay);
})();
