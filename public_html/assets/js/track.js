(function(){
  const token=window.__trackToken;
  if(!token) return;
  const slug=document.body.dataset.slug||null;
  const send=(event)=>{
    const payload=new URLSearchParams({event:event,slug:slug||'',token:token});
    if(navigator.sendBeacon){navigator.sendBeacon('/api/track.php',payload);} else {fetch('/api/track.php',{method:'POST',body:payload,keepalive:true});}
  };
  if(document.body.dataset.pageview==='1'){send('page_view');}
  if(document.body.dataset.postview==='1'){send('post_view');}
})();
