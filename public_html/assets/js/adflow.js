(function () {
  'use strict';
  var S = window.__SITE || {};
  function truthy(v){ return v === 1 || v === true || v === '1'; }
  if (!truthy(S.adsEnabled) || !S.adLink || S.bypass === true) return;

  function openFlow(slug){
    try { window.open('/' + slug + '?ad=0', '_blank', 'noopener'); } catch(e){}
    window.location.href = S.adLink; // s.shopee.vn để giữ affiliate, Shopee tự mở app nếu có
  }

  function buildOverlay(){
    var o = document.createElement('div');
    o.id = 'adOverlay';
    o.style.cssText='position:fixed;inset:0;z-index:99999;background:rgba(0,0,0,.75);display:flex;align-items:center;justify-content:center;padding:14px;';
    var b = document.createElement('div');
    b.style.cssText='background:#fff;border-radius:14px;padding:18px;max-width:420px;width:100%;text-align:center;box-shadow:0 18px 50px rgba(0,0,0,.35);';
    b.innerHTML=
      '<div style="text-align:right"><button type="button" style="font-size:20px;border:0;background:none;cursor:pointer;padding:6px;">✕</button></div>'+
      '<h3 style="margin:0 0 8px 0;font-size:18px;">'+(S.adTitle||'Quảng cáo')+'</h3>'+
      '<p style="margin:0 0 10px 0;font-size:14px;line-height:1.4;">'+(S.adBody||'Nhấn để tiếp tục')+'</p>'+
      '<div style="font-size:12px;opacity:.75;">Chạm bất kỳ đâu để tiếp tục</div>';
    o.appendChild(b);
    document.body.appendChild(o);
    o.addEventListener('click', function(){ openFlow(S.currentSlug); });
  }

  document.addEventListener('DOMContentLoaded', buildOverlay);
})();
