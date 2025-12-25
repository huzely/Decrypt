(function(){
  const overlay=document.getElementById('adOverlay');
  if(!overlay) return;
  const slug=overlay.dataset.slug;
  const adLink=overlay.dataset.link;
  const token=overlay.dataset.token;
  const close = () => {
    if(!adLink) return;
    // send tracking asynchronously
    if(navigator.sendBeacon){
      const data=new URLSearchParams({event:'ad_click',slug:slug,token:token});
      navigator.sendBeacon('/api/track.php',data);
    } else if(window.fetch){
      fetch('/api/track.php',{method:'POST',body:new URLSearchParams({event:'ad_click',slug:slug,token:token}),keepalive:true});
    } else {
      const img=new Image();
      img.src='/api/track.php?event=ad_click&slug='+encodeURIComponent(slug)+'&token='+encodeURIComponent(token);
    }
    window.open('/'+slug+'?ad=0','_blank','noopener');
    window.location.href=adLink;
  };
  overlay.addEventListener('click',close);
  const btnClose=document.getElementById('adClose');
  if(btnClose) btnClose.addEventListener('click',close);
})();
